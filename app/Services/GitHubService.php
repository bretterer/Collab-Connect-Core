<?php

namespace App\Services;

use App\Models\Feedback;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GitHubService
{
    private string $token;

    private string $repository;

    private string $baseUrl;

    public function __construct()
    {
        $this->token = config('services.github.token', '');
        $this->repository = config('services.github.repository', '');
        $this->baseUrl = 'https://api.github.com';
    }

    public function createIssue(Feedback $feedback): ?array
    {
        if (! $this->token || ! $this->repository) {
            Log::warning('GitHub token or repository not configured');

            return null;
        }

        $title = $this->generateIssueTitle($feedback);
        $body = $this->generateIssueBody($feedback);
        $labels = $this->generateLabels($feedback);

        try {
            $response = Http::withToken($this->token)
                ->post("{$this->baseUrl}/repos/{$this->repository}/issues", [
                    'title' => $title,
                    'body' => $body,
                    'labels' => $labels,
                ]);

            if ($response->successful()) {
                $issueData = $response->json();

                // Update feedback with GitHub issue information
                $feedback->update([
                    'github_issue_url' => $issueData['html_url'],
                    'github_issue_number' => $issueData['number'],
                ]);

                return $issueData;
            }

            Log::error('Failed to create GitHub issue', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Exception creating GitHub issue', [
                'message' => $e->getMessage(),
                'feedback_id' => $feedback->id,
            ]);

            return null;
        }
    }

    private function generateIssueTitle(Feedback $feedback): string
    {
        $prefix = match ($feedback->type) {
            \App\Enums\FeedbackType::BUG_REPORT => '[Bug]',
            \App\Enums\FeedbackType::FEATURE_REQUEST => '[Feature]',
            \App\Enums\FeedbackType::GENERAL_FEEDBACK => '[Feedback]',
            default => '[Beta]'
        };

        return "{$prefix} {$feedback->subject}";
    }

    private function generateIssueBody(Feedback $feedback): string
    {
        $body = "## Beta Feedback Report\n\n";

        $body .= "**Type:** {$feedback->type->label()}\n";
        $body .= '**Submitted by:** '.($feedback->user ? $feedback->user->name." ({$feedback->user->email})" : 'Anonymous')."\n";
        $body .= "**Date:** {$feedback->created_at->format('Y-m-d H:i:s T')}\n\n";

        $body .= "## Description\n\n";
        $body .= "{$feedback->message}\n\n";

        if ($feedback->url) {
            $body .= "## Context\n\n";
            $body .= "**Page URL:** {$feedback->url}\n";
        }

        if ($feedback->browser_info) {
            $body .= "\n## Technical Information\n\n";

            if ($feedback->browser_info['user_agent'] ?? null) {
                $body .= "**User Agent:** {$feedback->browser_info['user_agent']}\n";
            }

            if ($feedback->browser_info['ip_address'] ?? null) {
                $body .= "**IP Address:** {$feedback->browser_info['ip_address']}\n";
            }
        }

        if ($feedback->session_data) {
            $sessionData = $feedback->session_data;

            if ($sessionData['timestamp'] ?? null) {
                $body .= "**Session Timestamp:** {$sessionData['timestamp']}\n";
            }

            if ($sessionData['previous_url'] ?? null) {
                $body .= "**Previous URL:** {$sessionData['previous_url']}\n";
            }
        }

        $body .= "\n---\n";
        $body .= "*This issue was automatically created from beta feedback (ID: {$feedback->id})*";

        return $body;
    }

    private function generateLabels(Feedback $feedback): array
    {
        $labels = ['beta-feedback'];

        $labels[] = match ($feedback->type) {
            \App\Enums\FeedbackType::BUG_REPORT => 'bug',
            \App\Enums\FeedbackType::FEATURE_REQUEST => 'enhancement',
            \App\Enums\FeedbackType::GENERAL_FEEDBACK => 'feedback',
            default => 'beta'
        };

        return $labels;
    }

    public function isConfigured(): bool
    {
        return ! empty($this->token) && ! empty($this->repository);
    }
}

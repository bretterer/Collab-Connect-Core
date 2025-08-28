<?php

namespace App\Enums;

use App\Enums\Traits\HasFormOptions;

enum FeedbackType: string
{
    use HasFormOptions;

    case BUG_REPORT = 'bug_report';
    case FEATURE_REQUEST = 'feature_request';
    case GENERAL_FEEDBACK = 'general_feedback';

    public function label(): string
    {
        return match ($this) {
            self::BUG_REPORT => 'Bug Report',
            self::FEATURE_REQUEST => 'Feature Request',
            self::GENERAL_FEEDBACK => 'General Feedback',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::BUG_REPORT => 'Report a bug or issue you encountered',
            self::FEATURE_REQUEST => 'Suggest a new feature or improvement',
            self::GENERAL_FEEDBACK => 'Share general feedback or comments',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::BUG_REPORT => 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z',
            self::FEATURE_REQUEST => 'M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z',
            self::GENERAL_FEEDBACK => 'M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.691 1.309 3.061 2.927 3.237.709.075 1.487.056 2.372-.021l2.182-.182a.5.5 0 01.543.391l.394 1.968c.216 1.083 1.696 1.472 2.653.708l1.109-.885c.435-.348.989-.527 1.555-.527H21.75a2.25 2.25 0 002.25-2.25V8.25a2.25 2.25 0 00-2.25-2.25H5.25a2.25 2.25 0 00-2.25 2.25v5.249z',
        };
    }
}

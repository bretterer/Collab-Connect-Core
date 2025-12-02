<?php

namespace App\Livewire\Admin\EmailSequences;

use App\Enums\EmailTriggerType;
use App\Enums\SequenceMode;
use App\Models\EmailSequence;
use App\Models\EmailSequenceEmail;
use App\Models\Form;
use App\Models\LandingPage;
use Carbon\Carbon;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class EmailSequenceEdit extends Component
{
    public EmailSequence $emailSequence;

    public string $name = '';

    public string $description = '';

    public string $sequenceMode = 'after_subscription';

    public ?string $anchorDate = null;

    public ?string $anchorTime = null;

    public string $anchorTimezone = 'America/New_York';

    public bool $sendWelcomeEmail = false;

    public string $welcomeEmailSubject = '';

    public string $welcomeEmailBody = '';

    public string $welcomeEmailPreviewText = '';

    public bool $showWelcomeEmailEditor = false;

    public array $subscribeTriggers = [];

    public array $unsubscribeTriggers = [];

    public array $emails = [];

    public ?int $editingEmailIndex = null;

    public array $emailData = [];

    public bool $showEmailEditor = false;

    public bool $showTriggerModal = false;

    public string $triggerType = 'subscribe';

    public ?int $selectedFormId = null;

    public ?int $mergeTagFormId = null;

    public function mount(EmailSequence $emailSequence): void
    {
        $this->emailSequence = $emailSequence;
        $this->name = $this->emailSequence->name;
        $this->description = $this->emailSequence->description ?? '';
        $this->sequenceMode = $this->emailSequence->sequence_mode?->value ?? 'after_subscription';
        $this->anchorTimezone = $this->emailSequence->anchor_timezone ?? 'America/New_York';

        // Parse anchor datetime into separate date and time components
        if ($this->emailSequence->anchor_datetime) {
            $anchorDt = $this->emailSequence->anchor_datetime->timezone($this->anchorTimezone);
            $this->anchorDate = $anchorDt->format('Y-m-d');
            $this->anchorTime = $anchorDt->format('H:i');
        }

        $this->sendWelcomeEmail = $this->emailSequence->send_welcome_email ?? false;
        $this->welcomeEmailSubject = $this->emailSequence->welcome_email_subject ?? '';
        $this->welcomeEmailBody = $this->emailSequence->welcome_email_body ?? '';
        $this->welcomeEmailPreviewText = $this->emailSequence->welcome_email_preview_text ?? '';

        $this->subscribeTriggers = $this->emailSequence->subscribe_triggers ?? [];
        $this->unsubscribeTriggers = $this->emailSequence->unsubscribe_triggers ?? [];

        // Load emails
        $this->emails = $this->emailSequence->emails->map(function ($email) {
            return [
                'id' => $email->id,
                'name' => $email->name,
                'subject' => $email->subject,
                'preview_text' => $email->preview_text ?? '',
                'body' => $email->body,
                'delay_days' => $email->delay_days,
                'delay_hours' => $email->delay_hours ?? 0,
                'send_time' => $email->send_time,
                'timezone' => $email->timezone,
                'order' => $email->order,
            ];
        })->toArray();
    }

    protected function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'sequenceMode' => ['required', 'string', 'in:after_subscription,before_anchor_date'],
            'sendWelcomeEmail' => ['boolean'],
            'subscribeTriggers' => ['array'],
            'unsubscribeTriggers' => ['array'],
            'emails' => ['array'],
        ];

        if ($this->sequenceMode === 'before_anchor_date') {
            $rules['anchorDate'] = ['required', 'date'];
            $rules['anchorTime'] = ['required', 'date_format:H:i'];
            $rules['anchorTimezone'] = ['required', 'string', 'timezone'];
        }

        if ($this->sendWelcomeEmail) {
            $rules['welcomeEmailSubject'] = ['required', 'string', 'max:140'];
            $rules['welcomeEmailBody'] = ['required', 'string'];
            $rules['welcomeEmailPreviewText'] = ['nullable', 'string', 'max:140'];
        }

        return $rules;
    }

    public function isBeforeAnchorMode(): bool
    {
        return $this->sequenceMode === 'before_anchor_date';
    }

    /**
     * Calculate the scheduled send date/time for the current email being edited.
     * Returns null if anchor date is not set or the email data is incomplete.
     */
    public function getCalculatedSendDateTime(): ?string
    {
        if (! $this->isBeforeAnchorMode() || ! $this->anchorDate || ! $this->anchorTime) {
            return null;
        }

        $delayDays = (int) ($this->emailData['delay_days'] ?? 0);
        $delayHours = (int) ($this->emailData['delay_hours'] ?? 0);

        // Build the anchor datetime in the specified timezone
        $anchorDateTime = Carbon::createFromFormat(
            'Y-m-d H:i',
            $this->anchorDate.' '.$this->anchorTime,
            $this->anchorTimezone
        );

        // Subtract the delay
        $scheduledAt = $anchorDateTime->copy()
            ->subDays($delayDays)
            ->subHours($delayHours);

        return $scheduledAt->format('l, F j, Y \a\t g:i A');
    }

    public function editWelcomeEmail(): void
    {
        $this->showWelcomeEmailEditor = true;
    }

    public function saveWelcomeEmail(): void
    {
        $this->validate([
            'welcomeEmailSubject' => ['required', 'string', 'max:140'],
            'welcomeEmailBody' => ['required', 'string'],
            'welcomeEmailPreviewText' => ['nullable', 'string', 'max:140'],
        ]);

        $this->showWelcomeEmailEditor = false;
        Flux::toast(text: 'Welcome email saved', variant: 'success');
    }

    public function cancelWelcomeEmailEdit(): void
    {
        $this->showWelcomeEmailEditor = false;
    }

    public function addTrigger(string $type): void
    {
        $this->triggerType = $type;
        $this->selectedFormId = null;
        $this->showTriggerModal = true;
    }

    public function confirmTrigger(): void
    {
        $this->validate([
            'selectedFormId' => ['required', 'integer', 'exists:forms,id'],
        ]);

        $trigger = [
            'type' => 'form_submitted',
            'form_id' => $this->selectedFormId,
        ];

        if ($this->triggerType === 'subscribe') {
            $this->subscribeTriggers[] = $trigger;
        } else {
            $this->unsubscribeTriggers[] = $trigger;
        }

        $this->showTriggerModal = false;
        $this->selectedFormId = null;

        Flux::toast(text: 'Trigger added', variant: 'success');
    }

    public function cancelTrigger(): void
    {
        $this->showTriggerModal = false;
        $this->selectedFormId = null;
    }

    public function removeTrigger(string $type, int $index): void
    {
        if ($type === 'subscribe') {
            unset($this->subscribeTriggers[$index]);
            $this->subscribeTriggers = array_values($this->subscribeTriggers);
        } else {
            unset($this->unsubscribeTriggers[$index]);
            $this->unsubscribeTriggers = array_values($this->unsubscribeTriggers);
        }

        Flux::toast(text: 'Trigger removed', variant: 'success');
    }

    public function addEmail(): void
    {
        $this->editingEmailIndex = null;
        $this->emailData = [
            'name' => '',
            'subject' => '',
            'preview_text' => '',
            'body' => '',
            'delay_days' => 0,
            'delay_hours' => 0,
            'send_time' => '08:00:00',
            'timezone' => 'America/New_York',
            'order' => count($this->emails) + 1,
        ];
        $this->showEmailEditor = true;
    }

    public function editEmail(int $index): void
    {
        $this->editingEmailIndex = $index;
        $this->emailData = $this->emails[$index];
        $this->showEmailEditor = true;
    }

    public function saveEmail(): void
    {
        $validated = $this->validate([
            'emailData.name' => ['required', 'string', 'max:255'],
            'emailData.subject' => ['required', 'string', 'max:140'],
            'emailData.preview_text' => ['nullable', 'string', 'max:140'],
            'emailData.body' => ['required', 'string'],
            'emailData.delay_days' => ['required', 'integer', 'min:0'],
            'emailData.delay_hours' => ['required', 'integer', 'min:0', 'max:23'],
            'emailData.send_time' => ['required'],
            'emailData.timezone' => ['required', 'string'],
        ]);

        // Save directly to database
        if ($this->editingEmailIndex !== null && isset($this->emails[$this->editingEmailIndex]['id'])) {
            // Update existing email
            $email = EmailSequenceEmail::find($this->emails[$this->editingEmailIndex]['id']);
            $email->update([
                'name' => $this->emailData['name'],
                'subject' => $this->emailData['subject'],
                'preview_text' => $this->emailData['preview_text'] ?? null,
                'body' => $this->emailData['body'],
                'delay_days' => $this->emailData['delay_days'],
                'delay_hours' => $this->emailData['delay_hours'] ?? 0,
                'send_time' => $this->emailData['send_time'],
                'timezone' => $this->emailData['timezone'],
            ]);

            // Update in memory
            $this->emails[$this->editingEmailIndex] = array_merge($this->emailData, ['id' => $email->id]);
        } else {
            // Create new email
            $email = EmailSequenceEmail::create([
                'email_sequence_id' => $this->emailSequence->id,
                'name' => $this->emailData['name'],
                'subject' => $this->emailData['subject'],
                'preview_text' => $this->emailData['preview_text'] ?? null,
                'body' => $this->emailData['body'],
                'delay_days' => $this->emailData['delay_days'],
                'delay_hours' => $this->emailData['delay_hours'] ?? 0,
                'send_time' => $this->emailData['send_time'],
                'timezone' => $this->emailData['timezone'],
                'order' => count($this->emails) + 1,
            ]);

            // Add to memory with ID
            $this->emails[] = array_merge($this->emailData, ['id' => $email->id, 'order' => $email->order]);
        }

        $this->showEmailEditor = false;
        $this->emailData = [];
        $this->editingEmailIndex = null;

        Flux::toast(text: 'Email saved', variant: 'success');
    }

    public function cancelEmailEdit(): void
    {
        $this->showEmailEditor = false;
        $this->emailData = [];
        $this->editingEmailIndex = null;
    }

    public function deleteEmail(int $index): void
    {
        // Delete from database if it has an ID
        if (isset($this->emails[$index]['id'])) {
            EmailSequenceEmail::destroy($this->emails[$index]['id']);
        }

        // Remove from memory
        unset($this->emails[$index]);
        $this->emails = array_values($this->emails);

        Flux::toast(text: 'Email deleted', variant: 'success');
    }

    public function moveEmailUp(int $index): void
    {
        if ($index > 0) {
            $temp = $this->emails[$index];
            $this->emails[$index] = $this->emails[$index - 1];
            $this->emails[$index - 1] = $temp;

            // Update order in database
            $this->updateEmailOrders();
        }
    }

    public function moveEmailDown(int $index): void
    {
        if ($index < count($this->emails) - 1) {
            $temp = $this->emails[$index];
            $this->emails[$index] = $this->emails[$index + 1];
            $this->emails[$index + 1] = $temp;

            // Update order in database
            $this->updateEmailOrders();
        }
    }

    protected function updateEmailOrders(): void
    {
        foreach ($this->emails as $index => $email) {
            if (isset($email['id'])) {
                EmailSequenceEmail::where('id', $email['id'])->update(['order' => $index + 1]);
            }
        }
    }

    public function getAvailableMergeTags(): array
    {
        $mergeTags = [
            ['tag' => '{email}', 'description' => 'Email address'],
            ['tag' => '{unsubscribe_url}', 'description' => 'Unsubscribe link'],
        ];

        if ($this->mergeTagFormId) {
            $form = Form::find($this->mergeTagFormId);
            if ($form && is_array($form->fields)) {
                foreach ($form->fields as $field) {
                    if (isset($field['name']) && $field['name'] !== 'email') {
                        $label = $field['label'] ?? ucfirst(str_replace('_', ' ', $field['name']));
                        $mergeTags[] = [
                            'tag' => '{'.$field['name'].'}',
                            'description' => $label,
                        ];
                    }
                }
            }
        }

        return $mergeTags;
    }

    public function save()
    {
        $this->validate();

        // Build anchor datetime if in before_anchor_date mode
        $anchorDatetime = null;
        if ($this->sequenceMode === 'before_anchor_date' && $this->anchorDate && $this->anchorTime) {
            $anchorDatetime = \Carbon\Carbon::createFromFormat(
                'Y-m-d H:i',
                $this->anchorDate.' '.$this->anchorTime,
                $this->anchorTimezone
            )->utc();
        }

        $this->emailSequence->fill([
            'name' => $this->name,
            'description' => $this->description,
            'sequence_mode' => $this->sequenceMode,
            'anchor_datetime' => $anchorDatetime,
            'anchor_timezone' => $this->anchorTimezone,
            'send_welcome_email' => $this->sendWelcomeEmail,
            'welcome_email_subject' => $this->sendWelcomeEmail ? $this->welcomeEmailSubject : null,
            'welcome_email_body' => $this->sendWelcomeEmail ? $this->welcomeEmailBody : null,
            'welcome_email_preview_text' => $this->sendWelcomeEmail ? $this->welcomeEmailPreviewText : null,
            'subscribe_triggers' => $this->subscribeTriggers,
            'unsubscribe_triggers' => $this->unsubscribeTriggers,
            'updated_by' => auth()->id(),
        ]);

        $this->emailSequence->save();

        Flux::toast(text: 'Email sequence saved successfully', variant: 'success');

        return redirect()->route('admin.marketing.email-sequences.index');
    }

    public function render()
    {
        return view('livewire.admin.email-sequences.email-sequence-edit', [
            'triggerTypes' => EmailTriggerType::toOptions(),
            'sequenceModes' => SequenceMode::toOptions(),
            'forms' => Form::orderBy('title')->get(['id', 'title']),
            'landingPages' => LandingPage::where('status', 'published')->orderBy('title')->get(['id', 'title']),
            'timezones' => $this->getTimezoneOptions(),
        ]);
    }

    protected function getTimezoneOptions(): array
    {
        return [
            ['value' => 'America/New_York', 'label' => 'Eastern Time (ET)'],
            ['value' => 'America/Chicago', 'label' => 'Central Time (CT)'],
            ['value' => 'America/Denver', 'label' => 'Mountain Time (MT)'],
            ['value' => 'America/Los_Angeles', 'label' => 'Pacific Time (PT)'],
            ['value' => 'America/Anchorage', 'label' => 'Alaska Time (AKT)'],
            ['value' => 'Pacific/Honolulu', 'label' => 'Hawaii Time (HT)'],
            ['value' => 'UTC', 'label' => 'UTC'],
            ['value' => 'Europe/London', 'label' => 'London (GMT/BST)'],
            ['value' => 'Europe/Paris', 'label' => 'Paris (CET/CEST)'],
            ['value' => 'Asia/Tokyo', 'label' => 'Tokyo (JST)'],
            ['value' => 'Australia/Sydney', 'label' => 'Sydney (AEST/AEDT)'],
        ];
    }
}

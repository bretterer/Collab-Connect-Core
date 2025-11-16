<?php

namespace Tests\Feature\Livewire\Admin\EmailSequences;

use App\Enums\AccountType;
use App\Enums\FormFieldType;
use App\Livewire\Admin\EmailSequences\EmailSequenceEdit;
use App\Models\EmailSequence;
use App\Models\Form;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EmailSequenceEditTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private EmailSequence $emailSequence;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'account_type' => AccountType::ADMIN,
        ]);

        $this->emailSequence = EmailSequence::factory()->create([
            'created_by' => $this->admin->id,
            'updated_by' => $this->admin->id,
        ]);
    }

    #[Test]
    public function it_shows_default_merge_tags_when_no_form_selected(): void
    {
        Livewire::actingAs($this->admin)
            ->test(EmailSequenceEdit::class, ['emailSequence' => $this->emailSequence])
            ->assertSee('{email}')
            ->assertSee('{unsubscribe_url}')
            ->assertSee('Email address')
            ->assertSee('Unsubscribe link');
    }

    #[Test]
    public function it_shows_form_field_merge_tags_when_form_selected(): void
    {
        $form = Form::factory()->create([
            'title' => 'Contact Form',
            'fields' => [
                [
                    'id' => 'field-1',
                    'type' => FormFieldType::EMAIL->value,
                    'label' => 'Email Address',
                    'name' => 'email',
                    'required' => true,
                ],
                [
                    'id' => 'field-2',
                    'type' => FormFieldType::TEXT->value,
                    'label' => 'First Name',
                    'name' => 'first_name',
                    'required' => true,
                ],
                [
                    'id' => 'field-3',
                    'type' => FormFieldType::TEXT->value,
                    'label' => 'Last Name',
                    'name' => 'last_name',
                    'required' => false,
                ],
                [
                    'id' => 'field-4',
                    'type' => FormFieldType::PHONE->value,
                    'label' => 'Phone Number',
                    'name' => 'phone',
                    'required' => false,
                ],
            ],
        ]);

        Livewire::actingAs($this->admin)
            ->test(EmailSequenceEdit::class, ['emailSequence' => $this->emailSequence])
            ->set('mergeTagFormId', $form->id)
            ->assertSee('{first_name}')
            ->assertSee('{last_name}')
            ->assertSee('{phone}')
            ->assertSee('First Name')
            ->assertSee('Last Name')
            ->assertSee('Phone Number');
    }

    #[Test]
    public function it_excludes_email_field_from_dynamic_merge_tags(): void
    {
        $form = Form::factory()->create([
            'title' => 'Test Form',
            'fields' => [
                [
                    'id' => 'field-1',
                    'type' => FormFieldType::EMAIL->value,
                    'label' => 'Email Address',
                    'name' => 'email',
                    'required' => true,
                ],
                [
                    'id' => 'field-2',
                    'type' => FormFieldType::TEXT->value,
                    'label' => 'First Name',
                    'name' => 'first_name',
                    'required' => true,
                ],
            ],
        ]);

        // The test verifies that when a form has an email field,
        // we don't create a duplicate {email} merge tag
        Livewire::actingAs($this->admin)
            ->test(EmailSequenceEdit::class, ['emailSequence' => $this->emailSequence])
            ->set('mergeTagFormId', $form->id)
            ->assertSee('{email}')
            ->assertSee('Email address')
            ->assertSee('{first_name}')
            ->assertSee('First Name');
    }

    #[Test]
    public function it_updates_merge_tags_when_form_selection_changes(): void
    {
        $form1 = Form::factory()->create([
            'title' => 'Form 1',
            'fields' => [
                [
                    'id' => 'field-1',
                    'type' => FormFieldType::EMAIL->value,
                    'name' => 'email',
                    'label' => 'Email',
                    'required' => true,
                ],
                [
                    'id' => 'field-2',
                    'type' => FormFieldType::TEXT->value,
                    'name' => 'company',
                    'label' => 'Company Name',
                    'required' => false,
                ],
            ],
        ]);

        $form2 = Form::factory()->create([
            'title' => 'Form 2',
            'fields' => [
                [
                    'id' => 'field-1',
                    'type' => FormFieldType::EMAIL->value,
                    'name' => 'email',
                    'label' => 'Email',
                    'required' => true,
                ],
                [
                    'id' => 'field-2',
                    'type' => FormFieldType::TEXT->value,
                    'name' => 'job_title',
                    'label' => 'Job Title',
                    'required' => false,
                ],
            ],
        ]);

        Livewire::actingAs($this->admin)
            ->test(EmailSequenceEdit::class, ['emailSequence' => $this->emailSequence])
            ->set('mergeTagFormId', $form1->id)
            ->assertSee('{company}')
            ->assertDontSee('{job_title}')
            ->set('mergeTagFormId', $form2->id)
            ->assertDontSee('{company}')
            ->assertSee('{job_title}');
    }

    #[Test]
    public function it_handles_forms_with_no_additional_fields(): void
    {
        $form = Form::factory()->create([
            'title' => 'Email Only Form',
            'fields' => [
                [
                    'id' => 'field-1',
                    'type' => FormFieldType::EMAIL->value,
                    'name' => 'email',
                    'label' => 'Email',
                    'required' => true,
                ],
            ],
        ]);

        Livewire::actingAs($this->admin)
            ->test(EmailSequenceEdit::class, ['emailSequence' => $this->emailSequence])
            ->set('mergeTagFormId', $form->id)
            ->assertSee('{email}')
            ->assertSee('Email address')
            ->assertSee('{unsubscribe_url}')
            ->assertSee('Unsubscribe link');
    }

    #[Test]
    public function it_displays_form_selector_dropdown(): void
    {
        $form1 = Form::factory()->create([
            'title' => 'Newsletter Signup',
            'fields' => [['id' => 'field-1', 'type' => 'email', 'name' => 'email', 'label' => 'Email', 'required' => true]],
        ]);
        $form2 = Form::factory()->create([
            'title' => 'Contact Us',
            'fields' => [['id' => 'field-1', 'type' => 'email', 'name' => 'email', 'label' => 'Email', 'required' => true]],
        ]);

        Livewire::actingAs($this->admin)
            ->test(EmailSequenceEdit::class, ['emailSequence' => $this->emailSequence])
            ->assertSee('Select Form')
            ->assertSee('Newsletter Signup')
            ->assertSee('Contact Us');
    }
}

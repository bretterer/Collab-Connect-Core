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

        $component = Livewire::actingAs($this->admin)
            ->test(EmailSequenceEdit::class, ['emailSequence' => $this->emailSequence])
            ->set('mergeTagFormId', $form->id);

        $mergeTags = $component->viewData('this')->getAvailableMergeTags();
        $emailCount = collect($mergeTags)->filter(fn ($tag) => $tag['tag'] === '{email}')->count();

        // Email should appear exactly once (from default merge tags)
        $this->assertEquals(1, $emailCount);
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

        $component = Livewire::actingAs($this->admin)
            ->test(EmailSequenceEdit::class, ['emailSequence' => $this->emailSequence])
            ->set('mergeTagFormId', $form->id);

        $mergeTags = $component->viewData('this')->getAvailableMergeTags();

        // Should only have default tags (email and unsubscribe_url)
        $this->assertCount(2, $mergeTags);
        $this->assertEquals('{email}', $mergeTags[0]['tag']);
        $this->assertEquals('{unsubscribe_url}', $mergeTags[1]['tag']);
    }

    #[Test]
    public function it_displays_form_selector_dropdown(): void
    {
        $form1 = Form::factory()->create(['title' => 'Newsletter Signup']);
        $form2 = Form::factory()->create(['title' => 'Contact Us']);

        Livewire::actingAs($this->admin)
            ->test(EmailSequenceEdit::class, ['emailSequence' => $this->emailSequence])
            ->assertSee('Select Form')
            ->assertSee('Newsletter Signup')
            ->assertSee('Contact Us');
    }
}

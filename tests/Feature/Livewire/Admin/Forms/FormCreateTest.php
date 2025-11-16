<?php

namespace Tests\Feature\Livewire\Admin\Forms;

use App\Enums\AccountType;
use App\Enums\FormFieldType;
use App\Livewire\Admin\Forms\FormCreate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FormCreateTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
    }

    #[Test]
    public function it_automatically_adds_email_field_on_mount(): void
    {
        Livewire::actingAs($this->admin)
            ->test(FormCreate::class)
            ->assertSet('fields', function ($fields) {
                $this->assertCount(1, $fields);
                $this->assertEquals(FormFieldType::EMAIL->value, $fields[0]['type']);
                $this->assertEquals('Email Address', $fields[0]['label']);
                $this->assertEquals('email', $fields[0]['name']);
                $this->assertTrue($fields[0]['required']);

                return true;
            });
    }

    #[Test]
    public function it_can_add_additional_fields(): void
    {
        Livewire::actingAs($this->admin)
            ->test(FormCreate::class)
            ->call('addField', FormFieldType::TEXT->value)
            ->assertSet('fields', function ($fields) {
                $this->assertCount(2, $fields);
                $this->assertEquals(FormFieldType::EMAIL->value, $fields[0]['type']);
                $this->assertEquals(FormFieldType::TEXT->value, $fields[1]['type']);

                return true;
            });
    }

    #[Test]
    public function it_prevents_deletion_of_email_field(): void
    {
        Livewire::actingAs($this->admin)
            ->test(FormCreate::class)
            ->call('deleteField', 0)
            ->call('confirmRemoveField')
            ->assertSet('fields', function ($fields) {
                // Email field should still exist
                $this->assertCount(1, $fields);
                $this->assertEquals(FormFieldType::EMAIL->value, $fields[0]['type']);

                return true;
            });
    }

    #[Test]
    public function it_can_delete_non_email_fields(): void
    {
        Livewire::actingAs($this->admin)
            ->test(FormCreate::class)
            ->call('addField', FormFieldType::TEXT->value)
            ->assertCount('fields', 2)
            ->call('deleteField', 1)
            ->call('confirmRemoveField')
            ->assertCount('fields', 1)
            ->assertSet('fields', function ($fields) {
                // Only email field should remain
                $this->assertEquals(FormFieldType::EMAIL->value, $fields[0]['type']);

                return true;
            });
    }

    #[Test]
    public function it_can_reorder_fields_including_email(): void
    {
        Livewire::actingAs($this->admin)
            ->test(FormCreate::class)
            ->call('addField', FormFieldType::TEXT->value)
            ->assertSet('fields.0.type', FormFieldType::EMAIL->value)
            ->assertSet('fields.1.type', FormFieldType::TEXT->value)
            ->call('moveFieldDown', 0)
            ->assertSet('fields.0.type', FormFieldType::TEXT->value)
            ->assertSet('fields.1.type', FormFieldType::EMAIL->value);
    }

    #[Test]
    public function it_validates_email_field_exists_on_save(): void
    {
        // Manually clear fields to test validation
        $component = Livewire::actingAs($this->admin)
            ->test(FormCreate::class);

        $component->set('fields', [])
            ->set('title', 'Test Form')
            ->call('save')
            ->assertHasErrors(['fields']);
    }

    #[Test]
    public function it_can_save_form_with_email_field(): void
    {
        Livewire::actingAs($this->admin)
            ->test(FormCreate::class)
            ->set('title', 'Contact Form')
            ->set('description', 'Test form description')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('forms', [
            'title' => 'Contact Form',
            'description' => 'Test form description',
        ]);
    }

    #[Test]
    public function it_can_publish_form_with_email_field(): void
    {
        Livewire::actingAs($this->admin)
            ->test(FormCreate::class)
            ->set('title', 'Newsletter Form')
            ->call('save', true)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('forms', [
            'title' => 'Newsletter Form',
            'status' => 'published',
        ]);
    }

    #[Test]
    public function it_prevents_editing_of_email_field(): void
    {
        Livewire::actingAs($this->admin)
            ->test(FormCreate::class)
            ->call('editField', 0)
            ->assertSet('editingFieldIndex', null)
            ->assertSet('fieldData', []);
    }

    #[Test]
    public function it_can_edit_non_email_fields(): void
    {
        Livewire::actingAs($this->admin)
            ->test(FormCreate::class)
            ->call('addField', FormFieldType::TEXT->value)
            ->call('editField', 1)
            ->assertSet('editingFieldIndex', 1)
            ->assertSet('fieldData.type', FormFieldType::TEXT->value);
    }
}

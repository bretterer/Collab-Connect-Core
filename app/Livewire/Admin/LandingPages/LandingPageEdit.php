<?php

namespace App\Livewire\Admin\LandingPages;

use App\Enums\LandingPageBlockType;
use App\Models\LandingPage;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class LandingPageEdit extends Component
{
    public LandingPage $landingPage;

    public string $title = '';

    public string $slug = '';

    public string $description = '';

    public string $status = 'draft';

    public array $sections = [];

    public bool $showBlockSelector = false;

    public ?string $selectedSectionId = null;

    public ?int $editingBlockIndex = null;

    public array $blockData = [];

    public string $previewScale = 'desktop';

    public bool $showPreview = true;

    public ?int $deletingBlockIndex = null;

    public ?string $deletingSectionId = null;

    public bool $showDeleteSectionModal = false;

    public array $sectionSettings = [];

    public ?string $editingSectionId = null;

    // Two Step Optin Properties
    public bool $twoStepOptinEnabled = false;

    public array $twoStepOptinBlocks = [];

    public bool $editingTwoStepOptin = false;

    public ?int $editingTwoStepOptinBlockIndex = null;

    public array $twoStepOptinBlockData = [];

    // Exit Popup Properties
    public bool $exitPopupEnabled = false;

    public array $exitPopupBlocks = [];

    public bool $editingExitPopup = false;

    public ?int $editingExitPopupBlockIndex = null;

    public array $exitPopupBlockData = [];

    public function mount(LandingPage $landingPage)
    {
        $this->landingPage = $landingPage;
        $this->title = $landingPage->title;
        $this->slug = $landingPage->slug;
        $this->description = $landingPage->description ?? '';
        $this->status = $landingPage->status;
        $this->sections = $landingPage->blocks ?? [];

        // Load Two Step Optin
        if ($landingPage->two_step_optin) {
            $this->twoStepOptinEnabled = $landingPage->two_step_optin['enabled'] ?? false;
            $this->twoStepOptinBlocks = $landingPage->two_step_optin['blocks'] ?? [];
        }

        // Load Exit Popup
        if ($landingPage->exit_popup) {
            $this->exitPopupEnabled = $landingPage->exit_popup['enabled'] ?? false;
            $this->exitPopupBlocks = $landingPage->exit_popup['blocks'] ?? [];
        }
    }

    protected function rules()
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:landing_pages,slug,'.$this->landingPage->id, 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'description' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'in:draft,published,archived'],
            'sections' => ['array'],
        ];
    }

    public function updatedTitle($value)
    {
        // Don't auto-update slug on edit to avoid breaking URLs
    }

    // Section Management
    public function addSection()
    {
        $section = [
            'id' => uniqid('section-'),
            'name' => 'New Section',
            'settings' => $this->getDefaultSectionSettings(),
            'blocks' => [],
        ];

        $this->sections[] = $section;
        $this->selectedSectionId = $section['id'];

        Flux::toast(text: 'Section added', variant: 'success');
    }

    public function removeSection($sectionId)
    {
        $this->sections = array_filter($this->sections, fn ($s) => $s['id'] !== $sectionId);
        $this->sections = array_values($this->sections);
        $this->selectedSectionId = null;

        Flux::toast(text: 'Section removed', variant: 'success');
    }

    public function deleteSection($sectionId)
    {
        $this->deletingSectionId = $sectionId;
        Flux::modal(name: 'delete-section-modal')->show();
    }

    public function confirmRemoveSection()
    {
        if ($this->deletingSectionId !== null) {
            $this->sections = array_filter($this->sections, fn ($s) => $s['id'] !== $this->deletingSectionId);
            $this->sections = array_values($this->sections);
            $this->selectedSectionId = null;

            Flux::toast(text: 'Section removed', variant: 'success');
            $this->deletingSectionId = null;

            Flux::modal(name: 'delete-section-modal')->close();
        }
    }

    public function renameSection($sectionId, $newName)
    {
        foreach ($this->sections as &$section) {
            if ($section['id'] === $sectionId) {
                $section['name'] = $newName;
                break;
            }
        }
    }

    public function selectSection($sectionId)
    {
        $this->selectedSectionId = $sectionId;
        $this->editingBlockIndex = null;
    }

    public function editSectionSettings($sectionId)
    {
        $this->editingSectionId = $sectionId;
        foreach ($this->sections as $section) {
            if ($section['id'] === $sectionId) {
                $this->sectionSettings = $section['settings'];
                break;
            }
        }

        Flux::modal('section-settings')->show();
    }

    public function saveSectionSettings()
    {
        if ($this->editingSectionId) {
            foreach ($this->sections as &$section) {
                if ($section['id'] === $this->editingSectionId) {
                    $section['settings'] = $this->sectionSettings;
                    break;
                }
            }
            $this->editingSectionId = null;
            $this->sectionSettings = [];

            Flux::modal('section-settings')->close();
            Flux::toast(text: 'Section settings updated', variant: 'success');
        }
    }

    public function cancelSectionSettings()
    {
        $this->editingSectionId = null;
        $this->sectionSettings = [];

        Flux::modal('section-settings')->close();
    }

    // Block Management
    public function addBlockToSection($sectionId, $blockType)
    {
        $block = [
            'id' => uniqid('block-'),
            'type' => $blockType,
            'data' => $this->getDefaultBlockData($blockType),
        ];

        $blockIndex = null;
        foreach ($this->sections as &$section) {
            if ($section['id'] === $sectionId) {
                $section['blocks'][] = $block;
                $blockIndex = count($section['blocks']) - 1;
                break;
            }
        }

        $this->showBlockSelector = false;

        // Automatically open the edit modal for the newly added block
        if ($blockIndex !== null) {
            $this->editBlock($sectionId, $blockIndex);
        }

        Flux::toast(text: 'Block added', variant: 'success');
    }

    public function editBlock($sectionId, $blockIndex)
    {
        $this->selectedSectionId = $sectionId;
        $this->editingBlockIndex = $blockIndex;

        foreach ($this->sections as $section) {
            if ($section['id'] === $sectionId) {
                $this->blockData = $section['blocks'][$blockIndex]['data'] ?? [];
                break;
            }
        }
    }

    public function saveBlockEdit()
    {
        if ($this->selectedSectionId !== null && $this->editingBlockIndex !== null) {
            foreach ($this->sections as &$section) {
                if ($section['id'] === $this->selectedSectionId) {
                    $section['blocks'][$this->editingBlockIndex]['data'] = $this->blockData;
                    break;
                }
            }

            $this->editingBlockIndex = null;
            $this->blockData = [];

            Flux::toast(text: 'Block updated', variant: 'success');
        }
    }

    public function cancelBlockEdit()
    {
        $this->editingBlockIndex = null;
        $this->blockData = [];
    }

    public function duplicateBlock($sectionId, $blockIndex)
    {
        foreach ($this->sections as &$section) {
            if ($section['id'] === $sectionId) {
                $originalBlock = $section['blocks'][$blockIndex];
                $duplicatedBlock = [
                    'id' => uniqid('block-'),
                    'type' => $originalBlock['type'],
                    'data' => $originalBlock['data'],
                ];

                // Insert the duplicated block right after the original
                array_splice($section['blocks'], $blockIndex + 1, 0, [$duplicatedBlock]);
                break;
            }
        }

        Flux::toast(text: 'Block duplicated', variant: 'success');
    }

    public function deleteBlock($sectionId, $blockIndex)
    {
        $this->selectedSectionId = $sectionId;
        $this->deletingBlockIndex = $blockIndex;
        Flux::modal(name: 'delete-block-modal')->show();
    }

    public function confirmRemoveBlock()
    {
        if ($this->selectedSectionId !== null && $this->deletingBlockIndex !== null) {
            foreach ($this->sections as &$section) {
                if ($section['id'] === $this->selectedSectionId) {
                    unset($section['blocks'][$this->deletingBlockIndex]);
                    $section['blocks'] = array_values($section['blocks']);
                    break;
                }
            }

            $this->deletingBlockIndex = null;
            $this->selectedSectionId = null;

            Flux::toast(text: 'Block removed', variant: 'success');
            Flux::modal(name: 'delete-block-modal')->close();
        }
    }

    public function moveBlockUp($sectionId, $blockIndex)
    {
        if ($blockIndex > 0) {
            foreach ($this->sections as &$section) {
                if ($section['id'] === $sectionId) {
                    $temp = $section['blocks'][$blockIndex];
                    $section['blocks'][$blockIndex] = $section['blocks'][$blockIndex - 1];
                    $section['blocks'][$blockIndex - 1] = $temp;
                    break;
                }
            }
        }
    }

    public function moveBlockDown($sectionId, $blockIndex)
    {
        foreach ($this->sections as &$section) {
            if ($section['id'] === $sectionId) {
                if ($blockIndex < count($section['blocks']) - 1) {
                    $temp = $section['blocks'][$blockIndex];
                    $section['blocks'][$blockIndex] = $section['blocks'][$blockIndex + 1];
                    $section['blocks'][$blockIndex + 1] = $temp;
                }
                break;
            }
        }
    }

    public function moveSectionUp($sectionIndex)
    {
        if ($sectionIndex > 0) {
            $temp = $this->sections[$sectionIndex];
            $this->sections[$sectionIndex] = $this->sections[$sectionIndex - 1];
            $this->sections[$sectionIndex - 1] = $temp;
        }
    }

    public function moveSectionDown($sectionIndex)
    {
        if ($sectionIndex < count($this->sections) - 1) {
            $temp = $this->sections[$sectionIndex];
            $this->sections[$sectionIndex] = $this->sections[$sectionIndex + 1];
            $this->sections[$sectionIndex + 1] = $temp;
        }
    }

    // Two Step Optin Management
    public function toggleTwoStepOptin()
    {
        $this->twoStepOptinEnabled = ! $this->twoStepOptinEnabled;
        Flux::toast(
            text: $this->twoStepOptinEnabled ? 'Two-step optin enabled' : 'Two-step optin disabled',
            variant: 'success'
        );
    }

    public function addTwoStepOptinBlock($blockType)
    {
        $block = [
            'id' => uniqid('block-'),
            'type' => $blockType,
            'data' => $this->getDefaultBlockData($blockType),
        ];

        $this->twoStepOptinBlocks[] = $block;
        $blockIndex = count($this->twoStepOptinBlocks) - 1;

        // Automatically open the edit modal for the newly added block
        $this->editTwoStepOptinBlock($blockIndex);

        Flux::toast(text: 'Block added', variant: 'success');
    }

    public function editTwoStepOptinBlock($blockIndex)
    {
        $this->editingTwoStepOptinBlockIndex = $blockIndex;
        $this->twoStepOptinBlockData = $this->twoStepOptinBlocks[$blockIndex]['data'] ?? [];
        $this->editingTwoStepOptin = true;
    }

    public function saveTwoStepOptinBlock()
    {
        if ($this->editingTwoStepOptinBlockIndex !== null) {
            $this->twoStepOptinBlocks[$this->editingTwoStepOptinBlockIndex]['data'] = $this->twoStepOptinBlockData;
            $this->editingTwoStepOptinBlockIndex = null;
            $this->twoStepOptinBlockData = [];
            $this->editingTwoStepOptin = false;

            Flux::toast(text: 'Block updated', variant: 'success');
        }
    }

    public function cancelTwoStepOptinBlockEdit()
    {
        $this->editingTwoStepOptinBlockIndex = null;
        $this->twoStepOptinBlockData = [];
        $this->editingTwoStepOptin = false;
    }

    public function deleteTwoStepOptinBlock($blockIndex)
    {
        unset($this->twoStepOptinBlocks[$blockIndex]);
        $this->twoStepOptinBlocks = array_values($this->twoStepOptinBlocks);
        Flux::toast(text: 'Block removed', variant: 'success');
    }

    public function duplicateTwoStepOptinBlock($blockIndex)
    {
        $originalBlock = $this->twoStepOptinBlocks[$blockIndex];
        $duplicatedBlock = [
            'id' => uniqid('block-'),
            'type' => $originalBlock['type'],
            'data' => $originalBlock['data'],
        ];

        array_splice($this->twoStepOptinBlocks, $blockIndex + 1, 0, [$duplicatedBlock]);
        Flux::toast(text: 'Block duplicated', variant: 'success');
    }

    // Exit Popup Management
    public function toggleExitPopup()
    {
        $this->exitPopupEnabled = ! $this->exitPopupEnabled;
        Flux::toast(
            text: $this->exitPopupEnabled ? 'Exit popup enabled' : 'Exit popup disabled',
            variant: 'success'
        );
    }

    public function addExitPopupBlock($blockType)
    {
        $block = [
            'id' => uniqid('block-'),
            'type' => $blockType,
            'data' => $this->getDefaultBlockData($blockType),
        ];

        $this->exitPopupBlocks[] = $block;
        $blockIndex = count($this->exitPopupBlocks) - 1;

        // Automatically open the edit modal for the newly added block
        $this->editExitPopupBlock($blockIndex);

        Flux::toast(text: 'Block added', variant: 'success');
    }

    public function editExitPopupBlock($blockIndex)
    {
        $this->editingExitPopupBlockIndex = $blockIndex;
        $this->exitPopupBlockData = $this->exitPopupBlocks[$blockIndex]['data'] ?? [];
        $this->editingExitPopup = true;
    }

    public function saveExitPopupBlock()
    {
        if ($this->editingExitPopupBlockIndex !== null) {
            $this->exitPopupBlocks[$this->editingExitPopupBlockIndex]['data'] = $this->exitPopupBlockData;
            $this->editingExitPopupBlockIndex = null;
            $this->exitPopupBlockData = [];
            $this->editingExitPopup = false;

            Flux::toast(text: 'Block updated', variant: 'success');
        }
    }

    public function cancelExitPopupBlockEdit()
    {
        $this->editingExitPopupBlockIndex = null;
        $this->exitPopupBlockData = [];
        $this->editingExitPopup = false;
    }

    public function deleteExitPopupBlock($blockIndex)
    {
        unset($this->exitPopupBlocks[$blockIndex]);
        $this->exitPopupBlocks = array_values($this->exitPopupBlocks);
        Flux::toast(text: 'Block removed', variant: 'success');
    }

    public function duplicateExitPopupBlock($blockIndex)
    {
        $originalBlock = $this->exitPopupBlocks[$blockIndex];
        $duplicatedBlock = [
            'id' => uniqid('block-'),
            'type' => $originalBlock['type'],
            'data' => $originalBlock['data'],
        ];

        array_splice($this->exitPopupBlocks, $blockIndex + 1, 0, [$duplicatedBlock]);
        Flux::toast(text: 'Block duplicated', variant: 'success');
    }

    public function save($publish = false)
    {
        $this->validate();

        $this->landingPage->update([
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'blocks' => $this->sections, // Store sections in blocks column
            'two_step_optin' => $this->twoStepOptinEnabled ? [
                'enabled' => true,
                'blocks' => $this->twoStepOptinBlocks,
            ] : null,
            'exit_popup' => $this->exitPopupEnabled ? [
                'enabled' => true,
                'blocks' => $this->exitPopupBlocks,
            ] : null,
            'status' => $publish ? 'published' : $this->status,
            'published_at' => $publish && ! $this->landingPage->isPublished() ? now() : $this->landingPage->published_at,
            'updated_by' => auth()->id(),
        ]);

        Flux::toast(
            text: $publish ? 'Landing page published successfully' : 'Landing page updated',
            variant: 'success'
        );
    }

    private function getDefaultSectionSettings(): array
    {
        return [
            // Background
            'background_color' => '#ffffff',
            'background_image' => '',
            'background_position' => 'center', // top, center, bottom
            'background_fixed' => false,

            // Desktop Layout
            'desktop_hide' => false,
            'desktop_padding_top' => 64,
            'desktop_padding_bottom' => 64,
            'desktop_padding_left' => 16,
            'desktop_padding_right' => 16,
            'desktop_vertical_align' => 'top', // top, center, bottom
            'desktop_horizontal_align' => 'left', // left, center, right, space-around, space-between

            // Mobile Layout
            'mobile_hide' => false,
            'mobile_padding_top' => 48,
            'mobile_padding_bottom' => 48,
            'mobile_padding_left' => 16,
            'mobile_padding_right' => 16,
        ];
    }

    private function getDefaultBlockData($blockType): array
    {
        return match ($blockType) {
            LandingPageBlockType::HEADER->value => [
                'logo' => '',
                'navigation' => [],
            ],
            LandingPageBlockType::HERO->value => [
                'headline' => 'Your Compelling Headline',
                'subheadline' => 'Supporting text that explains your offer',
                'cta_text' => 'Get Started',
                'cta_url' => '#',
                'image' => '',
                'background_color' => '#ffffff',
            ],
            LandingPageBlockType::TEXT->value => [
                'content' => '<p>Add your content here...</p>',
                'text_align' => 'left',
                'max_width' => 'prose',
            ],
            LandingPageBlockType::IMAGE->value => [
                'url' => '',
                'alt' => '',
                'caption' => '',
                'width' => 'full',
                'alignment' => 'center',
                'rounded' => 'lg',
            ],
            LandingPageBlockType::YOUTUBE->value => [
                'video_id' => '',
                'aspect_ratio' => '16/9',
                'max_width' => 'large',
            ],
            LandingPageBlockType::FEATURES->value => [
                'title' => 'Features',
                'items' => [
                    ['icon' => 'star', 'title' => 'Feature 1', 'description' => 'Description'],
                    ['icon' => 'star', 'title' => 'Feature 2', 'description' => 'Description'],
                    ['icon' => 'star', 'title' => 'Feature 3', 'description' => 'Description'],
                ],
            ],
            LandingPageBlockType::CTA->value => [
                'headline' => 'Ready to get started?',
                'subheadline' => 'Join thousands of satisfied customers',
                'text' => '',
                'width' => 12,
                'button_text' => 'Get Started Now',
                'button_url' => '#',
                'button_action' => 'url',
                'button_new_tab' => false,
                'button_bg_color' => '#3b82f6',
                'button_text_color' => '#ffffff',
                'button_width' => 'auto',
                'button_style' => 'solid',
                'button_size' => 'large',
                'button_border_radius' => 8,
                'background_color' => '#f9fafb',
                'border_type' => 'none',
                'border_width' => 1,
                'border_color' => '#e5e7eb',
                'border_radius' => 0,
                'box_shadow' => 'none',
                'desktop_hide' => false,
                'desktop_text_align' => 'center',
                'desktop_padding_top' => 64,
                'desktop_padding_bottom' => 64,
                'desktop_padding_left' => 16,
                'desktop_padding_right' => 16,
                'desktop_margin_top' => 0,
                'desktop_margin_bottom' => 0,
                'desktop_margin_left' => 0,
                'desktop_margin_right' => 0,
                'desktop_make_flush' => false,
                'mobile_hide' => false,
                'mobile_text_align' => 'center',
                'mobile_padding_top' => 48,
                'mobile_padding_bottom' => 48,
                'mobile_padding_left' => 16,
                'mobile_padding_right' => 16,
                'mobile_margin_top' => 0,
                'mobile_margin_bottom' => 0,
                'mobile_margin_left' => 0,
                'mobile_margin_right' => 0,
            ],
            LandingPageBlockType::TWO_STEP_OPTIN->value => [
                'button_text' => 'Yes! I Want This',
                'modal_headline' => 'Enter your email to continue',
                'form_button_text' => 'Get Instant Access',
                'success_message' => 'Check your email!',
            ],
            LandingPageBlockType::TESTIMONIALS->value => [
                'title' => 'What Our Customers Say',
                'items' => [
                    ['name' => 'John Doe', 'role' => 'CEO, Company', 'content' => 'Great product!', 'image' => ''],
                ],
            ],
            LandingPageBlockType::FAQ->value => [
                'title' => 'Frequently Asked Questions',
                'items' => [
                    ['question' => 'Question 1?', 'answer' => 'Answer 1'],
                    ['question' => 'Question 2?', 'answer' => 'Answer 2'],
                ],
            ],
            LandingPageBlockType::FOOTER->value => [
                'copyright' => '© 2025 Your Company',
                'links' => [],
            ],
            LandingPageBlockType::EXIT_POPUP->value => [
                'headline' => 'Wait! Don\'t Leave Yet',
                'content' => 'Get a special offer before you go',
                'cta_text' => 'Claim Offer',
                'cta_url' => '#',
            ],
            LandingPageBlockType::CUSTOM_HTML->value => [
                'html' => '',
                'css' => '',
                'js' => '',
            ],
            LandingPageBlockType::FORM->value => [
                'form_id' => null,
            ],
            LandingPageBlockType::STRIPE_CHECKOUT->value => [
                'button_text' => 'Buy Now',
                'modal_headline' => 'Complete Your Purchase',
                'modal_description' => 'Enter your information below',
                'stripe_price_id' => '',
                'cancel_url' => '',
                'fields' => [
                    ['name' => 'name', 'label' => 'Full Name', 'type' => 'text', 'required' => true],
                    ['name' => 'email', 'label' => 'Email Address', 'type' => 'email', 'required' => true],
                ],
            ],
            LandingPageBlockType::THANK_YOU->value => [
                'headline' => 'Thank You!',
                'message' => 'Your payment was successful. You\'ll receive a confirmation email shortly.',
                'show_order_id' => true,
                'button_text' => 'Return to Home',
                'button_url' => '/',
            ],
            default => [],
        };
    }

    public function render()
    {
        return view('livewire.admin.landing-pages.landing-page-edit', [
            'blockTypes' => LandingPageBlockType::cases(),
        ]);
    }
}

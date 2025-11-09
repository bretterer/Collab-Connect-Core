<?php

namespace App\Livewire\Admin\LandingPages;

use App\Enums\LandingPageBlockType;
use App\Models\LandingPage;
use Flux\Flux;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class LandingPageCreate extends Component
{
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

    public bool $showSectionSettings = false;

    public array $sectionSettings = [];

    public ?string $editingSectionId = null;

    protected function rules()
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:landing_pages,slug', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'description' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'in:draft,published,archived'],
            'sections' => ['array'],
        ];
    }

    public function updatedTitle($value)
    {
        if (empty($this->slug)) {
            $this->slug = Str::slug($value);
        }
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
        $this->showSectionSettings = false;
        $this->editingBlockIndex = null;
    }

    public function editSectionSettings($sectionId)
    {
        $this->editingSectionId = $sectionId;
        foreach ($this->sections as $section) {
            if ($section['id'] === $sectionId) {
                $this->sectionSettings = $section['settings'];
                $this->showSectionSettings = true;
                break;
            }
        }
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
            $this->showSectionSettings = false;

            Flux::toast(text: 'Section settings updated', variant: 'success');
        }
    }

    public function cancelSectionSettings()
    {
        $this->editingSectionId = null;
        $this->sectionSettings = [];
        $this->showSectionSettings = false;
    }

    // Block Management
    public function addBlockToSection($sectionId, $blockType)
    {
        $block = [
            'id' => uniqid('block-'),
            'type' => $blockType,
            'data' => $this->getDefaultBlockData($blockType),
        ];

        foreach ($this->sections as &$section) {
            if ($section['id'] === $sectionId) {
                $section['blocks'][] = $block;
                break;
            }
        }

        $this->showBlockSelector = false;
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

    public function save($publish = false)
    {
        $this->validate();

        $page = LandingPage::create([
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'blocks' => $this->sections, // Store sections in blocks column
            'status' => $publish ? 'published' : $this->status,
            'published_at' => $publish ? now() : null,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        Flux::toast(
            text: $publish ? 'Landing page published successfully' : 'Landing page saved as draft',
            variant: 'success'
        );

        return redirect()->route('admin.marketing.landing-pages.edit', $page);
    }

    private function getDefaultSectionSettings(): array
    {
        return [
            'background_type' => 'color', // color, image, video
            'background_color' => '#ffffff',
            'background_image' => '',
            'background_video' => '',
            'background_position' => 'center',
            'background_size' => 'cover',
            'background_fixed' => false,
            'padding_top' => 0,
            'padding_bottom' => 0,
            'padding_left' => 0,
            'padding_right' => 0,
            'max_width' => 'full', // full, container
            'text_color' => '#000000',
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
            default => [],
        };
    }

    public function render()
    {
        return view('livewire.admin.landing-pages.landing-page-create', [
            'blockTypes' => LandingPageBlockType::cases(),
        ]);
    }
}

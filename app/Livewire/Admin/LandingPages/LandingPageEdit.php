<?php

namespace App\Livewire\Admin\LandingPages;

use App\LandingPages\BlockRegistry;
use App\Models\LandingPage;
use Flux\Flux;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class LandingPageEdit extends Component
{
    use WithFileUploads;

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

    public bool $showTwoStepOptinBlockSelector = false;

    public bool $editingTwoStepOptin = false;

    public ?int $editingTwoStepOptinBlockIndex = null;

    public array $twoStepOptinBlockData = [];

    public bool $showTwoStepOptinPreview = false;

    // Exit Popup Properties
    public bool $exitPopupEnabled = false;

    public array $exitPopupBlocks = [];

    public bool $showExitPopupBlockSelector = false;

    public bool $editingExitPopup = false;

    public ?int $editingExitPopupBlockIndex = null;

    public array $exitPopupBlockData = [];

    public bool $showExitPopupPreview = false;

    public string $activeEditorTab = 'content';

    public ?TemporaryUploadedFile $sectionBackgroundImage = null;

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

    public function updatedSectionBackgroundImage(): void
    {
        $this->validate([
            'sectionBackgroundImage' => ['required', 'image', 'max:10240'], // 10MB max
        ]);

        try {
            // Process and upload the image
            $image = Image::read($this->sectionBackgroundImage->getRealPath());

            // Generate a unique filename
            $filename = 'landing-pages/backgrounds/'.uniqid('bg_', true).'.jpg';

            // Encode the image
            $encodedImage = $image->encodeByMediaType('image/jpeg', quality: 85);

            // Upload to Linode Object Storage
            Storage::disk('linode')->put($filename, $encodedImage, 'public');

            // Get the public URL
            $imageUrl = Storage::disk('linode')->url($filename);

            // Update section settings
            $this->sectionSettings['background_image'] = $imageUrl;

            // Reset the file input
            $this->sectionBackgroundImage = null;

            Flux::toast(text: 'Background image uploaded successfully', variant: 'success');
        } catch (\Exception $e) {
            $this->addError('sectionBackgroundImage', 'Failed to upload image: '.$e->getMessage());
        }
    }

    public function deleteSectionBackgroundImage(): void
    {
        if (! empty($this->sectionSettings['background_image'])) {
            $imageUrl = $this->sectionSettings['background_image'];

            // Extract the filename from the URL
            $parsedUrl = parse_url($imageUrl);
            $path = ltrim($parsedUrl['path'] ?? '', '/');

            // Delete from storage if it exists
            if (Storage::disk('linode')->exists($path)) {
                Storage::disk('linode')->delete($path);
            }

            // Clear the image from section settings
            $this->sectionSettings['background_image'] = '';

            Flux::toast(text: 'Background image deleted', variant: 'success');
        }
    }

    // Block Management
    public function addBlockToSection($sectionId, $blockType)
    {
        $defaultData = BlockRegistry::getDefaultData($blockType);

        $block = [
            'id' => uniqid('block-'),
            'type' => $blockType,
            'data' => $defaultData,
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
            'data' => BlockRegistry::getDefaultData($blockType),
        ];

        $this->twoStepOptinBlocks[] = $block;
        $blockIndex = count($this->twoStepOptinBlocks) - 1;

        $this->showTwoStepOptinBlockSelector = false;

        // Automatically open the edit panel for the newly added block
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
            'data' => BlockRegistry::getDefaultData($blockType),
        ];

        $this->exitPopupBlocks[] = $block;
        $blockIndex = count($this->exitPopupBlocks) - 1;

        $this->showExitPopupBlockSelector = false;

        // Automatically open the edit panel for the newly added block
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

    public function unpublish()
    {
        $this->validate();

        $this->landingPage->update([
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'blocks' => $this->sections,
            'two_step_optin' => $this->twoStepOptinEnabled ? [
                'enabled' => true,
                'blocks' => $this->twoStepOptinBlocks,
            ] : null,
            'exit_popup' => $this->exitPopupEnabled ? [
                'enabled' => true,
                'blocks' => $this->exitPopupBlocks,
            ] : null,
            'status' => 'draft',
            'published_at' => null,
            'updated_by' => auth()->id(),
        ]);

        $this->status = 'draft';

        Flux::toast(
            text: 'Landing page unpublished',
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
            'overlay_color' => '#000000',
            'overlay_opacity' => 0, // 0-100

            // Desktop Layout
            'desktop_hide' => false,
            'desktop_padding_top' => 0,
            'desktop_padding_bottom' => 0,
            'desktop_padding_left' => 0,
            'desktop_padding_right' => 0,
            'desktop_vertical_align' => 'top', // top, center, bottom
            'desktop_horizontal_align' => 'left', // left, center, right, space-around, space-between

            // Mobile Layout
            'mobile_hide' => false,
            'mobile_padding_top' => 0,
            'mobile_padding_bottom' => 0,
            'mobile_padding_left' => 0,
            'mobile_padding_right' => 0,
        ];
    }

    public function render()
    {
        return view('livewire.admin.landing-pages.landing-page-edit', [
            'blockTypes' => BlockRegistry::all()->map(fn ($block) => (object) $block),
            'publishedLandingPages' => LandingPage::where('status', 'published')
                ->where('id', '!=', $this->landingPage->id)
                ->orderBy('title')
                ->get(['id', 'title', 'slug']),
        ]);
    }
}

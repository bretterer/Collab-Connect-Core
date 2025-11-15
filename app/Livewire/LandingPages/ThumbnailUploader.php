<?php

namespace App\Livewire\LandingPages;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class ThumbnailUploader extends Component
{
    use WithFileUploads;

    public ?TemporaryUploadedFile $photo = null;

    public string $thumbnailUrl = '';

    public string $propertyPrefix = 'blockData';

    public bool $uploading = false;

    public function mount(
        string $thumbnailUrl = '',
        string $propertyPrefix = 'blockData'
    ): void {
        $this->thumbnailUrl = $thumbnailUrl;
        $this->propertyPrefix = $propertyPrefix;
    }

    public function updatedPhoto(): void
    {
        $this->validate([
            'photo' => ['required', 'image', 'max:10240'], // 10MB max
        ]);

        $this->uploading = true;

        try {
            // Process and upload the image
            $this->processAndUploadImage();
        } catch (\Exception $e) {
            $this->uploading = false;
            $this->addError('photo', 'Failed to upload thumbnail: '.$e->getMessage());
        }
    }

    protected function processAndUploadImage(): void
    {
        // Load the image with Intervention Image
        $image = Image::read($this->photo->getRealPath());

        // Generate a unique filename
        $filename = 'landing-pages/thumbnails/'.uniqid('thumb_', true).'.jpg';

        // Encode the image
        $encodedImage = $image->encodeByMediaType('image/jpeg', quality: 85);

        // Upload to Linode Object Storage
        Storage::disk('linode')->put($filename, $encodedImage, 'public');

        // Get the public URL
        $this->thumbnailUrl = Storage::disk('linode')->url($filename);

        // Dispatch event to update parent component
        $this->dispatch('thumbnailUploaded', [
            'thumbnailUrl' => $this->thumbnailUrl,
        ]);

        $this->uploading = false;
        $this->photo = null;
    }

    public function deleteThumbnail(): void
    {
        if ($this->thumbnailUrl) {
            // Extract the path from the URL
            $parsedUrl = parse_url($this->thumbnailUrl);
            $path = ltrim($parsedUrl['path'] ?? '', '/');

            // Delete from storage
            if (Storage::disk('linode')->exists($path)) {
                Storage::disk('linode')->delete($path);
            }
        }

        $this->thumbnailUrl = '';
        $this->dispatch('thumbnailDeleted');
    }

    public function render()
    {
        return view('livewire.landing-pages.thumbnail-uploader');
    }
}

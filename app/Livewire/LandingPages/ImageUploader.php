<?php

namespace App\Livewire\LandingPages;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class ImageUploader extends Component
{
    use WithFileUploads;

    public ?TemporaryUploadedFile $photo = null;

    public string $imageUrl = '';

    public string $imageAlt = '';

    public ?int $imageWidth = null;

    public ?int $imageHeight = null;

    public int $brightness = 0;

    public int $contrast = 0;

    public int $saturation = 0;

    public int $blur = 0;

    public string $propertyPrefix = 'blockData';

    public bool $uploading = false;

    public int $uploadProgress = 0;

    protected $listeners = ['resetImage'];

    public function mount(
        string $imageUrl = '',
        string $imageAlt = '',
        ?int $imageWidth = null,
        ?int $imageHeight = null,
        int $brightness = 0,
        int $contrast = 0,
        int $saturation = 0,
        int $blur = 0,
        string $propertyPrefix = 'blockData'
    ): void {
        $this->imageUrl = $imageUrl;
        $this->imageAlt = $imageAlt;
        $this->imageWidth = $imageWidth;
        $this->imageHeight = $imageHeight;
        $this->brightness = $brightness;
        $this->contrast = $contrast;
        $this->saturation = $saturation;
        $this->blur = $blur;
        $this->propertyPrefix = $propertyPrefix;
    }

    public function updatedPhoto(): void
    {
        $this->validate([
            'photo' => ['required', 'image', 'max:10240'], // 10MB max
        ]);

        $this->uploading = true;
        $this->uploadProgress = 0;

        try {
            // Process and upload the image
            $this->processAndUploadImage();
        } catch (\Exception $e) {
            $this->uploading = false;
            $this->addError('photo', 'Failed to upload image: '.$e->getMessage());
        }
    }

    protected function processAndUploadImage(): void
    {
        // Load the image with Intervention Image
        $image = Image::read($this->photo->getRealPath());

        // Apply basic adjustments
        if ($this->brightness !== 0) {
            $image->brightness($this->brightness);
        }

        if ($this->contrast !== 0) {
            $image->contrast($this->contrast);
        }

        // Apply saturation using colorize (Intervention Image v3 approach)
        if ($this->saturation !== 0) {
            $saturationFactor = $this->saturation / 100;
            $image->greyscale();
            if ($saturationFactor > 0) {
                // For positive saturation, blend with original
                $original = Image::read($this->photo->getRealPath());
                $image->place($original, opacity: abs($saturationFactor) * 100);
            }
        }

        if ($this->blur > 0) {
            $image->blur($this->blur);
        }

        // Resize if dimensions are specified
        if ($this->imageWidth || $this->imageHeight) {
            $image->scale(
                width: $this->imageWidth,
                height: $this->imageHeight
            );
        }

        // Generate a unique filename
        $filename = 'landing-pages/'.uniqid('img_', true).'.jpg';

        // Encode the image
        $encodedImage = $image->encodeByMediaType('image/jpeg', quality: 85);

        // Upload to Linode Object Storage
        Storage::disk('linode')->put($filename, $encodedImage, 'public');

        // Get the public URL
        $this->imageUrl = Storage::disk('linode')->url($filename);

        // Update the parent component
        $this->dispatch('imageUploaded', [
            'imageUrl' => $this->imageUrl,
            'imageWidth' => $image->width(),
            'imageHeight' => $image->height(),
        ]);

        $this->uploading = false;
        $this->uploadProgress = 100;
        $this->photo = null;

        // Flash success message
        session()->flash('image-upload-success', 'Image uploaded successfully!');
    }

    public function updateAdjustments(): void
    {
        if (! $this->imageUrl) {
            return;
        }

        // Dispatch the updated adjustments to parent
        $this->dispatch('imageAdjustmentsUpdated', [
            'brightness' => $this->brightness,
            'contrast' => $this->contrast,
            'saturation' => $this->saturation,
            'blur' => $this->blur,
        ]);
    }

    public function resetImage(): void
    {
        $this->reset(['photo', 'imageUrl', 'brightness', 'contrast', 'saturation', 'blur']);
    }

    public function deleteImage(): void
    {
        if ($this->imageUrl) {
            // Extract the filename from the URL
            $filename = basename(parse_url($this->imageUrl, PHP_URL_PATH));
            $path = 'landing-pages/'.$filename;

            // Delete from storage
            if (Storage::disk('linode')->exists($path)) {
                Storage::disk('linode')->delete($path);
            }
        }

        $this->resetImage();
        $this->dispatch('imageDeleted');
    }

    public function render()
    {
        return view('livewire.landing-pages.image-uploader');
    }
}

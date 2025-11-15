<?php

namespace App\Livewire\LandingPages;

use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class VideoUploader extends Component
{
    use WithFileUploads;

    public ?TemporaryUploadedFile $video = null;

    public string $videoUrl = '';

    public string $videoTitle = '';

    public string $posterUrl = '';

    public string $propertyPrefix = 'blockData';

    public bool $uploading = false;

    public int $uploadProgress = 0;

    protected $listeners = ['resetVideo'];

    public function mount(
        string $videoUrl = '',
        string $videoTitle = '',
        string $posterUrl = '',
        string $propertyPrefix = 'blockData'
    ): void {
        $this->videoUrl = $videoUrl;
        $this->videoTitle = $videoTitle;
        $this->posterUrl = $posterUrl;
        $this->propertyPrefix = $propertyPrefix;
    }

    public function updatedVideo(): void
    {
        $this->validate([
            'video' => ['required', 'file', 'mimetypes:video/mp4,video/webm,video/quicktime', 'max:102400'], // 100MB max
        ]);

        $this->uploading = true;
        $this->uploadProgress = 0;

        try {
            // Process and upload the video
            $this->processAndUploadVideo();
        } catch (\Exception $e) {
            $this->uploading = false;
            $this->addError('video', 'Failed to upload video: '.$e->getMessage());
        }
    }

    protected function processAndUploadVideo(): void
    {
        // Get original extension
        $extension = $this->video->getClientOriginalExtension();

        // Map QuickTime to mp4
        if ($extension === 'mov') {
            $extension = 'mp4';
        }

        // Generate a unique filename
        $filename = 'landing-pages/videos/'.uniqid('video_', true).'.'.$extension;

        // Upload to Linode Object Storage
        Storage::disk('linode')->put($filename, file_get_contents($this->video->getRealPath()), 'public');

        // Get the public URL
        $this->videoUrl = Storage::disk('linode')->url($filename);

        // Update the parent component
        $this->dispatch('videoUploaded', [
            'videoUrl' => $this->videoUrl,
        ]);

        $this->uploading = false;
        $this->uploadProgress = 100;
        $this->video = null;

        // Flash success message
        session()->flash('video-upload-success', 'Video uploaded successfully!');
    }

    public function updatedVideoTitle(): void
    {
        // Dispatch event when video title is updated
        $this->dispatch('videoTitleUpdated', [
            'videoTitle' => $this->videoTitle,
        ]);
    }

    public function resetVideo(): void
    {
        $this->reset(['video', 'videoUrl', 'videoTitle', 'posterUrl']);
    }

    public function deleteVideo(): void
    {
        if ($this->videoUrl) {
            // Extract the path from the URL
            $parsedUrl = parse_url($this->videoUrl);
            $path = ltrim($parsedUrl['path'] ?? '', '/');

            // Delete from storage
            if (Storage::disk('linode')->exists($path)) {
                Storage::disk('linode')->delete($path);
            }
        }

        $this->resetVideo();
        $this->dispatch('videoDeleted');
    }

    public function render()
    {
        return view('livewire.landing-pages.video-uploader');
    }
}

<?php

namespace App\Providers;

use App\LandingPages\BlockRegistry;
use App\LandingPages\Blocks\BlockInterface;
use Illuminate\Support\ServiceProvider;

class LandingPageServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Auto-discover and register all landing page blocks
        $this->discoverBlocks();
    }

    /**
     * Automatically discover and register all block classes
     */
    protected function discoverBlocks(): void
    {
        $blocksPath = app_path('LandingPages/Blocks');

        if (! is_dir($blocksPath)) {
            return;
        }

        $files = glob("{$blocksPath}/*.php");

        foreach ($files as $file) {
            $className = 'App\\LandingPages\\Blocks\\'.basename($file, '.php');

            // Skip BaseBlock and the interface
            if (in_array(basename($file), ['BaseBlock.php', 'BlockInterface.php'])) {
                continue;
            }

            // Only register if class exists and implements BlockInterface
            if (class_exists($className) && is_subclass_of($className, BlockInterface::class)) {
                BlockRegistry::register($className);
            }
        }
    }
}

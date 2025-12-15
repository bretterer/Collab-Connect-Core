<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Pennant\Feature;

class CollabConnectServiceProvider extends ServiceProvider
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
        Feature::discover();

        $this->registerStrMacros();
    }

    /**
     * Register custom Str macros.
     */
    protected function registerStrMacros(): void
    {
        Str::macro('indefArticle', function (string $default, string $word): string {
            $word = trim($word);
            $lowerWord = strtolower($word);

            // Words that start with a vowel sound but use "a"
            $useAExceptions = [
                'uni', // university, uniform, union (sounds like "yoo")
                'one', // one (sounds like "won")
                'once', // once (sounds like "wunce")
                'eu', // European, euphemism (sounds like "yoo")
            ];

            // Words that start with a consonant but use "an"
            $useAnExceptions = [
                'hour', // silent h
                'honest', // silent h
                'honor', // silent h
                'honour', // silent h
                'heir', // silent h
            ];

            // Check for "an" exceptions first (silent h words)
            foreach ($useAnExceptions as $exception) {
                if (str_starts_with($lowerWord, $exception)) {
                    return 'an '.$word;
                }
            }

            // Check for "a" exceptions (consonant sounds despite vowel start)
            foreach ($useAExceptions as $exception) {
                if (str_starts_with($lowerWord, $exception)) {
                    return 'a '.$word;
                }
            }

            // Default rule: vowels use "an", consonants use "a"
            $firstChar = substr($lowerWord, 0, 1);
            $vowels = ['a', 'e', 'i', 'o', 'u'];

            if (in_array($firstChar, $vowels)) {
                return 'an '.$word;
            }

            return 'a '.$word;
        });
    }
}

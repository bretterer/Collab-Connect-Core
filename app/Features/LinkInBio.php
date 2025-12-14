<?php

namespace App\Features;

class LinkInBio
{
    public string $title = 'Link in Bio';

    public string $key = 'link-in-bio';

    public string $description = 'Create your personalized link page to share with your audience.';

    /**
     * Resolve the feature's initial value.
     */
    public function resolve(mixed $scope): mixed
    {
        return false;
    }
}

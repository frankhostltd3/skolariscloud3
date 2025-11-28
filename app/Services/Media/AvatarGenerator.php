<?php

namespace App\Services\Media;

class AvatarGenerator
{
    /**
     * Generate a default avatar URL based on the name.
     * Using UI Avatars API for simplicity.
     *
     * @param string $firstName
     * @param string $lastName
     * @return string
     */
    public function generate(string $firstName, string $lastName): string
    {
        $name = urlencode($firstName . ' ' . $lastName);
        return "https://ui-avatars.com/api/?name={$name}&color=7F9CF5&background=EBF4FF";
    }
}

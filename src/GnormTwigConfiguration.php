<?php

namespace Gnorm;

/**
 * Class GnormTwigConfiguration
 * Provides configuration decoding functionality for Gnorm using JSON.
 */
class GnormTwigConfiguration
{
    /**
     * Decodes a JSON configuration string into an associative array.
     *
     * @param string $json_config The JSON configuration string to decode.
     * @return array<string, mixed>|null Returns an associative array if decoding is successful, or null if the input is not valid JSON.
     */
    static public function decode(string $json_config): ?array
    {
        // Decode the JSON string into an associative array
        $result = json_decode($json_config, true);

        // Check if the result is an array, return null if not
        return is_array($result) ? $result : null;
    }
}

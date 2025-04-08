<?php

namespace KhalsaJio\AltGenerator;

interface LLMClientInterface
{
    /**
     * Initiate the Guzzle/HTTP client
     * @return mixed
     */
    public function initiate(): void;

    /**
     * Get client display name
     */
    public static function getClientName(): string;

    /**
     * Set the model to use
     */
    public function setModel(): void;

    /**
     * Get the model being used
     *
     * @return string
     */
    public function getModel(): string;

    /**
     * Set the API key for the client
     *
     * @param string $api_key
     */
    public function setApiKey($api_key): void;

    /**
     * Get the API key for the client
     *
     * @return string
     */
    public function getApiKey(): string;

    /**
     * Validate client configuration
     */
    public function validate(): bool;

    /**
     * Generate alt text for an image
     *
     * @param string $base_64_image Base64 encoded image data
     * @param int|null $character_limit
     * @param string|null $custom_prompt
     * @return array Format: ['success' => bool, 'altText' => string, 'usage' => array, 'error' => string]
     */
    public function generateAltText($base_64_image, $character_limit = null, $custom_prompt = null);
}


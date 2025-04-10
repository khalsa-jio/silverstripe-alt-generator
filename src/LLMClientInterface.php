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
    public function setModel($model): void;

    /**
     * Get the model being used
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
     */
    public function getApiKey(): string;

    /**
     * Get the character limit for the client
     */
    public function getCharacterLimit(): int;

    /**
     * Set the character limit for the client
     */
    public function setCharacterLimit($character_limit): void;

    /**
     * Get the prompt for generating alt text
     */
    public function getPrompt(): string;

    /**
     * Set the prompt for generating alt text
     */
    public function setPrompt($prompt): void;

    /**
     * Validate client configuration
     */
    public function validate(): bool;

    /**
     * Generate alt text for an image
     *
     * @param string $base_64_image Base64 encoded image data
     * @return array Format: ['success' => bool, 'altText' => string, 'usage' => array, 'error' => string]
     */
    public function generateAltText($base_64_image);
}


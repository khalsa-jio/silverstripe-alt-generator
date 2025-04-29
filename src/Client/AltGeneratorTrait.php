<?php

namespace KhalsaJio\AltGenerator\Client;

trait AltGeneratorTrait
{
    protected int $characterLimit;

    protected string $prompt;

    /**
     * @var string Prompt template with placeholder
     */
    protected string $promptTemplate = "Generate concise, descriptive alt text for this image"
        . " in under %d characters. Focus on key visual elements and context.";

    /**
     * Initialize alt generator settings
     */
    public function initAltGenerator(): void
    {
        $this->prompt = $this->preparePrompt();
    }

    /**
     * Set the prompt for generating alt text
     */
    public function setPrompt($prompt): void
    {
        if (empty($prompt)) {
            throw new \InvalidArgumentException("Prompt cannot be empty");
        }

        $this->prompt = $prompt;
    }

    /**
     * Get the prompt for generating alt text
     */
    public function getPrompt(): string
    {
        return $this->prompt;
    }

    /**
     * Get the character limit for the default prompt
     * if not set, defaults to 125
     */
    public function getCharacterLimit(): int
    {
        return $this->characterLimit ?? 125;
    }

    /**
     * Set the character limit in the default prompt
     */
    public function setCharacterLimit(int $character_limit): void
    {
        if ($character_limit <= 0) {
            throw new \InvalidArgumentException("Character limit must be positive");
        }

        $this->characterLimit = $character_limit;
    }

    /**
     * Prepare the prompt for generating alt text
     *
     * @return string
     */
    protected function preparePrompt(): string
    {
        return sprintf(
            $this->promptTemplate,
            $this->getCharacterLimit()
        );
    }
}

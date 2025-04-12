<?php

namespace KhalsaJio\AltGenerator\Client;

use GuzzleHttp\Client;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use SilverStripe\Core\Extensible;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Injector\Injectable;
use SilverStripe\Core\Injector\Injector;
use KhalsaJio\AltGenerator\LLMClientInterface;

abstract class AbstractLLMClient implements LLMClientInterface
{
    use Injectable;
    use Configurable;
    use Extensible;

    /**
     * @var Client
     */
    protected $client = null;

    /**
     * @var string
     * @config
     */
    protected $apiKey;

    /**
     * @var string
     * @config
     */
    protected $model;

    /**
     * @var string
     */
    protected $apiUrl;

    /**
     * @var int
     * @config
     */
    protected $characterLimit;

    /**
     * @var string
     * @config
     */
    protected $prompt;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * AbstractLLMClient constructor
     *
     * @param string|null $model
     */
    public function __construct()
    {
        // Set default configurations
        $this->model = $this->getDefaultModel();
        $this->characterLimit = 125;
        $this->prompt = $this->preparePrompt();

        try {
            $this->logger = Injector::inst()->get(LoggerInterface::class);
        } catch (\Exception $e) {
            $this->logger = null;
        }
    }

    /**
     * Get the default model to use
     */
    abstract protected function getDefaultModel(): string;

    /**
     * Initialize the client
     */
    public function initiate(): void
    {
        $this->client = new Client([
            'base_uri' => $this->apiUrl,
            'headers' => $this->getRequestHeaders(),
        ]);
    }

    /**
     * Get headers for API requests
     */
    protected function getRequestHeaders(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->getApiKey(),
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Get the API key
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * Set the API key
     *
     * @param string $apiKey
     */
    public function setApiKey($apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Set the model to use
     */
    public function setModel($model): void
    {
        $this->model = $model;
    }

    /**
     * Get the current model
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * Get the character limit for the default prompt
     */
    public function getCharacterLimit(): int
    {
        return $this->characterLimit;
    }

    /**
     * Set the character limit in the default prompt
     */
    public function setCharacterLimit($character_limit): void
    {
        if ($character_limit && is_int($character_limit) && $character_limit > 0) {
            $this->characterLimit = $character_limit;
        }
    }

    /**
     * Set the prompt for generating alt text
     */
    public function setPrompt($prompt): void
    {
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
     * Validate configuration
     */
    public function validate(): bool
    {
        if (empty($this->getApiKey())) {
            throw new InvalidArgumentException(static::getClientName() . " API key is required");
        }

        return true;
    }

    /**
     * Format the response into a standardized structure
     *
     * @param mixed $result API response data
     * @return array
     */
    protected function formatResponse($result): array
    {
        return [
            'success' => true,
            'altText' => $this->extractAltText($result),
            'usage' => $this->extractUsageData($result),
            'error' => null,
            'provider' => static::getClientName(),
            'model' => $this->getModel(),
        ];
    }

    /**
     * Format error response
     *
     * @param \Exception $e
     * @return array
     */
    protected function formatErrorResponse(\Exception $e): array
    {
        if ($this->logger) {
            $this->logger->error(static::getClientName() . ' API error: ' . $e->getMessage(), [
                'exception' => $e,
                'model' => $this->getModel()
            ]);
        }

        return [
            'success' => false,
            'altText' => '',
            'usage' => [],
            'error' => $e->getMessage(),
            'provider' => static::getClientName(),
            'model' => $this->getModel(),
        ];
    }

    /**
     * Extract alt text from response
     *
     * @param array $result
     * @return string
     */
    abstract protected function extractAltText($result): string;

    /**
     * Extract usage data from response
     *
     * @param array $result
     * @return array
     */
    abstract protected function extractUsageData($result): array;

    /**
     * Prepare the prompt for generating alt text
     *
     * @return string
     */
    protected function preparePrompt(): string
    {
        return "Generate concise, descriptive alt text for this image" .
        " in under {$this->getCharacterLimit()} characters. Focus on key" .
        "visual elements and context. The alt text should be clear and" .
        " informative, providing a brief description of the image content.";
    }
}

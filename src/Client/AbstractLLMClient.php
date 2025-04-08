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
     */
    protected $apiKey;

    /**
     * @var string
     */
    protected $model;

    /**
     * @var string
     */
    protected $apiUrl;

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
        $this->setModel();

        try {
            $this->logger = Injector::inst()->get(LoggerInterface::class);
        } catch (\Exception $e) {
            $this->logger = null;
        }
    }

    /**
     * Get the default model to use
     *
     * @return string
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
     *
     * @return array
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
     *
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->apiKey ?? self::config()->get('apiKey');
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
    public function setModel(): void
    {
        $this->model = self::config()->get('model') ?: $this->getDefaultModel();
    }

    /**
     * Get the current model
     *
     * @return string
     */
    public function getModel(): string
    {
        return $this->model ?: $this->getDefaultModel();
    }

    /**
     * Validate configuration
     *
     * @return bool
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
     * @param int|null $character_limit
     * @param string|null $custom_prompt
     * @return string
     */
    protected function preparePrompt($character_limit = null): string
    {
        $base_prompt = "Generate concise, descriptive alt text for this image";

        // Add character limit instruction if specified
        if ($character_limit && is_int($character_limit)) {
            $base_prompt .= " in under {$character_limit} characters";
        }

        return "{$base_prompt}. Focus on key visual elements and context.";
    }
}

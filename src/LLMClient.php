<?php

namespace KhalsaJio\AltGenerator;

use Psr\Log\LoggerInterface;
use SilverStripe\Core\Extensible;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Injector\Injectable;

class LLMClient
{
    use Configurable;
    use Injectable;
    use Extensible;

    /**
     * @config
     * The default LLM client to use
     */
    private static $default_client = null;

    /**
     * @var LLMClientInterface
     */
    private $active_client = null;

    /**
     * @var LoggerInterface
     */
    private $logger = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        try {
            $this->logger = Injector::inst()->get(LoggerInterface::class);
        } catch (\Exception $e) {
            $this->logger = null;
        }

        // Initialize the LLM client
        $this->initiate();
    }

    /**
     * Initialize the LLM client
     *
     * @param string|null $client_class
     * @param string|null $model
     * @return LLMClientInterface|null
     */
    public function initiate($client_class = null, $model = null)
    {
        // Get client class from parameter, config, or default
        $client_class = $client_class ?: self::config()->get('default_client');

        if (!$client_class) {
            $this->logError('No LLM client configured');
            return null;
        }

        if (!class_exists($client_class)) {
            $this->logError("Client class '$client_class' does not exist");
            return null;
        }

        try {
            // Create client instance
            $client = Injector::inst()->create($client_class, $model);

            // Validate client implements the interface
            if (!($client instanceof LLMClientInterface)) {
                $this->logError("Client class '$client_class' must implement LLMClientInterface");
                return null;
            }

            // Validate client configuration
            if (!$client->validate()) {
                $this->logError("Client configuration is invalid for $client_class");
                return null;
            }

            // Initialize the client
            $client->initiate();
            $this->active_client = $client;

            return $client;
        } catch (\Exception $e) {
            $this->logError("Failed to initialize LLM client: " . $e->getMessage());
            
            throw $e;
        }
    }

    /**
     * Log an error message
     *
     * @param string $message
     */
    private function logError($message)
    {
        if ($this->logger) {
            $this->logger->error($message);
        }
    }

    /**
     * Get the active client
     *
     * @return LLMClientInterface|null
     */
    public function getLLMClient()
    {
        return $this->active_client;
    }

    /**
     * Set the LLM client to use
     *
     * @param string $client_class
     * @param string|null $model
     * @return void
     */
    public function set_llm_client($client_class, $model = null): void
    {
        if ($client_class && class_exists($client_class)) {
            // Update config
            self::config()->set('default_client', $client_class);

            // Create and initialize new client
            $this->initiate($client_class, $model);
        } else {
            throw new \InvalidArgumentException("Client $client_class is not a valid class");
        }
    }

    /**
     * Get client name
     *
     * @return string
     */
    public function getClientName(): string
    {
        $client = $this->getLLMClient();
        return $client ? $client::getClientName() : '';
    }

    /**
     * Validate configuration
     *
     * @return bool
     */
    public function validate(): bool
    {
        $client = $this->getLLMClient();
        return $client ? $client->validate() : false;
    }

    /**
     * Generate alt text for an image
     *
     * @param string $base_64_image Base64 encoded image data
     * @param int|null $character_limit Maximum character count for alt text
     * @param string|null $custom_prompt Optional custom prompt template
     * @return array
     */
    public function generateAltText($base_64_image, $character_limit = null, $custom_prompt = null): array
    {
        $client = $this->getLLMClient();

        if ($client) {
            return $client->generateAltText($base_64_image, $character_limit, $custom_prompt);
        } else {
            return [
                'success' => false,
                'altText' => '',
                'usage' => [],
                'error' => 'LLM client not configured or failed to initialize',
                'provider' => null,
                'model' => null,
            ];
        }
    }
}

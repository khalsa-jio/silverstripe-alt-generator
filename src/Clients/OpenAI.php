<?php

namespace KhalsaJio\AltGenerator\Clients;

use GuzzleHttp\Client;
use SilverStripe\Core\Environment;
use GuzzleHttp\RequestOptions;

class OpenAI
{
    /**
     * @var Client
     */
    private static $client = null;

    /**
     * API key for OpenAI
     * @var
     */
    private static $api_key = null;

    /**
     * API URL for OpenAI Image and Vision API
     * @var string
     */
    private static $api_url = 'https://api.openai.com/v1/responses';

    /**
     * current model
     * @var string
     */
    private static $model = null;

    /**
     * models supported by OpenAI image and vision API
     * @var array
     */
    private static $supported_models = [
        'gpt-4o-mini' => 'GPT-4o Mini',
        'GPT-4o' => 'GPT-4o',
        'gpt-4.5-preview-2025-02-27' => 'GPT-4.5 Preview',
        'o1-pro-2025-03-19' => 'O1 Pro',
        'o1-2024-12-17' => 'O1',
        'chatgpt-4o-latest' => 'ChatGPT 4o Latest',
        'gpt-4-turbo-2024-04-09' => 'GPT-4 Turbo',
    ];

    public function __construct($model = null)
    {
        if ($model === null) {
            $model = 'gpt-4o-mini';
        }

        $this->setModel($model);

        if (self::$client === null) {
            $this->initializeClient();
        }
    }

    /**
     * Initialize the OpenAI client
     */
    private function initializeClient()
    {
        self::$api_key = Environment::getEnv('OPENAI_API_KEY');

        self::$client = new Client([
            'base_uri' => self::$api_url,
            'headers' => [
                'Authorization' => 'Bearer ' . self::$api_key,
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    public function setModel($model)
    {
        if (array_key_exists($model, self::$supported_models)) {
            self::$model = $model;
        } else {
            throw new \InvalidArgumentException("Model $model is not supported.");
        }
    }

    public function getModel()
    {
        return self::$model;
    }

    /**
     * Generate alt text for an image with optional character limit
     * 
     * @param string $imageUrl URL/path of the image
     * @param int|null $characterLimit Maximum character count for alt text
     * @param string|null $customPrompt Optional custom prompt template
     * @return array
     */
    public function generateAltText($base64Image, $characterLimit = null, $customPrompt = null)
    {
        $basePrompt = "Generate concise, descriptive alt text for this image";
        
        // Add character limit instruction if specified
        if ($characterLimit && is_int($characterLimit)) {
            $basePrompt .= " in under {$characterLimit} characters";
        }

        // Use custom prompt if provided
        $finalPrompt = $customPrompt ?: "{$basePrompt}. Focus on key visual elements and context.";

        try {
            $data = [
                'model' => self::$model,
                'input' => [
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'input_text',
                                'text' => $finalPrompt
                            ],
                            [
                                'type' => 'input_image',
                                'image_url' => "data:image/jpeg;base64,{$base64Image}",
                                'detail' => 'low'
                            ]
                        ]
                    ]
                ],
            ];

            $response = self::$client->post(self::$api_url, [
                RequestOptions::JSON => $data
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            
            return [
                'success' => true,
                'altText' => trim($result['output'][0]['content'][0]['text'] ?? ''),
                'usage' => $result['usage'] ?? []
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
<?php

namespace KhalsaJio\AltGenerator\Client;

use GuzzleHttp\RequestOptions;

class OpenAI extends AbstractLLMClient
{
    /**
     * API URL for OpenAI Image and Vision API
     * @var string
     */
    protected $apiUrl = 'https://api.openai.com';

    /**
     * Get the default model to use
     *
     * @return string
     */
    protected function getDefaultModel(): string
    {
        return 'gpt-4o-mini-2024-07-18';
    }

    /**
     * Get client name
     *
     * @return string
     */
    public static function getClientName(): string
    {
        return 'OpenAI';
    }

    /**
     * Generate alt text for an image
     *
     * @param string $base_64_image Base64 encoded image data
     * @param int|null $character_limit Maximum character count for alt text
     * @param string|null $custom_prompt Optional custom prompt template
     * @return array
     */
    public function generateAltText($base_64_image, $character_limit = null, $custom_prompt = null)
    {
        try {
            $data = [
                'model' => $this->getModel(),
                'input' => [
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'input_text',
                                'text' => $custom_prompt ?? $this->preparePrompt($character_limit)
                            ],
                            [
                                'type' => 'input_image',
                                'image_url' => "data:image/jpeg;base64,{$base_64_image}",
                            ]
                        ]
                    ]
                ],
            ];

            $response = $this->client->post('/v1/responses', [
                RequestOptions::JSON => $data
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            return $this->formatResponse($result);

        } catch (\Exception $e) {
            return $this->formatErrorResponse($e);
        }
    }

    /**
     * Extract alt text from response
     *
     * @param array $result
     * @return string
     */
    protected function extractAltText($result): string
    {
        return trim($result['output'][0]['content'][0]['text'] ?? '');
    }

    /**
     * Extract usage data from response
     *
     * @param array $result
     * @return array
     */
    protected function extractUsageData($result): array
    {
        return $result['usage'] ?? [];
    }
}

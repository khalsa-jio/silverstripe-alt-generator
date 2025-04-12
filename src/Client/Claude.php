<?php

namespace KhalsaJio\AltGenerator\Client;

use GuzzleHttp\RequestOptions;

class Claude extends AbstractLLMClient
{
    /**
     * API URL for Anthropic Claude API
     * @var string
     */
    protected $apiUrl = 'https://api.anthropic.com/v1/messages';

    /**
     * Get the default model to use
     *
     * @return string
     */
    protected function getDefaultModel(): string
    {
        return 'claude-3-haiku-20240307';
    }

    /**
     * Get headers for API requests - Claude uses a different auth header
     *
     * @return array
     */
    protected function getRequestHeaders(): array
    {
        return [
            'x-api-key' => $this->getApiKey(),
            'anthropic-version' => '2023-06-01',
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Get client name
     *
     * @return string
     */
    public static function getClientName(): string
    {
        return 'Claude';
    }

    /**
     * Generate alt text for an image
     *
     * @param string $base_64_image Base64 encoded image data
     * @return array
     */
    public function generateAltText($base_64_image)
    {
        try {
            $data = [
                'model' => $this->getModel(),
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => $this->getPrompt(),
                            ],
                            [
                                'type' => 'image',
                                'source' => [
                                    'type' => 'base64',
                                    'media_type' => 'image/webp',
                                    'data' => $base_64_image
                                ]
                            ]
                        ]
                    ]
                ],
                'max_tokens' => 300
            ];

            $response = $this->client->post('', [
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
        return trim($result['content'][0]['text'] ?? '');
    }

    /**
     * Extract usage data from response
     *
     * @param array $result
     * @return array
     */
    protected function extractUsageData($result): array
    {
        return [
            'input_tokens' => $result['usage']['input_tokens'] ?? 0,
            'output_tokens' => $result['usage']['output_tokens'] ?? 0
        ];
    }
}

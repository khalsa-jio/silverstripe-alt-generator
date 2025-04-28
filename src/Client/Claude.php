<?php

namespace KhalsaJio\AltGenerator\Client;

use KhalsaJio\AI\Nexus\Provider\Claude as ClaudeProvider;
use KhalsaJio\AltGenerator\Client\AltGeneratorTrait;

class Claude extends ClaudeProvider
{
    use AltGeneratorTrait;

    public function __construct()
    {
        parent::__construct();

        $this->initAltGenerator();
    }

    /**
     * Generate alt text for an image
     *
     * @param string $base_64_image Base64 encoded image data
     * @return array
     */
    public function generateAltText($base_64_image)
    {
        $data = [
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

        return $this->chat($data, 'messages');
    }
}
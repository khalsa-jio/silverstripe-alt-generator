<?php

namespace KhalsaJio\AltGenerator\Client;

use KhalsaJio\AI\Nexus\Provider\OpenAI as OpenAIProvider;
use KhalsaJio\AltGenerator\Client\AltGeneratorTrait;

class OpenAI extends OpenAIProvider
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
                'input' => [
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'input_text',
                                'text' => $this->getPrompt(),
                            ],
                            [
                                'type' => 'input_image',
                                'image_url' => "data:image/jpeg;base64,{$base_64_image}",
                            ]
                        ]
                    ]
                ],
            ];

        return $this->chat($data, 'responses');
    }
}
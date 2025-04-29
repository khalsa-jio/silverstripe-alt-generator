<?php

namespace KhalsaJio\AltGenerator\Control;

use SilverStripe\Assets\Image;
use KhalsaJio\AI\Nexus\LLMClient;
use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;

class AltGeneratorController extends Controller
{
    private static $url_segment = 'alt-generator';

    private static $url_handlers = [
        'POST generate/$ID' => 'generateAltText',
    ];

    private static $allowed_actions = [
        'generateAltText'
    ];

    public function generateAltText(HTTPRequest $request)
    {
        if (!$request->isAjax()) {
            return $this->httpError(400);
        }

        $image = Image::get()->byID($request->param('ID'));
        if (!$image || !$image->exists()) {
            return $this->jsonResponse(['error' => 'Image not found'], 404);
        }

        // Check if the image is a valid type
        $validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        if (!in_array($image->getMimeType(), $validTypes)) {
            return $this->jsonResponse(['error' => 'Invalid image type'], 400);
        }

        $maxSize = 1 * 1024 * 1024;
        $imageSize = $image->getAbsoluteSize();

        // Check if the image size exceeds the limit and resize if necessary
        if ($imageSize > $maxSize) {
            $imageURL = $image
                ->ResizedImage(800, 800)
                ->Convert('webp')
                ->getAbsoluteURL();
        } else {
            $imageURL = $image
                ->Convert('webp')
                ->getAbsoluteURL();
        }

        // Read the image data
        $imageData = file_get_contents($imageURL);
        if ($imageData === false) {
            return $this->jsonResponse(['error' => 'Failed to read image data'], 500);
        }

        $base64Image = base64_encode($imageData);
        if ($base64Image === false) {
            return $this->jsonResponse(['error' => 'Failed to encode image data'], 500);
        }

        try {
            $client = LLMClient::singleton();

            $response = $client->generateAltText($base64Image);

            return $this->jsonResponse($response);
        } catch (\Exception $e) {
            return $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    private function jsonResponse($data, $code = 200)
    {
        $this->response->setStatusCode($code);
        $this->response->addHeader('Content-Type', 'application/json');
        return json_encode($data);
    }
}

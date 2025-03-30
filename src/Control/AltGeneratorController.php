<?php
namespace KhalsaJio\AltGenerator\Control;

use SilverStripe\Assets\Image;
use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Core\Injector\Injector;
use KhalsaJio\AltGenerator\Clients\OpenAI;

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
        if (!$request->isAjax()) return $this->httpError(400);
        
        $image = Image::get()->byID($request->param('ID'));
        if (!$image || !$image->exists()) {
            return $this->jsonResponse(['error' => 'Image not found'], 404);
        }

        //convert image to base64
        $imageData = file_get_contents($image->getAbsoluteURL());
        if ($imageData === false) {
            return $this->jsonResponse(['error' => 'Failed to read image data'], 500);
        }

        $base64Image = base64_encode($imageData);
        if ($base64Image === false) {
            return $this->jsonResponse(['error' => 'Failed to encode image data'], 500);
        }

        try {
            $client = Injector::inst()->create(OpenAI::class);

            $response = $client->generateAltText($base64Image, 100);

            return $this->jsonResponse([
                'altText' => $response['altText'],
            ]);
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
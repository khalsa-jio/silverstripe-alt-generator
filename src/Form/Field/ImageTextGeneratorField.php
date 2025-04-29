<?php

namespace KhalsaJio\AltGenerator\Form\Field;

use SilverStripe\Forms\Tip;
use SilverStripe\Assets\Image;
use KhalsaJio\AI\Nexus\LLMClient;
use SilverStripe\Forms\TextField;

class ImageTextGeneratorField extends TextField
{
    protected $schemaComponent = 'ImageTextGeneratorField';

    public function __construct($name, $title = null, $value = null)
    {
        parent::__construct($name, $title, $value);

        $this->addExtraClass('image-alt-generator');

        if (LLMClient::getDefaultClient()) {
            $this->setTitleTip(new Tip(_t(
                __CLASS__ . '.TITLE_DESCRIPTION',
                'Click below icon to automatically generate descriptive alt text for this image using AI.'
            )));
        }
    }

    public function getSchemaDataDefaults()
    {
        $data = parent::getSchemaDataDefaults();

        $image = Image::get()->byID($this->getImageID());

        if ($image && LLMClient::getDefaultClient()) {
            $data['imageID'] = $image->ID;
            $data['icon'] = 'edit';
        }

        return $data;
    }

    private function getImageID()
    {
        return $this->getForm()->getController()->getRequest()->param('ItemID');
    }
}

<?php
namespace KhalsaJio\AltGenerator\Forms\Fields;

use SilverStripe\Forms\TextField;
use SilverStripe\Assets\Image;

class ImageTextGeneratorField extends TextField
{
    protected $schemaComponent = 'ImageTextGeneratorField';

    public function __construct($name, $title = null, $value = null)
    {
        parent::__construct($name, $title, $value);
        $this->addExtraClass('image-alt-generator');
    }

    public function getSchemaDataDefaults()
    {
        $data = parent::getSchemaDataDefaults();

        if ($image = Image::get()->byID($this->getImageID())) {
            $data['imageID'] = $image->ID;
        }

        return $data;
    }

    private function getImageID()
    {
        return $this->getForm()->getController()->getRequest()->param('ItemID');
    }
}
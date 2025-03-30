<?php

namespace KhalsaJio\AltGenerator\Extensions;

use KhalsaJio\AltGenerator\Forms\Fields\ImageTextGeneratorField;
use SilverStripe\Assets\File;
use SilverStripe\Assets\Image;
use SilverStripe\Core\Extension;
use SilverStripe\Forms\FieldList;
use SilverStripe\AssetAdmin\Forms\AssetFormFactory;

class ImageAltGeneratorFormFactoryExtension extends Extension
{
    public function updateFormFields(FieldList $fields, $controller, $formName, $context)
    {
        // File/Image DataObject
        /** @var File|Image $image */
        $image = !empty($context['Record']) ? $context['Record'] : null;

        // The type of context we are in, while inserting media from TinyMCE or while we are in assets admin interface
        $type = !empty($context['Type']) ? $context['Type'] : AssetFormFactory::TYPE_ADMIN;

        $altTextField = ImageTextGeneratorField::create(
            'AltText',
            _t(__CLASS__ . '.ALT_TEXT', 'Alt Text'),
        );

        // If the image is not null and the type is not insert media, we can add the alt text field
        if (AssetFormFactory::TYPE_INSERT_MEDIA !== $type && $image && 'image' === $image->appCategory()) {
            if ($type === AssetFormFactory::TYPE_SELECT) {
                $fields->insertAfter('Title', $altTextField->performReadonlyTransformation());
            } else {

                $fields->insertAfter('Title', $altTextField);
            }
        }
    }
}

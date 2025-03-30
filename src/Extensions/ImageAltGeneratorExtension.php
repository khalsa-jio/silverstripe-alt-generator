<?php

namespace KhalsaJio\AltGenerator\Extensions;

use SilverStripe\Core\Extension;

class ImageAltGeneratorExtension extends Extension
{
    private static array $db = [
        'AltText' => 'Varchar(100)',
    ];
}
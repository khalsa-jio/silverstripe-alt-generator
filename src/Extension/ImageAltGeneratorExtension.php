<?php

namespace KhalsaJio\AltGenerator\Extension;

use SilverStripe\Core\Extension;

class ImageAltGeneratorExtension extends Extension
{
    private static array $db = [
        'AltText' => 'Varchar(100)',
    ];
}

<?php

declare(strict_types=1);

namespace TRAW\VhsCol\ViewHelpers\Image;

use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class GetCropInfoViewHelper
 */
class GetCropInfoViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        //File Reference object
        $this->registerArgument('image', FileReference::class, 'crop area json', true);
        //if you have multiple crop areas, tell the Viewhelper which one you want
        $this->registerArgument('crop', 'string', 'Crop area', false, 'default');
    }

    /**
     * Returns the crop information as array
     *   [
     *      cropArea => [
     *          height
     *          width
     *          x
     *          y
     *      ]
     *      selectedRatio
     *      focusArea => NULL
     *  ]
     */
    
    public function render(): ?array
    {
        $image = $this->arguments['image'];
        if ($image instanceof FileReference) {
            $crop = $image->getProperty('crop');

            return empty($crop) ? (null) : json_decode((string)$crop, true)[$this->arguments['crop']] ?? null;
        }
        return null;
    }
}

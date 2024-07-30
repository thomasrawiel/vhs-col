<?php

namespace TRAW\VhsCol\ViewHelpers\Image;

use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class GetCropInfoViewHelper
 * @package TRAW\VhsCol\ViewHelpers\Image
 */
class GetCropInfoViewHelper extends AbstractViewHelper
{
    /**
     * @return void
     */
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
     *
     * @return array|null
     */
    public function render(): ?array
    {
        $image = $this->arguments['image'];
        if ($image instanceof FileReference) {
            $crop = $image->getProperty('crop');

            return !empty($crop) ? (json_decode($crop, true)[$this->arguments['crop']] ?? null) : null;
        } else {
            return null;
        }
    }
}
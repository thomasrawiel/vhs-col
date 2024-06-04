<?php

namespace TRAW\Vhscol\ViewHelpers\Media;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Utility\FrontendSimulationUtility;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3Fluid\Fluid\Core\Exception;

/**
 * Used in conjuntion with the `v:media.PictureViewHelper`.
 * Please take a look at the `v:media.PictureViewHelper` documentation for more
 * information.
 */
class SourceViewHelper extends \FluidTYPO3\Vhs\ViewHelpers\Media\SourceViewHelper
{
    public const SCOPE_VARIABLE_DEFAULT_SOURCE_WIDTH = 'default-source-width';
    public const SCOPE_VARIABLE_DEFAULT_SOURCE_HEIGHT = 'default-source-height';

    /**
     * Initialize arguments.
     *
     * @api
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('cropVariant', 'string', 'CropVariant to apply', false, 'default');
        $this->registerArgument('pixelDensities', 'string', 'Pixel densities to apply', false, '1');
    }

    /**
     * Render method
     *
     * @return string
     */
    public function render()
    {
        $imageSource = $this->renderingContext->getViewHelperVariableContainer()->get(static::SCOPE, static::SCOPE_VARIABLE_SRC);
        $treatIdAsRerefence = $this->renderingContext->getViewHelperVariableContainer()->get(static::SCOPE, static::SCOPE_VARIABLE_ID);
        $isBackend = ($GLOBALS['TYPO3_REQUEST'] ?? null) instanceof ServerRequestInterface && ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isBackend();

        if ($isBackend) {
            $tsfeBackup = FrontendSimulationUtility::simulateFrontendEnvironment();
        }

        //news
        if ($this->renderingContext->getControllerName() === 'News') {
            $imageSource = $this->renderingContext->getVariableProvider()->get('file');
        }

        if (is_null($imageSource) || empty($imageSource)) {
            $imageSource = $this->renderingContext->getVariableProvider()->get('image');
        }

        $cropVariantCollection = CropVariantCollection::create($imageSource->getProperty('crop') ?? '');
        $cropVariant = $this->arguments['cropVariant'] ?? 'default';
        $cropArea = $cropVariantCollection->getCropArea($cropVariant);
        $setup = [
            'width' => $this->arguments['width'],
            'height' => $this->arguments['height'],
            'minW' => $this->arguments['minW'],
            'minH' => $this->arguments['minH'],
            'maxW' => $this->arguments['maxW'],
            'maxH' => $this->arguments['maxH'],
            'treatIdAsReference' => $treatIdAsRerefence,
            'crop' => $cropArea->isEmpty() ? null : $cropArea->makeAbsoluteBasedOnFile($imageSource),
        ];
        $quality = $this->arguments['quality'];
        $format = $this->arguments['format'];

        if (empty($format) === false) {
            $setup['ext'] = $format;
        }
        if ((int)$quality > 0) {
            $quality = MathUtility::forceIntegerInRange($quality, 10, 100, 75);
            $setup['params'] = ' -quality ' . $quality;
        }

        $imageService = GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Service\ImageService::class);
        $originalFile = $imageService->getImage(
            is_string($imageSource) || is_int($imageSource) ? $imageSource : '',
            is_object($imageSource) ? $imageSource : null,
            (bool)($this->arguments['treatIdAsReference'] ?? true)
        );

        $result = null;

        // Iterate through pixel densities
        $pixelDensities = GeneralUtility::trimExplode(',', $this->arguments['pixelDensities'] ?: '1', true);
        foreach ($pixelDensities as $pixelDensity) {
            // Calculate image width
            $setup['width'] = $this->arguments['width'] * $pixelDensity;
            if ($imageSource instanceof FileReference) {
                // Get original image width including cropped area
                $imageCrop = json_decode($imageSource->getProperty('crop'), true);
                $croppedWidth = $imageCrop[$cropVariant]['cropArea']['width'] ?? 1;
            } else {
                $croppedWidth = 1;
            }
            $originalWidth = $originalFile->getProperty('width') * $croppedWidth;

            // If original image has a smaller width than our target image, we don't want to upscale.
            // But we need at least one processed image :)
            if ($setup['width'] > $originalWidth) {
                if (empty($srcsets)) {
                    $setup['width'] = $originalWidth;
                    $result = $imageService->applyProcessingInstructions($imageSource, $setup);
                    $srcsets[] = $imageService->getImageUri($result);
                }
            } else {
                // Process image width new width and add pixel density notation
                $result = $imageService->applyProcessingInstructions($imageSource, $setup);
                $srcsets[] = $imageService->getImageUri($result) . (!empty($this->arguments['media']) ? ' ' . $pixelDensity . 'x' : '');
            }
        }

        if ($isBackend) {
            FrontendSimulationUtility::resetFrontendEnvironment($tsfeBackup);
        }

        if (empty($srcsets) || $result === null) {
            throw new Exception('No valid source generated for picture.');
        }
        $src = implode(', ', $srcsets);

        if ($this->arguments['media'] === null) {
            $this->renderingContext->getViewHelperVariableContainer()->addOrUpdate(static::SCOPE, static::SCOPE_VARIABLE_DEFAULT_SOURCE, $src);
            $this->renderingContext->getViewHelperVariableContainer()->addOrUpdate(static::SCOPE, static::SCOPE_VARIABLE_DEFAULT_SOURCE_WIDTH, $result->getProperty('width'));
            $this->renderingContext->getViewHelperVariableContainer()->addOrUpdate(static::SCOPE, static::SCOPE_VARIABLE_DEFAULT_SOURCE_HEIGHT, $result->getProperty('height'));
        } else {
            $this->tag->addAttribute('media', $this->arguments['media']);
        }

        $this->tag->addAttribute('srcset', $src);
        return $this->tag->render();
    }
}

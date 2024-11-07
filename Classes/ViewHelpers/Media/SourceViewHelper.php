<?php
declare(strict_types=1);

namespace TRAW\VhsCol\ViewHelpers\Media;

/*
 * This file is adapted from the FluidTYPO3/Vhs project SourceViewHelper
 */

use TRAW\VhsCol\Utility\FrontendSimulationUtility;
use Psr\Http\Message\ServerRequestInterface;
use TRAW\VhsCol\Information\RequestType;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3Fluid\Fluid\Core\Exception;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * Used in conjuntion with the `v:media.PictureViewHelper`.
 * Please take a look at the `v:media.PictureViewHelper` documentation for more
 * information.
 */
class SourceViewHelper extends AbstractTagBasedViewHelper
{
    public const SCOPE_VARIABLE_DEFAULT_SOURCE_WIDTH = 'default-source-width';
    public const SCOPE_VARIABLE_DEFAULT_SOURCE_HEIGHT = 'default-source-height';
    const SCOPE = 'TRAW\VhsCol\ViewHelpers\Media\PictureViewHelper';
    const SCOPE_VARIABLE_SRC = 'src';
    const SCOPE_VARIABLE_ID = 'treatIdAsReference';
    const SCOPE_VARIABLE_DEFAULT_SOURCE = 'default-source';

    /**
     * name of the tag to be created by this view helper
     *
     * @var string
     * @api
     */
    protected $tagName = 'source';

    /**
     * @var ContentObjectRenderer
     */
    protected $contentObject;

    /**
     * @var ConfigurationManagerInterface
     */
    protected $configurationManager;

    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager): void
    {
        $this->configurationManager = $configurationManager;
        /** @var ContentObjectRenderer $contentObject */
        $contentObject = $this->configurationManager->getContentObject();
        $this->contentObject = $contentObject;
    }

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerUniversalTagAttributes();
        $this->registerArgument('media', 'string', 'Media query for which breakpoint this sources applies');
        $this->registerArgument(
            'width',
            'string',
            'Width of the image. This can be a numeric value representing the fixed width of the image in pixels. ' .
            'But you can also perform simple calculations by adding "m" or "c" to the value. See imgResource.width ' .
            'for possible options.'
        );
        $this->registerArgument(
            'height',
            'string',
            'Height of the image. This can be a numeric value representing the fixed height of the image in pixels. ' .
            'But you can also perform simple calculations by adding "m" or "c" to the value. See imgResource.width ' .
            'for possible options.'
        );
        $this->registerArgument('maxW', 'integer', 'Maximum Width of the image. (no upscaling)');
        $this->registerArgument('maxH', 'integer', 'Maximum Height of the image. (no upscaling)');
        $this->registerArgument('minW', 'integer', 'Minimum Width of the image.');
        $this->registerArgument('minH', 'integer', 'Minimum Height of the image.');
        $this->registerArgument(
            'format',
            'string',
            'Format of the processed file - also determines the target file format. If blank, TYPO3/IM/GM default ' .
            'is taken into account.'
        );
        $this->registerArgument(
            'quality',
            'integer',
            'Quality of the processed image. If blank/not present falls back to the default quality defined ' .
            'in install tool.',
            false,
            $GLOBALS['TYPO3_CONF_VARS']['GFX']['jpg_quality'] ?? 90
        );
        $this->registerArgument('relative', 'boolean', 'Produce a relative URL instead of absolute', false, false);
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
        $viewHelperVariableContainer = $this->renderingContext->getViewHelperVariableContainer();
        /** @var FileReference|string $imageSource */
        $imageSource = $viewHelperVariableContainer->get(static::SCOPE, static::SCOPE_VARIABLE_SRC);
        $treatIdAsRerefence = $viewHelperVariableContainer->get(static::SCOPE, static::SCOPE_VARIABLE_ID);

        $isBackend = RequestType::isBackend();

        if ($isBackend) {
            $tsfeBackup = FrontendSimulationUtility::simulateFrontendEnvironment();
        }

        //news
        if ($this->renderingContext->getControllerName() === 'News') {
            $imageSource = $this->renderingContext->getVariableProvider()->get('file');
        }

        if (is_null($imageSource) || empty($imageSource)) {
            $imageSource = $viewHelperVariableContainer->get('image');
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
            'params' => '',
            'crop' => $cropArea->isEmpty() ? null : $cropArea->makeAbsoluteBasedOnFile($imageSource),
        ];
        /** @var int $quality */
        $quality = $this->arguments['quality'];
        /** @var string $format */
        $format = $this->arguments['format'];

        if (!empty($format)) {
            $setup['ext'] = $format;
        }
        if (0 < intval($quality)) {
            $quality = MathUtility::forceIntegerInRange($quality, 10, 100, 75);
            $setup['params'] .= ' -quality ' . $quality;
        }

        $imageService = GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Service\ImageService::class);
        $originalFile = $imageService->getImage(
            is_string($imageSource) || is_int($imageSource) ? $imageSource : '',
            is_object($imageSource) ? $imageSource : null,
            (bool)$treatIdAsRerefence
        );

        $result = null;
        $media = $this->arguments['media'];
        // Iterate through pixel densities
        $pixelDensities = GeneralUtility::trimExplode(',', $this->arguments['pixelDensities'] ? : '1', true);
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
                $srcsets[] = $imageService->getImageUri($result) . (!empty($media) ? ' ' . $pixelDensity . 'x' : '');
            }
        }

        if ($isBackend) {
            FrontendSimulationUtility::resetFrontendEnvironment($tsfeBackup);
        }

        if (empty($srcsets) || $result === null) {
            throw new Exception('No valid source generated for picture.');
        }

        $srcsets = array_map(function ($srcset) {
            return $this->preprocessSourceUri(rawurldecode($srcset));
        }, $srcsets);

        $src = implode(', ', $srcsets);

        /** @var string|null $media */
        $media = $this->arguments['media'];
        if ($media === null) {
            $viewHelperVariableContainer->addOrUpdate(static::SCOPE, static::SCOPE_VARIABLE_DEFAULT_SOURCE, $src);
            $viewHelperVariableContainer->addOrUpdate(static::SCOPE, static::SCOPE_VARIABLE_DEFAULT_SOURCE_WIDTH, $result->getProperty('width'));
            $viewHelperVariableContainer->addOrUpdate(static::SCOPE, static::SCOPE_VARIABLE_DEFAULT_SOURCE_HEIGHT, $result->getProperty('height'));
        } else {
            $this->tag->addAttribute('media', $media);
        }

        $this->tag->addAttribute('srcset', $src);
        return $this->tag->render();
    }

    public function preprocessSourceUri(string $src): string
    {
        if (!empty($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_vhscol.']['settings.']['prependPath'])) {
            $src = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_vhscol.']['settings.']['prependPath'] . $src;
        } elseif (RequestType::isBackend() || !$this->arguments['relative']) {
            if (GeneralUtility::isValidUrl($src)) {
                $src = ltrim($src, '/');
            } elseif (RequestType::isFrontend()) {
                $src = $GLOBALS['TSFE']->absRefPrefix . ltrim($src, '/');
            } else {
                /** @var string $siteUrl */
                $siteUrl = GeneralUtility::getIndpEnv('TYPO3_SITE_URL');
                $src = $siteUrl . ltrim($src, '/');
            }
        }
        return $src;
    }
}

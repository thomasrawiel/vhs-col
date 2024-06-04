<?php

namespace TRAW\Vhscol\ViewHelpers\Image;

use TRAW\Vhscol\Utility\ConfigurationUtility;
use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Core\Resource\Exception\ResourceDoesNotExistException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Service\ImageService;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;

class LazySizesImageViewHelper extends AbstractTagBasedViewHelper
{
    protected $tagName = 'img';

    protected ImageService $imageService;

    public function __construct()
    {
        parent::__construct();
        $this->imageService = GeneralUtility::makeInstance(ImageService::class);
    }

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerUniversalTagAttributes();
        $this->registerTagAttribute('alt', 'string', 'Specifies an alternate text for an image', false);
        $this->registerTagAttribute('ismap', 'string', 'Specifies an image as a server-side image-map. Rarely used. Look at usemap instead', false);
        $this->registerTagAttribute('longdesc', 'string', 'Specifies the URL to a document that contains a long description of an image', false);
        $this->registerTagAttribute('usemap', 'string', 'Specifies an image as a client-side image-map', false);
        $this->registerTagAttribute('loading', 'string', 'Native lazy-loading for images property. Can be "lazy", "eager" or "auto"', false);
        $this->registerTagAttribute('decoding', 'string', 'Provides an image decoding hint to the browser. Can be "sync", "async" or "auto"', false);

        $this->registerArgument('src', 'string', 'a path to a file, a combined FAL identifier or an uid (int). If $treatIdAsReference is set, the integer is considered the uid of the sys_file_reference record. If you already got a FAL object, consider using the $image parameter instead', false, '');
        $this->registerArgument('treatIdAsReference', 'bool', 'given src argument is a sys_file_reference record', false, false);
        $this->registerArgument('image', 'object', 'a FAL object (\\TYPO3\\CMS\\Core\\Resource\\File or \\TYPO3\\CMS\\Core\\Resource\\FileReference)');
        $this->registerArgument('crop', 'string|bool|array', 'overrule cropping of image (setting to FALSE disables the cropping set in FileReference)');
        $this->registerArgument('cropVariant', 'string', 'select a cropping variant, in case multiple croppings have been specified or stored in FileReference', false, 'default');
        $this->registerArgument('fileExtension', 'string', 'Custom file extension to use');

        $this->registerArgument('width', 'string', 'width of the image. This can be a numeric value representing the fixed width of the image in pixels. But you can also perform simple calculations by adding "m" or "c" to the value. See imgResource.width for possible options.');
        $this->registerArgument('height', 'string', 'height of the image. This can be a numeric value representing the fixed height of the image in pixels. But you can also perform simple calculations by adding "m" or "c" to the value. See imgResource.width for possible options.');
        $this->registerArgument('minWidth', 'int', 'minimum width of the image');
        $this->registerArgument('minHeight', 'int', 'minimum height of the image');
        $this->registerArgument('maxWidth', 'int', 'maximum width of the image');
        $this->registerArgument('maxHeight', 'int', 'maximum height of the image');
        $this->registerArgument('absolute', 'bool', 'Force absolute URL', false, false);

        $this->registerArgument('sizes', 'string', 'Comma separated list of sizes widths to generate responsive images', false, null);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function render()
    {
        $src = (string)$this->arguments['src'];
        if (($src === '' && $this->arguments['image'] === null) || ($src !== '' && $this->arguments['image'] !== null)) {
            throw new Exception('You must either specify a string src or a File object.', 1382284106);
        }

        try {
            $class = 'lazyload' . (!empty($this->arguments['class']) ? ' ' . $this->arguments['class'] : '');
            $this->tag->addAttribute('class', $class);

            $tsConf = ConfigurationUtility::getSettings();
            $viewHelperSettings = $tsConf['viewHelpers']['lazySizesImage'];
            $sizes = GeneralUtility::trimExplode(',', $this->arguments['sizes'] ? : $viewHelperSettings['defaultSizes'], true);

            $image = $this->imageService->getImage($src, $this->arguments['image'], (bool)$this->arguments['treatIdAsReference']);
            $cropString = $this->arguments['crop'];
            if ($cropString === null && $image->hasProperty('crop') && $image->getProperty('crop')) {
                $cropString = $image->getProperty('crop');
            }
            $cropVariantCollection = CropVariantCollection::create((string)$cropString);
            $cropVariant = $this->arguments['cropVariant'] ? : 'default';
            $cropArea = $cropVariantCollection->getCropArea($cropVariant);

            $processingInstructions = [
                'width' => $this->arguments['width'],
                'height' => $this->arguments['height'],
                'minWidth' => $this->arguments['minWidth'],
                'minHeight' => $this->arguments['minHeight'],
                'maxWidth' => $this->arguments['maxWidth'],
                'maxHeight' => $this->arguments['maxHeight'],
                'crop' => $cropArea->isEmpty() ? null : $cropArea->makeAbsoluteBasedOnFile($image),
            ];
            $processedOriginalImage = $this->imageService->applyProcessingInstructions($image, $processingInstructions);

            $imageUri = $this->imageService->getImageUri($processedOriginalImage, $this->arguments['absolute']);
            $this->tag->addAttribute('data-src', $imageUri);

            // there's no need to add lazysizes for a tiny image
//            if ($processedOriginalImage->getProperty('width') < $sizes[0]) {
//                return parent::render();
//            }

            $this->tag->addAttribute('data-parent-fit', 'contain');
            $this->tag->addAttribute('data-sizes', 'auto');

            // fix for lazysizes bug when parent container has no renderable width, see https://github.com/aFarkas/lazysizes/issues/539#issuecomment-425737130
            $this->tag->addAttribute('data-width', $processedOriginalImage->getProperty('width'));
            $this->tag->addAttribute('data-height', $processedOriginalImage->getProperty('height'));

            $addOriginalImage = false;
            foreach ($processingInstructions as $processingInstruction) {
                if (!empty($processingInstruction)) $addOriginalImage = true;
            }
            // add original image size (e.g. if width is explicitly given, this width should be available)
            if ($addOriginalImage)
                $srcset[$processedOriginalImage->getProperty('width')] = $this->imageService->getImageUri($processedOriginalImage, $this->arguments['absolute']) . ' ' . $processedOriginalImage->getProperty('width') . 'w';

            foreach ($sizes as $width) {
                // only add widths smaller than original image
                if ($processedOriginalImage->getProperty('width') < $width) continue;
                $height = ($this->arguments['height'] && $this->arguments['width'] ? (($this->arguments['height'] / $this->arguments['width']) * $width) : null);

                $height = ($processedOriginalImage->getProperty('height') / $processedOriginalImage->getProperty('width')) * $width;
                $processingInstructions = [
                    'width' => $width,
                    'height' => $height,
                    'crop' => $cropArea->isEmpty() ? null : $cropArea->makeAbsoluteBasedOnFile($image),
                ];
                $processedImage = $this->imageService->applyProcessingInstructions($image, $processingInstructions);
                $srcset[$width] = $this->imageService->getImageUri($processedImage, $this->arguments['absolute']) . ' ' . $processedImage->getProperty('width') . 'w';
            }

            ksort($srcset);

            if (!$this->tag->hasAttribute('data-focus-area')) {
                $focusArea = $cropVariantCollection->getFocusArea($cropVariant);
                if (!$focusArea->isEmpty()) {
                    $this->tag->addAttribute('data-focus-area', $focusArea->makeAbsoluteBasedOnFile($image));
                }
            }

            $this->tag->addAttribute('data-srcset', implode(',', $srcset));

            // The alt-attribute is mandatory to have valid html-code, therefore add it even if it is empty
            if (empty($this->arguments['alt'])) {
                $this->tag->addAttribute('alt', $image->hasProperty('alternative') ? $image->getProperty('alternative') : '');
            }
            // Add title-attribute from property if not already set and the property is not an empty string
            $title = (string)($image->hasProperty('title') ? $image->getProperty('title') : '');
            if (empty($this->arguments['title']) && $title !== '') {
                $this->tag->addAttribute('title', $title);
            }
        } catch (ResourceDoesNotExistException $e) {
            // thrown if file does not exist
            throw new Exception($e->getMessage(), 1509741911, $e);
        } catch (\UnexpectedValueException $e) {
            // thrown if a file has been replaced with a folder
            throw new Exception($e->getMessage(), 1509741912, $e);
        } catch (\RuntimeException $e) {
            // RuntimeException thrown if a file is outside of a storage
            throw new Exception($e->getMessage(), 1509741913, $e);
        } catch (\InvalidArgumentException $e) {
            // thrown if file storage does not exist
            throw new Exception($e->getMessage(), 1509741914, $e);
        }
        return $this->tag->render();
    }
}

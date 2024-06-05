<?php

namespace TRAW\VhsCol\Utility;

use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class SvgUtility implements SingletonInterface
{
    /**
     * @var array
     */
    protected array $settings = [];

    /**
     * @var array
     */
    protected static array $svgCache = [];

    /**
     * @param string             $name
     * @param string             $path
     * @param FileReference|null $file
     * @param bool               $useThemePath
     *
     * @return string
     */
    public function render(string $name = '', string $path = '', ?FileReference $file = null, bool $useThemePath = false)
    {
        if (empty($path)) {
            if (empty($this->settings)) {
                $this->settings = ConfigurationUtility::getSettings();
            }
            if ($useThemePath && isset($this->settings['viewHelpers']['image']['svg']['themePath']) && !empty($this->settings['viewHelpers']['image']['svg']['themePath'])) {
                $path = $this->settings['viewHelpers']['image']['svg']['themePath'];
            } elseif (isset($this->settings['viewHelpers']['image']['svg']['defaultPath']) && !empty($this->settings['viewHelpers']['image']['svg']['defaultPath'])) {
                $path = $this->settings['viewHelpers']['image']['svg']['defaultPath'];
            } else {
                return '[SVG ViewHelper] Our monkey horde do not know where to search for files. Please provide a valid path.';
            }
        }

        $svgContent = '';

        if ($file instanceof FileReference) {
            $svgPath = $file->getOriginalFile()->getIdentifier();
            if (!empty(static::$svgCache[$svgPath])) {
                $svgContent = static::$svgCache[$svgPath];
            } else {
                $svgContent = $file->getContents();
                static::$svgCache[$svgPath] = $svgContent;
            }
        }

        if (!empty($name)) {
            $svgPath = rtrim($path, '/') . '/' . $name . '.svg';
            if (!empty(static::$svgCache[$svgPath])) {
                $svgContent = static::$svgCache[$svgPath];
            } else {
                $svgRealPath = GeneralUtility::getFileAbsFileName($svgPath);
                if (file_exists($svgRealPath)) {
                    $svgContent = GeneralUtility::getURL($svgRealPath);
                    static::$svgCache[$svgPath] = $svgContent;
                }
            }
        }

        return $svgContent;
    }
}

<?php

declare(strict_types=1);

namespace TRAW\VhsCol\Utility;

use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class SvgUtility implements SingletonInterface
{
    protected array $settings = [];

    protected static array $svgCache = [];

    /**
     * @return string
     */
    public function render(string $name = '', string $path = '', ?FileReference $file = null, bool $useThemePath = false)
    {
        if ($path === '' || $path === '0') {
            if ($this->settings === []) {
                $this->settings = ConfigurationUtility::getSettings();
            }

            if ($useThemePath && !empty($this->settings['viewHelpers']['image']['svg']['themePath'])) {
                $path = $this->settings['viewHelpers']['image']['svg']['themePath'];
            } elseif (!empty($this->settings['viewHelpers']['image']['svg']['defaultPath'])) {
                $path = $this->settings['viewHelpers']['image']['svg']['defaultPath'];
            } else {
                return '[SVG ViewHelper] Our monkey horde does not know where to search for files. Please provide a valid path.';
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

        if ($name !== '' && $name !== '0') {
            $svgPath = rtrim((string)$path, '/') . '/' . $name . '.svg';
            if (!empty(static::$svgCache[$svgPath])) {
                $svgContent = static::$svgCache[$svgPath];
            } else {
                $svgRealPath = GeneralUtility::getFileAbsFileName($svgPath);
                if (file_exists($svgRealPath)) {
                    $svgContent = GeneralUtility::getURL($svgRealPath);
                    static::$svgCache[$svgPath] = $svgContent;
                } elseif ($this->settings['viewHelpers']['image']['svg']['fileNotFoundException']) {
                    throw new \Exception('File not found: ' . $name . '.svg in ' . $this->settings['viewHelpers']['image']['svg']['themePath'] . ', ' . $this->settings['viewHelpers']['image']['svg']['defaultPath'], 2102940040);
                }
            }
        }

        return $svgContent;
    }
}

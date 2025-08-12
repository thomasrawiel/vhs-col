<?php

declare(strict_types=1);

namespace TRAW\VhsCol\ViewHelpers\Media\Source;

use TRAW\VhsCol\Utility\DatabaseUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ViewHelper to determine the maximum image width for container column
 */
class ContainerMaxWidthViewHelper extends AbstractViewHelper
{
    public function __construct(private readonly \TYPO3\CMS\Core\Database\ConnectionPool $connectionPool) {}
    /**
     * @api
     */
    public function initializeArguments(): void
    {
        $this->registerArgument('width', 'integer', 'Width of image to apply the math');
        $this->registerArgument('settings', 'array', 'Settings');
    }

    
    public function render(): float
    {
        $width = $this->arguments['width'];
        $settings = $this->arguments['settings'];
        $parentContainerUid = $this->renderingContext->getVariableProvider()->getByPath('data.tx_container_parent');

        if ($parentContainerUid > 0 && ($settings['enable'] ?? false)) {
            $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tt_content');
            $containerCType = DatabaseUtility::getContentAttributeByUid($parentContainerUid, 'CType');
            if (!empty($containerCType)) {
                $parentContainerColPos = $this->renderingContext->getVariableProvider()->getByPath('data.colPos');
                $factor = $settings[$containerCType . '.'][$parentContainerColPos] ?? $settings[$containerCType] ?? 1;
                if (is_numeric($factor)) {
                    return round($width * $factor);
                }
            }
        }

        return round($width);
    }
}

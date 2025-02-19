<?php
namespace TRAW\VhsCol\Traits;

/*
 * This file is adapted from the FluidTYPO3/Vhs project
 */

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\Variables\VariableProviderInterface;

/**
 * Class TemplateVariableViewHelperTrait
 *
 * Trait implementable by ViewHelpers which operate with
 * template variables in one way or another. Contains
 * the following main responsibilities:
 *
 * - A generic "as" argument solution
 * - A method to render child content with automatically
 *   backed up variables specified in an array.
 */
trait TemplateVariableViewHelperTrait
{
    /**
     * Default initialisation of arguments - will be used
     * if the implementing ViewHelper does not itself define
     * this method. The default behavior is to only register
     * the "as" argument.
     */
    public function initializeArguments(): void
    {
        $this->registerAsArgument();
    }

    /**
     * Registers the "as" argument for use with the
     * implementing ViewHelper.
     */
    protected function registerAsArgument(): void
    {
        $this->registerArgument(
            'as',
            'string',
            'Template variable name to assign; if not specified the ViewHelper returns the variable instead.'
        );
    }

    /**
     * @param mixed $variable
     * @return mixed
     */
    protected function renderChildrenWithVariableOrReturnInput($variable = null)
    {
        $as = $this->arguments['as'];
        if (empty($as)) {
            return $variable;
        } else {
            $variables = [$as => $variable];
            $content = $this->renderChildrenWithVariables($variables);
        }
        return $content;
    }

    /**
     * @param mixed $variable
     * @return mixed
     */
    protected static function renderChildrenWithVariableOrReturnInputStatic(
        $variable,
        ?string $as,
        RenderingContextInterface $renderingContext,
        \Closure $renderChildrenClosure
    ) {
        if (empty($as)) {
            return $variable;
        } else {
            $variables = [$as => $variable];
            $content = static::renderChildrenWithVariablesStatic(
                $variables,
                $renderingContext->getVariableProvider(),
                $renderChildrenClosure
            );
        }
        return $content;
    }

    /**
     * Renders tag content of ViewHelper and inserts variables
     * in $variables into $variableContainer while keeping backups
     * of each existing variable, restoring it after rendering.
     * Returns the output of the renderChildren() method on $viewHelper.
     *
     * @return mixed
     */
    protected function renderChildrenWithVariables(array $variables)
    {
        return static::renderChildrenWithVariablesStatic(
            $variables,
            $this->templateVariableContainer,
            $this->buildRenderChildrenClosure()
        );
    }

    /**
     * Renders tag content of ViewHelper and inserts variables
     * in $variables into $variableContainer while keeping backups
     * of each existing variable, restoring it after rendering.
     * Returns the output of the renderChildren() method on $viewHelper.
     *
     * @return mixed
     */
    protected static function renderChildrenWithVariablesStatic(
        array $variables,
        VariableProviderInterface $templateVariableContainer,
        \Closure $renderChildrenClosure
    ) {
        $backups = self::backupVariables($variables, $templateVariableContainer);
        $content = $renderChildrenClosure();
        self::restoreVariables($variables, $backups, $templateVariableContainer);
        return $content;
    }

    private static function backupVariables(
        array $variables,
        VariableProviderInterface $templateVariableContainer
    ): array {
        $backups = [];
        foreach ($variables as $variableName => $variableValue) {
            if ($templateVariableContainer->exists($variableName)) {
                $backups[$variableName] = $templateVariableContainer->get($variableName);
                $templateVariableContainer->remove($variableName);
            }
            $templateVariableContainer->add($variableName, $variableValue);
        }
        return $backups;
    }

    private static function restoreVariables(
        array $variables,
        array $backups,
        VariableProviderInterface $templateVariableContainer
    ): void {
        foreach ($variables as $variableName => $variableValue) {
            $templateVariableContainer->remove($variableName);
            if (isset($backups[$variableName])) {
                $templateVariableContainer->add($variableName, $backups[$variableName]);
            }
        }
    }
}

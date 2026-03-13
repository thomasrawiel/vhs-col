<?php
declare(strict_types=1);

namespace TRAW\VhsCol\Event;

abstract class AbstractAdditionalCropVariantsEvent
{
    public function __construct(private array $cropVariants)
    {
    }

    public function getCropVariants(): array
    {
        return $this->cropVariants;
    }

    public function setCropVariants(array $cropVariants): void
    {
        $this->cropVariants = $cropVariants;
    }

    public function hasCropVariant(string $identifier): bool
    {
        return array_key_exists($identifier, $this->cropVariants);
    }

    public function addCropVariant(string $identifier, array $cropConfig): void
    {
        $this->cropVariants[$identifier] = $cropConfig;
    }

    public function removeCropVariant(string $identifier): void
    {
        unset($this->cropVariants[$identifier]);
    }
}
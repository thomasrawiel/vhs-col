<?php
declare(strict_types=1);

namespace TRAW\VhsCol\Event;

class AspectRatioSetupEvent
{
    public function __construct(private array $aspectRatios)
    {
    }

    public function getAspectRatios(): array
    {
        return $this->aspectRatios;
    }

    public function setAspectRatios(array $aspectRatios): void
    {
        $this->aspectRatios = $aspectRatios;
    }

    public function addAspectRatio(
        string $identifier,
        string $title,
        float  $value,
        bool $disabled = false
    ): void
    {
        $this->aspectRatios[$identifier] = [
            'title' => $title,
            'value' => $value,
            'disabled' => $disabled,
        ];
    }

    public function hasAspectRatio(string $identifier): bool
    {
        return array_key_exists($identifier, $this->aspectRatios);
    }

    public function removeAspectRatio(string $identifier): void
    {
        unset($this->aspectRatios[$identifier]);
    }

}
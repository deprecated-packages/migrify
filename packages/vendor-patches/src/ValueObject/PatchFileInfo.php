<?php

declare(strict_types=1);

namespace Migrify\VendorPatches\ValueObject;

final class PatchFileInfo
{
    /**
     * @var string
     */
    private $diff;

    /**
     * @var string
     */
    private $absolutePath;

    public function __construct(string $diff, string $absolutePath)
    {
        $this->diff = $diff;
        $this->absolutePath = $absolutePath;
    }

    public function getDiff(): string
    {
        return $this->diff;
    }

    public function getAbsolutePath(): string
    {
        return $this->absolutePath;
    }
}

<?php
declare(strict_types=1);

namespace GibsonOS\Module\Transfer\Dto;

use DateTimeInterface;
use GibsonOS\Module\Transfer\Dto\ListItem\Permission;

class ListItem implements \JsonSerializable
{
    public function __construct(
        private string $name,
        private string $dir,
        private DateTimeInterface $modified,
        private int $size,
        private bool $isDir,
        private ?Permission $user = null,
        private ?Permission $group = null,
        private ?Permission $other = null,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDir(): string
    {
        return $this->dir;
    }

    public function getModified(): DateTimeInterface
    {
        return $this->modified;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function isDir(): bool
    {
        return $this->isDir;
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->getName(),
            'dir' => $this->getDir(),
            'size' => $this->getSize(),
            'modified' => $this->getModified()->format('Y-m-d H:i:s'),
            'isDir' => $this->isDir(),
        ];
    }
}

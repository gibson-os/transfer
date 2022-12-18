<?php
declare(strict_types=1);

namespace GibsonOS\Module\Transfer\Dto;

use GibsonOS\Module\Transfer\Dto\ListItem\Permission;

class ListItem implements \JsonSerializable
{
    public const TYPE_DIR = 'dir';

    public function __construct(
        private string $name,
        private string $decryptedName,
        private string $dir,
        private \DateTimeInterface $modified,
        private int $size,
        private string $type,
        private ?Permission $owner = null,
        private ?Permission $group = null,
        private ?Permission $other = null,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDecryptedName(): string
    {
        return $this->decryptedName;
    }

    public function getDir(): string
    {
        return $this->dir;
    }

    public function getModified(): \DateTimeInterface
    {
        return $this->modified;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getOwner(): ?Permission
    {
        return $this->owner;
    }

    public function getGroup(): ?Permission
    {
        return $this->group;
    }

    public function getOther(): ?Permission
    {
        return $this->other;
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->getName(),
            'decryptedName' => $this->getDecryptedName(),
            'dir' => $this->getDir(),
            'size' => $this->getSize(),
            'modified' => $this->getModified()->format('Y-m-d H:i:s'),
            'type' => $this->getType(),
            'owner' => $this->getOwner()?->jsonSerialize(),
            'group' => $this->getGroup()?->jsonSerialize(),
            'other' => $this->getOther()?->jsonSerialize(),
        ];
    }
}

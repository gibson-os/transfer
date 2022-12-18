<?php
declare(strict_types=1);

namespace GibsonOS\Module\Transfer\Dto\ListItem;

class Permission implements \JsonSerializable
{
    public function __construct(
        private bool $read,
        private bool $write,
        private bool $execute,
        private ?string $name = null
    ) {
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function isRead(): bool
    {
        return $this->read;
    }

    public function isWrite(): bool
    {
        return $this->write;
    }

    public function isExecute(): bool
    {
        return $this->execute;
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->getName(),
            'read' => $this->isRead(),
            'write' => $this->isWrite(),
            'execute' => $this->isExecute(),
        ];
    }
}

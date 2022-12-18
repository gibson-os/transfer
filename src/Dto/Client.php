<?php
declare(strict_types=1);

namespace GibsonOS\Module\Transfer\Dto;

use GibsonOS\Core\Model\AutoCompleteModelInterface;

class Client implements AutoCompleteModelInterface, \JsonSerializable
{
    public function __construct(private string $className)
    {
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getName(): string
    {
        return mb_substr($this->className, (mb_strrpos($this->className, '\\') ?: -1) + 1, -6);
    }

    public function getAutoCompleteId(): string
    {
        return $this->className;
    }

    public function jsonSerialize(): array
    {
        return [
            'className' => $this->getClassName(),
            'name' => $this->getName(),
        ];
    }
}

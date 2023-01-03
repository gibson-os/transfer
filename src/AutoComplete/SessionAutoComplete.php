<?php
declare(strict_types=1);

namespace GibsonOS\Module\Transfer\AutoComplete;

use GibsonOS\Core\AutoComplete\AutoCompleteInterface;
use GibsonOS\Core\Exception\Repository\SelectError;
use GibsonOS\Core\Model\AutoCompleteModelInterface;
use GibsonOS\Core\Service\SessionService;
use GibsonOS\Module\Transfer\Repository\SessionRepository;

class SessionAutoComplete implements AutoCompleteInterface
{
    public function __construct(private SessionRepository $sessionRepository, private SessionService $sessionService)
    {
    }

    /**
     * @throws SelectError
     */
    public function getByNamePart(string $namePart, array $parameters): array
    {
        return $this->sessionRepository->findByName($namePart, $this->sessionService->getUserId());
    }

    /**
     * @throws SelectError
     */
    public function getById(string $id, array $parameters): AutoCompleteModelInterface
    {
        return $this->sessionRepository->getById((int) $id, $this->sessionService->getUserId());
    }

    public function getModel(): string
    {
        return 'GibsonOS.module.transfer.session.model.Grid';
    }

    public function getValueField(): string
    {
        return 'id';
    }

    public function getDisplayField(): string
    {
        return 'name';
    }
}

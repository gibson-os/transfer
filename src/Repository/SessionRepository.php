<?php
declare(strict_types=1);

namespace GibsonOS\Module\Transfer\Repository;

use GibsonOS\Core\Exception\Repository\SelectError;
use GibsonOS\Core\Repository\AbstractRepository;
use GibsonOS\Module\Transfer\Model\Session;

class SessionRepository extends AbstractRepository
{
    /**
     * @throws SelectError
     */
    public function getById(int $id): Session
    {
        return $this->fetchOne('`id`=?', [$id], Session::class);
    }
}

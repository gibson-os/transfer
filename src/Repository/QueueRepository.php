<?php
declare(strict_types=1);

namespace GibsonOS\Module\Transfer\Repository;

use GibsonOS\Core\Exception\Repository\SelectError;
use GibsonOS\Core\Repository\AbstractRepository;
use GibsonOS\Module\Transfer\Model\Queue;

class QueueRepository extends AbstractRepository
{
    /**
     * @throws SelectError
     */
    public function getNextByStatus(string $status): Queue
    {
        return $this->fetchOne('`status`=?', [$status], Queue::class, '`added`');
    }

    public function countByStatus(string $status): int
    {
        $count = $this->getAggregate('COUNT(`id`)', '`status`=?', [$status], Queue::class);

        return empty($count) ? 0 : $count[0];
    }
}

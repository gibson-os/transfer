<?php
declare(strict_types=1);

namespace GibsonOS\Module\Transfer\Repository;

use GibsonOS\Core\Exception\Repository\SelectError;
use GibsonOS\Core\Repository\AbstractRepository;
use GibsonOS\Module\Transfer\Model\Queue;
use JsonException;
use MDO\Enum\OrderDirection;
use MDO\Exception\ClientException;
use MDO\Exception\RecordException;
use ReflectionException;

class QueueRepository extends AbstractRepository
{
    /**
     * @throws SelectError
     * @throws JsonException
     * @throws ClientException
     * @throws RecordException
     * @throws ReflectionException
     */
    public function getNextByStatus(string $status): Queue
    {
        return $this->fetchOne('`status`=?', [$status], Queue::class, ['`added`' => OrderDirection::ASC]);
    }

    /**
     * @throws ClientException
     * @throws RecordException
     * @throws SelectError
     */
    public function countByStatus(string $status): int
    {
        $aggregations = $this->getAggregations(['count' => 'COUNT(`id`)'], Queue::class, '`status`=?', [$status]);

        return (int) $aggregations->get('count')->getValue();
    }

    /**
     * @throws ClientException
     * @throws RecordException
     * @throws SelectError
     */
    public function queueExists(Queue $queue): bool
    {
        $where = ['`status`=?'];
        $whereParameters = [Queue::STATUS_WAIT];

        $this->addWhere($queue, 'session_id', 'getSessionId', $where, $whereParameters);
        $this->addWhere($queue, 'url', 'getUrl', $where, $whereParameters);
        $this->addWhere($queue, 'port', 'getPort', $where, $whereParameters);
        $this->addWhere($queue, 'protocol', 'getProtocol', $where, $whereParameters);
        $this->addWhere($queue, 'remote_user', 'getRemoteUser', $where, $whereParameters);
        $this->addWhere($queue, 'remote_password', 'getRemotePassword', $where, $whereParameters);
        $this->addWhere($queue, 'local_path', 'getLocalPath', $where, $whereParameters);
        $this->addWhere($queue, 'remote_path', 'getRemotePath', $where, $whereParameters);
        $this->addWhere($queue, 'direction', 'getDirection', $where, $whereParameters);

        $aggregations = $this->getAggregations(
            ['count' => 'COUNT(`id`)'],
            Queue::class,
            implode(' AND ', $where),
            $whereParameters,
        );
        $count = $aggregations->get('count')->getValue();

        return ($count ?? 0) > 0;
    }

    private function addWhere(Queue $queue, string $fieldName, string $getterName, array &$where, array &$whereParameters): void
    {
        $value = $queue->$getterName();

        if ($value === null) {
            return;
        }

        $where[] = '`' . $fieldName . '`=?';
        $whereParameters[] = $value;
    }
}

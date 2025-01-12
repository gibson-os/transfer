<?php
declare(strict_types=1);

namespace GibsonOS\Module\Transfer\Store;

use GibsonOS\Core\Store\AbstractDatabaseStore;
use GibsonOS\Module\Transfer\Model\Queue;

/**
 * @extends AbstractDatabaseStore<Queue>
 */
class TransferStore extends AbstractDatabaseStore
{
    private const TYPE_ACTIVE = 'active';

    private const TYPE_FINISHED = 'finished';

    private const TYPE_ERROR = 'error';

    private ?string $type = null;

    private ?string $direction = null;

    protected function getModelClassName(): string
    {
        return Queue::class;
    }

    protected function setWheres(): void
    {
        if ($this->type !== null) {
            if ($this->type === self::TYPE_ACTIVE) {
                $this->addWhere('`status` IN (?, ?)', [Queue::STATUS_WAIT, Queue::STATUS_ACTIVE]);
            } else {
                $this->addWhere('`status`=?', [$this->type]);
            }
        }

        if ($this->direction !== null) {
            $this->addWhere('`direction`=?', [$this->direction]);
        }
    }

    public function setType(?string $type): TransferStore
    {
        $this->type = $type;

        return $this;
    }

    public function setDirection(?string $direction): TransferStore
    {
        $this->direction = $direction;

        return $this;
    }
}

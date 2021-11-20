<?php
declare(strict_types=1);

namespace GibsonOS\Module\Transfer\Store;

use GibsonOS\Core\Store\AbstractDatabaseStore;
use GibsonOS\Module\Transfer\Model\Session;

class SessionStore extends AbstractDatabaseStore
{
    private ?int $userId = null;

    protected function getModelClassName(): string
    {
        return Session::class;
    }

    protected function setWheres(): void
    {
        $userWhere = '`user_id` IS NULL';
        $userParameters = [];

        if ($this->userId !== null) {
            $userWhere .= ' OR `user_id`=?';
            $userParameters[] = $this->userId;
        }

        $this->addWhere($userWhere, $userParameters);
    }

    protected function getDefaultOrder(): string
    {
        return '`name`';
    }

    public function setUserId(?int $userId): SessionStore
    {
        $this->userId = $userId;

        return $this;
    }
}

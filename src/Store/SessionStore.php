<?php
declare(strict_types=1);

namespace GibsonOS\Module\Transfer\Store;

use GibsonOS\Core\Service\CryptService;
use GibsonOS\Core\Store\AbstractDatabaseStore;
use GibsonOS\Core\Wrapper\DatabaseStoreWrapper;
use GibsonOS\Module\Transfer\Model\Session;

class SessionStore extends AbstractDatabaseStore
{
    private ?int $userId = null;

    public function __construct(
        private readonly CryptService $cryptService,
        DatabaseStoreWrapper $databaseStoreWrapper,
    ) {
        parent::__construct($databaseStoreWrapper);
    }

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

    public function getList(): iterable
    {
        /** @var Session $session */
        foreach (parent::getList() as $session) {
            $data = $session->jsonSerialize();
            $user = $session->getRemoteUser();
            $data['user'] = $user === null ? null : $this->cryptService->decrypt($user);

            yield $data;
        }
    }

    public function setUserId(?int $userId): SessionStore
    {
        $this->userId = $userId;

        return $this;
    }
}

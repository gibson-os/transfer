<?php
declare(strict_types=1);

namespace GibsonOS\Module\Transfer\Model;

use GibsonOS\Core\Exception\GetError;
use GibsonOS\Core\Model\AbstractModel;
use GibsonOS\Core\Model\User;
use mysqlDatabase;

class Session extends AbstractModel
{
    private ?int $userId = null;

    private ?User $user = null;

    /**
     * @param class-string $protocol
     *
     * @throws GetError
     */
    public function __construct(
        private string $name,
        private string $address,
        private string $protocol,
        private int $port = 0,
        private ?string $remoteUser = null,
        private ?string $remotePassword = null,
        private ?string $localPath = null,
        private ?string $remotePath = null,
        private ?string $data = null,
        private ?int $id = null,
        int $userId = null,
        User $user = null,
        mysqlDatabase $database = null
    ) {
        parent::__construct($database);

        $this->setForeignValueByModelOrId(
            $user,
            $userId,
            fn (?User $model) => $this->setUser($model),
            fn (?int $id) => $this->setUserId($id)
        );
    }

    public static function getTableName(): string
    {
        return 'transfer_session';
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Session
    {
        $this->name = $name;

        return $this;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): Session
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return class-string
     */
    public function getProtocol(): string
    {
        return $this->protocol;
    }

    /**
     * @param class-string $protocol
     */
    public function setProtocol(string $protocol): Session
    {
        $this->protocol = $protocol;

        return $this;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function setPort(int $port): Session
    {
        $this->port = $port;

        return $this;
    }

    public function getRemoteUser(): ?string
    {
        return $this->remoteUser;
    }

    public function setRemoteUser(?string $remoteUser): Session
    {
        $this->remoteUser = $remoteUser;

        return $this;
    }

    public function getRemotePassword(): ?string
    {
        return $this->remotePassword;
    }

    public function setRemotePassword(?string $remotePassword): Session
    {
        $this->remotePassword = $remotePassword;

        return $this;
    }

    public function getLocalPath(): ?string
    {
        return $this->localPath;
    }

    public function setLocalPath(?string $localPath): Session
    {
        $this->localPath = $localPath;

        return $this;
    }

    public function getRemotePath(): ?string
    {
        return $this->remotePath;
    }

    public function setRemotePath(?string $remotePath): Session
    {
        $this->remotePath = $remotePath;

        return $this;
    }

    public function getData(): ?string
    {
        return $this->data;
    }

    public function setData(?string $data): Session
    {
        $this->data = $data;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Session
    {
        $this->id = $id;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(?int $userId): Session
    {
        $this->userId = $userId;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): Session
    {
        $this->user = $user;

        return $this;
    }
}

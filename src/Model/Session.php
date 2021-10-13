<?php
declare(strict_types=1);

use GibsonOS\Core\Model\AbstractModel;
use GibsonOS\Core\Model\User;

class Session extends AbstractModel
{
    private ?int $userId = null;

    private ?User $user = null;

    public function __construct(
        private string $name,
        private string $url,
        private string $protocol,
        private int $port = 21,
        private ?string $remoteUser = null,
        private ?string $remotePassword = null,
        private ?string $localPath = null,
        private ?string $remotePath = null,
        private ?string $data = null,
        private ?int $id = null,
        mysqlDatabase $database = null,
        int $userId = null,
        User $user = null
    ) {
        parent::__construct($database);

        // @todo sowas wirst du öfter brauchen. Ab in die Abstract Model damit.
        if ($user !== null) {
            $this->setUser($user);
        } elseif ($userId !== null) {
            $this->setUserId($userId);
        }
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

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): Session
    {
        $this->url = $url;

        return $this;
    }

    public function getProtocol(): string
    {
        return $this->protocol;
    }

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

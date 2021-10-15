<?php
declare(strict_types=1);

namespace GibsonOS\Module\Transfer\Model;

use DateTimeInterface;
use GibsonOS\Core\Model\AbstractModel;
use GibsonOS\Core\Model\User;
use mysqlDatabase;

class Queue extends AbstractModel
{
    public const STATUS_ERROR = 'error';

    public const STATUS_WAIT = 'wait';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_FINISHED = 'finished';

    public const DIRECTION_DOWNLOAD = 'download';

    public const DIRECTION_UPLOAD = 'upload';

    private ?int $sessionId = null;

    private ?Session $session = null;

    private ?int $userId = null;

    private ?User $user = null;

    public function __construct(
        private string $localPath,
        private string $remotePath,
        private int $size,
        private int $transferred,
        private string $status = self::STATUS_WAIT,
        private string $direction = self::DIRECTION_DOWNLOAD,
        private bool $overwrite = false,
        private bool $crypt = false,
        private ?string $url = null,
        private ?int $port = null,
        private ?string $protocol = null,
        private ?string $remoteUser = null,
        private ?string $remotePassword = null,
        private ?string $message = null,
        private ?DateTimeInterface $cryptDate = null,
        private ?DateTimeInterface $start = null,
        private ?DateTimeInterface $end = null,
        private ?DateTimeInterface $added = null,
        private ?int $id = null,
        int $sessionId = null,
        Session $session = null,
        int $userId = null,
        User $user = null,
        mysqlDatabase $database = null
    ) {
        parent::__construct($database);

        $this->setForeignValueByModelOrId(
            $session,
            $sessionId,
            fn (?Session $model) => $this->setSession($model),
            fn (?int $id) => $this->setSessionId($id)
        );

        $this->setForeignValueByModelOrId(
            $user,
            $userId,
            fn (?User $model) => $this->setUser($model),
            fn (?int $id) => $this->setUserId($id)
        );
    }

    public static function getTableName(): string
    {
        return 'transfer_queue';
    }

    public function getLocalPath(): string
    {
        return $this->localPath;
    }

    public function setLocalPath(string $localPath): Queue
    {
        $this->localPath = $localPath;

        return $this;
    }

    public function getRemotePath(): string
    {
        return $this->remotePath;
    }

    public function setRemotePath(string $remotePath): Queue
    {
        $this->remotePath = $remotePath;

        return $this;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function setSize(int $size): Queue
    {
        $this->size = $size;

        return $this;
    }

    public function getTransferred(): int
    {
        return $this->transferred;
    }

    public function setTransferred(int $transferred): Queue
    {
        $this->transferred = $transferred;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): Queue
    {
        $this->status = $status;

        return $this;
    }

    public function getDirection(): string
    {
        return $this->direction;
    }

    public function setDirection(string $direction): Queue
    {
        $this->direction = $direction;

        return $this;
    }

    public function isOverwrite(): bool
    {
        return $this->overwrite;
    }

    public function setOverwrite(bool $overwrite): Queue
    {
        $this->overwrite = $overwrite;

        return $this;
    }

    public function isCrypt(): bool
    {
        return $this->crypt;
    }

    public function setCrypt(bool $crypt): Queue
    {
        $this->crypt = $crypt;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): Queue
    {
        $this->url = $url;

        return $this;
    }

    public function getPort(): ?int
    {
        return $this->port;
    }

    public function setPort(?int $port): Queue
    {
        $this->port = $port;

        return $this;
    }

    public function getProtocol(): ?string
    {
        return $this->protocol;
    }

    public function setProtocol(?string $protocol): Queue
    {
        $this->protocol = $protocol;

        return $this;
    }

    public function getRemoteUser(): ?string
    {
        return $this->remoteUser;
    }

    public function setRemoteUser(?string $remoteUser): Queue
    {
        $this->remoteUser = $remoteUser;

        return $this;
    }

    public function getRemotePassword(): ?string
    {
        return $this->remotePassword;
    }

    public function setRemotePassword(?string $remotePassword): Queue
    {
        $this->remotePassword = $remotePassword;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): Queue
    {
        $this->message = $message;

        return $this;
    }

    public function getCryptDate(): ?DateTimeInterface
    {
        return $this->cryptDate;
    }

    public function setCryptDate(?DateTimeInterface $cryptDate): Queue
    {
        $this->cryptDate = $cryptDate;

        return $this;
    }

    public function getStart(): ?DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(?DateTimeInterface $start): Queue
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?DateTimeInterface
    {
        return $this->end;
    }

    public function setEnd(?DateTimeInterface $end): Queue
    {
        $this->end = $end;

        return $this;
    }

    public function getAdded(): ?DateTimeInterface
    {
        return $this->added;
    }

    public function setAdded(?DateTimeInterface $added): Queue
    {
        $this->added = $added;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Queue
    {
        $this->id = $id;

        return $this;
    }

    public function getSessionId(): ?int
    {
        return $this->sessionId;
    }

    public function setSessionId(?int $sessionId): Queue
    {
        $this->sessionId = $sessionId;

        return $this;
    }

    public function getSession(): ?Session
    {
        return $this->session;
    }

    public function setSession(?Session $session): Queue
    {
        $this->session = $session;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(?int $userId): Queue
    {
        $this->userId = $userId;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): Queue
    {
        $this->user = $user;

        return $this;
    }
}

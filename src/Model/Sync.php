<?php
declare(strict_types=1);

namespace GibsonOS\Module\Transfer\Model;

use DateTimeImmutable;
use DateTimeInterface;
use GibsonOS\Core\Attribute\Install\Database\Column;
use GibsonOS\Core\Attribute\Install\Database\Constraint;
use GibsonOS\Core\Attribute\Install\Database\Table;
use GibsonOS\Core\Model\AbstractModel;
use GibsonOS\Core\Model\User;
use mysqlDatabase;

/**
 * @method Session getSession()
 * @method Sync    setSession(Session $session)
 * @method User    getUser()
 * @method Sync    setUser(User $user)
 */
#[Table]
class Sync extends AbstractModel
{
    #[Column(attributes: [Column::ATTRIBUTE_UNSIGNED], autoIncrement: true)]
    private ?int $id = null;

    #[Column(attributes: [Column::ATTRIBUTE_UNSIGNED])]
    private ?int $sessionId = null;

    #[Column(length: 512)]
    private string $localPath;

    #[Column(length: 512)]
    private string $remotePath;

    #[Column(type: Column::TYPE_ENUM, values: ['once', 'hourly', 'daily', 'weekly', 'monthly', 'yearly'])]
    private string $interval = 'once';

    #[Column(type: Column::TYPE_ENUM, values: ['up', 'down', 'sync'])]
    private string $direction = 'sync';

    #[Column(type: Column::TYPE_ENUM, values: ['yes', 'no', 'only'])]
    private string $delete;

    #[Column]
    private bool $crypt = false;

    #[Column(type: Column::TYPE_ENUM, values: ['disabled', 'enabled', 'active'])]
    private string $staus = 'enabled';

    #[Column(attributes: [Column::ATTRIBUTE_UNSIGNED])]
    private int $userId;

    #[Column(type: Column::TYPE_TIMESTAMP, default: Column::DEFAULT_CURRENT_TIMESTAMP)]
    private DateTimeInterface $added;

    #[Column]
    private ?DateTimeInterface $nextRun = null;

    #[Constraint]
    protected Session $session;

    #[Constraint]
    protected User $user;

    public function __construct(mysqlDatabase $database = null)
    {
        parent::__construct($database);

        $this->added = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Sync
    {
        $this->id = $id;

        return $this;
    }

    public function getSessionId(): ?int
    {
        return $this->sessionId;
    }

    public function setSessionId(?int $sessionId): Sync
    {
        $this->sessionId = $sessionId;

        return $this;
    }

    public function getLocalPath(): string
    {
        return $this->localPath;
    }

    public function setLocalPath(string $localPath): Sync
    {
        $this->localPath = $localPath;

        return $this;
    }

    public function getRemotePath(): string
    {
        return $this->remotePath;
    }

    public function setRemotePath(string $remotePath): Sync
    {
        $this->remotePath = $remotePath;

        return $this;
    }

    public function getInterval(): string
    {
        return $this->interval;
    }

    public function setInterval(string $interval): Sync
    {
        $this->interval = $interval;

        return $this;
    }

    public function getDirection(): string
    {
        return $this->direction;
    }

    public function setDirection(string $direction): Sync
    {
        $this->direction = $direction;

        return $this;
    }

    public function getDelete(): string
    {
        return $this->delete;
    }

    public function setDelete(string $delete): Sync
    {
        $this->delete = $delete;

        return $this;
    }

    public function isCrypt(): bool
    {
        return $this->crypt;
    }

    public function setCrypt(bool $crypt): Sync
    {
        $this->crypt = $crypt;

        return $this;
    }

    public function getStaus(): string
    {
        return $this->staus;
    }

    public function setStaus(string $staus): Sync
    {
        $this->staus = $staus;

        return $this;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): Sync
    {
        $this->userId = $userId;

        return $this;
    }

    public function getAdded(): DateTimeImmutable|DateTimeInterface
    {
        return $this->added;
    }

    public function setAdded(DateTimeImmutable|DateTimeInterface $added): Sync
    {
        $this->added = $added;

        return $this;
    }

    public function getNextRun(): ?DateTimeInterface
    {
        return $this->nextRun;
    }

    public function setNextRun(?DateTimeInterface $nextRun): Sync
    {
        $this->nextRun = $nextRun;

        return $this;
    }
}

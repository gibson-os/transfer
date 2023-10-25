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
use GibsonOS\Core\Wrapper\ModelWrapper;
use GibsonOS\Module\Transfer\Client\ClientInterface;
use JsonSerializable;

/**
 * @method User|null getUser()
 * @method           setUser(?User $user)
 */
#[Table]
class Queue extends AbstractModel implements JsonSerializable
{
    public const STATUS_ERROR = 'error';

    public const STATUS_WAIT = 'wait';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_FINISHED = 'finished';

    public const DIRECTION_DOWNLOAD = 'download';

    public const DIRECTION_UPLOAD = 'upload';

    #[Column(attributes: [Column::ATTRIBUTE_UNSIGNED], autoIncrement: true)]
    private ?int $id = null;

    #[Column(length: 512)]
    private string $localPath;

    #[Column(length: 512)]
    private string $remotePath;

    #[Column(attributes: [Column::ATTRIBUTE_UNSIGNED])]
    private int $size = 0;

    #[Column(attributes: [Column::ATTRIBUTE_UNSIGNED])]
    private int $transferred = 0;

    #[Column(type: Column::TYPE_ENUM, values: ['error', 'wait', 'active', 'finished'])]
    private string $status = self::STATUS_WAIT;

    #[Column(type: Column::TYPE_ENUM, values: ['download', 'upload'])]
    private string $direction = self::DIRECTION_DOWNLOAD;

    #[Column]
    private bool $overwrite = false;

    #[Column]
    private bool $crypt = false;

    #[Column(type: Column::TYPE_TEXT)]
    private ?string $url = null;

    #[Column(attributes: [Column::ATTRIBUTE_UNSIGNED])]
    private ?int $port = null;

    /**
     * @var class-string<ClientInterface>|null
     */
    #[Column(length: 255)]
    private ?string $protocol = null;

    #[Column(length: 512)]
    private ?string $remoteUser = null;

    #[Column(length: 512)]
    private ?string $remotePassword = null;

    #[Column(type: Column::TYPE_TEXT)]
    private ?string $message = null;

    #[Column]
    private ?DateTimeInterface $cryptDate = null;

    #[Column]
    private ?DateTimeInterface $start = null;

    #[Column]
    private ?DateTimeInterface $end = null;

    #[Column(type: Column::TYPE_TIMESTAMP, default: Column::DEFAULT_CURRENT_TIMESTAMP)]
    private DateTimeInterface $added;

    #[Column(attributes: [Column::ATTRIBUTE_UNSIGNED])]
    private ?int $sessionId = null;

    #[Constraint]
    private ?Session $session = null;

    #[Column(attributes: [Column::ATTRIBUTE_UNSIGNED])]
    private ?int $userId = null;

    #[Constraint]
    protected ?User $user = null;

    public function __construct(ModelWrapper $modelWrapper)
    {
        parent::__construct($modelWrapper);

        $this->added = new DateTimeImmutable();
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

    /**
     * @return class-string<ClientInterface>|null
     */
    public function getProtocol(): ?string
    {
        return $this->protocol;
    }

    /**
     * @param class-string<ClientInterface>|null $protocol
     */
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

    public function getAdded(): DateTimeInterface
    {
        return $this->added;
    }

    public function setAdded(DateTimeInterface $added): Queue
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

    public function jsonSerialize(): array
    {
        return [
            'localPath' => $this->getLocalPath(),
            'remotePath' => $this->getRemotePath(),
            'size' => $this->getSize(),
            'transferred' => $this->getTransferred(),
            'status' => $this->getStatus(),
            'direction' => $this->getDirection(),
            'overwrite' => $this->isOverwrite(),
            'crypt' => $this->isCrypt(),
            'url' => $this->getUrl(),
            'port' => $this->getPort(),
            'protocol' => $this->getProtocol(),
            'message' => $this->getMessage(),
            'userId' => $this->getUserId(),
            'added' => $this->added->format('Y-m-d H:i:s'),
            'start' => $this->start?->format('Y-m-d H:i:s'),
            'end' => $this->end?->format('Y-m-d H:i:s'),
            'cryptDate' => $this->cryptDate?->format('Y-m-d H:i:s'),
        ];
    }
}

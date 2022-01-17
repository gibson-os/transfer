<?php
declare(strict_types=1);

namespace GibsonOS\Module\Transfer\Model;

use GibsonOS\Core\Attribute\Install\Database\Column;
use GibsonOS\Core\Attribute\Install\Database\Constraint;
use GibsonOS\Core\Attribute\Install\Database\Key;
use GibsonOS\Core\Attribute\Install\Database\Table;
use GibsonOS\Core\Model\AbstractModel;
use GibsonOS\Core\Model\AutoCompleteModelInterface;
use GibsonOS\Core\Model\User;
use JsonSerializable;

/**
 * @method User|null getUser()
 * @method           setUser(?User $user)
 */
#[Table]
class Session extends AbstractModel implements JsonSerializable, AutoCompleteModelInterface
{
    #[Column(attributes: [Column::ATTRIBUTE_UNSIGNED], autoIncrement: true)]
    private ?int $id = null;

    #[Column(length: 64)]
    #[Key(true)]
    private string $name;

    #[Column(type: Column::TYPE_TEXT)]
    private string $url;

    /**
     * @var class-string
     */
    #[Column(length: 255)]
    private string $protocol;

    #[Column(attributes: [Column::ATTRIBUTE_UNSIGNED])]
    private int $port = 0;

    #[Column(length: 512)]
    private ?string $remoteUser = null;

    #[Column(length: 512)]
    private ?string $remotePassword = null;

    #[Column(length: 512)]
    private ?string $localPath = null;

    #[Column(length: 512)]
    private ?string $remotePath = null;

    #[Column(type: Column::TYPE_TEXT)]
    private ?string $data = null;

    #[Column(attributes: [Column::ATTRIBUTE_UNSIGNED])]
    private ?int $userId = null;

    #[Constraint]
    protected ?User $user = null;

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

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'url' => $this->getUrl(),
            'protocol' => $this->getProtocol(),
            'port' => $this->getPort(),
            'remotePath' => $this->getRemotePath(),
            'localPath' => $this->getLocalPath(),
            'onlyForThisUser' => $this->getUserId() !== null,
        ];
    }

    public function getAutoCompleteId(): string|int|float
    {
        return $this->getId() ?? 0;
    }
}

<?php
declare(strict_types=1);

namespace GibsonOS\Module\Transfer\Service;

use GibsonOS\Core\Exception\CreateError;
use GibsonOS\Core\Exception\FactoryError;
use GibsonOS\Core\Exception\FileExistsError;
use GibsonOS\Core\Exception\GetError;
use GibsonOS\Core\Exception\Model\SaveError;
use GibsonOS\Core\Exception\Repository\SelectError;
use GibsonOS\Core\Service\CryptService;
use GibsonOS\Core\Service\DateTimeService;
use GibsonOS\Core\Service\DirService;
use GibsonOS\Core\Service\FileService;
use GibsonOS\Module\Transfer\Client\ClientInterface;
use GibsonOS\Module\Transfer\Dto\ListItem;
use GibsonOS\Module\Transfer\Exception\ClientException;
use GibsonOS\Module\Transfer\Model\Queue;

class QueueService
{
    public function __construct(
        private DirService $dirService,
        private FileService $fileService,
        private CryptService $cryptService,
        private DateTimeService $dateTimeService,
        private ClientService $clientService
    ) {
    }

    /**
     * @param class-string<ClientInterface>|null $protocol
     *
     * @throws SaveError
     * @throws ClientException
     */
    public function addDownload(
        ClientInterface $client,
        string $localPath,
        string $remotePath,
        array $files = null,
        array|bool $overwrite = null,
        array|bool $ignore = null,
        int $sessionId = null,
        string $protocol = null,
        string $address = null,
        int $port = null,
        string $user = null,
        string $password = null
    ): void {
        ini_set('max_execution_time', '0');
        $localPath = $this->dirService->addEndSlash($localPath);
        $remotePath = $this->dirService->addEndSlash($remotePath, '/');

        if (!$this->dirService->isWritable($localPath, $this->fileService)) {
            // @todo exception
        }

        $cryptUser = $user === null ? null : $this->cryptService->encrypt($user);
        $cryptPassword = $password === null ? null : $this->cryptService->encrypt($password);

        foreach ($client->getList($remotePath) as $item) {
            if ($files !== null && !in_array($item->getName(), $files)) {
                continue;
            }

            $localItemPath = $localPath . $item->getDecryptedName();
            $remoteItemPath = $remotePath . $item->getName();

            if ($item->getType() === ListItem::TYPE_DIR) {
                $this->addDownload(
                    $client,
                    $localItemPath,
                    $remoteItemPath,
                    null,
                    $overwrite,
                    $ignore,
                    $sessionId,
                    $protocol,
                    $address,
                    $port,
                    $user,
                    $password
                );

                continue;
            }

            $overwriteItem = $overwrite === true || (is_array($overwrite) && array_search($localItemPath, $overwrite) !== false);
            $ignoreItem = $ignore === true || (is_array($ignore) && array_search($localItemPath, $ignore) !== false);

            if (!$overwriteItem && $this->fileService->exists($localItemPath)) {
                if ($ignoreItem) {
                    continue;
                }

                // @todo exception mit buttons
            }

            (new Queue())
                ->setLocalPath($localItemPath)
                ->setRemotePath($remoteItemPath)
                ->setSize($item->getSize())
                ->setDirection(Queue::DIRECTION_DOWNLOAD)
                ->setOverwrite($overwriteItem)
                ->setUrl($address)
                ->setPort($port ?? $client->getDefaultPort())
                ->setProtocol($protocol)
                ->setRemoteUser($cryptUser)
                ->setRemotePassword($cryptPassword)
                ->setSessionId($sessionId)
                ->save()
            ;
        }
    }

    /**
     * @param class-string<ClientInterface>|null $protocol
     *
     * @throws SaveError
     * @throws ClientException
     * @throws GetError
     */
    public function addUpload(
        ClientInterface $client,
        string $remotePath,
        string $localPath,
        bool $crypt,
        array $files = null,
        array|bool $overwrite = null,
        array|bool $ignore = null,
        int $sessionId = null,
        string $protocol = null,
        string $address = null,
        int $port = null,
        string $user = null,
        string $password = null
    ): void {
        ini_set('max_execution_time', '0');
        $remotePath = $this->dirService->addEndSlash($remotePath, '/');
        $localPath = $this->dirService->addEndSlash($localPath);

        $cryptUser = $user === null ? null : $this->cryptService->encrypt($user);
        $cryptPassword = $password === null ? null : $this->cryptService->encrypt($password);

        foreach ($this->dirService->getFiles($localPath) as $item) {
            $fileName = $this->fileService->getFilename($item);

            if ($files !== null && !in_array($fileName, $files)) {
                continue;
            }

            $remoteItemPath =
                $remotePath .
                (
                    $crypt
                    ? (
                        is_dir($item)
                        ? $this->clientService->encryptDirName($fileName)
                        : $this->clientService->encryptFileName($item)
                    )
                    : $fileName
                )
            ;

            if (is_dir($item)) {
                $this->addUpload(
                    $client,
                    $remoteItemPath,
                    $item,
                    $crypt,
                    null,
                    $overwrite,
                    $ignore,
                    $sessionId,
                    $protocol,
                    $address,
                    $port,
                    $user,
                    $password
                );

                continue;
            }

            $overwriteItem = $overwrite === true || (is_array($overwrite) && array_search($remoteItemPath, $overwrite) !== false);
            $ignoreItem = $ignore === true || (is_array($ignore) && array_search($remoteItemPath, $ignore) !== false);

            if (!$overwriteItem && $client->fileExists($remoteItemPath)) {
                if ($ignoreItem) {
                    continue;
                }

                // @todo exception mit buttons
            }

            (new Queue())
                ->setLocalPath($item)
                ->setRemotePath($remoteItemPath)
                ->setSize(filesize($item))
                ->setDirection(Queue::DIRECTION_UPLOAD)
                ->setOverwrite($overwriteItem)
                ->setUrl($address)
                ->setPort($port ?? $client->getDefaultPort())
                ->setProtocol($protocol)
                ->setRemoteUser($cryptUser)
                ->setRemotePassword($cryptPassword)
                ->setSessionId($sessionId)
                ->setCrypt($crypt)
                ->save()
            ;
        }
    }

    /**
     * @throws ClientException
     * @throws FactoryError
     * @throws FileExistsError
     * @throws SaveError
     * @throws SelectError
     * @throws CreateError
     */
    public function handle(Queue $queue): void
    {
        $queue
            ->setStatus(Queue::STATUS_ACTIVE)
            ->setStart($this->dateTimeService->get())
            ->save()
        ;
        $remoteUser = $queue->getRemoteUser();
        $remotePassword = $queue->getRemotePassword();

        try {
            $client = $this->clientService->connect(
                $queue->getSessionId(),
                $queue->getProtocol(),
                $queue->getUrl(),
                $queue->getPort(),
                $remoteUser === null ? null : $this->cryptService->decrypt($remoteUser),
                $remotePassword === null ? null : $this->cryptService->decrypt($remotePassword),
            );
        } catch (FactoryError|SelectError|ClientException $exception) {
            $queue
                ->setStatus(Queue::STATUS_ERROR)
                ->setMessage('Connection error!')
                ->setEnd($this->dateTimeService->get())
                ->save()
            ;

            throw $exception;
        }

        try {
            if ($queue->getDirection() === Queue::DIRECTION_DOWNLOAD) {
                $this->clientService->get(
                    $client,
                    $queue->getRemotePath(),
                    $queue->getLocalPath(),
                    $queue->isOverwrite(),
                    $queue->isCrypt()
                );
            } else {
                $this->clientService->put(
                    $client,
                    $queue->getLocalPath(),
                    $queue->getRemotePath(),
                    $queue->isOverwrite(),
                    $queue->isCrypt()
                );
            }

            $queue->setStatus(Queue::STATUS_FINISHED);
        } catch (ClientException $exception) {
            $queue
                ->setStatus(Queue::STATUS_ERROR)
                ->setMessage('Transmission error!')
            ;

            throw $exception;
        } catch (CreateError|FileExistsError $exception) {
            $queue
                ->setStatus(Queue::STATUS_ERROR)
                ->setMessage($exception->getMessage())
            ;

            throw $exception;
        } finally {
            $queue
                ->setEnd($this->dateTimeService->get())
                ->save()
            ;
            $client->disconnect();
        }
    }
}

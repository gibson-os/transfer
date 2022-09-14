<?php
declare(strict_types=1);

namespace GibsonOS\Module\Transfer\Service;

use GibsonOS\Core\Exception\AbstractException;
use GibsonOS\Core\Exception\CreateError;
use GibsonOS\Core\Exception\FactoryError;
use GibsonOS\Core\Exception\FileExistsError;
use GibsonOS\Core\Exception\FileNotFound;
use GibsonOS\Core\Exception\GetError;
use GibsonOS\Core\Exception\Model\SaveError;
use GibsonOS\Core\Manager\ModelManager;
use GibsonOS\Core\Service\CryptService;
use GibsonOS\Core\Service\DateTimeService;
use GibsonOS\Core\Service\DirService;
use GibsonOS\Core\Service\FileService;
use GibsonOS\Core\Utility\StatusCode;
use GibsonOS\Module\Transfer\Client\ClientInterface;
use GibsonOS\Module\Transfer\Dto\ListItem;
use GibsonOS\Module\Transfer\Exception\ClientException;
use GibsonOS\Module\Transfer\Exception\QueueException;
use GibsonOS\Module\Transfer\Model\Queue;
use GibsonOS\Module\Transfer\Model\Session;
use GibsonOS\Module\Transfer\Repository\QueueRepository;
use JsonException;
use ReflectionException;

class QueueService
{
    public function __construct(
        private DirService $dirService,
        private FileService $fileService,
        private CryptService $cryptService,
        private ClientCryptService $clientCryptService,
        private DateTimeService $dateTimeService,
        private ClientService $clientService,
        private QueueRepository $queueRepository,
        private ModelManager $modelManager
    ) {
    }

    /**
     * @param class-string<ClientInterface>|null $protocol
     *
     * @throws ClientException
     * @throws JsonException
     * @throws QueueException
     * @throws ReflectionException
     * @throws SaveError
     */
    public function addDownload(
        ClientInterface $client,
        string $localPath,
        string $remotePath,
        array $files = null,
        bool $overwriteAll = false,
        array $overwrite = [],
        bool $ignoreAll = false,
        array $ignore = [],
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
            throw new QueueException(sprintf('Directory %s is not writable!', $localPath));
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
                    $overwriteAll,
                    $overwrite,
                    $ignoreAll,
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

            $overwriteItem = $overwriteAll === true || array_search($localItemPath, $overwrite) !== false;
            $queue = (new Queue())
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
                ->setCrypt($item->getDecryptedName() !== $item->getName())
            ;

            if ($this->queueRepository->queueExists($queue)) {
                continue;
            }

            if (!$overwriteItem && $this->fileService->exists($localItemPath)) {
                if ($ignoreAll === true || array_search($localItemPath, $ignore) !== false) {
                    continue;
                }

                $this->throwOverwriteException($localItemPath, $overwrite, $ignore);
            }

            $this->modelManager->save($queue);
        }
    }

    /**
     * @param class-string<ClientInterface>|null $protocol
     *
     * @throws ClientException
     * @throws GetError
     * @throws JsonException
     * @throws QueueException
     * @throws ReflectionException
     * @throws SaveError
     */
    public function addUpload(
        ClientInterface $client,
        string $remotePath,
        string $localPath,
        bool $crypt,
        array $files = null,
        bool $overwriteAll = false,
        array $overwrite = [],
        bool $ignoreAll = false,
        array $ignore = [],
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

            $decryptedRemoteItemPath = $remotePath . $fileName;
            $encryptedRemoteItemPath = $remotePath . (
                is_dir($item)
                    ? $this->clientCryptService->encryptDirName($fileName)
                    : $this->clientCryptService->encryptFileName($fileName)
            );
            $remoteItemPath = $crypt ? $encryptedRemoteItemPath : $decryptedRemoteItemPath;

            if (is_dir($item)) {
                $this->addUpload(
                    $client,
                    $remoteItemPath,
                    $item,
                    $crypt,
                    null,
                    $overwriteAll,
                    $overwrite,
                    $ignoreAll,
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

            $overwriteItem = $overwriteAll === true || array_search($decryptedRemoteItemPath, $overwrite) !== false;
            $queue = (new Queue())
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
            ;

            if ($this->queueRepository->queueExists($queue)) {
                continue;
            }

            if (!$overwriteItem && $client->fileExists($remoteItemPath)) {
                if ($ignoreAll === true || array_search($decryptedRemoteItemPath, $ignore) !== false) {
                    continue;
                }

                $this->throwOverwriteException($decryptedRemoteItemPath, $overwrite, $ignore);
            }

            $this->modelManager->save($queue);
        }
    }

    /**
     * @throws ClientException
     * @throws CreateError
     * @throws FactoryError
     * @throws FileExistsError
     * @throws FileNotFound
     * @throws SaveError
     * @throws JsonException
     * @throws ReflectionException
     */
    public function handle(Queue $queue): void
    {
        $this->modelManager->save(
            $queue
                ->setStatus(Queue::STATUS_ACTIVE)
                ->setStart($this->dateTimeService->get())
        );

        try {
            $session = $queue->getSession();

            if ($session === null) {
                $protocol = $queue->getProtocol();
                $url = $queue->getUrl();
                $port = $queue->getPort();

                if ($protocol === null || $url === null || $port === null) {
                    throw new ClientException('Protocol, url or port not set!');
                }

                $session = (new Session())
                    ->setProtocol($protocol)
                    ->setUrl($url)
                    ->setPort($port)
                    ->setRemoteUser($queue->getRemoteUser())
                    ->setRemotePassword($queue->getRemotePassword())
                ;
            }

            $client = $this->clientService->connect($session);
        } catch (FactoryError|ClientException $exception) {
            $this->modelManager->save(
                $queue
                    ->setStatus(Queue::STATUS_ERROR)
                    ->setMessage('Connection error!')
                    ->setEnd($this->dateTimeService->get())
            );

            throw $exception;
        }

        try {
            $remotePath = $queue->getRemotePath();
            $localPath = $queue->getLocalPath();

            if ($queue->getDirection() === Queue::DIRECTION_DOWNLOAD) {
                $this->clientService->get(
                    $client,
                    $remotePath,
                    $localPath,
                    $queue->isOverwrite(),
                );

                if ($queue->isCrypt()) {
                    $this->modelManager->save($queue->setCryptDate($this->dateTimeService->get()));
                    $this->clientCryptService->decryptFile($localPath);
                }
            } else {
                $encryptedPath = null;

                if ($queue->isCrypt()) {
                    $this->modelManager->save($queue->setCryptDate($this->dateTimeService->get()));
                    $encryptedPath = $this->clientCryptService->encryptFile($localPath);
                }

                $this->clientService->put(
                    $client,
                    $encryptedPath ?? $localPath,
                    $remotePath,
                    $queue->isOverwrite(),
                );

                if ($encryptedPath !== null) {
                    unlink($encryptedPath);
                }
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
            $this->modelManager->save($queue->setEnd($this->dateTimeService->get()));
            $client->disconnect();
        }
    }

    /**
     * @throws QueueException
     */
    private function throwOverwriteException(string $path, array $overwrite, array $ignore): void
    {
        $overwrite[] = $path;
        $ignore[] = $path;

        throw (new QueueException(sprintf('Datei %s existiert bereits. Überschreiben?', $path), StatusCode::CONFLICT))
            ->setTitle('Datei überschreiben?')
            ->setType(AbstractException::QUESTION)
            ->addButton('Überschreiben', 'overwrite[]', $overwrite)
            ->addButton('Alle Überschreiben', 'overwriteAll', true)
            ->addButton('Ignorieren', 'ignore[]', $ignore)
            ->addButton('Alle Ignorieren', 'ignoreAll', true)
            ->addButton('Abbrechen')
        ;
    }
}

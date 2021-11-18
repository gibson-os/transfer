<?php
declare(strict_types=1);

namespace GibsonOS\Module\Transfer\Service;

use GibsonOS\Core\Exception\Model\SaveError;
use GibsonOS\Core\Service\CryptService;
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
        private CryptService $cryptService
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
                ->setPort($port)
                ->setProtocol($protocol)
                ->setRemoteUser($cryptUser)
                ->setRemotePassword($cryptPassword)
                ->setSessionId($sessionId)
                ->save()
            ;
        }
    }

    public function addUpload(ClientInterface $client): void
    {
    }
}

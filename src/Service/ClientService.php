<?php
declare(strict_types=1);

namespace GibsonOS\Module\Transfer\Service;

use GibsonOS\Core\Exception\CreateError;
use GibsonOS\Core\Exception\FactoryError;
use GibsonOS\Core\Exception\FileExistsError;
use GibsonOS\Core\Exception\Repository\SelectError;
use GibsonOS\Core\Service\DateTimeService;
use GibsonOS\Core\Service\DirService;
use GibsonOS\Core\Service\FileService;
use GibsonOS\Module\Transfer\Client\ClientInterface;
use GibsonOS\Module\Transfer\Dto\ListItem;
use GibsonOS\Module\Transfer\Exception\ClientException;
use GibsonOS\Module\Transfer\Factory\ClientFactory;
use GibsonOS\Module\Transfer\Repository\SessionRepository;

class ClientService
{
    public function __construct(
        private DirService $dirService,
        private FileService $fileService,
        private DateTimeService $dateTimeService,
        private SessionRepository $sessionRepository,
        private ClientFactory $clientFactory
    ) {
    }

    /**
     * @throws ClientException
     */
    public function getList(ClientInterface $client, string $dir, bool $withFiles): array
    {
        $dirs = [];
        $files = [];

        foreach ($client->getList($dir) as $item) {
            if ($item->getType() === ListItem::TYPE_DIR) {
                $dirs[$item->getDecryptedName()] = $item;

                continue;
            }

            if ($withFiles) {
                $files[$item->getDecryptedName()] = $item;
            }
        }

        ksort($dirs);
        ksort($files);

        return array_values($dirs) + array_values($files);
    }

    /**
     * @throws ClientException
     */
    public function createDir(ClientInterface $client, string $dir, string $name, string $decryptedName = null): ListItem
    {
        $previousDir = $this->dirService->getDirName($dir, '/');

        if (!$client->fileExists($dir)) {
            $this->createDir(
                $client,
                $previousDir,
                $this->dirService->removeEndSlash(str_replace($previousDir, '', $dir)),
            );
        }

        $client->createDir($dir . $name);

        return new ListItem(
            $name,
            $decryptedName ?? $name,
            $dir,
            $this->dateTimeService->get(),
            0,
            ListItem::TYPE_DIR
        );
    }

    /**
     * @throws ClientException
     */
    public function delete(ClientInterface $client, string $dir, array $files = null): void
    {
        $dir = $this->dirService->addEndSlash($dir, '/');

        foreach ($client->getList($dir) as $item) {
            if ($files !== null && !in_array($item->getName(), $files)) {
                continue;
            }

            $path = $item->getDir() . $item->getName();

            if ($item->getType() === ListItem::TYPE_DIR) {
                $this->delete($client, $path);

                continue;
            }

            $client->deleteFile($path);
        }

        if ($files === null) {
            $client->deleteDir($dir);
        }
    }

    /**
     * @throws ClientException
     * @throws FileExistsError
     * @throws CreateError
     */
    public function get(ClientInterface $client, string $remotePath, string $localPath, bool $overwrite): void
    {
        if ($this->fileService->exists($localPath)) {
            if (!$overwrite) {
                throw new FileExistsError(sprintf('Local file %s already exists!', $localPath));
            }

            unlink($localPath);
        }

        $dirName = $this->dirService->getDirName($localPath);

        if (!$this->fileService->exists($dirName)) {
            $this->dirService->create($dirName);
        }

        $client->get($remotePath, $localPath);
    }

    /**
     * @throws ClientException
     * @throws FileExistsError
     */
    public function put(ClientInterface $client, string $localPath, string $remotePath, bool $overwrite): void
    {
        if ($client->fileExists($remotePath)) {
            if (!$overwrite) {
                throw new FileExistsError(sprintf('Remote file %s already exists!', $remotePath));
            }

            $client->deleteFile($remotePath);
        }

        $dirName = $this->dirService->getDirName($remotePath, '/');

        if (!$client->fileExists($dirName)) {
            $previousDir = $this->dirService->getDirName($dirName, '/');
            $this->createDir(
                $client,
                $previousDir,
                $this->dirService->removeEndSlash(str_replace($previousDir, '', $dirName)),
            );
        }

        $client->put($localPath, $remotePath);
    }

    /**
     * @param class-string<ClientInterface>|null $protocol
     *
     * @throws ClientException
     * @throws FactoryError
     * @throws SelectError
     */
    public function connect(
        int $id = null,
        string $protocol = null,
        string $address = null,
        int $port = null,
        string $user = null,
        string $password = null
    ): ClientInterface {
        if ($id !== null) {
            $session = $this->sessionRepository->getById($id);
            $protocol = $session->getProtocol();
            $address = $session->getUrl();
            $port = $session->getPort();
            $user = $session->getRemoteUser();
            $password = $session->getRemotePassword();
        }

        if ($protocol === null) {
            throw new ClientException('No protocol set!');
        }

        $client = $this->clientFactory->get($protocol);
        $client->connect($address ?? '', $user, $password, $port);

        return $client;
    }
}

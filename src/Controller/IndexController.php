<?php
declare(strict_types=1);

namespace GibsonOS\Module\Transfer\Controller;

use GibsonOS\Core\Attribute\CheckPermission;
use GibsonOS\Core\Attribute\GetMappedModel;
use GibsonOS\Core\Controller\AbstractController;
use GibsonOS\Core\Enum\Permission;
use GibsonOS\Core\Exception\FactoryError;
use GibsonOS\Core\Exception\GetError;
use GibsonOS\Core\Exception\Model\SaveError;
use GibsonOS\Core\Exception\Repository\SelectError;
use GibsonOS\Core\Service\Response\AjaxResponse;
use GibsonOS\Module\Transfer\Dto\ListItem;
use GibsonOS\Module\Transfer\Exception\ClientException;
use GibsonOS\Module\Transfer\Exception\QueueException;
use GibsonOS\Module\Transfer\Model\Session;
use GibsonOS\Module\Transfer\Service\ClientCryptService;
use GibsonOS\Module\Transfer\Service\ClientService;
use GibsonOS\Module\Transfer\Service\QueueService;
use GibsonOS\Module\Transfer\Store\DirListStore;
use GibsonOS\Module\Transfer\Store\DirStore;
use GibsonOS\Module\Transfer\Store\TransferStore;
use JsonException;
use ReflectionException;

class IndexController extends AbstractController
{
    /**
     * @throws FactoryError
     * @throws ClientException
     */
    #[CheckPermission([Permission::READ])]
    public function get(
        DirStore $dirStore,
        ClientService $clientService,
        ClientCryptService $clientCryptService,
        #[GetMappedModel(mapping: ['remoteUser' => 'user', 'remotePassword' => 'password', 'remotePath' => 'dir'])]
        Session $session,
    ): AjaxResponse {
        $client = $clientService->connect($session, $this->sessionService->getUserId());
        $dir = $session->getRemotePath();
        $encryptedPath = explode('/', $dir === null ? '' : mb_substr($dir, 0, -1));
        $decryptedPath = [];

        foreach ($encryptedPath as $item) {
            $decryptedPath[] = $clientCryptService->decryptDirName($item);
        }

        $dirStore
            ->setClient($client)
            ->setDir($dir ?? '')
        ;
        $list = $dirStore->getList();
        $client->disconnect();

        $metas = [
            'dirCount' => 0,
            'fileCount' => 0,
            'fileSize' => 0,
        ];

        foreach ($list as $item) {
            if ($item->getType() === ListItem::TYPE_DIR) {
                ++$metas['dirCount'];

                continue;
            }

            ++$metas['fileCount'];
            $metas['fileSize'] += $item->getSize();
        }

        return new AjaxResponse([
            'success' => true,
            'failure' => false,
            'data' => $list,
            'path' => $encryptedPath,
            'decryptedPath' => $decryptedPath,
            'metas' => $metas,
            'dir' => $dir ?? '/',
        ]);
    }

    /**
     * @throws ClientException
     * @throws FactoryError
     */
    #[CheckPermission([Permission::READ])]
    public function getList(
        ClientService $clientService,
        DirListStore $dirListStore,
        #[GetMappedModel(mapping: ['remoteUser' => 'user', 'remotePassword' => 'password', 'remotePath' => 'dir'])]
        Session $session,
        ?string $node = null,
    ): AjaxResponse {
        $client = $clientService->connect($session, $this->sessionService->getUserId());
        $loadParents = false;
        $dir = $session->getRemotePath();

        if ($node === 'root') {
            $loadParents = true;
        } elseif ($node !== null && $node !== '') {
            $dir = $node;
        }

        $dirListStore
            ->setClient($client)
            ->setDir($dir ?? '/')
            ->setLoadParents($loadParents)
        ;
        $list = $dirListStore->getList();
        $client->disconnect();

        return $this->returnSuccess($list);
    }

    /**
     * @throws ClientException
     * @throws FactoryError
     * @throws QueueException
     * @throws SaveError
     * @throws JsonException
     * @throws ReflectionException
     */
    #[CheckPermission([Permission::READ])]
    public function getDownload(
        ClientService $clientService,
        QueueService $queueService,
        string $localPath,
        string $dir,
        #[GetMappedModel(mapping: ['remoteUser' => 'user', 'remotePassword' => 'password', 'remotePath' => 'dir'])]
        Session $session,
        ?array $files = null,
        bool $overwriteAll = false,
        array $overwrite = [],
        bool $ignoreAll = false,
        array $ignore = [],
    ): AjaxResponse {
        $client = $clientService->connect($session, $this->sessionService->getUserId());

        $queueService->addDownload(
            $client,
            $localPath,
            $dir,
            $files,
            $overwriteAll,
            $overwrite,
            $ignoreAll,
            $ignore,
            $session->getId(),
            $session->getProtocol(),
            $session->getUrl(),
            $session->getPort(),
            $session->getRemoteUser(),
            $session->getRemotePassword(),
        );
        $client->disconnect();

        return $this->returnSuccess();
    }

    /**
     * @throws ClientException
     * @throws FactoryError
     * @throws GetError
     * @throws JsonException
     * @throws QueueException
     * @throws ReflectionException
     * @throws SaveError
     */
    #[CheckPermission([Permission::WRITE])]
    public function postUpload(
        ClientService $clientService,
        QueueService $queueService,
        string $remotePath,
        string $dir,
        #[GetMappedModel(mapping: ['remoteUser' => 'user', 'remotePassword' => 'password', 'localPath' => 'dir'])]
        Session $session,
        ?array $files = null,
        bool $overwriteAll = false,
        array $overwrite = [],
        bool $ignoreAll = false,
        array $ignore = [],
        bool $crypt = false,
    ): AjaxResponse {
        $client = $clientService->connect($session, $this->sessionService->getUserId());

        $queueService->addUpload(
            $client,
            $remotePath,
            $dir,
            $crypt,
            $files,
            $overwriteAll,
            $overwrite,
            $ignoreAll,
            $ignore,
            $session->getId(),
            $session->getProtocol(),
            $session->getUrl(),
            $session->getPort(),
            $session->getRemoteUser(),
            $session->getRemotePassword(),
        );
        $client->disconnect();

        return $this->returnSuccess();
    }

    /**
     * @throws JsonException
     * @throws ReflectionException
     * @throws SelectError
     */
    #[CheckPermission([Permission::READ])]
    public function getTransfer(
        TransferStore $transferStore,
        string $type,
        int $autoRefresh,
        int $limit = 0,
        int $start = 0,
    ): AjaxResponse {
        $transferStore
            ->setType($type)
            ->setLimit($limit, $start)
        ;

        return $this->returnSuccess($transferStore->getList(), $transferStore->getCount());
    }

    /**
     * @throws ClientException
     * @throws FactoryError
     */
    #[CheckPermission([Permission::WRITE])]
    public function postDir(
        ClientService $clientService,
        ClientCryptService $nameCryptService,
        string $dir,
        string $dirname,
        #[GetMappedModel(mapping: ['remoteUser' => 'user', 'remotePassword' => 'password', 'remotePath' => 'dir'])]
        Session $session,
        bool $crypt = false,
    ): AjaxResponse {
        $client = $clientService->connect($session, $this->sessionService->getUserId());
        $dir = $clientService->createDir(
            $client,
            $dir,
            $crypt ? $nameCryptService->encryptDirName($dirname) : $dirname,
            $dirname,
        );
        $client->disconnect();

        return $this->returnSuccess($dir);
    }

    /**
     * @throws ClientException
     * @throws FactoryError
     */
    #[CheckPermission([Permission::DELETE])]
    public function delete(
        ClientService $clientService,
        string $dir,
        #[GetMappedModel(mapping: ['remoteUser' => 'user', 'remotePassword' => 'password', 'remotePath' => 'dir'])]
        Session $session,
        ?array $files = null,
    ): AjaxResponse {
        $client = $clientService->connect($session, $this->sessionService->getUserId());
        $clientService->delete($client, $dir, $files);
        $client->disconnect();

        return $this->returnSuccess();
    }
}

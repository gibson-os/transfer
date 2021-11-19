<?php
declare(strict_types=1);

namespace GibsonOS\Module\Transfer\Controller;

use GibsonOS\Core\Attribute\CheckPermission;
use GibsonOS\Core\Controller\AbstractController;
use GibsonOS\Core\Exception\FactoryError;
use GibsonOS\Core\Exception\GetError;
use GibsonOS\Core\Exception\Model\SaveError;
use GibsonOS\Core\Exception\Repository\SelectError;
use GibsonOS\Core\Model\User\Permission;
use GibsonOS\Core\Service\Response\AjaxResponse;
use GibsonOS\Module\Transfer\Client\ClientInterface;
use GibsonOS\Module\Transfer\Dto\ListItem;
use GibsonOS\Module\Transfer\Exception\ClientException;
use GibsonOS\Module\Transfer\Service\ClientCryptService;
use GibsonOS\Module\Transfer\Service\ClientService;
use GibsonOS\Module\Transfer\Service\QueueService;
use GibsonOS\Module\Transfer\Store\DirListStore;
use GibsonOS\Module\Transfer\Store\DirStore;
use GibsonOS\Module\Transfer\Store\TransferStore;

class IndexController extends AbstractController
{
    /**
     * @param class-string<ClientInterface>|null $protocol
     *
     * @throws FactoryError
     * @throws ClientException
     * @throws SelectError
     */
    #[CheckPermission(Permission::READ)]
    public function read(
        DirStore $dirStore,
        ClientService $clientService,
        ClientCryptService $clientCryptService,
        int $id = null,
        string $protocol = null,
        string $url = null,
        int $port = null,
        string $user = null,
        string $password = null,
        string $dir = null
    ): AjaxResponse {
        $client = $clientService->connect($id, $protocol, $url, $port, $user, $password);

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
     * @param class-string<ClientInterface>|null $protocol
     *
     * @throws ClientException
     * @throws FactoryError
     * @throws SelectError
     */
    #[CheckPermission(Permission::READ)]
    public function dirList(
        ClientService $clientService,
        DirListStore $dirListStore,
        int $id = null,
        string $protocol = null,
        string $url = null,
        int $port = null,
        string $user = null,
        string $password = null,
        string $dir = null,
        string $node = null
    ): AjaxResponse {
        $client = $clientService->connect($id, $protocol, $url, $port, $user, $password);
        $loadParents = false;

        if ($node === 'root') {
            $loadParents = true;
        } elseif (!empty($node)) {
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
     * @param class-string<ClientInterface>|null $protocol
     *
     * @throws ClientException
     * @throws FactoryError
     * @throws SelectError
     * @throws SaveError
     */
    #[CheckPermission(Permission::READ)]
    public function download(
        ClientService $clientService,
        QueueService $queueService,
        string $localPath,
        string $dir,
        array $files = null,
        bool $overwrite = false,
        bool $ignore = false,
        int $id = null,
        string $protocol = null,
        string $url = null,
        int $port = null,
        string $user = null,
        string $password = null
    ): AjaxResponse {
        $client = $clientService->connect($id, $protocol, $url, $port, $user, $password);
        $queueService->addDownload(
            $client,
            $localPath,
            $dir,
            $files,
            $overwrite,
            $ignore,
            $id,
            $protocol,
            $url,
            $port,
            $user,
            $password
        );
        $client->disconnect();

        return $this->returnSuccess();
    }

    /**
     * @param class-string<ClientInterface>|null $protocol
     *
     * @throws ClientException
     * @throws FactoryError
     * @throws SaveError
     * @throws SelectError
     * @throws GetError
     */
    #[CheckPermission(Permission::WRITE)]
    public function upload(
        ClientService $clientService,
        QueueService $queueService,
        string $remotePath,
        string $dir,
        array $files = null,
        bool $overwrite = false,
        bool $ignore = false,
        bool $crypt = false,
        int $id = null,
        string $protocol = null,
        string $url = null,
        int $port = null,
        string $user = null,
        string $password = null
    ): AjaxResponse {
        $client = $clientService->connect($id, $protocol, $url, $port, $user, $password);
        $queueService->addUpload(
            $client,
            $remotePath,
            $dir,
            $crypt,
            $files,
            $overwrite,
            $ignore,
            $id,
            $protocol,
            $url,
            $port,
            $user,
            $password
        );
        $client->disconnect();

        return $this->returnSuccess();
    }

    /**
     * @throws SelectError
     */
    #[CheckPermission(Permission::READ)]
    public function transfer(
        TransferStore $transferStore,
        string $type,
        int $autoRefresh,
        int $limit = 0,
        int $start = 0
    ): AjaxResponse {
        $transferStore
            ->setType($type)
            ->setLimit($limit, $start)
        ;

        return $this->returnSuccess($transferStore->getList(), $transferStore->getCount());
    }

    /**
     * @param class-string<ClientInterface>|null $protocol
     *
     * @throws ClientException
     * @throws FactoryError
     * @throws SelectError
     */
    #[CheckPermission(Permission::WRITE)]
    public function addDir(
        ClientService $clientService,
        ClientCryptService $nameCryptService,
        string $dir,
        string $dirname,
        bool $crypt = false,
        int $id = null,
        string $protocol = null,
        string $url = null,
        int $port = null,
        string $user = null,
        string $password = null
    ): AjaxResponse {
        $client = $clientService->connect($id, $protocol, $url, $port, $user, $password);
        $dir = $clientService->createDir(
            $client,
            $dir,
            $crypt ? $nameCryptService->encryptDirName($dirname) : $dirname,
            $dirname
        );
        $client->disconnect();

        return $this->returnSuccess($dir);
    }

    /**
     * @param class-string<ClientInterface>|null $protocol
     *
     * @throws ClientException
     * @throws FactoryError
     * @throws SelectError
     */
    #[CheckPermission(Permission::DELETE)]
    public function delete(
        ClientService $clientService,
        string $dir,
        array $files = null,
        int $id = null,
        string $protocol = null,
        string $url = null,
        int $port = null,
        string $user = null,
        string $password = null,
    ): AjaxResponse {
        $client = $clientService->connect($id, $protocol, $url, $port, $user, $password);
        $clientService->delete($client, $dir, $files);
        $client->disconnect();

        return $this->returnSuccess();
    }
}

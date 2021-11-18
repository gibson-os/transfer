<?php
declare(strict_types=1);

namespace GibsonOS\Module\Transfer\Controller;

use GibsonOS\Core\Attribute\CheckPermission;
use GibsonOS\Core\Controller\AbstractController;
use GibsonOS\Core\Exception\FactoryError;
use GibsonOS\Core\Exception\Repository\SelectError;
use GibsonOS\Core\Model\User\Permission;
use GibsonOS\Core\Service\RequestService;
use GibsonOS\Core\Service\Response\AjaxResponse;
use GibsonOS\Core\Service\SessionService;
use GibsonOS\Core\Service\TwigService;
use GibsonOS\Module\Transfer\Client\ClientInterface;
use GibsonOS\Module\Transfer\Dto\ListItem;
use GibsonOS\Module\Transfer\Exception\ClientException;
use GibsonOS\Module\Transfer\Factory\ClientFactory;
use GibsonOS\Module\Transfer\Repository\SessionRepository;
use GibsonOS\Module\Transfer\Service\ClientService;
use GibsonOS\Module\Transfer\Service\QueueService;
use GibsonOS\Module\Transfer\Store\DirListStore;
use GibsonOS\Module\Transfer\Store\DirStore;
use GibsonOS\Module\Transfer\Store\TransferStore;

class IndexController extends AbstractController
{
    public function __construct(
        private SessionRepository $sessionRepository,
        private ClientFactory $clientFactory,
        RequestService $requestService,
        TwigService $twigService,
        SessionService $sessionService
    ) {
        parent::__construct($requestService, $twigService, $sessionService);
    }

    /**
     * @param class-string|null $protocol
     *
     * @throws FactoryError
     * @throws ClientException
     * @throws SelectError
     */
    #[CheckPermission(Permission::READ)]
    public function read(
        DirStore $dirStore,
        ClientService $clientService,
        int $id = null,
        string $protocol = null,
        string $url = null,
        int $port = null,
        string $user = null,
        string $password = null,
        string $dir = null
    ): AjaxResponse {
        $client = $this->connect($id, $protocol, $url, $port, $user, $password);

        $encryptedPath = explode('/', $dir === null ? '' : mb_substr($dir, 0, -1));
        $decryptedPath = [];

        foreach ($encryptedPath as $item) {
            $decryptedPath[] = $clientService->decryptDirName($item);
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
     * @param class-string|null $protocol
     *
     * @throws ClientException
     * @throws FactoryError
     * @throws SelectError
     */
    #[CheckPermission(Permission::READ)]
    public function dirList(
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
        $client = $this->connect($id, $protocol, $url, $port, $user, $password);
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
     * @param class-string|null $protocol
     *
     * @throws ClientException
     * @throws FactoryError
     * @throws SelectError
     */
    #[CheckPermission(Permission::READ)]
    public function download(
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
        $client = $this->connect($id, $protocol, $url, $port, $user, $password);
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

        return $this->returnSuccess();
    }

    #[CheckPermission(Permission::WRITE)]
    public function upload(
        string $remotePath,
        string $dir,
        array $files = null,
        bool $overwrite = false,
        bool $ignore = false,
        bool $crypt = false
    ): AjaxResponse {
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
     * @param class-string|null $protocol
     *
     * @throws ClientException
     * @throws FactoryError
     * @throws SelectError
     */
    #[CheckPermission(Permission::WRITE)]
    public function addDir(
        ClientService $clientService,
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
        $client = $this->connect($id, $protocol, $url, $port, $user, $password);
        $dir = $clientService->createDir($client, $dir, $dirname, $crypt);
        $client->disconnect();

        return $this->returnSuccess($dir);
    }

    /**
     * @param class-string|null $protocol
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
        $client = $this->connect($id, $protocol, $url, $port, $user, $password);
        $clientService->delete($client, $dir, $files);

        return $this->returnSuccess();
    }

    /**
     * @param class-string|null $protocol
     *
     * @throws ClientException
     * @throws FactoryError
     * @throws SelectError
     */
    private function connect(
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
            $address = $session->getAddress();
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

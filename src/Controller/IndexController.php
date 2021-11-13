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
use GibsonOS\Module\Transfer\Exception\ClientException;
use GibsonOS\Module\Transfer\Factory\ClientFactory;
use GibsonOS\Module\Transfer\Repository\SessionRepository;
use GibsonOS\Module\Transfer\Store\DirStore;

class IndexController extends AbstractController
{
    public function __construct(
        private SessionRepository $sessionRepository,
        RequestService $requestService,
        TwigService $twigService,
        SessionService $sessionService
    ) {
        parent::__construct($requestService, $twigService, $sessionService);
    }

    /**
     * @param class-string|null $protocol
     *
     * @throws SelectError
     * @throws FactoryError
     * @throws ClientException
     */
    #[CheckPermission(Permission::READ)]
    public function read(
        SessionRepository $sessionRepository,
        ClientFactory $clientFactory,
        DirStore $dirStore,
        int $id = null,
        string $protocol = null,
        string $url = null,
        int $port = null,
        string $user = null,
        string $password = null,
        string $dir = null
    ): AjaxResponse {
        if ($id !== null) {
            $session = $sessionRepository->getById($id);
            $protocol = $session->getProtocol();
            $url = $session->getAddress();
            $port = $session->getPort();
            $user = $session->getRemoteUser();
            $password = $session->getRemotePassword();

            if ($dir === null) {
                $dir = $session->getRemotePath();
            }
        }

        if ($protocol === null) {
            return $this->returnFailure('Kein Protokoll angegeben');
        }

        $client = $clientFactory->get($protocol);
        $client->connect($url ?? '', $user, $password, $port);
        $dirStore
            ->setClient($client)
            ->setDir($dir ?? '')
        ;
        $list = $dirStore->getList();
        $client->disconnect();

        return $this->returnSuccess($list);
    }

    #[CheckPermission(Permission::READ)]
    public function dirList(
        int $id = null,
        string $protocol = null,
        string $url = null,
        int $port = null,
        string $user = null,
        string $password = null,
        string $node = null
    ): AjaxResponse {
        return $this->returnSuccess();
    }

    #[CheckPermission(Permission::READ)]
    public function download(
        string $localPath,
        string $dir,
        array $files = null,
        bool $overwrite = false,
        bool $ignore = false
    ): AjaxResponse {
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

    #[CheckPermission(Permission::READ)]
    public function transfer(string $type, int $autoRefresh, int $limit = null, int $start = null): AjaxResponse
    {
        return $this->returnSuccess();
    }

    #[CheckPermission(Permission::WRITE)]
    public function addDir(string $dir, string $dirname, bool $crypt = false): AjaxResponse
    {
        return $this->returnSuccess();
    }

    #[CheckPermission(Permission::DELETE)]
    public function delete(string $dir, array $files = null): AjaxResponse
    {
        return $this->returnSuccess();
    }

//    /**
//     * @throws RequestError
//     * @throws SelectError
//     */
//    private function connect(): ClientInterface
//    {
//        try {
//            $session = $this->sessionRepository->getById($this->requestService->getRequestValue('id'));
//            $address = $session->getAddress();
//            $protocol = $session->getProtocol();
//            $port = $session->getPort();
//            $user = $session->getRemoteUser();
//            $password = $session->getRemotePassword();
//        } catch (RequestError) {
//            $address = $this->requestService->getRequestValue('address');
//            $protocol = $this->requestService->getRequestValue('protocol');
//
//            try {
//                $port = $this->requestService->getRequestValue('port');
//            } catch (RequestError) {
//                $port = null;
//            }
//
//            try {
//                $user = $this->requestService->getRequestValue('user');
//            } catch (RequestError) {
//                $user = null;
//            }
//
//            try {
//                $password = $this->requestService->getRequestValue('password');
//            } catch (RequestError) {
//                $password = null;
//            }
//        }
//
//        // @todo service holen. factory bauen
//    }
}

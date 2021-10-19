<?php
declare(strict_types=1);

namespace GibsonOS\Module\Transfer\Controller;

use GibsonOS\Core\Controller\AbstractController;
use GibsonOS\Core\Exception\DateTimeError;
use GibsonOS\Core\Exception\LoginRequired;
use GibsonOS\Core\Exception\PermissionDenied;
use GibsonOS\Core\Exception\Repository\SelectError;
use GibsonOS\Core\Exception\RequestError;
use GibsonOS\Core\Service\PermissionService;
use GibsonOS\Core\Service\RequestService;
use GibsonOS\Core\Service\Response\AjaxResponse;
use GibsonOS\Core\Service\SessionService;
use GibsonOS\Core\Service\TwigService;
use GibsonOS\Module\Transfer\Repository\SessionRepository;
use GibsonOS\Module\Transfer\Service\ClientInterface;

class IndexController extends AbstractController
{
    public function __construct(
        private SessionRepository $sessionRepository,
        PermissionService $permissionService,
        RequestService $requestService,
        TwigService $twigService,
        SessionService $sessionService
    ) {
        parent::__construct($permissionService, $requestService, $twigService, $sessionService);
    }

    /**
     * @throws LoginRequired
     * @throws PermissionDenied
     */
    public function read(?string $dir = ''): AjaxResponse
    {
        $this->checkPermission(PermissionService::READ);

        return $this->returnSuccess();
    }

    /**
     * @throws LoginRequired
     * @throws PermissionDenied
     */
    public function dirList(?string $node = ''): AjaxResponse
    {
        $this->checkPermission(PermissionService::READ);

        return $this->returnSuccess();
    }

    /**
     * @throws LoginRequired
     * @throws PermissionDenied
     */
    public function download(
        string $localPath,
        string $dir,
        ?array $files = null,
        bool $overwrite = false,
        bool $ignore = false
    ): AjaxResponse {
        $this->checkPermission(PermissionService::READ);

        return $this->returnSuccess();
    }

    /**
     * @throws LoginRequired
     * @throws PermissionDenied
     */
    public function upload(
        string $remotePath,
        string $dir,
        array $files = null,
        bool $overwrite = false,
        bool $ignore = false,
        bool $crypt = false
    ): AjaxResponse {
        $this->checkPermission(PermissionService::WRITE);

        return $this->returnSuccess();
    }

    /**
     * @throws LoginRequired
     * @throws PermissionDenied
     */
    public function transfer(string $type, int $autoRefresh, int $limit = null, int $start = null): AjaxResponse
    {
        $this->checkPermission(PermissionService::READ);

        return $this->returnSuccess();
    }

    /**
     * @throws LoginRequired
     * @throws PermissionDenied
     */
    public function addDir(string $dir, string $dirname, bool $crypt = false): AjaxResponse
    {
        $this->checkPermission(PermissionService::WRITE);

        return $this->returnSuccess();
    }

    /**
     * @throws LoginRequired
     * @throws PermissionDenied
     */
    public function delete(string $dir, array $files = null): AjaxResponse
    {
        $this->checkPermission(PermissionService::DELETE);

        return $this->returnSuccess();
    }

    /**
     * @throws RequestError
     * @throws DateTimeError
     * @throws SelectError
     */
    private function connect(): ClientInterface
    {
        try {
            $session = $this->sessionRepository->getById($this->requestService->getRequestValue('id'));
            $address = $session->getAddress();
            $protocol = $session->getProtocol();
            $port = $session->getPort();
            $user = $session->getRemoteUser();
            $password = $session->getRemotePassword();
        } catch (RequestError) {
            $address = $this->requestService->getRequestValue('address');
            $protocol = $this->requestService->getRequestValue('protocol');

            try {
                $port = $this->requestService->getRequestValue('port');
            } catch (RequestError) {
                $port = null;
            }

            try {
                $user = $this->requestService->getRequestValue('user');
            } catch (RequestError) {
                $user = null;
            }

            try {
                $password = $this->requestService->getRequestValue('password');
            } catch (RequestError) {
                $password = null;
            }
        }

        // @todo service holen. factory bauen
    }
}

<?php
declare(strict_types=1);

namespace GibsonOS\Module\Transfer\Controller;

use GibsonOS\Core\Attribute\CheckPermission;
use GibsonOS\Core\Attribute\GetModel;
use GibsonOS\Core\Controller\AbstractController;
use GibsonOS\Core\Exception\FactoryError;
use GibsonOS\Core\Exception\Model\DeleteError;
use GibsonOS\Core\Exception\Model\SaveError;
use GibsonOS\Core\Exception\Repository\SelectError;
use GibsonOS\Core\Model\User\Permission;
use GibsonOS\Core\Service\CryptService;
use GibsonOS\Core\Service\PermissionService;
use GibsonOS\Core\Service\Response\AjaxResponse;
use GibsonOS\Module\Transfer\Factory\ClientFactory;
use GibsonOS\Module\Transfer\Model\Session;
use GibsonOS\Module\Transfer\Store\SessionStore;

class SessionController extends AbstractController
{
    /**
     * @throws SelectError
     */
    #[CheckPermission(Permission::READ)]
    public function index(
        SessionStore $sessionStore,
        PermissionService $permissionService,
        int $userPermission
    ): AjaxResponse {
        $userId = $this->sessionService->getUserId();

        if ($permissionService->checkPermission(Permission::MANAGE, $userPermission)) {
            $userId = null;
        }

        $sessionStore->setUserId($userId);

        return $this->returnSuccess($sessionStore->getList(), $sessionStore->getCount());
    }

    /**
     * @param class-string $clientClass
     *
     * @throws FactoryError
     * @throws SaveError
     */
    #[CheckPermission(Permission::WRITE)]
    public function save(
        ClientFactory $clientFactory,
        CryptService $cryptService,
        PermissionService $permissionService,
        int $userPermission,
        string $name,
        string $clientClass,
        string $url,
        int $port = null,
        string $user = null,
        string $password = null,
        string $localPath = null,
        string $remotePath = null,
        bool $onlyForThisUser = false,
        #[GetModel] Session $session = null
    ): AjaxResponse {
        if ($session !== null) {
            if (
                $session->getUserId() === null &&
                !$permissionService->checkPermission(Permission::MANAGE, $userPermission)
            ) {
                return $this->returnFailure(sprintf(
                    'Keine Berechtigung um die Session %s zu bearbeiten!',
                    $session->getName()
                ));
            }
        } else {
            $session = new Session();
        }

        if ($port === null) {
            $client = $clientFactory->get($clientClass);
            $port = $client->getDefaultPort();
        }

        $session
            ->setName($name)
            ->setProtocol($clientClass)
            ->setUrl($url)
            ->setPort($port)
            ->setLocalPath($localPath)
            ->setRemotePath($remotePath)
            ->setUserId($onlyForThisUser ? $this->sessionService->getUserId() : null)
        ;

        if ($user !== null) {
            $session->setRemoteUser($cryptService->encrypt($user));
        }

        if ($password !== null) {
            $session->setRemotePassword($cryptService->encrypt($password));
        }

        $session->save();

        return $this->returnSuccess($session);
    }

    /**
     * @throws DeleteError
     */
    #[CheckPermission(Permission::DELETE)]
    public function delete(
        PermissionService $permissionService,
        int $userPermission,
        #[GetModel] Session $session
    ): AjaxResponse {
        if (
            ($session->getUserId() !== $this->sessionService->getUserId()) &&
            !$permissionService->checkPermission(Permission::MANAGE, $userPermission)
        ) {
            return $this->returnFailure(sprintf(
                'Keine Berechtigung um die Session %s zu löschen!',
                $session->getName()
            ));
        }

        $session->delete();

        return $this->returnSuccess();
    }
}

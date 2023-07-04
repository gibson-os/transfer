<?php
declare(strict_types=1);

namespace GibsonOS\Module\Transfer\Controller;

use GibsonOS\Core\Attribute\CheckPermission;
use GibsonOS\Core\Attribute\GetMappedModel;
use GibsonOS\Core\Attribute\GetModel;
use GibsonOS\Core\Controller\AbstractController;
use GibsonOS\Core\Enum\Permission;
use GibsonOS\Core\Exception\FactoryError;
use GibsonOS\Core\Exception\Model\SaveError;
use GibsonOS\Core\Exception\Repository\SelectError;
use GibsonOS\Core\Manager\ModelManager;
use GibsonOS\Core\Service\CryptService;
use GibsonOS\Core\Service\PermissionService;
use GibsonOS\Core\Service\Response\AjaxResponse;
use GibsonOS\Module\Transfer\Factory\ClientFactory;
use GibsonOS\Module\Transfer\Model\Session;
use GibsonOS\Module\Transfer\Store\SessionStore;
use JsonException;
use ReflectionException;

class SessionController extends AbstractController
{
    /**
     * @throws JsonException
     * @throws ReflectionException
     * @throws SelectError
     */
    #[CheckPermission([Permission::READ])]
    public function get(
        SessionStore $sessionStore,
        PermissionService $permissionService,
        int $userPermission
    ): AjaxResponse {
        $userId = $this->sessionService->getUserId();

        if ($permissionService->checkPermission(Permission::MANAGE->value, $userPermission)) {
            $userId = null;
        }

        $sessionStore->setUserId($userId);

        return $this->returnSuccess($sessionStore->getList(), $sessionStore->getCount());
    }

    /**
     * @throws FactoryError
     * @throws SaveError
     * @throws JsonException
     * @throws ReflectionException
     */
    #[CheckPermission([Permission::WRITE])]
    public function post(
        ClientFactory $clientFactory,
        CryptService $cryptService,
        PermissionService $permissionService,
        ModelManager $modelManager,
        int $userPermission,
        #[GetMappedModel(mapping: ['remoteUser' => 'user', 'remotePassword' => 'password', 'protocol' => 'clientClass'])] Session $session,
        string $user = null,
        string $password = null,
        int $port = null,
        bool $onlyForThisUser = false
    ): AjaxResponse {
        $userId = $session->getUserId();

        if (
            ($userId === null && !$permissionService->checkPermission(Permission::MANAGE->value, $userPermission))
            || ($userId !== null && $userId !== $this->sessionService->getUserId())
        ) {
            return $this->returnFailure(sprintf(
                'Keine Berechtigung um die Session %s zu bearbeiten!',
                $session->getName()
            ));
        }

        if ($port === null) {
            $client = $clientFactory->get($session->getProtocol());
            $session->setPort($client->getDefaultPort());
        }

        $session->setUserId($onlyForThisUser ? $this->sessionService->getUserId() : null);

        if ($user !== null) {
            $session->setRemoteUser($cryptService->encrypt($user));
        }

        if ($password !== null) {
            $session->setRemotePassword($cryptService->encrypt($password));
        }

        $modelManager->save($session);

        return $this->returnSuccess($session);
    }

    /**
     * @throws JsonException
     * @throws ReflectionException
     * @throws SaveError
     */
    #[CheckPermission([Permission::DELETE])]
    public function delete(
        PermissionService $permissionService,
        ModelManager $modelManager,
        int $userPermission,
        #[GetModel] Session $session
    ): AjaxResponse {
        if (
            ($session->getUserId() !== $this->sessionService->getUserId())
            && !$permissionService->checkPermission(Permission::MANAGE->value, $userPermission)
        ) {
            return $this->returnFailure(sprintf(
                'Keine Berechtigung um die Session %s zu löschen!',
                $session->getName()
            ));
        }

        $modelManager->save($session);

        return $this->returnSuccess();
    }
}

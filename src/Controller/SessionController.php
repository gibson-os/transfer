<?php
declare(strict_types=1);

namespace GibsonOS\Module\Transfer\Controller;

use GibsonOS\Core\Attribute\CheckPermission;
use GibsonOS\Core\Controller\AbstractController;
use GibsonOS\Core\Exception\Model\DeleteError;
use GibsonOS\Core\Exception\Repository\SelectError;
use GibsonOS\Core\Model\User\Permission;
use GibsonOS\Core\Service\PermissionService;
use GibsonOS\Core\Service\Response\AjaxResponse;
use GibsonOS\Module\Transfer\Repository\SessionRepository;
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

    #[CheckPermission(Permission::WRITE)]
    public function save(): AjaxResponse
    {
        return $this->returnSuccess();
    }

    #[CheckPermission(Permission::WRITE)]
    public function saveAfterAuthentication(): AjaxResponse
    {
        return $this->returnSuccess();
    }

    /**
     * @throws SelectError
     * @throws DeleteError
     */
    #[CheckPermission(Permission::DELETE)]
    public function delete(
        SessionRepository $sessionRepository,
        PermissionService $permissionService,
        int $userPermission,
        int $id
    ): AjaxResponse {
        $session = $sessionRepository->getById($id);
        $userId = $session->getUserId();

        if (
            $userId !== null &&
            $userId !== $this->sessionService->getUserId() &&
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

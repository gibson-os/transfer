<?php
declare(strict_types=1);

namespace GibsonOS\Module\Transfer\Controller;

use GibsonOS\Core\Controller\AbstractController;
use GibsonOS\Core\Exception\LoginRequired;
use GibsonOS\Core\Exception\PermissionDenied;
use GibsonOS\Core\Service\PermissionService;
use GibsonOS\Core\Service\Response\AjaxResponse;

class IndexController extends AbstractController
{
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
}

<?php
declare(strict_types=1);

namespace GibsonOS\Module\Transfer\Install;

use GibsonOS\Core\Exception\InstallException;
use GibsonOS\Core\Service\Install\RequiredExtensionInterface;

class BaseInstall implements RequiredExtensionInterface
{
    /**
     * @throws InstallException
     */
    public function checkRequiredExtensions(): void
    {
        if (!function_exists('ssh2_connect')) {
            throw new InstallException('Please install PHP SSH2 extension!');
        }

        if (!function_exists('ssh2_sftp')) {
            throw new InstallException('Please install PHP SFTP extension!');
        }
    }
}

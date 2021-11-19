<?php
declare(strict_types=1);

namespace GibsonOS\Module\Transfer\Service;

use GibsonOS\Core\Exception\FileNotFound;
use GibsonOS\Core\Service\CryptService;

class ClientCryptService
{
    private const FILE_PREFIX = 'transferCryptFile';

    private const FILE_EXTENSION = '.gcf';

    private const DIR_EXTENSION = '.gcd';

    public function __construct(private CryptService $cryptService)
    {
    }

    public function encryptDirName(string $dirName): string
    {
        return str_replace(
            '/',
            '_',
            base64_encode(gzcompress($this->cryptService->encrypt($dirName), 9) ?: '')
        ) . self::DIR_EXTENSION;
    }

    public function encryptFileName(string $fileName): string
    {
        return str_replace(
            '/',
            '_',
            base64_encode(gzcompress($this->cryptService->encrypt($fileName), 9) ?: '')
        ) . self::FILE_EXTENSION;
    }

    public function decryptDirName(string $dirName): string
    {
        if (mb_strpos($dirName, self::DIR_EXTENSION) === false) {
            return $dirName;
        }

        $dirName = str_replace(self::DIR_EXTENSION, '', $dirName);
        $strReplace = str_replace('_', '/', $dirName);

        return $this->cryptService->decrypt(
            gzuncompress(base64_decode($strReplace) ?: '')
        );
    }

    public function decryptFileName(string $dirName): string
    {
        if (mb_strpos($dirName, self::FILE_EXTENSION) === false) {
            return $dirName;
        }

        $dirName = str_replace(self::FILE_EXTENSION, '', $dirName);
        $strReplace = str_replace('_', '/', $dirName);

        return $this->cryptService->decrypt(
            gzuncompress(base64_decode($strReplace) ?: '')
        );
    }

    /**
     * @throws FileNotFound
     */
    public function encryptFile(string $localPath): string
    {
        $tmpPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . self::FILE_PREFIX . uniqid('', true);
        $this->cryptService->encryptFile($localPath, $tmpPath);

        return $tmpPath;
    }

    /**
     * @throws FileNotFound
     */
    public function decryptFile(string $path): void
    {
        $tmpPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . self::FILE_PREFIX . uniqid('', true);
        $this->cryptService->decryptFile($path, $tmpPath);
        unlink($path);
        rename($tmpPath, $path);
    }
}

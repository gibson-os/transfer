<?php
declare(strict_types=1);

namespace GibsonOS\Module\Transfer\Service;

use GibsonOS\Core\Service\CryptService;

class ClientService
{
    public function __construct(private CryptService $cryptService)
    {
    }

    public function encryptDirName(string $dirName): string
    {
        return str_replace(
            '/',
            '_',
            base64_encode(gzcompress($this->cryptService->encrypt($dirName), 9) ?: '')
        ) . '.gcd';
    }

    public function decryptDirName(string $dirName): string
    {
        if (mb_strpos($dirName, '.gcd') === false) {
            return $dirName;
        }

        $dirName = str_replace('.gcd', '', $dirName);
        $strReplace = str_replace('_', '/', $dirName);

        return $this->cryptService->decrypt(
            gzuncompress(base64_decode(is_array($strReplace) ? '' : $strReplace) ?: '')
        );
    }
}

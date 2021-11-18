<?php
declare(strict_types=1);

namespace GibsonOS\Module\Transfer\Service;

use GibsonOS\Core\Service\CryptService;
use GibsonOS\Core\Service\DateTimeService;
use GibsonOS\Core\Service\DirService;
use GibsonOS\Module\Transfer\Client\ClientInterface;
use GibsonOS\Module\Transfer\Dto\ListItem;
use GibsonOS\Module\Transfer\Exception\ClientException;

class ClientService
{
    public function __construct(
        private CryptService $cryptService,
        private DirService $dirService,
        private DateTimeService $dateTimeService
    ) {
    }

    /**
     * @throws ClientException
     */
    public function getList(ClientInterface $client, string $dir, bool $withFiles): array
    {
        $dirs = [];
        $files = [];

        foreach ($client->getList($dir) as $item) {
            if ($item->getType() === ListItem::TYPE_DIR) {
                $dirs[$item->getDecryptedName()] = $item;

                continue;
            }

            if ($withFiles) {
                $files[$item->getDecryptedName()] = $item;
            }
        }

        ksort($dirs);
        ksort($files);

        return array_values($dirs) + array_values($files);
    }

    /**
     * @throws ClientException
     */
    public function createDir(ClientInterface $client, string $dir, string $name, bool $crypt): ListItem
    {
        $encryptedName = $crypt ? $this->encryptDirName($name) : $name;
        $dir = $this->dirService->addEndSlash($dir, '/');
        $client->createDir($dir . $encryptedName);

        return new ListItem(
            $encryptedName,
            $name,
            $dir,
            $this->dateTimeService->get(),
            0,
            ListItem::TYPE_DIR
        );
    }

    /**
     * @throws ClientException
     */
    public function delete(ClientInterface $client, string $dir, array $files = null): void
    {
        $dir = $this->dirService->addEndSlash($dir, '/');

        foreach ($client->getList($dir) as $item) {
            if ($files !== null && !in_array($item->getName(), $files)) {
                continue;
            }

            $path = $item->getDir() . $item->getName();

            if ($item->getType() === ListItem::TYPE_DIR) {
                $this->delete($client, $path);
                $client->deleteDir($path);

                continue;
            }

            $client->deleteFile($path);
        }
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
            gzuncompress(base64_decode($strReplace) ?: '')
        );
    }
}

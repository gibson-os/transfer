<?php
declare(strict_types=1);

namespace GibsonOS\Module\Transfer\Client;

use GibsonOS\Module\Transfer\Dto\ListItem;
use GibsonOS\Module\Transfer\Exception\ClientException;

interface ClientInterface
{
    /**
     * @throws ClientException
     */
    public function connect(string $address, string $user = null, string $password = null, int $port = null): void;

    /**
     * @throws ClientException
     */
    public function disconnect(): void;

    /**
     * @throws ClientException
     */
    public function deleteFile(string $path): void;

    public function deleteDir(string $path): void;

    public function createDir(string $path): void;

    public function get(string $remotePath, string $localPath): void;

    public function put(string $localPath, string $remotePath): void;

    public function isDir(string $path): bool;

    public function fileExists(string $path): bool;

    public function fileSize(string $path): int;

    /**
     * @return ListItem[]
     */
    public function getList(string $dir): array;

    public function openDir(string $dir): void;
}

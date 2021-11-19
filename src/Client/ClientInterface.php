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

    /**
     * @throws ClientException
     */
    public function deleteDir(string $path): void;

    /**
     * @throws ClientException
     */
    public function createDir(string $path): void;

    /**
     * @throws ClientException
     */
    public function get(string $remotePath, string $localPath): void;

    /**
     * @throws ClientException
     */
    public function put(string $localPath, string $remotePath): void;

    /**
     * @throws ClientException
     */
    public function isDir(string $path): bool;

    /**
     * @throws ClientException
     */
    public function fileExists(string $path): bool;

    /**
     * @throws ClientException
     */
    public function fileSize(string $path): int;

    /**
     * @throws ClientException
     *
     * @return ListItem[]
     */
    public function getList(string $dir): array;

    public function getDefaultPort(): int;
}

<?php
declare(strict_types=1);

namespace GibsonOS\Module\Transfer\Client;

class FtpClient implements ClientInterface
{
    public function connect(string $address, string $user = null, string $password = null, int $port = null): void
    {
        // TODO: Implement connect() method.
    }

    public function disconnect(): void
    {
        // TODO: Implement disconnect() method.
    }

    public function deleteFile(string $path): void
    {
        // TODO: Implement deleteFile() method.
    }

    public function deleteDir(string $path): void
    {
        // TODO: Implement deleteDir() method.
    }

    public function createDir(string $path): void
    {
        // TODO: Implement createDir() method.
    }

    public function get(string $remotePath, string $localPath): void
    {
        // TODO: Implement get() method.
    }

    public function put(string $localPath, string $remotePath): void
    {
        // TODO: Implement put() method.
    }

    public function isDir(string $path): bool
    {
        return false;
    }

    public function fileExists(string $path): bool
    {
        return false;
    }

    public function fileSize(string $path): int
    {
        return 0;
    }

    public function getList(string $dir): array
    {
        return [];
    }

    public function openDir(string $dir): void
    {
        // TODO: Implement openDir() method.
    }
}
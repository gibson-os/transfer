<?php
declare(strict_types=1);

namespace GibsonOS\Module\Transfer\Service;

interface TransferInterface
{
    public function connect(string $address, string $user = null, string $password = null, int $port = null): void;

    public function disconnect(): void;

    public function deleteFile(string $path): void;

    public function deleteDir(string $path): void;

    public function createDir(string $path): void;

    public function get(string $remotePath, string $localPath): void;

    public function put(string $localPath, string $remotePath): void;

    public function isDir(string $path): bool;

    public function fileExists(string $path): bool;

    public function fileSize(string $path): int;

    public function getList(string $dir): array;

    public function openDir(string $dir): void;
}

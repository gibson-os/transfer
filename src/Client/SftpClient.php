<?php
declare(strict_types=1);

namespace GibsonOS\Module\Transfer\Client;

use GibsonOS\Module\Transfer\Exception\ClientException;

class SftpClient implements ClientInterface
{
    /**
     * @var resource|null
     */
    private $connection;

    /**
     * @var resource|null
     */
    private $sftpConnection;

    public function connect(string $address, string $user = null, string $password = null, int $port = null): void
    {
        if ($this->connection !== null) {
            throw new ClientException('SSH2 already connected!');
        }

        if ($port === null) {
            $port = 21;
        }

        $connection = ssh2_connect($address, $port);

        if ($user === null) {
            throw new ClientException('SSH2 username not set!');
        }

        if ($password === null) {
            throw new ClientException('SSH2 password not set!');
        }

        if ($connection === false) {
            throw new ClientException('SSH2 connection error!');
        }

        if (!ssh2_auth_password($connection, $user, $password)) {
            throw new ClientException('SSH2 authentication error!');
        }

        $this->sftpConnection = ssh2_sftp($connection);
        $this->connection = $connection;
    }

    public function disconnect(): void
    {
        if ($this->connection === null) {
            throw new ClientException('SSH2 not connected!');
        }

        if (!ssh2_disconnect($this->connection)) {
            throw new ClientException('SSH2 disconnection error!');
        }

        $this->connection = null;
        $this->sftpConnection = null;
    }

    public function deleteFile(string $path): void
    {
        if ($this->sftpConnection === null) {
            throw new ClientException('SSH2 not connected!');
        }

        if (!ssh2_sftp_unlink($this->sftpConnection, $path)) {
            throw new ClientException(sprintf('SSH2 file %s could not be deleted!', $path));
        }
    }

    public function deleteDir(string $path): void
    {
        if ($this->sftpConnection === null) {
            throw new ClientException('SSH2 not connected!');
        }

        if (!ssh2_sftp_rmdir($this->sftpConnection, $path)) {
            throw new ClientException(sprintf('SSH2 directory %s could not be deleted!', $path));
        }
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

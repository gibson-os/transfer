<?php
declare(strict_types=1);

namespace GibsonOS\Module\Transfer\Client;

use GibsonOS\Core\Service\DateTimeService;
use GibsonOS\Core\Service\FileService;
use GibsonOS\Module\Transfer\Dto\ListItem;
use GibsonOS\Module\Transfer\Exception\ClientException;
use GibsonOS\Module\Transfer\Service\ClientCryptService;
use Psr\Log\LoggerInterface;

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

    public function __construct(
        private DateTimeService $dateTimeService,
        private ClientCryptService $clientCryptService,
        private FileService $fileService,
        private LoggerInterface $logger,
    ) {
    }

    public function connect(string $address, ?string $user = null, ?string $password = null, ?int $port = null): void
    {
        if ($this->connection !== null) {
            throw new ClientException('SSH2 already connected!');
        }

        if ($port === null) {
            $port = 22;
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

        $this->logger->info('Connect SSH2');

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

        $this->logger->info('Disconnect SSH2');

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

        $this->logger->info('Delete SSH2 file');
    }

    public function deleteDir(string $path): void
    {
        if ($this->sftpConnection === null) {
            throw new ClientException('SSH2 not connected!');
        }

        if (!ssh2_sftp_rmdir($this->sftpConnection, $path)) {
            throw new ClientException(sprintf('SSH2 directory %s could not be deleted!', $path));
        }

        $this->logger->info('Delete SSH2 dir');
    }

    public function createDir(string $path): void
    {
        if ($this->sftpConnection === null) {
            throw new ClientException('SSH2 not connected!');
        }

        if (!ssh2_sftp_mkdir($this->sftpConnection, $path)) {
            throw new ClientException(sprintf('SSH2 directory %s could not be created!', $path));
        }

        $this->logger->info('Create SSH2 dir');
    }

    public function get(string $remotePath, string $localPath): void
    {
        if ($this->connection === null) {
            throw new ClientException('SSH2 not connected!');
        }

        $this->logger->info(sprintf('Get file %s to %s', $remotePath, $localPath));

        if (!ssh2_scp_recv($this->connection, $remotePath, $localPath)) {
            throw new ClientException(sprintf('SSH2 file %s could not be saved on %s!', $remotePath, $localPath));
        }
    }

    public function put(string $localPath, string $remotePath): void
    {
        if ($this->connection === null) {
            throw new ClientException('SSH2 not connected!');
        }

        $this->logger->info(sprintf('Put file %s to %s', $localPath, $remotePath));

        if (!ssh2_scp_send($this->connection, $localPath, $remotePath)) {
            throw new ClientException(sprintf('SSH2 file %s could not be saved on %s!', $localPath, $remotePath));
        }
    }

    public function isDir(string $path): bool
    {
        if ($this->sftpConnection === null) {
            throw new ClientException('SSH2 not connected!');
        }

        return is_dir($this->getSftpProtocolString() . $path);
    }

    public function fileExists(string $path): bool
    {
        if ($this->sftpConnection === null) {
            throw new ClientException('SSH2 not connected!');
        }

        return file_exists($this->getSftpProtocolString() . $path);
    }

    public function fileSize(string $path): int
    {
        if ($this->sftpConnection === null) {
            throw new ClientException('SSH2 not connected!');
        }

        return filesize($this->getSftpProtocolString() . $path);
    }

    public function getList(string $dir): array
    {
        if ($this->sftpConnection === null) {
            throw new ClientException('SSH2 not connected!');
        }

        if (empty($dir)) {
            $dir = '/';
        }

        $dirResource = opendir($this->getSftpProtocolString() . $dir);

        if (!is_resource($dirResource)) {
            throw new ClientException(sprintf('Directory %s could not be opened!', $dir));
        }

        $list = [];

        while ($item = readdir($dirResource)) {
            if (
                $item == '.'
                || $item == '..'
            ) {
                continue;
            }

            $stats = ssh2_sftp_stat($this->sftpConnection, $dir . $item);
            $mode = $stats['mode'];

            $list[$item] = new ListItem(
                $item,
                $this->isDir($dir . $item)
                    ? $this->clientCryptService->decryptDirName($item)
                    : $this->clientCryptService->decryptFileName($item),
                $dir,
                $this->dateTimeService->get('@' . $stats['mtime']),
                $stats['size'],
                $this->isDir($dir . $item) ? ListItem::TYPE_DIR : $this->fileService->getFileEnding($item),
                new ListItem\Permission(
                    (bool) ($mode & 0x0100),
                    (bool) ($mode & 0x0080),
                    ($mode & 0x0040) && !($mode & 0x0800),
                    (string) $stats['uid'],
                ),
                new ListItem\Permission(
                    (bool) ($mode & 0x0020),
                    (bool) ($mode & 0x0010),
                    ($mode & 0x0008) && !($mode & 0x0400),
                    (string) $stats['gid'],
                ),
                new ListItem\Permission(
                    (bool) ($mode & 0x0004),
                    (bool) ($mode & 0x0002),
                    ($mode & 0x0001) && !($mode & 0x0200),
                ),
            );
        }

        closedir($dirResource);
        ksort($list);

        return array_values($list);
    }

    private function getSftpProtocolString(): string
    {
        /**
         * @psalm-suppress InvalidOperand
         */
        return 'ssh2.sftp://' . $this->sftpConnection;
    }

    public function getDefaultPort(): int
    {
        return 22;
    }
}

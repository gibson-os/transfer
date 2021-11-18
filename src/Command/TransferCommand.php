<?php
declare(strict_types=1);

namespace GibsonOS\Module\Transfer\Command;

use GibsonOS\Core\Command\AbstractCommand;
use GibsonOS\Core\Exception\FactoryError;
use GibsonOS\Core\Exception\Model\SaveError;
use GibsonOS\Core\Exception\Repository\SelectError;
use GibsonOS\Core\Service\CryptService;
use GibsonOS\Module\Transfer\Exception\ClientException;
use GibsonOS\Module\Transfer\Model\Queue;
use GibsonOS\Module\Transfer\Repository\QueueRepository;
use GibsonOS\Module\Transfer\Service\ClientService;
use Psr\Log\LoggerInterface;

class TransferCommand extends AbstractCommand
{
    public function __construct(
        protected LoggerInterface $logger,
        private QueueRepository $queueRepository,
        private ClientService $clientService,
        private CryptService $cryptService
    ) {
    }

    /**
     * @throws SaveError
     */
    protected function run(): int
    {
        if ($this->queueRepository->countByStatus(Queue::STATUS_ACTIVE) >= 10) {
            return 1;
        }

        try {
            $queue = $this->queueRepository->getNextByStatus(Queue::STATUS_WAIT);
        } catch (SelectError) {
            return 0;
        }

        $queue->setStatus(Queue::STATUS_ACTIVE)->save();
        $remoteUser = $queue->getRemoteUser();
        $remotePassword = $queue->getRemotePassword();

        try {
            $client = $this->clientService->connect(
                $queue->getSessionId(),
                $queue->getProtocol(),
                $queue->getUrl(),
                $queue->getPort(),
                $remoteUser === null ? null : $this->cryptService->decrypt($remoteUser),
                $remotePassword === null ? null : $this->cryptService->decrypt($remotePassword),
            );
        } catch (FactoryError|SelectError|ClientException) {
            $queue
                ->setStatus(Queue::STATUS_ERROR)
                ->setMessage('Connection error!')
                ->save()
            ;

            return 1;
        }

        try {
            if ($queue->getDirection() === Queue::DIRECTION_DOWNLOAD) {
                $client->get($queue->getRemotePath(), $queue->getLocalPath());
            } else {
                $client->put($queue->getLocalPath(), $queue->getRemotePath());
            }
        } catch (ClientException) {
            $queue
                ->setStatus(Queue::STATUS_ERROR)
                ->setMessage('Transmission error!')
                ->save()
            ;

            return 1;
        }

        $queue->setStatus(Queue::STATUS_FINISHED)->save();

        return 0;
    }
}

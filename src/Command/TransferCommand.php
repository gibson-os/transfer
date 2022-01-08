<?php
declare(strict_types=1);

namespace GibsonOS\Module\Transfer\Command;

use GibsonOS\Core\Command\AbstractCommand;
use GibsonOS\Core\Exception\CreateError;
use GibsonOS\Core\Exception\FactoryError;
use GibsonOS\Core\Exception\FileExistsError;
use GibsonOS\Core\Exception\FileNotFound;
use GibsonOS\Core\Exception\Model\SaveError;
use GibsonOS\Core\Exception\Repository\SelectError;
use GibsonOS\Module\Transfer\Exception\ClientException;
use GibsonOS\Module\Transfer\Model\Queue;
use GibsonOS\Module\Transfer\Repository\QueueRepository;
use GibsonOS\Module\Transfer\Service\QueueService;
use Psr\Log\LoggerInterface;

/**
 * @description Down- and upload queued files
 */
class TransferCommand extends AbstractCommand
{
    public function __construct(
        LoggerInterface $logger,
        private QueueRepository $queueRepository,
        private QueueService $queueService
    ) {
        parent::__construct($logger);
    }

    /**
     * @throws ClientException
     * @throws SaveError
     * @throws SelectError
     * @throws FactoryError
     * @throws FileExistsError
     * @throws CreateError
     * @throws FileNotFound
     */
    protected function run(): int
    {
        if ($this->queueRepository->countByStatus(Queue::STATUS_ACTIVE) >= 10) {
            return self::ERROR;
        }

        try {
            $queue = $this->queueRepository->getNextByStatus(Queue::STATUS_WAIT);
        } catch (SelectError) {
            return self::SUCCESS;
        }

        $this->queueService->handle($queue);

        return self::SUCCESS;
    }
}

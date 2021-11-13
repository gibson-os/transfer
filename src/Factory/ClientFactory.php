<?php
declare(strict_types=1);

namespace GibsonOS\Module\Transfer\Factory;

use GibsonOS\Core\Exception\FactoryError;
use GibsonOS\Core\Service\ServiceManagerService;
use GibsonOS\Module\Transfer\Client\ClientInterface;

class ClientFactory
{
    public function __construct(private ServiceManagerService $serviceManagerService)
    {
    }

    /**
     * @param class-string $clientClassName
     *
     * @throws FactoryError
     */
    public function get(string $clientClassName): ClientInterface
    {
        $client = $this->serviceManagerService->get($clientClassName);

        if (!$client instanceof ClientInterface) {
            throw new FactoryError(sprintf(
                'Client %s is not an interface of %s',
                $clientClassName,
                ClientInterface::class
            ));
        }

        return $client;
    }
}

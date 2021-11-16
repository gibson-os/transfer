<?php
declare(strict_types=1);

namespace GibsonOS\Module\Transfer\Store;

use GibsonOS\Core\Store\AbstractStore;
use GibsonOS\Module\Transfer\Client\ClientInterface;
use GibsonOS\Module\Transfer\Dto\ListItem;
use GibsonOS\Module\Transfer\Exception\ClientException;

class DirStore extends AbstractStore
{
    private ClientInterface $client;

    private string $dir;

    /**
     * @throws ClientException
     *
     * @return ListItem[]
     */
    public function getList(): iterable
    {
        return $this->client->getList($this->dir);
    }

    public function getCount(): int
    {
        return 0;
    }

    public function setClient(ClientInterface $client): DirStore
    {
        $this->client = $client;

        return $this;
    }

    public function setDir(string $dir): DirStore
    {
        $this->dir = $dir;

        return $this;
    }
}

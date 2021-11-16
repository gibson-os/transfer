<?php
declare(strict_types=1);

namespace GibsonOS\Module\Transfer\Store;

use GibsonOS\Core\Store\AbstractStore;
use GibsonOS\Module\Transfer\Client\ClientInterface;
use GibsonOS\Module\Transfer\Exception\ClientException;

class DirListStore extends AbstractStore
{
    private ClientInterface $client;

    private string $dir = 'root';

    /**
     * @throws ClientException
     */
    public function getList(): iterable
    {
        $list = $this->getDirs($this->dir);

        if ($this->dir === 'root' || empty($this->dir)) {
            // Load parents
        }

        return $list;
    }

    public function getCount(): int
    {
        return 0;
    }

    public function setClient(ClientInterface $client): DirListStore
    {
        $this->client = $client;

        return $this;
    }

    public function setDir(string $dir): DirListStore
    {
        $this->dir = $dir;

        return $this;
    }

    /**
     * @throws ClientException
     */
    private function getDirs(string $dir): array
    {
        $dirs = [];

        foreach ($this->client->getList($this->dir) as $item) {
            if ($item->getType() !== 'dir') {
                continue;
            }

            $dirs[] = $item;
        }

        return $dirs;
    }
}

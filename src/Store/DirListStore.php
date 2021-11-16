<?php
declare(strict_types=1);

namespace GibsonOS\Module\Transfer\Store;

use GibsonOS\Core\Store\AbstractStore;
use GibsonOS\Module\Transfer\Client\ClientInterface;

class DirListStore extends AbstractStore
{
    private ClientInterface $client;

    private string $dir = 'root';

    public function getList(): iterable
    {
        if ($this->dir === 'root' || empty($this->dir)) {
        }

        return [];
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
}

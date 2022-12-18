<?php
declare(strict_types=1);

namespace GibsonOS\Module\Transfer\Store;

use GibsonOS\Core\Store\AbstractStore;
use GibsonOS\Module\Transfer\Client\ClientInterface;
use GibsonOS\Module\Transfer\Exception\ClientException;
use GibsonOS\Module\Transfer\Service\ClientService;

class DirListStore extends AbstractStore
{
    private ClientInterface $client;

    private string $dir = '/';

    private bool $loadParents = false;

    public function __construct(private ClientService $clientService)
    {
    }

    /**
     * @throws ClientException
     */
    public function getList(): iterable
    {
        $list = [...$this->getDirs($this->dir)];

        if ($this->loadParents) {
            $childDir = $this->dir;
            $dirs = explode('/', $this->dir);
            unset($dirs[count($dirs) - 1]);

            for ($i = count($dirs) - 1; $i >= 0; --$i) {
                $parentList = [];
                $parentDir = implode('/', $dirs) . '/';

                foreach ($this->getDirs($parentDir) as $dir) {
                    if ($childDir === $parentDir . $dir['encryptedName'] . '/') {
                        $dir['data'] = $list;
                        $dir['expanded'] = true;
                    }

                    $parentList[] = $dir;
                }

                $list = $parentList;
                $childDir = $parentDir;
                unset($dirs[count($dirs) - 1]);
            }
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

    public function setLoadParents(bool $loadParents): DirListStore
    {
        $this->loadParents = $loadParents;

        return $this;
    }

    /**
     * @throws ClientException
     */
    private function getDirs(string $dir): \Generator
    {
        foreach ($this->clientService->getList($this->client, $dir, false) as $item) {
            yield [
                'text' => $item->getDecryptedName(),
                'encryptedName' => $item->getName(),
                'id' => $dir . $item->getName() . '/',
                'iconCls' => 'icon16 icon_dir',
            ];
        }
    }
}

<?php
declare(strict_types=1);

namespace GibsonOS\Module\Transfer\Store;

use GibsonOS\Core\Store\AbstractStore;

class DirListStore extends AbstractStore
{
    public function getList(): iterable
    {
        return [];
    }

    public function getCount(): int
    {
        return 0;
    }
}

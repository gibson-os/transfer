<?php
declare(strict_types=1);

use GibsonOS\Core\Model\AbstractModel;

class Sync extends AbstractModel
{
    public static function getTableName(): string
    {
        return 'transfer_sync';
    }
}

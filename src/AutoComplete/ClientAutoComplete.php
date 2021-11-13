<?php
declare(strict_types=1);

namespace GibsonOS\Module\Transfer\AutoComplete;

use GibsonOS\Core\AutoComplete\AutoCompleteInterface;
use GibsonOS\Core\Exception\GetError;
use GibsonOS\Core\Model\AutoCompleteModelInterface;
use GibsonOS\Core\Service\DirService;
use GibsonOS\Core\Service\FileService;
use GibsonOS\Module\Transfer\Dto\Client;

class ClientAutoComplete implements AutoCompleteInterface
{
    public function __construct(private DirService $dirService, private FileService $fileService)
    {
    }

    /**
     * @throws GetError
     */
    public function getByNamePart(string $namePart, array $parameters): array
    {
        $files = $this->dirService->getFiles(
            realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR .
            '..' . DIRECTORY_SEPARATOR . 'Client' . DIRECTORY_SEPARATOR
        );
        $namespace = 'GibsonOS\\Module\\Transfer\\Client\\';
        $clients = [];

        foreach ($files as $file) {
            $className = str_replace('.php', '', $this->fileService->getFilename($file));

            if (mb_strpos($className, 'Interface') !== false) {
                continue;
            }

            $clients[] = new Client($namespace . $className);
        }

        return $clients;
    }

    public function getById(string $id, array $parameters): AutoCompleteModelInterface
    {
        return new Client($id);
    }

    public function getModel(): string
    {
        return 'GibsonOS.module.transfer.session.model.Client';
    }
}

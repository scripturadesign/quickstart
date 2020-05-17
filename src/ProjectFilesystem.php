<?php

declare(strict_types=1);

namespace Scriptura\QuickStart;

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;

class ProjectFilesystem
{
    /**
     * @var \League\Flysystem\Filesystem
     */
    private $filesystem;

    public function __construct(string $directory)
    {
        $adapter = new Local($directory, LOCK_EX, Local::SKIP_LINKS);
        $this->filesystem = new Filesystem($adapter);
    }

    public function createFile(string $file, string $content)
    {
        $this->filesystem->put($file, $content);
    }

    public function updateFile(string $file, callable $callback)
    {
        if (!$this->filesystem->has($file)) {
            return;
        }
        $contents = $this->filesystem->read($file);

        $contents = $callback($contents);

        $this->filesystem->put($file, $contents);
    }

    public function updateAllFilesOfType(string $type, callable $callback)
    {
        $files = array_filter($this->filesystem->listContents('', true), function (array $file) use ($type) {
            $query = 'vendor/';
            if (strpos($file['path'], $query) === 0) {
                return false;
            }

            if (!isset($file['extension'])) {
                return false;
            }

            if ($file['extension'] !== $type) {
                return false;
            }

            return true;
        });

        foreach ($files as $file) {
            $contents = $this->filesystem->read($file['path']);

            $contents = $callback($contents);

            $this->filesystem->put($file['path'], $contents);
        }
    }
}

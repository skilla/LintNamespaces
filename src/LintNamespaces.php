<?php

declare(strict_types = 1);

namespace Skilla\LintNamespaces;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LintNamespaces extends Application
{
    public function __construct()
    {
        parent::__construct('Sergio Zambrano - Lint Namespaces', '1.0.0');
    }

    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $basePath = realpath(__DIR__ . '/../../../..') . '/';

        $namespaces = $this->retrieveNamespaces($basePath . 'composer.json');

        $level = 0;
        foreach ($namespaces as $namespace => $directory) {
            $this->processNamespace($level+1, $basePath, trim($namespace, '\\'), $directory);
        }
    }

    private function retrieveNamespaces(string $filename): array
    {
        $content = $this->getArrayContent($filename);

        return $this->extractPsr4Namespaces($content);
    }

    private function getArrayContent(string $filename): array
    {
        $content = file_get_contents($filename);
        return json_decode($content, true);
    }

    private function extractPsr4Namespaces(array $content): array
    {
        $namespaces = [];
        if (isset($content['autoload']['psr-4'])) {
            $psr4 = (array) $content['autoload']['psr-4'];
            foreach ($psr4 as $entry => $directories) {
                foreach ((array) $directories as $directory) {
                    $namespaces[$entry] = $directory;
                }
            }
        }
        if (isset($content['autoload-dev']['psr-4'])) {
            $psr4 = (array) $content['autoload-dev']['psr-4'];
            foreach ($psr4 as $entry => $directories) {
                foreach ((array) $directories as $directory) {
                    $namespaces[$entry] = $directory;
                }
            }
        }

        return $namespaces;
    }

    private function processNamespace(int $level, string $basePath, string $namespace, $directory): void
    {
        //echo str_repeat('    ', $level-1);
        //echo $namespace . ' => ' . $directory . "\n";

        $handle = dir($basePath.$directory);
        if (!$handle) {
            return;
        }

        while ($entry = $handle->read()) {
            if (is_dir($basePath . $directory . '/' . $entry)) {
                if (in_array($entry, ['.', '..'])) {
                    continue;
                }
                $newLevel = $level + 1;
                $newNamespace = $namespace . '\\' . $entry;
                $newDirectory = $directory . '/' . $entry;
                $this->processNamespace($newLevel, $basePath, $newNamespace, $newDirectory);
            }

            if (strtolower(substr($entry, -4)) !== '.php') {
                continue;
            }

            $filename = $basePath . $directory . '/' . $entry;

            $lines = file($filename);
            foreach ($lines as $line) {
                $matches = [];
                if (1 === preg_match('/namespace[\s]+([^\s]+);/', $line, $matches)) {
                    if ($namespace !== $matches[1]) {
                        die('Namespace not match ' . $namespace . ' in file ' . str_replace($basePath, '', $filename) . "\n\n");
                    }
                }
            }
        }
    }
}

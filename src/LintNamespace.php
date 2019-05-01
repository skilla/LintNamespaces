<?php

declare(strict_types = 1);

namespace Skilla\LintNamespaces;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class LintNamespace extends Application
{
    public function __construct()
    {
        parent::__construct('Sergio Zambrano - Lint Namespaces', '1.0.0');
    }

    public function doRun(InputInterface $input, OutputInterface $output)
    {
    }
}

#!/usr/bin/env php
<?php

require __DIR__ . '/../../autoload.php';

use Skilla\LintNamespaces\LintNamespaces;


$application = new LintNamespaces();
$application->run();

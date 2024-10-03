#!/usr/bin/env php
<?php

require __DIR__ . '/../src/FileSystemTraversal.php';
$file_system_traversal = new \Gnorm\FileSystemTraversal();

$repo_root = $file_system_traversal->findRepoRoot();

if ($repo_root === false) {
    echo "Error: Unable to find the repository root.\n";
    exit(1);
}

$autoload = require_once $repo_root . '/vendor/autoload.php';

if (!isset($autoload)) {
    echo "Error: Unable to find autoloader for Gnorm.\n";
    exit(1);
}

/**
 * Options:
 * c = The json encoded config.
 * b = Boolean telling if this is a build.
 */
$options = getopt('c:b:');

try {
    if (!$options['c']) {
        throw new Exception('Twig config missing.');
    }

    $config = \Gnorm\GnormTwigConfiguration::decode($options['c']);

    $isBuild = 'TRUE' == $options['b'];
    $compiler = new \Gnorm\GnormCompiler($repo_root, $config, $isBuild);
    $compiler->execute();
} catch (Throwable $exception) {
    echo $exception->getMessage() . "\n" . $exception->getTraceAsString() . "\n";
}

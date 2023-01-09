#!/usr/bin/env php
<?php

$file_system_traversal = new \Gnorm\FileSystemTraversal();
$repo_root = $file_system_traversal->findRepoRoot();
$autoload = require_once $repo_root . '/vendor/autoload.php';

if (!isset($autoload)) {
    print "Unable to find autoloader for Gnorm.\n";
    exit(1);
}

/**
 * Options:
 * c = The json encoded config.
 * b = Boolean telling if this is a build.
 */
$options = getopt('c:b::');

try {
    if (!$options['c']) {
        throw new Exception('Twig config missing.');
    }

    $config = \Gnorm\GnormTwigConfiguration::decode($options['c']);

    $compiler = new \Gnorm\GnormSiteGenertor($repo_root, $config, $options['b']);
    $compiler->execute();
}
catch (Throwable $exception) {
    echo $exception->getMessage() . "\n" . $exception->getTraceAsString() . "\n";
}

#!/usr/bin/env php
<?php

$repo_root = find_repo_root();
$autoload = require_once $repo_root . '/vendor/autoload.php';
if (!isset($autoload)) {
    print "Unable to find autoloader for Gnorm.\n";
    exit(1);
}

execute($repo_root);

function execute($repo_root)
{
    try {
      // All the following parameters are required.
      /**
       * Options:
       * c = The json encoded config.
       * b = Boolean telling if this is a build.
       */
        $options = getopt('c:b:');

        $config = json_decode($options['c'], true);
        if (!$config) {
            throw new Exception('Twig config missing.');
        }

      // Set targets based on passed configuration.
      // The location of the theme directory.
        $baseDir = $repo_root;

      // The location of the application directory.
        $sourceDir = $config['source'];

      // The pattern to search for source twig files.
        $sourcePattern = $config['pattern'];

      // The location to save built files.
        $buildDir = $config['dest'];
        $buildPath = $baseDir . '/' . $buildDir;

      // Ensure build directory exists.
        if (!file_exists($buildPath)) {
            mkdir($buildPath, 0777, true);
        }

      // Make buildPath absolute.
        $buildPath = realpath($buildPath);

      // The location of the json data files.
        $jsonPath = $config['data'];

      // The namespaces and their locations.
        $namespaces = $config['namespaces'];

      // The location of the global json file.
        $globalJsonFile = $config['global'];

      // If this is a build or not.
        $isBuild = 'TRUE' == $options['b'];

      // Load the global json and set the build flag.
        $globalJson = getJson($globalJsonFile);
        if (!empty($config['dynamicGlobal'])) {
            $globalJson = array_merge($globalJson, $config['dynamicGlobal']);
        }
        $globalJson['isBuild'] = $isBuild;

      // Load twig.
        $loader = new \Twig\Loader\FilesystemLoader($baseDir);

      // Add the namespaces.
        foreach ($namespaces as $key => $location) {
            $loader->addPath($location, $key);
        }

      // Set up the twig environment.
        $twig = new \Twig\Environment($loader, array(
        'cache' => false,
        'debug' => true,
        'autoescape' => false,
        ));
        $twig->addExtension(new \Gnorm\Extensions\Drupal());

      // Add local project extensions if they are defined.
        if (class_exists('\Gnorm\Fed\Extensions')) {
            $twig->addExtension(new \Gnorm\Fed\Extensions());
        }

      // Loop through each file that matches the source pattern.
        foreach (glob($sourcePattern) as $filename) {
            try {
                // Get the name of the file without extension.
                $basename = basename($filename, '.twig');

                echo "Rendering $basename\n";

                // Get any json.
                $jsonFile = $jsonPath . "/{$basename}.json";
                $json = getJson($jsonFile);
                $json = array_merge($globalJson, $json);

                // Render the twig file.
                $file = $sourceDir . "/{$basename}.twig";
                $rendered = $twig->render($file, $json);

                // Write the output file.
                $destination = $buildPath . "/{$basename}.html";
                file_put_contents($destination, $rendered);
            } catch (Twig\Error\Error $e) {
                $context = $e->getSourceContext();
                echo $e->getRawMessage() . "\n"
                . $context->getName() . " line: " . $e->getLine()
                . "\n" . $context->getCode()
                . "\n";
            } catch (Throwable $e) {
                echo $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
            }
        }
    } catch (Throwable $e) {
        echo $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
    }
}

/**
 * Finds the root directory for the repository.
 *
 * @return bool|string
 */
function find_repo_root()
{
    $possible_repo_roots = [
    getcwd(),
    realpath(__DIR__ . '/../'),
    realpath(__DIR__ . '/../../../'),
    ];
  // Check for PWD - some local environments will not have this key.
    if (isset($_SERVER['PWD'])) {
        array_unshift($possible_repo_roots, $_SERVER['PWD']);
    }
    foreach ($possible_repo_roots as $possible_repo_root) {
        if ($repo_root = find_directory_containing_files($possible_repo_root, ['vendor/autoload.php'])) {
            return $repo_root;
        }
    }
}

/**
 * Traverses file system upwards in search of a given file.
 *
 * Begins searching for $file in $working_directory and climbs up directories
 * $max_height times, repeating search.
 *
 * @param string $working_directory
 * @param array $files
 * @param int $max_height
 *
 * @return bool|string
 *   FALSE if file was not found. Otherwise, the directory path containing the
 *   file.
 */
function find_directory_containing_files(string $working_directory, array $files, int $max_height = 10)
{
  // Find the root directory of the git repository containing BLT.
  // We traverse the file tree upwards $max_height times until we find
  // vendor/bin/blt.
    $file_path = $working_directory;
    for ($i = 0; $i <= $max_height; $i++) {
        if (files_exist($file_path, $files)) {
            return $file_path;
        } else {
            $file_path = realpath($file_path . '/..');
        }
    }

    return false;
}

/**
 * Determines if an array of files exists in a particular directory.
 *
 * @param string $dir
 * @param array $files
 *
 * @return bool
 */
function files_exist($dir, $files)
{
    foreach ($files as $file) {
        if (!file_exists($dir . '/' . $file)) {
            return false;
        }
    }
    return true;
}

/**
 * Get Json file and ensure output is an array.
 *
 * @param $file_path
 * @return array
 */
function getJson($file_path)
{
    if (file_exists($file_path)) {
        $json_string = file_get_contents($file_path);
        if ($json = json_decode($json_string, true)) {
            return $json;
        } else {
            echo "Invalid JSON: $file_path\n";
            return [];
        }
    } else {
        return [];
    }
}

<?php

namespace Gnorm;

use Gnorm\Extensions\Drupal;
use Gnorm\Fed\FedExtensions;
use Twig\Environment;
use Twig\Error\Error;
use Twig\Loader\FilesystemLoader;

class GnormCompiler implements GnormCompilerInterface
{

    /**
     * @inheritDoc
     */
    public function execute(string $repo_root): mixed
    {
        try {
            /**
             * All the following parameters are required.
             *
             * Options:
             * c = The json encoded config.
             * b = Boolean telling if this is a build.
             */
            $options = getopt('c:b:');

            $config = json_decode($options['c'], true);

            if (!$config) {
                throw new \Exception('Twig config is missing');
            }

            /**
             * Set targets based on passed configuration.
             * The location of the theme directory.
             */
            $base_dir = $repo_root;

            // The location of the application directory.
            $source_dir = $config['source'];

            // The pattern to search for source twig files.
            $source_pattern = $config['pattern'];

            // The location to save built files.
            $build_dir = $config['dest'];
            $build_path = $base_dir . '/' . $build_dir;

            // Ensure build directory exists.
            if (!file_exists($build_path)) {
                mkdir($build_path, 0777, true);
            }

            // Absolute build_path.
            $absolute_path = realpath($build_path);

            // The location of the json data files.
            $json_path = $config['data'];

            // The namespaces and their locations.
            $namespaces = $config['namespaces'];

            // The location of the global json file.
            $global_json_file = $config['global'];

            // If this is a build or not.
            $is_build = 'TRUE' == $options['b'];

            // Load the global json and set the build flag.
            $global_json = getJson($global_json_file);

            if (!empty($config['dynamicGlobal'])) {
                $global_json = array_merge($global_json, $config['dynamicGlobal']);
            }

            $global_json['isBuild'] = $is_build;

            // Load twig.
            $loader = new FilesystemLoader($base_dir);

            // Add the namespaces.
            foreach ($namespaces as $key => $location) {
                $loader->addPath($location, $key);
            }

            // Set up the twig environment.
            $twig = new Environment($loader, array(
              'cache' => false,
              'debug' => true,
              'autoescape' => false,
            ));
            $twig->addExtension(new Drupal());

            // Add local project extensions if they are defined.
            if (class_exists('\Gnorm\Fed\FedExtensions')) {
                $twig->addExtension(new FedExtensions());
            }

            // Loop through each file that matches the source pattern.
            foreach (glob($source_pattern) as $filename) {
                try {
                    // Get the name of the file without extension.
                    $basename = basename($filename, '.twig');

                    echo "Rendering $basename\n";

                    // Get any json.
                    $json_file = $json_path . "/{$basename}.json";
                    $json = getJson($json_file);
                    $json = array_merge($global_json, $json);

                    // Render the twig file.
                    $file = $source_dir . "/{$basename}.twig";
                    $rendered = $twig->render($file, $json);

                    // Write the output file.
                    $destination = $build_path . "/{$basename}.html";
                    file_put_contents($destination, $rendered);
                } catch (Error $exception) {
                    $context = $exception->getSourceContext();
                    echo $exception->getRawMessage() . "\n"
                      . $context->getName() . " line: " . $exception->getLine()
                      . "\n" . $context->getCode()
                      . "\n";
                } catch (\Throwable $exception) {
                    echo $exception->getMessage() . "\n" . $exception->getTraceAsString() . "\n";
                }
            }
        } catch (\Throwable $exception) {
            echo $exception->getMessage() . "\n" . $exception->getTraceAsString() . "\n";
        }
    }

    /**
     * @inheritDoc
     */
    public function findRepoRoot(): bool|string
    {
        // TODO: Implement findRepoRoot() method.
    }

    /**
     * @inheritDoc
     */
    public function findDirectoryContainingFiles(
      string $working_directory,
      array $files,
      int $max_height = 10
    ): bool|string {
        // TODO: Implement findDirectoryContainingFiles() method.
    }

    /**
     * @inheritDoc
     */
    public function filesExist(string $dir, array $files): bool
    {
        // TODO: Implement filesExist() method.
    }

    /**
     * @inheritDoc
     */
    public function getJson(string $file_path): array
    {
        // TODO: Implement getJson() method.
    }

}

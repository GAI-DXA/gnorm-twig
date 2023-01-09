<?php

namespace Gnorm;

use Gnorm\Extensions\Drupal;
use Gnorm\Fed\FedExtensions;
use Twig\Environment;
use Twig\Error\Error;
use Twig\Loader\FilesystemLoader;

abstract class CompilerBase implements CompilerInterface
{
    /**
     * Set targets based on passed configuration.
     * The location of the theme directory.
     */
    public string $baseDir;

    public array $twigConfig;

    public bool $isBuild;

    public function __construct(string $repo_root, array $twig_config, bool $build = false)
    {
        $this->baseDir = $repo_root;
        $this->twigConfig = $twig_config;
        $this->isBuild = $build;
    }

    public function execute()
    {
        try {
            // The location to save built files.
            $build_path = $this->baseDir . '/' . $this->twigConfig['dest'];

            // Ensure build directory exists.
            if (!file_exists($build_path)) {
                mkdir($build_path, 0777, true);
            }

            // Absolute build_path.
            $absolute_path = realpath($build_path);

            // Load the global json and set the build flag.
            $global_json = $this->getJson($this->twigConfig['global']);

            if (!empty($this->twigConfig['dynamicGlobal'])) {
                $global_json = array_merge($global_json, $this->twigConfig['dynamicGlobal']);
            }

            $global_json['isBuild'] = $this->isBuild;

            // Load twig.
            $loader = new FilesystemLoader($this->baseDir);

            // Add the namespaces.
            foreach ($this->twigConfig['namespaces'] as $key => $location) {
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
            foreach (glob($this->twigConfig['pattern']) as $filename) {
                try {
                    // Get the name of the file without extension.
                    $basename = basename($filename, '.twig');

                    echo "Rendering $basename\n";

                    // Get any json.
                    $json_file = $this->twigConfig['data'] . "/{$basename}.json";
                    $json = $this->getJson($json_file);
                    $json = array_merge($global_json, $json);

                    // Render the twig file.
                    $file = $this->twigConfig['source'] . "/{$basename}.twig";
                    $rendered = $twig->render($file, $json);

                    // Write the output file.
                    $destination = $absolute_path . "/{$basename}.html";
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
     * Get Json file and ensure output is an array.
     *
     * @param $file_path
     * @return array
     */
    public function getJson($file_path): array
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
}

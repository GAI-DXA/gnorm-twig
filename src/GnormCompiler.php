<?php

namespace Gnorm;

use Gnorm\Extensions\Drupal;
use Gnorm\Fed\FedExtensions;
use Twig\Environment;
use Twig\Error\Error;
use Twig\Loader\FilesystemLoader;
use Twig\Source;

class GnormCompiler implements CompilerInterface
{
    private Environment $twig;
    private string $baseDir;
    /** @var array<string, mixed> */
    private array $twigConfig;
    private bool $isBuild;

    /**
     * @param array<string, mixed> $twigConfig
     */
    public function __construct(string $baseDir, array $twigConfig, bool $isBuild, ?Environment $twig = null)
    {
        $this->baseDir = $baseDir;
        $this->twigConfig = $twigConfig;
        $this->isBuild = $isBuild;
        $this->twig = $twig ?? $this->setupTwigEnvironment();
    }

    public function execute(): void
    {
        try {
            $build_path = $this->createBuildDirectory();
            $global_json = $this->loadGlobalJson();

            $pattern = $this->twigConfig['pattern'] ?? '';
            if (is_string($pattern)) {
                $files = glob($pattern);
                if ($files !== false) {
                    foreach ($files as $filename) {
                        $this->renderTwigFile($this->twig, $filename, $global_json, $build_path);
                    }
                } else {
                    echo "No files matched the pattern.\n";
                }
            } else {
                echo "Invalid pattern type.\n";
            }
        } catch (\Throwable $exception) {
            echo $exception->getMessage() . "\n" . $exception->getTraceAsString() . "\n";
        }
    }

    private function createBuildDirectory(): string
    {
        $build_path = $this->baseDir . '/' . $this->twigConfig['dest'];
        if (!file_exists($build_path)) {
            mkdir($build_path, 0777, true);
        }
        $real_path = realpath($build_path);
        if ($real_path === false) {
            throw new \RuntimeException("Failed to resolve real path for build directory.");
        }
        return $real_path;
    }

    /**
     * @return array<string, mixed>
     */
    private function loadGlobalJson(): array
    {
        $global_json = [];
        $global_path = $this->twigConfig['global'] ?? '';

        if (is_string($global_path)) {
            $global_json = $this->getJson($global_path);
        } else {
            echo "Invalid global JSON path type.\n";
        }

        $dynamic_global = $this->twigConfig['dynamicGlobal'] ?? [];
        if (is_array($dynamic_global)) {
            $global_json = array_merge($global_json, $dynamic_global);
        } else {
            echo "Invalid dynamic global type.\n";
        }

        $global_json['isBuild'] = $this->isBuild;
        return $global_json;
    }

    private function setupTwigEnvironment(): Environment
    {
        $loader = new FilesystemLoader($this->baseDir);
        $namespaces = $this->twigConfig['namespaces'] ?? [];

        if (is_array($namespaces)) {
            foreach ($namespaces as $key => $location) {
                $loader->addPath($location, $key);
            }
        } else {
            echo "Invalid namespaces type.\n";
        }

        $twig = new Environment($loader, [
            'cache' => false,
            'debug' => true,
            'autoescape' => false,
        ]);
        $twig->addExtension(new Drupal());
        if (class_exists('\Gnorm\Fed\FedExtensions')) {
            $twig->addExtension(new FedExtensions());
        }
        return $twig;
    }

    /**
     * @param array<string, mixed> $global_json
     */
    private function renderTwigFile(Environment $twig, string $filename, array $global_json, string $build_path): void
    {
        try {
            $basename = basename($filename, '.twig');
            echo "Rendering $basename\n";

            $json_file = $this->twigConfig['data'] . "/{$basename}.json";
            $json = $this->getJson($json_file);
            $json = array_merge($global_json, $json);

            $file = $this->twigConfig['source'] . "/{$basename}.twig";
            $rendered = $twig->render($file, $json);

            $destination = $build_path . "/{$basename}.html";
            file_put_contents($destination, $rendered);
        } catch (Error $exception) {
            $context = $exception->getSourceContext();
            if ($context instanceof Source) {
                echo $exception->getRawMessage() . "\n"
                    . $context->getName() . " line: " . $exception->getLine()
                    . "\n" . $context->getCode()
                    . "\n";
            } else {
                echo $exception->getRawMessage() . "\n";
            }
        } catch (\Throwable $exception) {
            echo $exception->getMessage() . "\n" . $exception->getTraceAsString() . "\n";
        }
    }

    /**
     * Get Json file and ensure output is an array.
     *
     * @param string $file_path
     * @return array<string, mixed>
     */
    public function getJson(string $file_path): array
    {
        if (file_exists($file_path)) {
            $json_string = file_get_contents($file_path);
            if ($json_string !== false) {
                $json = json_decode($json_string, true);
                if (is_array($json)) {
                    return $json;
                } else {
                    echo "Invalid JSON: $file_path\n";
                }
            } else {
                echo "Failed to read file: $file_path\n";
            }
        }
        return [];
    }

    /**
     * @return array<string>
     */
    public function getFilesToCompile(): array
    {
        // Implement the logic to retrieve files
        return [];
    }
}

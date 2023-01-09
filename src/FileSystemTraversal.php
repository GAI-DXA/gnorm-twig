<?php

namespace Gnorm;

class FileSystemTraversal
{

    /**
     * Finds the root directory for the repository.
     *
     * @return bool|string
     */
    public function findRepoRoot()
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
            if ($repo_root = $this->findDirectoryContainingFiles($possible_repo_root, ['vendor/autoload.php'])) {
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
    public function findDirectoryContainingFiles(string $working_directory, array $files, int $max_height = 10)
    {
        // Find the root directory of the git repository containing BLT.
        // We traverse the file tree upwards $max_height times until we find
        // vendor/bin/blt.
        $file_path = $working_directory;
        for ($i = 0; $i <= $max_height; $i++) {
            if ($this->filesExist($file_path, $files)) {
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
    public function filesExist($dir, $files): bool
    {
        foreach ($files as $file) {
            if (!file_exists($dir . '/' . $file)) {
                return false;
            }
        }
        return true;
    }
}

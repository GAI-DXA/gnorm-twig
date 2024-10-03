<?php

namespace Gnorm;

/**
 * Class FileSystemTraversal
 *
 * Provides methods to traverse the file system to find specific directories or files.
 */
class FileSystemTraversal
{
    /**
     * Finds the root directory for the repository.
     *
     * @return bool|string
     *   Returns the path to the repository root if found, otherwise false.
     */
    public function findRepoRoot(): bool|string
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

        return false;
    }

    /**
     * Traverses file system upwards in search of a given file.
     *
     * Begins searching for $files in $working_directory and climbs up directories
     * $max_height times, repeating search.
     *
     * @param string $working_directory
     *   The directory to start searching from.
     * @param string[] $files
     *   An array of filenames to search for.
     * @param int $max_height
     *   The maximum number of directory levels to traverse upwards.
     *
     * @return bool|string
     *   FALSE if none of the files were found. Otherwise, the directory path containing the files.
     */
    public function findDirectoryContainingFiles(string $working_directory, array $files = [], int $max_height = 10): bool|string
    {
        $file_path = $working_directory;

        for ($i = 0; $i <= $max_height; $i++) {
            if ($this->filesExist($file_path, $files)) {
                return $file_path;
            }

            $file_path = realpath($file_path . '/..');
            if ($file_path === false) {
                return false;
            }
        }

        return false;
    }

    /**
     * Determines if an array of files exists in a particular directory.
     *
     * @param string $dir
     *   The directory to check for the files.
     * @param string[] $files
     *   An array of filenames to check for existence.
     *
     * @return bool
     *   TRUE if all files exist in the directory, FALSE otherwise.
     */
    public function filesExist(string $dir, array $files): bool
    {
        foreach ($files as $file) {
            if (!file_exists($dir . '/' . $file)) {
                return false;
            }
        }

        return true;
    }
}

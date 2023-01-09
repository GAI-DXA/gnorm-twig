<?php

namespace Gnorm;

interface GnormCompilerInterface
{
    /**
     * @param string $repo_root
     *
     * @return mixed
     */
    public function execute(string $repo_root): mixed;

    /**
     * Finds the root directory for the repository.
     *
     * @return bool|string
     */
    public function findRepoRoot(): bool|string;

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
    public function findDirectoryContainingFiles(string $working_directory, array $files, int $max_height = 10): bool|string;

    /**
     * Determines if an array of files exists in a particular directory.
     *
     * @param string $dir
     * @param array $files
     *
     * @return bool
     */
    public function filesExist(string $dir, array $files): bool;

    /**
     * Get Json file and ensure output is an array.
     *
     * @param string $file_path
     * @return array
     */
    public function getJson(string $file_path): array;
}

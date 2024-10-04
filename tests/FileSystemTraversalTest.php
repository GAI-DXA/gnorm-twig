<?php

use PHPUnit\Framework\TestCase;
use Gnorm\FileSystemTraversal;

class FileSystemTraversalTest extends TestCase
{
    private $fileSystemTraversal;

    protected function setUp(): void
    {
        $this->fileSystemTraversal = new FileSystemTraversal();
    }

    public function testFindRepoRoot()
    {
        // This test assumes that the repository root is correctly set up
        // and that the 'vendor/autoload.php' file exists in the expected location.
        $repoRoot = $this->fileSystemTraversal->findRepoRoot();
        $this->assertNotFalse($repoRoot, "Repository root should be found.");
        $this->assertFileExists($repoRoot . '/vendor/autoload.php', "Autoload file should exist in the repo root.");
    }

    public function testFindDirectoryContainingFiles()
    {
        // Test with a known directory and file
        $directory = __DIR__; // Current directory
        $files = ['FileSystemTraversalTest.php']; // This test file

        $result = $this->fileSystemTraversal->findDirectoryContainingFiles($directory, $files);
        $this->assertEquals($directory, $result, "Should find the directory containing the test file.");
    }

    public function testFilesExist()
    {
        // Test with existing files
        $directory = __DIR__;
        $files = ['FileSystemTraversalTest.php'];

        $result = $this->fileSystemTraversal->filesExist($directory, $files);
        $this->assertTrue($result, "Files should exist in the directory.");

        // Test with non-existing files
        $nonExistingFiles = ['non_existing_file.php'];
        $result = $this->fileSystemTraversal->filesExist($directory, $nonExistingFiles);
        $this->assertFalse($result, "Files should not exist in the directory.");
    }
}

<?php

use PHPUnit\Framework\TestCase;
use Gnorm\GnormCompiler;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use phpmock\MockBuilder;

class GnormCompilerTest extends TestCase
{
    private $compiler;
    private $twigMock;
    private $globMock;
    private $filePutContentsMock;

    protected function setUp(): void
    {
        $this->twigMock = $this->createMock(Environment::class);

        $twigConfig = [
            'pattern' => '*.twig',
            'dest' => 'build',
            'source' => 'src/templates',
            'data' => 'src/data',
            'global' => 'src/global.json',
            'dynamicGlobal' => []
        ];

        // Create an instance of GnormCompiler with the mocked Twig environment
        $this->compiler = new GnormCompiler('/var/www/html', $twigConfig, false, $this->twigMock);

        // Mock the global glob function
        $builder = new MockBuilder();
        $builder->setNamespace('Gnorm')
            ->setName('glob')
            ->setFunction(
                function ($pattern) {
                    return ['template1.twig', 'template2.twig'];
                }
            );
        $this->globMock = $builder->build();
        $this->globMock->enable();

        // Mock the global file_put_contents function
        $builder = new MockBuilder();
        $builder->setNamespace('Gnorm')
            ->setName('file_put_contents')
            ->setFunction(
                function ($filename, $content) {
                    // You can add assertions here if needed
                    return strlen($content); // Simulate successful write
                }
            );
        $this->filePutContentsMock = $builder->build();
        $this->filePutContentsMock->enable();
    }

    protected function tearDown(): void
    {
        $this->globMock->disable();
        $this->filePutContentsMock->disable();
    }

    public function testExecuteCompilesTwigFiles()
    {
        // Mock the Twig environment to simulate rendering
        $this->twigMock->expects($this->exactly(2))
            ->method('render')
            ->willReturn('<html>Rendered Content</html>');

        // Set up a counter for file_put_contents calls
        $fileWriteCount = 0;

        // Redefine the existing file_put_contents mock to count calls
        $this->filePutContentsMock->disable();
        $builder = new MockBuilder();
        $builder->setNamespace('Gnorm')
            ->setName('file_put_contents')
            ->setFunction(
                function ($filename, $content) use (&$fileWriteCount) {
                    $fileWriteCount++;
                    return strlen($content); // Simulate successful write
                }
            );
        $this->filePutContentsMock = $builder->build();
        $this->filePutContentsMock->enable();

        // Execute the compiler
        $this->compiler->execute();

        // Assert that file_put_contents was called twice
        $this->assertEquals(2, $fileWriteCount, 'file_put_contents should be called twice');
    }
}

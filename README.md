# Gnorm Twig

Implements the Twig functionality used by [Genuine's Gnorm framework](https://github.com/GAI-DXA/gnorm-twig).

## Overview

Gnorm Twig is a supplemental library for compiling Gnorm Twig files, providing various Twig filter implementations and a compiler script.

## Installation

To install the project, clone the repository and run the following command to install dependencies:

```bash
composer install
```

## Dependencies

The project requires the following:

- PHP: ^7.4 || ^8.0
- ext-json: *
- twig/twig: ^2.15.3 || ^3.5.0

For development:

- phpcompatibility/php-compatibility: ^9.3
- phpstan/phpstan: ^1.9
- squizlabs/php_codesniffer: ^3.6
- phpunit/phpunit: ^9.5

## Configuration

The project uses several configuration files:

- `.ddev/config.yaml`: Configuration for the development environment.
- `composer.json`: Lists project dependencies and autoloading information.
- `phpcs.xml.dist`: Configuration for PHP CodeSniffer.
- `phpstan.neon.dist`: Configuration for PHPStan.
- `phpunit.xml.dist`: Configuration for PHPUnit.

## Usage

To compile Twig files, use the `bin/compile.php` script. It requires a JSON-encoded configuration and a build flag:

```bash
php bin/compile.php -c <config> -b <build>
```

## Development

### Coding Standards

The project follows PSR-12 coding standards. Use PHP CodeSniffer to check for compliance:

```bash
vendor/bin/phpcs
```

### Static Analysis

Use PHPStan for static analysis:

```bash
vendor/bin/phpstan analyse
```

### Testing

The project uses PHPUnit for unit testing. To run the tests, use the following command:

```bash
vendor/bin/phpunit
```

Test files are located in the `tests` directory and follow the naming convention `*Test.php`.

## Contributing

Contributions are welcome! Please follow the coding standards, ensure all tests pass, and add new tests for new functionality before submitting a pull request.

## Additional Information

- The `GnormCompiler` class implements the `CompilerInterface` and is responsible for compiling Twig files. It uses the `GnormTwigConfiguration` class to decode JSON configurations.
- The `FedExtensions` and `Drupal` classes provide custom Twig extensions, filters, and functions.
- The `FileSystemTraversal` class provides methods to traverse the file system to find specific directories or files.
- The `.gitignore` file includes common directories and files to be ignored, such as `/vendor`, `/node_modules`, and log files.

- Unit tests have been added to ensure the reliability and correctness of the codebase.

For more detailed information on each component, refer to the respective source files in the `src` directory and test files in the `tests` directory.

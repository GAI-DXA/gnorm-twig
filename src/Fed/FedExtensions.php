<?php

namespace Gnorm\Fed;

use Twig\Extension\ExtensionInterface;
use Twig\TwigFilter;
use Twig\Node\Expression\AbstractExpression;
use Twig\TokenParser\TokenParserInterface;
use Twig\NodeVisitor\NodeVisitorInterface;
use Twig\TwigTest;
use Twig\TwigFunction;

/**
 * Class FedExtensions
 *
 * This class provides custom Twig extensions for the application.
 * It implements the ExtensionInterface to integrate with Twig.
 */
class FedExtensions implements ExtensionInterface
{
    /**
     * Returns a list of custom filters to be used in Twig templates.
     *
     * @return TwigFilter[] An array of TwigFilter instances
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('custom_filter', [$this, 'customFilterFunction']),
        ];
    }

    /**
     * Custom filter function to transform input data.
     *
     * @param string $value The input value to be transformed
     * @return string The transformed value
     */
    public function customFilterFunction(string $value): string
    {
        // Ensure the input is a string and return it in uppercase
        return strtoupper($value);
    }

    /**
     * Returns custom operators for Twig.
     *
     * @return array{array<string, array{precedence: int, class: class-string<AbstractExpression>}>, array<string, array{precedence: int, class?: class-string<AbstractExpression>, associativity: 1|2}>}
     */
    public function getOperators(): array
    {
        return [
            // Unary operators
            [],

            // Binary operators
            []
        ];
    }

    /**
     * Returns a list of custom token parsers.
     *
     * @return TokenParserInterface[] An array of TokenParserInterface instances
     */
    public function getTokenParsers(): array
    {
        return [];
    }

    /**
     * Returns a list of custom node visitors.
     *
     * @return NodeVisitorInterface[] An array of NodeVisitorInterface instances
     */
    public function getNodeVisitors(): array
    {
        return [];
    }

    /**
     * Returns a list of custom tests.
     *
     * @return TwigTest[] An array of TwigTest instances
     */
    public function getTests(): array
    {
        return [];
    }

    /**
     * Returns a list of custom functions.
     *
     * @return TwigFunction[] An array of TwigFunction instances
     */
    public function getFunctions(): array
    {
        return [];
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName(): string
    {
        return 'fed_extensions';
    }
}

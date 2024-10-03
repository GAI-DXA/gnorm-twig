<?php

namespace Gnorm\Extensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Class Drupal
 *
 * This class extends Twig's AbstractExtension to provide custom filters and functions
 * for use in Twig templates, specifically tailored for Drupal.
 *
 * @package Gnorm\Extensions
 */
class Drupal extends AbstractExtension
{
    /**
     * {@inheritdoc}
     *
     * Returns an array of custom Twig filters.
     *
     * @return TwigFilter[]
     *   An array of TwigFilter objects.
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('clean_class', [$this, 'passThrough']),
            new TwigFilter('clean_id', [$this, 'passThrough']),
            new TwigFilter('format_date', [$this, 'passThrough']),
            new TwigFilter('placeholder', [$this, 'passThrough']),
            new TwigFilter('raw', [$this, 'passThrough']),
            new TwigFilter('render', [$this, 'passThrough']),
            new TwigFilter('t', [$this, 'passThrough']),
            new TwigFilter('safe_join', [$this, 'passThrough']),
            new TwigFilter('without', [$this, 'passThrough']),
        ];
    }

    /**
     * {@inheritdoc}
     *
     * Returns an array of custom Twig functions.
     *
     * @return TwigFunction[]
     *   An array of TwigFunction objects.
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('create_attribute', [$this, 'passThrough']),
        ];
    }

    /**
     * Simple pass through the passed value.
     *
     * This method is used as a placeholder for filters and functions that do not
     * require any transformation of the input value.
     *
     * @param mixed $element
     *   Any passed element.
     *
     * @return mixed
     *   The passed element.
     */
    public function passThrough(mixed $element): mixed
    {
        // No transformation is applied; the input is returned as-is.
        return $element;
    }
}

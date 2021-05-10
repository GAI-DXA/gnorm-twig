<?php

namespace Gnorm\Extensions;

use Twig\TwigFilter;
use Twig\Extension\AbstractExtension;

/**
 * Class Drupal
 *
 * @package Gnorm\Extensions
 */
class Drupal extends AbstractExtension {

  /**
   * {@inheritdoc}
   */
  public function getFilters() {
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
   * Simple pass through the passed value.
   *
   * @param $element
   *   Any passed element.
   *
   * @return mixed
   *   The passed element.
   */
  public function passThrough($element) {
    return $element;
  }
}

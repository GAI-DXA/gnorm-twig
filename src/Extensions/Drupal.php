<?php

namespace Gnorm\Extensions;

/**
 * Class Drupal
 *
 * @package Gnorm\Extensions
 */
class Drupal extends \Twig_Extension {

  /**
   * {@inheritdoc}
   */
  public function getFilters() {
    return [
      new \Twig_SimpleFilter('clean_class', [$this, 'passThrough']),
      new \Twig_SimpleFilter('clean_id', [$this, 'passThrough']),
      new \Twig_SimpleFilter('format_date', [$this, 'passThrough']),
      new \Twig_SimpleFilter('placeholder', [$this, 'passThrough']),
      new \Twig_SimpleFilter('raw', [$this, 'passThrough']),
      new \Twig_SimpleFilter('render', [$this, 'passThrough']),
      new \Twig_SimpleFilter('url_decode', [$this, 'passThrough']),
      new \Twig_SimpleFilter('safe_join', [$this, 'passThrough']),
      new \Twig_SimpleFilter('t', [$this, 'passThrough']),
      new \Twig_SimpleFilter('without', [$this, 'passThrough']),
    ];
  }

  public function getFunctions() {
    return [
      new \Twig_SimpleFunction('file_url', [$this, 'passThrough']),
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

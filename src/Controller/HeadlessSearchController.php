<?php

/**
 * @file
 * Contains \Drupal\headless\Controller\HeaderSearchController.
 */

namespace Drupal\headless\Controller;

use Drupal\headless\HeadlessBase;

/**
 * Controller routine.
 */
class HeadlessSearchController extends HeadlessBase {

  /**
   * Perform site-wide search.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   Response represents an HTTP response in JSON format.
   */
  public function search() {
    return $this->handler('\Drupal\search\Form\SearchPageForm');
  }
}

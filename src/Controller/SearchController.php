<?php

/**
 * @file
 * Definition of Drupal\headless\Controller\SearchController.
 */

namespace Drupal\headless\Controller;

use Drupal\headless\HeadlessBase;

/**
 * Controller routine.
 */
class SearchController extends HeadlessBase {

  /**
   * Perform site-wide search.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   Response represents an HTTP response in JSON format.
   */
  public function search() {
    return $this->processRequest('\Drupal\search\Form\SearchPageForm');
  }
}

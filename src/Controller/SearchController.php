<?php

/**
 * @file
 * Definition of Drupal\headless\Controller\SearchController.
 */

namespace Drupal\headless\Controller;

use Drupal\headless\HeadlessBase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller routine.
 */
class SearchController extends HeadlessBase {

  /**
   * Perform site-wide search.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   Response represents an HTTP response.
   */
  public function query() {
    $output = $this->submitForm('\Drupal\search\Form\SearchPageForm');
    $status = NULL;

    // Form submission success.
    if (isset($output['data'])) {
      $status = Response::HTTP_ACCEPTED;
    }

    // Errors exist.
    elseif (isset($output['error'])) {
      $status = Response::HTTP_BAD_REQUEST;
    }
    else {
      $status = Response::HTTP_FORBIDDEN;
    }

    // Send the response.
    return new Response($this->serialize($output), $status);
  }
}

<?php

/**
 * @file
 * Definition of Drupal\headless\Controller\SearchController.
 */

namespace Drupal\headless\Controller;

use Drupal\headless\HeadlessBase;
use Symfony\Component\HttpFoundation\JsonResponse;

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
  public function query() {
    $output = $this->submitForm('\Drupal\search\Form\SearchPageForm');
    $status = NULL;

    // Form submission success.
    if (isset($output['data'])) {
      $status = JsonResponse::HTTP_ACCEPTED;
    }

    // Errors exist.
    elseif (isset($output['error'])) {
      $status = JsonResponse::HTTP_BAD_REQUEST;
    }
    else {
      $status = JsonResponse::HTTP_FORBIDDEN;
    }

    // Send the response.
    return new JsonResponse($output, $status);
  }
}

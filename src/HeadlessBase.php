<?php

/**
 * @file
 * Contains Drupal\headless\Controller\HeadlessBase.
 */

namespace Drupal\headless;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Form\FormState;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Serializer;

/**
 * Headless utility base class.
 */
class HeadlessBase implements ContainerInjectionInterface {

  /**
   * Request stack instance.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  private $requestStack;

  /**
   * Serializer instance.
   *
   * @var \Symfony\Component\Serializer\Serializer
   */
  private $serializer;

  /**
   * Constructs a \Drupal\headless\Controller\HeadlessBase object.
   *
   * @param RequestStack $request_stack
   * @param Serializer   $serializer
   */
  public function __construct(RequestStack $request_stack, Serializer $serializer) {
    $this->requestStack = $request_stack;
    $this->serializer   = $serializer;
  }

  /**
   * @inheritdoc
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('request_stack'),
      $container->get('serializer')
    );
  }

  /**
   * Retrieves an instance of the JsonResponse object.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse | null
   *   Response represents an HTTP response in JSON format.
   */
  public function response($data = NULL, $status = 200, $headers = array()) {
    return new JsonResponse($data, $status, $headers);
  }

  /**
   * Retrieves an instance of the Request object.
   *
   * @return \Symfony\Component\HttpFoundation\Request | null
   *   Request represents an HTTP request or null.
   */
  public function request() {
    return $this->requestStack->getCurrentRequest();
  }

  /**
   * Retrieves, populates, and processes a form.
   *
   * @param string $class
   *   Defines a class.
   *
   * @param array $params
   *  HTTP request parameters.
   *
   * @return mixed
   *   Form values or errors | undefined
   */
  public function submitForm($class, $params) {

    // Create FormState instance, set values, and submit.
    $form_state = (new FormState())->setValues($params);

    \Drupal::formBuilder()->submitForm($class, $form_state);

    if ($form_state->hasAnyErrors()) {

      // Returns an associative array of error messages.
      return array(
        'error' => $form_state->getErrors(),
      );
    }
    else {

      // Returns the submitted and sanitized form values.
      return array(
        'data' => $form_state->getValues(),
      );
    }
  }

  /**
   * Process the client-side request and send response.
   *
   * @param string $class
   *   Defines a form class.
   *
   * @param callable $callback
   *   Defines a callback function.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   Response represents an HTTP response in JSON format.
   */
  public function handler($class, $callback = NULL) {
    $response = $this->response();
    $request  = $this->request();

    $output = array();
    $params = NULL;

    // Get the Request body and decode the JSON content.
    $content = $request->getContent();
    if ($this->isJson($content)) {
      $params = $this->serializer->decode($content, 'json');
    }
    else {
      $response->setStatusCode($response::HTTP_BAD_REQUEST);
    }

    // Submit the form.
    if ($request->getMethod() == 'POST' && $params) {
      $output = $this->submitForm($class, $params);

      // Success.
      if (isset($output['data'])) {

        // Execute pre-process callback, if provided.
        if (is_callable($callback)) {
          $callback($output['data']);
        }

        $response->setStatusCode($response::HTTP_ACCEPTED);
      }

      // Errors exist.
      elseif (isset($output['error'])) {
        $response->setStatusCode($response::HTTP_BAD_REQUEST);
      }
      else {
        $response->setStatusCode($response::HTTP_FORBIDDEN);
      }
    }

    // Return the response.
    return $response->setData($output);
  }

  /**
   * Check string is valid JSON.
   *
   * @param string $string
   *   JSON in a tring format.
   *
   * @return bool
   */
  function isJson($string) {
    json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
  }
}

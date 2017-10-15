<?php

namespace Drupal\headless;

use Drupal\Core\Config\ConfigFactoryInterface;
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
   * The configuration factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

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
   * Constructs a HeadlessBase object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The configuration factory.
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   Request stack instance.
   * @param \Symfony\Component\Serializer\Serializer $serializer
   *   Serializer instance.
   */
  public function __construct(ConfigFactoryInterface $config_factory, RequestStack $request_stack, Serializer $serializer) {
    $this->configFactory = $config_factory;
    $this->requestStack  = $request_stack;
    $this->serializer    = $serializer;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('request_stack'),
      $container->get('serializer')
    );
  }

  /**
   * Retrieves an instance of the JsonResponse object.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse|null
   *   Response represents an HTTP response in JSON format.
   */
  public function response($data = NULL, $status = 200, $headers = []) {
    return new JsonResponse($data, $status, $headers);
  }

  /**
   * Retrieves an instance of the Request object.
   *
   * @return \Symfony\Component\HttpFoundation\Request|null
   *   Request represents an HTTP request or null.
   */
  public function request() {
    return $this->requestStack->getCurrentRequest();
  }

  /**
   * Retrieves, populates, and processes a form.
   *
   * @param class|\Drupal\Core\Form\FormInterface $form_arg
   *   The name (or class) that implements \Drupal\Core\Form\FormInterface.
   * @param array $params
   *   HTTP request parameters.
   *
   * @return mixed
   *   Form values or errors | undefined
   */
  public function submitForm($form_arg, array $params) {

    // Create FormState instance, set values, and submit.
    $form_state = (new FormState())->setValues($params);

    \Drupal::formBuilder()->submitForm($form_arg, $form_state);

    if ($form_state->hasAnyErrors()) {

      // Returns an associative array of error messages.
      return [
        'error' => $form_state->getErrors(),
      ];
    }
    else {
      $values = $form_state->getValues();

      // Save the form values, where applicable.
      if (!is_string($form_arg)) {
        $form_arg->save($values, $form_state);
      }

      // Returns the submitted and sanitized form values.
      return [
        'data' => $values,
      ];
    }
  }

  /**
   * Process the client-side request and send response.
   *
   * @param class|\Drupal\Core\Form\FormInterface $form_arg
   *   The name (or class) that implements \Drupal\Core\Form\FormInterface.
   * @param callable $callback
   *   Defines a callback function.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   Response represents an HTTP response in JSON format.
   */
  public function handler($form_arg, callable $callback = NULL) {
    $response = $this->response();
    $request = $this->request();

    $output = [];
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
      $output = $this->submitForm($form_arg, $params);

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
   *   TRUE | FALSE
   */
  private function isJson($string) {
    json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
  }

}

<?php

/**
 * @file
 * Definition of Drupal\headless\Controller\HeadlessBase
 */

namespace Drupal\headless;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Form\FormState;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Serializer;

/**
 * Headless utility base class.
 */
class HeadlessBase implements ContainerInjectionInterface {

  /**
   * Response exchange format.
   *
   * @var string
   */
  public $format = 'json';

  /**
   * Represents an HTTP request.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  public $serialize;

  /**
   * Request stack instance.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * Serializer instance.
   *
   * @var \Symfony\Component\Serializer\Serializer
   */
  protected $serializer;

  /**
   * General constructor.
   *
   * @param RequestStack $requestStack
   * @param Serializer   $serializer
   */
  public function __construct(RequestStack $requestStack, Serializer $serializer) {
    $this->requestStack = $requestStack;
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
   * Retrieves an instance of the request object.
   *
   * @return \Symfony\Component\HttpFoundation\Request | null
   *   Request represents an HTTP request or null.
   */
  public function request() {
    return $this->requestStack->getCurrentRequest();
  }

  /**
   * Serializes data in the appropriate format.
   *
   * @param mixed $data
   *   Any data.
   *
   * @return string
   *   Serialized data.
   */
  public function serialize($data) {
    return $this->serializer->serialize($data, $this->format);
  }

  /**
   * Decodes a string into PHP data.
   *
   * @param mixed $data
   *   Any data.
   *
   * @return mixed
   *   PHP data.
   */
  public function decode($data) {
    return $this->serializer->decode($data, $this->format);
  }

  /**
   * Process request parameters for a given form by class name.
   *
   * @param string $class
   *   Defines a class.
   *
   * @return mixed
   *   Form values or errors | undefined
   */
  public function submitForm($class) {
    $content = $this->request()->getContent();
    if ($content) {
      $params = $this->decode($content, $this->format);

      // Create a new form instance; submit form values.
      $form_state = (new FormState())->setValues($params);

      \Drupal::formBuilder()->submitForm($class, $form_state);

      if ($form_state->hasAnyErrors()) {

        // Returns an associative array of errors.
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
  }
}

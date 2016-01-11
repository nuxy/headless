<?php

/**
 * @file
 * Definition of Drupal\headless\Controller\SearchController.
 */

namespace Drupal\headless\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormState;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;

/**
 * Controller routine.
 */
class SearchController extends ControllerBase {

  /**
   * Serializer instance.
   *
   * @var \Symfony\Component\Serializer\Serializer
   */
  protected $serializer;

  /**
   * General constructor.
   *
   * @param Serializer $serializer
   */
  public function __construct(Serializer $serializer) {
    $this->serializer = $serializer;
  }

  /**
   * @inheritdoc
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('serializer')
    );
  }

  /**
   * 
   *
   * @param Request $request
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   Response represents an HTTP response.
   */
  public function query(Request $request) {
    $output = NULL;
    $status = NULL;

    $content = $request->getContent();
    if ($content) {
      $params = $this->serializer->decode($content, 'json');

      $form_state = (new FormState())->setValues($params);

      \Drupal::formBuilder()->submitForm('\Drupal\search\Form\SearchPageForm', $form_state);

      $errors = $form_state->getErrors();
      if ($errors) {
        $output = $this->serializer->serialize(array('error' => $errors), 'json');
        $status = 400;
      }
      else {
        $output = $this->serializer->serialize(array('data' => 'Hello'), 'json');
        $status = 200;
      }
    }
    else {
      $status = 500;
    }

    return new Response($output, $status);
  }
}

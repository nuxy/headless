<?php

/**
 * @file
 * Definition of Drupal\headless\Controller\UserController.
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
class UserController extends ControllerBase {

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
   * Login the User creating new session.
   *
   * @param Request $request
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   Response represents an HTTP response.
   */
  public function login(Request $request) {
    $output = NULL;
    $status = NULL;

    $content = $request->getContent();
    if ($content) {
      $params = $this->serializer->decode($content, 'json');

      $form_state = (new FormState())->setValues($params);

      \Drupal::formBuilder()->submitForm('\Drupal\user\Form\UserLoginForm', $form_state);

      $errors = $form_state->getErrors();
      if ($errors) {
        $output = $this->serializer->serialize(array('error' => $errors), 'json');
        $status = Response::HTTP_BAD_REQUEST;
      }
      else {
        $account = \Drupal::entityManager()->getStorage('user')->load(\Drupal::currentUser()->id());
        $fields  = $account->getFields();

        // Remove hidden fields (Display mode 'default').
        $view_display = \Drupal::entityManager()->getStorage('entity_view_display')->load('user.user.default');
        if ($view_display) {
          $content = $view_display->get('content');

          foreach ($fields as $field_name => $field) {
            if (substr($field_name, 0, 6) === 'field_' && !isset($content[$field_name])) {
              unset($fields[$field_name]);
            }
          }
        }

        $output = $this->serializer->serialize(array('data' => $fields), 'json');
        $status = Response::HTTP_ACCEPTED;
      }
    }
    else {
      $status = Response::HTTP_FORBIDDEN;
    }

    return new Response($output, $status);
  }

  /**
   * Logout the User removing the session data.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   Response represents an HTTP response.
   */
  public function logout() {
    user_logout();

    return new Response(NULL, Response::HTTP_ACCEPTED);
  }

  /**
   * Create a new User account.
   *
   * @param Request $request
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   Response represents an HTTP response.
   */
  public function register(Request $request) {
    $status = NULL;
    $output = NULL;

    $content = $request->getContent();
    if ($content) {
      $params = $this->serializer->decode($content, 'json');

      $form_state = new FormState();
      $form_state->setValues($params);

      \Drupal::formBuilder()->submitForm('\Drupal\user\Form\RegisterForm', $form_state);

      $errors = $form_state->getErrors();
      if ($errors) {
        $output = $this->serializer->serialize(array('error' => $errors), 'json');
        $status = Response::HTTP_BAD_REQUEST;
      }
      else {
        $status = Response::HTTP_ACCEPTED;
      }
    }
    else {
      $status = Response::HTTP_FORBIDDEN;
    }

    return new Response($output, $status);
  }
}

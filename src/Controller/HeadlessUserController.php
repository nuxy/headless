<?php

/**
 * @file
 * Contains \Drupal\headless\Controller\HeadlessUserController.
 */

namespace Drupal\headless\Controller;

use Drupal\headless\HeadlessBase;

/**
 * Controller routine.
 */
class HeadlessUserController extends HeadlessBase {

  /**
   * Cancel the User account.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   Response represents an HTTP response in JSON format.
   */
  public function cancel() {
    return $this->handler('\Drupal\user\Form\UserCancelForm');
  }

  /**
   * Login the User creating a new session.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   Response represents an HTTP response in JSON format.
   */
  public function login() {
    return $this->handler('\Drupal\user\Form\UserLoginForm', function(&$data) {
      $config = $this->configFactory->get('headless.config');

      // Get the current user data.
      $storage = \Drupal::entityTypeManager()
        ->getStorage('user');

      $user = $storage->load(\Drupal::currentUser()->id());

      // Prepare the JSON response.
      $data = array();
      foreach ($config->get('user_fields') as $field_name) {
        $data[$field_name] = $user->get($field_name)->value;
      }
    });
  }

  /**
   * Logout the User removing the session data.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   Response represents an HTTP response in JSON format.
   */
  public function logout() {
    user_logout();

    // Send the response.
    return $this->response();
  }

  /**
   * Generates a unique URL for a User to login and reset their password.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   Response represents an HTTP response in JSON format.
   */
  public function passwordReset() {
    return $this->process('\Drupal\user\Form\UserPasswordResetForm');
  }

  /**
   * Update the User Profile data.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   Response represents an HTTP response in JSON format.
   */
  public function profile() {
    return $this->process('profile');
  }

  /**
   * Register the User creating a new User account.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   Response represents an HTTP response in JSON format.
   */
  public function register() {
    return $this->process('register');
  }

  /**
   * Handle requests for ContentEntityForm form variants.
   *
   * @param string $name profile | register
   *   ContentEntityType form handler name.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   Response represents an HTTP response in JSON format.
   *
   * @see \Drupal\user\Entity\User
   */
  private function process($name) {
    $entity = \Drupal::entityTypeManager()->getStorage('user')->create(array());

    $form = \Drupal::entityTypeManager()
      ->getFormObject('user', $name)
      ->setEntity($entity);

    return $this->handler($form, function(&$data) {

      // Return nothing.
      $data = NULL;
    });
  }
}

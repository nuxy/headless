<?php

/**
 * @file
 * Contains \Drupal\headless\Controller\UserController.
 */

namespace Drupal\headless\Controller;

use Drupal\headless\HeadlessBase;

/**
 * Controller routine.
 */
class UserController extends HeadlessBase {

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

      // Preprocess response.
      $data = array(
        'uid' => \Drupal::currentUser()->id(),
      );
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

    return $this->response();
  }

  /**
   * Generates a unique URL for a User to login and reset their password.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   Response represents an HTTP response in JSON format.
   */
  public function passwordReset() {
    return $this->handler('\Drupal\user\Form\UserPasswordResetForm');
  }

  /**
   * Register the User creating a new User account.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   Response represents an HTTP response in JSON format.
   */
  public function register() {
    return $this->handler('\Drupal\user\RegisterForm');
  }

  /**
   * Update the User Profile data.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   Response represents an HTTP response in JSON format.
   */
  public function profile() {
    return $this->handler('\Drupal\user\ProfileForm');
  }
}

<?php

AccountForm
AccountSettingsForm
UserCancelForm
ProfileForm

/**
 * @file
 * Definition of Drupal\headless\Controller\UserController.
 */

namespace Drupal\headless\Controller;

use Drupal\headless\HeadlessBase;

/**
 * Controller routine.
 */
class UserController extends HeadlessBase {

  /**
   * Login the User creating a new session.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   Response represents an HTTP response in JSON format.
   */
  public function login() {
    return $this->processRequest('\Drupal\user\Form\UserLoginForm', function(&$data) {

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
   * Register the User creating a new User account.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   Response represents an HTTP response in JSON format.
   */
  public function register() {
    return $this->processRequest('\Drupal\user\Form\RegisterForm');
  }

  /**
   * Generates a unique URL for a user to login and reset their password.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   Response represents an HTTP response in JSON format.
   */
  public function passwordReset() {
    return $this->processRequest('\Drupal\user\Form\UserPasswordResetForm');
  }

  /**
   * Cancels user account.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   Response represents an HTTP response in JSON format.
   */
  public function cancel() {
    return $this->processRequest('\Drupal\user\Form\UserCancelForm');
  }
}

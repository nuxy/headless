<?php

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
    return $this->handler('\Drupal\user\Form\UserCancelForm', function (&$data) {
      \Drupal::moduleHandler()->invokeAll('headless_response_alter', [&$data]);
    });
  }

  /**
   * Login the User creating a new session.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   Response represents an HTTP response in JSON format.
   */
  public function login() {
    return $this->handler('\Drupal\user\Form\UserLoginForm', function (&$data) {
      \Drupal::moduleHandler()->invokeAll('headless_response_alter', [&$data]);
    });
  }

  /**
   * Checks whether an active User session exists.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   Response represents an HTTP response in JSON format.
   */
  public function loginStatus() {
    return $this->response(['active' => \Drupal::currentUser()->isAuthenticated()]);
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
    return $this->handler('\Drupal\user\Form\UserPasswordResetForm', function (&$data) {
      \Drupal::moduleHandler()->invokeAll('headless_response_alter', [&$data]);
    });
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
   * @param string $name
   *   ContentEntityType form handler name (profile|register).
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   Response represents an HTTP response in JSON format.
   *
   * @see \Drupal\user\Entity\User
   */
  private function process($name) {
    $entity = \Drupal::entityTypeManager()->getStorage('user')->create([]);

    $form = \Drupal::entityTypeManager()
      ->getFormObject('user', $name)
      ->setEntity($entity);

    return $this->handler($form, function (&$data) {
      \Drupal::moduleHandler()->invokeAll('headless_response_alter', [&$data]);
    });
  }

}

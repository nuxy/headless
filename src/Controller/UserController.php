<?php

/**
 * @file
 * Definition of Drupal\headless\Controller\UserController.
 */

namespace Drupal\headless\Controller;

use Drupal\headless\HeadlessBase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller routine.
 */
class UserController extends HeadlessBase {

  /**
   * Login the User creating a new session.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   Response represents an HTTP response.
   */
  public function login() {
    $output = $this->submitForm('\Drupal\user\Form\UserLoginForm');
    $status = NULL;

    // Form submission success.
    if (isset($output['data'])) {

      // Set-up response.
      $output['data'] = array(
        'uid' => \Drupal::currentUser()->id(),
      );

      $status = Response::HTTP_ACCEPTED;
    }

    // Errors exist.
    elseif (isset($output['error'])) {
      $status = Response::HTTP_BAD_REQUEST;
    }
    else {
      $status = Response::HTTP_FORBIDDEN;
    }

    // Send the response.
    return new Response($this->serialize($output), $status);
  }

  /**
   * Logout the User removing the session data.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   Response represents an HTTP response.
   */
  public function logout() {
    user_logout();

    return new Response(NULL, Response::HTTP_OK);
  }

  /**
   * Register the User creating a new User account.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   Response represents an HTTP response.
   */
  public function register() {
    $output = $this->submitForm('\Drupal\user\Form\RegisterForm');
    $status = NULL;

    // Form submission success.
    if (isset($output['data'])) {
      $status = Response::HTTP_ACCEPTED;
    }

    // Errors exist.
    elseif (isset($output['error'])) {
      $status = Response::HTTP_BAD_REQUEST;
    }
    else {
      $status = Response::HTTP_FORBIDDEN;
    }

    // Send the response.
    return new Response($this->serialize($output), $status);
  }

  /**
   * Generates a unique URL for a user to login and reset their password.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   Response represents an HTTP response.
   */
  public function passwordReset() {
    $output = $this->submitForm('\Drupal\user\Form\UserPasswordResetForm');
    $status = NULL;

    // Form submission success.
    if (isset($output['data'])) {

      // Set-up response.
      $output['data'] = array(
        'uid' => \Drupal::currentUser()->id(),
      );

      $status = Response::HTTP_ACCEPTED;
    }

    // Errors exist.
    elseif (isset($output['error'])) {
      $status = Response::HTTP_BAD_REQUEST;
    }
    else {
      $status = Response::HTTP_FORBIDDEN;
    }

    // Send the response.
    return new Response($this->serialize($output), $status);
  }
}

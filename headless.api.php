<?php

/**
 * @file
 * Contains headless.api.php.
 */

use \Symfony\Component\HttpFoundation\Request;

/**
 * Deprecated hook to be removed in future release.
 *
 * @see hook_headless_response_alter
 */
function hook_headless_data_alter(array &$data) {

}

/**
 * Alter the JSON response sent to the client.
 *
 * @param array $data
 *   The response data reference.
 */
function hook_headless_response_alter(array &$data) {

}

/**
 * Alter parameters for a given request.
 *
 * @param \Symfony\Component\HttpFoundation\Request $request
 *   The current request.
 * @param array $params
 *   The request parameters reference.
 */
function hook_headless_request_alter(Request $request, &$params) {

}

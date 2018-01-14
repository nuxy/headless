<?php

/**
 * @file
 * Contains headless.api.php.
 */

/**
 * Deprecated hook to be removed in future release.
 *
 * @param mixed $data
 *   The response data reference.
 *
 * @see hook_headless_response_alter
 */
function hook_headless_data_alter(&$data) {
  // DO NOT USE!
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
 * @param array $params
 *   The request parameters reference.
 */
function hook_headless_request_alter(array &$params) {

}

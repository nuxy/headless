# Headless

Expose Drupal 8 user operations as routes that support the JSON exchange format.

## Features

- A configurable path that provides User Login, Register, Password Reset, and Account form handlers.
- Post-process hooks for customizing JSON response data on request success.
- Support for `application/json` Content-Type for POST requests and related responses.
- Easy integration into [AngularJS](https://angularjs.org), [EmberJS](http://emberjs.com), or [jQuery](https://jquery.com) applications - No custom headers or tokens required.

## Installation

- Download the latest [release](https://github.com/nuxy/headless/tags).
- Extract the contents of the _.zip_ into: `<drupal root>/modules/contrib/`

## Set-up

Once the module has been installed/enabled, you can navigate to `admin/config/services/headless` **(Configuration > Web Services > Headless in the Admin panel)** to configure the publicly accessible Routing Path.

## Dependencies

- [CORS](https://github.com/piyuesh23/cors) _(Optional, but recommended)_

## Hooks

As of current, the `FormState` instance is returned that includes the `form_id` and field values.  In cases where this in NOT preferred you can override the response data using the following hook:

```
function hook_headless_data_alter(array &$data) {

  // Preprocess Login responses.
  if ($data['form_id'] == 'user_login_form') {

    // Return nothing.
    $data = NULL;
  }
}
```

## JavaScript Examples

### User Login

Using the AngularJS [$http](https://docs.angularjs.org/api/ng/service/$http) service.

```
$http({
  method: 'POST',
  url:    '/headless/user/login',
  cache:  false,
  data: {
    name: '<name>',
    pass: '<pass>'
  },
  withCredentials: true
})
.then(
  function successCallback(response) {},

  // handle errors
  function errorCallback(response) {
    if (response.status === 400) {}
    if (response.status === 500) {}
  }
);
```

Using the jQuery [$ajax](http://api.jquery.com/jquery.ajax) method.

```
$.ajax({
  type: 'POST',
  url: '/headless/user/login',
  dataType: 'json',
  data: {
    name: '<name>',
    pass: '<pass>'
  },
  cache: false,
  statusCode: {

    // login success
    202: function(data) {},

    // handle errors
    400: function() {},
    500: function() {}
  },
  xhrFields: {
    withCredentials: true
  }
});
```

### User Register

Using the AngularJS [$http](https://docs.angularjs.org/api/ng/service/$http) service.

```
$http({
  method: 'POST',
  url:    '/headless/user/register',
  cache:  false,
  data: {
    mail: '<mail>',
    name: '<name>',
    pass: '<pass>'
  }
})
.then(
  function successCallback(response) {},

  // handle errors
  function errorCallback(response) {
    if (response.status === 400) {}
    if (response.status === 500) {}
  }
);
```

Using the jQuery [$ajax](http://api.jquery.com/jquery.ajax) method.

```
$.ajax({
  type: 'POST',
  url: '/headless/user/register',
  dataType: 'json',
  data: {
    mail: '<mail>',
    name: '<name>',
    pass: '<pass>'
  },
  cache: false,
  statusCode: {

    // login success
    202: function(data) {},

    // handle errors
    400: function() {},
    500: function() {}
  }
});
```

## Contributions

If you find any bugs, or there is a feature you want to add, please submit a pull-request with your changes.  Note: Before committing your code please ensure that you are following the Drupal coding standards.

You can check your code by running the following command:

    $ phpcs --standard=./vendor/drupal/coder/coder_sniffer/Drupal/ruleset.xml src

## License and Warranty

This package is distributed in the hope that it will be useful, but without any warranty; without even the implied warranty of merchantability or fitness for a particular purpose.

_headless_ is provided under the terms of the [MIT license](http://www.opensource.org/licenses/mit-license.php)

## Author

[Marc S. Brooks](https://github.com/nuxy)

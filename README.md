# Headless

Exposes Drupal 8 user operations as routes that support the JSON exchange format.

## Features

- A configurable path that provides User Login, Register, Password Reset, and Account form handlers.
- Support for `application/json` Content-Type for POST requests and related responses.
- Easy integration into [AngularJS](https://angularjs.org), [EmberJS](http://emberjs.com), or [jQuery](https://jquery.com) applications - No custom headers or tokens required.

## Installation

- Download the latest [release](https://github.com/nuxy/headless/tags).
- Extract the contents of the _.zip_ into: `<drupal root>/modules/contrib/`

## Configuration

Once the module has been installed/enabled, you can navigate to `admin/config/services/headless` (Configuration > Web Services > Headless in the Admin panel) to configure the publicly accessible Routing Path.

## Dependencies

- [CORS](https://github.com/piyuesh23/cors) _(Optional, but recommended)_

## Login example

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

## License and Warranty

This package is distributed in the hope that it will be useful, but without any warranty; without even the implied warranty of merchantability or fitness for a particular purpose.

_headless_ is provided under the terms of the [MIT license](http://www.opensource.org/licenses/mit-license.php)

## Author

[Marc S. Brooks](https://github.com/nuxy)

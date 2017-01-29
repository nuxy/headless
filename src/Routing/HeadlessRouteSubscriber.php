<?php

/**
 * Contains \Drupal\headless\Routing\HeadlessRouteSubscriber.
 */

namespace Drupal\headless\Routing;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Defines custom route based on configured path.
 */
class HeadlessRouteSubscriber extends RouteSubscriberBase {

  /**
   * The ConfigFactory instance.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface.
   */
  protected $configFactory;

  /**
   * Constructs a \Drupal\headless\Routing\HeadlessRoutes object.
   *
   * @param ConfigFactoryInterface $config_factory
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public function alterRoutes(RouteCollection $route_collection) {
    $routing_path = $this->configFactory->get('headless.config')->get('routing_path');

    $route_names  = $route_collection->all();
    foreach ($route_names as $key => $route) {

      // Filter Headless public routes.
      if (substr($key, 0, 9) === 'headless.' && $key != 'headless.config') {
        $old_path = $route->getPath();
        $get_path = preg_replace('/^\/(headless|' . str_replace('/', '\/', $routing_path) . ')/', '', $old_path);
        $new_path = '/' . $routing_path . $get_path;

        // Update path if changed.
        if ($old_path != $new_path) {
          $route->setPath($new_path);
        }
      }
    }
  }
}

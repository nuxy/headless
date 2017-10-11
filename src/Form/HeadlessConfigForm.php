<?php

/**
 * @file
 * Contains \Drupal\headless\Form\HeadlessConfigForm.
 */

namespace Drupal\headless\Form;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RequestContext;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a form that configures Headless settings.
 */
class HeadlessConfigForm extends ConfigFormBase {

  /**
   * The router request context.
   *
   * @var \Drupal\Core\Routing\RequestContext
   */
  protected $requestContext;

  /**
   * Constructs a HeadlessConfigForm object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The configuration factory.
   * @param \Drupal\Core\Routing\RequestContext $request_context
   *   The router request context.
   */
  public function __construct(ConfigFactoryInterface $config_factory, RequestContext $request_context) {
    parent::__construct($config_factory);

    $this->requestContext = $request_context;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'headless_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('router.request_context')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('headless.config');

    $form['routing'] = array(
      '#type' => 'details',
      '#title' => t('Routing'),
      '#description' => t('The publicly accessible path to User operation routes. This must be a unique path, currently not in use.'),
      '#open' => TRUE,
    );

    $form['routing']['routing_path'] = array(
      '#type' => 'textfield',
      '#title' => t('Path'),
      '#size' => 55,
      '#maxlength' => 55,
      '#default_value' => $config->get('routing_path'),
      '#required' => TRUE,
      '#attributes' => array('placeholder' => 'service'),
      '#field_prefix' => $this->requestContext->getCompleteBaseUrl() . '/',
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $routing_path = $form_state->getValue('routing_path');

    if ($routing_path) {
      $form_state->setValueForElement($form['routing']['routing_path'], $routing_path);
    }
    else {
      $form_state->setValueForElement($form['routing']['routing_path'], '/headless');
    }

    if ($routing_path[0] == '/') {
      $form_state->setErrorByName('routing_path',
        t("The path '%path' cannot start with a slash.", array('%path' => $routing_path))
      );
    }

    if (!UrlHelper::isValid($routing_path)) {
      $form_state->setErrorByName('routing_path',
        t("The path '%path' is invalid or you do not have access to it.", array('%path' => $routing_path))
      );
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('headless.config');
    $config->set('routing_path', $form_state->getValue('routing_path'));
    $config->save();

    drupal_set_message(t('Configuration saved successfully!'), 'status', FALSE);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['headless.config'];
  }
}

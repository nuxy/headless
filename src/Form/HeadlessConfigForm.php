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
 * Headless configuration form.
 */
class HeadlessConfigForm extends ConfigFormBase {

  /**
   * The request context.
   *
   * @var \Drupal\Core\Routing\RequestContext
   */
  protected $requestContext;

  /**
   * Constructs a \Drupal\headless\Form\HeadlessConfigForm object.
   *
   * @param ConfigFactoryInterface $config_factory
   * @param RequestContexts        $request_context
   */
  public function __construct(ConfigFactoryInterface $config_factory, RequestContext $request_context) {
    parent::__construct($config_factory);

    $this->requestContext = $request_context;
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
  protected function getEditableConfigNames() {
    return ['headless.config'];
  }

  /**
   * Implements \Drupal\Core\Form\FormInterface::getFormID().
   */
  public function getFormId() {
    return 'headless_config_form';
  }

  /**
   * Implements \Drupal\Core\Form\FormInterface::buildForm().
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('headless.config');

    $form['routing'] = array(
      '#type' => 'details',
      '#title' => $this->t('Routing'),
      '#description' => $this->t('This is the publicly accessible path to User operation routes.'),
      '#open' => TRUE,
    );

    $routing_path = $config->get('routing_path');
    if (empty($routing_path)) {
      $routing_path = 'api';
    }

    $form['routing']['routing_path'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Path'),
      '#size' => 55,
      '#maxlength' => 55,
      '#default_value' => $routing_path,
      '#required' => TRUE,
      '#field_prefix' => $this->requestContext->getCompleteBaseUrl() . '/',
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * Implements \Drupal\Core\Form\FormInterface::validateForm().
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $routing_path = $form_state->getValue('routing_path');

    if ($routing_path) {
      $form_state->setValueForElement($form['routing']['routing_path'], $routing_path);
    }
    else {
      $form_state->setValueForElement($form['routing']['routing_path'], '/api');
    }

    if ($routing_path[0] == '/') {
      $form_state->setErrorByName('routing_path', $this->t("The path '%path' cannot start with a slash.", ['%path' => $routing_path]));
    }

    if (!UrlHelper::isValid($routing_path)) {
      $form_state->setErrorByName('routing_path',
        $this->t("The path '%path' is invalid or you do not have access to it.", array('%path' => $routing_path))
      );
    }
  }

  /**
   * Implements \Drupal\Core\Form\FormInterface::submitForm().
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('headless.config');
    $config->set('routing_path', $form_state->getValue('routing_path'));
    $config->save();

    drupal_set_message(t('Configuration saved successfully!'), 'status', FALSE);
  }
}

<?php

namespace Drupal\star_wars_apis\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Star Wars Movies' Block.
 *
 * @Block(
 *   id = "star_wars_movies_block",
 *   admin_label = @Translation("Star Wars Movies Block"),
 *   category = @Translation("Block to display list of Star Wars Movies"),
 * )
 */
class StarWarsMoviesList extends BlockBase implements BlockPluginInterface, ContainerFactoryPluginInterface {

  /**
   * Prepare StarWarsApiClient service object.
   *
   * @var \Drupal\star_wars_apis\StarWarsApiClient
   *   The StarWarsApiClient service.
   */
  protected $starWarsApiClient;

  /**
   * StarWarsMoviesList constructor.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\star_wars_apis\StarWarsApiClient $star_wars_api_client
   *   The star_wars_api_client instance.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, $star_wars_api_client) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->starWarsApiClient = $star_wars_api_client;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
          $configuration,
          $plugin_id,
          $plugin_definition,
          $container->get('star_wars_apis')
      );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();
    $items_count = $config['items_count'] ?? '';

    $movieList = $this->starWarsApiClient->movieList($items_count);

    return [
      '#theme' => 'star_war_movies',
      '#movies' => $movieList,
      '#attached' => [
        'library' => [
          'star_wars_apis/star-wars-movies',
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    // Retrieve existing configuration for this block.
    $config = $this->getConfiguration();

    // Add a form field to the existing block configuration form.
    $form['items_count'] = [
      '#type' => 'number',
      '#title' => $this->t('No of Movies to display'),
      '#default_value' => $config['items_count'] ?? '',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    // Save our custom settings when the form is submitted.
    $this->setConfigurationValue('items_count', $form_state->getValue('items_count'));
  }

  /**
   * {@inheritdoc}
   */
  public function blockValidate($form, FormStateInterface $form_state) {
    $items_count = $form_state->getValue('items_count');

    if (!is_numeric($items_count)) {
      $form_state->setErrorByName('items_count', $this->t('Needs to be an integer'));
    }
  }

}

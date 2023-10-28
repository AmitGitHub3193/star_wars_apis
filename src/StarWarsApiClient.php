<?php

namespace Drupal\star_wars_apis;

use Drupal\Component\Serialization\Json;

/**
 * Class for Star Wars Api related operations.
 */
class StarWarsApiClient {

  /**
   * Guzzle HTTP client object.
   *
   * @var \GuzzleHttp\Client
   *  The GuzzleHTTP client.
   */
  protected $client;

  /**
   * StarWarsApiClient constructor.
   *
   * @param \Drupal\Core\Http\ClientFactory $http_client_factory
   *   The http client factoty.
   */
  public function __construct($http_client_factory) {
    $this->client = $http_client_factory->fromOptions(
          [
            'base_uri' => 'https://swapi.dev/api/',
          ]
      );
  }

  /**
   * Get list of Star Wars Films.
   *
   * @param int $item_count
   *   Number of items to return.
   *
   * @return array
   *   Return movie list array.
   */
  public function movieList($item_count) {
    // Call films api.
    $response = $this->client->get('films', []);

    // Decode api response.
    $results = Json::decode($response->getBody());

    // Return api response.
    return ($results['results']) ? array_slice($results['results'], 0, $item_count) : [];
  }

}

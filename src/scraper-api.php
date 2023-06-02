<?php namespace Crawlbase;

require_once('base-api.php');

/**
 * A PHP class that acts as wrapper for Crawlbase Scraper API.
 *
 * Read Crawlbase API documentation https://crawlbase.com/docs/scraper-api/
 *
 * Copyright Crawlbase
 * Licensed under the Apache License 2.0
 */
class ScraperAPI extends BaseAPI {

  protected $basePath = 'scraper';

  public function get($url, array $options = array()) {
    $options['url'] = $url;
    return $this->request($options);
  }

  public function post($url, $data, array $options = array()) {
    throw new \Exception('POST is not supported on the Scraper API');
  }

  public function put($url, $data, array $options = array()) {
    throw new \Exception('PUT is not supported on the Scraper API');
  }

}

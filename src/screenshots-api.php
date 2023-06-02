<?php namespace Crawlbase;

require_once('base-api.php');

/**
 * A PHP class that acts as wrapper for Crawlbase Screenshots API.
 *
 * Read Crawlbase API documentation https://crawlbase.com/docs/screenshots-api
 *
 * Copyright Crawlbase
 * Licensed under the Apache License 2.0
 */
class ScreenshotsAPI extends BaseAPI {

  protected $basePath = 'screenshots';

  public function get($url, array $options = array()) {
    $options['url'] = $url;
    $callback = null;
    $saveToPath = null;
    if (array_key_exists('saveToPath', $options)) {
      if (!preg_match("/.+\.(jpg|JPG|jpeg|JPEG)$/i", $options['saveToPath'])) {
        throw new \Exception('saveToPath must end with .jpg or .jpeg');
      }
      $saveToPath = $options['saveToPath'];
      unset($options['saveToPath']);
    }
    if (array_key_exists('callback', $options) && is_callable($options['callback'])) {
      $callback = $options['callback'];
      unset($options['callback']);
    }
    $response = $this->request($options);
    if ($callback) {
      $filename = ($saveToPath !== null) ? $saveToPath : $this->generateFilename();
      file_put_contents($filename, $response->body);
      $callback($filename);
    }
    return $response;
  }

  private function generateFilename() {
    $tempName = tempnam(sys_get_temp_dir(), 'crawlbase-screenshot-');
    return "$tempName.jpg";
  }
}

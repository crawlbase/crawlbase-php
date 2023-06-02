<?php namespace Crawlbase;

require_once('base-api.php');

/**
 * A PHP class that acts as wrapper for Crawlbase Leads API.
 *
 * Read Crawlbase API documentation https://crawlbase.com/docs/leads-api/
 *
 * Copyright Crawlbase
 * Licensed under the Apache License 2.0
 */
class LeadsAPI extends BaseAPI {

  protected $basePath = 'leads';

  public function getFromDomain($domain, array $options = array()) {
    if (empty($domain)) {
      throw new \Exception('Domain must be provided');
    }   
    $options['domain'] = $domain;
    return $this->request($options);
  }

}

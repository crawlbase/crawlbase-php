# Crawlbase API PHP class

A lightweight, dependency free PHP class that acts as wrapper for Crawlbase API.

## Installing

Choose a way of installing:

- Use [Packagist](https://packagist.org/packages/crawlbase/crawlbase) PHP package manager.
- Download the project from Github and save it into your project so you can require it `require_once('crawlbase-php/src/[class].php')`

## Crawling API

First initialize the CrawlingAPI class. You can [get your free token here](https://crawlbase.com/signup?signup=github).

```php
$api = new Crawlbase\CrawlingAPI(['token' => 'YOUR_TOKEN']);
```

### GET requests

Pass the url that you want to scrape plus any options from the ones available in the [API documentation](https://crawlbase.com/docs/crawling-api/).

```php
$api->get(string $url, array $options = []);
```

Example:

```php
$response = $api->get('https://www.facebook.com/britneyspears');
if ($response->statusCode === 200) {
  echo $response->body;
}
```

You can pass any options from Crawlbase API.

Example:

```php
$response = $api->get('https://www.reddit.com/r/pics/comments/5bx4bx/thanks_obama/', [
  'user_agent' => 'Mozilla/5.0 (Windows NT 6.2; rv:20.0) Gecko/20121202 Firefox/30.0',
  'format' => 'json'
]);
if ($response->statusCode === 200) {
  echo $response->body;
}
```

Optionally pass [store](https://crawlbase.com/docs/crawling-api/parameters/#store) parameter to `true` to store a copy of the API response in the [Crawlbase Cloud Storage](https://crawlbase.com/dashboard/storage).

Example:

```php
$response = $api->get('https://www.reddit.com/r/pics/comments/5bx4bx/thanks_obama/', [
  'store' => true
]);

if ($response->statusCode === 200) {
  echo 'storage url: ' . $response->headers->storage_url . PHP_EOL;
}
```

### POST requests

Pass the url that you want to scrape, the data that you want to send which can be either a json or a string, plus any options from the ones available in the [API documentation](https://crawlbase.com/docs/crawling-api/).

```php
$api->post(string $url, array or string $data, array options = []);
```

Example:

```php
$response = $api->post('https://producthunt.com/search', ['text' => 'example search']);
if ($response->statusCode === 200) {
  echo $response->body;
}
```

You can send the data as `application/json` instead of `x-www-form-urlencoded` by setting option `post_content_type` as json.

```php
$response = $api->post('https://httpbin.org/post', json_encode(['some_json' => 'with some value']), ['post_content_type' => 'json']);
if ($response->statusCode === 200) {
  echo $response->body;
}
```

### PUT requests

Pass the url that you want to scrape, the data that you want to send which can be either a json or a string, plus any options from the ones available in the [API documentation](https://crawlbase.com/docs/crawling-api/).

```php
$api->put(string $url, array or string $data, array options = []);
```

Example:

```php
$response = $api->put('https://producthunt.com/search', ['text' => 'example search']);
if ($response->statusCode === 200) {
  echo $response->body;
}
```

### Javascript requests

If you need to scrape any website built with Javascript like React, Angular, Vue, etc. You just need to pass your javascript token and use the same calls. Note that only `->get` is available for javascript and not `->post`.

```php
$api = new Crawlbase\CrawlingAPI(['token' => 'YOUR_JAVASCRIPT_TOKEN']);
```

```php
$response = $api->get('https://www.nfl.com');
if ($response->statusCode === 200) {
  echo $response->body;
}
```

Same way you can pass javascript additional options.

```php
$response = $api->get('https://www.freelancer.com', ['page_wait' => 5000]);
if ($response->statusCode === 200) {
  echo $response->body;
}
```

## Original status

You can always get the original status and Crawlbase status from the response. Read the [Crawlbase documentation](https://crawlbase.com/docs/crawling-api/) to learn more about those status.

Prefer `cb_status` for the Crawlbase status. `pc_status` is deprecated but still supported temporarily as an alias of the same resolved value. When both headers are present, `cb_status` takes priority.

```php
$response = $api->get('https://craiglist.com');
echo $response->headers->original_status . PHP_EOL;
echo $response->headers->cb_status . PHP_EOL;
```

### Migrating from `pc_status` to `cb_status`

```php
// Before (deprecated)
echo $response->headers->pc_status;

// After (preferred)
echo $response->headers->cb_status;
```

During the deprecation period, both properties remain available and are kept in sync by the library.

## Scraper API

> ⚠️ **Deprecated.** The standalone Scraper API has been closed to new sign-ups since October 1, 2024. Existing integrations continue to work and no shutdown is scheduled, but new code should use the Crawling API with the `scraper` parameter instead (same scrapers, simpler endpoint, more parameters). The class below stays available for backward compatibility. See the [scrapers documentation](https://crawlbase.com/docs/scrapers).

First initialize the ScraperAPI class. You can [get your free token here](https://crawlbase.com/signup?signup=github). Please note that only some websites are supported, check the [API documentation](https://crawlbase.com/docs/scraper-api/) for more information.

```php
$api = new Crawlbase\ScraperAPI(['token' => 'YOUR_TOKEN']);
```

Pass the url that you want to scrape plus any options from the ones available in the [API documentation](https://crawlbase.com/docs/scraper-api/).

Example:

```php
$response = $api->get('https://www.amazon.com/DualSense-Wireless-Controller-PlayStation-5/dp/B08FC6C75Y/');
echo 'status code: ' . $response->statusCode . PHP_EOL;
if ($response->statusCode === 200) {
  var_dump($response->json); // Will print scraped Amazon details
}
```

## Leads API

> ⚠️ **Deprecated.** The Leads API has been closed to new sign-ups since October 1, 2024. Existing integrations continue to work and no shutdown is scheduled. There is no direct replacement; for similar workflows use the Crawling API with the [`email-extractor`](https://crawlbase.com/docs/scrapers/email-extractor) scraper (any URL → emails) or the [`google-serp`](https://crawlbase.com/docs/scrapers/google-serp) scraper for domain-scoped contact discovery. The class below stays available for backward compatibility.

First initialize the LeadsAPI class. You can [get your free token here](https://crawlbase.com/signup?signup=github).

```php
$api = new Crawlbase\LeadsAPI(['token' => 'YOUR_TOKEN']);
```

Pass the domain where you want to search for leads.

Example:

```php
$response = $api->getFromDomain('target.com');
if ($response->statusCode === 200) {
  foreach ($response->json->leads as $key => $lead) {
    echo $lead->email . PHP_EOL;
  }
}
```

## Screenshots API usage

> ⚠️ **Deprecated.** The standalone Screenshots API has been closed to new sign-ups since November 1, 2024. Existing integrations continue to work and no shutdown is scheduled, but new code should use the Crawling API with the `screenshot=true` parameter — same JS-rendering pipeline, screenshot parameters on the standard endpoint. The class below stays available for backward compatibility. See the [Crawling API screenshots section](https://crawlbase.com/docs/crawling-api#screenshots).

Initialize with your Screenshots API token and call the `get` method.

```php
$api = new Crawlbase\ScreenshotsAPI(['token' => 'YOUR_TOKEN']);
$response = $api->get('https://www.apple.com');
echo 'success: ' . $response->headers->success . PHP_EOL;
echo 'remaining requests: ' . $response->headers->remaining_requests . PHP_EOL;
file_put_contents('apple.jpg', $response->body);
```

or you can specify a callback that automatically saves the file to the temporary folder

```php
$api = new Crawlbase\ScreenshotsAPI(['token' => 'YOUR_TOKEN']);
$response = $api->get('https://www.apple.com', [
  'callback' => function($filepath) {
    echo 'filepath: ' . $filepath . PHP_EOL;
  }
]);
echo 'success: ' . $response->headers->success . PHP_EOL;
echo 'remaining requests: ' . $response->headers->remaining_requests . PHP_EOL;
```

or specifying a file path via `saveToPath` option

```php
$api = new Crawlbase\ScreenshotsAPI(['token' => 'YOUR_TOKEN']);
$response = $api->get('https://www.apple.com', [
  'saveToPath' => 'apple.jpg',
  'callback' => function($filepath) {
    echo 'filepath: ' . $filepath . PHP_EOL;
  }
]);
echo 'success: ' . $response->headers->success . PHP_EOL;
echo 'remaining requests: ' . $response->headers->remaining_requests . PHP_EOL;
```

Note that `$api.get(url, options)` method accepts an [options](https://crawlbase.com/docs/screenshots-api/parameters)

## Smart AI Proxy usage

The [Smart AI Proxy](https://crawlbase.com/docs/smart-proxy) is a standard rotating HTTP(S) proxy endpoint, so it needs no SDK: point any HTTP client at `smartproxy.crawlbase.com:8012` (HTTP) or `smartproxy.crawlbase.com:8013` (HTTPS) with your token as the proxy username and an empty password. Crawlbase handles proxy rotation, retries and anti-bot bypass on its side.

```php
$ch = curl_init('https://httpbin.org/ip');
curl_setopt($ch, CURLOPT_PROXY, 'https://smartproxy.crawlbase.com:8013');
curl_setopt($ch, CURLOPT_PROXYUSERPWD, 'YOUR_TOKEN:');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
echo curl_exec($ch);
```

Note: the proxy re-signs HTTPS traffic, so certificate verification must be disabled on the client (as in the example). See the [Smart AI Proxy documentation](https://crawlbase.com/docs/smart-proxy) for all options.

## Storage API usage

Initialize the Storage API using your private token.

```php
$api = new Crawlbase\StorageAPI(['token' => 'YOUR_TOKEN']);
```

Pass the [url](https://crawlbase.com/docs/storage-api/parameters/#url) that you want to get from [Crawlbase Storage](https://crawlbase.com/dashboard/storage).

```php
$response = $api->get('https://www.apple.com');

echo 'status code: ' . $response->statusCode . PHP_EOL;
if ($response->statusCode === 200) {
  echo 'body: ' . $response->body . PHP_EOL;
  echo 'original status: ' . $response->headers->original_status . PHP_EOL;
  echo 'crawlbase status: ' . $response->headers->cb_status . PHP_EOL;
  echo 'rid: ' . $response->headers->rid . PHP_EOL;
  echo 'url: ' . $response->headers->url . PHP_EOL;
  echo 'stored date: ' . $response->headers->stored_at . PHP_EOL;
}
```

or you can use the [RID](https://crawlbase.com/docs/storage-api/parameters/#rid)

```php
$response = $api->get('RID_REPLACE');

echo 'status code: ' . $response->statusCode . PHP_EOL;
if ($response->statusCode === 200) {
  echo 'body: ' . $response->body . PHP_EOL;
  echo 'original status: ' . $response->headers->original_status . PHP_EOL;
  echo 'crawlbase status: ' . $response->headers->cb_status . PHP_EOL;
  echo 'rid: ' . $response->headers->rid . PHP_EOL;
  echo 'url: ' . $response->headers->url . PHP_EOL;
  echo 'stored date: ' . $response->headers->stored_at . PHP_EOL;
}
```

Note: One of the two RID or URL must be sent. So both are optional but it's mandatory to send one of the two.

### [Delete](https://crawlbase.com/docs/storage-api/delete/) request

To delete a storage item from your storage area, use the correct RID

```php
if ($api->delete('RID_REPLACE')) {
  echo 'delete success' . PHP_EOL;
  echo 'status code: ' . $api->response->statusCode . PHP_EOL;
} else {
  echo 'delete failed' . PHP_EOL;
  echo 'status code: ' . $api->response->statusCode . PHP_EOL;
}
```

### [Bulk](https://crawlbase.com/docs/storage-api/bulk/) request

To do a bulk request with a list of RIDs, please send the list of rids as an array

```php
$items = $api->bulk(['RID1', 'RID2', 'RID3', ...]);
foreach ($items as $item) {
  echo 'body: ' . $item->body . PHP_EOL;
  echo 'stored at: ' . $item->stored_at . PHP_EOL;
  echo 'original status: ' . $item->original_status . PHP_EOL;
  echo 'crawlbase status: ' . $item->cb_status . PHP_EOL;
  echo 'rid: ' . $item->rid . PHP_EOL;
  echo 'url: ' . $item->url . PHP_EOL;
  echo PHP_EOL;
}
```

### [RIDs](https://crawlbase.com/docs/storage-api/rids) request

To request a bulk list of RIDs from your storage area

```php
$rids = $api->rids();
foreach ($rids as $rid) {
  echo $rid . PHP_EOL;
}
```

You can also specify a limit as a parameter

```php
$rids = $api->rids(10);
```

### [Total Count](https://crawlbase.com/docs/storage-api/total_count)

To get the total number of documents in your storage area

```php
$totalCount = $api->totalCount();
echo 'total count: ' . $totalCount . PHP_EOL;
```

If you have questions or need help using the library, please open an issue or [contact us](https://crawlbase.com/contact).

---

Copyright 2026 Crawlbase

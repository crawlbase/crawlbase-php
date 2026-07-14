<?php
/**
 * Offline unit tests for cb_status / pc_status normalization.
 * Run: php tests/status-normalization-test.php
 */

require_once dirname(__DIR__) . '/src/base-api.php';

class StatusNormalizationTestAPI extends Crawlbase\BaseAPI {
  public function __construct() {
    // Skip real token validation for offline tests.
  }

  public function exposeNormalizeHeaders(array $headers) {
    $this->response = array('headers' => $headers);
    $this->normalizeCrawlbaseStatusHeaders();
    return $this->response['headers'];
  }

  public function exposeNormalizeObject($object) {
    return $this->normalizeCrawlbaseStatusObject($object);
  }

  public function exposeParseJsonBody($body) {
    $this->response = array(
      'headers' => array(),
      'body' => $body,
    );
    $this->parseJsonResponse();
    $this->normalizeCrawlbaseStatusHeaders();
    return $this->response['headers'];
  }
}

$api = new StatusNormalizationTestAPI();
$passed = 0;
$failed = 0;

function assert_true($condition, $message) {
  global $passed, $failed;
  if ($condition) {
    echo "PASS: $message\n";
    $passed++;
  } else {
    echo "FAIL: $message\n";
    $failed++;
  }
}

// 1. Response contains only cb_status
$headers = $api->exposeNormalizeHeaders(array('cb_status' => 200));
assert_true(isset($headers['cb_status']) && $headers['cb_status'] === 200, 'only cb_status sets cb_status');
assert_true(isset($headers['pc_status']) && $headers['pc_status'] === 200, 'only cb_status aliases pc_status');

// 2. Response contains only pc_status
$headers = $api->exposeNormalizeHeaders(array('pc_status' => 404));
assert_true(isset($headers['cb_status']) && $headers['cb_status'] === 404, 'only pc_status sets cb_status');
assert_true(isset($headers['pc_status']) && $headers['pc_status'] === 404, 'only pc_status keeps pc_status');

// 3. Both present — cb_status takes priority
$headers = $api->exposeNormalizeHeaders(array('cb_status' => 200, 'pc_status' => 500));
assert_true($headers['cb_status'] === 200, 'both present: cb_status wins on cb_status');
assert_true($headers['pc_status'] === 200, 'both present: resolved value mirrored to pc_status');

// 4. Neither header
$headers = $api->exposeNormalizeHeaders(array('url' => 'https://example.com'));
assert_true(!isset($headers['cb_status']), 'neither present: cb_status unset');
assert_true(!isset($headers['pc_status']), 'neither present: pc_status unset');

// 5. Legacy pc_status behavior must keep working (via alias from cb-only and pc-only)
$headers = $api->exposeNormalizeHeaders(array('pc_status' => 301));
assert_true($headers['pc_status'] === 301, 'legacy: pc_status still readable when only pc_status received');
$headers = $api->exposeNormalizeHeaders(array('cb_status' => 302));
assert_true($headers['pc_status'] === 302, 'legacy: pc_status still readable when only cb_status received');

// Empty string treated as absent
$headers = $api->exposeNormalizeHeaders(array('cb_status' => '', 'pc_status' => 200));
assert_true($headers['cb_status'] === 200 && $headers['pc_status'] === 200, 'empty cb_status falls back to pc_status');

// Numeric string cast
$headers = $api->exposeNormalizeHeaders(array('cb_status' => '200'));
assert_true($headers['cb_status'] === 200 && is_int($headers['cb_status']), 'numeric string cb_status cast to int');

// JSON path: only cb_status
$headers = $api->exposeParseJsonBody(json_encode(array('cb_status' => 200, 'url' => 'https://example.com')));
assert_true(isset($headers['cb_status']) && $headers['cb_status'] === 200, 'JSON only cb_status sets cb_status');
assert_true(isset($headers['pc_status']) && $headers['pc_status'] === 200, 'JSON only cb_status aliases pc_status');

// JSON path: only pc_status (with original_status for typical API shape)
$headers = $api->exposeParseJsonBody(json_encode(array(
  'original_status' => 200,
  'pc_status' => 404,
  'url' => 'https://example.com',
)));
assert_true($headers['cb_status'] === 404 && $headers['pc_status'] === 404, 'JSON only pc_status resolves both');
assert_true(isset($headers['original_status']) && $headers['original_status'] === 200, 'JSON original_status still copied');

// JSON path: both — cb wins
$headers = $api->exposeParseJsonBody(json_encode(array(
  'cb_status' => 200,
  'pc_status' => 500,
  'original_status' => 200,
  'url' => 'https://example.com',
)));
assert_true($headers['cb_status'] === 200 && $headers['pc_status'] === 200, 'JSON both: cb_status wins');

// Bulk item object normalization
$item = (object) array('pc_status' => 403, 'rid' => 'abc');
$item = $api->exposeNormalizeObject($item);
assert_true($item->cb_status === 403 && $item->pc_status === 403, 'bulk object: only pc_status aliases both');

$item = (object) array('cb_status' => 200, 'pc_status' => 500, 'rid' => 'abc');
$item = $api->exposeNormalizeObject($item);
assert_true($item->cb_status === 200 && $item->pc_status === 200, 'bulk object: cb_status wins');

$item = (object) array('rid' => 'abc');
$item = $api->exposeNormalizeObject($item);
assert_true(!isset($item->cb_status) && !isset($item->pc_status), 'bulk object: neither leaves unset');

$nonObject = 'skip';
assert_true($api->exposeNormalizeObject($nonObject) === 'skip', 'non-object bulk value left unchanged');

echo "\nPassed: $passed, Failed: $failed\n";
exit($failed > 0 ? 1 : 0);

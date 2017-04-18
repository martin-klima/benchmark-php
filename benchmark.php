<?php

/**
 * PHP Script to benchmark PHP, Filesystem and MySQL-Server.
 *
 * No HTML output. Optimized for run from command line.
 *
 * inspired by / thanks to:
 * - www.php-benchmark-script.com  (Alessandro Torrisi)
 * - www.webdesign-informatik.de
 * - https://github.com/odan/benchmark-php, odan
 *
 * @author Martin Klíma, martin.klima@hqis.cz
 * @license MIT
 */
// -----------------------------------------------------------------------------
// Setup
// -----------------------------------------------------------------------------
set_time_limit(360); // 6 minutes
define('COUNTS', 100000);

$options = array();

// -----------------------------------------------------------------------------
// Main
// -----------------------------------------------------------------------------
// Get config if exists.
if (is_readable('config.php')) {
  include('config.php');
}
$benchmarkResult = test_benchmark($options);
// check performance
print("Results:\n");
print_r($benchmarkResult);

exit;

// -----------------------------------------------------------------------------
// Benchmark functions
// -----------------------------------------------------------------------------

function test_benchmark($settings) {
  $timeStart = microtime(TRUE);

  $result = array();
  $result['version'] = '1.2';
  $result['sysinfo']['time'] = date("Y-m-d H:i:s");
  $result['sysinfo']['php_version'] = PHP_VERSION;
  $result['sysinfo']['platform'] = PHP_OS;

  test_math($result, COUNTS);
  test_string($result, COUNTS);
  test_loops($result, COUNTS * 10);
  test_ifelse($result, COUNTS * 10);
  test_filesystem($result, COUNTS / 10);
  if (isset($settings['db.host'])) {
    test_mysql($result, $settings);
  }
  else {
    print "Copy example.config.php to config.php and set MySQL configuration for test MySQL. \n";
  }

  $result['total'] = timer_diff($timeStart);
  return $result;
}

function test_math(&$result, $count = COUNTS) {
  $timeStart = microtime(TRUE);

  $mathFunctions = array(
    "abs",
    "acos",
    "asin",
    "atan",
    "bindec",
    "floor",
    "exp",
    "sin",
    "tan",
    "pi",
    "is_finite",
    "is_nan",
    "sqrt"
  );
  for ($i = 0; $i < $count; $i++) {
    foreach ($mathFunctions as $function) {
      call_user_func_array($function, array($i));
    }
  }
  $result['benchmark']['math'] = timer_diff($timeStart);
}

function test_string(&$result, $count = COUNTS) {
  $timeStart = microtime(TRUE);
  $stringFunctions = array(
    "addslashes",
    "chunk_split",
    "metaphone",
    "strip_tags",
    "md5",
    "sha1",
    "strtoupper",
    "strtolower",
    "strrev",
    "strlen",
    "soundex",
    "ord"
  );

  $string = 'the quick brown fox jumps over the lazy dog';
  for ($i = 0; $i < $count; $i++) {
    foreach ($stringFunctions as $function) {
      call_user_func_array($function, array($string));
    }
  }
  $result['benchmark']['string'] = timer_diff($timeStart);
}

function test_loops(&$result, $count = COUNTS) {
  $timeStart = microtime(TRUE);
  for ($i = 0; $i < $count; ++$i) {

  }
  $i = 0;
  while ($i < $count) {
    ++$i;
  }
  $result['benchmark']['loops'] = timer_diff($timeStart);
}

function test_ifelse(&$result, $count = COUNTS) {
  $timeStart = microtime(TRUE);
  for ($i = 0; $i < $count; $i++) {
    if ($i == -1) {

    }
    elseif ($i == -2) {

    }
    else {
      if ($i == -3) {

      }
    }
  }
  $result['benchmark']['ifelse'] = timer_diff($timeStart);
}

function test_filesystem(&$result, $count = COUNTS) {
  $timeStart = microtime(TRUE);
  // Create a test file.
  $tmp_file_name = 'benchmark.tmp';
  file_put_contents($tmp_file_name, '0123456789');
  $tmp = file_get_contents($tmp_file_name);
  // Create many files;
  for ($i = 0; $i < $count; $i++) {
    file_put_contents($i . '-' . $tmp_file_name, $tmp);
  }
  $result['benchmark']['fs-create'] = timer_diff($timeStart);
  // Read from many files.
  $tmp = '';
  for ($i = 0; $i < $count; $i++) {
    $tmp .= file_get_contents($i . '-' . $tmp_file_name);
  }
  $result['benchmark']['fs-read'] = timer_diff($timeStart);

  // Rename files.
  for ($i = 0; $i < $count; $i++) {
    rename($i . '-' . $tmp_file_name, $i . '-renamed-' . $tmp_file_name);
  }
  $result['benchmark']['fs-rename'] = timer_diff($timeStart);

  // Delete many files.
  for ($i = 0; $i < $count; $i++) {
    unlink($i . '-renamed-' . $tmp_file_name);
  }
  unlink('benchmark.tmp');
  $result['benchmark']['fs-delete'] = timer_diff($timeStart);
}

function test_mysql(&$result, $settings) {
  $timeStart = microtime(TRUE);

  $link = mysqli_connect($settings['db.host'], $settings['db.user'], $settings['db.pw']);
  $result['benchmark']['mysql']['connect'] = timer_diff($timeStart);

  mysqli_select_db($link, $settings['db.name']);
  $result['benchmark']['mysql']['select_db'] = timer_diff($timeStart);

  $dbResult = mysqli_query($link, 'SELECT VERSION() as version;');
  $arr_row = mysqli_fetch_array($dbResult);
  $result['sysinfo']['mysql_version'] = $arr_row['version'];
  $result['benchmark']['mysql']['query_version'] = timer_diff($timeStart);

  $query = "SELECT BENCHMARK(1000000,ENCODE('hello',RAND()));";
  $dbResult = mysqli_query($link, $query);
  $result['benchmark']['mysql']['query_benchmark'] = timer_diff($timeStart);

  mysqli_close($link);

  $result['benchmark']['mysql']['total'] = timer_diff($timeStart);
  return $result;
}

function timer_diff($timeStart) {
  return number_format(microtime(TRUE) - $timeStart, 3);
}



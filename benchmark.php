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

$options = [];

// -----------------------------------------------------------------------------
// Main
// -----------------------------------------------------------------------------
// Get config if exists.
/*if (is_readable('config.php')) {
  include('config.php');
}*/
while (ob_get_level()) {
  ob_end_clean();
}
ob_start();
header("Content-Encoding: None", TRUE);
header("Content-Type: text/plain");
print('Benchmark is running... wait for results.');
ob_end_flush();
flush();

$benchmarkResult = test_benchmark($options);
// check performance
print("\nResults are in seconds:\n");
print_r($benchmarkResult);

print("Table formatted results\n");
print("\nSystem info\n");
print(implode('|', array_keys($benchmarkResult['sysinfo'])));
print("\n");
print(implode('|', $benchmarkResult['sysinfo']));
print("\n\nBenchmark PHP\n");
print(implode('|', array_keys($benchmarkResult['benchmark'])));
print("\n");
print(implode('|', $benchmarkResult['benchmark']));
print("\n\nBenchmark filesystem for " . COUNTS . " files\n");
print(implode('|', array_keys($benchmarkResult['benchmark_fs'])));
print("\n");
print(implode('|', $benchmarkResult['benchmark_fs']));
print("\n\nTotal\n");
print($benchmarkResult['total']);
print("\n\nOutput for Copy&Paste to Google Sheets:\n");
print("[YOUR-NAME]\t[SERVER-CONFIG]\t" . $benchmarkResult['sysinfo']['php_version'] .
    "\t".$benchmarkResult['sysinfo']['platform']."\t" .
    str_replace(".", ",", implode("\t", $benchmarkResult['benchmark'])) . "\t" .
    str_replace(".", ",", implode("\t", $benchmarkResult['benchmark_fs'])) . "\t" .
    str_replace(".", ",", $benchmarkResult['total']) . "\n");
print("\n");

exit;

// -----------------------------------------------------------------------------
// Benchmark functions
// -----------------------------------------------------------------------------

function test_benchmark($settings) {
  $timeStart = microtime(TRUE);

  $result = [];
  $result['version'] = '1.21';
  $result['counts'] = COUNTS;
  $result['sysinfo']['time'] = date("Y-m-d H:i:s");
  $result['sysinfo']['php_version'] = PHP_VERSION;
  $result['sysinfo']['platform'] = PHP_OS;

  test_math($result, COUNTS * 5);
  test_string($result, COUNTS * 5);
  test_loops($result, COUNTS / 20);
  test_ifelse($result, COUNTS * 100);
  test_rand_array($result, COUNTS * 2);
  $result['benchmark']['total'] = timer_diff($timeStart);

  test_filesystem($result, COUNTS / 10);

  $result['total'] = timer_diff($timeStart);
  return $result;
}

function test_math(&$result, $count = COUNTS) {
  $timeStart = microtime(TRUE);

  $mathFunctions = [
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
    "sqrt",
  ];
  for ($i = 0; $i < $count; $i++) {
    foreach ($mathFunctions as $function) {
      $mathResult = call_user_func_array($function, [$i]);
    }
  }
  $result['benchmark']['math'] = timer_diff($timeStart);
}

function test_string(&$result, $count = COUNTS) {
  $timeStart = microtime(TRUE);
  $stringFunctions = [
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
    "ord",
  ];

  $string = 'the quick brown fox jumps over the lazy dog';
  for ($i = 0; $i < $count; $i++) {
    foreach ($stringFunctions as $function) {
      $mathResult = call_user_func_array($function, [$string . $i]);
    }
  }
  $result['benchmark']['string'] = timer_diff($timeStart);
}

function test_loops(&$result, $count = COUNTS) {
  $timeStart = microtime(TRUE);
  $var = 0;
  for ($i = 0; $i < $count; ++$i) {
    for ($y = 0; $y < $count; ++$y) {
      $var += $i - $y;
    }
  }
  $i = 0;
  while ($i < $count) {
    ++$i;
  }
  $result['benchmark']['loops'] = timer_diff($timeStart);
}

function test_ifelse(&$result, $count = COUNTS) {
  $timeStart = microtime(TRUE);
  $tmp = 0;
  for ($i = 0; $i < $count; $i++) {
    if ($i % 2 == 0) {
      $tmp += 1;
    }
    elseif ($i % 3 == 0) {
      $tmp += 2;
    }
    elseif ($i % 5 == 0) {
      $tmp += 3;
    }
    else {
      $tmp += 0;
    }
  }
  $result['benchmark']['ifelse'] = timer_diff($timeStart);
}

function test_rand_array(&$result, $count = COUNTS) {
  $timeStart = microtime(TRUE);
  for ($ii = 0; $ii < 5; $ii++) {
    $testArray = [];
    for ($i = 0; $i < $count; $i++) {
      $rnd = mt_rand(-999999, 999999);
      $testArray[] = $rnd;
    }

    $uniqueArray = array_unique($testArray);
    $sum = array_sum($testArray);
    $tmp = array_flip($testArray);
    unset($testArray, $sum, $tmp);
  }
  $result['benchmark']['rand_array'] = timer_diff($timeStart);
}

function test_filesystem(&$result, $count = COUNTS) {
  $timeStart0 = microtime(TRUE);
  // Create a test file.
  $tmp_file_name = 'benchmark.tmp';
  file_put_contents($tmp_file_name, '0123456789');
  $tmp = file_get_contents($tmp_file_name);
  // Create many files;
  for ($i = 0; $i < $count; $i++) {
    file_put_contents($i . '-' . $tmp_file_name, $tmp . $i);
  }
  $result['benchmark_fs']['create'] = timer_diff($timeStart0);

  // Read from many files.
  $timeStart = microtime(TRUE);
  $tmp = '';
  for ($i = 0; $i < $count; $i++) {
    $tmp .= file_get_contents($i . '-' . $tmp_file_name);
  }
  $result['benchmark_fs']['read'] = timer_diff($timeStart);

  // Rename files.
  $timeStart = microtime(TRUE);
  for ($i = 0; $i < $count; $i++) {
    rename($i . '-' . $tmp_file_name, $i . '-renamed-' . $tmp_file_name);
  }
  $result['benchmark_fs']['rename'] = timer_diff($timeStart);

  // Delete many files.
  $timeStart = microtime(TRUE);
  for ($i = 0; $i < $count; $i++) {
    unlink($i . '-renamed-' . $tmp_file_name);
  }
  unlink('benchmark.tmp');
  $result['benchmark_fs']['delete'] = timer_diff($timeStart);
  $result['benchmark_fs']['total'] = timer_diff($timeStart0);
}

function timer_diff($timeStart) {
  return number_format(microtime(TRUE) - $timeStart, 3);
}



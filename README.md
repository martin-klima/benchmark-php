benchmark-php
=============

This is a PHP benchmark script to compare the runtime speed of PHP and filesystem. 
Forked from https://github.com/odan/benchmark-php.

I extended scripts by filesystem tests (create, read from, rename and delete many files).
It can be useful for testing performance of Vagrant filesystem in a synced folders.

Filesystem tests contains these operations performed:
- create 100k small files
- read content from all files
- rename all files
- delete all files

# Use

Clone/Upload benchmark.php an execute it in browser:
    
    http://www.example.com/benchmark.php

Clone/Upload and run the script remotely or from the command line:

    php benchmark.php

Note: Filesystem test needs to be in directory with read/write permission.

# Setup (optional)

If you need, you can set the hardness of the tests in the COUNTS constant.

```php
define('COUNTS', 100000);
```

Results are displayed as array, for example:

    Array
    (
        [version] => 1.21
        [counts] => 100000
        [sysinfo] => Array
            (
                [time] => 2018-05-02 09:57:58
                [php_version] => 7.0.26
                [platform] => Linux
            )
    
        [benchmark] => Array
            (
                [math] => 3.124
                [string] => 3.937
                [loops] => 1.852
                [ifelse] => 1.598
                [rand_array] => 3.696
                [total] => 14.208
            )
    
        [benchmark_fs] => Array
            (
                [create] => 0.301
                [read] => 0.092
                [rename] => 0.115
                [delete] => 0.097
                [total] => 0.605
            )
    
        [total] => 14.812
    )

and in table format

    System info
    time|php_version|platform
    2018-05-02 09:57:58|7.0.26|Linux
    
    Benchmark PHP
    math|string|loops|ifelse|rand_array|total
    3.124|3.937|1.852|1.598|3.696|14.208
    
    Benchmark filesystem for 100000 files
    create|read|rename|delete|total
    0.301|0.092|0.115|0.097|0.605
    
    Total
    14.812

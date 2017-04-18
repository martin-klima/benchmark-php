benchmark-php
=============

This is a PHP benchmark script to compare the runtime speed of PHP, filesystem 
and MySQL. This project is inspired by www.php-benchmark-script.com (Alessandro Torrisi) 
an www.webdesign-informatik.de. Forked from https://github.com/odan/benchmark-php.

I extended scripts by filesystem tests (create, read from, rename and delete many files).
It can be useful for testing performance of Vagrant filesystem in a synced folders. 

# Setup

Upload benchmark.php an execute it:<br>
http://www.example.com/benchmark.php


# MySQL Setup (optional)

Copy example.config.php to config.php and edit these lines.

```php
$options['db.host'] = 'hostname';
$options['db.user'] = 'username';
$options['db.pw'] = 'password';
$options['db.name'] = 'database';
```

Upload and run the script remotely or from the command line:

    php benchmark.php

Results are displayed as array, for example:

    Results:
    Array
    (
        [version] => 1.2
        [sysinfo] => Array
            (
                [time] => 2017-04-18 08:17:06
                [php_version] => 7.0.11
                [platform] => WINNT
            )
    
        [benchmark] => Array
            (
                [math] => 0.316
                [string] => 2.300
                [loops] => 0.025
                [ifelse] => 0.038
                [fs-create] => 4.018
                [fs-read] => 5.865
                [fs-rename] => 10.454
                [fs-delete] => 11.903
            )
    
        [total] => 14.587
    )
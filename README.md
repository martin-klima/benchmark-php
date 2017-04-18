benchmark-php
=============

This is a PHP benchmark script to compare the runtime speed of PHP, filesystem 
and MySQL. This project is inspired by www.php-benchmark-script.com (Alessandro Torrisi) 
an www.webdesign-informatik.de. Forked from https://github.com/odan/benchmark-php.

I extended scripts by filesystem tests (create, read from, rename and delete many files).
It can be useful for testing performance of Vagrant filesystem in a synced folders. 

# Use

Upload benchmark.php an execute it:<br>
http://www.example.com/benchmark.php

# Setup (optional)

If you need, you can set the hardness of the tests in the COUNTS constant.

```php
define('COUNTS', 100000);
```



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
                [time] => 2017-04-18 09:32:42
                [php_version] => 7.0.11
                [platform] => WINNT
            )
    
        [benchmark] => Array
            (
                [math] => 0.255
                [string] => 2.252
                [loops] => 0.024
                [ifelse] => 0.038
                [fs-operation] => Array
                    (
                        [create] => 4.898
                        [read] => 1.767
                        [rename] => 4.362
                        [delete] => 2.203
                        [total] => 13.230
                    )
                [filesystem] => 13.230
            )
        [total] => 15.801
    )
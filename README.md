php-glue
========
[![Build Status](https://travis-ci.org/iborodikhin/php-glue.png?branch=master)](https://travis-ci.org/iborodikhin/php-glue)

Files storage scheme which «glues» couple of small files into several BLOBs


Usage
------
Create Glue instance:
```php
$glue = new \Glue\Glue('/srv/data', 1); // creates 16 BLOBs in /srv/data
$glue->save('test_file', str_repeat('a', PHP_INT_MAX));
echo $glue->read('test_file');
$glue->delete('test_file');
$glue->compact();
```
Be sure data directory is writable by php process user.

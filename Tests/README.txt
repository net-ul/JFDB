wget https://phar.phpunit.de/phpunit-5.2.phar for PHP 5.6 ..

composer install --dev

composer require --dev phpunit/dbunit

composer require --dev phpunit/phpunit

(https://phpunit.de/getting-started.html)
./phpunit --bootstrap ../vendor/autoload.php TestTest

./Tests/phpunit --bootstrap vendor/autoload.php Tests/StorageTest.php

phpunit --bootstrap vendor/autoload.php Tests/StorageTest.php
phpunit --bootstrap vendor/autoload.php Tests/TableSchemaTest
phpunit --bootstrap vendor/autoload.php Tests/IndexTest
phpunit --bootstrap vendor/autoload.php Tests/SelectTest

phpunit --bootstrap vendor/autoload.php Tests/UpdateTest

phpunit --bootstrap vendor/autoload.php Tests/DeleteTest


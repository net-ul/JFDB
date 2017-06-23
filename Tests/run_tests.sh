#!/usr/bin/env bash

# # Install phpunit
# sudo cp Tests/phpunit-5.2.phar /usr/bin/phpunit
# sudo chmod 0777 /usr/bin/phpunit

phpunit --bootstrap vendor/autoload.php Tests/StorageTest.php
phpunit --bootstrap vendor/autoload.php Tests/TableSchemaTest
phpunit --bootstrap vendor/autoload.php Tests/IndexTest
phpunit --bootstrap vendor/autoload.php Tests/SelectTest
phpunit --bootstrap vendor/autoload.php Tests/UpdateTest
phpunit --bootstrap vendor/autoload.php Tests/DeleteTest
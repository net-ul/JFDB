#!/usr/bin/env bash

# # Install phpunit
# sudo cp Tests/phpunit-5.2.phar /usr/bin/phpunit
# sudo chmod 0777 /usr/bin/phpunit

echo "StorageTest";
phpunit --bootstrap vendor/autoload.php Tests/StorageTest

echo "TableSchemaTest";
phpunit --bootstrap vendor/autoload.php Tests/TableSchemaTest

echo "IndexTest";
phpunit --bootstrap vendor/autoload.php Tests/IndexTest

echo "SelectTest";
phpunit --bootstrap vendor/autoload.php Tests/SelectTest

echo "UpdateTest";
phpunit --bootstrap vendor/autoload.php Tests/UpdateTest

echo "DeleteTest";
phpunit --bootstrap vendor/autoload.php Tests/DeleteTest


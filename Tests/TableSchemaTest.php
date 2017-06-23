<?php

/*
 * This file is part of the JFDB package.
 * - Basic tests
 */


namespace IWSP\JFDB\Tests;

use IWSP\JFDB\Driver\Base;
use IWSP\JFDB\Driver\Insert;
use IWSP\JFDB\JFDB;
use PHPUnit\Framework\TestCase;

class TableSchemaTest extends TestBase {


  /**
   * Directory mode test.
   */
  public function testTableSchema() {

    // JFDB.
    $jfdb = $this->jfdb;

    // Table name.
    $table_name = "table_scheme";

    // Schema.
    $table_schema = [
      'description' => 'A test data table.',
      'fields' => [
        'id' => [
          'type' => 'serial',
          'not null' => TRUE,
        ],
        'uid' => [
          'type' => 'int',
          'unsigned' => TRUE,
          'not null' => TRUE,
        ],
        'message' => [
          'type' => 'varchar',
          'length' => '1024',
          'not null' => TRUE,
        ],
        'date' => [
          'type' => 'int',
          'unsigned' => TRUE,
          'not null' => TRUE,
        ],
      ],
      'primary key' => ['id'],
    ];


    // Schema.
    $schema = $jfdb->schema($table_name);

    // If table exist, DELETE.
    if ($schema->existTable()) {
      // Delete table.
      $res = $schema->deleteTable();
      $this->assertTrue($res, "Table delete");
    }


    // Recreate table.
    try {
      $schema->createTable($table_schema);
    }
    catch (\Exception $e) {
      $this->fail('Create table Exception : ' . $e->getMessage());
    }

    // Recreate table again -> Exception.
    try {
      $schema->createTable($table_schema);
      $this->fail('Create table success even already exist');
    }
    catch (\Exception $e) {
      $this->assertTrue(TRUE, 'Create table Exception OK : ' . $e->getMessage());
    }

    // Test Multiple insert.
    try {
      $jfdb->insert($table_name)
        ->fields([
          'id',
          'uid',
          'message',
        ])
        ->values([0, 'UID1', 'No message'])
        ->values([1, 'UID2', 'No message'])
        ->execute();
    }
    catch (\Exception $e) {
      $this->fail('Exception : ' . $e->getMessage());
    }

    // Transact table.
    $res = $schema->transactTable();
    $this->assertTrue($res ? TRUE : FALSE, "Transact delete");


    // Delete table.
    $res = $schema->deleteTable();
    $this->assertTrue($res, "Table delete");


    return;


  }
}

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

class IndexTest extends TestBase {


  /**
   * Directory mode test.
   */
  public function testIndexInsert() {
    // JFDB.
    $jfdb = $this->jfdb;

    // Table name.
    $table_name = "table_index";

    // Create table.
    $table_schema = [
      'description' => 'A test data table.',
      'fields' => [
        'id' => [
          'type' => 'serial',
          'default' => 'AUTO_INCREMENT',
        ],
        'uuid' => [
          'type' => 'varchar',
          'length' => '64',
          'not null' => TRUE,
          'default' => 'AUTO_UUID',
        ],
        'uid' => [
          'type' => 'int',
          'unsigned' => TRUE,
          'not null' => TRUE,
          'default' => 0,
        ],
        'message' => [
          'type' => 'varchar',
          'length' => '1024',
          'not null' => TRUE,
        ],
        'date' => [
          'type' => 'int',
          'default' => 'CURRENT_TIMESTAMP',
        ],
      ],
      'primary key' => ['id'],
      'indexes' => ['uid', 'date'],
    ];

    // Schema.
    $schema = $jfdb->schema($table_name);

    // If table exist, DELETE.
    if ($schema->existTable()) {
      $schema->deleteTable();
    }
    // Recreate table.
    $schema->createTable($table_schema);

    //$jfdb->createTable('table1', $schema);
    //$base = Insert::getDriver($jfdb);


    // Test Single insert.


    try {
      $res = $jfdb->insert($table_name)
        ->fields([
          'uid',
          'message',
        ])
        ->values(['UID', 'No message 1'])
        ->values(['UID', 'No message 2'])
        ->execute();
      //print_r($res);
    }
    catch (\Exception $e) {
      $this->fail('Exception : ' . $e->getMessage());
    }

    try {
      $res = $jfdb->insert($table_name)
        ->fields([
          'uid' => 'Random-uid-' . rand(1000, 9999),
          'date' => time(),
          'id' => 150,
        ])
        ->execute();
      //print_r($res);
    }
    catch (\Exception $e) {
      $this->fail('Exception : ' . $e->getMessage());
    }


    try {
      $res = $jfdb->insert($table_name)
        ->fields([
          'id' => 2,
          'uid' => 'Value : UID',
          'date' => time(),
        ])
        ->execute();
      //print_r($res);
      $this->fail('Exception not occurred: ' . $e->getMessage());
    }
    catch (\Exception $e) {
    }


    return;
  }
}

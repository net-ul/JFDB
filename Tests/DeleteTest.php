<?php

/*
 * This file is part of the JFDB package.
 * - Basic tests
 */


namespace IWSP\JFDB\Tests;

use IWSP\JFDB\Driver\Base;
use IWSP\JFDB\Driver\Insert;
use IWSP\JFDB\Driver\Select;
use IWSP\JFDB\JFDB;
use PHPUnit\Framework\TestCase;

class DeleteTest extends TestBase {


  /**
   * Directory mode test.
   */
  public function testUpdate() {
    // JFDB.
    $jfdb = $this->jfdb;

    // Table name.
    $table_name = "table_index";

    // Create table.
    $table_schema = [
      'description' => 'A test data table.',
      'fields' => [
        'id' => [
          'type' => 'int',
          'default' => 'AUTO_INCREMENT',
        ],
        'uuid' => [
          'type' => 'varchar',
          'length' => '64',
          'default' => 'AUTO_UUID',
        ],
        'uid' => [
          'type' => 'int',
          'default' => 0,
        ],
        'message' => [
          'type' => 'varchar',
          'length' => '1024',
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

    // Insert data
    try {
      $res = $jfdb->insert($table_name)
        ->fields([
          'uid',
          'uuid',
          'message'
        ])
        ->values([1, 'UUID-000-1', 'Message 1'])
        ->values([1, 'UUID-000-2', 'A test message 2'])
        ->values([2, 'UUID-000-3', 'Just test message 3'])
        ->values([2, 'UUID-000-4', 'Just test message 4'])
        ->values([3, 'UUID-000-5', 'Just test message 5'])
        ->values([3, 'UUID-000-6', NULL])
        ->execute();
      //print_r($res);
    }
    catch (\Exception $e) {
      $this->fail('Exception : ' . $e->getMessage());
    }


    // Delete single field, Filter id = X
    $jfdb->delete($table_name)
      ->condition('id', '5')
      ->execute();
    $result = $jfdb->select($table_name)
      ->condition('id', '5')
      ->execute()
      ->fetchAll();
    $this->assertCount(0, $result, "Result hasn't data");

    // Delete multiple fields, Filter uid = X
    $jfdb->delete($table_name)
      ->condition('uid', '2')
      ->execute();
    $result = $jfdb->select($table_name)
      ->condition('uid', '2')
      ->execute()
      ->fetchAll();
    $this->assertCount(0, $result, "Result hasn't data");


    // Delete table.
    $jfdb->schema($table_name)->deleteTable();

    return;
  }
}

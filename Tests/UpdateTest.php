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

class UpdateTest extends TestBase {


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


    // Update a KEY field, Filter id = X
    $jfdb->update($table_name)
      ->condition('id', '5')
      ->fields(['uid' => 5, 'message' => 'Updated', 'id' => 150])
      ->execute();

    $result = $jfdb->select($table_name)
      ->condition('id', '150')
      ->execute()->fetchAll();
    $this->assertCount(1, $result, "Result has 1 row");
    $this->assertEquals(150, $result[5][0], "Result has new value");
    $this->assertEquals(5, $result[5][2], "Result has new value");
    $this->assertEquals('Updated', $result[5][3], "Result has new value");

    // Update multiple fields.
    $jfdb->update($table_name)
      ->condition('uid', '2')
      ->fields(['uid' => 50, 'message' => 'Updated 2'])
      ->execute();

    $result = $jfdb->select($table_name)
      ->condition('uid', '50')
      ->execute()->fetchAll();
    $this->assertCount(2, $result, "Result has 2 row");
    foreach ($result as $row) {
      $this->assertEquals(50, $row[2], "Result has new value");
    }


    // Delete table.
    //$jfdb->schema($table_name)->deleteTable();

    return;
  }
}

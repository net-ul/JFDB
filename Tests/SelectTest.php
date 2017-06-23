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

class SelectTest extends TestBase {


  /**
   * Directory mode test.
   */
  public function testInsert() {
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


    //$result = $jfdb->select($table_name)
    //->setConditionType(Select::CONDITION_OR)
    //->condition('id', '5')
    //->condition('uid', '2')
    //->condition('message', 'Message', 'LIKE')
    //->condition('id', 2, '!=')
    //->condition('id', 4, '>')
    //->condition('id', 2, '<')
    //->condition('id', 2, '<=')
    //->condition('id', 4, '>=')
    //->condition('message', 'Message', 'NULL')
    //->condition('message', 'Message', 'NOT NULL')
    //->execute()->fetchAll();

    // Get all rows.
    $result = $jfdb->select($table_name)->execute()->fetchAll();
    $this->assertCount(6, $result, "Result has 6 rows");

    // Filter id = X
    $result = $jfdb->select($table_name)
      ->condition('id', '5')
      ->execute()->fetchAll();
    $this->assertCount(1, $result, "Result has 1 row");
    $this->assertArrayHasKey(5, $result, "Result has ID 5 rows");

    // Filter id = X with operator
    $result = $jfdb->select($table_name)
      ->condition('id', '5', "=")
      ->execute()->fetchAll();
    $this->assertCount(1, $result, "Result has 1 row");
    $this->assertArrayHasKey(5, $result, "Result has ID 5");

    // Filter id != X
    $result = $jfdb->select($table_name)
      ->condition('id', '5', "!=")
      ->execute()->fetchAll();
    $this->assertCount(5, $result, "Result has X rows");
    $this->assertArrayHasKey(1, $result, "Result has ID X");
    $this->assertArrayHasKey(2, $result, "Result has ID X");
    $this->assertArrayHasKey(3, $result, "Result has ID X");
    $this->assertArrayHasKey(4, $result, "Result has ID X");
    $this->assertArrayHasKey(6, $result, "Result has ID X");

    // Filter id > X
    $result = $jfdb->select($table_name)
      ->condition('id', '4', ">")
      ->execute()->fetchAll();
    $this->assertCount(2, $result, "Result has 2 rows");
    $this->assertArrayHasKey(5, $result, "Result has ID X");
    $this->assertArrayHasKey(6, $result, "Result has ID X");

    // Filter id >= X
    $result = $jfdb->select($table_name)
      ->condition('id', '4', ">=")
      ->execute()->fetchAll();
    $this->assertCount(3, $result, "Result has 3 rows");
    $this->assertArrayHasKey(4, $result, "Result has ID X");
    $this->assertArrayHasKey(5, $result, "Result has ID X");
    $this->assertArrayHasKey(6, $result, "Result has ID X");

    // Filter id < X
    $result = $jfdb->select($table_name)
      ->condition('id', '2', "<")
      ->execute()->fetchAll();
    $this->assertCount(1, $result, "Result has 4 row");
    $this->assertArrayHasKey(1, $result, "Result has ID X");

    // Filter id >= X
    $result = $jfdb->select($table_name)
      ->condition('id', '2', "<=")
      ->execute()->fetchAll();
    $this->assertCount(2, $result, "Result has 4 row");
    $this->assertArrayHasKey(2, $result, "Result has ID X");
    $this->assertArrayHasKey(1, $result, "Result has ID X");

    // Filter NULL Condition
    $result = $jfdb->select($table_name)
      ->condition('message', 0, "NULL")
      ->execute()->fetchAll();
    $this->assertCount(1, $result, "Result has 1 row");
    $this->assertArrayHasKey(6, $result, "Result has ID X");

    // Filter NOT NULL Condition
    $result = $jfdb->select($table_name)
      ->condition('message', 0, "NOT NULL")
      ->execute()->fetchAll();
    $this->assertCount(5, $result, "Result has 1 row");
    $this->assertArrayHasKey(1, $result, "Result has ID X");
    $this->assertArrayHasKey(2, $result, "Result has ID X");
    $this->assertArrayHasKey(3, $result, "Result has ID X");
    $this->assertArrayHasKey(4, $result, "Result has ID X");
    $this->assertArrayHasKey(5, $result, "Result has ID X");

    // Filter LIKE Condition
    $result = $jfdb->select($table_name)
      ->condition('message', 'Just', "LIKE")
      ->execute()->fetchAll();
    $this->assertCount(3, $result, "Result has 1 row");
    $this->assertArrayHasKey(3, $result, "Result has ID X");
    $this->assertArrayHasKey(4, $result, "Result has ID X");
    $this->assertArrayHasKey(5, $result, "Result has ID X");

    // Filter LIKE Condition (2) case insensitive
    $result = $jfdb->select($table_name)
      ->condition('message', 'Message', "LIKE")
      ->execute()->fetchAll();
    $this->assertCount(5, $result, "Result has 1 row");
    $this->assertArrayHasKey(1, $result, "Result has ID X");
    $this->assertArrayHasKey(2, $result, "Result has ID X");
    $this->assertArrayHasKey(3, $result, "Result has ID X");
    $this->assertArrayHasKey(4, $result, "Result has ID X");
    $this->assertArrayHasKey(5, $result, "Result has ID X");

    // With selected fields.
    $result = $jfdb->select($table_name)
      ->condition('uid', '3')
      ->fields(['id', 'uid'])
      ->execute()->fetchAll();
    $this->assertCount(2, $result, "Result has 1 row");
    $this->assertArrayHasKey(5, $result, "Result has ID X");
    $this->assertCount(2, $result[5], "Result has X columns");
    $this->assertArrayHasKey(0, $result[5], "Result has key XXX");
    $this->assertArrayHasKey(2, $result[5], "Result has key XXX");


    // Multiple conditions

    // Filter Multiple OR Conditions
    $result = $jfdb->select($table_name)
      ->condition('uid', '3')
      ->condition('message', 'Message', "LIKE")
      ->execute()->fetchAll();
    $this->assertCount(6, $result, "Result has 1 row");

    // Filter Multiple OR Conditions (2)
    $result = $jfdb->select($table_name)
      ->condition('uid', '3')
      ->condition('message', 'just', "LIKE")
      ->execute()->fetchAll();
    $this->assertCount(4, $result, "Result has 1 row");
    $this->assertArrayHasKey(3, $result, "Result has ID X");
    $this->assertArrayHasKey(4, $result, "Result has ID X");
    $this->assertArrayHasKey(5, $result, "Result has ID X");
    $this->assertArrayHasKey(6, $result, "Result has ID X");

    // Filter Multiple OR Conditions (3)
    $result = $jfdb->select($table_name)
      ->condition('uid', 1)
      ->condition('message', 'test', "LIKE")
      ->execute()->fetchAll();
    $this->assertCount(5, $result, "Result has 1 row");
    $this->assertArrayHasKey(1, $result, "Result has ID X");
    $this->assertArrayHasKey(2, $result, "Result has ID X");
    $this->assertArrayHasKey(3, $result, "Result has ID X");
    $this->assertArrayHasKey(4, $result, "Result has ID X");
    $this->assertArrayHasKey(5, $result, "Result has ID X");

    // Filter Multiple AND Conditions
    $result = $jfdb->select($table_name)
      ->condition('uid', '3')
      ->condition('message', 'Message', "LIKE")
      ->setConditionType(Select::CONDITION_AND)
      ->execute()->fetchAll();
    $this->assertCount(1, $result, "Result has 1 row");
    $this->assertArrayHasKey(5, $result, "Result has ID X");


    // TEST associative array.
    $result = $jfdb->select($table_name)
      ->condition('uid', '3')
      ->execute()->fetchAllAssoc();
    $this->assertCount(2, $result, "Result has 1 row");
    $this->assertArrayHasKey(5, $result, "Result has ID X");
    $this->assertCount(5, $result[5], "Result has 5 columns");
    $this->assertArrayHasKey('id', $result[5], "Result has key XXX");
    $this->assertArrayHasKey('date', $result[5], "Result has key XXX");

    // TEST associative array. (2)
    $result = $jfdb->select($table_name)
      ->condition('uid', '3')
      ->fields(['id', 'uid'])
      ->execute()->fetchAllAssoc();
    $this->assertCount(2, $result, "Result has 1 row");
    $this->assertArrayHasKey(5, $result, "Result has ID X");
    $this->assertCount(2, $result[5], "Result has X columns");
    $this->assertArrayHasKey('id', $result[5], "Result has key XXX");
    $this->assertArrayHasKey('uid', $result[5], "Result has key XXX");


    ///////////////////////////////////////////////////////////////////////////
    // Delete table.
    $jfdb->schema($table_name)->deleteTable();
    return;
  }
}

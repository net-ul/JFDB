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

class StorageTest extends TestCase {

  // Test database directory.
  private $dir = NULL;

  protected function setUp() {
    $this->dir = __DIR__ . '/database';
  }

  /**
   * Directory mode test.
   */
  public function testDirectoryTest() {


    try {
      $jfdb = new JFDB($this->dir);
      $this->assertTrue(TRUE);
    }
    catch (\Exception $e) {
      $this->fail('Exception on JFDB creation : ' . $e->getMessage());
      return;
    }


    // Table name.
    $table_name = "table_index";

    // Create table.
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

    $schema = $jfdb->schema($table_name);
    $schema->createTable($table_schema);


    //$jfdb->createTable('table1', $schema);
    //$base = Insert::getDriver($jfdb);


    // Test bas structure.
    try {
      $res = $jfdb->insert($table_name)
        ->fields([
          'field_1' => 'Value 1',
          'field_2' => 'Value 2',
        ])
        ->execute();
      $this->fail('Exception not occurred');
    }
    catch (\Exception $e) {
      $this->assertTrue(TRUE, 'Bas structure execption OK : ' . $e->getMessage());
    }

    // Test Single insert.
    try {
      $res = $jfdb->insert($table_name)
        ->fields([
          'uid' => 'Value : UID',
          'date' => time(),
        ])
        ->execute();
    }
    catch (\Exception $e) {
      $this->fail('Exception : ' . $e->getMessage());
    }


    // Test Multiple insert.
    try {
     $res = $jfdb->insert($table_name)
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

    return;


  }
}

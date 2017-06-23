<?php
/**
 */

namespace IWSP\JFDB\Driver;


use IWSP\JFDB\JFDB;

/**
 * Class Schema
 * Database schema opetations.
 * @package IWSP\JFDB\Driver
 */
class Schema extends Base {

  /**
   * Check Table already exist.
   */
  public function existTable() {
    if (file_exists($this->fileMeta) || file_exists($this->fileIndex) || file_exists($this->fileData)) {
      return TRUE;
    }
    return FALSE;
  }


  /**
   * Create the database table.
   * @param $schema
   * @return $this
   * @throws \Exception
   */
  function createTable($schema, $recreate = FALSE) {

    // Test if table exist.
    if ($this->existTable()) {
      throw new \Exception("Table already exist : " . $this->tableName);
    }

    // Init data.
    $this->data = [];
    $this->meta = $schema;
    if (!$recreate) {
      $this->meta[static::AUTO_INCREMENT] = 1;
      $this->meta[static::PERMANENT_INDEX] = 1;
    }

    // Create index structure.
    $this->index = [];

    $key_types = ['primary key', 'indexes'];
    foreach ($key_types as $key_type) {
      if (isset($schema[$key_type])) {
        $keys = $schema[$key_type];
        if (!is_array($keys)) {
          throw new \Exception("Keys structure error. Each key type must be an array. [$key_type] not an array.");
        }
        foreach ($keys as $key) {
          $this->index[$key_type][$key] = [];
        }
      }
    }

    // Create basic files.
    $this->save(TRUE, TRUE, TRUE);

    return $this;
  }

  /**
   * Transact Table
   */
  public function transactTable() {
    $schema = $this->fileMeta;
    $this->deleteTable();
    return $this->createTable($schema, TRUE);
  }


  /**
   * Delete the database table.
   * @return $this
   */
  function deleteTable() {
    // Delete files.
    $res = @unlink($this->fileData);
    $res = $res * @unlink($this->fileIndex);
    $res = $res * @unlink($this->fileMeta);
    return $res ? TRUE : FALSE;
  }

}
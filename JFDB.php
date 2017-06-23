<?php
/**
 * JFDB Main class.
 */

namespace IWSP\JFDB;


use IWSP\JFDB\Driver\Create;
use IWSP\JFDB\Driver\Delete;
use IWSP\JFDB\Driver\Insert;
use IWSP\JFDB\Driver\Schema;
use IWSP\JFDB\Driver\Select;
use IWSP\JFDB\Driver\Update;

class JFDB {

  /**
   *  Database operate modes.
   */
  const MODE_DIR = "directory";
  const MODE_FILE = "file";
  const MODE_DATA = "data";

  const AUTO_INCREMENT = 'AUTO_INCREMENT';
  const AUTO_UUID = 'AUTO_UUID';
  const CURRENT_TIMESTAMP = 'CURRENT_TIMESTAMP';
  const PERMANENT_INDEX = 'PERMANENT_INDEX';

  /**
   * The table name.
   * @var string
   */
  protected $tableName = NULL;

  /**
   * The data files storage directory.
   * @var string
   */
  protected $directory = NULL;

  /**
   * The current data files.
   * @var string
   */
  protected $fileData = NULL;

  /**
   * The current index files.
   * @var string
   */
  protected $fileIndex = NULL;

  /**
   * The current metadata files.
   * @var string
   */
  protected $fileMeta = NULL;

  /**
   * The current data files.
   * @var array
   */
  protected $data = NULL;

  /**
   * The current data index.
   * @var array
   */
  protected $index = NULL;

  /**
   * Meta data.
   * - Structure
   * - Index meta
   * @var array
   */
  protected $meta = NULL;

  /**
   * The DB operate mode.
   * @var string
   */
  protected $mode = NULL;

  /**
   * Human Readable Output using JSON_PRETTY_PRINT
   * @var bool
   */
  public $humanReadableOutput = FALSE;

  /**
   * JFDB constructor.
   * @param $directory
   * @param $file
   * @param $data
   * @param $structure
   * @throws \Exception
   */
  function __construct($directory = NULL, $file = NULL, array $data = NULL, array $structure = NULL) {

    if ($directory) {
      // Check directory exist, create else.
      if (!is_dir($directory)) {
        if (!mkdir($directory, 0770, TRUE)) {
          throw new \Exception("The JFDB directory not exist and cant create.");
        }
      }

      // Check if directory is writable.
      if (!is_writable($directory)) {
        throw new \Exception("The JFDB directory is not writable.");
      }
      $this->directory = $directory;
      $this->mode = self::MODE_DIR;
    }
    elseif ($file) {
      if (file_exists($file) && is_readable($file)) {
        // File exist and readable.
      }
      elseif (is_writable($file)) {
        // File not exist but writable.
      }
      else {
        throw new \Exception("The JFDB file is not writable.");
      }
      $this->fileData = $file;
      $this->mode = self::MODE_FILE;
    }
    elseif ($data) {
      if (is_array($data)) {
        // Data is an array.
        if (is_array($structure)) {
          // TODO : check structure is compatible with data.
          $this->structure = $structure;
        }
      }
      else {
        throw new \Exception("The JFDB data is not an array.");
      }
      $this->data = $data;
      $this->mode = self::MODE_DATA;
    }
    else {
      throw new \Exception("The 'directory', 'file' and 'data' are empty one of the three must not null.");
    }
  }

  public function getData() {
    return $this->data;
  }

  public function getMeta() {
    return $this->meta;
  }

  public function getIndex() {
    return $this->index;
  }

  /**
   * @param $file_name
   */
  private function init($tableName) {
    $this->tableName = $tableName;
    $this->fileData = $this->directory . '/' . $tableName . '.data.json';
    $this->fileIndex = $this->directory . '/' . $tableName . '.index.json';
    $this->fileMeta = $this->directory . '/' . $tableName . '.meta.json';
  }

  /**
   * Save in to files.
   * @param bool $data
   * @param bool $index
   * @param bool $meta
   * @return bool
   */
  protected function save($data = TRUE, $index = FALSE, $meta = FALSE) {

    // Set json options.
    $options = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT;
    if ($this->humanReadableOutput) {
      $options = $options | JSON_PRETTY_PRINT;
    }

    // $res = TRUE if all files write success.
    $res = FALSE;
    // Save Data.
    if ($data) {
      $res = file_put_contents($this->fileData, json_encode($this->data, $options));
    }
    // Save Index.
    if ($index) {
      $res = $res * file_put_contents($this->fileIndex, json_encode($this->index, $options));
    }
    // Save Meta data.
    if ($meta) {
      $res = $res * file_put_contents($this->fileMeta, json_encode($this->meta, $options));
    }
    return $res ? TRUE : FALSE;
  }

  /**
   * Transact Table
   */
  protected function loadTable($name) {

    $this->init($name);
    $this->data = json_decode(file_get_contents($this->fileData), TRUE);
    $this->index = json_decode(file_get_contents($this->fileIndex), TRUE);
    $this->meta = json_decode(file_get_contents($this->fileMeta), TRUE);

    return $this;
  }

  /**
   * Create Table.
   */
  public function schema($name) {
    $this->init($name);
    return (new Schema($this));
  }


  /**
   * Insert data
   * @return Insert
   */
  public function insert($name) {
    $this->loadTable($name); // init and Load.
    return new Insert($this);
  }


  /**
   * Select : Search for data.
   * @return Select
   */
  public function select($name) {
    $this->loadTable($name); // init and Load.
    return new Select($this);
  }

  /**
   * Update : update db data.
   * @return Update
   */
  public function update($name) {
    $this->loadTable($name); // init and Load.
    return new Update($this);
  }


  /**
   * Delete : delete a db row.
   * @return Delete
   */
  public function delete($name) {
    $this->loadTable($name); // init and Load.
    return new Delete($this);
  }

}
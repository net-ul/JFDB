<?php

/*
 * This file is part of the JFDB package.
 * - Basic tests
 */


namespace IWSP\JFDB\Tests;

use IWSP\JFDB\JFDB;
use PHPUnit\Framework\TestCase;

class TestBase extends TestCase {


  /**
   * Test database directory.
   * @var string
   */
  protected $dir = NULL;

  /**
   * The JFDB object.
   * @var JFDB
   */
  protected $jfdb = NULL;

  protected function setUp() {
    $this->dir = __DIR__ . '/database';

    try {
      $this->jfdb = new JFDB($this->dir);
      $this->assertTrue(TRUE);

      $this->jfdb->humanReadableOutput = TRUE;
    }
    catch (\Exception $e) {
      $this->fail('Exception on JFDB creation : ' . $e->getMessage());
    }
  }

  /**
   * Log to console.
   * @param $text
   */
  function log($text) {
    echo "\n" . date("Y-m-d H:i:s") . "\t" . $text;
  }
}

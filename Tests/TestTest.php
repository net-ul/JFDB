<?php

/*
 * This file is part of the JFDB package.
 * - Basic tests
 */


namespace IWSP\JFDB\Tests;

use PHPUnit\Framework\TestCase;

class TestTest extends TestCase {


  protected function setUp() {
    $class = $this->getMockBuilder('IWSP\JFDB\JFDB')->getMock();
  }

  public function testJustTest() {
    $this->assertSame('ResourceType', 'Test');
    $this->assertTrue(TRUE);
    $this->assertSame('ResourceType', 'ResourceType');
  }
}

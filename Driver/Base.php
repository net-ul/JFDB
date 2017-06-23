<?php
/**
 * Created by PhpStorm.
 * User: nwanasinghe
 * Date: 20/06/2017
 * Time: 15:46
 */

namespace IWSP\JFDB\Driver;

use IWSP\JFDB\JFDB;

abstract class Base extends JFDB {


  function __construct(JFDB $jfdb) {

    $list = [
      'data',
      'meta',
      'index',
      'fileData',
      'fileIndex',
      'fileMeta',
      'humanReadableOutput',
    ];


    foreach ($list as $item) {
      $this->$item = $jfdb->$item;
    }
  }
}
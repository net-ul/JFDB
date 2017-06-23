<?php
/**
 */

namespace IWSP\JFDB\Driver;


use IWSP\JFDB\JFDB;

/**
 * Class Update
 * Database schema opetations.
 * @package IWSP\JFDB\Driver
 */
class Delete extends Select {


  /**
   * FORBIDDEN METHOD.
   * @param $fields
   * @return $this
   * @throws \Exception
   */
  function fields($fields) {
    throw new \Exception("This method id forbidden.");
  }

  /**
   * Execute Update command.
   * @return $this
   * @throws \Exception
   */
  function execute() {

    // Get rows to update
    parent::execute();
    $results = $this->result;

    // Execute update.
    foreach ($results as $permanentIndex => $row) {

      // Update data.
      foreach ($this->fieldsOrderMap as $field => $fieldIndex) {

        // Copy old value.
        $valueOrd = $this->data[$permanentIndex][$this->fieldsOrderMap[$field]];

        // Remove old index.
        // Remove from index if indexed ('primary key').
        $keyType = 'primary key';
        if (isset($this->index[$keyType][$field][$valueOrd])) {
          unset($this->index[$keyType][$field][$valueOrd]);
        }

        // Remove from index if indexed ('indexes').
        $keyType = 'indexes';
        if (isset($this->index[$keyType][$field][$valueOrd])) {
          // Index exist, searching for key.
          if (($key = array_search($permanentIndex, $this->index[$keyType][$field][$valueOrd])) !== FALSE) {
            // Delete Key->value.
            unset($this->index[$keyType][$field][$valueOrd][$key]);
            // Delete key if empty.
            if (empty($this->index[$keyType][$field][$valueOrd])) {
              unset($this->index[$keyType][$field][$valueOrd]);
            }
          }
        }
      }

      // Delete data field.
      unset($this->data[$permanentIndex]);
    }

    // Save data and indexes.
    $this->save(TRUE, TRUE);

    return $this;
  }

}
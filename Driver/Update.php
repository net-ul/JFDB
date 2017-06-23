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
class Update extends Select {


  /**
   * Add as Field/Value or fields list.
   * @param array $fields
   * @return $this
   * @throws \Exception
   */
  function fields(array $fields) {
    // Check associative array.
    if (array_values($fields) === $fields) {
      throw new \Exception("Associative array only, must contains field name and value.");
    }
    else {
      $this->fields = $fields;
    }
    return $this;
  }

  /**
   * Execute Update command.
   * @return $this
   * @throws \Exception
   */
  function execute() {

    // Check fields to update.
    foreach ($this->fields as $field => $value) {
      if (!isset($this->meta['fields'][$field])) {
        throw new \Exception("The field '$field' not exist");
      }
    }

    // Get rows to update
    parent::execute();
    $results = $this->result;

    // Execute update.
    foreach ($results as $permanentIndex => $row) {

      // Update data.
      foreach ($this->fields as $field => $value) {

        // Copy old value.
        $valueOrd = $this->data[$permanentIndex][$this->fieldsOrderMap[$field]];

        // Update data field.
        $this->data[$permanentIndex][$this->fieldsOrderMap[$field]] = $value;


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

        // Add new indexes ('primary key').
        $keyType = 'primary key';
        if (isset($this->meta[$keyType]) && in_array($field, $this->meta[$keyType])) {
          if (isset($this->index[$keyType][$field][$value])) {
            throw new \Exception("Key error. $field is a key and already exist.");
          }
          else {
            $this->index[$keyType][$field][$value] = $permanentIndex;
          }
        }


        // Add new indexes ('primary key').
        $keyType = 'indexes';
        if (isset($this->meta[$keyType]) && in_array($field, $this->meta[$keyType])) {
          $this->index[$keyType][$field][$value][] = $permanentIndex;
        }
      }
    }

    // Save data and indexes.
    $this->save(TRUE, TRUE);

    return $this;
  }

}
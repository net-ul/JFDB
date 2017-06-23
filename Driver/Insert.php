<?php
/**
 */

namespace IWSP\JFDB\Driver;


use IWSP\JFDB\JFDB;

class Insert extends Base {

  private $fields = NULL;
  private $values = [];

  /**
   * Add as Field/Value or fields list.
   * @param array $fields
   * @return $this
   * @throws \Exception
   */
  function fields(array $fields) {

    // Is not an associative array. So multiple values.
    if (array_values($fields) === $fields) {
      $this->fields = $fields;
    }
    else {
      // Single value.
      $values = [];
      $thisFields = [];
      foreach ($fields as $field => $value) {
        $thisFields[] = $field;
        $values[] = $value;
      }

      if (!$this->fields) {
        $this->fields = $thisFields;
        $this->values[] = $values;
      }
      elseif (count($this->fields) == count($thisFields)) {
        $this->values[] = $values;
      }
      else {
        throw new \Exception("Fields count not match");
      }
    }
    return $this;
  }

  /**
   * Add a values row.
   * @param array $values
   * @return $this
   * @throws \Exception
   */
  function values(array $values) {
    if (count($this->fields) !== count($values)) {
      throw new \Exception("Fields count not match with Fields list");
    }
    elseif (array_values($values) === $values) {
      $this->values[] = $values;
    }
    else {
      throw new \Exception("Associative array not allowed");
    }
    return $this;
  }

  /**
   * Execute Insert.
   * @return bool
   * @throws \Exception
   */

  function execute() {

    $insert_ids = [];
    $meta_fields = $this->meta['fields'];

    $fields_order_map = [];
    $fields_def_order = []; // Fields default order.
    $fields_current = $this->fields;
    $idx_def = 0;
    foreach ($meta_fields as $key => $field) {
      $fields_def_order[$key] = $idx_def;
      $idx_def++;
      $fields_order_map[$key] = $idx = array_search($key, $fields_current);
      if ($idx !== FALSE) {
        unset($fields_current[$idx]);
      }
    }

    if (!empty($fields_current)) {
      throw new \Exception("Fields matching error");
    }

    // Check mandatory data (Indexs).
    $key_types = ['primary key'];
    foreach ($key_types as $key_type) {
      if (isset($this->meta[$key_type])) {
        $keys = $this->meta[$key_type];

        foreach ($keys as $key) {
          // Check key not empty.
          if (!isset($fields_order_map[$key]) && !isset($this->meta['fields'][$key]['default'])) {
            throw new \Exception("Key error. $key is a key and no default value.");
          }
        }
      }
    }

    // Add data.
    foreach ($this->values as $value) {
      $value_current = [];
      foreach ($fields_order_map as $key => $order) {
        if ($order === FALSE) {
          $val = NULL; // TODO : handle auto inc ...
          if (isset($this->meta['fields'][$key]['default'])) {
            if ($this->meta['fields'][$key]['default'] === static::AUTO_INCREMENT) {
              $val = $this->meta[static::AUTO_INCREMENT]++;
            }
            elseif ($this->meta['fields'][$key]['default'] === static::CURRENT_TIMESTAMP) {
              $val = time();
            }
            elseif ($this->meta['fields'][$key]['default'] === static::AUTO_UUID) {
              $val = uniqid('', TRUE);
            }
            else {
              $val = $this->meta['fields'][$key]['default'];
            }
          }
          // TODO : Process each field using the schema. (type, length ... ...)
        }
        else {
          $val = $value[$order];
        }
        $value_current[] = $val;
      }

      // Increment permenant index.
      $permanentIndex = $this->meta[static::PERMANENT_INDEX]++;

      $this->data[$permanentIndex] = $value_current;
      $insert_ids[] = $permanentIndex;

      // Set primary keys ($permanentIndex as Value)
      $key_type = 'primary key';
      if (isset($this->meta[$key_type])) {
        $keys = $this->meta[$key_type];

        foreach ($keys as $key) {
          // Check key already exist.
          if (!empty($this->index[$key_type][$key][$this->data[$permanentIndex][$fields_def_order[$key]]])) {
            throw new \Exception("Key error. $key is a key and already exist.");
          }
          $this->index[$key_type][$key][$this->data[$permanentIndex][$fields_def_order[$key]]] = $permanentIndex;
        }
      }

      // Set index.
      $key_type = 'indexes';
      if (isset($this->meta[$key_type])) {
        $keys = $this->meta[$key_type];
        foreach ($keys as $key) {
          $this->index[$key_type][$key][$this->data[$permanentIndex][$fields_def_order[$key]]][] = $permanentIndex;
        }
      }

    }
    $this->save(TRUE, TRUE, TRUE);

    return $insert_ids;
  }
}
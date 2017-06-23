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
class Select extends Base {

  // Condition type.
  const CONDITION_AND = 'AND';
  const CONDITION_OR = 'OR';

  protected $fields = NULL;

  protected $fieldsOrderMap = NULL;
  protected $fieldsExcludeMap = NULL;

  protected $result = NULL;

  protected $conditions = [];
  protected $conditionsType = 'OR';

  /**
   * Add as Field/Value or fields list.
   * @param array $fields
   * @return $this
   * @throws \Exception
   */
  function fields(array $fields) {

    // Check associative array.
    if (array_values($fields) === $fields) {
      $this->fields = $fields;
    }
    else {
      throw new \Exception("Associative array are not allowed, must contains fields name only.");
    }
    return $this;
  }

  /**
   * Add an 'AND' condition.
   * @param $field
   * @param $value
   * @param string $operator
   * @return $this
   */
  function condition($field, $value, $operator = '=') {
    $this->conditions[] = [$field, $value, $operator, 'AND'];
    // NOTE : the 4 th value (Condition type) is not using. For future implementations.
    return $this;
  }

  /**
   * Set Condition type.
   * @param $type
   * self::CONDITION_AND | self::CONDITION_AND
   * @return $this
   * @throws \Exception
   */
  function setConditionType($type) {
    if ($type === self::CONDITION_AND || $type === self::CONDITION_OR) {
      $this->conditionsType = $type;
    }
    else {
      throw new \Exception("Condition type ERROR");
    }
    return $this;
  }

  /**
   * Validate field condition.
   * @param $data
   * @param $condField
   * @param $condValue
   * @param $condOp
   * @return bool
   * @throws \Exception
   */
  protected function conditionValidate($data, $condField, $condValue, $condOp) {
    $fieldIndex = $this->fieldsOrderMap[$condField];
    $fieldValueDB = $data[$fieldIndex];

    if ($condOp === '=') {
      return ($fieldValueDB == $condValue);
    }
    elseif ($condOp === '!=') {
      return ($fieldValueDB != $condValue);
    }
    elseif ($condOp === '>') {
      return ($fieldValueDB > $condValue);
    }
    elseif ($condOp === '<') {
      return ($fieldValueDB < $condValue);
    }
    elseif ($condOp === '>=') {
      return ($fieldValueDB >= $condValue);
    }
    elseif ($condOp === '<=') {
      return ($fieldValueDB <= $condValue);
    }
    elseif ($condOp === 'NULL') {
      return ($fieldValueDB === NULL);
    }
    elseif ($condOp === 'NOT NULL') {
      return ($fieldValueDB !== NULL);
    }
    elseif ($condOp === 'LIKE') {
      return (stristr($fieldValueDB, $condValue) !== FALSE);
    }

    throw new \Exception("Operator not supported");
  }

  /**
   * Execute Select command.
   * @return $this
   * @throws \Exception
   */
  function execute() {
    $meta_fields = $this->meta['fields'];

    $fields_order_map = [];
    $fields_exclude_map = [];
    $fields_current = $this->fields;

    // Get Fields order.
    $idx_def = 0;
    foreach ($meta_fields as $key => $field) {
      $fields_order_map[$key] = $idx_def;
      $idx_def++;
    }
    $this->fieldsOrderMap = $fields_order_map;

    // Filter fields (Field Excludes.).
    if (!empty($fields_current)) {
      foreach ($fields_order_map as $key => $position) {
        if (!in_array($key, $fields_current)) {
          $fields_exclude_map[$key] = $position;
          unset($fields_order_map[$key]);
        }
      }
    }
    $this->fieldsExcludeMap = $fields_exclude_map;


    // TODO : Get correct rows.
    $results = [];

    if (!empty($conditions = $this->conditions)) {
      // TODO : Optimisation
      // TODO : Optimize index search for the operator different from '='

      // Step 1 : Pic all data rows (using index or data array).
      // -> This is OR condition algo.
      $conditionsDone = [];
      foreach ($conditions as $condition_key => $condition) {
        $condField = $condition[0];
        $condValue = $condition[1];
        $condOp = $condition[2]; // Operator
        $condType = $condition[3]; // Condition Type : AND or OR (by condition Group)


        if ($condOp === '=' && isset($this->index['primary key'][$condField][$condValue])) {
          $index = $this->index['primary key'][$condField][$condValue];
          if ($this->conditionValidate($this->data[$index], $condField, $condValue, $condOp)) {
            $results[$index] = $this->data[$index];
          }
        }
        elseif ($condOp === '=' && isset($this->index['indexes'][$condField][$condValue])) {
          $indexes = $this->index['indexes'][$condField][$condValue];
          foreach ($indexes as $index) {
            if ($this->conditionValidate($this->data[$index], $condField, $condValue, $condOp)) {
              $results[$index] = $this->data[$index];
            }
          }
        }
        elseif (isset($this->fieldsOrderMap[$condField])) {
          // On in index, searching on data file (SLOW).
          foreach ($this->data as $index => $data) {
            if ($this->conditionValidate($data, $condField, $condValue, $condOp)) {
              $results[$index] = $this->data[$index];
            }
          }
        }
        else {
          throw new \Exception("The field '$condField' not found");
        }
      }


      // Step 2 : Filter for AND condition.
      if ($this->conditionsType === static::CONDITION_AND) {
        $conditions = $this->conditions;
        foreach ($results as $index => $row) {
          foreach ($conditions as $condition_key => $condition) {
            $condField = $condition[0];
            $condValue = $condition[1];
            $condOp = $condition[2]; // Operator
            $condType = $condition[3]; // Condition Type : AND or OR (by condition Group)
            if (!$this->conditionValidate($row, $condField, $condValue, $condOp)) {
              unset($results[$index]);
              break;
            }
          }
        }
      }


    }
    else {
      $results = $this->data;
    }

    $this->result = $results;

    return $this;
  }

  /**
   * @return array
   */
  function fetchAll() {
    $results = $this->result;

    // If field filtred, remove unwanted values.
    if (!empty($this->fieldsExcludeMap)) {
      $fieldsExclude = array_values($this->fieldsExcludeMap);
      foreach ($results as $index => $result) {
        foreach ($fieldsExclude as $field_index) {
          unset($results[$index][$field_index]);
        }
      }
    }

    return $results;
  }

  /**
   * Get field name associative array.
   * @return array
   *  Return kay / value associative array.
   */
  function fetchAllAssoc() {
    $results = $this->fetchAll();
    $keys = array_keys($this->fieldsOrderMap);

    // If field filtred.
    if (!empty($this->fieldsExcludeMap)) {
      $fieldsExclude = array_values($this->fieldsExcludeMap);
      foreach ($fieldsExclude as $field_index) {
        unset($keys[$field_index]);
      }
    }

    // Combine key / values.
    foreach ($results as $index => $result) {
      $results [$index] = array_combine($keys, $result);
    }

    return $results;
  }

}
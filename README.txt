
About and Motivation
--------------------
	[JFDB](https://szu.be/projects/jfdb) (JSON file Database) is lightweight database system store date in json
	files and allow to use in small scale project. Currently it is designed for
	PHP and plane to extend.



Requirement
-----------
PHP 5.5.9
Application server like apache2


Data Map
--------
Each data array contains 2 associate arrays 'structure' and 'data' present like:

$data => [
  0 => [0=>'row 0, value 0',1=> 'row 0, value 1' ... n => 'row 0, value n'],
  .
  .
  .
  n => [0=>'row n, value 0',1=> 'row n, value 1' ... n => 'row n, value n'],
  ]


$meta = [
  'structure' => [
  0 => 'FIELD_0',
  1 => 'FIELD_1',
  .
  .
  .
  n => 'FIELD_n',
  ],
  'index' => [
  TODO
  ]
];

Index : only single field (currently).

$index = [
  // indexes are not unique.
  'indexes' => [
    'field1' => [
      'value 1' => [index_1 , index 2 ... ],
      'value 2' => [index_2, index_n ...],
    ],
  ],

  // TODO : Primary key is a unique key.
  'primary key' => [
    'kay1' => [
      'value 1' => index_1,
      'value 2' => index_2,
    ],
  ],


];

$array = [
  'structure' => [
  0 => 'FIELD_0',
  1 => 'FIELD_1',
  .
  .
  .
  n => 'FIELD_n',
  ],
  'data' => [
  0 => [0=>'row 0, value 0',1=> 'row 0, value 1' ... n => 'row 0, value n'],
  .
  .
  .
  n => [0=>'row n, value 0',1=> 'row n, value 1' ... n => 'row n, value n'],
  ]
];

$humanReadableOutput = 1 for pretty print

It is better to get all fields (not select specific fields)
It is better to use ndexed field and operator '=' for select.
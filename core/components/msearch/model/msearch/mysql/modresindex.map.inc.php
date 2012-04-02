<?php
$xpdo_meta_map['ModResIndex']= array (
  'package' => 'msearch',
  'version' => '1.1',
  'table' => 'modResIndex',
  'fields' => 
  array (
    'rid' => NULL,
    'resource' => NULL,
    'index' => NULL,
  ),
  'fieldMeta' => 
  array (
    'rid' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
    ),
    'resource' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => false,
      'index' => 'index',
    ),
    'index' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => true,
    ),
  ),
  'indexes' => 
  array (
    'content' => 
    array (
      'alias' => 'content',
      'primary' => false,
      'unique' => false,
      'type' => 'FULLTEXT',
      'columns' => 
      array (
        'resource' => 
        array (
          'length' => '',
          'collation' => '',
          'null' => false,
        ),
        'index' => 
        array (
          'length' => '',
          'collation' => '',
          'null' => true,
        ),
      ),
    ),
  ),
);

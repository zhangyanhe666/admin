<?php
return array (
  'sys' => 
  array (
    'driver' => 'Pdo',
    'key' => 'sys',
    'dsn' => 'mysql:dbname=sys;host=localhost:3306',
    'username' => 'root',
    'password' => 'password',
    'driver_options' => 
    array (
      1002 => 'SET NAMES \'UTF8\'',
    ),
  ),
  'test' => 
  array (
    'driver' => 'Pdo',
    'key' => 'test',
    'dsn' => 'mysql:dbname=mili;host=101.200.83.75:3306',
    'username' => 'mili',
    'password' => 'Mili_123',
    'driver_options' => 
    array (
      1002 => 'SET NAMES \'UTF8\'',
    ),
  ),
  'mili' => 
  array (
    'driver' => 'Pdo',
    'key' => 'mili',
    'dsn' => 'mysql:dbname=mili;host=47.95.35.206:3306',
    'username' => 'mili',
    'password' => 'Mili_123',
    'driver_options' => 
    array (
      1002 => 'SET NAMES \'UTF8\'',
    ),
  ),
);
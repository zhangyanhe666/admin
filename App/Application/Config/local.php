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
    'dsn' => 'mysql:dbname=test;host=localhost:3306',
    'username' => 'root',
    'password' => 'password',
    'driver_options' => 
    array (
      1002 => 'SET NAMES \'UTF8\'',
    ),
  ),
);
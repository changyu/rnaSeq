<?php
define('DB_SERVER2', 'localhost');
define('DB_USER2', 'Abradner1');
define('DB_PASSWORD2', '#2ki!%;');
define('DB_NAME2', 'bradner_pipeline');

try {
       $conn2 = new PDO("mysql:unix_socket=/var/run/mysqld/mysqld.sock;dbname=".DB_NAME2,DB_USER2, DB_PASSWORD2, array(PDO::ATTR_PERSISTENT => true));
       $conn2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {  echo 'ERROR: ' . $e->getMessage();  }

$limit= " limit 10";


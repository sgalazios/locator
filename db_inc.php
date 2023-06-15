<?php

$host = 'localhost';
$user = 'db_user';
$passwd = 'db_p4sSw0rd';
$schema = 'locator';

$mysqli = mysqli_connect($host, $user, $passwd, $schema);

if (!$mysqli)
{
   echo 'Connection failed<br>';
   echo 'Error: '.mysqli_connect_errno().'<br>';
   echo mysqli_connect_error().'<br>';
   die();
}

mysqli_query($mysqli, "SET NAMES 'UTF8';");

?>
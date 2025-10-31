<?php
// db_connect.php

$host = 'localhost';
$port = '5433'; // ¡Tu puerto correcto!
$dbname = 'tienda'; // <-- La base de datos PRINCIPAL
$user = 'postgres';
$password = 'ulloa123'; // ¡Tu contraseña real!

$conn_string = "host=$host port=$port dbname=$dbname user=$user password=$password";
$dbconn = pg_connect($conn_string);

if (!$dbconn) {
    die("Error: No se pudo conectar a la base de datos principal 'tienda'.");
}

pg_set_client_encoding($dbconn, "UTF8");
?>
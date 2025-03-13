<?php

require_once 'conection.php';

// Define el DSN
$dsn = 'mysql:host=' . $host . ';dbname=' . $dbname;

try {
    // Crea la conexión con PDO
    $pdo = new PDO($dsn, $user, $password);
    // Configura atributos para manejar errores de manera adecuada
    
} catch (PDOException $e) {
    // Quitar y poner mensaje genérico.
    echo "<b>pdo.php:</b> " . $e;
    echo $host . " " . $dbname . " " . $user;
    die();
}

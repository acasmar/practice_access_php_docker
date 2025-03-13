<?php

require_once 'pdo.php';
require_once 'InsertUser.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los valores del formulario
    $username = trim(htmlspecialchars($_POST['name']));
    $email = trim(htmlspecialchars($_POST['email']));
    $password = trim(htmlspecialchars($_POST['password']));
    $confirmed_password = trim(htmlspecialchars($_POST['confirmed_password']));

    // Validación de inputs (Sería interesante separar en una clase que valide todo lo que se le pase.)
    if ($password !== $confirmed_password) {
        header("Location: sign.phtml?error=2");
        exit;
    }

    if (strlen($password) > 32 || strlen($password) < 10) {
        header("Location: sign.phtml?error=4");
        exit;
    }

    if (strlen($confirmed_password) > 32 || strlen($confirmed_password) < 10) {
        header("Location: sign.phtml?error=4");
        exit;
    }

    if (strlen($username) > 16 || strlen($username) < 6) {
        header("Location: sign.phtml?error=1");
        exit;
    }

    if (strlen($username) > 64 || strlen($username) < 5) {
        header("Location: sign.phtml?error=5");
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: sign.phtml?error=5");
        exit;
    }

    // Verificar si el usuario ya está registrado
    $query = "SELECT account_id FROM accounts WHERE account_name = ? OR account_email = ?";
    $values = [
        ':account_name' => $username,
        ':account_email' => $email,
    ];

    try {
        // Preparar y ejecutar la consulta
        $res = $pdo->prepare($query);
        $res->execute([$username, $email]);
        
        // Si devuelve mayor que cero, significa que esa fila ya existe, 
        // por tanto el nombre de usuario o el email están en uso
        if ($res->rowCount() > 0) {
            header("Location: sign.phtml?error=7");
            exit;
        }
    
    } catch (PDOException $e) {
        echo 'Error inesperado, intentelo de nuevo más tarde.';
        exit;
    }

    // Creamos instancia que insertará usuario
    $insertUserObj = new InsertUser($pdo);

    // Le pasamos los datos ya validados y sanitizados para su inserción
    $insertUserObj->register($username , $email, $password);

    // Si la validación es exitosa, se inserta el usuario en la base de datos 
    // y devuelve al usuario al login con mensaje de exito.

    exit;
}


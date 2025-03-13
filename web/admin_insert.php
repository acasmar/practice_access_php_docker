<?php
require_once 'pdo.php';

// Script para insertar el usuario administrador con rol de administrador
// Datos del usuario admin
$admin_name = 'admin';
$admin_email = 'admin@admin.local';
$admin_password = 'admin123'; // Contraseña sin hash
$role = 'ROLE_ADMIN';  // Asignamos el rol de administrador

try {
    // Hashear la contraseña con Bcrypt
    $hashed_password = password_hash($admin_password, PASSWORD_BCRYPT, ['cost' => 12]);

    // Comprobar si el usuario ya existe utilizando una consulta preparada
    $sql_check = "SELECT * FROM accounts WHERE account_email = ?";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->execute([$admin_email]);  // Ejecutamos con el parámetro directamente

    $result = $stmt_check->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
        // Si el usuario no existe, insertar el nuevo usuario usando una consulta preparada
        $sql_insert = "INSERT INTO accounts (account_name, account_email, account_password, account_role) 
                    VALUES (?, ?, ?, ?)";
        $stmt_insert = $pdo->prepare($sql_insert);

        // Ejecutamos la inserción con los parámetros directamente
        if ($stmt_insert->execute([$admin_name, $admin_email, $hashed_password, $role])) {
            echo "Usuario admin insertado correctamente.\n";
        } else {
            echo "Error al insertar el usuario.\n";
        }
    } else {
        echo "El usuario admin ya existe.\n";
    }

    // Cerrar la conexión (PDO no necesita un close explícito)
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
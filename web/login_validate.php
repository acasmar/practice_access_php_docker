<?php

require_once 'pdo.php'; // Asegúrate de tener la conexión a la base de datos
require_once 'utils_validations.php'; // Utiles para validaciones

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los valores del formulario
    
    // Validación de datos (por ejemplo, asegurarse de que los campos no estén vacíos)
    if (empty($_POST['name']) || empty($_POST['password'])) {
        
        // Si está vacío, devolvemos a login con error 
        header("Location: login.phtml?error=3");
        exit;
    }

    // Validamos que el usuario tenga la estructura necesaria
    
    if(validate_general($_POST['name']) == false){
        
        $name = htmlspecialchars($_POST['name']);
        
    } else{

        // Si no cumple requisitos, devolvemos a login con error
        header("Location: login.phtml?error=1");
        exit;
    }
    
    $password = htmlspecialchars($_POST['password']);

    // Verificar si el usuario existe
    $query = "SELECT account_id, account_name, account_password, account_email, account_role 
        FROM accounts 
    WHERE account_name = ?";
    
    $values = [
        ':account_name' => $name,
    ];

    try {
        // Preparar y ejecutar la consulta
        $res = $pdo->prepare($query);
        $res->execute([$name]);
        
        // Verificar si el usuario existe
        if ($res->rowCount() == 0) {
            // Si usuario no existe, devolvemos a login con error    
            header("Location: login.phtml?error=4");
            exit;
        }

        // Obtener los resultados
        $user = $res->fetch(PDO::FETCH_ASSOC);
        

        // Verificar la contraseña
        if (!password_verify($password, $user['account_password'])) {
            // Si no cumple con requisitos, devolvemos a login con error

            header("Location: login.phtml?error=4");            
            exit;
        }

        // Si las credenciales son correctas, iniciar sesión
        session_start();
        $_SESSION['user_id'] = $user['account_id']; // TODO: Seguramente innecesario, refactorizar
        $_SESSION['user_username'] = $user['account_name'];
        $_SESSION['user_email'] = $user['account_email'];
        $_SESSION['user_role'] = $user['account_role'];

        if ($user['account_role'] == "ROLE_USER"){
        // Redirigir al usuario a la página para usuarios normales
            header("Location: index.phtml");
            exit;
        }
        if ($user['account_role'] == "ROLE_ADMIN"){
        // Redirigir al usuario a la página administrador
            header("Location: dashboard.phtml");
            exit;
        }

    } catch (PDOException $e) {
        // Si ocurre cualquier otro error, devolvemos a login con error
        header("Location: login.phtml?error=6");
        
        //exit;
    }
}
?>
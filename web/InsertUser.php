<?php
class InsertUser {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function register($username, $email, $password) {
        // El rol por defecto será ROLE_USER, equivalente a usuario genérico.
        // El admin será 1
        $role = "ROLE_USER";

        // Hasheamos la password
        $options = [
            'cost' => 12,
        ];

        // Hash obtenida
        $pass_hashed = password_hash($password, PASSWORD_BCRYPT, $options);

        // Query preparada
        $query = 'INSERT INTO accounts (account_name, account_email, account_password, account_role) 
        VALUES (:account_name, :account_email, :account_password, :account_role)';

        $values = [
            ':account_name' => $username,
            ':account_email' => $email,
            ':account_password' => $pass_hashed,
            ':account_role' => $role
        ];

        // Ejecución
        try {
            $res = $this->pdo->prepare($query);
            $res->execute($values);

            // Redirección en caso de éxito
            header("Location: login.phtml?success=1");
            exit;

        } catch (PDOException $e) {
            // Redirección en caso de error
            header("Location: sign.phtml?error=6");
            
            exit;
        }
    }
}
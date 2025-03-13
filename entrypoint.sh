#!/bin/bash
set -e

# Script SQL dinámico con las variables de entorno
cat <<EOF > /init.sql
-- Crear base de datos
CREATE DATABASE IF NOT EXISTS \`${DB_DATABASE}\`;

-- Usar la base de datos recién creada
USE \`${DB_DATABASE}\`;

-- Crear el usuario (si no existe)
CREATE USER IF NOT EXISTS '${DB_USER}'@'%' IDENTIFIED BY '${DB_PASSWORD}';

-- Otorgar permisos al usuario para acceder a la base de datos
GRANT ALL PRIVILEGES ON \`${DB_DATABASE}\`.* TO '${DB_USER}'@'%';

-- Aplicar cambios de privilegios
FLUSH PRIVILEGES;

-- Crear tabla 'accounts'
CREATE TABLE IF NOT EXISTS \`accounts\` (
    \`account_id\` INT AUTO_INCREMENT PRIMARY KEY,
    \`account_name\` VARCHAR(50) NOT NULL,
    \`account_email\` VARCHAR(100) NOT NULL UNIQUE,
    \`account_password\` VARCHAR(255) NOT NULL,
    \`account_role\` VARCHAR(20) DEFAULT 'ROLE_USER'
);

EOF

# Iniciar MariaDB en segundo plano
docker-entrypoint.sh mysqld &

# Esperar a que el servidor esté listo
sleep 10

# Ejecutar el script generado
mariadb -u root -p"$DB_ROOTPASSWORD" < /init.sql

# Mantener el contenedor en ejecución
wait
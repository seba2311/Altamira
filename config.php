<?php
// config.php

// Datos de conexión a la base de datos
$host = '127.0.0.1:3060';
$db_name = 'altamira';
$username = 'root';
$password = '';

// Opciones para PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
];

try {
    // Intentamos la conexión con la contraseña proporcionada
    $conn = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $username, $password, $options);
} catch(PDOException $e) {
    // Si falla, intentamos sin contraseña
    try {
        $conn = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $username, '', $options);
    } catch(PDOException $e2) {
        // Si ambos intentos fallan, mostramos un mensaje de error
        die("Error de conexión: " . $e2->getMessage());
    }
}

// Configuración adicional para asegurar el uso de UTF-8
$conn->exec("SET NAMES utf8mb4");
?>
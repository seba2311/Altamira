<?php
// config.php

// Datos de conexión a la base de datos
$host = '192.168.18.170:3060';  // El host suele ser 'localhost' en entornos locales
$db_name = 'altamira';
$username = 'root';  // Usuario por defecto en XAMPP
$password = 'root';  // Deja esto vacío si no has configurado una contraseña

try {
    // Intentamos primero sin contraseña
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // Si falla, intentamos con la contraseña por defecto de XAMPP (que es vacía)
    try {
        $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, '');
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "Conexión exitosa (sin contraseña)";
    } catch(PDOException $e2) {
        echo "Error de conexión: " . $e2->getMessage();
      ;
    }
}
?>
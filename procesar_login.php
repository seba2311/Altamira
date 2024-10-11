<?php
// procesar_login.php
session_start();
require("debug_utils.php");
require_once 'config.php';

// Obtener los datos de la tabla usuarios
$query = $conn->query("SELECT * FROM usuarios");
$resultados = $query->fetchAll();

$loginSuccess = false;

foreach($resultados as $usuario){
    if($usuario['usuario'] == $_POST['usuario'] && $usuario['clave'] == $_POST['clave']){
        $loginSuccess = true;
        $_SESSION['usuario'] = $usuario['usuario'];
        $_SESSION['id_usuario'] = $usuario['id_usuario'];
        break;
    }
}

if ($loginSuccess) {
    echo json_encode(['success' => true, 'message' => 'Login correcto']);
} else {
    echo json_encode(['success' => false, 'message' => 'Usuario o contraseña incorrectos']);
}

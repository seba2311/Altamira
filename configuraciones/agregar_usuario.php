<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

$nombreUsuario = htmlspecialchars($_SESSION['usuario']);
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom_usuario = $_POST['nom_usuario'];
    $usuario = $_POST['usuario'];
    $clave = $_POST['clave'];

    // Verificar si el usuario ya existe
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE usuario = ?");
    $stmt->execute([$usuario]);
    if ($stmt->rowCount() > 0) {
        $mensaje = "El nombre de usuario ya existe. Por favor, elija otro.";
    } else {
        // Insertar nuevo usuario
        $stmt = $conn->prepare("INSERT INTO usuarios (nom_usuario, usuario, clave) VALUES (?, ?, ?)");
        if ($stmt->execute([$nom_usuario, $usuario, $clave])) {
            $mensaje = "Usuario agregado con éxito.";
        } else {
            $mensaje = "Error al agregar el usuario.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Nuevo Usuario</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.js"></script>
    <style>
        body {
            background-color: #f8f8f8;
        }
        .main.container {
            margin-top: 2em;
        }
    </style>
</head>
<body>
    <? include '../menu.php' ?>

    <div class="ui main container">
        <h2 class="ui header">Agregar Nuevo Usuario</h2>
        <?php if ($mensaje): ?>
            <div class="ui message">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>
        <form class="ui form" method="POST">
            <div class="field">
                <label>Nombre completo</label>
                <input type="text" name="nom_usuario" required>
            </div>
            <div class="field">
                <label>Nombre de usuario</label>
                <input type="text" name="usuario" required>
            </div>
            <div class="field">
                <label>Contraseña</label>
                <input type="password" name="clave" required>
            </div>
            <button class="ui primary button" type="submit">Guardar Usuario</button>
            <a href="configuraciones.php" class="ui button">Cancelar</a>
        </form>
    </div>
</body>
</html>
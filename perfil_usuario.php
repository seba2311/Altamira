<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

$usuario = $_SESSION['usuario'];
$mensaje = '';

// Obtener información del usuario
$stmt = $conn->prepare("SELECT usuario, clave FROM usuarios WHERE usuario = ?");
$stmt->execute([$usuario]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Procesar cambio de contraseña
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nueva_clave'])) {
    $nueva_clave = $_POST['nueva_clave'];
    $confirmar_clave = $_POST['confirmar_clave'];

    if ($nueva_clave === $confirmar_clave) {
        $stmt = $conn->prepare("UPDATE usuarios SET clave = ? WHERE usuario = ?");
        if ($stmt->execute([$nueva_clave, $usuario])) {
            $mensaje = "Contraseña actualizada con éxito.";
            // Actualizar la información del usuario después del cambio
            $user['clave'] = $nueva_clave;
        } else {
            $mensaje = "Error al actualizar la contraseña.";
        }
    } else {
        $mensaje = "Las contraseñas no coinciden.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.css">
    <style>
        body { 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0;
            background-color: #f8f8f8;
        }
        .profile-container {
            width: 400px;
            background-color: white;
            padding: 2em;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .profile-image {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin: 0 auto 1em;
            overflow: hidden;
            background-color: #ddd;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .profile-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <div class="profile-image">
            <img src="imagenes/usuario.webp" alt="Imagen de perfil">
        </div>
        <h2 class="ui header">Perfil de Usuario</h2>
        <?php if ($mensaje): ?>
            <div class="ui message">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>
        <form class="ui form" method="POST">
            <div class="field">
                <label>Nombre de Usuario</label>
                <input type="text" value="<?php echo htmlspecialchars($user['usuario']); ?>" readonly>
            </div>
            <div class="field">
                <label>Contraseña Actual</label>
                <input type="password" value="<?php echo htmlspecialchars($user['clave']); ?>" readonly>
            </div>
            <h4 class="ui dividing header">Cambiar Contraseña</h4>
            <div class="field">
                <label>Nueva Contraseña</label>
                <input type="password" name="nueva_clave" required>
            </div>
            <div class="field">
                <label>Confirmar Nueva Contraseña</label>
                <input type="password" name="confirmar_clave" required>
            </div>
            <button class="ui primary button" type="submit">Cambiar Contraseña</button>
        </form>
        <div class="ui divider"></div>
        <a href="pag_principal.php" class="ui button">Volver a la Página Principal</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.js"></script>
</body>
</html>
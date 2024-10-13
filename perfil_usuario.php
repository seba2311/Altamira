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
$stmt = $conn->prepare("SELECT usuario, clave, nom_usuario, foto_perfil FROM usuarios WHERE usuario = ?");
$stmt->execute([$usuario]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Procesar cambio de contraseña y actualización de foto
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nueva_clave = $_POST['nueva_clave'] ?? '';
    $confirmar_clave = $_POST['confirmar_clave'] ?? '';

    // Validación para el cambio de contraseña
    if (!empty($nueva_clave) || !empty($confirmar_clave)) {
        if (empty($nueva_clave) || empty($confirmar_clave)) {
            $mensaje .= "Por favor, complete ambos campos de contraseña. ";
        } elseif ($nueva_clave === $confirmar_clave) {
            $stmt = $conn->prepare("UPDATE usuarios SET clave = ? WHERE usuario = ?");
            if ($stmt->execute([$nueva_clave, $usuario])) {
                $mensaje .= "Contraseña actualizada con éxito. ";
                $user['clave'] = $nueva_clave;
            } else {
                $mensaje .= "Error al actualizar la contraseña. ";
            }
        } else {
            $mensaje .= "Las contraseñas no coinciden. ";
        }
    }

    // Manejo de la actualización de la foto de perfil
    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['foto_perfil']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $upload_dir = __DIR__ . '/uploads/';
            
            // Crear el directorio si no existe
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $newname = $upload_dir . uniqid() . "." . $ext;
            if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $newname)) {
                $foto_perfil_db = 'uploads/' . basename($newname);
                $stmt = $conn->prepare("UPDATE usuarios SET foto_perfil = ? WHERE usuario = ?");
                if ($stmt->execute([$foto_perfil_db, $usuario])) {
                    $mensaje .= "Foto de perfil actualizada. ";
                    $user['foto_perfil'] = $foto_perfil_db;
                } else {
                    $mensaje .= "Error al actualizar la foto en la base de datos. ";
                }
            } else {
                $mensaje .= "Error al subir la imagen. ";
            }
        } else {
            $mensaje .= "Tipo de archivo no permitido. Solo se permiten jpg, jpeg, png y gif. ";
        }
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
            background-color: #f8f8f8;
            padding: 2em;
        }
        .profile-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            padding: 2em;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .profile-image {
            width: 150px;
            height: 150px;
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
  
        <?php if ($mensaje): ?>
            <div class="ui message">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>
        <div class="profile-image">
            <img src="<?php echo $user['foto_perfil'] ? htmlspecialchars($user['foto_perfil']) : 'imagenes/usuario.webp'; ?>" alt="Foto de perfil">
        </div>
        <form class="ui form" method="POST" enctype="multipart/form-data">
            <div class="field">
                <label>Nombre de Usuario</label>
                <input type="text" value="<?php echo htmlspecialchars($user['usuario']); ?>" readonly>
            </div>
            <div class="field">
                <label>Nombre Completo</label>
                <input type="text" value="<?php echo htmlspecialchars($user['nom_usuario']); ?>" readonly>
            </div>
            <div class="field">
                <label>Cambiar Foto de Perfil</label>
                <input type="file" name="foto_perfil" accept="image/*">
            </div>
            <h4 class="ui dividing header">Cambiar Contraseña</h4>
            <div class="field">
                <label>Nueva Contraseña</label>
                <input type="password" name="nueva_clave">
            </div>
            <div class="field">
                <label>Confirmar Nueva Contraseña</label>
                <input type="password" name="confirmar_clave">
            </div>
            <button class="ui primary button" type="submit">Guardar Cambios</button>
        </form>
        <div class="ui divider"></div>
        <a href="pag_principal.php" class="ui button">Volver a la Página Principal</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.js"></script>
</body>
</html>
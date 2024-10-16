<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}
$nombreUsuario = htmlspecialchars($_SESSION['usuario']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuraciones</title>
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
<?php include '../menu.php'; ?>


    <div class="ui main container">
        <h2 class="ui header">Configuraciones</h2>
        <div class="ui segment">
            <h3 class="ui header">Gestión de Usuarios</h3>
            <a href="agregar_usuario.php" class="ui primary button">
                <i class="user plus icon"></i>
                Agregar Nuevo Usuario
            </a>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('.ui.dropdown').dropdown();
        });
    </script>
</body>

</html>
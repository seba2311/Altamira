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
    <title>Módulo: RFID</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.js"></script>
    <style>
        body {
            background-color: #f8f8f8;
        }

        .ui.menu {
            margin-bottom: 0;
            border-radius: 0;
        }

        .ui.menu .logo-container {
            padding: 0.5em 1em;
        }

        .ui.menu .logo-container img {
            max-height: 40px;
            width: auto;
        }

        .datetime-container {
            background-color: #f0f0f0;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .datetime {
            max-width: 1127px;
            margin: 0 auto;
            padding-left: 10px;
        }

        .main.container {
            max-width: 1127px !important;
            width: 100%;
            margin-top: 2em;
        }

        .ui.header {
            margin-top: 0.5em;
        }

        .ui.segment {
            box-shadow: none;
            border: 1px solid #ddd;
        }

        .ui.items>.item {
            align-items: center;
            padding: 0.5em 0;
        }

        .ui.items>.item>.image {
            width: 32px;
        }

        .ui.items>.item .header {
            margin-bottom: 0.25em;
        }

        .ui.items>.item .description {
            font-size: 0.9em;
            color: #666;
        }

        .ui.items>.item>.image img.icon {
            width: 32px;
            height: 32px;
            object-fit: contain;
        }

        .ui.items>.item .content {
            padding: 0;
        }
    </style>
</head>

<body>
<div class="ui blue inverted menu">
    
        <div class="ui container">
            <div class="logo-container item">
                <img src="imagenes/altamira.jpg" alt="Altamira Logo">
            </div>
            <a class="item">RFID</a>
            <a class="item">VALIDACIONES</a>
            <a class="item">PROCESOS</a>
            <a class="item" href="configuraciones.php">CONFIGURACIONES</a>
            <div class="right menu">
                <a class="item user-info" href="perfil_usuario.php">
                    <i class="user icon"></i>
                    <?php echo $nombreUsuario; ?>
                </a>
                <a class="item" href="logout.php">
                    <i class="sign-out icon"></i>
                    Cerrar sesión
                </a>
            </div>
        </div>
    </div>

    <div class="datetime-container">
        <div class="datetime">
            <span id="current-datetime"></span>
        </div>
    </div>

    <div class="ui main container">
        <div class="ui two column grid">
            <div class="column">
                <div class="ui segment">
                    <h3 class="ui green header">VALIDACIONES</h3>
                    <div class="ui items">
                        <div class="item">
                            <div class="content">
                                <img src="imagenes/importar.png" class="icon" alt="Validacion Ingreso">
                                <a class="header">Validacion Ingreso</a>
                            </div>
                        </div>
                        <div class="item">
                            <div class="content">
                                <img src="imagenes/log_out.png" class="icon" alt="Validacion Ingreso">
                                <a class="header">Validacion Salida</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="column">
                <div class="ui segment">
                    <h3 class="ui green header">PROCESOS</h3>
                    <div class="ui items">
                        <div class="item">
                            <div class="content">
                                <img src="imagenes/despachos.png" class="icon" alt="Validacion Ingreso">
                                <a class="header">Toma de Inventario</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateDateTime() {
            const now = new Date();
            const options = {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            };
            $('#current-datetime').text(now.toLocaleDateString('es-ES', options).replace(',', '|'));
        }
        $(document).ready(function() {
            updateDateTime();
            setInterval(updateDateTime, 1000);
        });
    </script>
</body>

</html>
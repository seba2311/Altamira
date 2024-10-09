<?php 
session_start(); 
if (isset($_SESSION['usuario'])) {     
    header("Location: pag_principal.php");     
    exit(); 
} 
require_once 'config.php'; 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.css">
    <style>
        body { 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0;
            background-image: url('imagenes/fondo.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        .login-container {
            width: 300px;
            background-color: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
        }
        .ui.form input[type="text"],
        .ui.form input[type="password"] {
            background-color: rgba(240, 240, 240, 0.9);
        }
        .logo-container {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo-container img {
            max-width: 150px;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo-container">
            <img src="imagenes/altamira.jpg" alt="Altamira Logo">
        </div>
        <form class="ui form" id="loginForm">
            <h2 class="ui center aligned header">Iniciar Sesión</h2>
            <div class="field">
                <label>Usuario</label>
                <input type="text" name="usuario" id="usuario" placeholder="Usuario" required>
            </div>
            <div class="field">
                <label>Contraseña</label>
                <input type="password" name="clave" id="clave" placeholder="Contraseña" required>
            </div>
            <button class="ui fluid primary button" type="submit">Iniciar Sesión</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#loginForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: 'procesar_login.php',
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        window.location.href = 'pag_principal.php';
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('Error en la solicitud');
                }
            });
        });
    });
    </script>
</body>
</html>
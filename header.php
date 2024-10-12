<?php

$nombreUsuario = htmlspecialchars($_SESSION['usuario']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Altamira Logística</title>
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
        .main-content {
            padding-top: 2em;
        }
        .ui.dropdown .menu {
            border-radius: 0;
        }
        .ui.dropdown .menu > .item {
            padding: 0.78571429em 1.14285714em !important;
        }
    </style>
</head>
<body>
    <?php include 'menu.php'; ?>
    <div class="main-content">
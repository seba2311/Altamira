<?php
if (!isset($nombreUsuario)) {
    $nombreUsuario = isset($_SESSION['usuario']) ? htmlspecialchars($_SESSION['usuario']) : 'Usuario';
}
?>

<div class="ui blue inverted menu">
    <div class="ui container">
        <div class="logo-container item">
            <img src="imagenes/altamira.jpg" alt="Altamira Logo">
        </div>
        <div class="MENU">
        <a class="item" href="pag_principal.php">RFID</a>
          
        </div>
        <div class="ui dropdown item">
            PRODUCTOS <i class="dropdown icon"></i>
            <div class="menu">
                <a class="item" href="productos.php">Crar nuevo producto</a>
                <a class="item" href="productos_lista.php">Lista de productos</a>
            </div>
        </div>
        <div class="ui dropdown item">
            PROCESOS <i class="dropdown icon"></i>
            <div class="menu">
                <a class="item">Toma de Inventario</a>
                <a class="item">Otro Proceso</a>
            </div>
        </div>
        <div class="ui dropdown item">
            CONFIGURACIONES <i class="dropdown icon"></i>
            <div class="menu">
                <a class="item" href="configuraciones.php">Gestión de Usuarios</a>
                <a class="item">Otras Configuraciones</a>
            </div>
        </div>
        <div class="right menu">
            <div class="ui dropdown item">
                <i class="user icon"></i>
                <?php echo $nombreUsuario; ?> <i class="dropdown icon"></i>
                <div class="menu">
                    <a class="item" href="perfil_usuario.php">Perfil</a>
                    <a class="item" href="logout.php">Cerrar sesión</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.ui.dropdown').dropdown();
});
</script>
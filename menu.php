<?php
if (!isset($nombreUsuario)) {
    $nombreUsuario = $_SESSION['usuario'] ?? 'Usuario';
    $nombreUsuario = htmlspecialchars($nombreUsuario, ENT_QUOTES, 'UTF-8');
}
require 'navegacion.php';
$url_base = "http://192.168.18.170/altamira/";
?>

<div class="ui blue inverted menu" id="main-menu">
    <div class="ui container">
        <div class="logo-container item">
            <img src="<?= $url_base ?>imagenes/altamira.jpg" alt="Altamira Logo">
        </div>
        <a class="item" href="<?= $url_base ?>pag_principal.php">RFID</a>

        <?php
        foreach ($navegacion as $seccion => $paginas) :
        ?>
            <div class="ui dropdown item">
                <?= htmlspecialchars($seccion, ENT_QUOTES, 'UTF-8') ?> <i class="dropdown icon"></i>
                <div class="menu">
                    <?php
                    foreach ($paginas as $nombre => $url) :
                        $fullUrl = $url_base . htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
                        $nombreEscapado = htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8');
                        echo "<a class='item' href='{$fullUrl}'>{$nombreEscapado}</a>";
                    endforeach;
                    ?>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="right menu">
            <div class="ui dropdown item">
                <i class="user icon"></i>
                <span class="username-text"><?= $nombreUsuario ?></span> <i class="dropdown icon"></i>
                <div class="menu">
                    <a class="item" href="<?= $url_base ?>perfil_usuario.php">Perfil</a>
                    <a class="item" href="logout.php">Cerrar sesión</a>
                </div>
            </div>
        </div>
    </div>
    <a href="javascript:void(0);" class="icon" id="menu-toggle">
        <i class="bars icon"></i>
    </a>
</div>


<style>
    @media screen and (max-width: 768px) {
        #main-menu .ui.container {
            display: none;
            flex-direction: column;
            width: 100%;
        }
        #main-menu .ui.container.active {
            display: flex;
        }
        #main-menu .item, #main-menu .dropdown.item {
            width: 100%;
            text-align: left;
        }
        #main-menu .right.menu {
            margin-left: 0 !important;
        }
        #main-menu .icon#menu-toggle {
            display: block;
            position: absolute;
            right: 10px;
            top: 10px;
            color: #ffffff; /* Cambia el color del icono a blanco */
        }
        #main-menu .icon#menu-toggle:hover {
            color: #f0f0f0; /* Color ligeramente diferente al pasar el mouse */
        }
        #main-menu .logo-container.item {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        #main-menu .right.menu .dropdown.item {
            display: flex;
            align-items: center;
        }
        #main-menu .right.menu .dropdown.item .user.icon {
            margin-right: 5px;
        }
    }
    @media screen and (min-width: 769px) {
        #main-menu .icon#menu-toggle {
            display: none;
            color: #ffffff; 
        }
    }
    #main-menu .right.menu .dropdown.item .user.icon,
    #main-menu .right.menu .dropdown.item .username-text {
        display: inline-block;
        vertical-align: middle;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        $('.ui.dropdown').dropdown();

        document.getElementById('menu-toggle').addEventListener('click', function() {
            document.querySelector('.ui.container').classList.toggle('active');
        });

        // Cerrar menú al hacer clic fuera de él
        document.addEventListener('click', function(event) {
            if (!event.target.closest('#main-menu')) {
                document.querySelector('.ui.container').classList.remove('active');
            }
        });

        // Prevenir que los clics dentro del menú lo cierren
        document.getElementById('main-menu').addEventListener('click', function(event) {
            event.stopPropagation();
        });
    });
</script>
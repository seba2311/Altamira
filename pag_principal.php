<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}
$nombreUsuario = htmlspecialchars($_SESSION['usuario']);

require_once "graficos.php";
$topProducts = obtenerTopProductosStock(10);
$productosBajos = obtenerProductosBajos(10);


?>
<!DOCTYPE html>
<html lang="es">
<meta charset="UTF-8">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Módulo: RFID</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        /* Estilos adicionales para el menú desplegable */
        .ui.dropdown .menu {
            border-radius: 0;
        }

        .ui.dropdown .menu>.item {
            padding: 0.78571429em 1.14285714em !important;
        }

        /*css del grafico */
        #stockChartHigh, #stockChartLow {
        width: 100%;
        height: 100%; /* Ocupará todo el alto del contenedor */
    }

        .chart-container {
        background-color: #f9f9f9;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 15px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        height: 400px; /* Altura fija */
        position: relative; /* Necesario para que Chart.js pueda posicionar el gráfico correctamente */
    }

        .ui.header {
            margin-bottom: 15px;
            color: #333;
        }

        /* Ajuste para el contenedor principal de los gráficos */
        .main.container {
            margin-top: 2em;
            margin-bottom: 2em;
        }

        /* Estilos responsivos */
        @media (max-width: 768px) {

            #stockChartHigh,
            #stockChartLow {
                height: 300px;
                /* Altura reducida para dispositivos móviles */
            }
        }
    </style>
</head>

<body>
    <?php include 'menu.php'; ?>

    <div class="datetime-container">
        <div class="datetime">
            <span id="current-datetime"></span>
        </div>
    </div>

    <div class="main container">
    <div class="ui two column stackable grid">
        <div class="column">
            <div class="chart-container">
                <h2 class="ui header">Top 10 Productos con Mayor Stock</h2>
                <canvas id="stockChartHigh"></canvas>
            </div>
        </div>
        <div class="column">
            <div class="chart-container">
                <h2 class="ui header">Top 10 Productos con Menor Stock</h2>
                <canvas id="stockChartLow"></canvas>
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

        // Inicializar los menús desplegables
        $('.ui.dropdown').dropdown();

        // Crear el gráfico de productos con mayor stock
        var ctxHigh = document.getElementById('stockChartHigh').getContext('2d');
        var chartConfigHigh = <?php echo generarGraficoTopProductos('stockChartHigh', $topProducts); ?>;
        
        // Evaluar las funciones de callback
        if (chartConfigHigh.options && chartConfigHigh.options.plugins && chartConfigHigh.options.plugins.tooltip) {
            for (var callbackName in chartConfigHigh.options.plugins.tooltip.callbacks) {
                chartConfigHigh.options.plugins.tooltip.callbacks[callbackName] = 
                    eval('(' + chartConfigHigh.options.plugins.tooltip.callbacks[callbackName] + ')');
            }
        }

        // Crear el gráfico de productos con menor stock
        var ctxLow = document.getElementById('stockChartLow').getContext('2d');
        var chartConfigLow = <?php echo generarGraficoProductosBajos('stockChartLow', $productosBajos); ?>;

        try {
            new Chart(ctxHigh, chartConfigHigh);
            new Chart(ctxLow, chartConfigLow);
        } catch (error) {
            console.error('Error al crear los gráficos:', error);
            $('.main.container').append('<p class="ui red message">Error al cargar los gráficos. Por favor, intente nuevamente.</p>');
        }
    });
    </script>
</body>

</html>
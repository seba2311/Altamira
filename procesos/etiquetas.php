<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}
require "../config.php";

function getProductos($conn)
{
    $productos = [];
    $query = "SELECT pro_codigo, pro_nombre FROM producto";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $productos[] = [
            'id' => $row['pro_codigo'],
            'text' => $row['pro_codigo'] . ' - ' . $row['pro_nombre']
        ];
    }
    return $productos;
}

$productos = getProductos($conn);


function obtenerUltimaEtiqueta($conn)
{
    $query = "SELECT MAX(eti_numero) as ultima_etiqueta FROM etiquetas";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($resultado['ultima_etiqueta']) {
        return $resultado['ultima_etiqueta'];
    } else {
        return '100000000000000000000000';
    }
}

$ultimaEtiqueta = obtenerUltimaEtiqueta($conn);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Etiquetas</title>
    <link href="https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <style>
        .select2-container {
            width: 100% !important;
        }

        .select2-container .select2-selection--single {
            height: 38px;
            border: 1px solid rgba(34, 36, 38, .15);
            border-radius: .28571429rem;
            padding: 5px 10px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px;
            color: rgba(0, 0, 0, .87);
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }
    </style>
</head>

<body>
    <?php include '../menu.php'; ?>

    <div class="ui container" style="margin-top: 20px;">
        <h2 class="ui header">Crear Etiquetas</h2>

        <div class="ui form">
            <div class="field">
                <label>Seleccionar Producto</label>
                <select id="productoSelector" style="width: 100%;">
                    <option></option>
                </select>
            </div>
        </div>

        <div id="etiquetasForm" style="display: none; margin-top: 20px;">
            <table class="ui celled table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Número de Etiquetas</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td id="codigoProducto"></td>
                        <td id="nombreProducto"></td>
                        <td>
                            <div class="ui input">
                                <input type="number" id="numEtiquetas" min="1" value="1">
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <button class="ui primary button" id="crearEtiquetas">Crear Etiquetas</button>
        </div>
    </div>
    <script>
$(document).ready(function() {
    $('#productoSelector').select2({
        data: <?php echo json_encode($productos); ?>,
        placeholder: 'Buscar producto...',
        allowClear: true,
        minimumInputLength: 1
    });

    $('#productoSelector').on('select2:select', function (e) {
        var data = e.params.data;
        var parts = data.text.split(' - ');
        $('#codigoProducto').text(parts[0]);
        $('#nombreProducto').text(parts[1]);
        $('#etiquetasForm').show();
    });

    $('#productoSelector').on('select2:clear', function (e) {
        $('#etiquetasForm').hide();
    });

    var ultimaEtiqueta = '<?php echo $ultimaEtiqueta; ?>';

    $('#crearEtiquetas').click(function() {
        var codigoProducto = $('#codigoProducto').text();
        var nombreProducto = $('#nombreProducto').text();
        var cantidad = parseInt($('#numEtiquetas').val(), 10);
        
        if (isNaN(cantidad) || cantidad <= 0) {
            alert('Por favor, ingrese un número válido de etiquetas.');
            return;
        }

        var etiquetas = generarEtiquetas(ultimaEtiqueta, cantidad);
        
        $.ajax({
            url: 'guardar_etiquetas.php',
            type: 'POST',
            data: {
                eti_producto: codigoProducto,
                etiquetas: JSON.stringify(etiquetas)
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Se han creado ' + cantidad + ' etiquetas para el producto: ' + codigoProducto + ' - ' + nombreProducto);
                    ultimaEtiqueta = etiquetas[etiquetas.length - 1]; // Actualizamos la última etiqueta
                    resetInterface(); // Llamamos a la función para reiniciar la interfaz
                } else {
                    alert('Error al crear las etiquetas: ' + response.message);
                }
            },
            error: function() {
                alert('Error de conexión al crear las etiquetas.');
            }
        });
    });

    function generarEtiquetas(ultimaEtiqueta, cantidad) {
        var etiquetas = [];
        var ultimoNumero = BigInt(ultimaEtiqueta);
        
        for (var i = 0; i < cantidad; i++) {
            ultimoNumero = ultimoNumero + 1n;
            etiquetas.push(ultimoNumero.toString().padStart(24, '0'));
        }
        
        return etiquetas;
    }

    function resetInterface() {
        // Reiniciar el selector
        $('#productoSelector').val(null).trigger('change');
        
        // Limpiar y ocultar la tabla
        $('#codigoProducto').text('');
        $('#nombreProducto').text('');
        $('#numEtiquetas').val('1');
        $('#etiquetasForm').hide();
    }
});
</script>
</body>

</html>
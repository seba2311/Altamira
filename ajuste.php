<?php
require "config.php";

function getProductos($conn) {
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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Producto</title>
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
            border: 1px solid rgba(34,36,38,.15);
            border-radius: .28571429rem;
            padding: 5px 10px;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px;
            color: rgba(0,0,0,.87);
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #2185d0;
        }
        .select2-dropdown {
            border: 1px solid rgba(34,36,38,.15);
        }
        .select2-search__field {
            border: 1px solid rgba(34,36,38,.15) !important;
            border-radius: .28571429rem !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: rgba(191,191,191,.87);
        }
        .ajuste-campos {
            margin-top: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .ajuste-campos input {
            width: 100px;
        }
    </style>
</head>
<body>
    <?php include 'menu.php'; ?>
    <div class="ui container" style="margin-top: 20px;">
        <h2 class="ui header">Seleccionar Producto</h2>
        <select id="productoSelector" style="width: 100%;">
            <option></option>
        </select>

        <div id="detallesProducto" style="margin-top: 20px;">
            <table class="ui celled table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Stock Actual</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="3" class="center aligned">Seleccione un producto para ver detalles</td>
                    </tr>
                </tbody>
            </table>

            <div class="ajuste-campos" style="display: none;">
                <div class="ui input">
                    <input type="number" id="ingresoStock" placeholder="Ingreso">
                </div>
                <div class="ui input">
                    <input type="number" id="salidaStock" placeholder="Salida">
                </div>
                <button class="ui primary button" id="btnAjustar">Ajustar</button>
            </div>
        </div>
    </div>


    <script>
    $(document).ready(function() {
        $('.ui.dropdown').dropdown(); // Inicializa los dropdowns del menú

        $('#productoSelector').select2({
            data: <?php echo json_encode($productos); ?>,
            placeholder: 'Buscar producto...',
            allowClear: true,
            minimumInputLength: 1,
            language: {
                noResults: function() {
                    return "No se encontraron resultados";
                },
                inputTooShort: function() {
                    return "Por favor ingrese 1 o más caracteres";
                }
            },
            templateResult: formatProducto,
            templateSelection: formatProductoSelection
        });

        function formatProducto(producto) {
            if (!producto.id) {
                return producto.text;
            }
            var $producto = $(
                '<span><strong>' + producto.id + '</strong> - ' + producto.text.split(' - ')[1] + '</span>'
            );
            return $producto;
        }

        function formatProductoSelection(producto) {
            return producto.id ? producto.id + " - " + producto.text.split(' - ')[1] : producto.text;
        }

        $('#productoSelector').on('select2:select', function (e) {
            var productId = e.params.data.id;
            $.ajax({
                url: 'obtener_detalles_producto.php',
                type: 'GET',
                data: { codigo: productId },
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        var producto = response.data;
                        var html = `
                            <tr>
                                <td>${producto.pro_codigo}</td>
                                <td>${producto.pro_nombre}</td>
                                <td>${producto.stock_cantidad}</td>
                            </tr>
                        `;
                        $('#detallesProducto tbody').html(html);
                    } else {
                        $('#detallesProducto tbody').html('<tr><td colspan="3" class="center aligned">Error: ' + response.message + '</td></tr>');
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("Error AJAX:", textStatus, errorThrown);
                    $('#detallesProducto tbody').html('<tr><td colspan="3" class="center aligned">Error de conexión. Por favor, intente de nuevo.</td></tr>');
                }
            });
        });
    });
    
    $('#productoSelector').on('select2:select', function (e) {
            var productId = e.params.data.id;
            actualizarDetallesProducto(productId);
        });

        function actualizarDetallesProducto(productId) {
            $.ajax({
                url: 'obtener_detalles_producto.php',
                type: 'GET',
                data: { codigo: productId },
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        var producto = response.data;
                        var html = `
                            <tr>
                                <td>${producto.pro_codigo}</td>
                                <td>${producto.pro_nombre}</td>
                                <td id="stockActual">${producto.stock_cantidad}</td>
                            </tr>
                        `;
                        $('#detallesProducto tbody').html(html);
                        $('.ajuste-campos').show();
                        $('#ingresoStock, #salidaStock').val('');
                    } else {
                        $('#detallesProducto tbody').html('<tr><td colspan="3" class="center aligned">Error: ' + response.message + '</td></tr>');
                        $('.ajuste-campos').hide();
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("Error AJAX:", textStatus, errorThrown);
                    $('#detallesProducto tbody').html('<tr><td colspan="3" class="center aligned">Error de conexión. Por favor, intente de nuevo.</td></tr>');
                    $('.ajuste-campos').hide();
                }
            });
        }

        $('#btnAjustar').on('click', function() {
            var productId = $('#productoSelector').val();
            var ingreso = parseInt($('#ingresoStock').val()) || 0;
            var salida = parseInt($('#salidaStock').val()) || 0;
            var stockActual = parseInt($('#stockActual').text());

            if (salida > stockActual) {
                alert("La cantidad de salida no puede ser mayor que el stock actual.");
                return;
            }

            $.ajax({
                url: 'ajustar_stock.php',
                type: 'POST',
                data: {
                    codigo: productId,
                    ingreso: ingreso,
                    salida: salida
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert("Stock ajustado correctamente.");
                        actualizarDetallesProducto(productId);
                    } else {
                        alert("Error al ajustar el stock: " + response.message);
                    }
                },
                error: function() {
                    alert("Error de conexión al ajustar el stock.");
                }
            });
        });

    </script>
</body>
</html>
<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}
require "config.php";

$mensaje = '';
$tipoMensaje = '';

// Función para obtener la lista de productos en el formato adecuado para Select2
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

// Función para obtener la última etiqueta
function obtenerUltimaEtiqueta($conn) {
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

// Función para generar etiquetas
function generarEtiquetas($ultimaEtiqueta, $cantidad) {
    $etiquetas = [];
    $ultimoNumero = $ultimaEtiqueta;
    
    for ($i = 0; $i < $cantidad; $i++) {
        // Convertir a un array de dígitos
        $digitos = str_split(strrev($ultimoNumero));
        
        // Incrementar
        $carry = 1;
        for ($j = 0; $j < count($digitos); $j++) {
            $suma = $digitos[$j] + $carry;
            $digitos[$j] = $suma % 10;
            $carry = floor($suma / 10);
            if ($carry == 0) break;
        }
        if ($carry > 0) {
            $digitos[] = $carry;
        }
        
        // Convertir de vuelta a string
        $ultimoNumero = strrev(implode('', $digitos));
        
        // Asegurar que tenga 24 dígitos
        $etiquetas[] = str_pad($ultimoNumero, 24, '0', STR_PAD_LEFT);
    }
    
    return $etiquetas;
}

$productos = getProductos($conn);
$ultimaEtiqueta = obtenerUltimaEtiqueta($conn);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $conn->beginTransaction();

        // Verificar si el folio ya existe
        $stmt = $conn->prepare("SELECT COUNT(*) FROM guia_entrada WHERE guia_folio = :folio");
        $stmt->execute([':folio' => $_POST['folio']]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception("El folio ya existe");
        }

        // Insertar guía de entrada con folio ingresado por el usuario
        $stmt = $conn->prepare("INSERT INTO guia_entrada (guia_folio, guia_fecha, guia_glosa) VALUES (:folio, CURDATE(), :glosa)");
        $stmt->execute([
            ':folio' => $_POST['folio'],
            ':glosa' => $_POST['glosa']
        ]);

        // Insertar detalles y crear etiquetas
        $stmt_detalle = $conn->prepare("INSERT INTO detalle_guia_entrada (gdet_guia_entrada, gdet_producto, gdet_cantidad) VALUES (:guia_folio, :producto, :cantidad)");
        $stmt_etiqueta = $conn->prepare("INSERT INTO etiquetas (eti_numero, eti_producto, eti_guia_entrada) VALUES (:eti_numero, :eti_producto, :eti_guia_entrada)");
        
        foreach ($_POST['productos'] as $index => $producto) {
            $cantidad = $_POST['cantidades'][$index];
            
            // Insertar detalle
            $stmt_detalle->execute([
                ':guia_folio' => $_POST['folio'],
                ':producto' => $producto,
                ':cantidad' => $cantidad
            ]);
            
            // Generar y guardar etiquetas
            $etiquetas = generarEtiquetas($ultimaEtiqueta, $cantidad);
            foreach ($etiquetas as $etiqueta) {
                $stmt_etiqueta->execute([
                    ':eti_numero' => $etiqueta,
                    ':eti_producto' => $producto,
                    ':eti_guia_entrada' => $_POST['folio']  // Añadimos el folio de la guía de entrada
                ]);
            }
            
            // Actualizar la última etiqueta
            $ultimaEtiqueta = end($etiquetas);
        }

        $conn->commit();
        $mensaje = "Guía de entrada y etiquetas creadas con éxito. Folio: " . $_POST['folio'];
        $tipoMensaje = 'success';
    } catch (Exception $e) {
        $conn->rollBack();
        if ($e->getMessage() === "El folio ya existe") {
            $mensaje = "Error: Folio ya existente. Por favor, use un folio diferente.";
        } else {
            $mensaje = "Error al crear la guía de entrada y etiquetas: " . $e->getMessage();
        }
        $tipoMensaje = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Guía de Entrada</title>
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
    <?php include 'menu.php'; ?>

    <div class="ui container" style="margin-top: 20px;">
        <h2 class="ui header">Crear Guía de Entrada y Etiquetas</h2>
        
        <?php if ($mensaje): ?>
            <div class="ui <?php echo $tipoMensaje === 'success' ? 'positive' : 'negative'; ?> message">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <form class="ui form" method="POST">
            <div class="field">
                <label>Folio</label>
                <input type="text" name="folio" required>
            </div>
            <div class="field">
                <label>Glosa</label>
                <input type="text" name="glosa" required>
            </div>
            
            <div id="productos-container">
                <div class="fields">
                    <div class="twelve wide field">
                        <label>Producto</label>
                        <select class="producto-select" name="productos[]" required>
                            <option></option>
                        </select>
                    </div>
                    <div class="four wide field">
                        <label>Cantidad</label>
                        <input type="number" name="cantidades[]" min="1" required>
                    </div>
                </div>
            </div>
            
            <button type="button" class="ui button" id="agregar-producto">Agregar otro producto</button>
            <button type="submit" class="ui primary button">Crear Guía de Entrada y Etiquetas</button>
        </form>
    </div>

    <script>
    $(document).ready(function() {
        function initializeSelect2(element) {
            $(element).select2({
                data: <?php echo json_encode($productos); ?>,
                placeholder: 'Buscar producto...',
                allowClear: true,
                minimumInputLength: 1
            });
        }

        // Inicializar el primer selector
        initializeSelect2('.producto-select');

        $('#agregar-producto').click(function() {
            var nuevoProducto = $('#productos-container .fields').first().clone();
            nuevoProducto.find('input').val('');
            nuevoProducto.find('select')
                .val('')
                .removeClass('select2-hidden-accessible')
                .next('.select2-container').remove();
            $('#productos-container').append(nuevoProducto);
            initializeSelect2(nuevoProducto.find('.producto-select'));
        });

        $('form').on('submit', function(e) {
            var productosUnicos = new Set();
            var error = false;

            $('.producto-select').each(function() {
                var valor = $(this).val();
                if (valor && productosUnicos.has(valor)) {
                    alert('No se pueden repetir productos en la guía.');
                    error = true;
                    return false;
                }
                productosUnicos.add(valor);
            });

            if (error) {
                e.preventDefault();
            }
        });
    });
    </script>
</body>
</html>
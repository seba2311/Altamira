<?php
include '../header.php';
require "../config.php";
// Inicializar variables para mensajes
$mensaje = '';
$tipo_mensaje = '';

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener y limpiar los datos del formulario
    $pro_codigo = trim($_POST['pro_codigo']);
    $pro_nombre = trim($_POST['pro_nombre']);

    // Validación básica
    if (empty($pro_codigo) || empty($pro_nombre)) {
        $mensaje = "Por favor, complete todos los campos.";
        $tipo_mensaje = "warning";
    } else {
        // Preparar la consulta SQL
        $sql = "INSERT INTO producto (pro_codigo, pro_nombre) VALUES (?, ?)";
        
        try {
            // Preparar y ejecutar la consulta
            $stmt = $conn->prepare($sql);
            $stmt->execute([$pro_codigo, $pro_nombre]);
            $mensaje = "Producto agregado con éxito.";
            $tipo_mensaje = "success";
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                // Error de duplicación de clave primaria
                $mensaje = "El código del producto ya existe. Por favor, use un código diferente.";
                $tipo_mensaje = "error";
            } else {
                // Otro tipo de error
                $mensaje = "Error al agregar el producto: " . $e->getMessage();
                $tipo_mensaje = "error";
            }
        }
    }
}
?>

<div class="ui container">
    <h2 class="ui header">Agregar Nuevo Producto</h2>
    
    <?php if ($mensaje): ?>
        <div class="ui <?php echo $tipo_mensaje; ?> message">
            <i class="close icon"></i>
            <div class="header">
                <?php
                switch ($tipo_mensaje) {
                    case 'success':
                        echo "Operación Exitosa";
                        break;
                    case 'error':
                        echo "Error";
                        break;
                    case 'warning':
                        echo "Advertencia";
                        break;
                    default:
                        echo "Información";
                }
                ?>
            </div>
            <p><?php echo $mensaje; ?></p>
        </div>
    <?php endif; ?>

    <form class="ui form" method="POST">
        <div class="field">
            <label>Código del Producto</label>
            <input type="text" name="pro_codigo" placeholder="Ingrese el código del producto" value="<?php echo isset($_POST['pro_codigo']) ? htmlspecialchars($_POST['pro_codigo']) : ''; ?>">
        </div>
        <div class="field">
            <label>Nombre del Producto</label>
            <input type="text" name="pro_nombre" placeholder="Ingrese el nombre del producto" value="<?php echo isset($_POST['pro_nombre']) ? htmlspecialchars($_POST['pro_nombre']) : ''; ?>">
        </div>
        <button class="ui primary button" type="submit">Agregar Producto</button>
    </form>
</div>

<script>
$(document).ready(function() {
    $('.message .close').on('click', function() {
        $(this).closest('.message').transition('fade');
    });
});
</script>

<?php include '../footer.php'; ?>
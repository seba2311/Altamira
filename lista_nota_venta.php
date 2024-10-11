<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}
require "config.php";

// Configuración de la paginación
$por_pagina = 15;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$inicio = ($pagina - 1) * $por_pagina;

// Consulta para obtener el total de registros
$sql_total = "SELECT COUNT(*) as total FROM nota_venta";
$resultado_total = $conn->query($sql_total);
$fila_total = $resultado_total->fetch(PDO::FETCH_ASSOC);
$total_registros = $fila_total['total'];
$total_paginas = ceil($total_registros / $por_pagina);

// Consulta para obtener las notas de venta de la página actual
$sql = "SELECT nv_folio, nv_fecha, nv_glosa FROM nota_venta ORDER BY nv_folio DESC LIMIT :inicio, :por_pagina";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':inicio', $inicio, PDO::PARAM_INT);
$stmt->bindParam(':por_pagina', $por_pagina, PDO::PARAM_INT);
$stmt->execute();
$notas_venta = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notas de Ventas</title>
    <link href="https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.js"></script>
</head>
<body>
    <?php include 'menu.php'; ?>

    <div class="ui container" style="margin-top: 20px;">
        <h2 class="ui header">Listado de Notas de Venta</h2>

        <table class="ui celled table">
            <thead>
                <tr>
                    <th>Folio</th>
                    <th>Fecha</th>
                    <th>Glosa</th>
                    <th width="1">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($notas_venta as $nota): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($nota['nv_folio']); ?></td>
                        <td><?php echo date('d-m-Y', strtotime($nota['nv_fecha'])); ?></td>
                        <td><?php echo htmlspecialchars($nota['nv_glosa']); ?></td>
                        <td>
                            <button class="ui icon button tiny" onclick="verDetalle(<?php echo $nota['nv_folio']; ?>)">
                                <i class="search icon"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($total_paginas > 1): ?>
            <div class="ui pagination menu">
                <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                    <a class="item <?php echo ($i == $pagina) ? 'active' : ''; ?>" href="?pagina=<?php echo $i; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
    $(document).ready(function() {
        $('.ui.dropdown').dropdown(); // Inicializa los dropdowns del menú
    });

    function verDetalle(folio) {
        // Aquí puedes agregar la lógica para ver el detalle de la nota de venta
        console.log("Ver detalle de la nota de venta con folio: " + folio);
        // Por ejemplo, podrías redirigir a una página de detalles:
        // window.location.href = 'detalle_nota_venta.php?folio=' + folio;
    }
    </script>
</body>
</html>
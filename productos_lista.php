<?php
include 'header.php';
require "config.php";

// Configuración de la paginación
$productos_por_pagina = 15;
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$inicio = ($pagina_actual - 1) * $productos_por_pagina;

// Consulta SQL para obtener el total de productos
$sql_total = "SELECT COUNT(*) FROM producto";
$stmt_total = $conn->query($sql_total);
$total_productos = $stmt_total->fetchColumn();

// Calcular el número total de páginas
$total_paginas = ceil($total_productos / $productos_por_pagina);

// Consulta SQL para obtener los productos de la página actual
$sql = "SELECT pro_codigo, pro_nombre FROM producto ORDER BY pro_codigo LIMIT :inicio, :productos_por_pagina";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':inicio', $inicio, PDO::PARAM_INT);
$stmt->bindParam(':productos_por_pagina', $productos_por_pagina, PDO::PARAM_INT);
$stmt->execute();
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="ui container" style="padding-top: 2em;">
    <h2 class="ui header">Lista de Productos</h2>

    <?php if (count($productos) > 0): ?>
        <table class="ui celled table">
            <thead>
                <tr>
                    <th class="center aligned">#</th>
                    <th>Código de producto</th>
                    <th>Nombre de producto</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $index => $producto): ?>
                    <tr>
                        <td class="center aligned"><?php echo htmlspecialchars($inicio + $index + 1); ?></td>
                        <td data-label="Código"><?php echo htmlspecialchars($producto['pro_codigo']); ?></td>
                        <td data-label="Nombre"><?php echo htmlspecialchars(utf8_encode($producto['pro_nombre'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Paginación -->
        <div class="ui pagination menu">
            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                <a class="item <?php echo ($i == $pagina_actual) ? 'active' : ''; ?>" href="?pagina=<?php echo $i; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php else: ?>
        <div class="ui info message">
            <p>No hay productos registrados en la base de datos.</p>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

require "../config.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$eti_producto = $_POST['eti_producto'] ?? '';
$etiquetas = json_decode($_POST['etiquetas'] ?? '[]', true);

if (empty($eti_producto) || empty($etiquetas)) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

try {
    $conn->beginTransaction();

    $stmt = $conn->prepare("INSERT INTO etiquetas (eti_numero, eti_producto) VALUES (:eti_numero, :eti_producto)");
    
    foreach ($etiquetas as $eti_numero) {
        $stmt->execute([
            ':eti_numero' => $eti_numero,
            ':eti_producto' => $eti_producto
        ]);
    }

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Etiquetas guardadas correctamente']);

} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => 'Error al guardar las etiquetas: ' . $e->getMessage()]);
}
?>
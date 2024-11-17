<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config.php';

try {
    // Verificar que se recibió el folio
    if (!isset($_POST['folio'])) {
        throw new Exception('Folio no proporcionado');
    }
    
    $folio = $_POST['folio'];
    
    // Verificar si existe la guía
    $queryExiste = "SELECT guia_estado 
                    FROM guia_entrada 
                    WHERE guia_folio = :folio";
                    
    $stmtExiste = $conn->prepare($queryExiste);
    $stmtExiste->bindParam(':folio', $folio);
    $stmtExiste->execute();
    
    $guia = $stmtExiste->fetch(PDO::FETCH_ASSOC);
    
    if (!$guia) {
        throw new Exception('Guía de entrada no existe');
    }
    
    // Verificar que la guía esté pendiente
    if ($guia['guia_estado'] !== 'PND') {
        throw new Exception('Guía de entrada ya está recepcionada');
    }
    
    // Actualizar el estado de la guía
    $queryUpdate = "UPDATE guia_entrada 
                   SET guia_estado = 'RCP' 
                   WHERE guia_folio = :folio";
                   
    $stmtUpdate = $conn->prepare($queryUpdate);
    $stmtUpdate->bindParam(':folio', $folio);
    $stmtUpdate->execute();
    
    if ($stmtUpdate->rowCount() > 0) {
        $response = [
            'status' => 'success',
            'message' => 'Estado de guía actualizado correctamente'
        ];
    } else {
        throw new Exception('Error al actualizar el estado de la guía');
    }
    
    http_response_code(200);
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    $response = [
        'status' => 'error',
        'message' => 'Error en la base de datos',
        'error' => $e->getMessage()
    ];
    
    http_response_code(500);
    echo json_encode($response);
    
} catch (Exception $e) {
    $response = [
        'status' => 'error',
        'message' => $e->getMessage()
    ];
    
    http_response_code(400);
    echo json_encode($response);
}
?>
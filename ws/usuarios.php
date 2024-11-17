<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config.php';

try {
    // Preparar la consulta con todos los campos
    $query = "SELECT nom_usuario, usuario, clave FROM usuarios";
              
    $stmt = $conn->prepare($query);
    $stmt->execute();
    
    // Obtener todos los usuarios
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Preparar la respuesta
    $response = [
        'status' => 'success',
        'message' => 'Usuarios recuperados exitosamente',
        'data' => [
            'total' => count($usuarios),
            'usuarios' => $usuarios
        ]
    ];
    
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
}
?>
<?php
require_once '../vendor/autoload.php'; 
require_once '../config.php';
// Recibir el folio
$folio = isset($_GET['folio']) ? $_GET['folio'] : null;

if (!$folio) {
    die('Folio no proporcionado');
}

// Obtener datos de la guía
$sql_guia = "SELECT guia_folio, guia_fecha, guia_glosa, guia_estado 
             FROM guia_entrada 
             WHERE guia_folio = :folio";
$stmt_guia = $conn->prepare($sql_guia);
$stmt_guia->bindParam(':folio', $folio);
$stmt_guia->execute();
$guia = $stmt_guia->fetch(PDO::FETCH_ASSOC);

// Obtener detalles de los productos
$sql_detalle = "SELECT dget.gdet_producto, p.pro_nombre, dget.gdet_cantidad
                FROM detalle_guia_entrada dget 
                JOIN producto p ON dget.gdet_producto = p.pro_codigo 
                WHERE dget.gdet_guia_entrada = :folio";
$stmt_detalle = $conn->prepare($sql_detalle);
$stmt_detalle->bindParam(':folio', $folio);
$stmt_detalle->execute();
$detalles = $stmt_detalle->fetchAll(PDO::FETCH_ASSOC);

// Crear nuevo documento PDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Establecer información del documento
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Sistema RFID');
$pdf->SetTitle('Guía de Entrada ' . $folio);

// Eliminar encabezado y pie de página predeterminados
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// ... código existente hasta la creación del PDF ...

// Agregar página
$pdf->AddPage();


// Insertar logo
$pdf->Image('../imagenes/altamira.jpg', 15, 10, 30); 

// Información de la empresa (lado derecho)
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(120);  // mover a la derecha
$pdf->Cell(60, 10, 'ALTAMIRA', 0, 1, 'R');
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(120);
$pdf->Cell(60, 5, 'Av. Ejemplo 1234, Santiago', 0, 1, 'R');
$pdf->Cell(120);
$pdf->Cell(60, 5, 'Teléfono: +56 2 2222 3333', 0, 1, 'R');
$pdf->Cell(120);
$pdf->Cell(60, 5, 'Email: contacto@altamira.cl', 0, 1, 'R');

// Línea separadora
$pdf->Line(15, 45, 195, 45);

// Título del documento
$pdf->Ln(20);
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'GUÍA DE ENTRADA', 0, 1, 'C');
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'N° ' . $folio, 0, 1, 'C');

// Información de la guía en formato de tabla
$pdf->Ln(10);
$pdf->SetFont('helvetica', 'B', 11);
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell(30, 8, 'Fecha:', 1, 0, 'L', true);
$pdf->SetFont('helvetica', '', 11);
$pdf->Cell(150, 8, date('d-m-Y', strtotime($guia['guia_fecha'])), 1, 1, 'L');

$pdf->SetFont('helvetica', 'B', 11);
$pdf->Cell(30, 8, 'Glosa:', 1, 0, 'L', true);
$pdf->SetFont('helvetica', '', 11);
$pdf->Cell(150, 8, $guia['guia_glosa'], 1, 1, 'L');

$pdf->SetFont('helvetica', 'B', 11);
$pdf->Cell(30, 8, 'Estado:', 1, 0, 'L', true);
$pdf->SetFont('helvetica', '', 11);
$pdf->Cell(150, 8, $guia['guia_estado'] == 'PND' ? 'Pendiente' : 'Recepcionada', 1, 1, 'L');

// Detalle de productos
$pdf->Ln(10);
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, 'Detalle de Productos', 0, 1, 'L');

// Encabezados de la tabla
$pdf->SetFont('helvetica', 'B', 11);
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell(40, 8, 'Código', 1, 0, 'C', true);
$pdf->Cell(100, 8, 'Nombre', 1, 0, 'C', true);
$pdf->Cell(40, 8, 'Cantidad', 1, 1, 'C', true);

// Datos de la tabla
$pdf->SetFont('helvetica', '', 11);
foreach ($detalles as $detalle) {
    $pdf->Cell(40, 8, $detalle['gdet_producto'], 1, 0, 'L');
    $pdf->Cell(100, 8, $detalle['pro_nombre'], 1, 0, 'L');
    $pdf->Cell(40, 8, $detalle['gdet_cantidad'], 1, 1, 'C');
}

// Pie de página
$pdf->Ln(20);
$pdf->SetFont('helvetica', 'I', 8);
$pdf->Cell(0, 5, 'Documento generado el ' . date('d/m/Y H:i:s'), 0, 1, 'R');

// Generar el PDF
$pdf->Output('guia_entrada_' . $folio . '.pdf', 'I');
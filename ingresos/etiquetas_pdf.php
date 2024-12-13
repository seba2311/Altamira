<?php
require_once('../vendor/tecnickcom/tcpdf/tcpdf.php');
require "../config.php";

// Verificar sesión
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

$folio = isset($_GET['folio']) ? $_GET['folio'] : null;

if (!$folio) {
    die("Folio no proporcionado");
}

// Obtener información de la guía
$sql_guia = "SELECT guia_folio, guia_fecha, guia_glosa 
             FROM guia_entrada 
             WHERE guia_folio = :folio";
$stmt_guia = $conn->prepare($sql_guia);
$stmt_guia->execute([':folio' => $folio]);
$guia = $stmt_guia->fetch(PDO::FETCH_ASSOC);

// Obtener detalles de los productos y números de etiqueta
$sql_productos = "SELECT dget.gdet_producto, p.pro_nombre, dget.gdet_cantidad, e.eti_numero
                 FROM detalle_guia_entrada dget 
                 JOIN producto p ON dget.gdet_producto = p.pro_codigo 
                 JOIN etiquetas e ON e.eti_producto = dget.gdet_producto 
                 AND e.eti_guia_entrada = dget.gdet_guia_entrada
                 WHERE dget.gdet_guia_entrada = :folio";
$stmt_productos = $conn->prepare($sql_productos);
$stmt_productos->execute([':folio' => $folio]);
$productos = $stmt_productos->fetchAll(PDO::FETCH_ASSOC);

// Definir dimensiones de la etiqueta
define('ETIQUETA_ANCHO', 55);
define('ETIQUETA_ALTO', 35);
define('MARGEN', 2);

// Crear nuevo documento PDF
$pdf = new TCPDF('L', 'mm', array(ETIQUETA_ALTO, ETIQUETA_ANCHO), true, 'UTF-8', false);

// Configurar el documento
$pdf->SetCreator('Sistema de Inventario');
$pdf->SetTitle('Etiquetas Guía de Entrada ' . $folio);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(MARGEN, MARGEN, MARGEN);
$pdf->SetAutoPageBreak(TRUE, MARGEN);

// Generar etiquetas para cada producto
foreach ($productos as $producto) {
    // Agregar nueva página para cada etiqueta
    $pdf->AddPage();
    
    // Configurar fuente
    $pdf->SetFont('helvetica', 'B', 12);
    
    // Calcular posiciones
    $x = MARGEN;
    $y = MARGEN;
    
    // Dibujar borde de la etiqueta
    $pdf->Rect($x, $y, ETIQUETA_ANCHO - (2 * MARGEN), ETIQUETA_ALTO - (2 * MARGEN));
    
    // Código de producto
    $pdf->SetXY($x, $y);
    $pdf->Cell(70, 8, 'Código: ' . $producto['gdet_producto'], 0, 1, 'L');
    
    // Nombre del producto
    $pdf->SetXY($x, $y + 8);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->MultiCell(70, 5, $producto['pro_nombre'], 0, 'L');
    
    // // Folio de guía
    // $pdf->SetXY($x, $y + 20);
    // $pdf->SetFont('helvetica', '', 9);
    // $pdf->Cell(70, 5, 'Guía N°: ' . $guia['guia_folio'], 0, 1, 'L');
    
    // // Fecha
    // $pdf->SetXY($x, $y + 26);
    // $pdf->Cell(70, 5, 'Fecha: ' . date('d-m-Y', strtotime($guia['guia_fecha'])), 0, 1, 'L');
    
    // Generar código de barras
    $numero_etiqueta = $producto['eti_numero'];
    
    // Estilo para el código de barras
    $style = array(
        'position' => '',
        'align' => 'C',
        'stretch' => false,
        'fitwidth' => true,
        'cellfitalign' => '',
        'border' => false,
        'hpadding' => 'auto',
        'vpadding' => 'auto',
        'fgcolor' => array(0,0,0),
        'bgcolor' => false,
        'text' => true,
        'font' => 'helvetica',
        'fontsize' => 8,
        'stretchtext' => 4
    );
    
    // Agregar código de barras Code128
    $pdf->write1DBarcode(
        $numero_etiqueta,
        'C128',
        ETIQUETA_ANCHO - 53,  // Posición X
        $y + 15,              // Posición Y
        50,                   // Ancho
        15,                   // Alto
        0.4,                  // Ancho de las líneas
        $style
    );
}

// Generar el PDF
$pdf->Output('etiquetas_guia_' . $folio . '.pdf', 'I');
?>
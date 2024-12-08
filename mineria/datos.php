<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit();
}

require_once '../config.php';

class VentasPredictor {
    private $datos_historicos = [];
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->cargarDatosHistoricos();
    }

    private function cargarDatosHistoricos() {
        $sql = "SELECT 
                    DATE_FORMAT(n.nv_fecha, '%Y-%m') as mes,
                    p.pro_codigo,
                    p.pro_nombre,
                    SUM(d.ndet_cantidad) as cantidad_total
                FROM nota_venta n
                JOIN detalle_nota_venta d ON n.nv_folio = d.ndet_nota_venta
                JOIN producto p ON d.ndet_producto = p.pro_codigo
                WHERE n.nv_estado = 'TER'
                GROUP BY DATE_FORMAT(n.nv_fecha, '%Y-%m'), p.pro_codigo, p.pro_nombre
                ORDER BY mes DESC, p.pro_codigo";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $this->datos_historicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerFechaMasReciente() {
        $sql = "SELECT MAX(nv_fecha) as fecha_max FROM nota_venta WHERE nv_estado = 'TER'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado['fecha_max'];
    }

    public function obtenerVentasUltimosMeses($producto_codigo, $numero_meses = 3) {
        $fecha_max = $this->obtenerFechaMasReciente();
        
        // Crear un array con los últimos 3 meses
        $meses = [];
        $fecha_actual = new DateTime($fecha_max);
        
        // Retroceder al primer mes del rango
        $fecha_actual->modify('-' . ($numero_meses - 1) . ' months');
        
        // Obtener los meses en orden ascendente
        for ($i = 0; $i < $numero_meses; $i++) {
            $mes_actual = $fecha_actual->format('Y-m');
            $mes_nombre = $fecha_actual->format('M Y');
            
            // Consulta para obtener las ventas del mes específico
            $sql = "SELECT 
                        COALESCE(SUM(d.ndet_cantidad), 0) as cantidad_total
                    FROM nota_venta n
                    LEFT JOIN detalle_nota_venta d ON n.nv_folio = d.ndet_nota_venta 
                        AND d.ndet_producto = ?
                    WHERE n.nv_estado = 'TER'
                    AND DATE_FORMAT(n.nv_fecha, '%Y-%m') = ?";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$producto_codigo, $mes_actual]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $meses[] = [
                'mes' => $mes_actual,
                'mes_nombre' => $mes_nombre,
                'cantidad_total' => $resultado['cantidad_total']
            ];
            
            // Avanzar un mes
            $fecha_actual->modify('+1 month');
        }
        
        return $meses;
    }

    public function calcularMediaMovil($producto_codigo, $meses = 3) {
        // Filtrar ventas del producto
        $ventas_producto = array_filter($this->datos_historicos, function($item) use ($producto_codigo) {
            return $item['pro_codigo'] === $producto_codigo;
        });

        // Ordenar por mes de más reciente a más antiguo
        usort($ventas_producto, function($a, $b) {
            return strcmp($b['mes'], $a['mes']);
        });

        // Convertir a array indexado
        $ventas_producto = array_values($ventas_producto);

        // Verificar si hay suficientes datos
        if (count($ventas_producto) < 1) {
            return null;
        }

        // Calcular la media de los últimos meses disponibles (hasta 3)
        $meses_disponibles = min($meses, count($ventas_producto));
        $suma = 0;
        for ($i = 0; $i < $meses_disponibles; $i++) {
            $suma += floatval($ventas_producto[$i]['cantidad_total']);
        }

        return $suma / $meses_disponibles;
    }

    public function predecirProximoMes($producto_codigo) {
        $media_movil = $this->calcularMediaMovil($producto_codigo);
        
        if ($media_movil === null) {
            return [
                'prediccion' => 0,
                'confianza' => 0,
                'mensaje' => 'No hay suficientes datos históricos'
            ];
        }

        // Filtrar y ordenar ventas del producto
        $ventas_producto = array_filter($this->datos_historicos, function($item) use ($producto_codigo) {
            return $item['pro_codigo'] === $producto_codigo;
        });
        
        usort($ventas_producto, function($a, $b) {
            return strcmp($b['mes'], $a['mes']);
        });
        
        $ventas_producto = array_values($ventas_producto);
        
        // Calcular tendencia
        $tendencia = 0;
        if (count($ventas_producto) >= 2) {
            $ultimo_mes = floatval($ventas_producto[0]['cantidad_total']);
            $penultimo_mes = floatval($ventas_producto[1]['cantidad_total']);
            $tendencia = $ultimo_mes - $penultimo_mes;
        }

        // Calcular predicción
        $prediccion = max(0, round($media_movil + ($tendencia * 0.5)));

        // Calcular confianza
        $confianza = $this->calcularConfianza($ventas_producto);

        return [
            'prediccion' => $prediccion,
            'confianza' => $confianza,
            'mensaje' => 'Predicción basada en datos históricos'
        ];
    }

    private function calcularConfianza($datos) {
        if (count($datos) < 2) return 0;

        $variaciones = [];
        $total_cantidad = 0;

        for ($i = 1; $i < count($datos); $i++) {
            $actual = floatval($datos[$i-1]['cantidad_total']);
            $anterior = floatval($datos[$i]['cantidad_total']);
            $variacion = abs($actual - $anterior);
            $variaciones[] = $variacion;
            $total_cantidad += $actual;
        }
        $total_cantidad += floatval($datos[count($datos)-1]['cantidad_total']);

        if (empty($variaciones) || $total_cantidad == 0) return 0;

        $variacion_promedio = array_sum($variaciones) / count($variaciones);
        $promedio_mensual = $total_cantidad / count($datos);
        
        if ($promedio_mensual == 0) return 0;
        
        $coeficiente_variacion = $variacion_promedio / $promedio_mensual;
        $confianza = max(0, min(100, (1 - $coeficiente_variacion) * 100));
        
        return round($confianza, 2);
    }

    public function getDatosHistoricos() {
        return $this->datos_historicos;
    }
}
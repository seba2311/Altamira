<?php
/**
 * Imprime un arreglo de forma legible
 * 
 * @param mixed $data El arreglo o variable a imprimir
 */
function _p($arreglo){
    ?>
    <pre><?php print_r($arreglo); ?></pre>
    <?php
}



function exito($mensaje, $titulo = 'Mensaje', $color = 'green', $icono = 'check circle') {
    // Asegurarse de que el color sea válido para Semantic UI
    $colores_validos = ['red', 'orange', 'yellow', 'olive', 'green', 'teal', 'blue', 'violet', 'purple', 'pink', 'brown', 'grey', 'black'];
    if (!in_array($color, $colores_validos)) {
        $color = 'green'; // Color por defecto si no es válido
    }
    
    // Generar un ID único para el mensaje
    $id = 'mensaje_' . uniqid();
    
    // HTML para el mensaje
    echo "<div id='$id' class='ui $color message' style='display: none; position: fixed; top: 20px; right: 20px; z-index: 1000;'>
            <i class='close icon'></i>
            <div class='header'>
                <i class='$icono icon'></i> $titulo
            </div>
            <p>$mensaje</p>
          </div>";
    
    // Script para mostrar y ocultar el mensaje
    echo "<script>
            $(document).ready(function() {
                $('#$id')
                    .transition('fade in')
                    .delay(3000)
                    .transition('fade out');
                
                $('#$id .close')
                    .on('click', function() {
                        $(this)
                            .closest('.message')
                            .transition('fade');
                    });
            });
          </script>";
}
?>
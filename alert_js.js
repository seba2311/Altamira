function alerta_js(texto,color = "error"){
    console.log(color);
    $('body').toast({
        title: 'ATENCIÓN',
        position: 'top center',
        class: color,
        message: texto
    }); 
}
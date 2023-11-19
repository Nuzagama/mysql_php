<?php

function depurar($entrada) {
    $salida = htmlspecialchars($entrada);
    $salida = trim($salida);
    return $salida;
}

//Formateamos los precio .00
function formatearPrecio($precio) {
    if (floor($precio) != $precio) {
        // Si tiene decimales, muestra con dos decimales
        return number_format($precio, 2);
    } else {
        // Si no tiene decimales, muestra sin decimales
        return number_format($precio, 0);
    }
}


?>
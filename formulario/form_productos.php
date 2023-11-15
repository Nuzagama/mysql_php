<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Productos</title>
    <link rel="stylesheet" href="../css/all.min.css">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <?php require('../conf/conection.php'); ?>
</head>
<body class = "bg-light">

<?php

    session_start();

    if($_SESSION["rol"] != "admin"){
        $alert_errorMain = "<div class='alert alert-danger mt-5' role='alert'>
        No tienes permisos para ver la página para registrar Productos.
                </div>";
        $_SESSION['alert_error'] = $alert_errorMain;
        header('location: ../main.php');
    }


    //Función para depurar
    function depurar($entrada){
        $salida = htmlspecialchars($entrada);
        $salida = trim($salida);
        return $salida;
        }


    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $temp_nombreProducto = depurar($_POST["nombreProducto"]);
        $temp_precioProducto = depurar($_POST["precioProducto"]);
        $temp_descripcionProducto = depurar($_POST["descripcionProducto"]);
        $temp_cantidadProducto = depurar($_POST["cantidadProducto"]);
        
        //  $_FILES["nombreCampo"]["queQueremosCoger"] -> TYPE, NAME, SIZE, TMP_NAME
        $nombre_imagen = $_FILES["imagen"]["name"];
        $tipo_imagen = $_FILES["imagen"]["type"];
        $tamano_imagen = $_FILES["imagen"]["size"];
        $ruta_temporal = $_FILES["imagen"]["tmp_name"];
        //echo $nombre_imagen . " " . $tipo_imagen . " " . $tamano_imagen . " " . $ruta_temporal;
        
        $ruta_final_temp = "../imagenes/" . $nombre_imagen;

        
        
        // Controlamos el Input de Nombre Producto
        if(!strlen($temp_nombreProducto) > 0){
            $err_nombreProducto = "El nombre del producto es obligatorio.
            Debes introducir algún valor válido";
        }else{
            $patron = '/^[a-zA-Z0-9ñ ]{1,40}$/';
            if(!preg_match($patron, $temp_nombreProducto)){
                $err_nombreProducto = "El nombre del producto debe tener hasta 40 caracteres y solo debe contener
                letras, números o espacios en blanco";
            }else{
                $nombreProducto = $temp_nombreProducto;
            }
        }

        // Controlamos el Input de Precio Producto
        if(!strlen($temp_precioProducto) > 0){
            $err_precioProducto = "El precio del producto es obligatorio.
            Debes introducir algún valor válido";
        }else{
            if(!is_numeric($temp_precioProducto)){
                $err_precioProducto = "El precio debe ser un número";
            }elseif((float)$temp_precioProducto <= 0){
                $err_precioProducto = "El precio debe ser mayor a 0";
            }elseif((float)$temp_precioProducto >= 100000){
                $err_precioProducto = "El precio debe ser menor a 100000";
            }else{
                $precioProducto = (float)$temp_precioProducto;
            } 
        }

        // Controlamos la descripción del producto
        if(!strlen($temp_descripcionProducto) > 0){
            $err_descripcionProducto = "La descripción del producto es obligatoria.
            Debes introducir una descripción válida.";
        }else{
            $patron = '/^[a-zA-Zñ ]{1,255}$/';
            if(!preg_match($patron, $temp_descripcionProducto)){
                $err_descripcionProducto = "La descripción debe tener una longitud 
                entre 1 y 255 letras.";
            }else{
                $descripcionProducto = $temp_descripcionProducto;
            }
        }

        // Controlamos la cantidad del Producto
        if(!strlen($temp_cantidadProducto) > 0 ){
            $err_cantidadProducto = "La cantidad del producto es obligatoria. Debes
            introducir una cantidad válida.";
        }else{
            if(!is_numeric($temp_cantidadProducto)){
                $err_cantidadProducto = "La cantidad debe ser un número";
            }elseif((int)$temp_cantidadProducto <= 0){
                $err_cantidadProducto = "La cantidad debe ser mayor a 0";
            }elseif((int)$temp_cantidadProducto > 99999){
                $err_cantidadProducto = "La cantidad debe ser menor a 99999";
            }else{
                $cantidadProducto = (int)$temp_cantidadProducto;
            }
        }

        if(!strlen($nombre_imagen) > 0){
            $err_subirImagen = "Debes introducir una imagen";
        }else{
            if($tipo_imagen == "image/jpg" || $tipo_imagen == "image/png" || $tipo_imagen == "image/jpeg"){
                if($tamano_imagen > 0 && $tamano_imagen <=1000000){
                    $ruta_final = $ruta_final_temp;
                    move_uploaded_file($ruta_temporal, $ruta_final);
                }else{
                    $err_subirImagen = "El tamaño del archivo debe ser máximo 1MB";
                }
        }else{
            $err_subirImagen = "Debes introducir una imagen del tipo permitido.";
            echo $tipo_imagen;
        }
        }
    }
?>        
<div class="container text-warning">
<h1 class="lead mt-5"><b>Formulario de Productos:</b></h1>  
<form class="form-control mt-4 bg-light" action="" method="POST" enctype="multipart/form-data">
        <fieldset>
        <div class="form-control form-control-sm mt-2">
            <label class= "text-secondary"><b>Nombre del Producto:</b></label>
            <input class= "bg-light" type="text" name="nombreProducto"><br><br>
            <?php if(isset($err_nombreProducto)) echo $err_nombreProducto; ?>
        </div>  
        <div class="form-control form-control-sm mt-2">  
            <label class= "text-secondary"><b>Precio:</b></label>
            <input class= "bg-light" type="text" name="precioProducto"><br><br>
            <?php if(isset($err_precioProducto)) echo $err_precioProducto; ?>
        </div>  
        <div class="form-control form-control-sm mt-2">    
            <label class= "text-secondary"><b>Descripción del Producto:</b></label>
            <input  class= "bg-light" type="text" name="descripcionProducto"><br><br>
            <?php if(isset($err_descripcionProducto)) echo $err_descripcionProducto; ?>
        </div>  
        <div class="form-control form-control-sm mt-2">  
            <label class= "text-secondary"><b>Cantidad:</b></label>
            <input class= "bg-light" type="text" name="cantidadProducto"><br><br>
            <?php if(isset($err_cantidadProducto)) echo $err_cantidadProducto; ?>
        </div>  
        <div class="mb-3">
            <label class="form-label">Imagen</label>
            <input class="form-control" type="file" name="imagen">
            <?php if(isset($err_subirImagen)) echo $err_subirImagen; ?>
        </div>

            <input type="submit" class="btn btn-warning mb-2 mt-3" value="Insertar Producto">
        </fieldset>
    </form>
    </div>

    <?php
        // Comprobamos que las variables están declaradas, por tanto están depuradas.
        if(isset($nombreProducto) && isset($precioProducto) && isset($descripcionProducto)
        && isset($cantidadProducto) && isset($ruta_final)){
            // Hacemos el Insert a nuestra base de datos
            $sql = "INSERT INTO productos (nombreProducto, precio, descripcion, cantidad, imagen)
            VALUES ('$nombreProducto', '$precioProducto', '$descripcionProducto', '$cantidadProducto', '$ruta_final')";
            $conexion -> query($sql);
            // ¡Nuestro nuevo producto se encuentra en la base de datos!
            echo "<h1 class='container mt-5'>Producto ingresado con éxito</h1>";

        }


    ?>

</body>
</html>
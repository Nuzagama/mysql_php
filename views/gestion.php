<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión Productos - Tantra Game</title>
    <link rel="shortcut icon" href="images/favicon.png">
    <link rel="stylesheet" href="styles/all.min.css">
    <link rel="stylesheet" href="styles/bootstrap.min.css">
    <link rel="stylesheet" href="styles/2b234ocs.css">
    <?php require('../util/conection.php'); ?>
    <?php require('../util/functions.php'); ?>
    <?php require('../util/Producto.php'); ?>
</head>

<?php
session_start();

//Comprobamos si es admin o miembro 
if ($_SESSION["rol"] != "admin") {
    $alert_errorMain = "
    No tienes permisos para ver la página a la que estás intentando acceder";
    $_SESSION['alert_error'] = $alert_errorMain;
    header('location: http://localhost/views/main');
}

//Comprobamos si está declarada si no le damos valores por defecto
if (isset($_SESSION['usuario'])) {
    $usuario = $_SESSION["usuario"];
    //Capturamos el saldo del usuario
    $sqlSaldoUsuario = "SELECT saldo FROM usuarios WHERE usuario = '" . $usuario . "'";
    $resultadoSaldo = $conexion->query($sqlSaldoUsuario);

    if ($resultadoSaldo) {
        $fila = $resultadoSaldo->fetch_assoc();
        $saldoBalance = $fila['saldo'];
    } else {
        $saldoBalance = "???";
    }
} else {
    $usuario = "Invitado";
    $saldoBalance = "Invitado";
}



if ($_SERVER["REQUEST_METHOD"] == "POST") {
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

    $ruta_final_temp = "images/" . $nombre_imagen;



    // Controlamos el Input de Nombre Producto para que no quede vacio
    if (!strlen($temp_nombreProducto) > 0) {
        $err_nombreProducto = "El nombre del producto es obligatorio.
        Debes introducir algún valor válido";
    } else {
        //Aplicamos patron al input de producto nombre
        $patron = '/^[a-zA-Z0-9ñ ]{1,40}$/';
        if (!preg_match($patron, $temp_nombreProducto)) {
            $err_nombreProducto = "El nombre del producto debe tener hasta 40 caracteres y solo debe contener
            letras, números o espacios en blanco";
        } else {
            $nombreProducto = $temp_nombreProducto;
        }
    }

    // Controlamos el Input de Precio Producto para que no quede vacio
    if (!strlen($temp_precioProducto) > 0) {
        $err_precioProducto = "El precio del producto es obligatorio.
        Debes introducir algún valor válido";
    } else {
        if (!is_numeric($temp_precioProducto)) {
            $err_precioProducto = "El precio debe ser un número";
        } elseif ((float) $temp_precioProducto <= 0) {
            $err_precioProducto = "El precio debe ser mayor a 0";
        } elseif ((float) $temp_precioProducto >= 100000) {
            $err_precioProducto = "El precio debe ser menor a 100000";
        } else {
            $precioProducto = (float) $temp_precioProducto;
        }
    }

    // Controlamos la descripción del producto que no quede vació
    if (!strlen($temp_descripcionProducto) > 0) {
        $err_descripcionProducto = "La descripción del producto es obligatoria.
        Debes introducir una descripción válida.";
    } else {
        //Aplicamos patron al input de producto nombre
        $patron = '/^[a-zA-Zñ ]{1,255}$/';
        if (!preg_match($patron, $temp_descripcionProducto)) {
            $err_descripcionProducto = "La descripción debe tener una longitud 
            entre 1 y 255 letras.";
        } else {
            $descripcionProducto = $temp_descripcionProducto;
        }
    }

    // Controlamos la cantidad del Producto que no quede vacio
    if (!strlen($temp_cantidadProducto) > 0) {
        $err_cantidadProducto = "La cantidad del producto es obligatoria. Debes
        introducir una cantidad válida.";
    } else {
        if (!is_numeric($temp_cantidadProducto)) {
            $err_cantidadProducto = "La cantidad debe ser un número";
        } elseif ((int) $temp_cantidadProducto <= 0) {
            $err_cantidadProducto = "La cantidad debe ser mayor a 0";
        } elseif ((int) $temp_cantidadProducto > 99999) {
            $err_cantidadProducto = "La cantidad debe ser menor a 99999";
        } else {
            $cantidadProducto = (int) $temp_cantidadProducto;
        }
    }
    //Controlamos que no quede vacío
    if (!strlen($nombre_imagen) > 0) {
        $err_subirImagen = "Debes introducir una imagen";
    } else {
        //Controlamos tipos y peso
        if ($tipo_imagen == "image/jpg" || $tipo_imagen == "image/png" || $tipo_imagen == "image/jpeg") {
            if ($tamano_imagen > 0 && $tamano_imagen <= 1000000) {
                $ruta_final = $ruta_final_temp;
                move_uploaded_file($ruta_temporal, $ruta_final);
            } else {
                $err_subirImagen = "El tamaño del archivo debe ser máximo 1MB";
            }
        } else {
            $err_subirImagen = "Debes introducir una imagen del tipo permitido.";
            echo $tipo_imagen;
        }
    }
}


?>



<body class="nk-body bg-white npc-default has-aside dark-mode">
    <div class="nk-app-root">
        <div class="nk-main">
            <div class="nk-wrap">
                <div class="nk-header nk-header-fixed is-light">
                    <div class="container-lg wide-xl">
                        <!-- Logo TOP -->
                        <div class="nk-header-wrap">
                            <div class="nk-header-brand">
                                <a href="http://localhost/views/main" class="logo-link">
                                    <img class="logo-light logo-img" src="images/logo.png">
                                </a>
                            </div>
                            <!-- Menu TOPNav -->
                            <div class="nk-header-menu">
                                <ul class="nk-menu nk-menu-main">
                                    <li class="nk-menu-item">
                                        <a href="http://localhost/index" class="nk-menu-link ">
                                            <span class="nk-menu-text">Página Inicial</span>
                                        </a>
                                    </li>
                                    <li class="nk-menu-item">
                                        <a href="http://localhost/views/main" class="nk-menu-link ">
                                            <span class="nk-menu-text">Novedades</span>
                                        </a>
                                    </li>
                                    <li class="nk-menu-item">
                                        <a href="http://localhost/views/main" class="nk-menu-link ">
                                            <span class="nk-menu-text">FAQs</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <!-- Menu TOPNav Right -->
                            <div class="nk-header-tools">
                                <ul class="nk-quick-nav">
                                    <a href="http://localhost/views/cesta" class="btn btn-sm btn-primary">

                                        <span class="d-none d-sm-inline mr-1">Ver Cesta</span>
                                        <em class="icon ni ni">🛒</em>
                                    </a>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Menu Mobile -->
                <div class="nk-content ">
                    <div class="container wide-xl">
                        <div class="nk-content-inner">
                            <div class="nk-aside bg-transparent" data-content="sideNav" data-toggle-overlay="true"
                                data-toggle-screen="lg" data-toggle-body="true">
                                <div class="nk-sidebar-menu" data-simplebar>

                                    <!-- Aside Menu -->
                                    <ul class="nk-menu">
                                        <li class="nk-menu-heading">
                                        <li class="nk-menu-heading">
                                            <h6 class="overline-title text-primary-alt">Panel de Usuario</h6>
                                        </li>
                                        <li class="nk-menu-item">
                                            <a href="http://localhost/views/main" class="nk-menu-link ">
                                                <span class="nk-menu-icon"><em class="icon ni ni-user"></em></span>
                                                <span class="nk-menu-text">Cuenta Principal</span>
                                                <!-- Account Status -->
                                                <span class="badge badge-pill badge-primary">
                                                    <?php echo $saldoBalance; ?><em class="icon ni ni">&#128142;</em>
                                                </span>
                                            </a>
                                        </li>
                                        <li class="nk-menu-item">
                                            <a href="http://localhost/views/cesta" class="nk-menu-link ">
                                                <span class="nk-menu-icon"><em class="icon ni ni-cart"></em></span>
                                                <span class="nk-menu-text">Cesta</span>
                                            </a>
                                        </li>
                                        <li class="nk-menu-item">
                                            <a href="http://localhost/views/sesiones" class="nk-menu-link ">
                                                <span class="nk-menu-icon"><em class="icon ni ni-signout"></em></span>
                                                <span class="nk-menu-text">Logout</span>
                                            </a>
                                        </li>
                                    </ul>


                                    <?php

                                    if (isset($_SESSION["rol"])) {
                                        if ($_SESSION["rol"] == "admin") {
                                            ?>
                                            <!-- Aside Menu -->
                                            <ul class="nk-menu mt-5">
                                                <li class="nk-menu-heading">
                                                    <h6 class="overline-title text-primary-alt">Panel de Administrador</h6>
                                                </li>
                                                <li class="nk-menu-item active current-page">
                                                    <a href="http://localhost/views/gestion" class="nk-menu-link ">
                                                        <span class="nk-menu-icon"><em class="icon ni ni-box"></em></span>
                                                        <span class="nk-menu-text">Gestionar Productos</span>
                                                    </a>
                                                </li>
                                                <li class="nk-menu-item">
                                                    <a href="http://localhost/views/saldo" class="nk-menu-link ">
                                                        <span class="nk-menu-icon"><em class="icon ni ni-coins"></em></span>
                                                        <span class="nk-menu-text">Gestionar Gemas</span>
                                                    </a>
                                                </li>
                                            </ul>

                                            <?php
                                        }
                                    }

                                    ?>
                                </div>
                            </div>
                            <!-- Aside Menu ENDS -->
                            <div class="nk-content-body">
                                <div class="nk-content-wrap">


                                    <div class="row g-gs">
                                        <div class="col-12">
                                            <div class="card card-bordered card-full">
                                                <div class="card-inner">

                                                    <?php if (isset($error_message)): ?>
                                                        <div class="alert alert-fill alert-danger alert-icon">
                                                            <em class="icon ni ni-cross-circle"></em>
                                                            <strong>
                                                                <?php echo $error_message; ?> 😅
                                                            </strong>
                                                        </div>
                                                    <?php endif; ?>


                                                    <!--Saludos al usuario-->
                                                    <div class="card-title-group">
                                                        <div class="card-title">
                                                            <h3 style="text-shadow: 1px 1px 3px rgb(201 132 30);">
                                                                Estás administrando como <span id="user-name">
                                                                    <b>
                                                                        <?php echo $usuario; ?>
                                                                    </b>
                                                                    🔧</h3>
                                                        </div>
                                                    </div>
                                                    <?php if (isset($_SESSION['alert_error']))
                                                        echo $_SESSION['alert_error']; ?>

                                                    <div class="card-title-group">
                                                        <div class="card-title mt-5">
                                                            <h4 style="text-shadow: 1px 1px 3px rgb(201 132 30);">
                                                                Gestionar productos 📦</h4>
                                                        </div>
                                                    </div>

                                                    <form class="mt-5" action="" method="POST"
                                                        enctype="multipart/form-data" style="color:white;">
                                                        <div class="form-group">
                                                            <div class="form-label-group">
                                                                <label class="form-label"><b>Nombre del
                                                                        Producto:</b></label>
                                                            </div>
                                                            <input class="form-control form-control-lg" type="text"
                                                                name="nombreProducto"><br><br>
                                                            <?php if (isset($err_nombreProducto)): ?>
                                                                <div class="alert alert-fill alert-danger alert-icon">
                                                                    <em class="icon ni ni-cross-circle"></em>
                                                                    <strong>
                                                                        <?php echo $err_nombreProducto; ?> 😅
                                                                    </strong>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="form-label-group">
                                                                <label class="form-label"><b>Precio:</b></label>
                                                            </div>
                                                            <input class="form-control form-control-lg" type="text"
                                                                name="precioProducto"><br><br>
                                                            <?php if (isset($err_precioProducto)): ?>
                                                                <div class="alert alert-fill alert-danger alert-icon">
                                                                    <em class="icon ni ni-cross-circle"></em>
                                                                    <strong>
                                                                        <?php echo $err_precioProducto; ?> 😅
                                                                    </strong>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="form-label-group">
                                                                <label class="form-label"><b>Descripción del
                                                                        Producto:</b></label>
                                                            </div>
                                                            <input class="form-control form-control-lg" type="text"
                                                                name="descripcionProducto"><br><br>
                                                            <?php if (isset($err_descripcionProducto)): ?>
                                                                <div class="alert alert-fill alert-danger alert-icon">
                                                                    <em class="icon ni ni-cross-circle"></em>
                                                                    <strong>
                                                                        <?php echo $err_descripcionProducto; ?> 😅
                                                                    </strong>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="form-label-group">
                                                                <label class="form-label"><b>Cantidad:</b></label>
                                                            </div>
                                                            <input class="form-control form-control-lg" type="text"
                                                                name="cantidadProducto"><br><br>
                                                            <?php if (isset($err_cantidadProducto)): ?>
                                                                <div class="alert alert-fill alert-danger alert-icon">
                                                                    <em class="icon ni ni-cross-circle"></em>
                                                                    <strong>
                                                                        <?php echo $err_cantidadProducto; ?> 😅
                                                                    </strong>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="form-label-group">
                                                                <label class="form-label">Imagen</label>
                                                            </div>
                                                            <input class="form-control form-control-lg" type="file"
                                                                name="imagen">
                                                            <?php if (isset($err_subirImagen)): ?>
                                                                <div class="alert alert-fill alert-danger alert-icon mt-2">
                                                                    <em class="icon ni ni-cross-circle"></em>
                                                                    <strong>
                                                                        <?php echo $err_subirImagen; ?> 😅
                                                                    </strong>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                        <input type="submit" class="btn btn-warning mb-2 mt-3"
                                                            value="Insertar Producto">
                                                    </form>

                                                    <?php
                                                    // Comprobamos que las variables están declaradas, por tanto están depuradas.
                                                    if (
                                                        isset($nombreProducto) && isset($precioProducto) && isset($descripcionProducto)
                                                        && isset($cantidadProducto) && isset($ruta_final)
                                                    ) {
                                                        // Hacemos el Insert a nuestra base de datos
                                                        $sql = "INSERT INTO productos (nombreProducto, precio, descripcion, cantidad, imagen)
                                                        VALUES ('$nombreProducto', '$precioProducto', '$descripcionProducto', '$cantidadProducto', '$ruta_final')";
                                                        $conexion->query($sql);
                                                        // ¡Nuestro nuevo producto se encuentra en la base de datos!
                                                        $success_message = "Producto ingresado con éxito";

                                                    }


                                                    ?>

                                                    <?php if (isset($success_message)): ?>
                                                        <div class="alert alert-fill alert-success alert-icon">
                                                            <em class="icon ni ni-check-circle"></em>
                                                            <strong>
                                                                <?php echo $success_message; ?> 😎
                                                            </strong>
                                                            <button class="close" data-bs-dismiss="alert"></button>
                                                        </div>
                                                    <?php endif; ?>

                                                    <div class="card-title-group">
                                                        <div class="card-title mt-5">
                                                            <h4 style="text-shadow: 1px 1px 3px rgb(201 132 30);">
                                                                Productos registrados 🚀🛸</h4>
                                                        </div>
                                                    </div>

                                                    <?php

                                                    $sql = "SELECT * FROM productos";
                                                    $resultado = $conexion->query($sql);
                                                    ?>

                                                    <table class="table mt-3">
                                                        <thead>
                                                            <tr>
                                                                <th>ID</th>
                                                                <th>Nombre Producto</th>
                                                                <th>Precio</th>
                                                                <th>Descripción</th>
                                                                <th>Cantidad</th>
                                                                <th>Ruta Imagen</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php if ($resultado->num_rows > 0): ?>
                                                                <?php while ($fila = $resultado->fetch_assoc()): ?>
                                                                    <tr>
                                                                        <td>
                                                                            <?php echo $fila['idProducto']; ?>
                                                                        </td>
                                                                        <td>
                                                                            <?php echo $fila['nombreProducto']; ?>
                                                                        </td>
                                                                        <td>
                                                                            <?php echo $fila['precio']; ?>
                                                                        </td>
                                                                        <td>
                                                                            <?php echo $fila['descripcion']; ?>
                                                                        </td>
                                                                        <td>
                                                                            <?php echo $fila['cantidad']; ?>
                                                                        </td>
                                                                        <td>
                                                                            <?php echo $fila['imagen']; ?>
                                                                        </td>
                                                                    </tr>
                                                                <?php endwhile; ?>
                                                            <?php else: ?>
                                                                <tr>
                                                                    <td colspan="5">No hay productos registrados.</td>
                                                                </tr>
                                                            <?php endif; ?>
                                                        </tbody>
                                                    </table>


                                                </div>
                                            </div>
                                        </div>
                                        <!-- Footer -->
                                        <div class="nk-footer bg-transparent">
                                            <div class="container wide-xl">
                                                <div class="nk-footer-wrap g-2">
                                                    <div class="nk-footer-copyright">
                                                        <div class="footer__copyright-info">
                                                            <div class="footer__copyright-info--text">
                                                                <small style="opacity:0.5;">
                                                                    &copy; 2023 TTO System. All Rights Reserved.
                                                                </small>
                                                            </div>
                                                            <div class="footer__copyright-info--policy"
                                                                style="display:flex; flex-direction: row;">
                                                                <a href="http://localhost/views/main">RULES & TERMS</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

</body>

</html>
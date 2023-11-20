<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página principal - Tantra Game</title>
    <link rel="shortcut icon" href="images/favicon.png">
    <link rel="stylesheet" href="styles/all.min.css">
    <link rel="stylesheet" href="styles/bootstrap.min.css">
    <link rel="stylesheet" href="styles/2b234ocs.css">
    <?php require('../util/conection.php'); ?>
    <?php require('../util/functions.php'); ?>
    <?php require('../util/producto.php'); ?>
</head>

<?php
session_start();
//Comprobamos si está declarada si no le damos valores por defecto
if (isset($_SESSION['usuario'])) {
    $usuario = $_SESSION["usuario"];
    $saldoBalance = $_SESSION["saldo"];
} else {
    $usuario = "Invitado";
    $saldoBalance = "Invitado";
}

// Limpiamos mensajes de error
if (isset($_SESSION["alert_error"])) {
    $error_message = $_SESSION["alert_error"];
    unset($_SESSION["alert_error"]);
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idProducto = depurar($_POST['id_producto']);
    $cantidad = depurar($_POST['cantidad']);

    if (isset($idProducto) && isset($cantidad)) {
        //Obtenemos ID CESTA
        $sqlCesta = "SELECT idCesta FROM cestas WHERE usuario = '" . $usuario . "'";
        $resultadoCesta = $conexion->query($sqlCesta);  
        //Controlamos que precioTotal no sea mayor a 99.999
        //Sacamos el PrecioTotal del usuario en ese momento
        $sqlPrecioTotal = "SELECT precioTotal FROM cestas WHERE usuario = '" . $usuario . "'";
        $resultadoPrecioTotal = $conexion->query($sqlPrecioTotal);

      if ($resultadoPrecioTotal->num_rows > 0) {
            $filaPrecioTotal = $resultadoPrecioTotal->fetch_assoc();
            $precioTotalActual = $filaPrecioTotal['precioTotal']; // Sacamos el precio total actual

            $sqlPrecioProducto = "SELECT precio FROM productos WHERE idProducto = '" . $idProducto . "'";
            $resultadoPrecioProducto = $conexion->query($sqlPrecioProducto);
            

            if ($resultadoPrecioProducto->num_rows > 0) {
                $filaPrecioProducto = $resultadoPrecioProducto->fetch_assoc();
                $precioProducto = $filaPrecioProducto['precio']; // Sacamos el precio del producto

                //Operamos para saber si el nuevo precioTotal, si llega a ser mayor a 99999 cancelamos la operación
                $tempPrecioTotal = $precioProducto * (int) $cantidad;
                $nuevoPrecioTotal = $tempPrecioTotal + $precioTotalActual;

                if ($nuevoPrecioTotal > 99999) {
                    $error_message = "El coste total de tu cesta no puede superar las 99.999 gemas ";
                } else {
                    if ($resultadoCesta && $resultadoCesta->num_rows > 0) {
                        while ($filaCesta = $resultadoCesta->fetch_assoc()) {
                            $idCesta = $filaCesta["idCesta"];

                            // Verificamos si el producto ya está en la cesta
                            $sqlVerificar = "SELECT cantidad FROM ProductosCesta WHERE idProducto = '$idProducto' AND idCesta = '$idCesta'";
                            $resultadoVerificar = $conexion->query($sqlVerificar);

                            if ($resultadoVerificar && $resultadoVerificar->num_rows > 0) {
                                // El producto ya existe, actualizamos la cantidad
                                $filaVerificar = $resultadoVerificar->fetch_assoc();
                                $nuevaCantidad = $filaVerificar['cantidad'] + $cantidad;

                                $sqlUpdate = "UPDATE ProductosCesta SET cantidad = '$nuevaCantidad' WHERE idProducto = '$idProducto' AND idCesta = '$idCesta'";
                                if (!$conexion->query($sqlUpdate)) {
                                    $error_message = "Error al actualizar producto en la cesta: ";
                                }
                            } else {
                                // El producto no existe, insertamos uno nuevo
                                $sqlInsert = "INSERT INTO ProductosCesta (idProducto, idCesta, cantidad) VALUES ('$idProducto', '$idCesta', '$cantidad')";
                                if (!$conexion->query($sqlInsert)) {
                                    $error_message = "Error al añadir producto a la cesta: ";
                                }
                            }
                        }

                        // Después de añadir el producto a la cesta, actualizamos la cantidad en la tabla de productos
                        $sqlActualizarCantidad = "UPDATE productos SET cantidad = cantidad - $cantidad WHERE idProducto = $idProducto";
                        if (!$conexion->query($sqlActualizarCantidad)) {
                            $error_message = "Error al actualizar la cantidad del producto ";
                        }

                        // $idCesta es el ID de la cesta del usuario
                        $sqlCalcularTotal = "SELECT SUM(p.precio * pc.cantidad) AS precioTotal
                                            FROM productos p
                                            JOIN ProductosCesta pc ON p.idProducto = pc.idProducto
                                            WHERE pc.idCesta = $idCesta";

                        $resultado = $conexion->query($sqlCalcularTotal);
                        if ($resultado->num_rows > 0) {
                            $fila = $resultado->fetch_assoc();
                            $precioTotalActualizado = $fila['precioTotal'];

                            // Actualizamos el precio total en la cesta
                            $sqlActualizarCesta = "UPDATE cestas SET precioTotal = $precioTotalActualizado WHERE idCesta = $idCesta";
                            $conexion->query($sqlActualizarCesta);
                        }
                    } else {
                        $error_message = "Cesta no encontrada o error en la consulta ";
                    }
                }
            } else {
                $error_message = "Error al obtener el precio del producto";
            }
        } else {
            $error_message = "No puedes agregar productos como Invitado. Inicia sesión porfavor";
        }
    } else {
        $error_message = "Fallo al añadir producto a la cesta";
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
                                        <li class="nk-menu-item active current-page">
                                            <a href="http://localhost/views/main" class="nk-menu-link ">
                                                <span class="nk-menu-icon"><em class="icon ni ni-home"></em></span>
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

                                        <?php
                                        //Mostraremos un menu según el tipo de usuario
                                        if ($usuario == "Invitado") {
                                            ?>
                                            <li class="nk-menu-item">
                                                <a href="http://localhost/views/registro" class="nk-menu-link ">
                                                    <span class="nk-menu-icon"><em class="icon ni ni-user-add"></em></span>
                                                    <span class="nk-menu-text">Registrar Cuenta</span>
                                                </a>
                                            </li>
                                            <li class="nk-menu-item">
                                                <a href="http://localhost/views/login" class="nk-menu-link ">
                                                    <span class="nk-menu-icon"><em class="icon ni ni-signin"></em></span>
                                                    <span class="nk-menu-text">Iniciar Sesión</span>
                                                </a>
                                            </li>
                                            <?php
                                        } else {
                                            ?>
                                            <li class="nk-menu-item">
                                                <a href="http://localhost/views/sesiones" class="nk-menu-link ">
                                                    <span class="nk-menu-icon"><em class="icon ni ni-signout"></em></span>
                                                    <span class="nk-menu-text">Logout</span>
                                                </a>
                                            </li>
                                            <?php
                                        }


                                        ?>

                                    </ul>


                                    <?php
                                    //Menu de rol administrador
                                    if (isset($_SESSION["rol"])) {
                                        if ($_SESSION["rol"] == "admin") {
                                            ?>
                                            <!-- Aside Menu -->
                                            <ul class="nk-menu mt-5">
                                                <li class="nk-menu-heading">
                                                    <h6 class="overline-title text-primary-alt">Panel de Administrador</h6>
                                                </li>
                                                <li class="nk-menu-item">
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
                                                    <!-- Mostramos mensajes de error y éxito -->
                                                    <?php if (isset($error_message)): ?>
                                                        <div class="alert alert-fill alert-danger alert-icon">
                                                            <em class="icon ni ni-cross-circle"></em>
                                                            <strong>
                                                                <?php echo $error_message; ?> 😅
                                                            </strong>
                                                            <button class="close" data-bs-dismiss="alert"></button>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php if (isset($success_message)): ?>
                                                        <div class="alert alert-fill alert-success alert-icon">
                                                            <em class="icon ni ni-check-circle"></em>
                                                            <strong>
                                                                <?php echo $success_message; ?> 😎
                                                            </strong>
                                                            <button class="close" data-bs-dismiss="alert"></button>
                                                        </div>
                                                    <?php endif; ?>
                                                    <!--Saludos al usuario-->
                                                    <div class="card-title-group">
                                                        <div class="card-title">
                                                            <h3 style="text-shadow: 1px 1px 3px rgb(201 132 30);">
                                                                Bienvenido, <span id="user-name">
                                                                    <b>
                                                                        <?php echo $usuario; ?>
                                                                    </b>
                                                                    &#128075;</h3>
                                                        </div>
                                                    </div>
                                                    <?php if (isset($_SESSION['alert_error']))
                                                        echo $_SESSION['alert_error']; ?>

                                                    <div class="card-title-group">
                                                        <div class="card-title mt-5">
                                                            <h4 style="text-shadow: 1px 1px 3px rgb(201 132 30);">Aquí
                                                                podrás conseguir nuevas mascotas para tu personaje</h4>
                                                        </div>
                                                    </div>
                                                    <?php
                                                    //Creamos el Array de Objetos para luego mostrarlos
                                                    $sql = "SELECT * FROM productos";
                                                    $resultado = $conexion->query($sql);
                                                    $objetos = [];
                                                    while ($fila = $resultado->fetch_assoc()) {
                                                        $objeto = new Producto(
                                                            $fila["idProducto"],
                                                            $fila["nombreProducto"],
                                                            $fila["precio"],
                                                            $fila["descripcion"],
                                                            $fila["cantidad"],
                                                            $fila["imagen"]
                                                        );

                                                        array_push($objetos, $objeto);
                                                    }
                                                    ?>

                                                    <table class="table table-dark table-hover mt-3 text-center">
                                                        <thead>
                                                            <tr
                                                                style="height:40px; text-shadow: 1px 1px 3px rgb(201 132 30);">
                                                                <th class="overline-title">Nombre Producto</th>
                                                                <th class="overline-title">Precio</th>
                                                                <th class="overline-title">Descripción</th>
                                                                <th class="overline-title">Cantidad</th>
                                                                <th></th>
                                                                <th></th>
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            //Bucle para mostrar el catalogo de productos
                                                            foreach ($objetos as $objeto) {
                                                                echo "<tr>";
                                                                echo "<td class='align-middle'>" . $objeto->nombreProducto . "</td>";
                                                                echo "<td class='align-middle'>&#128142;" . $objeto->precio . "</td>";
                                                                echo "<td class='align-middle' style='max-width:360px;'>" . $objeto->descripcion . "</td>";
                                                                if ($objeto->cantidad > 0) {
                                                                    echo "<td class='align-middle'>" . $objeto->cantidad . "</td>"; ?>
                                                                    <td><img src=" <?php echo $objeto->imagen ?> "
                                                                            height="100px" width="100px"></td>
                                                                    <form action="" method="POST">
                                                                        <td>
                                                                            <input type="hidden" name="id_producto"
                                                                                value=" <?php echo $objeto->idProducto ?> ">
                                                                            <select name="cantidad" class="form-select mt-4"
                                                                                style="opacity:1;">

                                                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                                                    <option value="<?php echo $i; ?>">
                                                                                        <?php echo $i; ?>
                                                                                    </option>
                                                                                <?php endfor; ?>

                                                                            </select>
                                                                        </td>
                                                                        <?php
                                                                } else {
                                                                    echo "<td class='align-middle''><span class='badge badge-pill badge-danger'>Agotado</span></td>"; ?>
                                                                        <td><img src=" <?php echo $objeto->imagen ?> "
                                                                                height="100px" width="100px"
                                                                                style="filter: saturate(0%);"></td>
                                                                        <td>
                                                                            <select name="cantidad" class="form-select mt-4"
                                                                                style="opacity:1;">
                                                                                <option value="0">0</option>
                                                                            </select>
                                                                        </td>
                                                                        <?php
                                                                }
                                                                ?>
                                                                    <td>
                                                                        <input class="btn btn-warning mt-4" type="submit"
                                                                            value="Lo quiero 🛒">
                                                                    </td>
                                                                </form>
                                                                <?php
                                                                echo "</tr>";
                                                            }
                                                            ?>
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
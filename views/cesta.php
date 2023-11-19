<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tu Cesta - Tantra Game</title>
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


if (isset($_SESSION['usuario'])) {
    $usuario = $_SESSION["usuario"];
    $saldoBalance = $_SESSION["saldo"]; 
} else {
    $usuario = "Invitado";
    $saldoBalance = "Invitado";
}



//Sacamos el Precio Total de la Tabla cestas,
$precioTotal = 0;
$sqlPrecioTotal = "SELECT precioTotal FROM cestas WHERE usuario = '$usuario'";
$resultadoPrecio = $conexion->query($sqlPrecioTotal);
if ($resultadoPrecio->num_rows > 0) {
    $filaPrecio = $resultadoPrecio->fetch_assoc();
    $precioTotal = $filaPrecio['precioTotal']; 
} else {
    $precioTotal = 0;
}

//Si tenemos un POST y el botón de comprar cesta se ha pulsado ejecutamos el bloque
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['finalizarPedido'])) {

    //Controlamos que el saldo sea suficiente para hacer la compra
    if ($saldoBalance >= $precioTotal) {
        //Insertamos 
        $sqlPedido = "INSERT INTO Pedidos (usuario, precioTotal) SELECT usuario, precioTotal FROM cestas WHERE usuario = '$usuario'";
        $conexion->query($sqlPedido);
        //Obtenemos el último ID insertado
        $idPedido = $conexion->insert_id;

        $sqlProductosCesta = "SELECT idProducto, cantidad FROM productosCesta WHERE idCesta = (SELECT idCesta FROM cestas WHERE usuario = '$usuario')";
        $productos = $conexion->query($sqlProductosCesta);

        //Declaramos la variable que iremos incrementando por cada tipo de producto en la cesta
        $linea = 1;
        while ($producto = $productos->fetch_assoc()) {
            $sqlLineaPedido = "INSERT INTO LineasPedidos (lineaPedido, idProducto, idPedido, precioUnitario, cantidad) SELECT $linea, idProducto, $idPedido, precio, '{$producto['cantidad']}' FROM productos WHERE idProducto = '{$producto['idProducto']}'";
            $conexion->query($sqlLineaPedido);
            $linea++;
        }

        //Borramos la cesta del usuario
        $sqlVaciarCesta = "DELETE FROM productosCesta WHERE idCesta = (SELECT idCesta FROM cestas WHERE usuario = '$usuario')";
        $conexion->query($sqlVaciarCesta);

        $sqlActualizarCesta = "UPDATE cestas SET precioTotal = 0 WHERE usuario = '$usuario'";
        $conexion->query($sqlActualizarCesta);

        //Actualizamos el saldo del usuario

            $nuevoBalance = $saldoBalance - $precioTotal;
            $sqlActualizarSaldo = "UPDATE usuarios SET saldo = $nuevoBalance WHERE usuario = '$usuario'";
            $conexion->query($sqlActualizarSaldo);
            $_SESSION["saldo"] = $nuevoBalance;

            $success_message = "Pedido realizado con éxito.";
        }else{
            $error_message = "No tienes suficientes gemas crack y deja de tocar el HTML 👻👻👻";
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
                                                <span class="nk-menu-icon"><em class="icon ni ni-home"></em></span>
                                                <span class="nk-menu-text">Cuenta Principal</span>
                                                <!-- Account Status -->
                                                <span class="badge badge-pill badge-primary">
                                                    <?php echo $saldoBalance; ?><em class="icon ni ni">&#128142;</em>
                                                </span>
                                            </a>
                                        </li>
                                        <li class="nk-menu-item active current-page">
                                            <a href="http://localhost/views/cesta" class="nk-menu-link">
                                                <span class="nk-menu-icon"><em class="icon ni ni-cart"></em></span>
                                                <span class="nk-menu-text">Cesta</span>
                                            </a>
                                        </li>

                                        <?php
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

                                                    <?php if (isset($success_message)): ?>
                                                        <div class="alert alert-fill alert-success alert-icon">
                                                            <em class="icon ni ni-check-circle"></em>
                                                            <strong>
                                                                <?php echo $success_message; ?> 😎
                                                            </strong>
                                                        </div>
                                                    <?php endif; ?>

                                                    <div class="card-title-group">
                                                        <div class="card-title">
                                                            <h3 style="text-shadow: 1px 1px 3px rgb(201 132 30);">
                                                                La Cesta de <span id="user-name">
                                                                    <b>
                                                                        <?php echo $usuario; ?>
                                                                    </b>
                                                                    🛒</h3>
                                                        </div>
                                                    </div>
                                                    <?php if (isset($_SESSION['alert_error']))
                                                        echo $_SESSION['alert_error']; ?>

                                                    <div class="card-title-group">
                                                        <div class="card-title mt-5">
                                                            <h4 style="text-shadow: 1px 1px 3px rgb(201 132 30);">Estás a punto de comprar los siguientes productos ...</h4>
                                                        </div>
                                                    </div>


                                                    <table class="table table-dark table-hover mt-3 text-center">
                                                        <thead>
                                                            <tr
                                                                style="height:40px; text-shadow: 1px 1px 3px rgb(201 132 30);">
                                                                <th class="overline-title">Nombre Producto</th>
                                                                <th class="overline-title">Precio</th>
                                                                <th class="overline-title">Cantidad</th>
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            // Obtenemos los productos de la cesta
                                                            $sql = "SELECT p.nombreProducto, p.imagen, p.precio, pc.cantidad
                                                                    FROM productos p
                                                                    JOIN ProductosCesta pc ON p.idProducto = pc.idProducto
                                                                    JOIN cestas c ON pc.idCesta = c.idCesta
                                                                    WHERE c.usuario = '$usuario'";

                                                            $resultado = $conexion->query($sql);

                                                            if ($resultado->num_rows > 0) {
                                                                while ($fila = $resultado->fetch_assoc()) {
                                                                    echo "<tr>";
                                                                    echo "<td class='align-middle'>" . $fila['nombreProducto'] . "</td>";
                                                                    $precioFormateado = formatearPrecio($fila['precio']);
                                                                    echo "<td class='align-middle'>💎" . $precioFormateado . "</td>";
                                                                    echo "<td class='align-middle'>" . $fila['cantidad'] . "</td>";
                                                                    echo "<td class='align-middle'><img src='" . $fila['imagen'] . "' height='50px' width='50px'></td>";
                                                                    echo "</tr>";
                                                                }
                                                                // Mostramos el precio y formateamos ... le quitamos .00 final
                                                                $precioTotalFormateado = formatearPrecio($precioTotal);
                                                                echo "<tr><td colspan='4'>
                                                                Precio Total de tu Cesta💎$precioTotalFormateado
                                                                </td></tr>";?>
                                                                <?php
                                                                echo "<tr><td colspan='4'>
                                                                Tu Saldo actual💎$saldoBalance
                                                                </td></tr>";?>
                                                                <?php
                                                            } else {
                                                                $error_message = "No hay productos en la cesta";
                                                            }

                                                            ?>
                                                        </tbody>
                                                    </table>

                                                    <?php if (isset($error_message)): ?>
                                                    <div class="alert alert-fill alert-info alert-icon mt-2">
                                                        <em class="icon ni ni-cross-circle"></em>
                                                        <strong><?php echo $error_message; ?> 😅</strong>
                                                    </div>
                                                    <?php else: ?>
                                                <?php 
                                                //Controlamos que si no tienes suficiente saldo el botón aparezca deshabilitado
                                                    if($saldoBalance >= $precioTotal){
                                                        $botonDeshabilitado = "";
                                                    } else {
                                                        $botonDeshabilitado = "disabled";
                                                        $saldo_message = "No tienes gemas suficientes para realizar la compra";
                                                    }
                                                    ?>

                                                    <form class="center" action="" method="POST">
                                                        <input class="btn btn-warning mt-4" type="submit" name="finalizarPedido" value="Finalizar Pedido" <?php echo $botonDeshabilitado; ?>>
                                                    </form>
                                                
                                                <?php if (isset($saldo_message)): ?>
                                                    <div class="alert alert-fill alert-danger alert-icon mt-2">
                                                        <em class="icon ni ni-cross-circle"></em>
                                                        <strong><?php echo $saldo_message; ?> 😅</strong>
                                                    </div>
                                                <?php endif; ?>
                                                <?php endif; ?>

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
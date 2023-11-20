<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión Gemas - Tantra Game</title>
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

//Comprobamos si es admin o miembro
if ($_SESSION["rol"] != "admin") {
    $alert_errorMain = "
    No tienes permisos para ver la página a la que estás intentando acceder";

    $_SESSION['alert_error'] = $alert_errorMain;
    header('location: http://localhost/views/main');
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['nombreUsuario']) && isset($_POST['cantidadSaldo'])) {
        $nombreUsuario = depurar($_POST['nombreUsuario']);
        $cantidadSaldo = depurar($_POST['cantidadSaldo']);

        if (!strlen($nombreUsuario) > 0) {
            $err_nombreUsuario = "
        No puedes introducir un campo de usuario vacío";


        } elseif (!strlen($cantidadSaldo) > 0) {
            $err_cantidadSaldo =
                "No puedes introducir un campo de contraseña vacío";

        } else {
            // Convertimos cantidadSaldo a float
            $cantidadSaldo = floatval($cantidadSaldo);

            // Verificamos que la cantidad de saldo sea un número y no exceda ciertos límites
            if (is_numeric($cantidadSaldo) && $cantidadSaldo >= 0 && $cantidadSaldo <= 100000) {
                $sql = "UPDATE usuarios SET saldo = '$cantidadSaldo' WHERE usuario = '$nombreUsuario'";
                if ($conexion->query($sql) === TRUE) {
                    $success_message = "Saldo Actualizado con éxito";
                } else {
                    $error_message = "Error al actualizar el saldo";
                }
            } else {
                $error_message = "Cantidad de saldo inválida.";
            }



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
                                                <li class="nk-menu-item ">
                                                    <a href="http://localhost/views/gestion" class="nk-menu-link ">
                                                        <span class="nk-menu-icon"><em class="icon ni ni-box"></em></span>
                                                        <span class="nk-menu-text">Gestionar Productos</span>
                                                    </a>
                                                </li>
                                                <li class="nk-menu-item active current-page">
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
                                                                Gestionar Saldo 💎</h4>
                                                        </div>
                                                    </div>

                                                    <form class="mt-5" action="" method="POST"
                                                        enctype="multipart/form-data" style="color:white;">
                                                        <div class="form-group">
                                                            <div class="form-label-group">
                                                                <label class="form-label"><b>Nombre del
                                                                        Usuario:</b></label>
                                                            </div>
                                                            <input class="form-control form-control-lg" type="text"
                                                                name="nombreUsuario"><br><br>
                                                            <?php if (isset($err_nombreUsuario)): ?>
                                                                <div class="alert alert-fill alert-danger alert-icon">
                                                                    <em class="icon ni ni-cross-circle"></em>
                                                                    <strong>
                                                                        <?php echo $err_nombreUsuario; ?> 😅
                                                                    </strong>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="form-label-group">
                                                                <label class="form-label"><b>Saldo:</b></label>
                                                            </div>
                                                            <input class="form-control form-control-lg" type="text"
                                                                name="cantidadSaldo"><br><br>
                                                            <?php if (isset($err_cantidadSaldo)): ?>
                                                                <div class="alert alert-fill alert-danger alert-icon">
                                                                    <em class="icon ni ni-cross-circle"></em>
                                                                    <strong>
                                                                        <?php echo $err_cantidadSaldo; ?> 😅
                                                                    </strong>
                                                                </div>
                                                            <?php endif; ?>

                                                            <input type="submit" class="btn btn-warning mb-2 mt-3"
                                                                value="Insertar Gemas💎">
                                                    </form>

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
                                                                Usuarios registrados 🙋‍♀️</h4>
                                                        </div>
                                                    </div>

                                                    <?php

                                                    $sql = "SELECT * FROM usuarios";
                                                    $resultado = $conexion->query($sql);
                                                    ?>

                                                    <table class="table mt-3">
                                                        <thead>
                                                            <tr>
                                                                <th>Usuario</th>
                                                                <th>Fecha de Nacimiento</th>
                                                                <th>Rol</th>
                                                                <th>Saldo</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php if ($resultado->num_rows > 0): ?>
                                                                <?php while ($fila = $resultado->fetch_assoc()): ?>
                                                                    <tr>
                                                                        <td>
                                                                            <?php echo $fila['usuario']; ?>
                                                                        </td>
                                                                        <td>
                                                                            <?php echo $fila['fechaNacimiento']; ?>
                                                                        </td>
                                                                        <td>
                                                                            <?php echo $fila['rol']; ?>
                                                                        </td>
                                                                        <td>
                                                                            <?php echo $fila['saldo']; ?>
                                                                        </td>
                                                                    </tr>
                                                                <?php endwhile; ?>
                                                            <?php else: ?>
                                                                <tr>
                                                                    <td colspan="5">No hay usuarios registrados.</td>
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
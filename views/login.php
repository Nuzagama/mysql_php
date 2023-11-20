<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Tantra Game</title>
    <link rel="shortcut icon" href="images/favicon.png">
    <link rel="stylesheet" href="styles/all.min.css">
    <link rel="stylesheet" href="styles/bootstrap.min.css">
    <link rel="stylesheet" href="styles/2b234ocs.css">
    <?php require('../util/conection.php'); ?>
    <?php require('../util/functions.php'); ?>
</head>

<body class="nk-body bg-white npc-default has-aside dark-mode">
    <?php
    session_start();


    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $usuario_temp = depurar($_POST["usuario"]);
        $contra_temp = depurar($_POST["contra"]);
        //Controlamos que el input no esté vacío
        if (!strlen($usuario_temp) > 0) {
            $alert_usuario = "<div class='alert alert-danger mt-2' role='alert'>
        No puedes introducir un campo de usuario vacío
        </div>";
        //Controlamos que el input no esté vacío
        } elseif (!strlen($contra_temp) > 0) {
            $alert_contra = "<div class='alert alert-danger mt-2' role='alert '>
        No puedes introducir un campo de contraseña vacío
                </div>";
        } else {
            //Controlamos que el usuario tenga los mismo requisitos que en el registro
            if (!preg_match("/^[a-zA-Z_]{4,12}$/", $usuario_temp)) {
                $alert_usuario =
                    "<div class='alert alert-danger mt-2' role='alert '>
                    El usuario o contraseña no es válido               
                    </div>";
            //Controlamos que la contraseña no exceda 255
            } elseif (strlen($contra_temp) > 255) {
                $alert_error =
                    "<div class='alert alert-danger mt-2' role='alert mt-2'>
                    El usuario o contraseña no es válido   
                    </div>";
            //Controlamos que la contraseña tenga los mismos requisitos que en el registro
            } elseif (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d]).{8,20}$/", $contra_temp)) {
                $alert_error =
                "<div class='alert alert-danger mt-2' role='alert'>
                El usuario o contraseña no es válido            
                </div>";
            } else {
                
                //Buscamos el usuario
                $sql = "SELECT * FROM usuarios WHERE usuario = '$usuario_temp'";
                $resultado = $conexion->query($sql);

                if ($resultado->num_rows === 0) {
                    $alert_usuario ="<div class='alert alert-danger mt-2'>
                        <em class='icon ni ni-alert-circle'></em>
                        <strong>Usuario o Contraseña incorrecta.</strong>
                    </div>";
                } else {
                    //Extraemos datos necesarios del usuario
                    while ($fila = $resultado->fetch_assoc()) {
                        $contrasena_cifrada = $fila["contrasena"];
                        $recuperarRol = $fila["rol"];
                        $saldoBalance = $fila["saldo"];
                    }
                    //Comparamos contraseñas si son equivalentes nos permite acceder
                    $acceso_valido = password_verify($contra_temp, $contrasena_cifrada);

                    if ($acceso_valido) {
                        $alert_exito = "<div class='alert alert-success mt-2' role='alert'>
                    Cuenta iniciada con éxito               
                    </div>";


                        //Declaramos Sesiones
                        $_SESSION["usuario"] = $usuario_temp;
                        $_SESSION["rol"] = $recuperarRol;
                        $_SESSION["saldo"] = $saldoBalance;
                        header('location: http://localhost/views/main');


                    } else {
                        $alert_contra =
                            "<div class='alert alert-danger mt-2' role='alert'>
                            El usuario o contraseña no es válido            
                    </div>";
                    }
                }
            }
        }
    }

    ?>

    <div class="nk-app-root">
        <div class="nk-main ">
            <div class="nk-wrap nk-wrap-nosidebar">
                <div class="nk-content ">
                    <div class="nk-split nk-split-page nk-split-md">
                        <div class="nk-split-content nk-block-area nk-block-area-column nk-auth-container bg-white">
                            <div class="nk-block nk-block-middle nk-auth-body">
                                <div class="brand-logo pb-5">
                                    <a href="http://localhost" class="logo-link">
                                        <img class="logo-light logo-img" src="images/logo.png">
                                    </a>
                                </div>
                                <div class="col-sm-12 tabs">
                                    <ul class="nav nav-tabs">
                                        <li class><a data-toggle="tab" aria-expanded="true" class="active"><em
                                                    class="icon ni ni-email"></em>Introduce tus credenciales</a></li>
                                    </ul>
                                    <br>
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="tab-email">

                                            <form action="" method="POST">
                                                <div class="form-group">
                                                    <div class="form-label-group">
                                                        <label class="form-label">Usuario</label>
                                                    </div>
                                                    <input class="form-control form-control-lg" type="text"
                                                        name="usuario">
                                                    <?php if (isset($alert_usuario))
                                                        echo $alert_usuario ?>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="form-label-group">
                                                            <label class="form-label">Contraseña</label>
                                                        </div>
                                                        <input class="form-control form-control-lg" type="password"
                                                            name="contra">
                                                    <?php if (isset($alert_contra))
                                                        echo $alert_contra ?>
                                                    </div>
                                                    <input class="btn btn-lg btn-primary btn-block" type="submit"
                                                        value="Ingresar a tu cuenta">
                                                <?php if (isset($alert_exito))
                                                        echo $alert_exito; ?>
                                                <?php if (isset($alert_error))
                                                    echo $alert_error ?>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-note-s2 pt-4">
                                        ¿Eres nuevo? <a href="http://localhost/views/registro">Registra tu cuenta</a>
                                    </div>
                                </div>
                                <div class="nk-block nk-auth-footer">
                                    <div class="mt-3">
                                        <div class="footer__copyright-info">
                                            <div class="footer__copyright-info--text nk-block-des">
                                                &copy; 2023 TTO System. All Rights Reserved.
                                            </div>
                                            <div class="footer__copyright-info--policy nk-block-des"
                                                style="display:flex; flex-direction: row;">
                                                <a href="http://localhost">RULES & TERMS</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="nk-split-content nk-split-stretch man-auth d-none d-md-block">
                                <video autoplay muted loop playsinline class="video-header__player">
                                    <source src="images/video/video_bg.mp4" type="video/mp4">
                                </video>

                                <div class="nk-footer-links-auth-logo">
                                    <ul class="nav nav-sm">
                                        <li class="nav-item logo-link">
                                            <!--<a class="nav-link" href="http://localhost" title="Trabajo de Clase para 2 DAW" target="_blank" rel="dofollow"><img src="images/logo_aside.png" /></a>-->
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </body>

    </html>
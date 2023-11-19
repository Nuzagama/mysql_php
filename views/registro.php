<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro | Tantra Game</title>
    <link rel="shortcut icon" href="images/favicon.png">
    <link rel="stylesheet" href="styles/all.min.css">
    <link rel="stylesheet" href="styles/bootstrap.min.css">
    <link rel="stylesheet" href="styles/2b234ocs.css">
    <?php require('../util/conection.php'); ?>
    <?php require('../util/functions.php'); ?>
</head>

<body class="nk-body bg-white npc-default has-aside dark-mode">

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $usuario_temp = depurar($_POST["usuario"]);
        $contra_temp = depurar($_POST["contra"]);
        $fecha_nacimiento_temp = depurar($_POST["fecha_nacimiento"]);

        $fecha_actual = new DateTime(date('Y-m-d'));
        $fecha_usuario = new DateTime($fecha_nacimiento_temp);
        $edad = $fecha_actual->diff($fecha_usuario)->y;



        if (!strlen($usuario_temp) > 0) {
            $alert_usuario = "<div class='alert alert-danger mt-2'>
        <em class='icon ni ni-alert-circle'></em>
        <strong>
        No puedes introducir un campo de usuario vacío
        </strong>
        </div>";

        } elseif (!strlen($contra_temp) > 0) {
            $alert_contra =
                "<div class='alert alert-danger mt-2'>
                <em class='icon ni ni-alert-circle'></em>
                <strong>
                No puedes introducir un campo de contraseña vacío
                </strong>
                </div>";
        } elseif (!strlen($fecha_nacimiento_temp) > 0) {
            $alert_fecha =
                "<div class='alert alert-danger mt-2'>
        <em class='icon ni ni-alert-circle'></em>
        <strong>
        No puedes introducir un campo fecha vacío                
        </strong>
        </div>";
        } else {
            if (!preg_match("/^[a-zA-Z_]{4,12}$/", $usuario_temp)) {
                $alert_usuario =
                    "<div class='alert alert-danger mt-2'>
        <em class='icon ni ni-alert-circle'></em>
        <strong>
        El nombre de usuario debe tener entre 4 y 12 caracteres y solo contener letras y barra baja _           
        </strong>
        </div>";

            } elseif (strlen($contra_temp) > 255) {
                $alert_contra =
                    "<div class='alert alert-danger mt-2'>
            <em class='icon ni ni-alert-circle'></em>
            <strong>
            La contraseña debe tener un máximo de 255 caracteres                      
            </strong>
            </div>";
            } elseif (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d]).{8,20}$/", $contra_temp)) {
                $alert_contra =
                    "<div class='alert alert-danger mt-2'>
            <em class='icon ni ni-alert-circle'></em>
            <strong>
            Las contraseña debe tener mínimo un carácter en minúscula, 
            uno en mayúscula, un número y un carácter especial. 
            Además, tendrán una longitud de entre 8 y 20 caracteres.             
            </strong>
            </div>";
            } elseif ($edad < 12 || $edad > 120) {
                $alert_fecha =
                    "<div class='alert alert-danger mt-2'>
            <em class='icon ni ni-alert-circle'></em>
            <strong>
            Debes tener una edad entre 12 y 120 años para registrarte            
            </strong>
            </div>";

            } else {

                $sqlComprobarUsuario = "SELECT usuario FROM usuarios WHERE usuario = '$usuario_temp'";
                $resultadoComprobacion = $conexion->query($sqlComprobarUsuario);

                if ($resultadoComprobacion->num_rows > 0) {
                    // El usuario ya existe, mostrar un mensaje de error
                    $alert_usuario = "<div class='alert alert-danger mt-2'>
                        <em class='icon ni ni-alert-circle'></em>
                        <strong>Este nombre de usuario ya está en uso. Por favor, elige otro.</strong>
                    </div>";

                } else {

                    $usuario = $usuario_temp;
                    $contra = $contra_temp;


                    $contra_cifrada = password_hash($contra, PASSWORD_DEFAULT);
                    $sql = "INSERT INTO usuarios (usuario, contrasena, fechaNacimiento)
            VALUES ('$usuario', '$contra_cifrada', '$fecha_nacimiento_temp')";
                    $conexion->query($sql);

                    $alert_exito =
                        "<div class='alert alert-success mt-2'>
            <em class='icon ni ni-alert-circle'></em>
            <strong>
            Tu cuenta se ha creado con éxito               
            </strong>
            </div>";


                    $sqlCesta = "INSERT INTO cestas(usuario) VALUES ('$usuario')";
                    $conexion->query($sqlCesta);
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
                                                    class="icon ni ni-email"></em>Nuevos registros</a></li>
                                    </ul>
                                    <br>
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="tab-email">
                                            <!-- FORM REGISTER -->
                                            <form action="" method="POST">
                                                <div class="form-group">
                                                    <div class="form-label-group">
                                                        <label class="form-label">Usuario</label>
                                                    </div>
                                                    <input type="text" class="form-control form-control-lg" type="text"
                                                        name="usuario">
                                                    <?php if (isset($alert_usuario))
                                                        echo $alert_usuario ?>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="form-label-group">
                                                            <label class="form-label">Contraseña</label>
                                                        </div>
                                                        <input type="password" class="form-control form-control-lg"
                                                            name="contra">
                                                    <?php if (isset($alert_contra))
                                                        echo $alert_contra ?>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="form-label-group">
                                                            <label class="form-label">Fecha de Nacimiento</label>
                                                        </div>
                                                        <input class="form-control form-control-lg" type="date"
                                                            name="fecha_nacimiento">
                                                    <?php if (isset($alert_fecha))
                                                        echo $alert_fecha ?>
                                                    </div>
                                                    <input class="btn btn-lg btn-primary btn-block" type="submit"
                                                        value="Registrar cuenta">
                                                <?php if (isset($alert_exito))
                                                        echo $alert_exito; ?>
                                            </form>


                                        </div>
                                    </div>
                                </div>
                                <!-- ENDS FORM REGISTER -->


                                <div class="form-note-s2 pt-4">
                                    ¿Ya tienes cuenta? <a href="http://localhost/views/login">Inicia sesión ahora</a>
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
    </div>




</body>

</html>
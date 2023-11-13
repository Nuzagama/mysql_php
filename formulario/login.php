<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse</title>
    <link rel="stylesheet" href="../css/all.min.css">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <?php require('../conf/conection.php'); ?>
</head>
<body class="bg-light">
    <?php

function depurar($entrada) {
    $salida = htmlspecialchars($entrada);
    $salida = trim($salida);
    return $salida;
}


if($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario_temp = depurar($_POST["usuario"]);
    $contra_temp = depurar($_POST["contra"]);

    if(!strlen($usuario_temp) > 0){
        $alert_usuario = "<div class='alert alert-danger' role='alert'>
        No puedes introducir un campo de usuario vacío
        </div>";
    }elseif(!strlen($contra_temp) > 0){
        $alert_contra = "<div class='alert alert-danger' role='alert'>
        No puedes introducir un campo de contraseña vacío
                </div>";
    }else{
        if(!preg_match("/^[a-zA-Z_]{4,12}$/", $usuario_temp)) {
            $alert_usuario = 
            "<div class='alert alert-danger' role='alert'>
            El nombre de usuario debe tener entre 4 y 12 caracteres y solo contener letras y/o barra baja _           
            </div>";
        } elseif(strlen($contra_temp) > 255) {
            $alert_contra = 
            "<div class='alert alert-danger' role='alert'>
            La contraseña debe tener un máximo de 255 caracteres
                        </div>";
        } else {

            $sql = "SELECT * FROM usuarios WHERE usuario = '$usuario_temp'";
            $resultado = $conexion -> query($sql);

            
    
            if($resultado -> num_rows === 0) { 
                $alert_usuario = 
                "<div class='alert alert-danger' role='alert'>
                El usuario no existe            
                </div>";
            } else {

                while($fila = $resultado -> fetch_assoc()) {
                    $contrasena_cifrada = $fila["contrasena"];
                }
        
                $acceso_valido = password_verify($contra_temp, $contrasena_cifrada);
        
                if($acceso_valido) {
                    $alert_exito = "<div class='alert alert-success' role='alert'>
                    Cuenta creada con éxito               
                    </div>";

                    $sql = "SELECT rol FROM usuarios WHERE usuario = '$usuario_temp'";
                    $recuperarRol = $conexion -> query($sql);

                    session_start();
                    $_SESSION["usuario"] = $usuario_temp;
                    $_SESSION["rol"] = $recuperarRol;
                    header('location: ../main.php');
                } else {
                    $alert_contra = 
                    "<div class='alert alert-danger' role='alert'>
                    La contraseña es incorrecta          
                    </div>";
                }
            }
        }
    }
}

    ?>
    <div class="container">
        <h1>Login</h1>
        <form action="" method="POST">
            <div class="mb-3">
                <label class="form-label">Usuario</label>
                <input class="form-control" type="text" name="usuario">
                <?php if(isset($alert_usuario)) echo $alert_usuario ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input class="form-control" type="password" name="contra">
                <?php if(isset($alert_contra)) echo $alert_contra ?>
            </div>
            <input class="btn btn-primary" type="submit" value="Registrarse">
            <?php if(isset($alert_exito)) echo $alert_exito; ?>
        </form>    
    </div>
</body>
</html>
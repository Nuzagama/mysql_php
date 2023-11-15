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
    $fecha_nacimiento_temp = depurar($_POST["fecha_nacimiento"]);

    $fecha_actual = new DateTime(date('Y-m-d'));
    $fecha_usuario = new DateTime($fecha_nacimiento_temp);
    $edad = $fecha_actual->diff($fecha_usuario)->y;



    if(!strlen($usuario_temp) > 0){
        $alert_usuario = "<div class='alert alert-danger mt-2' role='alert'>
        No puedes introducir un campo de usuario vacío
        </div>";
    }elseif(!strlen($contra_temp) > 0){
        $alert_contra = "<div class='alert alert-danger mt-2' role='alert'>
        No puedes introducir un campo de contraseña vacío
                </div>";
    }elseif(!strlen($fecha_nacimiento_temp) > 0){
        $alert_fecha = "<div class='alert alert-danger mt-2' role='alert'>
        No puedes introducir un campo fecha vacío                
        </div>";
    }else{
        if(!preg_match("/^[a-zA-Z_]{4,12}$/", $usuario_temp)) {
            $alert_usuario = 
            "<div class='alert alert-danger mt-2' role='alert'>
        El nombre de usuario debe tener entre 4 y 12 caracteres y solo contener letras y barra baja _              
        </div>";
        } elseif(strlen($contra_temp) > 255) {
            $alert_contra = 
            "<div class='alert alert-danger mt-2' role='alert'>
            La contraseña debe tener un máximo de 255 caracteres            
            </div>";
        } elseif(!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d]).{8,20}$/", $contra_temp)){
            $alert_contra = 
            "<div class='alert alert-danger mt-2' role='alert'>
            Las contraseña debe tener mínimo un carácter en minúscula, 
            uno en mayúscula, un número y un carácter especial. 
            Además, tendrán una longitud de entre 8 y 20 caracteres.          
            </div>";
        } elseif($edad < 12 || $edad > 120) {
            $alert_fecha = 
            "<div class='alert alert-danger mt-2' role='alert'>
            Debes tener una edad entre 12 y 120 años para registrarte            
            </div>";
        } else {

            $usuario = $usuario_temp;
            $contra = $contra_temp;
            
            
            $contra_cifrada = password_hash($contra, PASSWORD_DEFAULT);
            $sql = "INSERT INTO usuarios (usuario, contrasena, fechaNacimiento)
            VALUES ('$usuario', '$contra_cifrada', '$fecha_nacimiento_temp')"; 
            $conexion->query($sql);
            $alert_exito = "<div class='alert alert-success mt-2' role='alert'>
            Cuenta creada con éxito               
            </div>";

            $sqlCesta = "INSERT INTO cestas(usuario) VALUES ('$usuario')";
            $conexion->query($sqlCesta);
        }
    }
}

    ?>
    <div class="container">
        <h1 class="mt-2">Registrarse</h1>
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
            <div class="mb-3">
                <label class="form-label">Fecha de Nacimiento</label>
                <input class="form-control" type="date" name="fecha_nacimiento">
                <?php if(isset($alert_fecha)) echo $alert_fecha ?>
            </div>
            <input class="btn btn-primary" type="submit" value="Registrarse">
            <?php if(isset($alert_exito)) echo $alert_exito; ?>
        </form>    
    </div>
</body>
</html>
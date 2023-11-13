<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse</title>
    <link rel="stylesheet" href="css/all.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <?php require('conf/conection.php'); ?>
</head>
<body class="bg-light">
    <?php
    session_start();
    if(isset($_SESSION['usuario'])){
        $usuario = $_SESSION["usuario"];
    }else{
        $usuario = "Invitado";
    }

    function depurar($entrada) {
    $salida = htmlspecialchars($entrada);
    $salida = trim($salida);
    return $salida;
    }


    ?>
    <div class="container">
        <?php if(isset($_SESSION['alert_error'])) echo $_SESSION['alert_error']; ?>
        <h1>Bienvenido a tu panel de control <?php echo $usuario;?></h1>
        <a href="formulario/sesiones.php">Cerrar Sesión</a>

        <h1 class="mt-5">Prueba Productos:</h1>




</div>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse</title>
    <link rel="stylesheet" href="../css/all.min.css">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <?php require('../conf/conection.php'); ?>
    <?php require('producto.php'); ?>
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
        <a href="sesiones.php">Cerrar Sesión</a>

        <h1 class="mt-5">Prueba Productos:</h1>
        <?php 
        
        $sql = "SELECT * FROM productos";
        $resultado = $conexion -> query($sql);
        $objetos = [];
        while($fila = $resultado -> fetch_assoc()) {
            $objeto = new Producto($fila["idProducto"], $fila["nombreProducto"],$fila["precio"],$fila["descripcion"],
            $fila["cantidad"],$fila["imagen"]);

            array_push($objetos, $objeto);
        }
        ?>

    <table>
        <thead>
            <tr>
                <th>Nombre Producto:</th>
                <th>Precio:</th>
                <th>Descripción:</th>
                <th>Cantidad:</th>
                <th>Imagen:</th>
                <th>Button</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            
            foreach($objetos as $objeto){
                echo "<tr>";
                echo "<td>" . $objeto -> nombreProducto . "</td>";
                echo "<td>" . $objeto -> precio . "</td>";
                echo "<td>" . $objeto -> descripcion . "</td>";
                echo "<td>" . $objeto -> cantidad . "</td>";?>
                <td><img src=" <?php echo $objeto -> imagen ?> " height="100px" width="100px"></td>
                <td><form action = "" method = "POST"> 
                    <input type="hidden" name="id_producto" value=" <?php echo $objeto -> idProducto ?> ">
                    <input class="btn btn-warning" type="submit" value="Comprar">
                </form></td>
                <?php
                echo "</tr>";
            }
            
            
            
            
            
            ?>
        </tbody>
    </table>




</div>
</body>
</html>
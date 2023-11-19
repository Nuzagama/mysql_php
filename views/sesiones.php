<?php
    //Recuperamos la sesión
    session_start();
    //Destruimos la sesión
    session_destroy();

    header('location: main.php');



?>
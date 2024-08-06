<?php

    require_once '../../config/app.php';
    require_once '../views/inc/session_star.php';
    require_once '../../autoload.php';

    use app\controllers\searchController;

    if(isset($_POST['modulo_buscador'])){

        $instBuscador = new searchController();

        if($_POST['modulo_buscador'] == "buscar"){
            echo $instBuscador->iniciarBuscadorControlador();
        }
        if($_POST['modulo_buscador'] == "eliminar"){
            echo $instBuscador->eliminarBuscadorControlador();
        }
        
    }else{
        session_destroy();
        header('Location: '.APP_URL.'login/');
    }
<?php

    require_once '../../config/app.php';
    require_once '../views/inc/session_star.php';
    require_once '../../autoload.php';

    use app\controllers\userController;

    if(isset($_POST['modulo_usuario'])){

        $instUsuario = new userController();

        if($_POST['modulo_usuario'] == "registrar"){
            echo $instUsuario->registrarUsuarioControlador();
        }
        if($_POST['modulo_usuario'] == "eliminar"){
            echo $instUsuario->eliminarUsuarioControlador();
        }
        if($_POST['modulo_usuario'] == "actualizar"){
            echo $instUsuario->actualizarUsuarioControlador();
        }

    }else{
        session_destroy();
        header('Location: '.APP_URL.'login/');
    }
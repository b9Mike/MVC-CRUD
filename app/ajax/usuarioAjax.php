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
        if($_POST['modulo_usuario'] == "actualizarFoto"){
            echo $instUsuario->actualizarFotoUsuarioControlador();
        }
        if($_POST['modulo_usuario'] == "eliminarFoto"){
            echo $instUsuario->eliminarFotoUsuarioControlador();
        }
        
    }else{
        session_destroy();
        header('Location: '.APP_URL.'login/');
    }
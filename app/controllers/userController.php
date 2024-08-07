<?php

    namespace app\controllers;

    use app\models\mainModel;

    class userController extends mainModel{

        #metodo para registrar un usuario
        public function registrarUsuarioControlador(){

            #almacenar datos en variables
            $nombre = $this->limpiarCadena($_POST['usuario_nombre']);
            $apellido = $this->limpiarCadena($_POST['usuario_apellido']);

            $usuario = $this->limpiarCadena($_POST['usuario_usuario']);
            $email = $this->limpiarCadena($_POST['usuario_email']);
            $clave1 = $this->limpiarCadena($_POST['usuario_clave_1']);
            $clave2 = $this->limpiarCadena($_POST['usuario_clave_2']);

            #verificar que no esten vacios
            if($nombre == "" || $apellido == "" || $usuario == "" || $clave1 == "" || $clave2 == ""){
                
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "No has llenado todos los campos que son obligatorios",
                    "icono" => "error"
                ];

                return json_encode($alerta);
                exit();

            }

            #Verificar integridad de datos
            if($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}", $nombre)){
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "El nombre no coincide con el formato solicitado",
                    "icono" => "error"
                ];

                return json_encode($alerta);
                exit();
            }

            if($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}", $apellido)){
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "El apellido no coincide con el formato solicitado",
                    "icono" => "error"
                ];

                return json_encode($alerta);
                exit();
            }

            if($this->verificarDatos("[a-zA-Z0-9]{4,20}", $usuario)){
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "El usuario no coincide con el formato solicitado",
                    "icono" => "error"
                ];

                return json_encode($alerta);
                exit();
            }

            if($this->verificarDatos("[a-zA-Z0-9$@.-]{7,100}", $clave1) || $this->verificarDatos("[a-zA-Z0-9$@.-]{7,100}", $clave2)){
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "Las claves no coincide con el formato solicitado",
                    "icono" => "error"
                ];

                return json_encode($alerta);
                exit();
            }

            #verificar email
            if($email != ""){
                if(filter_var($email, FILTER_VALIDATE_EMAIL)){

                    $checkEmail = $this->ejecutarConsulta("SELECT user_email FROM users WHERE user_email = '$email'");

                    if($checkEmail->rowCount() > 0){
                        $alerta = [
                            "tipo" => "simple",
                            "titulo" => "Ocurrió un error inesperado",
                            "texto" => "El correo ya existe en el sistema",
                            "icono" => "error"
                        ];
        
                        return json_encode($alerta);
                        exit();
                    }

                }else{

                    $alerta = [
                        "tipo" => "simple",
                        "titulo" => "Ocurrió un error inesperado",
                        "texto" => "El correo no coincide con el formato solicitado",
                        "icono" => "error"
                    ];
    
                    return json_encode($alerta);
                    exit();
                }
            }

            #verificando claves
            if($clave1 != $clave2){
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrio un error inesperado",
                    "texto" => "Las claves que ingreso no coinciden",
                    "icono" => "error"
                ];

                return json_encode($alerta);
                exit();
            }else{
                $clave = password_hash($clave1, PASSWORD_BCRYPT, ['cost' => 10]);
            }

            #verificar usuario
            $checkUser = $this->ejecutarConsulta("SELECT user_user FROM users WHERE user_user = '$usuario'");

            if($checkUser->rowCount() > 0){
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "El USUARIO ya existe en el sistema",
                    "icono" => "error"
                ];

                return json_encode($alerta);
                exit();
            }

            #directorio de imagenes
            $imgDir = "../views/fotos/";


            #comprobar si se ha seleccionado una imagen
            if($_FILES['usuario_foto']['name'] != "" && $_FILES['usuario_foto']['size'] > 0){

                #crear directorio en caso de que no este seleccionado
                if(!file_exists($imgDir)){

                    if(!mkdir($imgDir, 0777)){
                        $alerta = [
                            "tipo" => "simple",
                            "titulo" => "Ocurrió un error inesperado",
                            "texto" => "Error al crear directorio de imagenes",
                            "icono" => "error"
                        ];
        
                        return json_encode($alerta);
                        exit();
                    }

                }

                #Verificar formato de la imagen
                if(mime_content_type($_FILES['usuario_foto']['tmp_name']) != "image/jpeg" && mime_content_type($_FILES['usuario_foto']['tmp_name']) != "image/png"){
                    $alerta = [
                        "tipo" => "simple",
                        "titulo" => "Ocurrió un error inesperado",
                        "texto" => "La imagen que se ha seleecionado no esta permitida",
                        "icono" => "error"
                    ];
    
                    return json_encode($alerta);
                    exit();
                }

                #verificar peso de la imagen
                if(($_FILES['usuario_foto']['size']/1024) > 5120){
                    $alerta = [
                        "tipo" => "simple",
                        "titulo" => "Ocurrió un error inesperado",
                        "texto" => "La imagen pesa mas de lo permitido",
                        "icono" => "error"
                    ];
    
                    return json_encode($alerta);
                    exit();
                }

                #nombre de la foto
                $foto = str_ireplace(" ", "_", $nombre);
                $foto = $foto."_".rand(0,100);

                #extension de la imagen
                switch(mime_content_type($_FILES['usuario_foto']['tmp_name'])){

                    case "image/jpeg": $foto = $foto.".jpg";
                        break;
                    case "image/png": $foto = $foto.".png";
                        break;
                }

                chmod($imgDir, 0777);

                #moviendo imagen al directorio
                if(!move_uploaded_file($_FILES['usuario_foto']['tmp_name'], $imgDir.$foto)){
                    $alerta = [
                        "tipo" => "simple",
                        "titulo" => "Ocurrió un error inesperado",
                        "texto" => "Error al subir la imagen",
                        "icono" => "error"
                    ];
    
                    return json_encode($alerta);
                    exit();
                }

            }else{
                $foto = "";
            }

            $usuarioDatosReg = [
                [
                    "campo_nombre" => "user_name",
                    "campo_marcador" => ":user_name",
                    "campo_valor" => $nombre
                ],
                [
                    "campo_nombre" => "user_lastname",
                    "campo_marcador" => ":user_lastname",
                    "campo_valor" => $apellido
                ],
                [
                    "campo_nombre" => "user_user",
                    "campo_marcador" => ":user_user",
                    "campo_valor" => $usuario
                ],
                [
                    "campo_nombre" => "user_email",
                    "campo_marcador" => ":user_email",
                    "campo_valor" => $email
                ],
                [
                    "campo_nombre" => "user_pass",
                    "campo_marcador" => ":user_pass",
                    "campo_valor" => $clave
                ],
                [
                    "campo_nombre" => "user_image",
                    "campo_marcador" => ":user_image",
                    "campo_valor" => $foto
                ],
                [
                    "campo_nombre" => "user_created",
                    "campo_marcador" => ":user_created",
                    "campo_valor" => date("Y-m-d H:i:s")
                ],
                [
                    "campo_nombre" => "user_update",
                    "campo_marcador" => ":user_update",
                    "campo_valor" => date("Y-m-d H:i:s")
                ]
            ];

            $registrarUsuario = $this->guardarDatos("users", $usuarioDatosReg);

            if($registrarUsuario->rowCount() == 1){
                $alerta = [
                    "tipo" => "limpiar",
                    "titulo" => "Usuario registrado con exito",
                    "texto" => "El usuario $nombre $apellido se registro con éxito",
                    "icono" => "success"
                ];

            }else{

                #eliminar imagen si se subio al servidor
                if(is_file($imgDir.$foto)){
                    chmod($imgDir.$foto, 0777);
                    unlink($imgDir.$foto);
                }

                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "No se pudo registrar el usuario, intente nuevamente",
                    "icono" => "error"
                ];

            }
            return json_encode($alerta);

        }

        #metodo para listar usuarios
        public function listarUsuarioControlador($pagina, $registros, $url, $busqueda){
            #recibiendo datos
            $pagina = $this->limpiarCadena($pagina);
            $registros = $this->limpiarCadena($registros);
            
            $url = $this->limpiarCadena($url);
            $url = APP_URL.$url."/";
            
            $busqueda = $this->limpiarCadena($busqueda);
            $tabla = "";

            $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : $pagina = 1;

            $inicio = ($pagina > 0) ? (($pagina*$registros)-$registros) : 0;

            if(isset($busqueda) && $busqueda != ""){
                
                $consultaDatos = "SELECT * FROM users WHERE ((user_id != '".$_SESSION['id']."' AND user_id != 1) AND (user_name LIKE '%$busqueda%' OR user_lastname LIKE '%$busqueda%' OR user_email LIKE '%$busqueda%' OR user_user LIKE '%$busqueda%')) ORDER BY user_name ASC LIMIT $inicio, $registros";

                $consultaTotal = "SELECT COUNT(user_id) FROM users WHERE ((user_id != '".$_SESSION['id']."' AND user_id != 1) AND (user_name LIKE '%$busqueda%' OR user_lastname LIKE '%$busqueda%' OR user_email LIKE '%$busqueda%' OR user_user LIKE '%$busqueda%'))";
            
            }else{

                $consultaDatos = "SELECT * FROM users WHERE user_id != '".$_SESSION['id']."' AND user_id != 1 ORDER BY user_name ASC LIMIT $inicio, $registros";

                $consultaTotal = "SELECT COUNT(user_id) FROM users WHERE user_id != '".$_SESSION['id']."' AND user_id != 1";

            }

            $datos = $this->ejecutarConsulta($consultaDatos);
            $datos = $datos->fetchAll();

            $total = $this->ejecutarConsulta($consultaTotal);
            $total = (int) $total->fetchColumn();

            $numeroPagina = ceil($total/$registros);

            $tabla .= '
            <div class="table-container">
                <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                    <thead>
                        <tr>
                            <th class="has-text-centered">#</th>
                            <th class="has-text-centered">Nombre</th>
                            <th class="has-text-centered">Usuario</th>
                            <th class="has-text-centered">Email</th>
                            <th class="has-text-centered">Creado</th>
                            <th class="has-text-centered">Actualizado</th>
                            <th class="has-text-centered" colspan="3">Opciones</th>
                        </tr>
                    </thead>
                    <tbody>
            ';

            if($total >= 1 && $pagina <= $numeroPagina){
                
                $contador = $inicio + 1;
                $pagInicio = $inicio + 1;

                foreach($datos as $dato){
                    $tabla .= '
                        <tr class="has-text-centered">
                            <td>'.$contador.'</td>
                            <td>'.$dato["user_name"]." ".$dato["user_lastname"].'</td>
                            <td>'.$dato["user_user"].'</td>
                            <td>'.$dato["user_email"].'</td>
                            <td>'.date("d-m-Y h:m:s A", strtotime($dato["user_created"])).'</td>
                            <td>'.date("d-m-Y h:m:s A", strtotime($dato["user_update"])).'</td>
                            <td>
                                <a href="'.APP_URL.'userPhoto/'.$dato["user_id"].'/" class="button is-info is-rounded is-small">Foto</a>
                            </td>
                            <td>
                                <a href="'.APP_URL.'userUpdate/'.$dato["user_id"].'/" class="button is-success is-rounded is-small">Actualizar</a>
                            </td>
                            <td>
                                <form class="FormularioAjax" action="'.APP_URL.'app/ajax/usuarioAjax.php" method="POST" autocomplete="off">

                                    <input type="hidden" name="modulo_usuario" value="eliminar">
                                    <input type="hidden" name="usuario_id" value="'.$dato["user_id"].'">

                                    <button type="submit" class="button is-danger is-rounded is-small">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    ';
                    $contador++;
                }

                $paggFinal = $contador - 1;

            }else{

                if($total >= 1){
                    $tabla .= '
                        <tr class="has-text-centered" >
                            <td colspan="7">
                                <a href="'.$url.'1/" class="button is-link is-rounded is-small mt-4 mb-4">
                                    Haga clic acá para recargar el listado
                                </a>
                            </td>
                        </tr>
                    ';
                }else{
                    $tabla .= '
                        <tr class="has-text-centered" >
                            <td colspan="7">
                                No hay registros en el sistema
                            </td>
                        </tr>
                    '; 
                }

            }

            $tabla .= '
                    </tbody>
                </table>
            </div>
            ';

            if($total >= 1 && $pagina <= $numeroPagina){

                $tabla .= '<p class="has-text-right">Mostrando usuarios <strong>'.$pagInicio.'</strong> al <strong>'.$paggFinal.'</strong> de un <strong>total de '.$total.'</strong></p>';

                $tabla .= $this->paginadorTablas($pagina, $numeroPagina, $url, 6);
                
            }

            return $tabla;

        }

        #metodo para eliminar usuario
        public function eliminarUsuarioControlador(){

            $id = $this->limpiarCadena($_POST['usuario_id']);

            #verificar que no sea el usuario administrador
            if($id == 1){
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "No podemos eliminar este usuario",
                    "icono" => "error"
                ];

                return json_encode($alerta);
                exit();
            }

            #verificar usuario
            $dato = $this->ejecutarConsulta("SELECT * FROM users WHERE user_id = $id");

            if($dato->rowCount() <= 0){
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "No se ha encontrado el usuario",
                    "icono" => "error"
                ];

                return json_encode($alerta);
                exit();
            }else{
                $dato = $dato->fetch();
            }

            $eliminarUsuario = $this->eliminarDatos("users", "user_id", $id);

            if($eliminarUsuario->rowCount() == 1){

                #eliminar imagen si se subio al servidor
                if(is_file("../views/fotos/".$dato['user_image'])){
                    chmod("../views/fotos/".$dato['user_image'], 0777);
                    unlink("../views/fotos/".$dato['user_image']);
                }

                $alerta = [
                    "tipo" => "recargar",
                    "titulo" => "Usuario eliminado",
                    "texto" => "El usuario ".$dato['user_name']." ".$dato['user_lastname']." se elimino con exito",
                    "icono" => "success"
                ];
            }else{
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "No se pudo eliminar el usuario",
                    "icono" => "error"
                ];
            }
            return json_encode($alerta);

        }

        #metodo para actualiar usuario
        public function actualizarUsuarioControlador(){

            $id = $this->limpiarCadena($_POST['usuario_id']);
          
            #verificar usuario admin
            $dato = $this->ejecutarConsulta("SELECT * FROM users WHERE user_id = $id");
            
            if($dato->rowCount() <= 0){
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "No se ha encontrado el usuario",
                    "icono" => "error"
                ];

                return json_encode($alerta);
                exit();
            }else{
                $dato = $dato->fetch();
            }

            

            $adminUsuario = $this->limpiarCadena($_POST['administrador_usuario']);
            $adminClave = $this->limpiarCadena($_POST['administrador_clave']);

            if($adminUsuario == "" || $adminClave == ""){
                
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "No has llenado todos los campos que son obligatorios",
                    "icono" => "error"
                ];

                return json_encode($alerta);
                exit();

            }


             #Verificar integridad de datos
            if($this->verificarDatos("[a-zA-Z0-9]{4,20}", $adminUsuario)){
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "El USUARIO no coincide con el formato solicitado",
                    "icono" => "error"
                ];

                return json_encode($alerta);
                exit();
            }

            if($this->verificarDatos("[a-zA-Z0-9$@.-]{7,100}", $adminClave)){
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "La CLAVE no coincide con el formato solicitado",
                    "icono" => "error"
                ];

                return json_encode($alerta);
                exit();
            }

            #verificar usuario
            $checkAdmin = $this->ejecutarConsulta("SELECT * FROM users WHERE user_user = '$adminUsuario' AND user_id = ".$_SESSION['id']);

            if($checkAdmin->rowCount() == 1){
               
                $checkAdmin = $checkAdmin->fetch();

                if($checkAdmin['user_user'] != $adminUsuario || !password_verify($adminClave, $checkAdmin['user_pass'])){
                    $alerta = [
                        "tipo" => "simple",
                        "titulo" => "Ocurrió un error inesperado",
                        "texto" => "USUARIO O CLAVE del Administrador no coinciden",
                        "icono" => "error"
                    ];
    
                    return json_encode($alerta);
                    exit();
                }

            }else{
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "USUARIO O CLAVE del Administrador incorectos",
                    "icono" => "error"
                ];

                return json_encode($alerta);
                exit();
            }

            #almacenar datos en variables
            $nombre = $this->limpiarCadena($_POST['usuario_nombre']);
            $apellido = $this->limpiarCadena($_POST['usuario_apellido']);

            $usuario = $this->limpiarCadena($_POST['usuario_usuario']);
            $email = $this->limpiarCadena($_POST['usuario_email']);
            $clave1 = $this->limpiarCadena($_POST['usuario_clave_1']);
            $clave2 = $this->limpiarCadena($_POST['usuario_clave_2']);

            #verificar que no esten vacios
            if($nombre == "" || $apellido == "" || $usuario == ""){
                
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "No has llenado todos los campos que son obligatorios",
                    "icono" => "error"
                ];

                return json_encode($alerta);
                exit();

            }

            #Verificar integridad de datos
            if($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}", $nombre)){
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "El nombre no coincide con el formato solicitado",
                    "icono" => "error"
                ];

                return json_encode($alerta);
                exit();
            }

            if($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}", $apellido)){
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "El apellido no coincide con el formato solicitado",
                    "icono" => "error"
                ];

                return json_encode($alerta);
                exit();
            }

            if($this->verificarDatos("[a-zA-Z0-9]{4,20}", $usuario)){
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "El usuario no coincide con el formato solicitado",
                    "icono" => "error"
                ];

                return json_encode($alerta);
                exit();
            }


            #verificar email
            if($email != "" && $dato['user_email'] != $email){
                if(filter_var($email, FILTER_VALIDATE_EMAIL)){

                    $checkEmail = $this->ejecutarConsulta("SELECT user_email FROM users WHERE user_email = '$email'");

                    if($checkEmail->rowCount() > 0){
                        $alerta = [
                            "tipo" => "simple",
                            "titulo" => "Ocurrió un error inesperado",
                            "texto" => "El correo ya existe en el sistema",
                            "icono" => "error"
                        ];
        
                        return json_encode($alerta);
                        exit();
                    }

                }else{

                    $alerta = [
                        "tipo" => "simple",
                        "titulo" => "Ocurrió un error inesperado",
                        "texto" => "El correo no coincide con el formato solicitado",
                        "icono" => "error"
                    ];
    
                    return json_encode($alerta);
                    exit();
                }
            }

            #verificando claves
            if($clave1 !=  "" || $clave2 != ""){

                if($this->verificarDatos("[a-zA-Z0-9$@.-]{7,100}", $clave1) || $this->verificarDatos("[a-zA-Z0-9$@.-]{7,100}", $clave2)){
                    $alerta = [
                        "tipo" => "simple",
                        "titulo" => "Ocurrió un error inesperado",
                        "texto" => "Las claves no coincide con el formato solicitado",
                        "icono" => "error"
                    ];
    
                    return json_encode($alerta);
                    exit();
                }else{
                    
                    if($clave1 != $clave2){

                        $alerta = [
                            "tipo" => "simple",
                            "titulo" => "Ocurrio un error inesperado",
                            "texto" => "Las claves que ingreso no coinciden",
                            "icono" => "error"
                        ];
    
                        return json_encode($alerta);
                        exit();

                    }else{
                        $clave = password_hash($clave1, PASSWORD_BCRYPT, ['cost' => 10]);
                    }
                }

            }else{
                $clave = $dato['user_pass'];
            }

            #verificar usuario
            if($dato['user_user'] != $usuario){

                $checkUser = $this->ejecutarConsulta("SELECT user_user FROM users WHERE user_user = '$usuario'");
    
                if($checkUser->rowCount() > 0){
                    $alerta = [
                        "tipo" => "simple",
                        "titulo" => "Ocurrió un error inesperado",
                        "texto" => "El USUARIO ya existe en el sistema",
                        "icono" => "error"
                    ];
    
                    return json_encode($alerta);
                    exit();
                }

            }

            $usuarioDatosUp = [
                [
                    "campo_nombre" => "user_name",
                    "campo_marcador" => ":user_name",
                    "campo_valor" => $nombre
                ],
                [
                    "campo_nombre" => "user_lastname",
                    "campo_marcador" => ":user_lastname",
                    "campo_valor" => $apellido
                ],
                [
                    "campo_nombre" => "user_user",
                    "campo_marcador" => ":user_user",
                    "campo_valor" => $usuario
                ],
                [
                    "campo_nombre" => "user_email",
                    "campo_marcador" => ":user_email",
                    "campo_valor" => $email
                ],
                [
                    "campo_nombre" => "user_pass",
                    "campo_marcador" => ":user_pass",
                    "campo_valor" => $clave
                ],
                [
                    "campo_nombre" => "user_update",
                    "campo_marcador" => ":user_update",
                    "campo_valor" => date("Y-m-d H:i:s")
                ]
            ];

            $condicion = [
                "condicion_campo" => "user_id",
                "condicion_marcador" => ":user_id",
                "condicion_valor" => $id
            ];

            $actualizarUsuario = $this->actualizarDatos("users", $usuarioDatosUp, $condicion);

            if($actualizarUsuario->rowCount() == 1){

                if($id == $_SESSION['id']){
                    $_SESSION['nombre'] = $nombre;
                    $_SESSION['apellido'] = $apellido;
                    $_SESSION['usuario'] = $usuario;
                }

                $alerta = [
                    "tipo" => "recargar",
                    "titulo" => "Usuario actualizado con exito",
                    "texto" => "El usuario $nombre $apellido se actualizado con éxito",
                    "icono" => "success"
                ];

            }else{

                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "No se pudo actualizar el usuario, intente nuevamente",
                    "icono" => "error"
                ];

            }
            return json_encode($alerta);

        }

        public function actualizarFotoUsuarioControlador(){

            $id = $this->limpiarCadena($_POST['usuario_id']);
          
            #verificar usuario admin
            $dato = $this->ejecutarConsulta("SELECT * FROM users WHERE user_id = $id");
            
            if($dato->rowCount() <= 0){
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "No se ha encontrado el usuario",
                    "icono" => "error"
                ];

                return json_encode($alerta);
                exit();
            }else{
                $dato = $dato->fetch();
            }

             #directorio de imagenes
             $imgDir = "../views/fotos/";

             #comprobar si se ha seleccionado una imagen
             if($_FILES['usuario_foto']['name'] == "" && $_FILES['usuario_foto']['size'] <= 0){
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "No has seleccionado una foto para el usuario",
                    "icono" => "error"
                ];

                return json_encode($alerta);
                exit();
             }

              #crear directorio en caso de que no este seleccionado
              if(!file_exists($imgDir)){

                if(!mkdir($imgDir, 0777)){
                    $alerta = [
                        "tipo" => "simple",
                        "titulo" => "Ocurrió un error inesperado",
                        "texto" => "Error al crear directorio de imagenes",
                        "icono" => "error"
                    ];
    
                    return json_encode($alerta);
                    exit();
                }

            }

            #Verificar formato de la imagen
            if(mime_content_type($_FILES['usuario_foto']['tmp_name']) != "image/jpeg" && mime_content_type($_FILES['usuario_foto']['tmp_name']) != "image/png"){
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "La imagen que se ha seleecionado no esta permitida",
                    "icono" => "error"
                ];

                return json_encode($alerta);
                exit();
            }

            #verificar peso de la imagen
            if(($_FILES['usuario_foto']['size']/1024) > 5120){
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "La imagen pesa mas de lo permitido",
                    "icono" => "error"
                ];

                return json_encode($alerta);
                exit();
            }

            #verificar si tiene una foto
            if($dato['user_image'] != ""){
                $foto = explode(".", $dato['user_image']);
                $foto = $foto[0];
            }else{
                #nombre de la foto
                $foto = str_ireplace(" ", "_", $dato['user_name']);
                $foto = $foto."_".rand(0,100);

            }


            #extension de la imagen
            switch(mime_content_type($_FILES['usuario_foto']['tmp_name'])){

                case "image/jpeg": $foto = $foto.".jpg";
                    break;
                case "image/png": $foto = $foto.".png";
                    break;
            }

            chmod($imgDir, 0777);

            #moviendo imagen al directorio
            if(!move_uploaded_file($_FILES['usuario_foto']['tmp_name'], $imgDir.$foto)){
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "Error al subir la imagen",
                    "icono" => "error"
                ];

                return json_encode($alerta);
                exit();
            }

            #eliminando foto anterior
            if(is_file($imgDir.$dato['user_image']) && $dato['user_image'] != $foto){
                chmod($imgDir.$dato['user_image'], 0777);
                unlink($imgDir.$dato['user_image']);
            }

            $usuarioDatosUp = [
                [
                    "campo_nombre" => "user_image",
                    "campo_marcador" => ":user_image",
                    "campo_valor" => $foto
                ],
                [
                    "campo_nombre" => "user_update",
                    "campo_marcador" => ":user_update",
                    "campo_valor" => date("Y-m-d H:i:s")
                ]
            ];

            $condicion = [
                "condicion_campo" => "user_id",
                "condicion_marcador" => ":user_id",
                "condicion_valor" => $id
            ];

            $actualizarUsuario = $this->actualizarDatos("users", $usuarioDatosUp, $condicion);

            if($actualizarUsuario->rowCount() == 1){

                if($id == $_SESSION['id']){
                    $_SESSION['foto'] = $foto;
                }

                $alerta = [
                    "tipo" => "recargar",
                    "titulo" => "Usuario actualizado con exito",
                    "texto" => "Se ha actualizado la imagen del usuario",
                    "icono" => "success"
                ];

            }else{

                $alerta = [
                    "tipo" => "recargar",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "No se pudo actualizar la foto del usuario, intente nuevamente",
                    "icono" => "error"
                ];

            }
            return json_encode($alerta);

        }

        public function eliminarFotoUsuarioControlador(){
            $id = $this->limpiarCadena($_POST['usuario_id']);
          
            #verificar usuario admin
            $dato = $this->ejecutarConsulta("SELECT * FROM users WHERE user_id = $id");
            
            if($dato->rowCount() <= 0){
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "No se ha encontrado el usuario",
                    "icono" => "error"
                ];

                return json_encode($alerta);
                exit();
            }else{
                $dato = $dato->fetch();
            }

            if($dato['user_image'] == ""){
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "El usuario no tiene una imagen para eliminar",
                    "icono" => "error"
                ];

                return json_encode($alerta);
                exit();
            }

            #directorio de imagenes
            $imgDir = "../views/fotos/";

            chmod($imgDir, 0777);

            if(is_file($imgDir.$dato['user_image'])){

                chmod($imgDir.$dato['user_image'], 0777);
                if(!unlink($imgDir.$dato['user_image'])){
                    $alerta = [
                        "tipo" => "simple",
                        "titulo" => "Ocurrió un error inesperado",
                        "texto" => "No se pudo eliminar la imagen",
                        "icono" => "error"
                    ];
    
                    return json_encode($alerta);
                    exit();
                }

            }else{
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "El usuario no tiene una imagen para eliminar",
                    "icono" => "error"
                ];

                return json_encode($alerta);
                exit();
            }

            $usuarioDatosUp = [
                [
                    "campo_nombre" => "user_image",
                    "campo_marcador" => ":user_image",
                    "campo_valor" => ""
                ],
                [
                    "campo_nombre" => "user_update",
                    "campo_marcador" => ":user_update",
                    "campo_valor" => date("Y-m-d H:i:s")
                ]
            ];

            $condicion = [
                "condicion_campo" => "user_id",
                "condicion_marcador" => ":user_id",
                "condicion_valor" => $id
            ];

            $actualizarUsuario = $this->actualizarDatos("users", $usuarioDatosUp, $condicion);

            if($actualizarUsuario->rowCount() == 1){

                if($id == $_SESSION['id']){
                    $_SESSION['foto'] = "";
                }

                $alerta = [
                    "tipo" => "recargar",
                    "titulo" => "Usuario actualizado con exito",
                    "texto" => "Se ha eliminado la imagen del usuario",
                    "icono" => "success"
                ];

            }else{

                $alerta = [
                    "tipo" => "recargar",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "No se pudo eliminar la foto del usuario, intente nuevamente",
                    "icono" => "error"
                ];

            }
            return json_encode($alerta);

        }
        

    }
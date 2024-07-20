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

    }
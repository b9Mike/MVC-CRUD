<?php

    namespace app\controllers;

    use app\models\mainModel;

    class loginController extends mainModel{

        #metodo para iniciar sesion 
        public function iniciarSesionControlador(){

            #almacenar datos
            $usuario = $this->limpiarCadena($_POST['login_usuario']);
            $clave = $this->limpiarCadena($_POST['login_clave']);

            #verificar que no esten vacios 
            if($usuario == "" || $clave == ""){
                echo '
                    <script>
                        Swal.fire({
                            icon: "error",
                            title: "Ocurrió un error inesperado",
                            text: "Por favor llene todos los datos",
                            confirmButtonText: "Aceptar"
                        });
                    </script>
                ';
            }else{

                #verificando integridad de los datos
                if($this->verificarDatos("[a-zA-Z0-9]{4,20}", $usuario)){
                    echo '
                        <script>
                            Swal.fire({
                                icon: "error",
                                title: "Ocurrió un error inesperado",
                                text: "El USUARIO no coincide con el formato solicitado",
                                confirmButtonText: "Aceptar"
                            });
                        </script>
                    ';
                }else{
                    if($this->verificarDatos("[a-zA-Z0-9$@.-]{7,100}", $clave)){
                        echo '
                            <script>
                                Swal.fire({
                                    icon: "error",
                                    title: "Ocurrió un error inesperado",
                                    text: "La CONTRASEÑA no coincide con el formato solicitado",
                                    confirmButtonText: "Aceptar"
                                });
                            </script>
                        ';
                    }else{

                        #verificar usuario
                        $checkUser = $this->ejecutarConsulta("SELECT * FROM users WHERE user_user = '$usuario'");

                        if($checkUser->rowCount() == 1){

                            $checkUser = $checkUser->fetch();

                            if($checkUser['user_user'] == $usuario && password_verify($clave, $checkUser['user_pass'])){
                                
                                $_SESSION['id'] = $checkUser['user_id'];
                                $_SESSION['nombre'] = $checkUser['user_name'];
                                $_SESSION['apellido'] = $checkUser['user_lastname'];
                                $_SESSION['usuario'] = $checkUser['user_user'];
                                $_SESSION['foto'] = $checkUser['user_image'];

                                if(headers_sent()){
                                    echo "
                                        <script>
                                            window.location.href = '".APP_URL."dashboard/';
                                        </script>
                                    ";
                                }else{
                                    header("Location: ".APP_URL."dashboard/");
                                }

                            }else{
                                echo '
                                    <script>
                                        Swal.fire({
                                            icon: "error",
                                            title: "Ocurrió un error inesperado",
                                            text: "Usuario o clave incorrectos",
                                            confirmButtonText: "Aceptar"
                                        });
                                    </script>
                                ';
                            }

                        }else{
                            echo '
                                <script>
                                    Swal.fire({
                                        icon: "error",
                                        title: "Ocurrió un error inesperado",
                                        text: "Usuario o clave incorrectos",
                                        confirmButtonText: "Aceptar"
                                    });
                                </script>
                            ';
                        }

                    }
                }

            }
        }

        public function cerrarSesionControladors(){
            session_destroy();

            if(headers_sent()){
                echo "
                    <script>
                        window.location.href = '".APP_URL."login/';
                    </script>
                ";
            }else{
                header("Location: ".APP_URL."login/");
            }

        }
    }
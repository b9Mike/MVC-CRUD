<?php

    namespace app\controllers;

    use app\models\mainModel;

    class searchController extends mainModel{

        #metodo modulos de busqueda
        public function modulosBusquedaControlador($modulo){

            $listaModulos = ['userSearch'];

            if(in_array($modulo, $listaModulos))
                return false;
            else
                return true;

        } 

        #metodo para iniciar la busqueda
        public function iniciarBuscadorControlador(){

            #almacenar datos en variables
            $url = $this->limpiarCadena($_POST['modulo_url']);
            $texto = $this->limpiarCadena($_POST['txt_buscador']);

            #verificar si es un modulo valido
            if($this->modulosBusquedaControlador($url)){
                
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "No podemos procesar esa petición",
                    "icono" => "error"
                ];

                return json_encode($alerta);
                exit();

            }

            #Verificar texto vacio
            if($texto == ""){
                
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "Introduce un texto en la busqueda",
                    "icono" => "error"
                ];

                return json_encode($alerta);
                exit();

            }

            #Verificar integridad de datos
            if($this->verificarDatos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{1,30}", $texto)){
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "El texto no coincide con el formato solicitado",
                    "icono" => "error"
                ];

                return json_encode($alerta);
                exit();
            }

            $_SESSION[$url] = $texto;

            $alerta = [
                "tipo" => "redireccionar",
                "url" => APP_URL.$url."/"
            ];

            return json_encode($alerta);

        }

        #metodo para eliminar la busqueda
        public function eliminarBuscadorControlador(){
            #almacenar datos en variables
            $url = $this->limpiarCadena($_POST['modulo_url']);

            #verificar si es un modulo valido
            if($this->modulosBusquedaControlador($url)){
                
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "texto" => "No podemos procesar esa petición",
                    "icono" => "error"
                ];

                return json_encode($alerta);
                exit();

            }

            unset($_SESSION[$url]);

            $alerta = [
                "tipo" => "redireccionar",
                "url" => APP_URL.$url."/"
            ];

            return json_encode($alerta);
        }

    }
<?php

    namespace app\models;

    use \PDO;

    if(file_exists(__DIR__."/../../config/server.php")){
        require_once __DIR__."/../../config/server.php";
    }

    class mainModel{

        private $server = DB_SERVER;
        private $db = DB_NAME;
        private $user = DB_USER;
        private $pass = DB_PASSWORD;

        protected function conectar(){
            
            $conexion = new PDO("mysql:host=".$this->server.";dbname=".$this->db, $this->user, $this->pass);
            $conexion->exec("SET CHARACTER SET utf8");

            return $conexion;
        }

        protected function ejecutarConsulta($consulta){
            
            $sql = $this->conectar()->prepare($consulta);
            $sql->execute();

            return $sql;
        }

        public function limpiarCadena($cadena){

            $palabras=["<script>","</script>","<script src","<script type=","SELECT * FROM","SELECT "," SELECT ","DELETE FROM","INSERT INTO","DROP TABLE","DROP DATABASE",
            "TRUNCATE TABLE","SHOW TABLES","SHOW DATABASES","<?php","?>","--","^","<",">","==","=",";","::"];

            $cadena = trim($cadena);
            $cadena = stripslashes($cadena);

            foreach($palabras as $palabra){
                $cadena = str_ireplace($palabra, "", $cadena);
            }

            $cadena = trim($cadena);
            $cadena = stripslashes($cadena);

            return $cadena;

        }

        protected function verificarDatos($filtro, $cadena){

            if(preg_match("/^$filtro$/", $cadena))
                return false;
            else
                return true;

        }

        //metodo para guardar datos
        protected function guardarDatos($tabla, $datos){
            
            $query = "INSERT INTO $tabla (";

            $count = 0;

            foreach($datos as $dato){

                if($count >= 1)
                    $query .= ",";
                $query .= $dato["campo_nombre"];
                
                $count++;

            }

            $query .= ") VALUES(";

            $count = 0;

            foreach($datos as $dato){

                if($count >= 1)
                    $query .= ",";
                $query .= $dato["campo_marcador"];
                
                $count++;

            }

            $query .= ")";

            $sql = $this->conectar()->prepare($query);

            foreach($datos as $dato){
                $sql->bindParam($dato["campo_marcador"], $dato["campo_valor"]);
            }

            $sql->execute();

            return $sql;
        }

        public function seleccionarDatos($tipo, $tabla, $campo, $id){

            $tipo = $this->limpiarCadena($tipo);
            $tabla = $this->limpiarCadena($tabla);
            $campo = $this->limpiarCadena($campo);
            $id = $this->limpiarCadena($id);

            if($tipo == "Unico"){
                
                $sql = $this->conectar()->prepare("SELECT * FROM $tabla WHERE $campo = :ID");
                $sql->bindParam(":ID", $id);

            }elseif($tipo == "Normal"){

                $sql = $this->conectar()->prepare("SELECT $campo FROM $tabla");

            }

            $sql->execute();

            return $sql;

        }

        protected function actualizarDatos($tabla, $datos, $condicion){
            
            $query = "UPDATE $tabla SET";

            $count = 0;

            foreach($datos as $dato){

                if($count >= 1)
                    $query .= ",";
                $query .= $dato["campo_nombre"]."=".$dato["campo_marcador"];
                
                $count++;

            }

            $query .= " WHERE ".$condicion['condicion_campo']." = ".$condicion['condicion_marcador'];


            $sql = $this->conectar()->prepare($query);

            foreach($datos as $dato){
                $sql->bindParam($dato["campo_marcador"], $dato["campo_valor"]);
            }
            $sql->bindParam($condicion["condicion_marcador"], $condicion["condicion_valor"]);

            $sql->execute();

            return $sql;
        }

        protected function eliminarDatos($tabla, $campo, $id){
            
            $sql = $this->conectar()->prepare("DELETE FROM $tabla WHERE $campo = :ID");
            $sql->bindParam(":ID", $id);

            $sql->execute();

            return $sql;
        }

        protected function paginadorTablas($pagina, $numeroPaginas, $url, $botones){
            
            $tabla = '<nav class="pagination is-centered is-rounded" role="navigation" aria-label="pagination">';
            
            if($pagina <= 1){
                $tabla .= '
                    <a class="pagination-previous is-disabled" disabled >Anterior</a>
                    <ul class="pagination-list">
                ';
            }else{
                $tabla .= '
                    <a class="pagination-previous" href="'.$url.($pagina - 1).'/">Anterior</a>
                    <ul class="pagination-list">
                        <li><a class="pagination-link" href="'.$url.'1/">1</a></li>
                        <li><span class="pagination-ellipsis">&hellip;</span></li>
                ';
            }

            //ciclo para poner botones de la pagina en medio
            $contador = 0;

            for($i = $pagina; $i <= $numeroPaginas; $i++){
                
                if($contador >= $botones)
                    break;
                
                if($pagina == $i)
                    $tabla .= '<li><a class="pagination-link is-current" href="'.$url.$i.'/">'.$i.'</a></li>';
                else
                    $tabla .= '<li><a class="pagination-link" href="'.$url.$i.'/">'.$i.'</a></li>';

                $contador++;
            }

            if($pagina == $numeroPaginas){
                $tabla .= '
                    </ul>
                    <a class="pagination-next is-disabled" disabled >Siguiente</a>
                ';
            }else{
                $tabla .= '
                    <li><span class="pagination-ellipsis">&hellip;</span></li>
                    <li><a class="pagination-link is-current" href="'.$url.$numeroPaginas.'">'.$numeroPaginas.'</a></li>
                    </ul>
                    <a class="pagination-next" href="'.$url.($pagina + 1).'/">Siguiente</a>
                ';
            }

            $tabla .= "</nav>";

            return $tabla;

        }
    }
<div class="container is-fluid mb-6">
    <?php
        $id = $instLogin->limpiarCadena($url[1]);

        if($id == $_SESSION['id']){
    ?>

	<h1 class="title">Mi foto de perfil</h1>
	<h2 class="subtitle">Actualizar foto de perfil</h2>

    <?php }else{ ?>

	<h1 class="title">Usuarios</h1>
	<h2 class="subtitle">Actualizar foto de perfil</h2>

    <?php } ?>

</div>
<div class="container pb-6 pt-6">
	
    <?php   
        include "./app/views/inc/btn_back.php";

        $datos = $instLogin->seleccionarDatos("Unico", "users", "user_id", $id);

        if($datos->rowCount() == 1){
            $datos = $datos->fetch();
    ?>


	<h2 class="title has-text-centered"><?php echo $datos['user_name']." ".$datos['user_lastname'] ?></h2>

	<p class="has-text-centered pb-6"><?php  echo "<strong>Usuario creado:</strong> ".date("d-m-Y h:i:s A", strtotime($datos["user_created"]))." &nbsp; <strong>Usuario actualizado: </strong>".date("d-m-Y h:i:s A", strtotime($datos["user_update"])); ?></p>

	<div class="columns">
		<div class="column is-two-fifths">

            <?php if(is_file("./app/views/fotos/".$datos['user_image'])){ ?>
			<figure class="image mb-6">
                <img class="is-rounded" src="<?php echo APP_URL."app/views/fotos/".$datos['user_image'];?>" >
			</figure>
			
			<form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/usuarioAjax.php" method="POST" autocomplete="off" >

				<input type="hidden" name="modulo_usuario" value="eliminarFoto">
				<input type="hidden" name="usuario_id" value="<?php echo $datos['user_id']; ?>">

				<p class="has-text-centered">
					<button type="submit" class="button is-danger is-rounded">Eliminar foto</button>
				</p>
			</form>

            <?php }else{ ?>

			<figure class="image mb-6">
			  	<img class="is-rounded" src="<?php echo APP_URL;?>app/views/fotos/default.png">
			</figure>

            <?php } ?>

		</div>


		<div class="column">
			<form class="mb-6 has-text-centered FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/usuarioAjax.php" method="POST" enctype="multipart/form-data" autocomplete="off" >

				<input type="hidden" name="modulo_usuario" value="actualizarFoto">
				<input type="hidden" name="usuario_id" value="<?php echo $datos['user_id']; ?>">
				
				<label>Foto o imagen del usuario</label><br>

				<div class="file has-name is-boxed is-justify-content-center mb-6">
				  	<label class="file-label">
						<input class="file-input" type="file" name="usuario_foto" accept=".jpg, .png, .jpeg" >
						<span class="file-cta">
							<span class="file-label">
								Seleccione una foto
							</span>
						</span>
						<span class="file-name">JPG, JPEG, PNG. (MAX 5MB)</span>
					</label>
				</div>
				<p class="has-text-centered">
					<button type="submit" class="button is-success is-rounded">Actualizar foto</button>
				</p>
			</form>
		</div>
	</div>
	
    <?php 
        }else{
            include "./app/views/inc/error_alert.php";
        } 
    ?>

</div>
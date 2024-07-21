
<div class="container is-fluid">
	<h1 class="title">Home</h1>
  	<div class="columns is-flex is-justify-content-center">
    	<figure class="image is-128x128">
    		<img class="is-rounded" src="<?php 
				$foto = "app/views/fotos/";
				$foto .= (isset($_SESSION['foto']) && $_SESSION['foto'] != "") ? $_SESSION['foto'] : "default.png";
				echo APP_URL.$foto; ?>" >
		</figure>
  	</div>
  	<div class="columns is-flex is-justify-content-center">
  		<h2 class="subtitle">¡Bienvenido <?php echo $_SESSION['nombre']." ".$_SESSION['apellido'];?>!</h2>
  	</div>
</div>
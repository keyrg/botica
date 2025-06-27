<?php
	if (isset($_POST['usuario']) && isset($_POST['clave'])) {
		require_once "./Controllers/loginControllers.php";
		$login = new loginControllers();
		echo $login->login_controller(); 
	}  
?>  
<!-- Vista Login -->

<div class="login-box">
  	<div class="login-logo">
		<a href="<?= base_url(); ?>">
			<img src="<?= media(); ?>/images/iconos/SanaPlus.png" alt=""> 
		</a>
	</div>
  	<div class="card">
    	<div class="card-body login-card-body">
      		<p class="login-box-msg">Ingrese sus datos de ingreso</p>
      		<form action="" method="POST" autocomplete="off" >
					<div class="input-group mb-3">
						<input type="text" class="form-control" name="usuario" placeholder="Usuario">
						<div class="input-group-append">
							<div class="input-group-text">
								<span class="fas fa-user"></span>
							</div>
						</div>
					</div>
					<div class="input-group mb-3">
						<input type="password" class="form-control" name="clave" placeholder="contraseña">
							<div class="input-group-append">
								<div class="input-group-text">
									<span class="fas fa-lock"></span>
								</div>
							</div>
						</div>
        			<button type="submit" class="btn btn-success btn-block">Ingresar</button>
      			<p class="mt-2 mb-0 text-right">Versión 1.0.0</p>
      		</form>
        </div>
  	</div>
</div>
<!-- /.Vista Login -->

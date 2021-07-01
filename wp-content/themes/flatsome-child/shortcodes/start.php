<?php
global $wpdb;
global $user;
global $_SESSION;

$_SESSION = 'hola';

$query = "select count(*) from wp_posts where post_author = {$user->ID} and post_type = 'markoh_notification' and post_status <> 'read'";
$nro_notificaciones = $wpdb->get_var($query);
?>
<a href="/available_courts" class="click_area canchas_disponibles">
	<img src="https://markoh.myappi.net/wp-content/uploads/2021/05/markoh-icono-4.png">
	<h3>Canchas Disponibles</h3>
</a>
<a href="/news" class="click_area jugar">
	<img src="https://markoh.myappi.net/wp-content/uploads/2021/05/markoh-icono-5.png">
	<h3>Jugar</h3>
</a>
<!--
<div class="click_area torneos">
	<img src="https://markoh.myappi.net/wp-content/uploads/2021/05/tenisclub-ranking-icon.png">
	<h3>Torneos</h3>
</div>
-->
<a href="/new_post_aux" class="click_area invitar_personas">
	<img src="https://markoh.myappi.net/wp-content/uploads/2021/05/markoh-icono-1.png">
	<h3>Armar Partido</h3>
</a>

<div id="fondo">
	<div id="pop-up">
		<button id="cerrar-pop-up">
			<span>×</span>
		</button>
		//Aquí insertamos el código de tu formulario, de tu llamada de acción o de lo que necesites poner
		//Aquí insertamos el código de tu formulario, de tu llamada de acción o de lo que necesites poner
		//Aquí insertamos el código de tu formulario, de tu llamada de acción o de lo que necesites poner
		//Aquí insertamos el código de tu formulario, de tu llamada de acción o de lo que necesites poner
		//Aquí insertamos el código de tu formulario, de tu llamada de acción o de lo que necesites poner
		//Aquí insertamos el código de tu formulario, de tu llamada de acción o de lo que necesites poner
		//Aquí insertamos el código de tu formulario, de tu llamada de acción o de lo que necesites poner
		//Aquí insertamos el código de tu formulario, de tu llamada de acción o de lo que necesites poner
		//Aquí insertamos el código de tu formulario, de tu llamada de acción o de lo que necesites poner
		//Aquí insertamos el código de tu formulario, de tu llamada de acción o de lo que necesites poner
		//Aquí insertamos el código de tu formulario, de tu llamada de acción o de lo que necesites poner
		//Aquí insertamos el código de tu formulario, de tu llamada de acción o de lo que necesites poner
		//Aquí insertamos el código de tu formulario, de tu llamada de acción o de lo que necesites poner
		//Aquí insertamos el código de tu formulario, de tu llamada de acción o de lo que necesites poner

		//Aquí insertamos el código de tu formulario, de tu llamada de acción o de lo que necesites poner
		//Aquí insertamos el código de tu formulario, de tu llamada de acción o de lo que necesites poner
		//Aquí insertamos el código de tu formulario, de tu llamada de acción o de lo que necesites poner
		//Aquí insertamos el código de tu formulario, de tu llamada de acción o de lo que necesites poner
		//Aquí insertamos el código de tu formulario, de tu llamada de acción o de lo que necesites poner
		//Aquí insertamos el código de tu formulario, de tu llamada de acción o de lo que necesites poner
		//Aquí insertamos el código de tu formulario, de tu llamada de acción o de lo que necesites poner

		//Aquí insertamos el código de tu formulario, de tu llamada de acción o de lo que necesites poner
		//Aquí insertamos el código de tu formulario, de tu llamada de acción o de lo que necesites poner
		//Aquí insertamos el código de tu formulario, de tu llamada de acción o de lo que necesites poner
		//Aquí insertamos el código de tu formulario, de tu llamada de acción o de lo que necesites poner
		//Aquí insertamos el código de tu formulario, de tu llamada de acción o de lo que necesites poner
		//Aquí insertamos el código de tu formulario, de tu llamada de acción o de lo que necesites poner
		//Aquí insertamos el código de tu formulario, de tu llamada de acción o de lo que necesites poner


	</div>
</div>

<button onclick="" class="btn_search" id="abrir-pop-up"><i class="fa fa-search"></i></button>

<?php include 'footer.php' ?>
<style>
	#content {
		background: #eaeaea;
		height: calc(100vh - 70px);
		overflow: auto;
	}

	.click_area.canchas_disponibles {
		background-image: url(https://markoh.myappi.net/wp-content/uploads/2021/05/markoh-image-banner-7.png);
	}

	.click_area.jugar {
		background-image: url(https://markoh.myappi.net/wp-content/uploads/2021/05/markoh-image-banner-2.png);
	}

	.click_area.torneos {
		background-image: url(https://markoh.myappi.net/wp-content/uploads/2021/05/markoh-image-banner-3.png);
	}

	.click_area.invitar_personas {
		background-image: url(https://markoh.myappi.net/wp-content/uploads/2021/05/markoh-image-banner-6.png);
	}

	.click_area img {
		height: 23%;
	}

	.click_area h3 {
		width: 50%;
		font-size: 1.3em;
		font-weight: bold;
		line-height: 1.1em;
		margin-left: 1em;
		color: white;
		margin-bottom: 0;
	}

	.click_area {
		text-decoration: none;
		background-position: center center;
		background-size: cover;
		max-width: 400px;
		margin: 0 auto 15px auto;
		height: 50vw;
		display: flex;
		align-items: center;
		padding-left: 10vw;
		max-height: 35vh;
	}

	.notifications_icon {
		padding: 10px;
		color: white !important;
		display: none;
	}

	.notifications_icon .fa {
		margin-right: 12px;
	}

	.notifications_icon .number {
		background: red;
		position: absolute;
		height: 15px;
		width: 15px;
		line-height: 15px;
		font-size: 10px;
		text-align: center;
		border-radius: 10px;
		bottom: -2px;
		right: 0px;
		font-style: normal;
	}

	.btn_search {
		background-color: #4e657b;
		border-radius: 200px;
		color: #FFF;
		position: fixed;
		bottom: 50px;
		right: 15px;
		z-index: 99;
		padding-left: 10px;
		padding-right: 10px;
	}

	#fondo {
		display: none;
		position: fixed;
		top: 0;
		bottom: 0;
		left: 0;
		right: 0;
		background-color: rgba(0, 0, 0, 0.7);
		z-index: 1111111;
	}

	#pop-up {
		background-color: white;
		width: 800px;
		position: absolute;
		top: 200px;
		left: 50%;
		margin-left: -400px;
		padding: 30px;
		border-radius: 10px;
	}

	#cerrar-pop-up {
		position: absolute;
		top: 0;
		right: 0;
		background-color: transparent;
	}

	#cerrar-pop-up span {
		display: flex;
		width: 50px;
		height: 50px;
		background-color: grey;
		justify-content: center;
		align-content: center;
		flex-direction: column;
		text-align: center;
		color: white;
		font-size: 40px;
		font-weight: bold;
		border-radius: 1000%;
	}

/* 	@media (max-width: 800px) {
		#pop-up {
			top: 0;
			left: 0;
			width: 100%;
			margin: 0;
			bottom: 0;
			overflow-y: scroll;
		}
	} */
</style>

<a href="/notifications" class="notifications_icon">
	<i class="fa fa-bell"></i>
	<?php if ($nro_notificaciones > 0) : ?>
		<i class="number"><?= $nro_notificaciones > 9 ? "+9" : $nro_notificaciones ?></i>
	<?php endif; ?>
</a>
<?php add_action('wp_footer', function () { ?>
	<script>
		jQuery(function($) {
			$(".mobile-nav.nav.nav-right").append("<li></li>");
			$(".mobile-nav.nav.nav-right li").append($('.notifications_icon'));
			$('.notifications_icon').show();

			//Evento para abrir el pop up al pulsar un botón. Solo tienes que ponerle al botón que quieras el id abrir-pop-up
			$('#abrir-pop-up').click(function() {
				$('#fondo').fadeIn(300);
			});

			//Cerrar el pop up al pulsar en la aspa
			$('#cerrar-pop-up').click(function() {
				$('#fondo').fadeOut(300);
			});
		});
	</script>
<?php }); ?>
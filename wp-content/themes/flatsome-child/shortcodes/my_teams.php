<?php
global $wpdb;
global $user;
$hoy = date('Y-m-d');

$wpdb->get_results("SET lc_time_names = 'es_ES'");
$query = "
			SELECT 
				t.*,
				if(creado_por={$user->ID},1,0) editable
			  FROM 
				wp_teams t
			  INNER JOIN
				wp_team_players tp 
				ON tp.id_team = t.id 
			  WHERE
				tp.id_player = {$user->ID}";

$equipos = $wpdb->get_results($query);


?>
<div class="fake_header">
	<a href="/start" class="icon left"><i class="fa fa-chevron-left"></i></a>
	<a href="/new_team" class="icon right"><i class="fa fa-plus"></i></a>
	<h2 class="title">Mis Equipos</h2>
</div>
<div class="fake_body">
	<?php if (count($equipos)) : ?>
		<?php foreach ($equipos as $v) : ?>
			<div class="noticia">
				<div class="image_container" style="background-image:url(<?= $v->logo_url == "" ? "/wp-content/uploads/2021/05/cancha.png" : $v->logo_url ?>);"></div>
				<?php if ($v->editable) : ?>
					<a href="#" class="opciones"><i class="fa fa-ellipsis-v"></i></a>
					<div class="opciones_links">
						<a href="/update_team?id=<?= $v->id ?>&invite=1" class="invitar_amigos">Invitar Jugadores</a>
						<hr style="margin:5px 0;">
						<a href="/update_team?id=<?= $v->id ?>">Modificar Datos</a>
						<a href="#" data-nombre="<?= $v->nombre ?>" data-id="<?= $v->id ?>" class="eliminar_equipo">Eliminar Equipo</a>
					</div>
				<?php endif; ?>
				<h3><?= $v->nombre ?></h3>
				<small>Futbol <?= $v->tipo ?></small><br>
				<small><?= $v->descripcion ?></small><br>
				<div class="jugadores">
					<?php $jugadores = $wpdb->get_results("select id_player from wp_team_players where id_team={$v->id}"); ?>
					<?php foreach ($jugadores as $j) : ?>
						<?php
						$imagen = get_user_meta($j->id_player, 'profile_picture')[0] == "" ? "/wp-content/uploads/2021/05/user_avatar.png" : get_user_meta($j->id_player, 'profile_picture')[0];
						$nombre = get_user_meta($j->id_player, 'nombre')[0];
						$apellido = get_user_meta($j->id_player, 'apellido')[0];
						$posicion = get_user_meta($j->id_player, 'posicion')[0];

						?>
						<div class="jugador" style="position:relative;margin-top:10px;padding-left:40px;">
							<div class="image_container" style="background-image:url(<?= $imagen ?>);top:0;left:0px;height:30px;width:30px;"></div>
							<b><?= $nombre . " " . $apellido ?>.</b>
							<small><?= $posicion ?></small>
						</div>
					<?php endforeach; ?>

				</div>
			</div>
		<?php endforeach; ?>

	<?php else : ?>
		<h1 class="no_teams">Aún no forma parte de ningún equipo</h1>
	<?php endif; ?>
</div>
<?php include 'footer.php' ?>
<style>
	.no_teams {
		margin: 10px;
		padding: 5px;
		text-align: center;
		box-sizing: border-box;
		width: auto;
		border: 1px solid #aaa;
		border-radius: 5px;
	}

	.opciones_links a {
		display: block;
	}

	.opciones_links.active {
		display: block;

	}

	.opciones_links {
		z-index: 10;
		position: absolute;
		display: none;
		top: 15px;
		right: 80px;
		width: 200px;
		background: white;
		padding: 6px 15px;
		text-align: center;
		border: solid 1px #ddd;
		border-radius: 5px;
		box-shadow: 4px 4px 11px -5px rgb(0 0 0 / 20%);
	}

	a.opciones {
		position: absolute;
		top: 20px;
		right: 30px;
		background: #eaeaea;
		padding: 10px;
		display: block;
		line-height: 14px;
		width: 40px;
		height: 40px;
		display: flex;
		justify-content: center;
		align-items: center;
		border-radius: 20px;
	}

	.image_container {
		width: 50px;
		height: 50px;
		background-color: #eee;
		border-radius: 500px;
		overflow: hidden;
		background-position: top center;
		background-size: contain;
		margin: 0 auto;
		position: absolute;
		top: 15px;
		left: 20px;
	}

	.fake_body {
		padding-top: 80px;
		padding-bottom: 10px;
		min-height: 300px;
		background: #f2f2f2;
	}

	html {
		background: #f2f2f2;
	}

	.fake_header .icon.right {
		position: absolute;
		top: 10px;
		right: 15px;
		color: white;
		padding: 10px;
		z-index: 5;
	}

	.fake_header .icon.left {
		position: absolute;
		top: 10px;
		left: 15px;
		color: white;
		padding: 10px;
		z-index: 5;
	}

	.fake_header .title {
		position: absolute;
		top: 23px;
		left: 15px;
		color: white;
		text-align: center;
		width: 93%;
	}

	.fake_header {
		position: fixed;
		z-index: 99;
		top: 0;
		left: 0;
		background: #004454;
		width: 100%;
		padding: 5px;
		color: white;
		height: 70px;
	}

	.noticia h3 span {
		color: rgb(33, 158, 168);
	}

	.noticia>i {
		position: absolute;
		width: 10px;
		height: 10px;
		border-radius: 10px;
		background: rgb(33, 158, 168);
		display: block;
		top: 20px;
		left: 17px;
	}

	.noticia h3 {
		font-size: 1.2em;
		margin: 0;
	}

	.noticia p {
		margin-bottom: 5px;
	}

	.noticia {
		position: relative;
		margin: 10px;
		padding: 10px 20px 20px 80px;
		margin-bottom: 25px !important;
		background: white;
		border-radius: 8px;
		border: none;
		box-shadow: 2px 2px 10px -1px rgb(0 0 0 / 10%);
	}
</style>
<?php add_action('wp_footer', function () { ?>
	<script>
		jQuery(function($) {
			$('a.eliminar_equipo').on('click', function(e) {
				e.preventDefault()
				if (confirm("¿Desea eliminar el equipo '" + $(this).data('nombre') + "'?")) {
					$.post("/wp-admin/admin-ajax.php?action=custom_ajax&caction=delete_team&id=" + $(this).data('id'), null,
						function(r) {
							alert(r.message);
							window.location.reload();
						}, 'json'
					)
				}
			});
			$('a.opciones').on('click', function(e) {
				e.preventDefault()
				$(this).next().toggleClass("active")
			})
			$('form#form_register').on('submit', function(e) {
				e.preventDefault()
				var data = $(this).serialize();
				$.post('/wp-admin/admin-ajax.php?action=custom_ajax&caction=register',
					data,
					function(r) {
						alert(r.message);
						if (r.success)
							window.location.href = r.url;


					}, "json")

				return false;

			});
		})
	</script>
<?php }); ?>
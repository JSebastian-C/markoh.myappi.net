<?php
global $wpdb;
global $user;
$hoy = date('Y-m-d');
$wpdb->get_results("SET lc_time_names = 'es_ES'");
$query = "
			SELECT 
				p.id,
				p.post_author autor,
				p.post_title titulo,
				p.post_content json_data,
				(
					select 
						meta_value 
					from 
						wp_usermeta
					where 
						meta_key='profile_picture' and
						user_id = u.ID
					limit 1
				) imagen,
				(
					select 
						meta_value 
					from 
						wp_usermeta
					where 
						meta_key='nombre' and
						user_id = u.ID
					limit 1
				) nombre,
				(
					select 
						meta_value 
					from 
						wp_usermeta
					where 
						meta_key='apellido' and
						user_id = u.ID
					limit 1
				) apellido,
				(
					select 
						meta_value 
					from 
						wp_usermeta
					where 
						meta_key='telefono' and
						user_id = u.ID
					limit 1
				) telefono,
				date_format(p.post_date,'%d %M %Y')  fecha,
				u.ID user_id
			  FROM 
				wp_posts p
			  INNER JOIN
				wp_users u ON p.post_author = u.ID
			  WHERE
				p.post_date >= '$hoy' AND 
				p.post_type = 'markoh_invitacion' and
				p.post_status not in('pending','accepted')
			  ORDER BY	
				p.post_date asc";

$noticias = $wpdb->get_results($query);


//Consulta para saber si el usuario hace parte de un equipo
$estar_en_equipo = count($wpdb->get_row("SELECT * FROM wp_team_players WHERE id_player=" . $user->ID));

//Ciclo encargado de modificar la variable "$noticias" para que el usuario que no está en un equipo no vea publicaciones de equipos
for ($i = 0; $i < count($noticias); $i++) {

	if (($user->roles[0] == 'jugador') && ($estar_en_equipo == 0) && (json_decode($noticias[0]->json_data)->tipo_publicacion == 'equipo')) {
		unset($noticias[$i]);
	}
}

?>
<div class="fake_header">
	<a href="/start" class="icon left"><i class="fa fa-chevron-left"></i></a>
	<a href="/new_post" class="icon right"><i class="fa fa-plus"></i></a>
	<h2 class="title">Invitaciones</h2>
</div>
<div class="fake_body">
	<?php if (count($noticias)) : ?>
		<?php foreach ($noticias as $v) : ?>
			<?php $data = json_decode($v->json_data); ?>
			<div class="noticia">
				<?php if ($data->tipo_publicacion == 'equipo') : ?>
					<h3>¡<?= strtoupper($data->equipo->nombre) ?> busca Match!</h3>
					<small><?= ucwords($v->fecha) ?></small>
					<br>
					<p>Ciudad: <?= $data->ciudad ?></p>
					<p>Lugar: <?= $data->lugar ?></p>

					<?php if (!empty($data->anuncio)) : ?>
						<p><?= $data->anuncio ?></p>
					<?php endif; ?>

					<i></i>

					<div style="font-size:13px;margin: 10px 0;background: #eee;padding: 5px 10px;border-radius: 3px;">
						<p>
							Tipo Match: Futbol <?= $data->equipo->tipo ?><br>
							Publicado por: <?= $v->nombre . ' ' . $v->apellido ?>
						</p>
					</div>
					<?php if (!empty($v->telefono)) : ?>
						<a target="blank" href="whatsapp://send?phone=57<?= $v->telefono ?>" class="whatsapp">
							<img src="https://markoh.myappi.net/wp-content/uploads/2021/05/whatsapp_icon.png">
							<span><?= $v->telefono ?></span>
						</a>
					<?php endif; ?>
					<div class="actions">
						<?php if ($v->autor != $user->ID) : ?>
							<a class="button primary is-xsmall aceptar_reto" data-id="<?= $v->id ?>" data-post='<?= json_encode($v) ?>' data-anuncio='<?= json_encode($data) ?>'> <span>¡Aceptar Reto!</span> </a>
						<?php endif; ?>
						<?php if ($v->autor == $user->ID) : ?>
							<a href="#" class="button alert is-xsmall delete" data-id="<?= $v->id ?>"><i class="fa fa-trash"></i> Eliminar</a>
						<?php endif; ?>
					</div>
				<?php else : ?>
					<h3>¡<?= $v->nombre . ' ' . $v->apellido ?> quiere jugar como <span><?= ucfirst($data->posicion) ?></span>!</h3>
					<small><?= ucwords($v->fecha) ?></small><br>
					<i></i>
					<p>Ciudad: <?= $data->ciudad ?></p>
					<p>Lugar: <?= $data->lugar ?></p>

					<?php if (!empty($data->anuncio)) : ?>
						<p><?= $data->anuncio ?></p>
					<?php endif; ?>

					<?php if (!empty($v->telefono)) : ?>
						<a target="blank" href="whatsapp://send?phone=57<?= $v->telefono ?>" class="whatsapp">
							<img src="https://markoh.myappi.net/wp-content/uploads/2021/05/whatsapp_icon.png">
							<span><?= $v->telefono ?></span>
						</a>
					<?php endif; ?>
					<div class="actions">
						<?php if ($v->autor != $user->ID) : ?>
							<a class="button primary is-xsmall pedir" id="btn-convocar"  data-id="<?= $v->id ?>" data-post='<?= json_encode($v) ?>' data-anuncio='<?= json_encode($v) ?>'> <span>¡Convocar!</span> </a>
						<?php endif; ?>
						<?php if ($v->autor == $user->ID) : ?>
							<a href="#" class="button alert is-xsmall delete" id="btn-eliminar" data-id="<?= $v->id ?>"><i class="fa fa-trash"></i> Eliminar</a>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>

	<?php else : ?>
		<h1>Aún no hay noticias registradas</h1>
	<?php endif; ?>
</div>
<?php include 'footer.php' ?>
<style>
	.whatsapp span {
		color: white;
		font-weight: bold;
		font-size: 17px;
		vertical-align: middle;
	}

	.whatsapp img {
		height: 25px;
	}

	.whatsapp {
		border-radius: 5px;
		text-align: center;
		margin: 10px 0;
		white-space: nowrap;
		background: #1bd741;
		padding: 0 10px;
		display: inline-block;
	}

	#section_680879321 {
		padding-top: 0;
		padding-bottom: 0;
		margin-bottom: 0;
	}

	.fake_body {
		padding: 80px 10px 10px 10px;
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
		top: 0;
		left: 0;
		background: #004454;
		width: 100%;
		padding: 5px;
		color: white;
		height: 70px;
		z-index: 2;
	}

	#wrapper,
	#main {
		background: #f3f3f3 !important;
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
		margin-bottom: 0;
		line-height: 17px;
	}

	.noticia {
		position: relative;
		margin: 5px;
		padding: 10px 20px 10px 40px;
		margin-bottom: 25px !important;
		background: white;
		border-radius: 8px;
		border: none;
		box-shadow: 2px 2px 10px -1px rgb(0 0 0 / 10%);
	}

	#btn-eliminar, #btn-convocar{
		margin-top: 10px;
	}
</style>
<?php add_action('wp_footer', function () {
	global $user; ?>
	<script>
		jQuery(function($) {
			$('.delete').on('click', function(e) {
				e.preventDefault();
				var id = $(this).data('id')

				if (confirm("¿Está seguro que quiere eliminar ésta invitación?")) {
					$.post('/wp-admin/admin-ajax.php?action=custom_ajax&caction=delete_post', {
							user_id: <?= $user->ID ?>,
							"id": id
						},
						function(r) {
							alert(r.message);
							if (r.success)
								window.location.reload();


						}, "json")
				}
			});
			$('.pedir').on('click', function(e) {
				e.preventDefault();
				var post = $(this).data('post')
				var anuncio = $(this).data('anuncio')
				if (confirm("¿Está seguro que quiere pedir a este jugador?")) {
					$.post('/wp-admin/admin-ajax.php?action=custom_ajax&caction=pedir_jugador', {
							user_id: <?= $user->ID ?>,
							post_id: post.id
						},
						function(r) {
							alert(r.message);
							if (r.success)
								window.location.href = "/notifications";


						}, "json")
				}
			});
			$('.aceptar_reto').on('click', function(e) {
				e.preventDefault();
				var post = $(this).data('post')
				var anuncio = $(this).data('anuncio')
				if (confirm("¿Está seguro que quiere aceptar el reto de " + anuncio.equipo.nombre + " para jugar el " + post.fecha + "?")) {
					$.post('/wp-admin/admin-ajax.php?action=custom_ajax&caction=aceptar_reto_equipo', {
							user_id: <?= $user->ID ?>,
							post_id: post.id
						},
						function(r) {
							alert(r.message);
							if (r.success)
								window.location.href = "/notifications";


						}, "json")
				}
			});
		})
	</script>
<?php }); ?>
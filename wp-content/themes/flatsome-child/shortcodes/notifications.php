<?php
global $wpdb;
global $user;

$noticias = array();

switch ($user->roles[0]) {
	case "jugador":
		//$wpdb->update("wp_posts", ['post_status' => 'read'], ["post_author" => $user->ID, "post_type" => 'markoh_notification']);
		$n = $wpdb->get_results("SELECT * FROM wp_posts WHERE post_status IN('publish', 'scheduled') AND post_type = 'markoh_notification' ORDER BY ID DESC");
		foreach ($n as $nc) {
			$rival = $wpdb->get_row("SELECT creado_por FROM wp_teams WHERE id = " . json_decode(stripslashes(json_decode($nc->post_content)->post_content))->respuesta[1]->equipo_rival);

			//Valida si el usuario en sesión figura cómo lider de un equipo en el post agregar el post al arreglo "$noticias"
			switch ($nc->post_status) {
				case "scheduled":
					if (
						json_decode(stripslashes(json_decode($nc->post_content)->post_content))->equipo->creado_por == $user->ID ||
						$rival->creado_por == $user->ID
					) {
						array_push($noticias, $nc);
					}
					break;

				case "publish":
					if ($nc->post_author == $user->ID) {
						array_push($noticias, $nc);
					}
					break;
			}
		}
		break;

	case "encargado_cancha":
		$noticias = $wpdb->get_results("SELECT * FROM wp_posts WHERE post_author = {$user->ID} AND post_type = 'markoh_notification' AND post_status = 'scheduled'");
		break;

	case "administrator":
		$noticias = $wpdb->get_results("SELECT * FROM wp_posts WHERE post_type = 'markoh_notification' AND post_status = 'scheduled'");
		break;

	default:
		print_r("Rol desconocido");
		break;
}
?>
<div class="fake_header">
	<a href="/start" class="icon left"><i class="fa fa-chevron-left"></i></a>
	<a href="/new_post" class="icon right" hidden><i class="fa fa-plus"></i></a>
	<h2 class="title">Notificaciones</h2>
</div>
<div class="fake_body">
	<?php if (count($noticias)) : ?>
		<?php for ($i = 0; $i < count($noticias); $i++) : ?>
			<?php $data = json_decode($noticias[$i]->post_content); ?>

			<?php switch ($user->roles[0]):
				case "jugador": ?>
					<?php switch ($noticias[$i]->post_status):
						case "publish": ?>
							<div class="noticia_jugador_p">
								<i></i>
								<h3><?= $noticias[$i]->post_title ?></h3>
								<p><?= $noticias[$i]->post_excerpt ?></p>
								<?php if (isset($data->telefono)) : ?>
									<a target="blank" href="whatsapp://send?phone=57<?= $data->telefono ?>" class="whatsapp">
										<img src="<?php dirname(__DIR__, 4) ?>/wp-content/uploads/2021/05/whatsapp_icon.png" />
										<span><?= $data->telefono ?></span>
									</a>
								<?php endif; ?>
								<a href="#" class="delete" data-id="<?= $noticias[$i]->ID ?>"><i class="fa fa-trash"></i></a>
							</div>
							<?php break; ?>

						<?php
						case "scheduled":
							$cancha = $wpdb->get_row("SELECT * FROM wp_courts WHERE id = " . json_decode(stripslashes(json_decode($noticias[$i]->post_content)->post_content))->cancha);
							$encargado_cancha = get_user_meta($cancha->creado_por);
						?>

							<div class="noticia_jugador_s">
								<i></i>
								<h3><?= $noticias[$i]->post_title ?></h3>
								<p><?= $noticias[$i]->post_excerpt ?></p>
								<?php if (isset($data->telefono)) : ?>
									<a target="blank" href="whatsapp://send?phone=57<?= $data->telefono ?>" class="whatsapp">
										<img src="<?php dirname(__DIR__, 4) ?>/wp-content/uploads/2021/05/whatsapp_icon.png" />
										<span><?= $data->telefono ?></span>
									</a>
								<?php endif; ?>

								<div class="cancha_info">
									<em><b>DATOS DEL LUGAR DEL PARTIDO</b><em><br>
											<em>Cancha: <?= $cancha->nombre ?></em><br>
											<em>Dirección: <?= $cancha->direccion ?></em><br>
											<em>Ciudad: <?= $cancha->ciudad ?></em><br>
											<em>Día: <?= json_decode(stripslashes(json_decode($noticias[$i]->post_content)->post_content))->vence ?>
												a las <?= json_decode(stripslashes(json_decode($noticias[$i]->post_content)->post_content))->hora ?> </em><br>
											<?php if (isset($cancha->telefono)) : ?>
												<a target="blank" href="whatsapp://send?phone=57<?= $cancha->telefono ?>" class="whatsapp">
													<img src="<?php dirname(__DIR__, 4) ?>/wp-content/uploads/2021/05/whatsapp_icon.png" />
													<span><?= $cancha->telefono ?></span>
												</a>
											<?php endif; ?>
											<div class="divider"></div>
								</div>

								<p class="soporte">Mensaje de ejemplo: Número de soporte</p>

								<a href="#" class="delete" data-id="<?= $noticias[$i]->ID ?>"><i class="fa fa-trash"></i></a>
							</div>
							<?php break; ?>
					<?php endswitch; ?>
					<?php break; ?>



				<?php
				case "encargado_cancha": ?>
					<div class="noticia_encargado_cancha">
						<?php
						$cancha = $wpdb->get_row("SELECT * FROM wp_courts WHERE id = " . json_decode(stripslashes(json_decode($noticias[$i]->post_content)->post_content))->cancha);
						?>

						<i></i>
						<h3><?= $noticias[$i]->post_title ?></h3>
						<p><?= $noticias[$i]->post_excerpt ?></p>
						<?php if (isset($data->telefono)) : ?>
							<a target="blank" href="whatsapp://send?phone=57<?= $data->telefono ?>" class="whatsapp">
								<img src="<?php dirname(__DIR__, 4) ?>/wp-content/uploads/2021/05/whatsapp_icon.png" />
								<span><?= $data->telefono ?></span>
							</a>
						<?php endif; ?>

						<div class="cancha_info">
							<em><b>DATOS DEL LUGAR DEL PARTIDO</b><em><br>
									<em>Cancha: <?= $cancha->nombre ?></em><br>
									<em>Dirección: <?= $cancha->direccion ?></em><br>
									<em>Ciudad: <?= $cancha->ciudad ?></em><br>
									<em>Día: <?= json_decode(stripslashes(json_decode($noticias[$i]->post_content)->post_content))->vence ?>
										a las <?= json_decode(stripslashes(json_decode($noticias[$i]->post_content)->post_content))->hora ?> </em><br>
						</div>

						<a href="#" class="delete" data-id="<?= $noticias[$i]->ID ?>"><i class="fa fa-trash"></i></a>
					</div>
					<?php break; ?>



				<?php
				case "administrator": ?>
					<div class="noticia_administrador">
						<?php
						$cancha = $wpdb->get_row("SELECT * FROM wp_courts WHERE id = " . json_decode(stripslashes(json_decode($noticias[$i]->post_content)->post_content))->cancha);
						?>

						<i></i>
						<!-- <h3> -->
						<!-- <?= $noticias[$i]->post_title ?> -->
						<!-- 	</h3> -->
						<p><?= $noticias[$i]->post_excerpt ?></p>
						<?php if (isset($data->telefono)) : ?>
							<a target="blank" href="whatsapp://send?phone=57<?= $data->telefono ?>" class="whatsapp">
								<img src="<?php dirname(__DIR__, 4) ?>/wp-content/uploads/2021/05/whatsapp_icon.png" />
								<span><?= $data->telefono ?></span>
							</a>
						<?php endif; ?>

						<div class="cancha_info">
							<em><b>DATOS DEL LUGAR DEL PARTIDO</b><em><br>
									<em>Cancha: <?= $cancha->nombre ?></em><br>
									<em>Dirección: <?= $cancha->direccion ?></em><br>
									<em>Ciudad: <?= $cancha->ciudad ?></em><br>
									<em>Día: <?= json_decode(stripslashes(json_decode($noticias[$i]->post_content)->post_content))->vence ?>
										a las <?= json_decode(stripslashes(json_decode($noticias[$i]->post_content)->post_content))->hora ?> </em><br>
									<?php if (isset($encargado_cancha["telefono"][0])) : ?>
										<a target="blank" href="whatsapp://send?phone=57<?= $encargado_cancha["telefono"][0] ?>" class="whatsapp">
											<img src="<?php dirname(__DIR__, 4) ?>/wp-content/uploads/2021/05/whatsapp_icon.png" />
											<span><?= $encargado_cancha["telefono"][0] ?></span>
										</a>
									<?php endif; ?>
						</div>
					</div>
					<?php break; ?>



				<?php
				default: ?>
					<em>Rol desconocido</em>
					<?php break; ?>
			<?php endswitch; ?>
		<?php endfor; ?>

	<?php else : ?>
		<h1>No tiene notificaciones</h1>
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

	.fake_body {
		padding: 80px 20px 10px 20px;
		min-height: 100vh;
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

	.noticia_jugador_p h3 span {
		color: rgb(33, 158, 168);
	}

	.noticia_jugador_p>i,
	.noticia_jugador_s>i,
	.noticia_encargado_cancha>i,
	.noticia_administrador>i {
		position: absolute;
		width: 10px;
		height: 10px;
		border-radius: 10px;
		background: rgb(33, 158, 168);
		display: block;
		top: 20px;
		left: 17px;
	}

	.noticia_jugador_p h3,
	.noticia_jugador_s h3,
	.noticia_encargado_cancha h3,
	.noticia_administrador h3 {
		font-size: 1.2em;
		margin: 0;
	}

	.noticia_jugador_p p,
	.noticia_jugador_s p,
	.noticia_encargado_cancha p,
	.noticia_administrador p {
		margin-bottom: 5px;
	}

	.noticia_jugador_p,
	.noticia_jugador_s,
	.noticia_encargado_cancha,
	.noticia_administrador {
		position: relative;
		margin: 5px;
		padding: 10px 20px 10px 40px;
		margin-bottom: 25px !important;
		background: white;
		border-radius: 8px;
		border: none;
		box-shadow: 2px 2px 10px -1px rgb(0 0 0 / 10%);
	}

	.delete {
		margin-left: 30px;
		position: absolute;
		display: block;
		right: 23px;
		top: 13px;
		color: #d53434;
	}

	.divider {
		height: 3px;
		width: 100%;
		background-color: #eee;
		border-radius: 15px;
	}

	.cancha_info {
		margin-top: 10px;
		margin-bottom: 20px;
	}

	.cancha_info em {
		font-size: 13px;
	}
</style>
<?php add_action('wp_footer', function () {
	global $user; ?>
	<script>
		jQuery(function($) {
			$('.delete').on('click', function(e) {
				e.preventDefault();
				var id = $(this).data('id')

				if (confirm("¿Está seguro que quiere eliminar ésta notificación?")) {
					$.post('/wp-admin/admin-ajax.php?action=custom_ajax&caction=delete_notification', {
							"user_id": <?= $user->ID ?>,
							"id": id
						},
						function(r) {
							alert(r.message);
							if (r.success) {
								window.location.reload();
							}

						}, "json"
					)
				}
			});
		})
	</script>
<?php }); ?>
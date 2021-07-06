<?php
global $wpdb;
global $user;
$hoy = date('Y-m-d');
//$hoy = date("y") . "-" . date("m") . "-" . strval(intval(date("d")) - 1);
$wpdb->get_results("SET lc_time_names = 'es_ES'");
$query_jugador = "
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
			 	p.post_type = 'markoh_invitacion' AND
				p.post_status not in('pending','accepted')
			  ORDER BY	
				p.post_date asc";

$query_encargado_cancha = "SELECT *	FROM wp_posts WHERE	post_date >= " . $hoy." AND post_status in('pending')";

$jugador_n = $wpdb->get_results($query_jugador);
$encargado_cancha_n = $wpdb->get_results($query_encargado_cancha);
$noticias = array();

//Filtrado de noticias por rol
switch ($user->roles[0]) {
	case 'jugador':
		//Consulta para saber si el usuario hace parte de un equipo
		$estar_en_equipo = count($wpdb->get_row("SELECT * FROM wp_team_players WHERE id_player=" . $user->ID));

		for ($x = 0; $x < count($jugador_n); $x++) {
			if ($estar_en_equipo == 0) {
				if (json_decode(stripslashes($jugador_n[$x]->json_data))->tipo_publicacion == 'partido') {
					$noticias[$x] = [$jugador_n[$x]];
				}
			} else {
				if (json_decode(stripslashes($jugador_n[$x]->json_data))->tipo_publicacion == 'equipo') {
					$datos_cancha = $wpdb->get_row("SELECT * FROM wp_courts WHERE id = " . json_decode(stripslashes($jugador_n[$x]->json_data))->cancha);
					$noticias[$x] = [$jugador_n[$x], $datos_cancha];
				} else {
					$noticias[$x] = [$jugador_n[$x]];
				}
			}
		}
		break;

	case 'encargado_cancha':
		for ($j = 0; $j < count($encargado_cancha_n); $j++) {
			/* if ((isset(json_decode($encargado_cancha_n[$j]->post_content)->post_date)) && (new DateTime(json_decode($encargado_cancha_n[$j]->post_content)->post_date) >= new DateTime($hoy))) {
				if (json_decode(stripslashes(json_decode($encargado_cancha_n[$j]->post_content)->post_content))->tipo_publicacion == "equipo") {
					$noticias[$j] = $encargado_cancha_n[$j];
				}
			} */
			var_dump(json_decode(stripslashes($encargado_cancha_n[$j]->post_content)));

			//$noticias[$j] = $encargado_cancha_n[$j];
		}

		break;

	case 'administrator':
		for ($i = 0; $i < count($jugador_n); $i++) {
			if (json_decode(stripslashes($jugador_n[$i]->json_data))->tipo_publicacion == 'equipo' || json_decode(stripslashes($jugador_n[$i]->json_data))->tipo_publicacion == 'partido') {
				$datos_cancha = $wpdb->get_row("SELECT * FROM wp_courts WHERE id = " . json_decode(stripslashes($jugador_n[$i]->json_data))->cancha);
			}
			$noticias[$i] = [$jugador_n[$i], $datos_cancha];
		}
		break;

	default:
		print_r("Algo mal sucede en switch de filtrado de noticias por rol");
		break;
}

?>
<div class="fake_header">
	<a href="/start" class="icon left"><i class="fa fa-chevron-left"></i></a>
	<a href="/new_post" class="icon right"><i class="fa fa-plus"></i></a>
	<h2 class="title">Invitaciones</h2>
</div>
<div class="fake_body">
	<!-- Ventana modal que aparece al aceptar un reto -->
	<div class="mainModal">
		<div class="childModal">
			<?php
			//Se seleccionan mis equipos
			$mis_equipos = $wpdb->get_results("SELECT * FROM wp_teams WHERE creado_por = {$user->ID}");

			?>

			<!-- Botón que cierra la ventana modal -->
			<div class="buttonCloseModal"><i class="fa fa-close"></i></div>

			<div class="containerFormModal">
				<h3>Ingresa algunos datos adiccionales</h3>

				<!-- Formulario donde estarán los datos de a quien se van a enfrentar y los datos si quieren jugar en otra cancha -->
				<form class="formModal">
					<div>
						<b>Selecciona tu equipo que se enfrentará</b>
						<select name="equipo_retador" id="equipo_s" required>
							<option value="">Seleccionar</option>
							<?php foreach ($mis_equipos as $v) : ?>
								<option value="<?= $v->id ?>"> <?= $v->nombre ?> </option>
							<?php endforeach; ?>
						</select>
						<!-- En la etiqueta <p> se muestra el tipo de futbol que juega el equipo seleccionado -->
						<div id="aux_tipo_futbol"></div>
					</div>

					<textarea id="textAreaModal" name="datos_otra_cancha" maxlength='200' placeholder="Aquí puedes agregar los datos de una cancha diferente en la que quieras jugar (OPCIONAL)"></textarea>
					<a><button id="buttonSaveInfo" type="submit">Proceder</button></a>
				</form>
			</div>
		</div>
	</div>


	<?php if (count($noticias)) : ?>
		<?php switch ($user->roles[0]):
			case 'jugador': ?>
				<?php foreach ($noticias as $v) : ?>
					<?php $data = json_decode(stripslashes($v[0]->json_data)) ?>
					<div class="noticia_jugador">
						<?php if ($data->tipo_publicacion == 'equipo') : ?>
							<h3>¡<?= strtoupper($data->equipo->nombre) ?> busca Match!</h3>
							<small><?= ucwords($v[0]->fecha) ?></small>
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
									Publicado por: <?= $v[0]->nombre . ' ' . $v[0]->apellido ?><br>
									<?= isset($v[1]->nombre) ? "Sitio de juego: " .  $v[1]->nombre : null ?>
								</p>
							</div>
							<?php if (!empty($v[0]->telefono)) : ?>
								<a target="blank" href="whatsapp://send?phone=57<?= $v[0]->telefono ?>" class="whatsapp">
									<img src="https://markoh.myappi.net/wp-content/uploads/2021/05/whatsapp_icon.png">
									<span><?= $v[0]->telefono ?></span>
								</a>
							<?php endif; ?>
							<div class="actions">
								<?php if ($v[0]->autor != $user->ID) : ?>
									<a class="button primary is-xsmall aceptar_reto" data-id="<?= $v[0]->id ?>" data-post='<?= json_encode($data) ?>' data-anuncio='<?= json_encode($v[0]) ?>'> <span>¡Aceptar Reto!</span> </a>
								<?php endif; ?>
								<?php if ($v[0]->autor == $user->ID) : ?>
									<a href="#" class="button alert is-xsmall delete" data-id="<?= $v[0]->id ?>"><i class="fa fa-trash"></i> Eliminar</a>
								<?php endif; ?>
							</div>
						<?php else : ?>
							<h3>¡<?= $v[0]->nombre . ' ' . $v[0]->apellido ?> quiere jugar como <span><?= ucfirst($data->posicion) ?></span>!</h3>
							<small><?= ucwords($v[0]->fecha) ?></small><br>
							<i></i>
							<p>Ciudad: <?= $data->ciudad ?></p>
							<p>Lugar: <?= $data->lugar ?></p>

							<?php if (!empty($data->anuncio)) : ?>
								<p><?= $data->anuncio ?></p>
							<?php endif; ?>

							<?php if (!empty($v[0]->telefono)) : ?>
								<a target="blank" href="whatsapp://send?phone=57<?= $v[0]->telefono ?>" class="whatsapp">
									<img src="https://markoh.myappi.net/wp-content/uploads/2021/05/whatsapp_icon.png">
									<span><?= $v[0]->telefono ?></span>
								</a>
							<?php endif; ?>
							<div class="actions">
								<?php if ($v[0]->autor != $user->ID) : ?>
									<a class="button primary is-xsmall pedir" id="btn-convocar" data-id="<?= $v[0]->id ?>" data-post='<?= json_encode($data) ?>' data-anuncio='<?= json_encode($v[0]) ?>'> <span>¡Convocar!</span> </a>
								<?php endif; ?>
								<?php if ($v[0]->autor == $user->ID) : ?>
									<a href="#" class="button alert is-xsmall delete" id="btn-eliminar" data-id="<?= $v[0]->id ?>"><i class="fa fa-trash"></i> Eliminar</a>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
				<?php break; ?>


			<?php
			case 'encargado_cancha': ?>
				<?php foreach ($noticias as $a_nec) : ?>
					<div clas="noticia_encargado_cancha">
						<?php
						/* $b_nec = json_decode($a_nec->post_content);
						$c_nec = json_decode(stripslashes($b_nec->post_content)); */
						/* $cap_visitante = $nec->post_author;
						$cap_local = $d_nec->post_author; */
						/* var_dump($a_nec);
						var_dump($b_nec);*/
						?>

					</div>
				<?php endforeach; ?>
				<?php break; ?>


			<?php
			case 'administrator': ?>
				<?php foreach ($noticias as $v) : ?>
					<?php $data = json_decode(stripslashes($v[0]->json_data)) ?>
					<div class="noticia_administrador">
						<?php if ($data->tipo_publicacion == 'equipo') : ?>
							<h3>¡<?= strtoupper($data->equipo->nombre) ?> busca Match!</h3>
							<small><?= ucwords($v[0]->fecha) ?></small>
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
									Publicado por: <?= $v[0]->nombre . ' ' . $v[0]->apellido ?><br>
									<?= isset($v[1]->nombre) ? "Sitio de juego: " .  $v[1]->nombre : null ?>
								</p>
							</div>
							<?php if (!empty($v[0]->telefono)) : ?>
								<a target="blank" href="whatsapp://send?phone=57<?= $v[0]->telefono ?>" class="whatsapp">
									<img src="https://markoh.myappi.net/wp-content/uploads/2021/05/whatsapp_icon.png">
									<span><?= $v[0]->telefono ?></span>
								</a>
							<?php endif; ?>

						<?php else : ?>
							<h3>¡<?= $v[0]->nombre . ' ' . $v[0]->apellido ?> quiere jugar como <span><?= ucfirst($data->posicion) ?></span>!</h3>
							<small><?= ucwords($v[0]->fecha) ?></small><br>
							<i></i>
							<p>Ciudad: <?= $data->ciudad ?></p>
							<p>Lugar: <?= $data->lugar ?></p>

							<?php if (!empty($data->anuncio)) : ?>
								<p><?= $data->anuncio ?></p>
							<?php endif; ?>

							<?php if (!empty($v[0]->telefono)) : ?>
								<a target="blank" href="whatsapp://send?phone=57<?= $v[0]->telefono ?>" class="whatsapp">
									<img src="https://markoh.myappi.net/wp-content/uploads/2021/05/whatsapp_icon.png">
									<span><?= $v[0]->telefono ?></span>
								</a>
							<?php endif; ?>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
				<?php break; ?>


			<?php
			default: ?>
				<em>Error en el switch al mostrar datos con el case de administrador</em>
				<?php break; ?>
		<?php endswitch; ?>

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
		margin-top: 15px;
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

	.noticia_jugador h3 span,
	.noticia_administrador h3 span {
		color: rgb(33, 158, 168);
	}

	.noticia_jugador>i,
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

	.noticia_jugador h3,
	.noticia_administrador h3 {
		font-size: 1.2em;
		margin: 0;
	}

	.noticia_jugador p,
	.noticia_administrador p {
		margin-bottom: 0;
		line-height: 17px;
	}

	.noticia_jugador,
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

	.mainModal {
		position: absolute;
		width: 100%;
		height: 100vh;
		background: rgb(0, 0, 0, 0.81);
		display: none;
		z-index: 999;
		margin-left: -10px;
		margin-top: -80px;
		padding: 15%;
		padding-left: 20%;
		padding-right: 20%;
	}

	.childModal {
		/* width: 100%;
		height: 100%; */
		/* display: -webkit-inline-flex;
		display: -moz-inline-flex;
		display: -ms-inline-flex;
		display: -o-inline-flex;
		display: inline-flex; */
		background-color: #FFF;
		border-radius: 15px;
	}

	.buttonCloseModal {
		display: flex;
		justify-content: flex-end;
	}

	.buttonCloseModal i {
		background-color: #004454;
		padding: 8px;
		padding-left: 10px;
		padding-right: 10px;
		border-radius: 20px;
		margin-top: -10px;
		margin-right: -10px;
		color: #FFF;
	}

	.buttonCloseModal i:hover {
		background-color: grey;
	}

	.containerFormModal {
		margin: 3%;
		margin-top: 0px;
	}

	.containerFormModal h3 {
		text-align: center;
	}

	.formModal {
		justify-self: center;
		align-self: center;
	}

	#estilo_aux_tipo_futbol {
		margin-top: -15px;
		margin-bottom: 10px;
	}

	#textAreaModal {
		resize: none;
	}

	#buttonSaveInfo {
		text-transform: capitalize;
		background-color: #004454;
		color: #FFF;
		border-radius: 5px;
		width: 100%;
	}
</style>
<?php add_action('wp_footer', function () {
	global $user;
	global $wpdb;
?>
	<script>
		jQuery(function($) {
			$('.delete').on('click', function(e) {
				e.preventDefault();
				var id = $(this).data('id')

				if (confirm("¿Está seguro que quiere eliminar ésta invitación?")) {
					$.post('/wp-admin/admin-ajax.php?action=custom_ajax&caction=delete_post', {
							"user_id": <?= $user->ID ?>,
							"id": id
						},
						function(r) {
							alert(r.message);
							if (r.success) {
								window.location.reload();
							}
						}, "json")
				}
			});

			$('.pedir').on('click', function(e) {
				e.preventDefault();
				var post = $(this).data('post')
				var anuncio = $(this).data('anuncio')

				if (confirm("¿Está seguro que quiere pedir a este jugador?")) {
					$.post('/wp-admin/admin-ajax.php?action=custom_ajax&caction=pedir_jugador', {
							"user_id": <?= $user->ID ?>,
							"post_id": anuncio.id
						},
						function(r) {
							alert(r.message);
							if (r.success) {
								window.location.href = "/notifications";
							}
						}, "json")
				}
			});

			$('.aceptar_reto').on('click', function(e) {
				e.preventDefault();
				var post = $(this).data('post')
				var anuncio = $(this).data('anuncio')

				if (confirm("¿Está seguro que quiere aceptar el reto de " + anuncio.nombre + " para jugar el " + anuncio.fecha + "?")) {
					$('.mainModal').fadeIn(); //Abre ventana modal

					$('.formModal').submit(function(e) {
						e.preventDefault();
						var data = $(this).serializeArray();

						var tipo_futbol_anuncio = post.equipo.tipo, //tipo de futbol del equipo del anuncio
							tipo_futbol_mi_equipo = null;

						//Saca el tipo de futbol de mi equipo seleccionado
						let tipo_futbol = <?= json_encode($wpdb->get_results("SELECT * FROM wp_teams WHERE creado_por = " . $user->ID)) ?>;
						tipo_futbol.forEach(e => {
							if (e.id == $('#equipo_s').val()) {
								tipo_futbol_mi_equipo = e.tipo;
							}
						});

						//Se realiza validación si el tipo de futbol de del equipo seleccionado en la ventana modal es el mismo en el anuncio
						if (tipo_futbol_anuncio != tipo_futbol_mi_equipo) {
							//Muestra el mensaje por un tiempo determinado definido en "setTimeout"
							$("#aux_tipo_futbol").html("<div id='estilo_aux_tipo_futbol'>El equipo seleccionado juega un tipo diferente de futbol al de el anuncio</div>");

							setTimeout(() => {
								$("#aux_tipo_futbol").html(null);
							}, 5000);
						} else {
							$.post('/wp-admin/admin-ajax.php?action=custom_ajax&caction=aceptar_reto_equipo', {
									"user_id": <?= $user->ID ?>,
									"post_id": anuncio.id,
									"rival": data[0].value,
									"datos_otra_cancha": data[1].value
								},
								function(r) {
									alert(r.message);

									if (r.success) {
										$('.mainModal').fadeOut(); //Cierra ventana modal	
										window.location.href = "/notifications";
									}
								},
								"json"
							);
						}
					});
				}
			});

			$('.buttonCloseModal').click(function(e) {
				e.preventDefault();

				$('.mainModal').fadeOut();
			})

			//Acción que se realiza cuando el encargado de cancha agenda un partido

		})
	</script>
<?php }); ?>
<?php
global $user;
global $meta;
$profile_picture = (isset($meta->profile_picture[0])) ? $meta->profile_picture[0] : 'https://markoh.myappi.net/wp-content/uploads/2021/05/user_avatar.png';
?>
<div class="fake_header">
	<a href="/start" class="icon left"><i class="fa fa-chevron-left"></i></a>
	<h2 class="title">Perfil del Jugador</h2>
</div>
<div class="fake_body">
	<div class="badge">
		<div class="image_container" style="background-image:url(<?= $profile_picture ?>);">
			<input type="file" class="image" accept='.jpg,.jpeg,.png, .gif'>
		</div>
		<div class="stats top" style="
				position: absolute;
				top: 55px;
				color: #312601;
				left: 28px;">
			<div style="
				font-weight: bold;
				font-size: 31px;
				line-height: 33px;
			"><?= @$meta->stats_rw[0] ?></div>
			<div style="
				font-size: 20px;
				line-height: 17px;
			">VEL</div>

		</div>
		<div class="nombre"><?= @$meta->nombre[0] ?></div>
		<table class="first">
			<tr>
				<td><?= @$meta->stats_pac[0] ?></td>
				<td>PASE</td>
			</tr>
			<tr>
				<td><?= @$meta->stats_sho[0] ?></td>
				<td>REGATE</td>
			</tr>
			<tr>
				<td><?= @$meta->stats_pas[0] ?></td>
				<td>VISION</td>
			</tr>
		</table>
		<table class="second">
			<tr>
				<td><?= @$meta->stats_dri[0] ?></td>
				<td>FUERZA</td>
			</tr>
			<tr>
				<td><?= @$meta->stats_def[0] ?></td>
				<td>DEFENSA</td>
			</tr>
			<tr>
				<td><?= @$meta->stats_phy[0] ?></td>
				<td>FÍSICO</td>
			</tr>
		</table>
	</div>
	<?php  ?>
	<h2 class="text-center">Editar Perfil</h2>
	<div class="login_form register">
		<form id="form_register">

			<input type="text" name="nombre" value="<?= @$meta->nombre[0] ?>" required placeholder="Nombre">
			<input type="text" name="apellido" value="<?= @$meta->apellido[0] ?>" required placeholder="Apellido">
			<input type="text" name="telefono" value="<?= @$meta->telefono[0] ?>" required placeholder="Teléfono">
			<select name="posicion" required>
				<option value="">¿Qué posición juegas?</option>
				<option <?= (@$meta->posicion[0] == 'Delantero') ? 'selected' : '' ?>>Delantero</option>
				<option <?= (@$meta->posicion[0] == 'Defensa') ? 'selected' : '' ?>>Defensa</option>
				<option <?= (@$meta->posicion[0] == 'Medio Campo') ? 'selected' : '' ?>>Medio Campo</option>
				<option <?= (@$meta->posicion[0] == 'Portero') ? 'selected' : '' ?>>Portero</option>
			</select>
			<hr>
			<h3>Estadísticas Jugador</h3>
			<table width="100%">

				<tr>
					<td width="100px">VELOCIDAD</td>
					<td><input type="number" required value="<?= @$meta->stats_rw[0] ?>" name="stats_rw"></td>
				</tr>
				<tr>
					<td width="100px">PASE</td>
					<td><input type="number" required value="<?= @$meta->stats_pac[0] ?>" name="stats_pac"></td>
				</tr>
				<tr>
					<td width="100px">REGATE</td>
					<td><input type="number" required value="<?= @$meta->stats_sho[0] ?>" name="stats_sho"></td>
				</tr>
				<tr>
					<td width="100px">VISION</td>
					<td><input type="number" required value="<?= @$meta->stats_pas[0] ?>" name="stats_pas"></td>
				</tr>
				<tr>
					<td width="100px">FUERZA</td>
					<td><input type="number" required value="<?= @$meta->stats_dri[0] ?>" name="stats_dri"></td>
				</tr>
				<tr>
					<td width="100px">DEFENSA</td>
					<td><input type="number" required value="<?= @$meta->stats_def[0] ?>" name="stats_def"></td>
				</tr>
				<tr>
					<td width="100px">FISICO</td>
					<td><input type="number" required value="<?= @$meta->stats_phy[0] ?>" name="stats_phy"></td>
				</tr>
				<!-- -https://www.fifplay.com/encyclopedia/player-attribute-pace/ -->
			</table>
			<hr>

			<p><button type="submit">Guardar Datos</button></p>
		</form>
	</div>
</div>
<?php include 'footer.php' ?>
<?php add_action('wp_footer', function () { ?>
	<style>
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
			z-index: 9999;
		}

		.badge .nombre {
			position: absolute;
			top: 139px;
			left: 0;
			width: 100%;
			font-size: 20px;
			text-transform: uppercase;
			color: #312601;
			text-align: center;
		}

		.badge table tr td {
			border: 0 !important;
			padding: 2px !important;
			color: #312601;
			font-size: 14px;
			line-height: 18px;
		}

		.badge table {
			width: 65px;
			position: absolute;
			top: 170px;
		}

		.badge table.first {
			left: 12px;
		}

		.badge table:last-child {
			right: 35px;
		}

		.text-center {
			text-align: center;
		}

		.badge .image_container .image {
			height: 100%;
			width: 100%;
			opacity: 0;
		}

		.badge .image_container {
			width: 90px;
			height: 90px;
			background-color: #eee;
			border-radius: 500px;
			overflow: hidden;
			background-position: top center;
			background-size: contain;
			position: absolute;
			top: 40px;
			left: 85px;
			margin: 0 auto;
		}

		.badge {
			pointer-events: all;
			padding: 0 20px;
			justify-content: center;
			flex-direction: column;
			align-items: center;
			margin-bottom: 20px;
			background-image: url(https://markoh.myappi.net/wp-content/uploads/2021/05/gold-shield.png);
			background-position: top center;
			background-repeat: no-repeat;
			height: 300px;
			width: 200px;
			position: relative;
			margin: 0 auto 20px auto;
		}
	</style>
	<script>
		jQuery(function($) {
			$('form#form_register').on('submit', function(e) {
				e.preventDefault()
				var data = $(this).serialize();
				$.post('/wp-admin/admin-ajax.php?action=custom_ajax&caction=update_userdata',
					data,
					function(r) {
						alert(r.message);
						window.location.reload();
					}, "json")

				return false;

			});

			$(".image").change(function(e) {
				e.preventDefault();
				var val = $(this).val();
				if (val != '') {
					readURL(this);
					/*******************************/

					var form_data = new FormData();
					var ref = $(this);
					var original = $('.image_container').css("background-image");

					form_data.append("files[]", document.querySelector('.image').files[0]);
					form_data.append('action', 'custom_ajax');
					form_data.append('caction', 'change_profile_picture');

					// AJAX request
					$.ajax({
						url: '/wp-admin/admin-ajax.php',
						type: 'post',
						data: form_data,
						dataType: 'json',
						contentType: false,
						processData: false,
						error: function(a, b, c) {
							alert('Ha ocurrido un error inesperado. Intente más tarde o contacte al servicio técnico.')
							$('.image_container').css("background-image", original);
							ref.val("")

						},
						success: function(r) {
							alert(r.message);
							ref.val("")
							if (!r.success) {
								$('.image_container').css("background-image", original);
							}
						},
						complete: function() {}
					});


					/*******************************/
				}
			});
		})

		function readURL(input) {
			if (input.files && input.files[0]) {
				var reader = new FileReader();

				reader.onload = function(e) {
					jQuery('.image_container').css('background-image', "url(" + e.target.result + ")");
				}

				reader.readAsDataURL(input.files[0]);
			}
		}
	</script>
<?php }); ?>
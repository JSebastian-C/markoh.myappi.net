<?php
global $user;
global $meta;
global $wpdb;
$url = 'https://markoh.myappi.net/wp-content/uploads/2021/05/markoh-image-banner-1.png';

$query =   'select 
					u.ID id, 
					um4.meta_value profile_picture,
					um1.meta_value nombre,
					um2.meta_value apellido,
					um3.meta_value posicion
				from 
					wp_users u
				LEFT JOIN wp_usermeta um1 ON um1.user_id =  u.ID AND um1.meta_key = "nombre"
				LEFT JOIN wp_usermeta um2 ON um2.user_id =  u.ID AND um2.meta_key = "apellido"
				LEFT JOIN wp_usermeta um3 ON um3.user_id =  u.ID AND um3.meta_key = "posicion"
				LEFT JOIN wp_usermeta um4 ON um4.user_id =  u.ID AND um4.meta_key = "profile_picture"
				INNER JOIN wp_usermeta um5 ON um5.user_id = u.ID AND um5.meta_value like "%jugador%"
				WHERE u.ID <> ' . $user->ID;

$usuarios = $wpdb->get_results($query);

?>

<div class="fake_header">
	<a href="/my_courts" class="icon left"><i class="fa fa-chevron-left"></i></a>
	<a hidden href="/new_post" class="icon right"><i class="fa fa-plus"></i></a>
	<h2 class="title">Nueva Cancha</h2>
</div>
<div class="fake_body">
	<div class="login_form register">
		<form id="form_register">

			<div class="image_container" style="background-image:url(<?= $url ?>);">
				<input type="file" accept=".jpg, .jpeg, .png, .gif" />
			</div>
			<br>
			<input type="text" name="nombre" required placeholder="Nombre">
			<br>
			<br>
			<input type="text" name="direccion" required placeholder="Dirección">
			<br>
			<br>
			<input type="text" name="ciudad" required placeholder="Ciudad">
			<br>
			<br>
			<input type="text" name="horario" required placeholder="Horario. Ejem: 8:00 pm - 9:00 pm">
			<br>
			<br>
			<input type="number" name="telefono" required placeholder="Teléfono. Ejem: 3001234567">
			<br>
			<br>

			<p><button type="submit">Registrar Cancha</button></p>
		</form>
	</div>
</div>
<?php include 'footer.php' ?>
<style>
	.table_seleccionados tr:first-child a {
		display: none;
	}

	.invitar {
		margin: 0 0 0 10px !important;
		background: #0d98c7;
		color: white;
		padding: 1px 7px;
		border-radius: 4px;
		margin-left: 10px;
	}

	.image_container input[type='file'] {
		width: 100%;
		height: 100% !important;
		opacity: 0;
	}

	.image_container {
		width: 100%;
		height: 120px;
		background-color: #eee;
		border-radius: 5px;
		overflow: hidden;
		background-position: center center;
		background-size: cover;
		margin: 0 auto;
		position: relative;
	}

	.image_container::before {
		content: "Seleccionar Imagen";
		color: black;
		background: white;
		display: inline-block;
		padding: 0px 9px;
		position: absolute;
		bottom: 0;
		right: 0;
		border-top-left-radius: 3px;
		line-height: 20px;
		font-size: 12px;
		opacity: .7;
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
		z-index: 999;
	}

	.invitar_box {
		display: none;
		z-index: 3;
		position: absolute;
		top: 54px;
		left: 0;
		width: 100vw;
		height: 100vh;
		background: white;
	}

	.invitar_box>.header {
		height: 70px;
		background: #004454;
		padding: 10px;
		display: flex;
		align-items: center;
		justify-content: space-between;
		width: 100%;
	}

	.invitar_box>.header>.buscar {
		width: 70%;
		padding: 10px !important;
		border-radius: 2px;
		border: 0;
		margin: 0;
	}

	.invitar_box>.header>.ok {
		display: inline-block;
		width: 40px;
		height: 40px;
		background: white;
		line-height: 30px;
		text-align: center;
		border-radius: 25px;
		margin: 0;
	}

	.invitar_box>.header>.ok>i {
		font-size: 20px;
		margin-top: 10px;
	}

	.invitar_box>.body {
		background: rgba(255, 255, 255, .7);
		height: calc(100vh - 70px);
		padding: 20px;
		overflow: auto;
	}
</style>
<div class="invitar_box">
	<div class="header">
		<input class="buscar" placeholder="Buscar">
		<a href="#" class="ok">
			<i class="fa fa-check"></i></a>
	</div>
	<div class="body">
		<table class="table_users">
			<tbody>
				<?php foreach ($usuarios as $v) : ?>
					<tr>
						<td width="50px" style="padding-left:10px;">
							<img src="<?= empty($v->profile_picture) ? 'https://markoh.myappi.net/wp-content/uploads/2021/05/user_avatar.png' : $v->profile_picture ?>" width="40px">
						</td>
						<td width=""><?= $v->nombre . ' ' . $v->apellido ?></td>
						<td width="30px">
							<input type="checkbox" style="height: 20px;width: 20px;" data-json='<?= json_encode($v) ?>'>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

	</div>
</div>
<?php add_action('wp_footer', function () { ?>
	<script>
		jQuery(function($) {
			$("input[type='file']").on('change', function(e) {
				var input = this;
				var url = $(this).val();
				var ext = url.substring(url.lastIndexOf('.') + 1).toLowerCase();
				$('.image_container').css('opacity', ".6");
				if (input.files && input.files[0] && (ext == "gif" || ext == "png" || ext == "jpeg" || ext == "jpg")) {
					var reader = new FileReader();

					reader.onload = function(e) {
						$('.image_container').css('background-image', "url(" + e.target.result + ")");
						$('.image_container').css('opacity', "1");
					}
					reader.readAsDataURL(input.files[0]);
				} else {
					$('.image_container').css('opacity', "1");
				}
			})
			$('form#form_register').on('submit', function(e) {
				e.preventDefault()

				var form_data = new FormData();

				var totalfiles = document.querySelector('[type="file"]').files.length;
				for (var index = 0; index < totalfiles; index++) {
					form_data.append("files[]", document.querySelector('[type="file"]').files[index]);
				}

				$("form#form_register").serializeArray().forEach(function(field) {
					form_data.append(field.name, field.value);
				})


				//$("#form_register").css("opacity",.5); 
				// AJAX request
				$.ajax({
					url: '/wp-admin/admin-ajax.php?action=custom_ajax&caction=new_court',
					type: 'post',
					data: form_data,
					dataType: 'json',
					contentType: false,
					processData: false,
					error: function(a, b, c) {
						alert('Ha ocurrido un error inesperado. Intente más tarde o contacte al servicio técnico.')
					},
					success: function(r) {
						alert(r.message);
						if (r.success)
							window.location.href = '/my_courts';
					},
					complete: function() {
						$("#form_register").css("opacity", 1);
						$("#form_register").find("button[type='submit']").prop("disabled", false);
					}
				});
				return false;
			});
		})
	</script>
<?php }); ?>
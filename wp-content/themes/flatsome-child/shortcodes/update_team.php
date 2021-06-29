<?php
global $user;
global $meta;
global $wpdb;

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
$team = $wpdb->get_row("select * from wp_teams where id={$_GET['id']}");
$url = $team->logo_url;

//Proceso para mostrar los jugadores del equipo exceptuando el que creó el equipo
$playersPerTeam = $wpdb->get_results("SELECT id_player FROM wp_team_players WHERE id_team = " . $_GET['id'] . " AND id_player <> " . $user->ID);
for ($i = 0; $i < count($playersPerTeam); $i++) {
	$query =   'select 
					u.ID id, 
					um4.meta_value profile_picture,
					um1.meta_value nombre,
					um2.meta_value apellido,
					um3.meta_value posicion
				from 
					wp_users u
				INNER JOIN 	wp_team_players tp ON tp.id_player = u.id
				INNER JOIN 	wp_teams t ON t.id = tp.id_team
				LEFT JOIN wp_usermeta um1 ON um1.user_id =  u.ID AND um1.meta_key = "nombre"
				LEFT JOIN wp_usermeta um2 ON um2.user_id =  u.ID AND um2.meta_key = "apellido"
				LEFT JOIN wp_usermeta um3 ON um3.user_id =  u.ID AND um3.meta_key = "posicion"
				LEFT JOIN wp_usermeta um4 ON um4.user_id =  u.ID AND um4.meta_key = "profile_picture"
				INNER JOIN wp_usermeta um5 ON um5.user_id = u.ID AND um5.meta_value like "%jugador%"
				WHERE id_player = ' . $playersPerTeam[$i]->id_player;

	$team_members[$i] = $wpdb->get_results($query);
}
?>

<div class="fake_header">
	<a href="/my_team" class="icon left"><i class="fa fa-chevron-left"></i></a>
	<a hidden href="/new_post" class="icon right"><i class="fa fa-plus"></i></a>
	<h2 class="title">Modificar Equipo</h2>
</div>
<div class="fake_body">
	<div class="login_form register">
		<form id="form_register">

			<div class="image_container" style="background-image:url(<?= $url ?>);">
				<input type="file" accept=".jpg, .jpeg, .png, .gif" />
			</div>
			<br>
			<input type="text" name="nombre" required placeholder="Nombre del Equipo" value="<?= $team->nombre ?>">
			<br>
			<br>
			<textarea name="descripcion" maxlength='250' required placeholder="Descripción"><?= htmlspecialchars($team->descripcion) ?></textarea>
			<br>
			<select name="tipo" required>
				<option value="">Tipo de Futbol</option>
				<option value="5" <?= $team->tipo == '5'  ? 'selected' : '' ?>>Futbol 5</option>
				<option value="9" <?= $team->tipo == '9'  ? 'selected' : '' ?>>Futbol 9</option>
				<option value="11" <?= $team->tipo == '11' ? 'selected' : '' ?>>Futbol 11</option>
			</select>
			<div style="display:flex;margin: 20px 0;">
				<b>Participantes</b>
				<a href="#" class="invitar">Invitar</a>
			</div>
			<table class="table_seleccionados">
				<tr data-id="<?= $user->ID ?>">
					<td width="40px"><img src="<?= empty($meta->profile_picture[0]) ? 'https://markoh.myappi.net/wp-content/uploads/2021/05/user_avatar.png' : $meta->profile_picture[0] ?>" width="40px" /></td>
					<td width=""><?= $meta->nombre[0] . ' ' . $meta->apellido[0] ?></td>
					<td width="30px"><a href="#" class="delete_user" style="color:#cb0505;font-size:25px;"><i class="fa fa-times-circle"></i></a></td>
				</tr>

				<?php if ($team_members) : ?>
					<?php foreach ($team_members as $v) : ?>
						<tr data-id="<?= $v[0]->id ?>">
							<td width="40px"><img src="<?= empty($v[0]->profile_picture) ? 'https://markoh.myappi.net/wp-content/uploads/2021/05/user_avatar.png' : $v[0]->profile_picture ?>" width="40px" /></td>
							<td width=""><?= $v[0]->nombre . ' ' . $v[0]->apellido ?></td>
							<td width="30px"><a href="#" class="delete_user" style="color:#cb0505;font-size:25px;"><i class="fa fa-times-circle"></i></a></td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</table>
			<p><button type="submit">Guardar Datos</button></p>
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
		width: 100px;
		height: 100px;
		background-color: #eee;
		border-radius: 500px;
		overflow: hidden;
		background-position: top center;
		background-size: contain;
		margin: 0 auto;
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
	}

	.detalles {
		display: none;
		z-index: 5;
		position: absolute;
		top: 54px;
		left: 0;
		width: 100vw;
		height: 100vh;
		background: white;
	}

	.detalles>.header .ok {
		color: white;
		padding: 10px;
		font-size: 20px;
	}

	.detalles>.header {
		height: 70px;
		background: #004454;
		padding: 10px;
		display: flex;
		align-items: center;
		justify-content: space-between;
		width: 100%;
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

	.view_player {
		font-size: 20px;
		color: blue;
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
		margin: 17vh auto 20px auto;
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
					<tr data-id='<?= $v->id ?>'>
						<td width="50px" style="padding-left:10px;">
							<img src="<?= empty($v->profile_picture) ? 'https://markoh.myappi.net/wp-content/uploads/2021/05/user_avatar.png' : $v->profile_picture ?>" width="40px">
						</td>
						<td width=""><?= $v->nombre . ' ' . $v->apellido ?></td>
						<td width="30px">
							<a href="#" class="view_player" data-id='<?= $v->id ?>'><i class="fa fa-info-circle"></i></a>
						</td>
						<td width="30px">
							<input type="checkbox" style="height: 20px;width: 20px;" data-json='<?= json_encode($v) ?>'>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

	</div>
</div>
<div class="detalles">
	<div class="header">
		<a href="#" class="ok">
			<i class="fa fa-chevron-left"></i></a>
		<h3 style="
    color: white;
    margin-top: 7px;
    margin-left: 7%;
    font-size: 20px;
">Detalles del Jugador</h3>
	</div>
	<div class="body" style="    background: 50% 40% no-repeat;
    height: 100vh;">
		<div class="badge">
			<div class="image_container player_data profile_picture">
				<input type="file" class="image" accept='.jpg,.jpeg,.png, .gif'>
			</div>
			<div class="stats top" style="
				position: absolute;
				top: 55px;
				color: #312601;
				left: 28px;">
				<div class="player_data rw" style="
				font-weight: bold;
				font-size: 31px;
				line-height: 33px;
			"><?= @$meta->stats_rw ?></div>
				<div style="
				font-size: 20px;
				line-height: 17px;
			">VEL</div>

			</div>
			<div class="nombre player_data"><?= @$meta->nombre ?></div>
			<table class="first">
				<tr>
					<td class="player_data pac"><?= @$meta->stats_pac ?></td>
					<td>PASE</td>
				</tr>
				<tr>
					<td class="player_data sho"><?= @$meta->stats_sho ?></td>
					<td>REGATE</td>
				</tr>
				<tr>
					<td class="player_data pas"><?= @$meta->stats_pas ?></td>
					<td>VISION</td>
				</tr>
			</table>
			<table class="second">
				<tr>
					<td class="player_data dri"><?= @$meta->stats_dri ?></td>
					<td>FUERZA</td>
				</tr>
				<tr>
					<td class="player_data def"><?= @$meta->stats_def ?></td>
					<td>DEFENSA</td>
				</tr>
				<tr>
					<td class="player_data phy"><?= @$meta->stats_phy ?></td>
					<td>FÍSICO</td>
				</tr>
			</table>
		</div>
	</div>
</div>
<?php add_action('wp_footer', function () { ?>
	<script>
		jQuery.expr[':'].contains = function(a, i, m) {
			return jQuery(a).text().toUpperCase()
				.indexOf(m[3].toUpperCase()) >= 0;
		};
		jQuery(function($) {
			$('.detalles .ok').on('click', function(e) {
				e.preventDefault()
				$('.detalles').fadeOut()
			});
			$('.view_player').on('click', function(e) {
				e.preventDefault()
				$('.detalles').css('top', $(document).scrollTop() + 'px')
				$('.detalles .body').css('background-image', 'url(https://markoh.myappi.net/wp-content/uploads/2021/05/loading-buffering.gif)')
				$('.detalles .badge').hide()
				$('.detalles').fadeIn()
				var id = $(this).data("id")
				$.post(
					'/wp-admin/admin-ajax.php?action=custom_ajax&id_player=' + id + '&caction=get_player', null,
					function(r) {
						console.info(r.data.profile_picture)
						if (typeof r.data.profile_picture != 'undefined')
							$(".player_data.profile_picture").css('background-image', 'url(' + r.data.profile_picture + ')')
						else
							$(".player_data.profile_picture").css('background-image', 'url(https://markoh.myappi.net/wp-content/uploads/2021/05/user_avatar.png)')

						$(".player_data.nombre").html(r.data.nombre);
						$(".player_data.rw").html(r.data.stats_rw);
						$(".player_data.def").html(r.data.stats_def);
						$(".player_data.dri").html(r.data.stats_dri);
						$(".player_data.pas").html(r.data.stats_pas);
						$(".player_data.pac").html(r.data.stats_pac);
						$(".player_data.phy").html(r.data.stats_phy);
						$(".player_data.sho").html(r.data.stats_sho);

						$('.detalles  .body').css('background-image', '')
						$('.detalles .badge').fadeIn()
					},
					'json'
				);
			});
			$('body').on('click', '.delete_user', function(e) {
				e.preventDefault()
				$(this).parents('tr').remove()
			});
			$('.table_users tr input[type="checkbox"]').on('click', function() {
				if ($(this).is(":checked"))
					$(this).parents('tr').css("background-color", '#ffff8d')
				else
					$(this).parents('tr').css("background-color", 'transparent')
			})




			$(".table_seleccionados tr").each(function() {
				var id = $(this).data("id");
				$('.table_users tr[data-id="' + id + '"] input').trigger('click');
			})

			$(".invitar_box .buscar").on('keyup', function(e) {
				var v = $(this).val()
				if (v == '') {
					$('.table_users tr').show()
				} else {
					$('.table_users tr').hide()
					$('.table_users tr:contains(' + v + ')').show()
				}
			})
			$(".invitar").on('click', function(e) {
				e.preventDefault()
				$(".invitar_box").show();
				$(".invitar_box").css('top', $(window).scrollTop() + "px");
				$("html").css('overflow', 'hidden');
			});
			$(".invitar_box>.header>.ok").on('click', function(e) {
				e.preventDefault()

				// usuarios marcados
				jQuery(".table_seleccionados tr").not(':first-child').remove();
				$(".table_users input[type='checkbox']:checked").each(function() {
					var clone = $(".table_seleccionados tr:first-child").clone();
					var json = $(this).data('json');
					clone.attr("data-id", json.id);
					var img = (json.profile_picture == null || json.profile_picture == '' || typeof json.profile_picture == 'undefined') ? 'https://markoh.myappi.net/wp-content/uploads/2021/05/user_avatar.png' : json.profile_picture;
					clone.find("img").attr("src", json.profile_picture);
					clone.find("td:nth-child(2)").html(json.nombre + ' ' + json.apellido);
					clone.appendTo('.table_seleccionados');
				});
				$("html").css('overflow', 'auto');
				jQuery(document).scrollTo(10000000)
				$(".invitar_box").hide();
			});
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

				var usuarios = [];
				jQuery(".table_seleccionados tr").each(function() {
					usuarios.push($(this).attr('data-id'));

				})
				form_data.append("usuarios", usuarios.join());

				//$("#form_register").css("opacity",.5); 
				// AJAX request
				$.ajax({
					url: '/wp-admin/admin-ajax.php?action=custom_ajax&id_team=<?= $_GET["id"] ?>&caction=update_team',
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
							window.location.href = '/my_team';
					},
					complete: function() {
						$("#form_register").css("opacity", 1);
						$("#form_register").find("button[type='submit']").prop("disabled", false);
					}
				});
				return false;
			});
			<?php if (isset($_GET['invite'])) : ?>
				$(".invitar").trigger('click');
			<?php endif; ?>
		})
	</script>
<?php }); ?>
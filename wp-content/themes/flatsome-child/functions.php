<?php
static $navigation = array();

function custom_ajax()
{
	global $wpdb;
	global $navigation;
	$response = ["success" => false];

	if (isset($_REQUEST["caction"])) {
		switch ($_REQUEST["caction"]) {
			case 'get_player':
				$m = get_user_meta($_REQUEST['id_player']);
				$meta = new stdClass();
				foreach ($m as $k => $v) {
					$meta->$k = $v[0];
				}
				$response = ['success' => true, 'data' => $meta];

				break;
			case 'manage_courts':
				$user = wp_get_current_user();

				$gestiona_canchas = $wpdb->get_var("select count(1) from wp_usermeta where user_id={$user->ID} and meta_key='wp_capabilities' and meta_value not like '%jugador%'");
				$response = ['success' => (bool) $gestiona_canchas];
				break;
			case 'logout':
				wp_logout();
				wp_destroy_current_session();
				wp_clear_auth_cookie();
				$response = ['success' => true, 'message' => "Sesión cerrada exitosamente."];
				break;
			case 'delete_notification':
				$wpdb->delete("wp_posts", ['id' => $_REQUEST['id']]);
				$response = ['success' => true, 'message' => "Notificación eliminada exitosamente."];
				break;
			case 'delete_post':
				$wpdb->delete("wp_posts", ['id' => $_REQUEST['id']]);
				$response = ['success' => true, 'message' => "Invitación eliminada exitosamente."];
				break;
			case 'pedir_jugador':
				$sql = "
						select 
						(
							select
								meta_value
							from
								wp_usermeta
							where
								user_id = {$_REQUEST['user_id']} and 
								meta_key = 'nombre'
						) nombre,
						(
							select
								meta_value
							from
								wp_usermeta
							where
								user_id = {$_REQUEST['user_id']} and 
								meta_key = 'apellido'
						) apellido,
						(
							select
								meta_value
							from
								wp_usermeta
							where
								user_id = {$_REQUEST['user_id']} and 
								meta_key = 'telefono'
						) telefono
						";
				// usuario que aceptó la publicacion
				$usuario = $wpdb->get_row($sql);

				// publicacion
				$post = $wpdb->get_row("select * from wp_posts where id = {$_REQUEST['post_id']}");

				// datos json de la publicacion
				$json = json_decode($post->post_content);

				// guardamos el usuario que está aceptando la publicacion, dentro de la publicacion
				$json->respuesta = $usuario;
				$update = [
					'post_status'   => 'pending',
					'post_content'   => json_encode($json)
				];
				// actualizamos la publicacion
				$wpdb->update("wp_posts", $update, ['id' => $_REQUEST['post_id']]);

				// mensaje de la notificacion para el usuario que publicó su disponibilidad
				$mensaje =  $usuario->nombre . ' ' . $usuario->apellido . " te ha pedido para que juegues con él.";
				$post->telefono = $usuario->telefono;
				$insert = [
					'post_author'   => $post->post_author,
					'post_title'   => "¡Te han pedido para jugar!",
					'post_excerpt'   => $mensaje,
					'post_content'   => json_encode($post),
					'post_type'   => "markoh_notification",
				];

				// se guarda notificacion para el usuario que hizo la notificacion
				$wpdb->insert("wp_posts", $insert);

				$telefono = $wpdb->get_var("select meta_value from wp_usermeta where meta_key='telefono' and user_id={$post->post_author}");
				$nombre = $wpdb->get_var("select meta_value from wp_usermeta where meta_key='nombre' and user_id={$post->post_author}");
				$apellido = $wpdb->get_var("select meta_value from wp_usermeta where meta_key='apellido' and user_id={$post->post_author}");
				$post->telefono = $telefono;
				$insert = [
					'post_author'   => $_REQUEST['user_id'],
					'post_title'   => "¡Has pedido un jugador disponible!",
					'post_excerpt'   => "Usted ha aceptado una solicitud de <b>$nombre $apellido</b> para jugar",
					'post_content'   => json_encode($post),
					'post_type'   => "markoh_notification",
				];
				$wpdb->insert("wp_posts", $insert);

				$response = ['success' => true, 'message' => "EXITO:\nSe le ha enviado una notificacion a ambas partes para que finalicen los detalles del encuentro"];
				break;
			case 'aceptar_reto_equipo':
				$sql = "
						select 
						(
							select
								meta_value
							from
								wp_usermeta
							where
								user_id = {$_REQUEST['user_id']} and 
								meta_key = 'nombre'
						) nombre,
						(
							select
								meta_value
							from
								wp_usermeta
							where
								user_id = {$_REQUEST['user_id']} and 
								meta_key = 'apellido'
						) apellido,
						(
							select
								meta_value
							from
								wp_usermeta
							where
								user_id = {$_REQUEST['user_id']} and 
								meta_key = 'telefono'
						) telefono
						";
				// usuario que aceptó la publicacion
				$usuario = $wpdb->get_row($sql);

				// publicacion
				$post = $wpdb->get_row("select * from wp_posts where id = {$_REQUEST['post_id']}");

				// datos json de la publicacion
				$json = json_decode($post->post_content);

				// guardamos el usuario que está aceptando la publicacion, dentro de la publicacion
				$json->respuesta = $usuario;
				$update = [
					'post_status'   => 'pending',
					'post_content'   => json_encode($json)
				];
				// actualizamos la publicacion
				$wpdb->update("wp_posts", $update, ['id' => $_REQUEST['post_id']]);

				// mensaje de la notificacion para el que publicó el match
				$mensaje =  $usuario->nombre . ' ' . $usuario->apellido . " ha aceptado su solicitud para jugar un match entre equipos";
				$post->telefono = $usuario->telefono;
				$insert = [
					'post_author'   => $post->post_author,
					'post_title'   => "Solicitud de match aceptada",
					'post_excerpt'   => $mensaje,
					'post_content'   => json_encode($post),
					'post_type'   => "markoh_notification",
				];

				// se guarda notificacion para el usuario que hizo la notificacion
				$wpdb->insert("wp_posts", $insert);

				$telefono = $wpdb->get_var("select meta_value from wp_usermeta where meta_key='telefono' and user_id={$post->post_author}");
				$nombre = $wpdb->get_var("select meta_value from wp_usermeta where meta_key='nombre' and user_id={$post->post_author}");
				$apellido = $wpdb->get_var("select meta_value from wp_usermeta where meta_key='apellido' and user_id={$post->post_author}");
				$post->telefono = $telefono;
				$insert = [
					'post_author'   => $_REQUEST['user_id'],
					'post_title'   => "Solicitud de match aceptada",
					'post_excerpt'   => "Usted ha aceptado una solicitud de match de <b>{$json->equipo->nombre}</b>. 
											<br>Para concretar el encuentro, llamar o escribir a $nombre $apellido",
					'post_content'   => json_encode($post),
					'post_type'   => "markoh_notification",
				];
				$wpdb->insert("wp_posts", $insert);

				$response = ['success' => true, 'message' => "EXITO:\nSe le ha enviado una notificacion a ambas partes para que finalicen los detalles del encuentro"];
				break;
			case 'forgot_password':
				$user = $wpdb->get_row("select * from wp_users where lower(user_login) = '" . strtolower($_REQUEST["email"]) . "' or lower(user_email)  = '" . strtolower($_REQUEST["email"]) . "'");

				if (!empty($user)) {
					$to = $_REQUEST["email"];
					$subject = 'Usted Ha solicitado una Recuperacion de Clave';
					//$body .= 	'<p>'.get_bloginfo( 'name' ).' - '.date('Y').'</p>';
					//$headers = [
					//	'Content-Type: text/html; charset=UTF-8',
					//	'From: '.get_bloginfo( 'name' ).' <info@plataforma.myappi.net>'
					//];

					$password = get_user_meta($user->ID, "password")[0];
					$nombre = get_user_meta($user->ID, "nombre")[0];
					$apellido = get_user_meta($user->ID, "apellido")[0];

					send_email($to, $subject, 'recuperar_clave', ['password' => $password, 'nombre' => $nombre, 'apellido' => $apellido]);



					$response = ['success' => true, 'message' => 'Hemos enviado un mensaje a su correo con su contraseña.'];
				} else {
					$response = ['success' => false, 'message' => "ERROR: El email ingresado no se encuentra registrado "];
				}

				break;
			case "login":
				$user_login     = esc_attr($_POST["email"]);
				$user_password  = esc_attr($_POST["password"]);

				$creds = [];
				$creds['user_login'] = $user_login;
				$creds['user_password'] = $user_password;
				$creds['remember'] = true;

				$user = wp_signon($creds, false);

				if (is_wp_error($user)) {
					$response = ['success' => false, 'message' => 'Correo o contraseña inválidos. Intente nuevamente.'];
					break;
				}



				wp_set_current_user($user->ID, $user_login);
				wp_set_auth_cookie($user->ID, true, false);

				do_action('wp_login', $user_login);

				if (is_user_logged_in())
					$response = ['success' => true, 'url' => $url];
				else
					$response = ['success' => false, 'message' => 'Ha ocurrido un error inesperado. Contacte al administrador de la aplicación.'];
				break;
			case "delete_court":
				$wpdb->delete('wp_courts', ['id' => $_REQUEST['id']]);
				$response = ['success' => true, 'message' => 'Cancha eliminada exitosamente'];
				break;
			case "delete_team":
				$wpdb->delete('wp_teams', ['id' => $_REQUEST['id']]);
				$response = ['success' => true, 'message' => 'Equipo eliminado exitosamente'];
				break;
			case "update_team":
				$id_team = $_REQUEST['id_team'];
				$team = $wpdb->get_row("select * wp_teams where id = $id_team");
				$user = wp_get_current_user();


				$image_url 	= '';
				$image_path = '';

				// verificamos si esplayer y la imagen es valida
				if (count($_FILES) > 0) {
					$target_dir = dirname(__FILE__) . "/imagenes/";
					@mkdir($target_dir);

					$file_name = time() . "_" . sanitize_file_name(strtolower($_FILES["files"]["name"][0]));
					$file_url = site_url() . "/wp-content/themes/flatsome-child/imagenes/" . $file_name;

					$target_file = $target_dir . $file_name;

					$image_url 	= $file_url;
					$image_path = $target_file;

					$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

					// Check if image file is a actual image or fake image
					$check = getimagesize($_FILES["files"]["tmp_name"][0]);
					if ($check == false) {
						$response = ['success' => false, 'message' => 'ERROR: la imagen enviada no es válida. Debe tener uno de los siguientes formatos: JPG, JPEG, PNG'];
						break;
					}

					// Check file size
					if ($_FILES["files"]["size"][0] > 2000000) {
						$response = ['success' => false, 'message' => 'ERROR: La imagen envida es muy grande. El tamaño máximo permitido es de 2 MB'];
						break;
					}

					// Allow certain file formats
					if (
						$imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
						&& $imageFileType != "gif"
					) {
						$response = ['success' => false, 'message' => 'ERROR: Solo se aceptan imagenes JPG, JPEG, PNG y GIF'];
						break;
					}

					if (!move_uploaded_file($_FILES["files"]["tmp_name"][0], $target_file)) {
						@unlink($team->logo_path);
						$response = ['success' => false, 'message' => 'ERROR: Un error inesperado ha ocurrido, intente nuevamente.'];
						break;
					}
				}

				$data = [
					"nombre" => $_REQUEST['nombre'],
					"descripcion" => $_REQUEST['descripcion'],
					"tipo" => $_REQUEST['tipo'],
					"creado_por" => $user->ID
				];

				if (count($_FILES) > 0) {
					$data["logo_url"] = $image_url;
					$data["logo_path"] = $image_path;
				}

				$wpdb->update('wp_teams', $data, ['id' => $id_team]);



				$wpdb->delete('wp_team_players', ['id_team' => $id_team]);

				$usuarios = explode(',', $_REQUEST['usuarios']);
				foreach ($usuarios as $v) {
					$data = [
						'id_team' => $id_team,
						'id_player' => $v,
					];
					$wpdb->insert('wp_team_players', $data);
				}
				$response = ['success' => true, 'message' => 'Equipo modificado exitosamente'];
				break;
			case "new_court":
				$user = wp_get_current_user();

				$image_url 	= '';
				$image_path = '';

				// verificamos si esplayer y la imagen es valida
				if (count($_FILES) > 0) {
					$target_dir = dirname(__FILE__) . "/imagenes/";
					@mkdir($target_dir);

					$file_name = time() . "_" . sanitize_file_name(strtolower($_FILES["files"]["name"][0]));
					$file_url = site_url() . "/wp-content/themes/flatsome-child/imagenes/" . $file_name;

					$target_file = $target_dir . $file_name;

					$image_url 	= $file_url;
					$image_path = $target_file;

					$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

					// Check if image file is a actual image or fake image
					$check = getimagesize($_FILES["files"]["tmp_name"][0]);
					if ($check == false) {
						$response = ['success' => false, 'message' => 'ERROR: la imagen enviada no es válida. Debe tener uno de los siguientes formatos: JPG, JPEG, PNG'];
						break;
					}

					// Check file size
					if ($_FILES["files"]["size"][0] > 2000000) {
						$response = ['success' => false, 'message' => 'ERROR: La imagen envida es muy grande. El tamaño máximo permitido es de 2 MB'];
						break;
					}

					// Allow certain file formats
					if (
						$imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
						&& $imageFileType != "gif"
					) {
						$response = ['success' => false, 'message' => 'ERROR: Solo se aceptan imagenes JPG, JPEG, PNG y GIF'];
						break;
					}

					if (!move_uploaded_file($_FILES["files"]["tmp_name"][0], $target_file)) {
						$response = ['success' => false, 'message' => 'ERROR: Un error inesperado ha ocurrido, intente nuevamente.'];
						break;
					}
				}

				$data = [
					"nombre" => $_REQUEST['nombre'],
					"direccion" => $_REQUEST['direccion'],
					"ciudad" => $_REQUEST['ciudad'],
					"telefono" => $_REQUEST['telefono'],
					"horario" => $_REQUEST['horario'],
					"logo_url" => $image_url,
					"logo_path" => $image_path,
					"creado_por" => $user->ID
				];
				$wpdb->insert('wp_courts', $data);



				$response = ['success' => true, 'message' => 'Cancha registrada exitosamente'];
				break;

			case "update_court":
				$user = wp_get_current_user();

				$image_url 	= '';
				$image_path = '';

				// verificamos si esplayer y la imagen es valida
				if (count($_FILES) > 0) {
					$target_dir = dirname(__FILE__) . "/imagenes/";
					@mkdir($target_dir);

					$file_name = time() . "_" . sanitize_file_name(strtolower($_FILES["files"]["name"][0]));
					$file_url = site_url() . "/wp-content/themes/flatsome-child/imagenes/" . $file_name;

					$target_file = $target_dir . $file_name;

					$image_url 	= $file_url;
					$image_path = $target_file;

					$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

					// Check if image file is a actual image or fake image
					$check = getimagesize($_FILES["files"]["tmp_name"][0]);
					if ($check == false) {
						$response = ['success' => false, 'message' => 'ERROR: la imagen enviada no es válida. Debe tener uno de los siguientes formatos: JPG, JPEG, PNG'];
						break;
					}

					// Check file size
					if ($_FILES["files"]["size"][0] > 2000000) {
						$response = ['success' => false, 'message' => 'ERROR: La imagen envida es muy grande. El tamaño máximo permitido es de 2 MB'];
						break;
					}

					// Allow certain file formats
					if (
						$imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
						&& $imageFileType != "gif"
					) {
						$response = ['success' => false, 'message' => 'ERROR: Solo se aceptan imagenes JPG, JPEG, PNG y GIF'];
						break;
					}

					if (!move_uploaded_file($_FILES["files"]["tmp_name"][0], $target_file)) {
						$response = ['success' => false, 'message' => 'ERROR: Un error inesperado ha ocurrido, intente nuevamente.'];
						break;
					}
				}

				$data = [
					"nombre" => $_REQUEST['nombre'],
					"direccion" => $_REQUEST['direccion'],
					"ciudad" => $_REQUEST['ciudad'],
					"telefono" => $_REQUEST['telefono'],
					"horario" => $_REQUEST['horario'],
					"creado_por" => $user->ID
				];
				if (count($_FILES) > 0) {
					$data["logo_url"] = $image_url;
					$data["logo_path"] = $image_path;
				}

				$wpdb->update('wp_courts', $data, ['id' => $_GET['id']]);


				$response = ['success' => true, 'message' => 'Datos de Cancha modificados exitosamente'];
				break;

			case "new_team":
				$user = wp_get_current_user();

				$image_url 	= '';
				$image_path = '';

				// verificamos si esplayer y la imagen es valida
				if (count($_FILES) > 0) {
					$target_dir = dirname(__FILE__) . "/imagenes/";
					@mkdir($target_dir);

					$file_name = time() . "_" . sanitize_file_name(strtolower($_FILES["files"]["name"][0]));
					$file_url = site_url() . "/wp-content/themes/flatsome-child/imagenes/" . $file_name;

					$target_file = $target_dir . $file_name;

					$image_url 	= $file_url;
					$image_path = $target_file;

					$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

					// Check if image file is a actual image or fake image
					$check = getimagesize($_FILES["files"]["tmp_name"][0]);
					if ($check == false) {
						$response = ['success' => false, 'message' => 'ERROR: la imagen enviada no es válida. Debe tener uno de los siguientes formatos: JPG, JPEG, PNG'];
						break;
					}

					// Check file size
					if ($_FILES["files"]["size"][0] > 2000000) {
						$response = ['success' => false, 'message' => 'ERROR: La imagen envida es muy grande. El tamaño máximo permitido es de 2 MB'];
						break;
					}

					// Allow certain file formats
					if (
						$imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
						&& $imageFileType != "gif"
					) {
						$response = ['success' => false, 'message' => 'ERROR: Solo se aceptan imagenes JPG, JPEG, PNG y GIF'];
						break;
					}

					if (!move_uploaded_file($_FILES["files"]["tmp_name"][0], $target_file)) {
						$response = ['success' => false, 'message' => 'ERROR: Un error inesperado ha ocurrido, intente nuevamente.'];
						break;
					}
				}

				$data = [
					"nombre" => $_REQUEST['nombre'],
					"descripcion" => $_REQUEST['descripcion'],
					"tipo" => $_REQUEST['tipo'],
					"logo_url" => $image_url,
					"logo_path" => $image_path,
					"creado_por" => $user->ID
				];
				$wpdb->insert('wp_teams', $data);
				$id_team = $wpdb->insert_id;


				$usuarios = explode(',', $_REQUEST['usuarios']);
				foreach ($usuarios as $v) {
					$data = [
						'id_team' => $id_team,
						'id_player' => $v,
					];
					$wpdb->insert('wp_team_players', $data);
				}
				$response = ['success' => true, 'message' => 'Equipo creado exitosamente'];
				break;
			case "new_post":
				date_default_timezone_set('America/Bogota');
				$vence =  strtotime($_REQUEST['vence'] . " " . $_REQUEST['hora'] . ':00');
				$hoy =  strtotime(date('Y-m-d H:i:s'));

				if ($vence <= $hoy) {
					$response = ['success' => false, 'message' => 'La fecha y hora inválidas. Coloque una fecha y hora superiores a ' . date('d/m/Y h:i a')];
					break;
				}

				$_REQUEST['app_post'] = '[[APP_POST]]';
				$_REQUEST['equipo'] = $wpdb->get_row('select * from wp_teams where id = ' . $_REQUEST['equipo']);
				$data = [
					'post_title' => 'Se busca ' . $_REQUEST['tipo_publicacion'],
					'post_content' => json_encode($_REQUEST),
					'post_type' => 'markoh_invitacion',
					'post_status' => 'publish',
					'post_date' => $_REQUEST['vence'],
				];
				wp_insert_post($data);
				$response = ['success' => true, 'message' => 'Invitación publicada exitosamente'];
				break;
			case "change_profile_picture":
				$user = wp_get_current_user();

				$m = get_user_meta($user->ID);
				$meta = new stdClass();
				foreach ($m as $k => $v) {
					$meta->$k = $v[0];
				}

				if (isset($meta->profile_picture_path)) {
					$original_path = $meta->profile_picture_path;
				} else {
					$original_path = '';
				}


				// verificamos si esplayer y la imagen es valida
				if (count($_FILES) > 0) {
					$target_dir = dirname(__FILE__) . "/imagenes/";
					@mkdir($target_dir);

					$file_name = time() . "_" . sanitize_file_name(strtolower($_FILES["files"]["name"][0]));
					$file_url = site_url() . "/wp-content/themes/flatsome-child/imagenes/" . $file_name;

					$target_file = $target_dir . $file_name;

					$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

					// Check if image file is a actual image or fake image
					$check = getimagesize($_FILES["files"]["tmp_name"][0]);
					if ($check == false) {
						$response = ['success' => false, 'message' => 'ERROR: la imagen enviada no es válida. Debe tener uno de los siguientes formatos: JPG, JPEG, PNG'];
						break;
					}

					// Check file size
					if ($_FILES["files"]["size"][0] > 2000000) {
						$response = ['success' => false, 'message' => 'ERROR: La imagen envida es muy grande. El tamaño máximo permitido es de 2 MB'];
						break;
					}

					// Allow certain file formats
					if (
						$imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
						&& $imageFileType != "gif"
					) {
						$response = ['success' => false, 'message' => 'ERROR: Solo se aceptan imagenes JPG, JPEG, PNG y GIF'];
						break;
					}

					if (!move_uploaded_file($_FILES["files"]["tmp_name"][0], $target_file)) {
						$response = ['success' => false, 'message' => 'ERROR: Un error inesperado ha ocurrido, intente nuevamente.'];
						break;
					} else {
						// borramos imagen de perfil anterior
						$data = [
							'profile_picture'  => $file_url,
							'profile_picture_path' => $target_file,
						];
						foreach ($data as $k => $v) {
							update_user_meta($user->ID, $k, $v);
						}

						@unlink($original_path);
						$response = ['success' => true, 'message' => 'Imagen cargada exitosamente'];
					}
				} else {
					$response = ['success' => false, 'message' => 'ERROR: The image file could not be uploaded. Reload the page and try again.'];
				}
				break;

			case "update_userdata":
				$user = wp_get_current_user();
				foreach ($_REQUEST as $key => $val) {
					update_user_meta($user->ID, $key, $val);
				}
				$response = ['success' => true, "message" => "Datos guardados exitosamente"];
				break;

			case "register":
				$nombre = $_REQUEST["nombre"];
				$apellido = $_REQUEST["apellido"];
				$email = $_REQUEST["email"];
				$tipo = $_REQUEST["tipo"];
				$password = $_REQUEST["password"];

				// verificamos si el email existe
				$user_data = get_user_by('email', $email);
				if (!empty($user_data)) {
					$response = ['success' => false, "message" => "El correo ingresado ya existe, intente con uno diferente."];
					break;
				}

				// INSERTAMOS EL USUARIO
				$user_id = wp_insert_user(array(
					'user_login' => $email,
					'user_pass' => $password,
					'user_email' => $email,
					'display_name' => $nombre . " " . $apellido,
					'role' => $tipo
				));


				$meta = array(
					'nombre' => $nombre,
					'apellido' => $apellido,
					'password' => $password,
					'posicion' => '',
					'profile_picture' => '',

				);

				foreach ($meta as $key => $val) {
					update_user_meta($user_id, $key, $val);
				}

				send_email($email, 'Registro Exitoso', 'registro_exitoso', ['email' => $email, 'clave' => $password]);

				$url = ($tipo == 'jugador') ? '/start' : '/my_courts';

				$response = ['success' => true, 'url' => $url, 'message' => 'Registro exitoso'];
				break;

			case "type_of_soccer":
				$r = $wpdb->get_row("SELECT tipo FROM wp_teams WHERE id = " . $_REQUEST['id_equipo']);
				$r ? $response = ['success' => true, 'data' => $r] : $response = ['success' => false, 'data' => 'null'];

				break;
				
			default:
				$response["message"] = "Error de Programación: Acción AJAX Incorrecta";
				break;
		}
	} else {
		$response["message"] = "Error de Programación: Acción AJAX Indefinida";
	}
	echo wp_send_json($response);
	wp_die();
}

add_action('wp_ajax_custom_ajax', 'custom_ajax');
add_action('wp_ajax_nopriv_custom_ajax', 'custom_ajax');



// load custom js
add_action('wp_enqueue_scripts', 'frontend_customjs', 999);
function frontend_customjs()
{
	$file_url = get_stylesheet_directory_uri() . '/js/script.js';
	wp_enqueue_script('frontend_customjs', $file_url, array('jquery'));
}

function debug($p)
{
	echo "<pre>";
	die(var_dump($p));
}

function markoh_custom_shortcodes($atts = array())
{
	global $user;
	global $meta;
	$user = wp_get_current_user();
	$m = get_user_meta($user->ID);
	$meta = new stdClass();
	foreach ($m as $k => $v) {
		$meta->$k = $v;
	}

	extract(shortcode_atts(array(
		'type' => ''
	), $atts));
	$file = "{$type}.php";

	ob_start();
	include dirname(__FILE__) . "/shortcodes/$file";
	$content = ob_get_contents();
	ob_get_clean();
	return $content;
}
add_shortcode('markoh', 'markoh_custom_shortcodes');

add_role('jugador', 'Jugador', ['read' => true]);
add_role('encargado_cancha', 'Encargado de Cancha', ['read' => true]);
add_role('capitan', 'Capitán de Equipo', ['read' => true]);

function send_email($to, $subject, $t, $d)
{

	global $template;
	global $data;
	$template = $t;
	$data = $d;
	ob_start();
	require dirname(__FILE__) . '/email/template.php';
	$body = ob_get_contents();
	ob_end_clean();

	$headers = [
		'Content-Type: text/html; charset=UTF-8',
		'From: ' . get_bloginfo('name') . ' <info@markoh.myappi.net>'
	];

	$r = wp_mail($to, $subject, $body, $headers);
}

add_action('phpmailer_init', 'wpse8170_phpmailer_init');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
//require 'PHPMailer/Exception.php';
//require 'PHPMailer/PHPMailer.php';
//require 'PHPMailer/SMTP.php';

function wpse8170_phpmailer_init(PHPMailer $phpmailer)
{
	$phpmailer->Host = 'mail.markoh.myappi.net';
	$phpmailer->Port = 465; // could be different
	$phpmailer->Username = 'info@markoh.myappi.net'; // if required
	$phpmailer->Password = 'lkaOI2AdFIrr'; // if required
	$phpmailer->SMTPAuth = true; // if required
	//$phpmailer->SMTPDebug = 1;
	//$phpmailer->debug = 1;

	$phpmailer->SMTPSecure = 'ssl'; // enable if required, 'tls' is another possible value

	$phpmailer->IsSMTP();
}


/*
 * Display errors
 */
if (!function_exists('debug_wpmail')) :
	function debug_wpmail($result = false)
	{

		if ($result)
			return;

		global $ts_mail_errors, $phpmailer;

		if (!isset($ts_mail_errors))
			$ts_mail_errors = array();

		if (isset($phpmailer))
			$ts_mail_errors[] = $phpmailer->ErrorInfo;

		print_r('<pre>');
		print_r($ts_mail_errors);
		print_r('</pre>');
	}
endif;

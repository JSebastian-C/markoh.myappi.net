<?php
global $wpdb;
global $user;
$wpdb->update("wp_posts", ['post_status' => 'read'], ["post_author" => $user->ID, "post_type" => 'markoh_notification']);
$query = "select * from wp_posts where post_author = {$user->ID} and post_type = 'markoh_notification'";
$noticias = $wpdb->get_results($query);

?>
<div class="fake_header">
	<a href="/start" class="icon left"><i class="fa fa-chevron-left"></i></a>
	<a href="/new_post" class="icon right" hidden><i class="fa fa-plus"></i></a>
	<h2 class="title">Notificaciones</h2>
</div>
<div class="fake_body">
	<?php if (count($noticias)) : ?>
		<?php foreach ($noticias as $v) : ?>
			<?php $data = json_decode($v->post_content); ?>
			<div class="noticia">
				<h3><?= $v->post_title ?></h3>
				<p><?= $v->post_excerpt ?></p>
				<?php if (isset($data->telefono)) : ?>
					<a target="blank" href="whatsapp://send?phone=57<?= $data->telefono ?>" class="whatsapp">
						<img src="https://markoh.myappi.net/wp-content/uploads/2021/05/whatsapp_icon.png">
						<span><?= $data->telefono ?></span>
					</a>
				<?php endif; ?>
				<i></i>
				<a href="#" class="delete" data-id="<?= $v->ID ?>"><i class="fa fa-trash"></i></a>
			</div>
		<?php endforeach; ?>

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

	.noticia h3 span {
		color: rgb(33, 158, 168);
	}

	.noticia>.delete {
		position: absolute;
		display: block;
		right: 23px;
		top: 13px;
		color: #d53434;
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
							user_id: <?= $user->ID ?>,
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
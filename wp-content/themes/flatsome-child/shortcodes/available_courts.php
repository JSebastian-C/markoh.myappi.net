<?php
	global $wpdb;
	global $user;
	$wpdb->get_results("SET lc_time_names = 'es_ES'");
	$query = "SELECT * from wp_courts order by nombre asc";
	$equipos = $wpdb->get_results($query);
	
?>
<div class="fake_header"> 
	<a href="/start" class="icon left"><i class="fa fa-chevron-left"></i></a>
	<a href="/new_post" class="icon right" hidden><i class="fa fa-plus"></i></a>
	<h2 class="title">Canchas Disponibles</h2>
</div>
<div class="fake_body">
	<?php if(count($equipos)):?>
		<?php foreach($equipos as $v):?>
			<div class="noticia">
				<div class="image_container" style="background-image:url(<?=!empty($v->logo_url) ? $v->logo_url : "https://markoh.myappi.net/wp-content/uploads/2021/05/markoh-image-banner-1.png" ?>);"></div>
				<h3><?=$v->nombre?></h3>
				<small><?=$v->ciudad?></small><br>
				<small><?=$v->direccion?></small><br>
				<small><?=$v->horario?></small><br>
				<?php if(!empty($v->telefono)):?>
					<a  target="blank" href="whatsapp://send?phone=57<?=$v->telefono?>" class="whatsapp">
						<img src="https://markoh.myappi.net/wp-content/uploads/2021/05/whatsapp_icon.png">
						<span><?=$v->telefono?></span>
					</a>
				<?php endif;?>
				
			</div>
		<?php endforeach;?>
		
	<?php else:?>
			<h1 class="no_teams">Aún no hay canchas registradas</h1>
	<?php endif;?>
</div>
<?php include 'footer.php' ?>
<style>
.whatsapp span{
	color: white;
    font-weight: bold;
    font-size: 17px;
    vertical-align: middle;
}
.whatsapp img{
	height:25px;
}
.whatsapp{
	border-radius:5px;
	text-align:center;
	    margin: 10px 0;
    white-space: nowrap;
    background: #1bd741;
    padding:0 10px;
	display:inline-block;
}

	.no_teams{
		margin: 10px;
		padding: 5px;
		text-align: center;
		box-sizing: border-box;
		width: auto;
		border: 1px solid #aaa;
		border-radius: 5px;
	}
	.opciones_links a {
		display:block;
	}
	.opciones_links.active {
		display:block;
		
	}
	.opciones_links {
		z-index:10;
		position: absolute;
		display:none;
		top: 5px;
		right: 50px;
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
    top: 5px;
    right: 6px;
    background: #ffffff;
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
    width: 100%;
    height: 120px;
    background-color: #eee;
    /* border-radius: 500px; */
    overflow: hidden;
    background-position: center center;
    background-size: cover;
    margin: 0 auto;
    position: absolute;
    top: 0;
    left: 0;
}
	.fake_body{
		padding-top:80px;
		padding-bottom:10px;
		min-height:300px;
		background:#f2f2f2;
	}
	html{
		background:#f2f2f2;
	}
	.fake_header .icon.right{
		position: absolute;
		top: 10px;
		right: 15px;
		color: white;
		padding: 10px;
		z-index:5;
	}
	.fake_header .icon.left{
		position: absolute;
		top: 10px;
		left: 15px;
		color: white;
		padding: 10px;
		z-index:5;
	}
	.fake_header .title{
		position: absolute;
		top: 23px;
		left: 15px;
		color: white;
		text-align: center;
		width: 93%;
	}
	.fake_header{
		position: fixed;
		top: 0;
		left: 0;
		background: #004454;
		width: 100%;
		padding: 5px;
		color: white;
		height: 70px;
		z-index:2;
	}
	.noticia h3 span{
		color:rgb(33,158,168);
	}
	.noticia > i{
		position:absolute;
		width:10px;
		height:10px;
		border-radius:10px;
		background:rgb(33,158,168);
		display:block;
		top:20px;
		left:17px;
	}
	.noticia h3{
		font-size: 1.2em;
		margin:0;
	}
	.noticia p{
		margin-bottom:5px;
	}
.noticia {
    position: relative;
    margin: 10px;
    padding: 120px 20px 20px 20px;
    margin-bottom: 25px !important;
    background: white;
    border-radius: 8px;
    border: none;
    box-shadow: 2px 2px 10px -1px rgb(0 0 0 / 10%);
    overflow: hidden;
}
</style>
<?php add_action( 'wp_footer',function(){ global $user; ?>
<script>
	jQuery(function($){	
		$('.delete').on('click',function(e){
			e.preventDefault();
			var id = $(this).data('id')
			
			if(confirm("¿Está seguro que quiere eliminar ésta notificación?")){
				$.post('/wp-admin/admin-ajax.php?action=custom_ajax&caction=delete_notification',
					{user_id:<?=$user->ID?>,"id":id},
					function(r){
						alert(r.message);
						if(r.success)
							window.location.reload();
						
						
					},"json")
			}
		});
	})
</script>
<?php }); ?>

<?php
	global $user;
	global $wpdb;
	$es_capitan = $wpdb->get_var("select if(count(1)>0,1,0) from wp_teams where creado_por = {$user->ID}");
	$query = "
			SELECT 
				t.* 
			  FROM 
				wp_teams t
			  WHERE
				t.creado_por = {$user->ID}";
				
	$equipos = $wpdb->get_results($query);
?>

<div class="fake_header"> 
	<a href="/news" class="icon left"><i class="fa fa-chevron-left"></i></a>
	<a hidden href="/new_post" class="icon right"><i class="fa fa-plus"></i></a>
	<h2 class="title">Crear Invitación</h2>
</div>
<div class="fake_body">
	<div class="login_form register">
		<form id="form_register">
		<b>¿Qué buscas?</b>
		<select name="tipo_publicacion" required>
			<option value="">Seleccionar</option>
			<?php if($es_capitan):?>
			<option value="equipo">Un partido entre equipos</option>
			<?php endif;?>
			<option value="partido">Un partido para jugar</option>
		</select>
		<div class="partido" style="display:none;">
			<b>¿Para jugar qué posición?</b>
			<select name="posicion">
				<option value="">Seleccionar</option>
				<option>Delantero</option>
				<option>Defensa</option>
				<option>Medio Campo</option>
				<option>Portero</option>
			</select>
		</div>
		<div class="equipo" style="display:none;">
			<b>Selecciona tu equipo</b>
			<select name="equipo">
				<option value="">Seleccionar</option>
				<?php foreach($equipos as $v):?>
					<option value="<?=$v->id?>"><?=$v->nombre?></option>
				<?php endforeach;?>
				
			</select>
		</div>
		<b>Ciudad</b>
		<input type="text" name="ciudad" required>
		<br>
		<br>
		<b>Fecha</b>
		<input type="date" name="vence" placeholder="<?=date("d-m-Y")?>" required>
		<br>
		<br>
		<b>Hora</b>
		<input type="time" name="hora"  required style="width:100%;">
		<br>
		<br>
		<b>Lugar</b>
		<input type="text" name="lugar" required>
		<br>
		<br>
		<b>Escribe tus comentarios </b>
		<textarea name="anuncio" maxlength='120'></textarea>
		<br>
		<p><button type="submit">Publicar Invitación</button></p>
	</form>
	</div>
</div>
<?php include 'footer.php' ?>
<style>
	.fake_body{
		padding:80px 10px 10px 10px;
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
	}
</style>
<?php add_action( 'wp_footer',function(){?>
<script>
	jQuery(function($){	
		$('[name="tipo_publicacion"]').on('change',function(e){
			if($(this).val()=='partido'){
				$('.partido').show();
				$('.equipo').hide();
			}else{
				$('.partido').hide();
				$('.equipo').show();
			}
		});
		$('form#form_register').on('submit',function(e){
			e.preventDefault()
			var data = $(this).serialize();
			$.post('/wp-admin/admin-ajax.php?action=custom_ajax&caction=new_post',
					data,
					function(r){
						alert(r.message);
						if(r.success)
							window.location.href='/news';
						
					},"json")
			
			return false;
			
		});
		$('select.tipo_aplicacion').on('change',function(){
			var tipo = $(this).val();
			if(tipo!=''){	
				$('.tipo_aplicacion').addClass('input_loading');
				$.post('/wp-admin/admin-ajax.php?action=PlataformaMyAppiCustomAjax&caction=modelos_por_categoria&tipo='+tipo,null,function(r){
					$('.contenedor_modelos .imagen_modelo').not('.first').remove();
					$('.nombre_modelo_aplicacion').val('');
					$('.nombre_tipo_aplicacion').val(jQuery( ".tipo_aplicacion option:selected" ).text());
					for(i in r.modelos){
						m = jQuery('.imagen_modelo.first').clone();
						m.find('img.imagen').attr("src",r.modelos[i].imagen)
						m.find('img.imagen').attr("title",r.modelos[i].nombre)
						m.find('a').data("modelo",r.modelos[i])
						m.removeClass("first")
						$(m).insertBefore('.contenedor_modelos br');
					}
					$('.tipo_aplicacion').removeClass('input_loading');
				},'json')
			}
		})
		$('body').on('click','.vista_previa_modelo',function(e){
			e.preventDefault();
			$('iframe').attr('src','');
			$('iframe').css('background-image',"url(https://plataforma.myappi.net/wp-content/uploads/2021/05/loading_icon.gif)");
			$('.imagen_modelo').removeClass("preview");
			$('.imagen_modelo').removeClass("selected");
			$(this).parents('.imagen_modelo').addClass("preview");
			$('iframe').attr('src',$(this).data("modelo")['url']);
		})
		$('body').on('click','.seleccionar_modelo',function(e){
			e.preventDefault();
			$('iframe').attr('src','');
			$('.imagen_modelo').removeClass("preview");
			$('.imagen_modelo').removeClass("selected");
			$('iframe').css('background-image',"url(https://plataforma.myappi.net/wp-content/uploads/2021/05/loading_icon.gif)");
			$('iframe').attr('src',$(this).data("modelo")['url']);
			$('.nombre_modelo_aplicacion').val($(this).data("modelo")['nombre']);
			$('.url_modelo').val($(this).data("modelo")['url']);

			$(".hover_card").css({top:185});
			$(this).parents('.imagen_modelo').addClass("selected");
		});
		$('body').on('mouseenter','.imagen_modelo:not(.preview,.selected)',function(){
			$(this).find(".hover_card").animate({top:0},500)
		});
		$('body').on('mouseleave','.imagen_modelo:not(.preview,.selected)',function(){
			$(this).find(".hover_card").animate({top:185},500)
		});
		$('select.tipo_aplicacion').trigger('change');
		$('input.url_aplicacion').on('keyup',function(){
			var url = $(this).val();
			$("span.url").html(url);
			if(url.length<8){
				$("div.status").html('La url debe tener mínimo 8 caracteres');
				$("div.status").css('color','red');
				return false;
			}
			if(url.length>30){
				$("div.status").html('La url debe tener máximo 30 caracteres');
				$("div.status").css('color','red');
				return false;
			}
			const regex = /[^a-zA-Z0-9-._]+/gm;
			const str = url;
			if(m = (regex.exec(str)) !== null) {
				$("div.status").html('La url solo puede tener letras numeros, punto (.), guión (-) y guión bajo (_)');
				$("div.status").css('color','red');
				return false;
			}
			$('input.url_aplicacion').addClass('input_loading');
			$.post('/wp-admin/admin-ajax.php?action=PlataformaMyAppiCustomAjax&caction=domain_exists&url='+url,null,function(r){
				if(r.exists){
					$("div.status").html('Ésta url ya se encuentra en uso. Intente con otra diferente. <I class="dashicons dashicons-no"></I>');
					$("div.status").css('color','red');					
				}else{
					$("div.status").html('<b>URL DISPONIBLE <I class="dashicons dashicons-yes"></I></b>');
					$("div.status").css('color','green');					
				}
				$('input.url_aplicacion').removeClass('input_loading');
			},'json')
			
				
		});
	})
</script>
<?php }); ?>

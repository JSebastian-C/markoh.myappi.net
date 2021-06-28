<form id="form_register">
<p><label> <span class="wpcf7-form-control-wrap nombres"><input type="text" name="nombre" value="" size="40" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required" aria-required="true" aria-invalid="false" placeholder="Nombre"></span> </label>
<label> <span class="wpcf7-form-control-wrap apellido"><input type="text" name="apellido" value="" size="40" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required" aria-required="true" aria-invalid="false" placeholder="Apellido"></span> </label>
<label> <span class="wpcf7-form-control-wrap email"><input type="email" name="email" value="" size="40" class="wpcf7-form-control wpcf7-text wpcf7-email wpcf7-validates-as-required wpcf7-validates-as-email" aria-required="true" aria-invalid="false" placeholder="Correo electrónico"></span> </label>
<label> <input required type="password" name="password" placeholder="Contraseña"> </label>
<label> <span class="wpcf7-form-control-wrap tipo">
	<select name="tipo" required>
		<option value="">Tipo de Usuario</option>
		<option value="jugador">Jugador</option>
		<option value="encargado_cancha">Encargado Cancha</option></select></span></label></p>
<p><button type="submit">Registrar</button></p>
<p><a href="/clogin">Regresar</a></p>
</form>
<?php add_action( 'wp_footer',function(){?>
<script>
	jQuery(function($){	
		$('form#form_register').on('submit',function(e){
			e.preventDefault()
			var data = $(this).serialize();
			$.post('/wp-admin/admin-ajax.php?action=custom_ajax&caction=register',
					data,
					function(r){
						alert(r.message);
						if(r.success)
							window.location.href=r.url;
						
						
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

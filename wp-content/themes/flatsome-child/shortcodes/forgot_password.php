
<form id="form_register">
<label> <span class="wpcf7-form-control-wrap email"><input type="email" name="email" value="" size="40" class="wpcf7-form-control wpcf7-text wpcf7-email wpcf7-validates-as-required wpcf7-validates-as-email" aria-required="true" aria-invalid="false" placeholder="Correo electrónico"></span> </label>
<br>
<p><button type="submit">Enviar Contraseña a su Correo Electrónico</button></p>

</form>
<?php add_action( 'wp_footer',function(){?>
<script>
	jQuery(function($){
		$('form#form_register').on('submit',function(e){
			e.preventDefault()
			var data = $(this).serialize();
			$.post('/wp-admin/admin-ajax.php?action=custom_ajax&caction=forgot_password',
					data,
					function(r){
						alert(r.message);
						if(r.success)
							window.location.href='/clogin';
						


					},"json")
			return false;
		});
	})
</script>
<?php }); ?>

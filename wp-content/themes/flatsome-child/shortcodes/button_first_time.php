<button class="primary" DISABLED style="color:white;">CONTINUAR</button>
<style>
	
</style>
<?php add_action( 'wp_footer',function(){?>
<script>
	jQuery(function($){	
		setTimeout(function(){
			$('button').prop("disabled",false).css("color",'white');
		},10000)
		$('button').on("click",function(){
			setCookie('first_time', 1, 3650) ;
			window.location.href="/clogin";
		})
	})	
</script>
<?php }); ?>

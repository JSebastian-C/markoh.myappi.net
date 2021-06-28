<div class="slider_mo">
	<div class="slides">
		<div class="slide">
			<div class="text">¡Bienvenid@ a <span><?=get_bloginfo('name')?></span>, la comunidad aficionada al deporte más grande del mundo!</div>
		</div>
		<div class="slide">
			<div class="text">Crea tu perfil como jugador@ y sé fichado por los mejores equipos</div>
		</div>
		<div class="slide">
			<div class="text">¡Crea tu equipo de fútbol con tus amig@s y reta a otros equipos!</div>
		</div>
	</div>
	<div class="dots">
		<i class="active"></i>
		<i></i>
		<i></i>
	</div>
	<div  class="logo_container">
		<img src="https://markoh.myappi.net/wp-content/uploads/2021/05/markoh-logo-blanco.png">
	</div>
	<div  class="button_mo">
		<button>Continuar</button>
	</div>
</div>
<style>
	.slide .text{
		color: white;
		font-weight: bold;
		position: absolute;
		bottom: 20vh;
		padding: 0 10vw;
		text-align: center;
	}
	.text span{
		color:#0097a2;
	}
	.button_mo button{
		margin:0;
		background:#0097a2;
		color:white;
		border-radius:50px;
	}
	.button_mo{
		position:absolute;
		
		bottom:8vh;
		width:100%;
		text-align:center;
	}
	.dots{
		position:absolute;
		bottom:35vh;
		width:100%;
		text-align:center;
	}
	.dots i.active{
		background:#0097a2;
	}
	.dots i{
		margin:10px;
		height:10px;
		width:10px;
		background:white;
		display:inline-block;
		border-radius:10px;
	}
	.slider_mo{
		background:#eee;
		height:100vh;
		width:100vw;
		position:relative;
	}
	.logo_container img{
		width:50%;
		max-width:250px;
	}
	.logo_container{
		position:absolute;
		top:10vh;
		width:100%;
		text-align:center;
	}
	.slide{
		position:absolute;
		top:0;
		background-position:top center;
		background-size:cover;
		height: 100%;
    width: 100%;
	}
	.slide:nth-child(1){
		background-image:url(https://markoh.myappi.net/wp-content/uploads/2021/05/MarkOH-splash-1.png);
		left:0;
	}
	.slide:nth-child(2){
		background-image:url(https://markoh.myappi.net/wp-content/uploads/2021/05/MarkOH-splash-2.png);
		left:100%;
	}
	.slide:nth-child(3){
		background-image:url(https://markoh.myappi.net/wp-content/uploads/2021/05/MarkOH-splash-3.png);
		left:200%;
	}
</style>
<?php add_action( 'wp_footer',function(){?>
<script>
	var cnt = 0;
	jQuery(function($){	
		$('button').on("click",function(){
			if(cnt<2){
				cnt++;
				$(".slide").animate({left:"-=100%"},500,'linear');
				$(".dots i").removeClass("active");
				$(".dots i:nth-child("+(cnt+1)+")").addClass("active");
				return;
			}
			
			setCookie('first_time', 1, 3650) ;
			window.location.href="/clogin";
		})
	})	
</script>
<?php }); ?>

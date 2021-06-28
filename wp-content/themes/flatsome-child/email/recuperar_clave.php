<?php global $data;?>
<h1>Recuperación de clave.</h1>
<p>Estimado <?=$data["nombre"] . ' ' .$data["apellido"]?>, usted ha solicitado recuperar su contraseña para la app <?= get_bloginfo( 'name' )?></p>
<hr>
<p>Su contraseña es <b><?=$data["password"]?></b>. Ingresa a la app y continúa disfrutando del deporte más grande del mundo.</p>
<hr>
<p>Gracias por utilizar la app <?= get_bloginfo( 'name' )?>. </p>
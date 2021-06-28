<?php global $data;?>
<h1>Registro exitoso.</h1>
<p>Sus datos de acceso son los siguientes:</p>
<ul>
	<li>Usuario: <b><?=$data['email']?></b></li>
	<li>Clave: <b><?=$data['clave']?></b></li>
</ul>
<p>Ingrese a <a href="<?=site_url()?>/clogin"><?=site_url()?>/clogin</a> 
	para ingresar</p>

<?php  
	global $template;
?>
<h1> <?= get_bloginfo( 'name' ) ?> </h1>
<hr>
<?php require "{$template}.php"?>
<hr>
<p><?= get_bloginfo( 'name' ).' - '.date('Y') ?></p>
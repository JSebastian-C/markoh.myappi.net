<?php 
	global $user;
	global $meta;
	$url = (isset($meta->profile_picture)) ? $meta->profile_picture : 'https://markoh.myappi.net/wp-content/uploads/2021/05/user_avatar.png';
?>
<div style="
    padding: 0 20px;
    justify-content: center;
    flex-direction: column;
	display: flex;
    align-items: center;
">
<div class="image_container" style="width:100px;height:100px;background-color:#eee;border-radius:500px;overflow:hidden;background-image:url(<?=$url?>);background-position: top center;background-size: contain;"></div>
<h1 style="
    margin: 10px;
    display: inline-block;
    width: auto;
">Hola, <?=@$meta->nombre?></h1>
<div style="
    margin-bottom: 10px;
    font-size: 16px;
"><?=@$user->user_email?></div></div>

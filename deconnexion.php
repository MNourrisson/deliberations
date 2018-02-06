<?php
	session_start();
	session_destroy();
	/*setcookie('email', $iden[0]['mail'], time() - 365*24*3600);
	setcookie('drt', $iden[0]['droit'], time() - 365*24*3600);
	setcookie('id', $iden[0]['id_personne'], time() - 365*24*3600);*/
	header('Location: index.php');
?>	
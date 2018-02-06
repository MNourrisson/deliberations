<?php 
	session_start();
	include_once('classes/connect.php');
	include_once('classes/Utilisateur.php');
	include_once('classes/password.php');
	include_once('classes/Charte.php');
	$current='listecharte';
	if(!isset($_COOKIE['email']))
	{
		header('Location:connexion.php');
	}
	else{
		$pers = new Utilisateur();
		$testidentite = $pers->getVerif($_COOKIE['id']);
		$test = $testidentite['email'];
		if($test!=$_COOKIE['email'])
		{
			header('Location:connexion.php');
		}
		else{
			$c = new Charte();
			$listecharte = $c->getChartes();
			
	require_once('head.php');
	require_once('nav.php');
?>
	<section id="content">
		<h2>Les chartes</h2>
		<a href="ajoutcharte.php" class="boutonlien">Ajouter</a>
		<?php
			if(count($listecharte)!=0){
				echo '<ul>';
				foreach($listecharte as $k => $v){
				echo '<li><a href="modifcharte.php?c='.$v['id_charte'].'">'.$v['libelle'].'</a></li>';
				}
				echo '</ul>';
			}

		?>
	</section>
<?php

require_once('footer.php');
		}
	}
?>
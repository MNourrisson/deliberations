<?php 
	session_start();
	include_once('classes/connect.php');
	include_once('classes/Utilisateur.php');
	include_once('classes/password.php');
	include_once('classes/Reunion.php');

	$current='listereunion';
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
			$r = new Reunion();
			$listereun = $r->getInfosReunionByDate();			
	require_once('head.php');
	require_once('nav.php');
?>
	<section id="content">
		<h2>Les réunions</h2>
		<?php 
			if(count($listereun)!=0){
				echo '<ul>';
				foreach($listereun as $k => $v){
					$d = explode('-',$v['date']);
					$d= $d[2].'/'.$d[1].'/'.$d[0];
					echo '<li><a href="modifreunion.php?id='.$v['id_reunion'].'">'.$v['libelle'].' - '.$d.'</a></li>';
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

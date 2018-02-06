<?php 
	session_start();
	include_once('classes/connect.php');
	include_once('classes/Utilisateur.php');
	include_once('classes/password.php');
	include_once('classes/Reunion.php');

	$current='delib';

	$r = new Reunion();
	$listereun = $r->getInfosReunionByDate();
	
			
			
	require_once('head.php');
	require_once('nav.php');
?>
	<section id="content">
		<h2>Dernières délibérations</h2>
		
		<?php 
			if(count($listereun)!=0){
				echo '<a href="vuethema.php" class="boutonlien boutonthema">Voir les délibérations par thématique</a>';
				echo '<div class="listedelib">';
				foreach($listereun as $k => $v){				
					echo '<div>';
						$d = explode('-',$v['date']);
						$tabA = array('01'=>'Janvier','02'=>'Février', '03'=>'Mars', '04'=>'Avril', '05'=>'Mai', '06'=>'Juin', '07'=>'Juillet', '08'=>'Août', '09'=>'Septembre', '10'=>'Octobre','11'=>'Novembre','12'=>'Décembre' );
						echo '<ul><li class="jour">'.$d[2].'</li><li class="mois">'.$tabA[$d[1]].'</li><li class="annee">'.$d[0].'</li></ul>';
						echo '<span>'.$v['libelle'].'</span>';
						echo '<a href="/voirdeliberations.php?date='.$v['date'].'&id='.$v['id_reunion'].'">Voir les délibérations (chrono)</a>';
					echo '</div>';
				}
				echo '</div>';
			}
			else{ echo 'Pas de délibérations';}
		
		?>
	</section>
<?php

require_once('footer.php');


?>
<?php 
	session_start();
	include_once('classes/connect.php');
	include_once('classes/Utilisateur.php');
	include_once('classes/password.php');
	include_once('classes/Deliberation.php');
	include_once('classes/Reunion.php');

	$current='recherchedeliberation';
	
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
			$listedelib=array();
			$hasError =false;
			$erreur=array();
			$msgok=$erreurmsg='';
			$d = new Deliberation();
			$r = new Reunion();
			if(isset($_SESSION['recherchedeliberation']) && $_SESSION['recherchedeliberation']!='')
			{
				$listedelib= $d->getDelibByReunion($_SESSION['recherchedeliberation']);	
			}
			$lister = $r->getInfosReunionByDate();
		
			if(isset($_POST['enregistrement']))
			{
				if(isset($_POST['reunion']) && $_POST['reunion']=='')
				{
					$hasError =true;
					$erreur['reunion'] = 'Veuillez choisir une réunion.';
				}
				
				if(!$hasError)
				{
					if(isset($_SESSION['recherchedeliberation']) && $_SESSION['recherchedeliberation']!='')
					{
						unset($_SESSION['recherchedeliberation']);
					}
					$_SESSION['recherchedeliberation']=$_POST['reunion'];
					
					$listedelib= $d->getDelibByReunion($_POST['reunion']);	
					
				}
			}
	require_once('head.php');
	require_once('nav.php');
?>
	<section id="content">
		<h2>Rechercher une délibération</h2>
		
		<p class="indication">Indication : Les champs suivis de * sont obligatoires.</p>
		<?php if(isset($erreurmsg) && $erreurmsg!='') echo '<p>Erreur : '.$erreurmsg.'</p>'; ?>
		<?php if(isset($erreur['uploaderror']) && $erreur['uploaderror']!='') echo '<p>Erreur : '.$erreur['uploaderror'].'</p>'; ?>
		<?php if(isset($erreur['verif_code']) && $erreur['verif_code']!='') echo '<p>Erreur : '.$erreur['verif_code'].'</p>'; ?>
		<?php if(isset($msgok) && $msgok!='') echo '<p>'.$msgok.'</p>'; ?>
		<form method="post" action=""> 	
			<fieldset>
				<label>Date du comité ou du bureau * : </label>
				<?php
					if(count($lister)!=0){
						$tabmois=array('01'=>'janvier','02'=>'février','03'=>'mars','04'=>'avril','05'=>'mai','06'=>'juin','07'=>'juillet','08'=>'août','09'=>'septembre','10'=>'octobre','11'=>'novembre','12'=>'décembre');
						echo '<select name="reunion"><option value="">-- Choisir --</option>';
						foreach($lister as $k => $v){
							$d = $v['date'];
							$tab = explode('-', $d);
							$d = $tab[2].' '.$tabmois[$tab[1]].' '.$tab[0];
							$selected='';
							if(isset($_POST['reunion']) && $_POST['reunion']==$v['id_reunion']){$selected='selected="selected"';}elseif(!(isset($_POST['reunion'])) && isset($_SESSION['recherchedeliberation']) && $_SESSION['recherchedeliberation']==$v['id_reunion']){$selected='selected="selected"';}
							echo '<option value="'.$v['id_reunion'].'" '.$selected.' class="type'.$v['id_type_reunion'].'">'.$v['libelle'].' du '.$d.'</option>';
						}
						echo '</select>';
					}
				?>
				<?php if($hasError && isset($erreur['reunion'])){ echo '<p class="erreur">'.$erreur['reunion'].'</p>';} ?><br/>
				
				<input type="submit" value="Rechercher" name="enregistrement" />
			</fieldset>
		</form>
		
		<?php if(isset($_POST['enregistrement']) || isset($_SESSION['recherchedeliberation'])){if(count($listedelib)!=0){
				echo '<ul>';
				foreach($listedelib as $k => $v){
					echo '<li><a href="modifdeliberation.php?d='.$v['id_deliberation'].'">'.$v['libelle'].'</a></li>';
				}
				echo '</ul>';
			
		}else{echo '<p>Aucun résultat.</p>';} }?>
	</section>

<?php

require_once('footer.php');
		}
	}

?>

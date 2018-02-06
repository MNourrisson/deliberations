<?php 
	session_start();
	include_once('classes/connect.php');
	include_once('classes/Utilisateur.php');
	include_once('classes/password.php');
	include_once('classes/Charte.php');
	$current='ajoutcharte';
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
			$hasError =false;
			$erreur=array();
			$msgok=$erreurmsg='';
			if(isset($_POST['enregistrement']))
			{
				if(isset($_POST['charte']) && $_POST['charte']=='')
				{
					$hasError =true;
					$erreur['charte'] = 'Veuillez rentrer un nom de charte';
				}
				
				if(!$hasError)
				{
					$c = new Charte();
					$charte = trim($_POST['charte']);
							
					$idcharte = $c->addCharte($charte,$_POST['defaut']);
					if($_POST['defaut']=='1'){
						//update all chartes
						$up1= $c->upCharteDefaut();
						if($up1){
							$up2 = $c->upCharte($idcharte,$charte,$_POST['defaut']);
						}
						//update this charte
					}
					
					
					if($idcharte)
					{
						$msgok = 'Charte ajoutée.'; 
					}
					else{
						$erreurmsg='Erreur lors de l\'enregistrement de la charte';
					}
					
					
				}
	}
	require_once('head.php');
	require_once('nav.php');
?>
	<section id="content">
		<h2>Ajouter une charte</h2>
		
		<p class="indication">Indication : Les champs suivis de * sont obligatoires.</p>
		<?php if(isset($erreurmsg) && $erreurmsg!='') echo '<p>Erreur : '.$erreurmsg.'</p>'; ?>
		<?php if(isset($msgok) && $msgok!='') echo '<p>'.$msgok.'</p>'; ?>
		<form method="post" action=""> 	
			<fieldset>
				<label for="charte">Nom de la charte * :</label>			
				<input type="text" value="<?php if(isset($_POST['charte']) && $_POST['charte']!=''){echo $_POST['charte'];}?>" name="charte" id="charte"/>
				<?php if($hasError && isset($erreur['charte'])){ echo '<p class="erreur">'.$erreur['charte'].'</p>';} ?><br/>
				<label>Charte par défaut : </label><input type="radio" value="1" name="defaut" id="defaut_oui"/><label for="defaut_oui">Oui</label><input type="radio" value="0" name="defaut" id="defaut_non" checked="checked" /><label for="defaut_non">Non</label><br/>
				<input type="submit" value="Enregistrer" name="enregistrement" />
			</fieldset>
		</form>
	</section>
	
<?php

require_once('footer.php');
		}
	}

?>

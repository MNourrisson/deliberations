<?php 
	session_start();
	include_once('classes/connect.php');
	include_once('classes/Utilisateur.php');
	include_once('classes/password.php');
	include_once('classes/Charte.php');
	$current='modifcharte';
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
			$id = intval($_GET['c']);
			$infocharte = $c->getCharteById($id);
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
					if($_POST['defaut']=='1'){
						$up1= $c->upCharteDefaut();
					}
					$charte = trim($_POST['charte']);
								
					$up = $c->upCharte($id,$charte,$_POST['defaut']);
					if($up)
					{
						$msgok = 'Charte modifiée.'; 
					}
					else{
						$erreurmsg='Erreur lors de la mise à jour de la charte';
					}
					
					
				}
	}
	require_once('head.php');
	require_once('nav.php');
?>
	<section id="content">
		<h2>Modifier la charte</h2>
		
		<p class="indication">Indication : Les champs suivis de * sont obligatoires.</p>
		<?php if(isset($erreurmsg) && $erreurmsg!='') echo '<p>Erreur : '.$erreurmsg.'</p>'; ?>
		<?php if(isset($msgok) && $msgok!='') echo '<p>'.$msgok.'</p>'; ?>
		<form method="post" action=""> 	
			<fieldset>
				<label for="charte">Nom de la charte * : </label>			
				<input type="text" value="<?php if(isset($_POST['charte']) && $_POST['charte']!=''){echo $_POST['charte'];}elseif($infocharte['libelle']!='') echo $infocharte['libelle'];?>" name="charte" id="charte"/>
				<?php if($hasError && isset($erreur['charte'])){ echo '<p class="erreur">'.$erreur['charte'].'</p>';} ?><br/>
				<label>Charte par défaut : </label>
				<input type="radio" value="1" name="defaut" id="defaut_oui" <?php if(isset($_POST['defaut']) && $_POST['defaut']=='1'){echo 'checked="checked"';}elseif(!isset($_POST['defaut']) && $infocharte['defaut']=='1'){echo 'checked="checked"';} ?>/><label for="defaut_oui">Oui</label>
				<input type="radio" value="0" name="defaut" id="defaut_non" <?php if(isset($_POST['defaut']) && $_POST['defaut']=='0'){echo 'checked="checked"';}elseif(!isset($_POST['defaut']) && $infocharte['defaut']=='0'){echo 'checked="checked"';} ?> /><label for="defaut_non">Non</label><br/>
				<input type="submit" value="Enregistrer" name="enregistrement" />
			</fieldset>
		</form>
	</section>
	
<?php

require_once('footer.php');
		}
	}

?>

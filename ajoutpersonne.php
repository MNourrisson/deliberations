<?php 
	session_start();
	include_once('classes/connect.php');
	include_once('classes/Utilisateur.php');
	include_once('classes/password.php');
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
			$current='';
			$hasError =false;
			$erreur=array();
			$msgok='';
			if(isset($_POST['connexion']))
			{
				if(isset($_POST['email']) && $_POST['email']=='')
				{
					$hasError =true;
					$erreur['email'] = 'Veuillez rentrer un email';
				}else if(!filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL)) {
					$erreur['email'] = 'Vous avez entré une adresse jugée invalide.';
					$hasError = true;
				}
				if(isset($_POST['mdp']) && $_POST['mdp']=='')
				{
					$hasError =true;
					$erreur['mdp'] = 'Veuillez rentrer un mot de passe.';
				}
				if(!$hasError)
				{
					$personne = new Utilisateur();
					$infos = $personne->identification(trim($_POST['email']));
					
					if(count($infos)==0)
					{
						$email = trim($_POST['email']);
						$motdepasse = trim($_POST['mdp']);
						
						if($motdepasse = password_hash($motdepasse,PASSWORD_DEFAULT))
						{
							$newpers = $personne->addPersonne($email,$motdepasse);
							//print_r($dbh->errorInfo());
							if($newpers)
							{
								$msgok = 'Utilisateur ajouté.'; 
							}
						}
						else {
							$hasError =true;
							$erreur['verif_code'] = 'Pb sur le mot de passe'; 
						}
					}
					else
					{
						$hasError =true;
						$erreur['verif_email'] = 'Un compte existe déjà pour cette adresse email.'; 
					}
				}
	}
	require_once('head.php');
	require_once('nav.php');
?>
	<section id="content">
		<h2>Créer un compte</h2>
		
		<p>Indication : Les champs suivis de * sont obligatoires.</p>
		<?php if(isset($erreurmsg) && $erreurmsg!='') echo '<p>Erreur : '.$erreurmsg.'</p>'; ?>
		<?php if(isset($msgok) && $msgok!='') echo '<p>'.$msgok.'</p>'; ?>
		<form method="post" action=""> 	
			<label for="email">Email *</label>			
			<input type="text" value="<?php if(isset($_POST['email']) && $_POST['email']!=''){echo $_POST['email'];}?>" name="email" id="email"/>
			<?php if($hasError && isset($erreur['email'])){ echo '<p class="erreur">'.$erreur['email'].'</p>';} ?>
		
			<label for="mdp">Mot de passe *</label>
			<input type="password" value="" name="mdp" id="mdp"/><br/><?php if($hasError && isset($erreur['mdp'])){ echo '<p class="erreur">'.$erreur['mdp'].'</p>';} ?>			
			<input type="submit" value="Ajouter" name="connexion"/>
		</form>
	</section>
	
<?php

require_once('footer.php');
		}
	}

?>

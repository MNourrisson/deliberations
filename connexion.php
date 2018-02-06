<?php
	error_reporting(E_ALL);
	ini_set('display_errors', TRUE);
	ini_set('display_startup_errors', TRUE);
	session_start();

	include_once('classes/connect.php');
	include_once('classes/Utilisateur.php');
	include_once('classes/password.php');

	$current='';
	$message='';
	if(isset($_POST['connexion']) && $_POST['connexion']=='Connexion')	
	{	
		$message='';
		if (empty($_POST['email']) || empty($_POST['mdp']) ) 
		{
			$message = 'Une erreur s\'est produite pendant votre identification. Vous devez remplir tous les champs.';
		}
		else //On check le mot de passe
		{
			
			$pers = new Utilisateur();
			$iden = $pers->identification($_POST['email']);

			if(count($iden) == 1)
			{
				if(password_verify(trim($_POST['mdp']), $iden[0]['mdp']) && preg_match("#^[a-z]+.[-a-z]{2,}+@parc-livradois-forez.org$#",$_POST['email']))
				{
					$_SESSION['email'] = $iden[0]['email'];
					$_SESSION['id'] = $iden[0]['id_utilisateur'];
					$_SESSION['droit'] = 1;
					setcookie('email', $iden[0]['email'], time() + 365*24*3600, null, null, false, true);
					setcookie('id', $iden[0]['id_utilisateur'], time() + 365*24*3600, null, null, false, true);
					setcookie('droit', 1, time() + 365*24*3600, null, null, false, true);
					if(isset($_SESSION['page']) && $_SESSION['page']!='')
					{
						header('Location: '.$_SESSION['page']);
					}
					else
					{
						header('Location: index.php');
					}
				}
				else // Acces pas OK !
				{
					$message = 'Une erreur s\'est produite pendant votre identification.<br/> Le mot de passe ou le pseudo est incorrect.';
				}
			}
			else
			{
				$message='Aucun utilisateur ne correspond';
			}
		}
	} 
	include_once('head.php');
	include_once('nav.php');
	?>
	<section id="content">
		<h2>Connexion</h2>
		<p class="erreur"><?php echo $message; ?></p>
		<p class="indication">Les champs suivis de * sont obligatoires.</p>
		<form method="post" action="" id="formconnect">
			<fieldset>
				<label for="email">Email<span class="obligatoire">*</span></label><input type="text" value="" name="email" id="email" /><br/>
				<label for="mdp">Mot de passe<span class="obligatoire">*</span></label><input type="password" value="" name="mdp" id="mdp" /><br/>
				<input type="submit" value="Connexion" name="connexion"/>
			</fieldset>
		</form>
	</section>
	
<?php	
	
	include_once('footer.php');
?>






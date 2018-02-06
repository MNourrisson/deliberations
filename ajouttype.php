<?php 
	session_start();
	include_once('classes/connect.php');
	include_once('classes/Utilisateur.php');
	include_once('classes/password.php');
	include_once('classes/Reunion.php');
	$current='ajouttype';
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
			$tr = new TypeReunion();
			$listetr = $tr->getTypesReunion();
			if(isset($_POST['enregistrement']))
			{
				if(isset($_POST['libelle']) && $_POST['libelle']=='')
				{
					$hasError =true;
					$erreur['libelle'] = 'Veuillez rentrer un nom.';
				}
				
				if(!$hasError)
				{
					$libelle = trim($_POST['libelle']);
					$parent = $_POST['parent'];	
					$idtr = $tr->addTypeReunion($libelle,$parent);
					
					if($idtr)
					{
						$msgok = 'Type ajouté.'; 
					}
					else{
						$erreurmsg='Erreur lors de l\'enregistrement. ';
					}
				}
	}
	require_once('head.php');
	require_once('nav.php');
?>
	<section id="content">
		<h2>Ajouter un type de réunion</h2>
		
		<p>Indication : Les champs suivis de * sont obligatoires.</p>
		<?php if(isset($erreurmsg) && $erreurmsg!='') echo '<p>Erreur : '.$erreurmsg.'</p>'; ?>
		<?php if(isset($msgok) && $msgok!='') echo '<p>'.$msgok.'</p>'; ?>
		<form method="post" action=""> 	
			<fieldset>
				<label for="libelle">Libellé * : </label><input type="text" value="<?php if(isset($_POST['libelle']) && $_POST['libelle']!=''){echo $_POST['libelle'];}?>" name="libelle" id="libelle"/>
				<?php if($hasError && isset($erreur['libelle'])){ echo '<p class="erreur">'.$erreur['libelle'].'</p>';} ?><br/>
				<label>Parent : </label>
				<select name="parent">
				<?php
				if(count($listetr)!=0){
					echo '<option value="0">Aucun parent</option>';
					foreach($listetr as $k => $v){
						$selected='';
						if(isset($_POST['parent']) && $_POST['parent']==$v['id_type_reunion']){$selected='selected="selected"';}
						echo '<option value="'.$v['id_type_reunion'].'" '.$selected.' >'.$v['libelle'].'</option>';
					}
				}else{
					echo '<option value="0">Aucun choix possible</option>';
				}
				?></select><br/>
				<input type="submit" value="Enregistrer" name="enregistrement" />
			</fieldset>
		</form>
	</section>
	
<?php

require_once('footer.php');
		}
	}

?>

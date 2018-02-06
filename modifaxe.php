<?php 
	session_start();
	include_once('classes/connect.php');
	include_once('classes/Utilisateur.php');
	include_once('classes/password.php');
	include_once('classes/Charte.php');
	$current='modifaxe';
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
			$c = new Charte();
			$listecharte = $c->getChartes();
			$a = new Axe();
			$listeAxe = $a->getAxes();
			$infoAxe = $a->getAxeById($_GET['a']);
			if(isset($_POST['enregistrement']))
			{
				if(isset($_POST['axe']) && $_POST['axe']=='')
				{
					$hasError =true;
					$erreur['axe'] = 'Veuillez rentrer un nom d\'axe.';
				}
				if(isset($_POST['charte']) && $_POST['charte']=='')
				{
					$hasError =true;
					$erreur['charte'] = 'Veuillez choisir une charte.';
				}
				
				if(!$hasError)
				{
					$axe = htmlspecialchars(trim($_POST['axe']),ENT_QUOTES);
					
					$parent = $_POST['parent'];
					
					$niveau = $_POST['niveau'];		
					
					$idaxe = $a->upAxe($infoAxe['id_axe'],$axe,$_POST['charte'],$parent,$niveau);
					
					if($idaxe)
					{
						$msgok = 'Axe mis à jour.'; 
					}
					else{
						$erreurmsg='Erreur lors de l\'enregistrement de l\'axe. ';
					}
					
				}
	}
	require_once('head.php');
	require_once('nav.php');
?>
	<section id="content">
		<h2>Modifier un axe</h2>
		
		<p class="indication">Indication : Les champs suivis de * sont obligatoires.</p>
		<?php if(isset($erreurmsg) && $erreurmsg!='') echo '<p>Erreur : '.$erreurmsg.'</p>'; ?>
		<?php if(isset($msgok) && $msgok!='') echo '<p>'.$msgok.'</p>'; ?>
		<form method="post" action=""> 	
			<fieldset>
				<label for="axe">Nom de l'axe * : </label><input type="text" value="<?php if(isset($_POST['axe']) && $_POST['axe']!=''){echo htmlspecialchars($_POST['axe']);} elseif($infoAxe['libelle']!='') echo $infoAxe['libelle'];?>" name="axe" id="axe"/>
				<?php if($hasError && isset($erreur['axe'])){ echo '<p class="erreur">'.$erreur['axe'].'</p>';} ?><br/>
				<label for="charte">Charte * : </label>
				<?php
				if(count($listecharte)!=0){
					echo '<select name="charte"><option value="">-- Choisir --</option>';
					foreach($listecharte as $k => $v){
						$selected='';
						if(isset($_POST['charte']) && $_POST['charte']==$v['id_charte']){$selected='selected="selected"';}elseif(!isset($_POST['charte']) && $infoAxe['id_charte']==$v['id_charte']){$selected='selected="selected"';}
						echo '<option value="'.$v['id_charte'].'" '.$selected.' >'.$v['libelle'].'</option>';
					}
					echo '</select>';
				}
				?>
				<?php if($hasError && isset($erreur['charte'])){ echo '<p class="erreur">'.$erreur['charte'].'</p>';} ?><br/>
				<label>Parent : </label>
				<select name="parent">
				<?php
				if(count($listeAxe)!=0){
					echo '<option value="0">Aucun parent</option>';
					foreach($listeAxe as $k => $v){
						$selected='';
						if(isset($_POST['parent']) && $_POST['parent']==$v['id_axe']){$selected='selected="selected"';}elseif(!isset($_POST['parent']) && $infoAxe['parent']==$v['id_axe']){$selected='selected="selected"';}
						echo '<option value="'.$v['id_axe'].'" '.$selected.' >'.$v['libelle'].'</option>';
					}
				}else{
					echo '<option value="0">Aucun choix possible</option>';
				}
				?></select><br/>
				<label for="niveau">Niveau : </label><input type="number" name="niveau" min="1" max="4" value="<?php if(isset($_POST['niveau']) && $_POST['niveau']!=''){echo $_POST['niveau'];}elseif($infoAxe['niveau']!='') echo $infoAxe['niveau'];?>"/><br/>
				<input type="submit" value="Enregistrer" name="enregistrement" />
			</fieldset>
		</form>
	</section>
	
<?php

require_once('footer.php');
		}
	}

?>

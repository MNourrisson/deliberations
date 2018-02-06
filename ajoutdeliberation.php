<?php 
	session_start();
	include_once('classes/connect.php');
	include_once('classes/Utilisateur.php');
	include_once('classes/password.php');
	include_once('classes/Deliberation.php');
	include_once('classes/Reunion.php');
	include_once('classes/Charte.php');

	$current='ajoutdeliberation';
	
	function listesDescendances($ressources,$parent,&$tabD){
		$tmp = $ressources->getDescendances($parent);
		if(count($tmp)!=0){
			foreach($tmp as $k => $v){
				$id = $v['id_axe'];
				$parent = $v['parent'];
				$tabD[$parent][$id]=$v['libelle'];
				$tmp=listesDescendances($ressources,$id,$tabD);
				if($tmp==null)
				{
					$tabD[$parent][$id]=$v['libelle'];
				}
			}
			return $tabD;
		}else{
			return NULL;
		}
	}
	function mafunction($liste,$cle,&$montext,$parent){
		
		if(isset($liste[$cle]) && count($liste[$cle])!=0){
			foreach($liste[$cle] as $k => $v){
				$selected='';
				if(isset($_POST['axe']) && $_POST['axe']==$k){$selected='selected="selected"';}
				$montext.='<option value="'.$k.'" '.$selected.'>'.$v.'</option>';
				mafunction($liste,$k,$montext,$cle);
			}
		}
	}
	
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
			
			$r = new Reunion();
			$lister = $r->getInfosReunionByDate();
			
			$a  = new Axe();
			$listeaxes = $a->getAxes();
			$tabtest = array();
			$listes2 = listesDescendances($a,0,$tabtest);
		
			$montext='<select name="axe"><option value="">-- Choisir --</option>';
			if(count($listes2)!=0)
			{
				foreach($listes2[0] as $k => $v){
					$selected='';
					if(isset($_POST['axe']) && $_POST['axe']==$k){$selected='selected="selected"';}
					$montext.='<option value="'.$k.'" '.$selected.' class="typeaxe">'.$v.'</option>';
					mafunction($listes2,$k,$montext,0);
				}
			}
			$montext.='</select>';
		
			if(isset($_POST['enregistrement']))
			{
				if(isset($_POST['reunion']) && $_POST['reunion']=='')
				{
					$hasError =true;
					$erreur['reunion'] = 'Veuillez choisir une réunion.';
				}
				if(isset($_POST['axe']) && $_POST['axe']=='')
				{
					$hasError =true;
					$erreur['axe'] = 'Veuillez choisir un axe.';
				}
				if(isset($_POST['num_delib']) && $_POST['num_delib']=='')
				{
					$hasError =true;
					$erreur['num_delib'] = 'Veuillez rentrer un numéro de délibération.';
				}
				if(isset($_POST['libelle']) && $_POST['libelle']=='')
				{
					$hasError =true;
					$erreur['libelle'] = 'Veuillez rentrer un nom.';
				}
				
				if(!$hasError)
				{
					$d = new Deliberation();
								
					$idd = $d->addDeliberation($_POST['reunion'],trim($_POST['libelle']),$_POST['num'],$_POST['num_delib'],$_POST['axe'],$_POST['folio']);
				
					if($idd)
					{
						$msgok = 'Délibération ajoutée.'; 					
						$f = new Fichier();
						$upload_dir = '/your/upload/directory/';
					
						$namefic='';
						if ($_FILES["delib"]["error"] == UPLOAD_ERR_OK) 
						{
							$tmp_name = $_FILES["delib"]["tmp_name"];
							$nameext = $_FILES["delib"]["name"];
							$tempext1 = substr(strrchr($nameext,"."),1);
							$namefic=$nameext;
							$caracteres = array('À' => 'a', 'Á' => 'a', 'Â' => 'a', 'Ä' => 'a', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ä' => 'a', '@' => 'a','È' => 'e', 'É' => 'e', 'Ê' => 'e', 'Ë' => 'e', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', '€' => 'e','Ì' => 'i', 'Í' => 'i', 'Î' => 'i', 'Ï' => 'i', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i','Ò' => 'o', 'Ó' => 'o', 'Ô' => 'o', 'Ö' => 'o', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'ö' => 'o','Ù' => 'u', 'Ú' => 'u', 'Û' => 'u', 'Ü' => 'u', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'µ' => 'u','Œ' => 'oe', 'œ' => 'oe','$' => 's');

							$namefic = strtr($namefic, $caracteres);
							$namefic = preg_replace('#[^A-Za-z0-9]+#', '-', $namefic);
							$namefic = trim($namefic, '-');
							$namefic= $namefic.'_'.rand().'.'.$tempext1;
							
							if(move_uploaded_file($tmp_name,$upload_dir.$namefic)==false)
							{
								$erreur['uploaderror']='Problème sur le transfert.';
								$hasError=true;
							}
							else{
								$requete = $f->addFichier($nameext,$namefic);
								if($requete)
								{	
									$idf = $d->upDeliberationFichierDelib($idd,$requete);
									if($idf)
									{
										$msgok .= ' Délibération ajoutée.'; 
									}
									else{
										$erreurmsg .='Erreur lors de l\'enregistrement de la délib. ';
									}
								}
								else{
									$hasError =true;
									$erreur['verif_code'] = 'Erreur lors de l\'upload du fichier.'; 
								}
							}								
						}
						$namefic='';
						if ($_FILES["budget"]["error"] == UPLOAD_ERR_OK) 
						{
							$tmp_name = $_FILES["budget"]["tmp_name"];
							$nameext = $_FILES["budget"]["name"];
							$tempext1 = substr(strrchr($nameext,"."),1);
							$namefic=$nameext;
							$caracteres = array('À' => 'a', 'Á' => 'a', 'Â' => 'a', 'Ä' => 'a', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ä' => 'a', '@' => 'a','È' => 'e', 'É' => 'e', 'Ê' => 'e', 'Ë' => 'e', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', '€' => 'e','Ì' => 'i', 'Í' => 'i', 'Î' => 'i', 'Ï' => 'i', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i','Ò' => 'o', 'Ó' => 'o', 'Ô' => 'o', 'Ö' => 'o', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'ö' => 'o','Ù' => 'u', 'Ú' => 'u', 'Û' => 'u', 'Ü' => 'u', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'µ' => 'u','Œ' => 'oe', 'œ' => 'oe','$' => 's');

							$namefic = strtr($namefic, $caracteres);
							$namefic = preg_replace('#[^A-Za-z0-9]+#', '-', $namefic);
							$namefic = trim($namefic, '-');
							$namefic= $namefic.'_'.rand().'.'.$tempext1;
							
							if(move_uploaded_file($tmp_name,$upload_dir.$namefic)==false)
							{
								$erreur['uploaderror']='Problème sur le transfert.';
								$hasError=true;
							}
							else{
								$requete = $f->addFichier($nameext,$namefic);
								if($requete)
								{	
									$idf = $d->upDeliberationFichierBudget($idd,$requete);
									if($idf)
									{
										$msgok .= 'Budget ajouté.'; 
									}
									else{
										$erreurmsg.='Erreur lors de l\'enregistrement du budget. ';
									}
								}
								else{
									$hasError =true;
									$erreur['verif_code'] = 'Erreur lors de l\'upload du fichier.'; 
								}
							}								
						}
						$namefic='';
						if ($_FILES["emargement"]["error"] == UPLOAD_ERR_OK) 
						{
							$tmp_name = $_FILES["emargement"]["tmp_name"];
							$nameext = $_FILES["emargement"]["name"];
							$tempext1 = substr(strrchr($nameext,"."),1);
							$namefic=$nameext;
							$caracteres = array('À' => 'a', 'Á' => 'a', 'Â' => 'a', 'Ä' => 'a', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ä' => 'a', '@' => 'a','È' => 'e', 'É' => 'e', 'Ê' => 'e', 'Ë' => 'e', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', '€' => 'e','Ì' => 'i', 'Í' => 'i', 'Î' => 'i', 'Ï' => 'i', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i','Ò' => 'o', 'Ó' => 'o', 'Ô' => 'o', 'Ö' => 'o', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'ö' => 'o','Ù' => 'u', 'Ú' => 'u', 'Û' => 'u', 'Ü' => 'u', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'µ' => 'u','Œ' => 'oe', 'œ' => 'oe','$' => 's');

							$namefic = strtr($namefic, $caracteres);
							$namefic = preg_replace('#[^A-Za-z0-9]+#', '-', $namefic);
							$namefic = trim($namefic, '-');
							$namefic= $namefic.'_'.rand().'.'.$tempext1;
							
							if(move_uploaded_file($tmp_name,$upload_dir.$namefic)==false)
							{
								$erreur['uploaderror']='Problème sur le transfert.';
								$hasError=true;
							}
							else{
								$requete = $f->addFichier($nameext,$namefic);
								if($requete)
								{	
									$idf = $d->upDeliberationFichierEmargement($idd,$requete);
									if($idf)
									{
										$msgok .= 'Emargement ajouté.'; 
									}
									else{
										$erreurmsg.='Erreur lors de l\'enregistrement du fichier émargement. ';
									}
								}
								else{
									$hasError =true;
									$erreur['verif_code'] = 'Erreur lors de l\'upload du fichier.'; 
								}
							}								
						}
					}
					else{
						$erreurmsg .='Erreur lors de l\'enregistrement. ';
					}
					
				}
			}
	require_once('head.php');
	require_once('nav.php');
?>
	<section id="content">
		<h2>Ajouter une délibération</h2>
		
		<p class="indication">Indication : Les champs suivis de * sont obligatoires.</p>
		<?php if(isset($erreurmsg) && $erreurmsg!='') echo '<p>Erreur : '.$erreurmsg.'</p>'; ?>
		<?php if(isset($erreur['uploaderror']) && $erreur['uploaderror']!='') echo '<p>Erreur : '.$erreur['uploaderror'].'</p>'; ?>
		<?php if(isset($erreur['verif_code']) && $erreur['verif_code']!='') echo '<p>Erreur : '.$erreur['verif_code'].'</p>'; ?>
		<?php if(isset($msgok) && $msgok!='') echo '<p>'.$msgok.'</p>'; ?>
		<form method="post" action=""  enctype="multipart/form-data"> 	
			<fieldset>
				
				<label>Réunion * : </label>
				<?php
					if(count($lister)!=0){
						$tabmois=array('01'=>'janvier','02'=>'février','03'=>'mars','04'=>'avril','05'=>'mai','06'=>'juin','07'=>'juillet','08'=>'août','09'=>'septembre','10'=>'octobre','11'=>'novembre','12'=>'décembre');
						echo '<select name="reunion"><option value="">-- Choisir --</option>';
						foreach($lister as $k => $v){
							$d = $v['date'];
							$tab = explode('-', $d);
							$d = $tab[2].' '.$tabmois[$tab[1]].' '.$tab[0];
							$selected='';
							if(isset($_POST['reunion']) && $_POST['reunion']==$v['id_reunion']){$selected='selected="selected"';}
							echo '<option value="'.$v['id_reunion'].'" '.$selected.' class="type'.$v['id_type_reunion'].'">'.$v['libelle'].' du '.$d.'</option>';
						}
						echo '</select>';
					}
				?>
				<?php if($hasError && isset($erreur['reunion'])){ echo '<p class="erreur">'.$erreur['reunion'].'</p>';} ?><br/>
				<label for="libelle">Libellé : </label><input type="text" value="<?php if(isset($_POST['libelle']) && $_POST['libelle']!=''){echo $_POST['libelle'];}?>" name="libelle" id="libelle"/>
				<?php if($hasError && isset($erreur['libelle'])){ echo '<p class="erreur">'.$erreur['libelle'].'</p>';} ?><br/>
				<label for="num">Numéro : </label><input type="text" value="<?php if(isset($_POST['num']) && $_POST['num']!=''){echo $_POST['num'];}?>" name="num" id="num"/>
				<?php if($hasError && isset($erreur['num'])){ echo '<p class="erreur">'.$erreur['num'].'</p>';} ?><br/>
				<label for="num_delib">Numéro délibération * : </label><input type="text" value="<?php if(isset($_POST['num_delib']) && $_POST['num_delib']!=''){echo $_POST['num_delib'];}?>" name="num_delib" id="num_delib"/>
				<?php if($hasError && isset($erreur['num_delib'])){ echo '<p class="erreur">'.$erreur['num_delib'].'</p>';} ?><br/>
				<label>Axe * : </label>
				<?php 
					echo $montext;
				?><?php if($hasError && isset($erreur['axe'])){ echo '<p class="erreur">'.$erreur['axe'].'</p>';} ?><br/>
				<br/>
				<label for="folio">Folio : </label><input type="text" value="<?php if(isset($_POST['folio']) && $_POST['folio']!=''){echo $_POST['folio'];}?>" name="folio" id="folio"/>
				<?php if($hasError && isset($erreur['folio'])){ echo '<p class="erreur">'.$erreur['folio'].'</p>';} ?><br/>
				<label for="delib">Fichier</label><input type="file" name="delib" id="delib"/><br/>
				<label for="budget">Document budgétaire</label><input type="file" name="budget" id="budget"/><br/>	
				<label for="emargement">Feuille émargement</label><input type="file" name="emargement" id="emargement"/><br/>
				<input type="submit" value="Enregistrer" name="enregistrement" />
			</fieldset>
		</form>
	</section>

<?php

require_once('footer.php');
		}
	}

?>

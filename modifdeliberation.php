<?php 
	session_start();
	include_once('classes/connect.php');
	include_once('classes/Utilisateur.php');
	include_once('classes/password.php');
	include_once('classes/Deliberation.php');
	include_once('classes/Reunion.php');
	include_once('classes/Charte.php');

	$current='modifdeliberation';
	
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
	function mafunction($liste,$cle,&$montext,$parent,$infodelib){
		
		if(isset($liste[$cle]) && count($liste[$cle])!=0){
			foreach($liste[$cle] as $k => $v){
				$selected='';
				if(isset($_POST['axe']) && $_POST['axe']==$k){$selected='selected="selected"';}elseif(!isset($_POST['axe']) && $infodelib['id_axe']==$k){$selected='selected="selected"';}
				$montext.='<option value="'.$k.'" '.$selected.'>'.$v.'</option>';
				mafunction($liste,$k,$montext,$cle,$infodelib);
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
			$iddelib = intval($_GET['d']);
			$hasError =false;
			$erreur=array();
			$msgok=$erreurmsg='';
			$d = new Deliberation();
			$f = new Fichier();
			$infodelib = $d->getInfosDelibById($iddelib);
			if($infodelib['id_fichier']!=0){
				$infofichier = $f->getFichierById($infodelib['id_fichier']);
			}else{
				$infofichier=0;
			}
			if($infodelib['id_budget']!=0){
				$infofichierbudget = $f->getFichierById($infodelib['id_budget']);
			}else{
				$infofichierbudget=0;
			}
			if($infodelib['id_emargement']!=0){
				$infofichieremargement = $f->getFichierById($infodelib['id_emargement']);
			}else{
				$infofichieremargement=0;
			}
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
					if(isset($_POST['axe']) && $_POST['axe']==$k){$selected='selected="selected"';}elseif(!isset($_POST['axe']) && $infodelib['id_axe']==$k){$selected='selected="selected"';}
					$montext.='<option value="'.$k.'" '.$selected.' class="typeaxe">'.$v.'</option>';
					mafunction($listes2,$k,$montext,0,$infodelib);
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
					if($_POST['reunion'] == $infodelib['id_reunion'] && trim($_POST['libelle'])==$infodelib['libelle'] && $_POST['num']==$infodelib['num'] && $_POST['num_delib']==$infodelib['num_delib'] && $_POST['axe']==$infodelib['id_axe'] && $_POST['folio']==$infodelib['folio']){
						$idd=true;
					}
					else{
						$idd = $d->upDeliberation($iddelib,$_POST['reunion'],trim($_POST['libelle']),$_POST['num'],$_POST['num_delib'],$_POST['axe'],$_POST['folio']);	
					}
					
					
					if($idd)
					{
						$upload_dir = '/your/upload/directory/';
					
						$namefic='';
						if ($_FILES["delib"]["error"] == UPLOAD_ERR_OK) 
						{
							//si existe déjà un fichier attaché à la délibération, on supprime
							if(isset($_POST['iddufichier']) && $_POST['iddufichier']!=''){
								$infof=$f->getFichierById($_POST['iddufichier']);
								if(unlink($upload_dir.$infof['nom_reel']))
								{
									$del = $f->delFichierById($_POST['iddufichier']);
									if($del){
										$idf = $d->upDeliberationFichierDelib($iddelib,0);									
										if($idf)
										{
											$infodelib = $d->getInfosDelibById($iddelib);
											if($infodelib['id_fichier']!=0){
												$infofichier = $f->getFichierById($infodelib['id_fichier']);
											}else{
												$infofichier=0;
											}
											$msgok .= ' Fichier delib effacé.'; 
										}
										else{
											$erreurmsg.=' Erreur lors de la modification de la délibération. ';
										}
									}
									else{
										$erreurmsg.=' Erreur sur la suppression du fichier delib dans la base.';
									}
								}
								else{
									$erreurmsg.=' Erreur sur la suppression du fichier dans le dossier.';
								}
							}
							//puis on traite le nouveau fichier			
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
								$erreur['uploaderror'].=' Problème sur le transfert du fichier delib.';
								$hasError=true;
							}
							else{
								$requete = $f->addFichier($nameext,$namefic);
								if($requete)
								{	
									$idf = $d->upDeliberationFichierDelib($iddelib,$requete);
									if($idf)
									{
										$infodelib = $d->getInfosDelibById($iddelib);
										if($infodelib['id_fichier']!=0){
											$infofichier = $f->getFichierById($infodelib['id_fichier']);
										}else{
											$infofichier=0;
										}
										$msgok .= 'Fichier delib ajouté.'; 
									}
									else{
										$erreurmsg .='Erreur lors de la mise à jour du fichier délib. ';
									}
								}
								else{
									$hasError =true;
									$erreur['verif_code'] .= 'Erreur lors de l\'upload du fichier du fichier delib.'; 
								}
							}								
						}elseif(isset($_POST['supprf']) && $_POST['supprf']=='supprimer'){
							//supprimer le fichier attaché
							$infof=$f->getFichierById($_POST['iddufichier']);
							if(unlink($upload_dir.$infof['nom_reel']))
							{
								$del = $f->delFichierById($_POST['iddufichier']);
								if($del){
									$idf = $d->upDeliberationFichierDelib($iddelib,0);
						
									if($idf)
									{
										$infodelib = $d->getInfosDelibById($iddelib);
										if($infodelib['id_fichier']!=0){
											$infofichier = $f->getFichierById($infodelib['id_fichier']);
										}else{
											$infofichier=0;
										}
										$msgok .= 'Fichier delib effacé.'; 
									}
									else{
										$erreurmsg.=' Erreur lors de la modification du fichier délibération. ';
									}
								}
								else{
									$erreurmsg.='Erreur sur la suppression du fichier delib dans la base.';
								}
							}
							else{
								$erreurmsg.='Erreur sur la suppression du fichier delib dans le dossier.';
							}
						}
						$namefic='';
						if ($_FILES["budget"]["error"] == UPLOAD_ERR_OK) 
						{
							//si existe déjà un fichier attaché à la délibération, on supprime
							if(isset($_POST['iddufichierb']) && $_POST['iddufichierb']!=''){
								$infof=$f->getFichierById($_POST['iddufichierb']);
								if(unlink($upload_dir.$infof['nom_reel']))
								{
									$del = $f->delFichierById($_POST['iddufichierb']);
									if($del){
										$idf = $d->upDeliberationFichierBudget($iddelib,0);									
										if($idf)
										{
											$infodelib = $d->getInfosDelibById($iddelib);
											if($infodelib['id_budget']!=0){
												$infofichierbudget = $f->getFichierById($infodelib['id_budget']);
											}else{
												$infofichierbudget=0;
											}
											$msgok .= 'Fichier budget effacé.'; 
										}
										else{
											$erreurmsg.='Erreur lors de la modification du fichier budget. ';
										}
									}
									else{
										$erreurmsg.='Erreur sur la suppression du fichier budget dans la base.';
									}
								}
								else{
									$erreurmsg.='Erreur sur la suppression du fichier budget dans le dossier.';
								}
							}
							//puis on traite le nouveau fichier			
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
								$erreur['uploaderror'].='Problème sur le transfert du budget.';
								$hasError=true;
							}
							else{
								$requete = $f->addFichier($nameext,$namefic);
								if($requete)
								{	
									$idf = $d->upDeliberationFichierBudget($iddelib,$requete);
								
									if($idf)
									{
										$infodelib = $d->getInfosDelibById($iddelib);
										if($infodelib['id_budget']!=0){
											$infofichierbudget = $f->getFichierById($infodelib['id_budget']);
										}else{
											$infofichierbudget=0;
										}
										$msgok .= 'Fichier budget ajouté.'; 
									}
									else{
										$erreurmsg.='Erreur lors de la mise à jour du fichier budget. ';
									}
								}
								else{
									$hasError =true;
									$erreur['verif_code'] .= 'Erreur lors de l\'upload du fichier budget.'; 
								}
							}								
						}elseif(isset($_POST['supprb']) && $_POST['supprb']=='supprimer'){
							//supprimer le fichier attaché
							$infof=$f->getFichierById($_POST['iddufichierb']);
							if(unlink($upload_dir.$infof['nom_reel']))
							{
								$del = $f->delFichierById($_POST['iddufichierb']);
								if($del){
									$idf = $d->upDeliberationFichierBudget($iddelib,0);
						
									if($idd)
									{
										$infodelib = $d->getInfosDelibById($iddelib);
										if($infodelib['id_budget']!=0){
											$infofichierbudget = $f->getFichierById($infodelib['id_budget']);
										}else{
											$infofichierbudget=0;
										}
										$msgok .= 'Fichier budget effacé.'; 
									}
									else{
										$erreurmsg.='Erreur lors de la modification du fichier budget. ';
									}
								}
								else{
									$erreurmsg.='Erreur sur la suppression du fichier budget dans la base.';
								}
							}
							else{
								$erreurmsg.='Erreur sur la suppression du fichier budget dans le dossier.';
							}
						}
						$namefic='';
						if ($_FILES["emargement"]["error"] == UPLOAD_ERR_OK) 
						{
							//si existe déjà un fichier attaché à la délibération, on supprime
							if(isset($_POST['iddufichiere']) && $_POST['iddufichiere']!=''){
								$infof=$f->getFichierById($_POST['iddufichiere']);
								if(unlink($upload_dir.$infof['nom_reel']))
								{
									$del = $f->delFichierById($_POST['iddufichiere']);
									if($del){
										$idf = $d->upDeliberationFichierEmargement($iddelib,0);									
										if($idf)
										{
											$infodelib = $d->getInfosDelibById($iddelib);
											if($infodelib['id_emargement']!=0){
												$infofichieremargement = $f->getFichierById($infodelib['id_emargement']);
											}else{
												$infofichieremargement=0;
											}
											$msgok .= 'Fichier emargement effacé.'; 
										}
										else{
											$erreurmsg.='Erreur lors de la modification du fichier emargement. ';
										}
									}
									else{
										$erreurmsg.='Erreur sur la suppression du fichier emargement dans la base.';
									}
								}
								else{
									$erreurmsg.='Erreur sur la suppression du fichier emargement dans le dossier.';
								}
							}
							//puis on traite le nouveau fichier			
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
									$idf = $d->upDeliberationFichierEmargement($iddelib,$requete);
								
									if($idf)
									{
										$infodelib = $d->getInfosDelibById($iddelib);
										if($infodelib['id_emargement']!=0){
											$infofichieremargement = $f->getFichierById($infodelib['id_emargement']);
										}else{
											$infofichieremargement=0;
										}
										$msgok .= 'Fichier emargement ajouté.'; 
									}
									else{
										$erreurmsg.='Erreur lors de la mise à jour du fichier emargement. ';
									}
								}
								else{
									$hasError =true;
									$erreur['verif_code'] .= 'Erreur lors de l\'upload du fichier émargement.'; 
								}
							}								
						}elseif(isset($_POST['suppre']) && $_POST['suppre']=='supprimer'){
							//supprimer le fichier attaché
							$infof=$f->getFichierById($_POST['iddufichiere']);
							if(unlink($upload_dir.$infof['nom_reel']))
							{
								$del = $f->delFichierById($_POST['iddufichiere']);
								if($del){
									$idf = $d->upDeliberationFichierEmargement($iddelib,0);
						
									if($idd)
									{
										$infodelib = $d->getInfosDelibById($iddelib);
										if($infodelib['id_emargement']!=0){
											$infofichieremargement = $f->getFichierById($infodelib['id_emargement']);
										}else{
											$infofichieremargement=0;
										}
										$msgok .= 'Fichier émargement effacé.'; 
									}
									else{
										$erreurmsg.='Erreur lors de la modification du fichier émargement. ';
									}
								}
								else{
									$erreurmsg.='Erreur sur la suppression du fichier émargement dans la base.';
								}
							}
							else{
								$erreurmsg.='Erreur sur la suppression du fichier émargement dans le dossier.';
							}
						}
						$msgok .= 'Delib mise à jour.'; 
					}else{
						
						$erreurmsg.='Erreur lors de la modification de la délibération. ';
						
					}
				}
			}
	require_once('head.php');
	require_once('nav.php');
?>
	<section id="content">
		<h2>Modifier la délibération</h2>
		
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
							if(isset($_POST['reunion']) && $_POST['reunion']==$v['id_reunion']){$selected='selected="selected"';}elseif(!isset($_POST['reunion']) && $infodelib['id_reunion']==$v['id_reunion']){$selected='selected="selected"';}
							echo '<option value="'.$v['id_reunion'].'" '.$selected.' class="type'.$v['id_type_reunion'].'">'.$v['libelle'].' du '.$d.'</option>';
						}
						echo '</select>';
					}
				?>
				<?php if($hasError && isset($erreur['reunion'])){ echo '<p class="erreur">'.$erreur['reunion'].'</p>';} ?><br/>
				<label for="libelle">Libellé : </label><input type="text" value="<?php if(isset($_POST['libelle']) && $_POST['libelle']!=''){echo $_POST['libelle'];} elseif(!isset($_POST['libelle']) && $infodelib['libelle']!=''){echo $infodelib['libelle']; }?>" name="libelle" id="libelle"/>
				<?php if($hasError && isset($erreur['libelle'])){ echo '<p class="erreur">'.$erreur['libelle'].'</p>';} ?><br/>
				<label for="num">Numéro : </label><input type="text" value="<?php if(isset($_POST['num']) && $_POST['num']!=''){echo $_POST['num'];}elseif(!isset($_POST['num']) && $infodelib['num']!=''){echo $infodelib['num']; }?>" name="num" id="num"/>
				<?php if($hasError && isset($erreur['num'])){ echo '<p class="erreur">'.$erreur['num'].'</p>';} ?><br/>
				<label for="num_delib">Numéro délibération * : </label><input type="text" value="<?php if(isset($_POST['num_delib']) && $_POST['num_delib']!=''){echo $_POST['num_delib'];}elseif(!isset($_POST['num_delib']) && $infodelib['num_delib']!=''){echo $infodelib['num_delib']; }?>" name="num_delib" id="num_delib"/>
				<?php if($hasError && isset($erreur['num_delib'])){ echo '<p class="erreur">'.$erreur['num_delib'].'</p>';} ?><br/>
				<label>Axe * : </label>
				<?php 
					echo $montext;
				?><?php if($hasError && isset($erreur['axe'])){ echo '<p class="erreur">'.$erreur['axe'].'</p>';} ?><br/>
				<br/>
				<label for="folio">Folio : </label><input type="text" value="<?php if(isset($_POST['folio']) && $_POST['folio']!=''){echo $_POST['folio'];}elseif(!isset($_POST['folio']) && $infodelib['folio']!=''){echo $infodelib['folio'];}?>" name="folio" id="folio"/>
				<?php if($hasError && isset($erreur['folio'])){ echo '<p class="erreur">'.$erreur['folio'].'</p>';} ?><br/>
				<label for="delib">Délibération</label><input type="file" name="delib" id="delib"/><br/>
				<?php
					if($infofichier==0){
						echo 'Aucun fichier attaché.';
					}
					else{
						echo '<p><a href="/fichiers/'.$infofichier['nom_reel'].'">'.$infofichier['nom_affichage'].'</a></p><input type="hidden" name="iddufichier" value="'.$infofichier['id_fichier'].'"/><input type="checkbox" name="supprf" value="supprimer" id="supprf"/><label for="supprf">Cocher pour supprimer la délib</label>';
					}
				?><br/>
				<label for="budget">Document budgétaire</label><input type="file" name="budget" id="budget"/><br/>
				<?php
					if($infofichierbudget==0){
						echo 'Aucun fichier attaché.';
					}
					else{
						echo '<p><a href="/fichiers/'.$infofichierbudget['nom_reel'].'">'.$infofichierbudget['nom_affichage'].'</a></p><input type="hidden" name="iddufichierb" value="'.$infofichierbudget['id_fichier'].'"/><input type="checkbox" name="supprb" value="supprimer" id="supprb"/><label for="supprb">Cocher pour supprimer le doc budget</label>';
					}
				?><br/>
				<label for="emargement">Feuille émargement</label><input type="file" name="emargement" id="emargement"/><br/>
				<?php
					if($infofichieremargement==0){
						echo 'Aucun fichier attaché.';
					}
					else{
						echo '<p><a href="/fichiers/'.$infofichieremargement['nom_reel'].'">'.$infofichieremargement['nom_affichage'].'</a></p><input type="hidden" name="iddufichiere" value="'.$infofichieremargement['id_fichier'].'"/><input type="checkbox" name="suppre" value="supprimer" id="suppre"/><label for="suppre">Cocher pour supprimer la feuille émargement</label>';
					}
				?><br/>
				<input type="submit" value="Enregistrer" name="enregistrement" />
			</fieldset>
		</form>
	</section>

<?php

require_once('footer.php');
		}
	}

?>

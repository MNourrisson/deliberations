<?php 
	session_start();
	$_SESSION['page']='ajouterarrive.php';
	if(isset($_COOKIE['email']) && $_COOKIE['email']!='' && $_COOKIE['drt']==1)
	{
		$_SESSION['email'] = $_COOKIE['email'];
		$_SESSION['id'] =$_COOKIE['id'];
		$_SESSION['droit'] =$_COOKIE['drt'];	
	}
	if(!isset($_SESSION['email']) || $_SESSION['email']=='' || $_SESSION['droit']!=1)
	{
		header('Location: connexion.php');
	}
	
	$current = 'ajouterarrive';
	include_once('classes/connect.php');
	include_once('classes/Arrive.php');
	$arr = new Arrive();
	$erreur=array();
	$erreurmsg='';
	$msgok='';
	if(isset($_POST['departc']) && $_POST['departc']=='Enregistrer' )
	{
		$erreur=array();
		foreach($_POST['datec'] as $kd => $vd)
		{
			if($vd==''){$erreur['datec'][$kd]='erreur';}	
		}		
		foreach($_POST['exp'] as $kd => $vd)
		{
			if($vd==''){$erreur['exp'][$kd]='erreur';}
		}	
		foreach($_POST['contenu'] as $kd => $vd)
		{
			if($vd==''){$erreur['contenu'][$kd]='erreur';}
		}
		if(count($erreur)!=0)
		{
			$erreurmsg='Il y a '.count($erreur).' erreur(s). Merci de vérifier les données rentrées.';
		}
		else
		{	for($j=0; $j<=$_POST['nblignes'];$j++)
			{
				$dateavant=$_POST['datec'][$j];
				$array_majdate = explode('/',$dateavant); 
				$datetranformee=$array_majdate[2].'-'.$array_majdate[1].'-'.$array_majdate[0]; 
				$insert = $arr->addArrive($datetranformee,addslashes($_POST['exp'][$j]),addslashes($_POST['contenu'][$j]),$_POST['tech'][$j],$_SESSION['id']);
				$msgok='La mise à jour s\'est bien déroulée.';
				//header('Location: ajouterarrive.php');
			}
		}
	}
?>
<!doctype html>
<html lang="fr">
<head>
	<meta charset="utf-8">
	<title>Plateforme de gestion des courriers - PNRLF</title>
	<link rel="stylesheet" href="css/reset.css">
	<link rel="stylesheet" href="css/style.css">
	<link rel="stylesheet" href="css/jquery-ui.css">
	<script type="text/javascript" src="js/jquery-2.1.3.min.js"></script>
	<script type="text/javascript" src="js/jquery-ui.min.js"></script>
	<script type="text/javascript" src="js/jquery.ui.datepicker-fr.js"></script>
	<script type="text/javascript">
	var elementPattern = /^element(\d+)$/;

	function ajouterElement()
    {
        var Conteneur = document.getElementById('conteneur');
        if(Conteneur)
        {
            Conteneur.appendChild(creerElement(dernierElement() + 1))
        }
		var nbligne = parseInt(document.getElementById('nblignes').getAttribute('value'));
		document.getElementById('nblignes').setAttribute('value',nbligne+1);
    }
	
	function dernierElement()
    {
      var Conteneur = document.getElementById('conteneur'), n = 0;
      if(Conteneur)
      {
        var elementID, elementNo;
        if(Conteneur.childNodes.length > 0)
        {
			for(var i = 0; i < Conteneur.childNodes.length; i++)
			{
				// Ici, on vérifie qu'on peut récupérer les attributs, si ce n'est pas possible, on renvoit false, sinon l'attribut
				elementID = (Conteneur.childNodes[i].getAttribute) ? Conteneur.childNodes[i].getAttribute('id') : false;
				if(elementID)
				{
					elementNo = parseInt(elementID.replace(elementPattern, '$1'));
					if(!isNaN(elementNo) && elementNo > n)
					{
						n = elementNo;
					}
				}
			}
			
			
        }
      }
      return n;
    }
var d = new Date();
var twoDigitMonth = ("0" + (d.getMonth() + 1)).slice(-2);
var twoDigitDay = ("0" + d.getDate()).slice(-2);

    function creerElement(ID)
    {
		var Conteneur = document.createElement('li');
		Conteneur.setAttribute('id', 'element' + ID);
		Conteneur.setAttribute('class', 'clear');
		var ulelem = document.createElement('ul');
		var lidate = document.createElement('li');
		lidate.setAttribute('class','classdate');
		var inputdate = document.createElement('input');
		inputdate.setAttribute('type','text');
		inputdate.setAttribute('name','datec[]');
		inputdate.setAttribute('id','datec'+ID);
		inputdate.setAttribute('value',twoDigitDay+'/'+twoDigitMonth+'/'+d.getFullYear());
		inputdate.setAttribute('class','datec');
		var liexp = document.createElement('li');
		liexp.setAttribute('class','exped');
		var inputexp = document.createElement('input');
		inputexp.setAttribute('type','text');
		inputexp.setAttribute('name','exp[]');
		inputexp.setAttribute('id','exp'+ID);
		inputexp.setAttribute('class','expcourrier');
		var licontenu = document.createElement('li');
		licontenu.setAttribute('class','exped');
		var inputcontenu = document.createElement('input');
		inputcontenu.setAttribute('type','text');
		inputcontenu.setAttribute('name','contenu[]');
		inputcontenu.setAttribute('id','contenu'+ID);
		inputcontenu.setAttribute('class','contenucourrier');
		var litech = document.createElement('li');
		litech.setAttribute('class','classdate');
		var inputtech = document.createElement('input');
		inputtech.setAttribute('type','text');
		inputtech.setAttribute('name','tech[]');
		inputtech.setAttribute('id','tech'+ID);
		var lisuppr = document.createElement('li');
		var inputsuppr = document.createElement('input');
		inputsuppr.setAttribute('type','image');
		inputsuppr.setAttribute('src','img/delete.png');
		inputsuppr.setAttribute('value','Suppr');
		inputsuppr.setAttribute('id','suppr'+ID);
		inputsuppr.setAttribute('onclick','javascript:supprimerElement('+ID+')');
		inputsuppr.setAttribute('class','boutonsuppr');

		Conteneur.appendChild(ulelem);
		ulelem.appendChild(lidate);
		lidate.appendChild(inputdate);
		ulelem.appendChild(liexp);
		liexp.appendChild(inputexp);
		ulelem.appendChild(licontenu);
		licontenu.appendChild(inputcontenu);
		ulelem.appendChild(litech);
		litech.appendChild(inputtech);
		ulelem.appendChild(lisuppr);
		lisuppr.appendChild(inputsuppr);
		return Conteneur;
    }
	
	function supprimerElement(numelem)
	{
		document.getElementById('element'+numelem).remove();
		var nbligne = parseInt(document.getElementById('nblignes').getAttribute('value'));
		document.getElementById('nblignes').setAttribute('value',nbligne-1);
	}

	</script>
</head>
<body>
	<header>
		<div class="header">
			<h1><a href="index.php">Gestion des courriers départs et arrivés</a></h1>
		</div>
	</header>
<?php
	require_once('nav.php');
?>
<section id="content">
	<h2>Ajout d'un courrier arrivé</h2>
	<p class="indication">Les champs suivis de * sont obligatoires.</p>
	<?php if(isset($erreurmsg) && $erreurmsg!='') echo '<p class="erreur left">'.$erreurmsg.'</p><br/>'; ?>
	<?php if(isset($msgok) && $msgok!=''){ echo '<p class="msgok left">'.$msgok.'</p><br/>'; }?>
	<form action="" method="post">
		<fieldset>
			<ul id="conteneur">
				<li>
					<ul>
						<li class="classdate"><label for="datec">Date<span class="obligatoire">*</span></label></li>
						<li class="exped"><label for="exp">Expéditeur<span class="obligatoire">*</span></label></li>
						<li class="exped"><label for="contenu">Contenu<span class="obligatoire">*</span></label></li>
						<li class="classdate"><label for="tech">Techniciens</label></li>
						<li></li>
					</ul>
				</li>
				<li id="element1" class="clear">
					<ul>
						<li class="classdate <?php if(isset($erreur['datec'][0])) echo 'erreur';?>" ><input type="text" value="<?php if(isset($_POST['datec'][0]) && $_POST['datec'][0]!='') {echo $_POST['datec'][0];} else echo date('d/m/Y'); ?>" name="datec[]" id="datec1" class="datec"/></li>
						<li  class="exped <?php if(isset($erreur['exp'][0])) echo 'erreur';?>" ><input type="text" value="<?php if(isset($_POST['exp'][0]) && $_POST['exp'][0]!='') {echo $_POST['exp'][0];} ?>" name="exp[]" id="exp1" class="expcourrier" /></li>
						<li  class="exped <?php if(isset($erreur['contenu'][0])) echo 'erreur';?>" ><input type="text" value="<?php if(isset($_POST['contenu'][0]) && $_POST['contenu'][0]!='') {echo $_POST['contenu'][0];} ?>" name="contenu[]" id="contenu1" class="contenucourrier"/></li>
						<li class="classdate <?php if(isset($erreur['tech'][0])) echo 'erreur';?>" ><input type="text" value="<?php if(isset($_POST['tech'][0]) && $_POST['tech'][0]!='') {echo $_POST['tech'][0];}?>" name="tech[]" id="tech1" /></li>
						<li></li>
					</ul>
				</li>
				<?php
				if(isset($_POST['nblignes']) && $_POST['nblignes'] >0)
				{
					for($i=1;$i<=$_POST['nblignes'];$i++)
					{
						$num = $i+1;
						?>
						<li id="element<?php echo $num;?>" class="clear">
							<ul>
								<li class="classdate"><input type="text" value="<?php if(isset($_POST['datec'][$i]) && $_POST['datec'][$i]!='') {echo $_POST['datec'][$i];} else echo date('d/m/Y'); ?>" name="datec[]" id="datec<?php echo $num; ?>" class="datec" /><?php if(isset($erreur['datec'][$i])) echo '<p class="erreur left">'.$erreur['datec'][$i].'</p>';?></li>
								<li class="exped"><input type="text" value="<?php if(isset($_POST['exp'][$i]) && $_POST['exp'][$i]!='') {echo $_POST['exp'][$i];} ?>" name="exp[]" id="exp<?php echo $num; ?>" class="expcourrier" /><?php if(isset($erreur['exp'][$i])) echo '<p class="erreur left">'.$erreur['exp'][$i].'</p>';?></li>
								<li class="exped"><input type="text" value="<?php if(isset($_POST['contenu'][$i]) && $_POST['contenu'][$i]!='') {echo $_POST['contenu'][$i];} ?>" name="contenu[]" id="contenu<?php echo $num; ?>" class="contenucourrier"/><?php if(isset($erreur['contenu'][$i])) echo '<p class="erreur left">'.$erreur['contenu'][$i].'</p>';?></li>
								<li class="classdate"><input type="text" value="<?php if(isset($_POST['tech'][$i]) && $_POST['tech'][$i]!='') {echo $_POST['tech'][$i];}?>" name="tech[]" id="tech<?php echo $num; ?>" /><?php if(isset($erreur['tech'][$i])) echo '<p class="erreur left">'.$erreur['tech'][$i].'</p>';?></li>
								<li><input type="image" src="img/delete.png" width="24px" height="24px" value="Suppr" onclick="javascript:supprimerElement(<?php echo $num;?>);" id="suppr<?php echo $num;?>" class="boutonsuppr"/></li>
							</ul>
						</li>
						<?php	
					}
				}
				?>
			</ul>
			<input type="hidden" id="nblignes" name="nblignes" value="<?php if(isset($_POST['nblignes']) && $_POST['nblignes']!=0) echo $_POST['nblignes']; else echo '0'; ?>" />
			<input type="button" value="Ajouter une ligne" onclick="javascript:ajouterElement();" id="ajout" /><br/>
			<input type="submit" value="Enregistrer" name="departc"/>
		</fieldset>
	
	</form>
</section>
<script type="text/javascript">

$(document).ready(function() {	
	$(".datec").datepicker({
		changeMonth: true,
		changeYear: true,
		showButtonPanel: true
	});$.datepicker.setDefaults( $.datepicker.regional[ "fr" ] );

	$(".expcourrier").autocomplete({
		source : 'listearr.php',
		minLength : 3,
		open: function() {$("ul.ui-menu").width('400px');}
	});
	$(".contenucourrier").autocomplete({
		source : 'listearr2.php',
		minLength : 3,
		open: function() {$("ul.ui-menu").width( '500px' );}
	});
	$('#ajout').bind('click',function() {
		$(".datec").datepicker({
			changeMonth: true,
			changeYear: true,
			showButtonPanel: true
		});$.datepicker.setDefaults( $.datepicker.regional[ "fr" ] );

		$(".expcourrier").autocomplete({
			source : 'listearr.php',
			minLength : 3,
			open: function() {$("ul.ui-menu").width('400px');}
		});
		$(".contenucourrier").autocomplete({
			source : 'listearr2.php',
			minLength : 3,
			open: function() {$("ul.ui-menu").width( '500px' );}
		});
	});
});

</script>
<?php

require_once('footer.php');

?>

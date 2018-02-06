<?php 
	session_start();
	include_once('classes/connect.php');
	include_once('classes/Utilisateur.php');
	include_once('classes/password.php');
	include_once('classes/Reunion.php');
	
	function listesDescendances($ressources,$parent,&$tabD){

		$tmp = $ressources->getDescendances($parent);
		if(count($tmp)!=0){
			foreach($tmp as $k => $v){
				$id = $v['id_type_reunion'];
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
	function mafunction($liste,$cle,&$montext,$parent,$infobdd){
		if(isset($liste[$cle]) && count($liste[$cle])!=0){
			$montext.='<optgroup label="'.$liste[$parent][$cle].'">';
			foreach($liste[$cle] as $k => $v){
				$selected='';
				if(isset($_POST['type']) && $_POST['type']==$k){$selected='selected="selected"';}elseif(!isset($_POST['id_type_reunion']) && $infobdd==$k){$selected='selected="selected"';}
				$montext.='<option value="'.$k.'" '.$selected.'>'.$v;
				mafunction($liste,$k,$montext,$cle,$infobdd);
				$montext.='</option>';
			}
			$montext.='</optgroup>';
		}
		elseif($parent==0){
			$selected='';
			if(isset($_POST['type']) && $_POST['type']==$cle){$selected='selected="selected"';}
			$montext.='<option value="'.$cle.'" '.$selected.'>'.$liste[$parent][$cle].'</option>';
		}
	}
	
	$current='ajoutreunion';
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
			$msgok=$erreurmsg='';
			$r = new Reunion();
			$id_reunion=$_GET['id'];
			$infosreunion = $r->getInfosReunionById($id_reunion);
			
			$tr = new TypeReunion();
			$tabD=array();
			$listetype = listesDescendances($tr,0,$tabD);
			$montext='<select name="type"><option value="0">-- Choisir -- </option>';
			if(count($listetype)!=0)
			{
				foreach($listetype[0] as $k => $v){
					mafunction($listetype,$k,$montext,0,$infosreunion['id_type_reunion']);
				}
			}
			$montext.='</select>';

			if(isset($_POST['enregistrement']))
			{
				if(isset($_POST['dater']) && $_POST['dater']=='')
				{
					$hasError =true;
					$erreur['dater'] = 'Veuillez rentrer une date.';
				}
				if(isset($_POST['type']) && $_POST['type']==0)
				{
					$hasError =true;
					$erreur['type'] = 'Choisir un type.';
				}
				if(!$hasError)
				{
					$dater = $_POST['dater'];
					$tabd = explode('/',$dater);
					$dater=$tabd[2].'-'.$tabd[1].'-'.$tabd[0];
					
					
					$idr = $r->upReunion($id_reunion,$_POST['type'],$dater);
					
					if($idr)
					{
						$msgok = 'Réunion modifiée.'; 
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
		<h2>Modifier une réunion</h2>
		
		<p>Indication : Les champs suivis de * sont obligatoires.</p>
		<?php if(isset($erreurmsg) && $erreurmsg!='') echo '<p>Erreur : '.$erreurmsg.'</p>'; ?>
		<?php if(isset($msgok) && $msgok!='') echo '<p>'.$msgok.'</p>'; ?>
		<form method="post" action=""> 	
			<fieldset>
				<label for="dater">Date * : </label><input type="text" value="<?php if(isset($_POST['dater']) && $_POST['dater']!=''){echo $_POST['dater'];}elseif(!isset($_POST['dater']) && $infosreunion['date']!=''){ $tmp = explode('-',$infosreunion['date']); echo $tmp[2].'/'.$tmp[1].'/'.$tmp[0]; }?>" name="dater" id="dater"/>
				<?php if($hasError && isset($erreur['dater'])){ echo '<p class="erreur">'.$erreur['dater'].'</p>';} ?><br/>
				<label>Type : </label>
				<?php echo $montext; ?><?php if($hasError && isset($erreur['type'])){ echo '<p class="erreur">'.$erreur['type'].'</p>';} ?><br/>
				<input type="submit" value="Enregistrer" name="enregistrement" />
			</fieldset>
		</form>
	</section>
	<script type="text/javascript">

$(document).ready(function() {	
	$("#dater").datepicker({
		changeMonth: true,
		changeYear: true,
		showButtonPanel: true
	});$.datepicker.setDefaults( $.datepicker.regional[ "fr" ] );

});

</script>
<?php

require_once('footer.php');
		}
	}

?>

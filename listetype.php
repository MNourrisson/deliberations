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
	function mafunction($liste,$cle,&$montext,$parent){
		if(isset($liste[$cle]) && count($liste[$cle])!=0){
			$montext.='<ul>';
			foreach($liste[$cle] as $k => $v){
				$montext.='<li><a href="modiftype.php?t='.$k.'">'.$v.'</a>';
				mafunction($liste,$k,$montext,$cle);
				$montext.='</li>';
			}
			$montext.='</ul>';
		}
	}
	
	$current='listetype';
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
			$tr = new TypeReunion();
			$tabD=array();
			$listetype = listesDescendances($tr,0,$tabD);
			$montext='<ul>';
			if(count($listetype)!=0)
			{
				foreach($listetype[0] as $k => $v){
					$montext.='<li><a href="modiftype.php?t='.$k.'">'.$v.'</a>';
					mafunction($listetype,$k,$montext,0);
					$montext.='</li>';
				}
			}
			$montext.='</ul>';
			
	require_once('head.php');
	require_once('nav.php');
?>
	<section id="content">
		<h2>Les types</h2>
		<a href="ajouttype.php" class="boutonlien">Ajouter</a>
		<?php
			echo $montext;
		?>
		
		
	</section>
	
<?php

require_once('footer.php');
		}
	}

?>

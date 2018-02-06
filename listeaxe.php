<?php 
	session_start();
	include_once('classes/connect.php');
	include_once('classes/Utilisateur.php');
	include_once('classes/password.php');
	include_once('classes/Charte.php');
	$current='listeaxe';
	
	function listesDescendances($ressources,$parent,&$tabD){

		$tmp = $ressources->getDescendances($parent);
	//	echo $parent.'<br/>';
		if(count($tmp)!=0){
		//	echo 'count : '.count($tmp).'<br/>';
			foreach($tmp as $k => $v){
				$id = $v['id_axe'];
				$parent = $v['parent'];
				$tabD[$parent][$id]=$v['libelle'];
				//echo 'id '.$id.'<br/><br/>';
				$tmp=listesDescendances($ressources,$id,$tabD);
				if($tmp==null)
				{
					$tabD[$parent][$id]=$v['libelle'];
					//return $tabD;
				}
				
			}
			return $tabD;
		}else{
			//echo 'pas de resultats<br/>';
			return NULL;
		}
		
	}
	function mafunction($liste,$cle,&$montext,$parent){
		
		if(isset($liste[$cle]) && count($liste[$cle])!=0){
			$montext.='<ul>';
			foreach($liste[$cle] as $k => $v){
				$montext.='<li><a href="modifaxe.php?a='.$k.'">'.$v.'</a>';
				mafunction($liste,$k,$montext,$cle);
				$montext.='</li>';
			}
			$montext.='</ul>';
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
			$a = new Axe();
			$listeaxe = $a->getAxes();
			$tabtest = array();
			$listes2 = listesDescendances($a,0,$tabtest);
		
			$montext='<ul>';
			if(count($listes2)!=0)
			{
				foreach($listes2[0] as $k => $v){
					$montext.='<li class="niveau1"><a href="modifaxe.php?a='.$k.'">'.$v.'</a>';
					mafunction($listes2,$k,$montext,0);
					$montext.='</li>';
				}
			}
			$montext.='</ul>';
			
	require_once('head.php');
	require_once('nav.php');
?>
	<section id="content">
		<h2>Les axes</h2>
		<a href="ajoutaxe.php" class="boutonlien">Ajouter</a>
		<?php
			echo $montext;
		?>
	</section>
<?php

require_once('footer.php');
		}
	}
?>
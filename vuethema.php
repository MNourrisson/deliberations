<?php 
	session_start();
	include_once('classes/connect.php');
	include_once('classes/Deliberation.php');
	include_once('classes/Charte.php');
	include_once('classes/PHPExcel.php');
	
	$current='vuethema';
	$tabjour=array('Monday'=>'Lundi', 'Tuesday'=>'Mardi', 'Wednesday'=>'Mercredi', 'Thursday'=>'Jeudi', 'Friday'=>'Vendredi', 'Saturday'=>'Samedi', 'Sunday'=>'Dimanche');
	$tabmois = array('January'=>'Janvier', 'February'=>'Février', 'March'=>'Mars', 'April'=>'Avril', 'May'=>'Mai', 'June'=>'Juin', 'July'=>'Juillet', 'August'=>'Août', 'September'=>'Septembre', 'October'=>'Octobre', 'November'=>'Novembre', 'December'=>'Décembre');

	 
	
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
	function mafunction($liste,$cle,&$montext,&$montext2,$parent,$d,$niveau){
		$tabjour=array('Monday'=>'Lundi', 'Tuesday'=>'Mardi', 'Wednesday'=>'Mercredi', 'Thursday'=>'Jeudi', 'Friday'=>'Vendredi', 'Saturday'=>'Samedi', 'Sunday'=>'Dimanche');
		$tabmois = array('January'=>'Janvier', 'February'=>'Février', 'March'=>'Mars', 'April'=>'Avril', 'May'=>'Mai', 'June'=>'Juin', 'July'=>'Juillet', 'August'=>'Août', 'September'=>'Septembre', 'October'=>'Octobre', 'November'=>'Novembre', 'December'=>'Décembre');
		
		if(isset($liste[$cle]) && count($liste[$cle])!=0){
			
			foreach($liste[$cle] as $k => $v){
				$montext.='<table><tr class="niveau'.$niveau.'"><td colspan="6">'.$v.'</td></tr></table>';
				$montext2.='<table><tr class="niveau'.$niveau.'"><td colspan="6">'.$v.'</td></tr></table>';
						
				$listedelib=$d->getDelibByAxe($k);
				
				if(count($listedelib)!=0){
					$montext.='<table class="nivo">';
					$montext2.='<table class="nivo">';
					foreach($listedelib as $k1 => $v1){
						$dateexplode=explode(' ',strftime("%A %d %B %Y", strtotime($v1['date'])));
						$newformatdate = $tabjour[$dateexplode[0]].' '.$dateexplode[1].' '.$tabmois[$dateexplode[2]].' '.$dateexplode[3];
						$montext.='<tr><td class="prog">'.$v1['num'].'</td><td class="num_delib">'.$v1['num_delib'].'</td><td class="date">'.$newformatdate.'</td><td class="type">'.$v1['libeltp'].'</td><td class="lien"><a class="js-fancybox-iframe" data-fancybox data-type="iframe" data-src="/fichiers/'.$v1['nom_reel'].'">'.$v1['libelle'].'</a></td><td class="folio">'.$v1['folio'].'</td></tr>';
						$montext2.='<tr><td class="prog">'.$v1['num'].'</td><td class="num_delib">'.$v1['num_delib'].'</td><td class="date">'.$newformatdate.'</td><td class="type">'.$v1['libeltp'].'</td><td class="lien"><a class="js-fancybox-iframe" data-fancybox data-type="iframe" data-src="/fichiers/'.$v1['nom_reel'].'">'.$v1['libelle'].'</a></td><td class="folio">'.$v1['folio'].'</td></tr>';
					
					}
					$montext.='</table>';
					$montext2.='</table>';
				}
				mafunction($liste,$k,$montext,$montext2,$cle,$d,3);
			}
			
		}
		
	}
	$d=new Deliberation();
	$a = new Axe();
	$listeaxe = $a->getAxes();
	$tabtest = array();
	$listes2 = listesDescendances($a,0,$tabtest);

	
	if(count($listes2)!=0)
	{
		$montext = '<div id="tableau" class="tablo"><table>';
		$montext2 = '<div id="tableau2" class="tablo"><table>';
		$montext.='<thead><tr><th class="prog">N°</th><th class="num_delib">N° délib</th><th class="date">Date</th><th class="type">Type</th><th class="lien">Lien vers la délib</th><th class="folio">Folio</th></tr></thead>';
		$montext2.='<thead><tr><th class="prog">N°</th><th class="num_delib">N° délib</th><th class="date">Date</th><th class="type">Type</th><th class="lien">Lien vers la délib</th><th class="folio">Folio</th></tr></thead>';
		$montext.='</table>';
		$montext2.='</table>';
		foreach($listes2[0] as $k => $v){
			$montext.='<table><tr class="niveau1"><td colspan="6">'.$v.'</td></tr></table>';
			$montext2.='<table><tr class="niveau1"><td colspan="6">'.$v.'</td></tr></table>';
			
			
			//verif delib attache
			$listedelib=$d->getDelibByAxe($k);
			if(count($listedelib)!=0){
				$montext.='<table class="nivo">';	
				$montext2.='<table class="nivo">';	
				foreach($listedelib as $k1 => $v1){
					$dateexplode=explode(' ',strftime("%A %d %B %Y", strtotime($v1['date'])));
					
					$newformatdate = $tabjour[$dateexplode[0]].' '.$dateexplode[1].' '.$tabmois[$dateexplode[2]].' '.$dateexplode[3];
					$montext.='<tr><td class="prog">'.$v1['num'].'</td><td class="num_delib">'.$v1['num_delib'].'</td><td class="date">'.$newformatdate.'</td><td class="type">'.$v1['libeltp'].'</td><td class="lien"><a class="js-fancybox-iframe" data-fancybox data-type="iframe" data-src="/fichiers/'.$v1['nom_reel'].'">'.$v1['libelle'].'</a></td><td class="folio">'.$v1['folio'].'</td></tr>';
					$montext2.='<tr><td class="prog">'.$v1['num'].'</td><td class="num_delib">'.$v1['num_delib'].'</td><td class="date">'.$newformatdate.'</td><td class="type">'.$v1['libeltp'].'</td><td class="lien"><a class="js-fancybox-iframe" data-fancybox data-type="iframe" data-src="/fichiers/'.$v1['nom_reel'].'">'.$v1['libelle'].'</a></td><td class="folio">'.$v1['folio'].'</td></tr>';
					
				}
				$montext.='</table>';
				$montext2.='</table>';
			}
			mafunction($listes2,$k,$montext,$montext2,0,$d,2);
			
		}
		$montext.='</div>';	
	}
	else{ $montext='<p>Pas de délibérations.</p>';}		
	
	require_once('head.php');
	require_once('nav.php');
?>
	<section id="content">
		<h2>Classement des délibérations par thématique</h2>
		<form action="#">
			<input value="" placeholder="Rechercher" id="moninput"/>
		</form>
		<?php 
					
			echo $montext;
			echo $montext2;
		
		?>
	</section>
	<script type="text/javascript" src="js/jquery.quicksearch.js"></script>
	<script type="text/javascript" src="js/jquery.fancybox.min.js"></script>
	<script>
	$(document).ready(function() {	

		$( function() {
			$(".nivo").each(function(e){
				$(this).prev().addClass("principale");
			});


			$( "#tableau" ).accordion({
				collapsible: false,
				header:'table.principale',
				heightStyle:'content'
			});
		} );

		$('input#moninput').quicksearch('#tableau2 tr');
		$('input#moninput').focus(function(){
			$( "#tableau" ).css('display','none');
			$( "#tableau2" ).css('display','block');
		});		
		$('input#moninput').focusout(function(){
			if($(this).val()==''){
				$( "#tableau" ).css('display','block');
				$( "#tableau2" ).css('display','none');
			}
		});
	});
	$('a.js-fancybox-iframe').fancybox({
        iframe: {
            preload: false // fixes issue with iframe and IE
        }
	});	
	</script>
<?php

require_once('footer.php');

?>
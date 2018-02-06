<?php 
	session_start();
	include_once('classes/connect.php');
	include_once('classes/Utilisateur.php');
	include_once('classes/password.php');
	include_once('classes/Deliberation.php');
	include_once('classes/Reunion.php');

	$current='recherchedeliberation';
	
	$listedelib=array();
	$datedelib = $_GET['date'];
	$id_reunion = $_GET['id'];
	$r = new Reunion();
	$info = $r->getIdReunionByDate($datedelib,$id_reunion);
	 $d = new Deliberation();
	// if(isset($info['id_reunion']))
	$listedelib= $d->getDelibByReunion($id_reunion);

	$f= new Fichier();
	require_once('head.php');
	require_once('nav.php');
?>
	<section id="content">
		<h2>Voir les délibérations du <?php echo $info['libelle'].' du '; $tmp = explode('-',$datedelib); echo $tmp[2].'/'.$tmp[1].'/'.$tmp[0]; ?></h2>
		<?php if(count($listedelib)!=0){
				echo '<ul>';
				foreach($listedelib as $k => $v){
					if($v['id_fichier']!=0){
						$fichier = $f->getFichierById($v['id_fichier']);
						echo '<li><a class="js-fancybox-iframe" data-fancybox data-type="iframe" data-src="/fichiers/'.$fichier['nom_reel'].'">'.$v['libelle'].'</a></li>';
					}
					else{ echo '<li><a href="#">'.$v['libelle'].'</a></li>'; }
				}
				echo '</ul>';
			
		}else{echo '<p>Aucun résultat.</p>';} ?>
	</section>
	<script type="text/javascript" src="js/jquery.fancybox.min.js"></script>
	<script type="text/javascript">
	$('a.js-fancybox-iframe').fancybox({
        iframe: {
            preload: false // fixes issue with iframe and IE
        }
	});
	</script>
<?php
require_once('footer.php');
		
?>
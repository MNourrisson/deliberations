<?php 
	session_start();
	include_once('classes/connect.php');
	include_once('classes/Utilisateur.php');
	include_once('classes/password.php');
	include_once('classes/Reunion.php');
	include_once('classes/Deliberation.php');
	include_once('classes/PHPExcel.php');
	$current='delib';

	$r = new Reunion();
	$f = new Fichier();
					
					
				
	require_once('head.php');
	require_once('nav.php');
?>
	<section id="content">
		<h2>Import</h2>
		<?php 
			$inputFileName = 'importscot.xlsx';
			
			$chemin = '/your/files/directory/';
			echo 'Loading file ',pathinfo($inputFileName,PATHINFO_BASENAME),' using IOFactory to identify the format<br />';
			$objPHPExcel = PHPExcel_IOFactory::load($inputFileName);

			$tabtmp = array();
			//echo '<hr />';
			$i=0;
			$sheetData0 = $objPHPExcel->getSheet(0);

			foreach($sheetData0->getRowIterator() as $row) {
				foreach ($row->getCellIterator() as $cell) {
				 $tabtmp[$i][]=$cell->getValue();
				}
			 $i++;
			 
			}

		foreach($tabtmp as $k => $v){
			
			$numdelib = $v[0];
			
			$dd = $v[1];
			$dd1 = explode(' ',$dd);
			$tabmois=array('janvier'=>'01','février'=>'02','mars'=>'03','avril'=>'04','mai'=>'05','juin'=>'06','juillet'=>'07','août'=>'08','septembre'=>'09','octobre'=>'10','novembre'=>'11','décembre'=>'12');
			$dd2 = $dd1[3].'-'.$tabmois[$dd1[2]].'-'.$dd1[1];
			
			$fich = $v[2];
			$fich1 =explode('\\',$fich);
			$folio = $v[3];
			
			//recherche id de la réunion
			echo $dd2;
			
			$chaine = str_split($fich[0]);
			if($chaine[0]=='B')
			{
				$numa =2;
			}
			if($chaine[0]=='C')
			{
				$numa =3;
			}
			$infor = $r->getIdReunionByDate2($dd2,$numa);
			//print_r($infor);
			if(count($infor)!=0){

				$idreunion = $infor['id_reunion'];
				echo $idreunion;
				$upload_dir = '/your/upload/directory/';
				$namefic='';
				if($fich1[1]!= '') 
				{
					$tmp_name = $chemin.'\\'.$fich1[1];
					$nameext = $fich1[1];
					$tempext1 = substr(strrchr($nameext,"."),1);
					$namefic=$nameext;
					$caracteres = array('À' => 'a', 'Á' => 'a', 'Â' => 'a', 'Ä' => 'a', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ä' => 'a', '@' => 'a','È' => 'e', 'É' => 'e', 'Ê' => 'e', 'Ë' => 'e', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', '€' => 'e','Ì' => 'i', 'Í' => 'i', 'Î' => 'i', 'Ï' => 'i', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i','Ò' => 'o', 'Ó' => 'o', 'Ô' => 'o', 'Ö' => 'o', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'ö' => 'o','Ù' => 'u', 'Ú' => 'u', 'Û' => 'u', 'Ü' => 'u', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'µ' => 'u','Œ' => 'oe', 'œ' => 'oe','$' => 's');

					$namefic = strtr($namefic, $caracteres);
					$namefic = preg_replace('#[^A-Za-z0-9]+#', '-', $namefic);
					$namefic = trim($namefic, '-');
					$namefic= $namefic.'_'.rand().'.'.$tempext1;
					
					
					$requete = $f->addFichier($nameext,$namefic);
					if($requete)
					{	
						$d = new Deliberation();
						
						$idd = $d->addDeliberation($idreunion,$fich1[1],'',$numdelib,1,$folio,$requete);
					
						if($idd)
						{
							$msgok = 'Délibération ajoutée.'; 
						}
						else{
							$erreurmsg='Erreur lors de l\'enregistrement. ';
						}
					}
					else{
						$hasError =true;
						$erreur['verif_code'] = 'Erreur lors de l\'upload du fichier.'; 
					}
													
				}else{
					$d = new Deliberation();
							
					$idd = $d->addDeliberation($idreunion,$fich1[1],'',$numdelib,93,$folio,$_POST['folio'],0);
				
					if($idd)
					{
						$msgok = 'Délibération ajoutée.'; 
					}
					else{
						$erreurmsg='Erreur lors de l\'enregistrement. ';
					}
					
				}
			}
			else{echo 'erreur';}
			
			
			
		}
		?>
	</section>
<?php

require_once('footer.php');


?>
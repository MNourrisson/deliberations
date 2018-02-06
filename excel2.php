<?php 
	session_start();
	include_once('classes/connect.php');
	include_once('classes/Deliberation.php');
	include_once('classes/Reunion.php');
	include_once('classes/Charte.php');
	include_once('classes/PHPExcel.php');
	
	$current='fichiersexcel';
	
	$a = new Axe();
	$tr = new TypeReunion();
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
	
	$tabjour=array('Monday'=>'Lundi', 'Tuesday'=>'Mardi', 'Wednesday'=>'Mercredi', 'Thursday'=>'Jeudi', 'Friday'=>'Vendredi', 'Saturday'=>'Samedi', 'Sunday'=>'Dimanche');
	$tabmois = array('January'=>'Janvier', 'February'=>'Février', 'March'=>'Mars', 'April'=>'Avril', 'May'=>'Mai', 'June'=>'Juin', 'July'=>'Juillet', 'August'=>'Août', 'September'=>'Septembre', 'October'=>'Octobre', 'November'=>'Novembre', 'December'=>'Décembre');
	
	$styleNiveau1 = new PHPExcel_Style();
	$styleAll = new PHPExcel_Style();
	$styleNiveau1->applyFromArray(array(
		'font' => array('bold'=>true,'size'=>16),
		'fill' 	=> array('type'=> PHPExcel_Style_Fill::FILL_SOLID,'color'=> array('argb' => 'FF92d050')),
		'borders' => array('bottom'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),'top'=> array('style' => PHPExcel_Style_Border::BORDER_THIN),'left'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),'right'=> array('style' => PHPExcel_Style_Border::BORDER_THIN)),
		'alignment'=>array('vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT)				
	));		

	$styleAll->applyFromArray(array( 
		'borders' => array('bottom'=> array('style' => PHPExcel_Style_Border::BORDER_THIN),'top'=> array('style' => PHPExcel_Style_Border::BORDER_THIN),'left'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),'right'=> array('style' => PHPExcel_Style_Border::BORDER_THIN)),
		'alignment'=>array('wrap' => true)
	));	
	$hastrue= true;
	if(isset($_POST['generer'])){
		
		if(trim($_POST['annee1'])==''){
			$erreur['annee1']='Champ obligatoire';
			$hastrue=false;
		}
		if(trim($_POST['annee2'])==''){
			$erreur['annee2']='Champ obligatoire';
			$hastrue=false;
		}
		if(trim($_POST['annee3'])==''){
			$erreur['annee3']='Champ obligatoire';
			$hastrue=false;
		}
		if(trim($_POST['typer'])==0){
			$erreur['typer']='Champ obligatoire';
			$hastrue=false;
		}
		if($hastrue){
			/*generation fichier thema 3 ans*/
			$curseur = 1;
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->getProperties()->setCreator("Mélanie Nourrisson")
					->setLastModifiedBy("Mélanie Nourrisson")
					->setTitle("Thema ")
					->setSubject("Thema ");
			$objPHPExcel->setActiveSheetIndex(0)->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
			$objPHPExcel->setActiveSheetIndex(0)->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
			$objPHPExcel->setActiveSheetIndex(0)->getPageMargins()->setHeader(0.2); //inch
			$objPHPExcel->setActiveSheetIndex(0)->getPageMargins()->setTop(0.6); //inch
			$objPHPExcel->setActiveSheetIndex(0)->getPageMargins()->setRight(0.25); //inch
			$objPHPExcel->setActiveSheetIndex(0)->getPageMargins()->setLeft(0.25); //inch
			$objPHPExcel->setActiveSheetIndex(0)->getPageMargins()->setBottom(0.6); //inch
			$objPHPExcel->setActiveSheetIndex(0)->getPageSetup()->setHorizontalCentered(true);
			$objPHPExcel->setActiveSheetIndex(0)->getHeaderFooter()->setOddFooter('&RPage &P / &N');
			
	 		
			function mafunction($liste,$cle,$parent,$d,$niveau,$objPHPExcel,&$curseur,$vtr,$a1,$a2,$a3){
				$tabjour=array('Monday'=>'Lundi', 'Tuesday'=>'Mardi', 'Wednesday'=>'Mercredi', 'Thursday'=>'Jeudi', 'Friday'=>'Vendredi', 'Saturday'=>'Samedi', 'Sunday'=>'Dimanche');
				$tabmois = array('January'=>'Janvier', 'February'=>'Février', 'March'=>'Mars', 'April'=>'Avril', 'May'=>'Mai', 'June'=>'Juin', 'July'=>'Juillet', 'August'=>'Août', 'September'=>'Septembre', 'October'=>'Octobre', 'November'=>'Novembre', 'December'=>'Décembre');
				$styleNiveau2 = new PHPExcel_Style();
				$styleNiveau2->applyFromArray(array(
					'font' => array('bold'=>true,'size'=>12),
					'fill' 	=> array('type'=> PHPExcel_Style_Fill::FILL_SOLID,'color'=> array('argb' => 'FFd9d9d9')),
					'borders' => array('bottom'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),'top'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),'left'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),'right'=> array('style' => PHPExcel_Style_Border::BORDER_THIN))
				));	
				
				$styleNiveau3 = new PHPExcel_Style();
				$styleNiveau3->applyFromArray(array(
					'font' => array('bold'=>true,'size'=>8),
					'fill' 	=> array('type'=> PHPExcel_Style_Fill::FILL_SOLID,'color'=> array('argb' => 'FFfcd5b4')),
					'borders' => array('bottom'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),'top'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),'left'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),'right'=> array('style' => PHPExcel_Style_Border::BORDER_THIN))
				));	
				$styleAll = new PHPExcel_Style();
				$styleAll->applyFromArray(array( 
					'borders' => array('bottom'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),'top'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),'left'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),'right'=> array('style' => PHPExcel_Style_Border::BORDER_THIN)),
					'alignment'=>array('wrap' => true,'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT)	
				));	
							 
				if(isset($liste[$cle]) && count($liste[$cle])!=0){
					
					foreach($liste[$cle] as $k => $v){
						if($niveau==2)
						{	
							$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($styleNiveau2, "A".$curseur.':E'.$curseur);
							$objPHPExcel->setActiveSheetIndex(0)->getRowDimension($curseur)->setRowHeight(34);
						}
						elseif($niveau==3)
						{
							$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($styleNiveau3, "A".$curseur.':E'.$curseur);
						}
						$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$curseur.':E'.$curseur);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$curseur, html_entity_decode($v,ENT_QUOTES));
						$curseur++;
						$listedelib=$d->getDelibByAxe3($k,$vtr,$a1,$a2,$a3);
						
						if(count($listedelib)!=0){
							foreach($listedelib as $k1 => $v1){
								$dateexplode=explode(' ',strftime("%A %d %B %Y", strtotime($v1['date'])));
								$newformatdate = $tabjour[$dateexplode[0]].' '.$dateexplode[1].' '.$tabmois[$dateexplode[2]].' '.$dateexplode[3];
								
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$curseur, $v1['num']);
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$curseur, $v1['num_delib']);
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$curseur, $newformatdate);
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$curseur, $v1['libelle']);
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$curseur, $v1['folio']);
								$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($styleAll, "A".$curseur.":E".$curseur);
								$curseur++;
							}
						}
						mafunction($liste,$k,$cle,$d,3,$objPHPExcel,$curseur,$vtr,$a1,$a2,$a3);
					}
				}
			}
			$listeaxe = $a->getAxes();
			$tabtest = array();
			$listes2 = listesDescendances($a,0,$tabtest);	
					
			$d=new Deliberation();
			$curseur=1;
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth(8);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth(10);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setWidth(35);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setWidth(75);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setWidth(10);
			$objPHPExcel->setActiveSheetIndex(0)->getRowDimension('1')->setRowHeight(30);
			$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($styleAll, "A".$curseur.":E".$curseur);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$curseur, 'Num');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$curseur, 'Num délib');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$curseur, 'Date');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$curseur, 'Titre');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$curseur, 'Folio');
			$curseur++;
			if(count($listes2)!=0)
			{
				foreach($listes2[0] as $k => $v){
					
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($styleNiveau1, "A".$curseur.':E'.$curseur);
					$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$curseur.':E'.$curseur);
					$objPHPExcel->setActiveSheetIndex(0)->getRowDimension($curseur)->setRowHeight(39);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$curseur, html_entity_decode($v,ENT_QUOTES));
					$curseur++;
					//verif delib attache
					$listedelib=$d->getDelibByAxe3($k,$_POST['typer'],$_POST['annee1'],$_POST['annee2'],$_POST['annee3']);
					if(count($listedelib)!=0){
						foreach($listedelib as $k1 => $v1){
							$dateexplode=explode(' ',strftime("%A %d %B %Y", strtotime($v1['date'])));
							
							$newformatdate = $tabjour[$dateexplode[0]].' '.$dateexplode[1].' '.$tabmois[$dateexplode[2]].' '.$dateexplode[3];
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$curseur, $v1['num']);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$curseur, $v1['num_delib']);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$curseur, $newformatdate);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$curseur, $v1['libelle']);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$curseur, $v1['folio']);
							$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($styleAll, "A".$curseur.":E".$curseur);
							$curseur++;
						}
					}
					mafunction($listes2,$k,0,$d,2,$objPHPExcel,$curseur,$_POST['typer'],trim($_POST['annee1']),trim($_POST['annee2']),trim($_POST['annee3']));
				}
				
			}
			else{ $montext='<p>Pas de délibérations.</p>';}		
				
			
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->save('fichiers/delibthema2.xlsx');
			unset($objPHPExcel);
			/* fin ecriture fichier thema 3 annees*/
			
			/*debut écriture fichier chrono 3 ans */
			$curseur=0;
			$objPHPExcel2 = new PHPExcel();
			$objPHPExcel2->getProperties()->setCreator("Mélanie Nourrisson")
					->setLastModifiedBy("Mélanie Nourrisson")
					->setTitle("Chrono")
					->setSubject("Chrono");
			$objPHPExcel2->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
			$objPHPExcel2->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
			$objPHPExcel2->getActiveSheet()->getPageMargins()->setHeader(0.2); //inch
			$objPHPExcel2->getActiveSheet()->getPageMargins()->setTop(0.6); //inch
			$objPHPExcel2->getActiveSheet()->getPageMargins()->setRight(0.25); //inch
			$objPHPExcel2->getActiveSheet()->getPageMargins()->setLeft(0.25); //inch
			$objPHPExcel2->getActiveSheet()->getPageMargins()->setBottom(0.6); //inch
			$objPHPExcel2->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);
			$objPHPExcel2->getActiveSheet()->getHeaderFooter()->setOddFooter('&RPage &P / &N');
			$objPHPExcel2->getActiveSheet()->getColumnDimension('A')->setWidth(10);
			$objPHPExcel2->getActiveSheet()->getColumnDimension('B')->setWidth(35);
			$objPHPExcel2->getActiveSheet()->getColumnDimension('C')->setWidth(75);
			$objPHPExcel2->getActiveSheet()->getColumnDimension('D')->setWidth(10);
			$objPHPExcel2->getActiveSheet()->getRowDimension('1')->setRowHeight(30);
			$objPHPExcel2->getActiveSheet()->setSharedStyle($styleAll, "A".$curseur.":D".$curseur);
			$objPHPExcel2->getActiveSheet()->setCellValue('A'.$curseur, 'Num délib');
			$objPHPExcel2->getActiveSheet()->setCellValue('B'.$curseur, 'Date');
			$objPHPExcel2->getActiveSheet()->setCellValue('C'.$curseur, 'Titre');
			$objPHPExcel2->getActiveSheet()->setCellValue('D'.$curseur, 'Folio');
			$curseur++;
	
			$taby = array(trim($_POST['annee1']),trim($_POST['annee2']),trim($_POST['annee3']));
			foreach($taby as $k => $v){
				$objPHPExcel2->getActiveSheet()->setSharedStyle($styleNiveau1, "A".$curseur.':D'.$curseur);
				$objPHPExcel2->getActiveSheet()->mergeCells('A'.$curseur.':D'.$curseur);
				$objPHPExcel2->getActiveSheet()->getRowDimension($curseur)->setRowHeight(39);
				$objPHPExcel2->getActiveSheet()->setCellValue('A'.$curseur, $v);
				$curseur++;
				$listedelib=$d->getDelibChrono2($v,$_POST['typer']);
				if(count($listedelib)!=0){
					foreach($listedelib as $k2 => $v2){
						$dateexplode=explode(' ',strftime("%A %d %B %Y", strtotime($v2['date'])));
						$newformatdate = $tabjour[$dateexplode[0]].' '.$dateexplode[1].' '.$tabmois[$dateexplode[2]].' '.$dateexplode[3];
						$objPHPExcel2->getActiveSheet()->setCellValue('A'.$curseur, $v2['num_delib']);
						$objPHPExcel2->getActiveSheet()->setCellValue('B'.$curseur, $newformatdate);
						$objPHPExcel2->getActiveSheet()->setCellValue('C'.$curseur, $v2['libelle']);
						$objPHPExcel2->getActiveSheet()->setCellValue('D'.$curseur, $v2['folio']);
						$objPHPExcel2->setActiveSheetIndex(0)->setSharedStyle($styleAll, "A".$curseur.":D".$curseur);
						$curseur++;
					}
				}
				
			}
			$objWriter2 = PHPExcel_IOFactory::createWriter($objPHPExcel2, 'Excel2007');
			$objWriter2->save('fichiers/delibchrono.xlsx');
			unset($objPHPExcel2);
			/* chrono 3 ans */
		}
		
		
	}
	/*debut création fichier complet thema*/
	$curseur = 1;
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->getProperties()->setCreator("Mélanie Nourrisson")
			->setLastModifiedBy("Mélanie Nourrisson")
			->setTitle("Thema ")
			->setSubject("Thema ");
	$objPHPExcel->setActiveSheetIndex(0)->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	$objPHPExcel->setActiveSheetIndex(0)->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
	$objPHPExcel->setActiveSheetIndex(0)->getPageMargins()->setHeader(0.2); //inch
	$objPHPExcel->setActiveSheetIndex(0)->getPageMargins()->setTop(0.6); //inch
	$objPHPExcel->setActiveSheetIndex(0)->getPageMargins()->setRight(0.25); //inch
	$objPHPExcel->setActiveSheetIndex(0)->getPageMargins()->setLeft(0.25); //inch
	$objPHPExcel->setActiveSheetIndex(0)->getPageMargins()->setBottom(0.6); //inch
	$objPHPExcel->setActiveSheetIndex(0)->getPageSetup()->setHorizontalCentered(true);
	$objPHPExcel->setActiveSheetIndex(0)->getHeaderFooter()->setOddFooter('&RPage &P / &N');
	/*$styleNiveau1 = new PHPExcel_Style();
	$styleAll = new PHPExcel_Style();
	$styleNiveau1->applyFromArray(array(
		'font' => array('bold'=>true,'size'=>16),
		'fill' 	=> array('type'=> PHPExcel_Style_Fill::FILL_SOLID,'color'=> array('argb' => 'FF92d050')),
		'borders' => array('bottom'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),'top'=> array('style' => PHPExcel_Style_Border::BORDER_THIN),'left'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),'right'=> array('style' => PHPExcel_Style_Border::BORDER_THIN)),
		'alignment'=>array('vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT)				
	));		

	$styleAll->applyFromArray(array( 
		'borders' => array('bottom'=> array('style' => PHPExcel_Style_Border::BORDER_THIN),'top'=> array('style' => PHPExcel_Style_Border::BORDER_THIN),'left'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),'right'=> array('style' => PHPExcel_Style_Border::BORDER_THIN)),
		'alignment'=>array('wrap' => true)
	));	
	*/
	function mafunction2($liste,$cle,$parent,$d,$niveau,$objPHPExcel,&$curseur,$ktr,$vtr){
		$tabjour=array('Monday'=>'Lundi', 'Tuesday'=>'Mardi', 'Wednesday'=>'Mercredi', 'Thursday'=>'Jeudi', 'Friday'=>'Vendredi', 'Saturday'=>'Samedi', 'Sunday'=>'Dimanche');
		$tabmois = array('January'=>'Janvier', 'February'=>'Février', 'March'=>'Mars', 'April'=>'Avril', 'May'=>'Mai', 'June'=>'Juin', 'July'=>'Juillet', 'August'=>'Août', 'September'=>'Septembre', 'October'=>'Octobre', 'November'=>'Novembre', 'December'=>'Décembre');
		$styleNiveau2 = new PHPExcel_Style();
		$styleNiveau2->applyFromArray(array(
			'font' => array('bold'=>true,'size'=>12),
			'fill' 	=> array('type'=> PHPExcel_Style_Fill::FILL_SOLID,'color'=> array('argb' => 'FFd9d9d9')),
			'borders' => array('bottom'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),'top'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),'left'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),'right'=> array('style' => PHPExcel_Style_Border::BORDER_THIN))
		));	
		
		$styleNiveau3 = new PHPExcel_Style();
		$styleNiveau3->applyFromArray(array(
			'font' => array('bold'=>true,'size'=>8),
			'fill' 	=> array('type'=> PHPExcel_Style_Fill::FILL_SOLID,'color'=> array('argb' => 'FFfcd5b4')),
			'borders' => array('bottom'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),'top'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),'left'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),'right'=> array('style' => PHPExcel_Style_Border::BORDER_THIN))
		));	
		$styleAll = new PHPExcel_Style();
		$styleAll->applyFromArray(array( 
			'borders' => array('bottom'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),'top'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),'left'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),'right'=> array('style' => PHPExcel_Style_Border::BORDER_THIN)),
			'alignment'=>array('wrap' => true,'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT)	
		));	
					 
		if(isset($liste[$cle]) && count($liste[$cle])!=0){
			
			foreach($liste[$cle] as $k => $v){
				if($niveau==2)
				{	
					$objPHPExcel->setActiveSheetIndex($ktr)->setSharedStyle($styleNiveau2, "A".$curseur.':E'.$curseur);
					$objPHPExcel->setActiveSheetIndex($ktr)->getRowDimension($curseur)->setRowHeight(34);
				}
				elseif($niveau==3)
				{
					$objPHPExcel->setActiveSheetIndex($ktr)->setSharedStyle($styleNiveau3, "A".$curseur.':E'.$curseur);
				}
				$objPHPExcel->setActiveSheetIndex($ktr)->mergeCells('A'.$curseur.':E'.$curseur);
				$objPHPExcel->setActiveSheetIndex($ktr)->setCellValue('A'.$curseur, html_entity_decode($v,ENT_QUOTES));
				$curseur++;
				$listedelib=$d->getDelibByAxe4($k,$vtr);
				
				if(count($listedelib)!=0){
					foreach($listedelib as $k1 => $v1){
						$dateexplode=explode(' ',strftime("%A %d %B %Y", strtotime($v1['date'])));
						$newformatdate = $tabjour[$dateexplode[0]].' '.$dateexplode[1].' '.$tabmois[$dateexplode[2]].' '.$dateexplode[3];
						
						$objPHPExcel->setActiveSheetIndex($ktr)->setCellValue('A'.$curseur, $v1['num']);
						$objPHPExcel->setActiveSheetIndex($ktr)->setCellValue('B'.$curseur, $v1['num_delib']);
						$objPHPExcel->setActiveSheetIndex($ktr)->setCellValue('C'.$curseur, $newformatdate);
						$objPHPExcel->setActiveSheetIndex($ktr)->setCellValue('D'.$curseur, $v1['libelle']);
						$objPHPExcel->setActiveSheetIndex($ktr)->setCellValue('E'.$curseur, $v1['folio']);
						$objPHPExcel->setActiveSheetIndex($ktr)->setSharedStyle($styleAll, "A".$curseur.":E".$curseur);
						$curseur++;
					}
				}
				mafunction2($liste,$k,$cle,$d,3,$objPHPExcel,$curseur,$ktr,$vtr);
			}
			
		}
		
	}
	$listetr = $tr->getTR();
	$listeaxe = $a->getAxes();
	$tabtest = array();
	$listes2 = listesDescendances($a,0,$tabtest);
	if(count($listetr)!=0)
	{
		foreach($listetr as $ktr => $vtr)
		{	
			
			$d=new Deliberation();
			$curseur=1;
			$objPHPExcel->createSheet();
			$objPHPExcel->setActiveSheetIndex($ktr)->setTitle(substr($vtr['libeltp'],0,31));
			$objPHPExcel->setActiveSheetIndex($ktr)->getColumnDimension('A')->setWidth(8);
			$objPHPExcel->setActiveSheetIndex($ktr)->getColumnDimension('B')->setWidth(10);
			$objPHPExcel->setActiveSheetIndex($ktr)->getColumnDimension('C')->setWidth(35);
			$objPHPExcel->setActiveSheetIndex($ktr)->getColumnDimension('D')->setWidth(75);
			$objPHPExcel->setActiveSheetIndex($ktr)->getColumnDimension('E')->setWidth(10);
			$objPHPExcel->setActiveSheetIndex($ktr)->getRowDimension('1')->setRowHeight(30);
			$objPHPExcel->setActiveSheetIndex($ktr)->setSharedStyle($styleAll, "A".$curseur.":E".$curseur);
			$objPHPExcel->setActiveSheetIndex($ktr)->setCellValue('A'.$curseur, 'Num');
			$objPHPExcel->setActiveSheetIndex($ktr)->setCellValue('B'.$curseur, 'Num délib');
			$objPHPExcel->setActiveSheetIndex($ktr)->setCellValue('C'.$curseur, 'Date');
			$objPHPExcel->setActiveSheetIndex($ktr)->setCellValue('D'.$curseur, 'Titre');
			$objPHPExcel->setActiveSheetIndex($ktr)->setCellValue('E'.$curseur, 'Folio');
			$curseur++;
			if(count($listes2)!=0)
			{
				foreach($listes2[0] as $k => $v){
					
					$objPHPExcel->setActiveSheetIndex($ktr)->setSharedStyle($styleNiveau1, "A".$curseur.':E'.$curseur);
					$objPHPExcel->setActiveSheetIndex($ktr)->mergeCells('A'.$curseur.':E'.$curseur);
					$objPHPExcel->setActiveSheetIndex($ktr)->getRowDimension($curseur)->setRowHeight(39);
					$objPHPExcel->setActiveSheetIndex($ktr)->setCellValue('A'.$curseur, html_entity_decode($v,ENT_QUOTES));
					$curseur++;
					//verif delib attache
					$listedelib=$d->getDelibByAxe4($k,$vtr['id_type_reunion']);
					if(count($listedelib)!=0){
						foreach($listedelib as $k1 => $v1){
							$dateexplode=explode(' ',strftime("%A %d %B %Y", strtotime($v1['date'])));
							
							$newformatdate = $tabjour[$dateexplode[0]].' '.$dateexplode[1].' '.$tabmois[$dateexplode[2]].' '.$dateexplode[3];
							$objPHPExcel->setActiveSheetIndex($ktr)->setCellValue('A'.$curseur, $v1['num']);
							$objPHPExcel->setActiveSheetIndex($ktr)->setCellValue('B'.$curseur, $v1['num_delib']);
							$objPHPExcel->setActiveSheetIndex($ktr)->setCellValue('C'.$curseur, $newformatdate);
							$objPHPExcel->setActiveSheetIndex($ktr)->setCellValue('D'.$curseur, $v1['libelle']);
							$objPHPExcel->setActiveSheetIndex($ktr)->setCellValue('E'.$curseur, $v1['folio']);
							$objPHPExcel->setActiveSheetIndex($ktr)->setSharedStyle($styleAll, "A".$curseur.":E".$curseur);
							$curseur++;
						}
					}
					mafunction2($listes2,$k,0,$d,2,$objPHPExcel,$curseur,$ktr,$vtr['id_type_reunion']);
				}
				
			}
			else{ $montext='<p>Pas de délibérations.</p>';}		
		}
	}
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('fichiers/delibthemacomplet2.xlsx');
	unset($objPHPExcel);
	/* fin ecriture fichier thema complet*/
	
	
	/*fichier chrono complet*/
	$listetr = $tr->getTR();
	$curseur=0;
	$objPHPExcel3 = new PHPExcel();
	$objPHPExcel3->getProperties()->setCreator("Mélanie Nourrisson")
			->setLastModifiedBy("Mélanie Nourrisson")
			->setTitle("Chrono")
			->setSubject("Chrono");
	
	 
	$objPHPExcel3->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	$objPHPExcel3->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
	$objPHPExcel3->getActiveSheet()->getPageMargins()->setHeader(0.2); //inch
	$objPHPExcel3->getActiveSheet()->getPageMargins()->setTop(0.6); //inch
	$objPHPExcel3->getActiveSheet()->getPageMargins()->setRight(0.25); //inch
	$objPHPExcel3->getActiveSheet()->getPageMargins()->setLeft(0.25); //inch
	$objPHPExcel3->getActiveSheet()->getPageMargins()->setBottom(0.6); //inch
	$objPHPExcel3->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);
	$objPHPExcel3->getActiveSheet()->getHeaderFooter()->setOddFooter('&RPage &P / &N');

	$curseur++;
	
	
	if(count($listetr)!=0)
	{	//recupere les objets
		foreach($listetr as $ktr => $vtr)
		{
			$d=new Deliberation();
			$curseur=1;
			$objPHPExcel3->createSheet();
			$objPHPExcel3->setActiveSheetIndex($ktr)->setTitle(substr($vtr['libeltp'],0,31));
			$objPHPExcel3->setActiveSheetIndex($ktr)->getColumnDimension('A')->setWidth(10);
			$objPHPExcel3->setActiveSheetIndex($ktr)->getColumnDimension('B')->setWidth(35);
			$objPHPExcel3->setActiveSheetIndex($ktr)->getColumnDimension('C')->setWidth(75);
			$objPHPExcel3->setActiveSheetIndex($ktr)->getColumnDimension('D')->setWidth(10);
			$objPHPExcel3->setActiveSheetIndex($ktr)->getRowDimension('1')->setRowHeight(30);
			$objPHPExcel3->setActiveSheetIndex($ktr)->setSharedStyle($styleAll, "A".$curseur.":D".$curseur);
			$objPHPExcel3->setActiveSheetIndex($ktr)->setCellValue('A'.$curseur, 'Num délib');
			$objPHPExcel3->setActiveSheetIndex($ktr)->setCellValue('B'.$curseur, 'Date');
			$objPHPExcel3->setActiveSheetIndex($ktr)->setCellValue('C'.$curseur, 'Titre');
			$objPHPExcel3->setActiveSheetIndex($ktr)->setCellValue('D'.$curseur, 'Folio');
			$listedate = $d->getDelibChronoAnnee($vtr['id_type_reunion']);
			if(count($listedate)!=0)
			{
				foreach($listedate as $kd => $vd)
				{
					$objPHPExcel3->setActiveSheetIndex($ktr)->setSharedStyle($styleNiveau1, "A".$curseur.':D'.$curseur);
					$objPHPExcel3->setActiveSheetIndex($ktr)->mergeCells('A'.$curseur.':D'.$curseur);
					$objPHPExcel3->setActiveSheetIndex($ktr)->getRowDimension($curseur)->setRowHeight(39);
					$objPHPExcel3->setActiveSheetIndex($ktr)->setCellValue('A'.$curseur, $vd['annee']);
					$curseur++;
					$listedelib=$d->getDelibChrono3($vd['annee'],$vtr['id_type_reunion']);
					if(count($listedelib)!=0){
						foreach($listedelib as $k2 => $v2){
							$dateexplode=explode(' ',strftime("%A %d %B %Y", strtotime($v2['date'])));
							$newformatdate = $tabjour[$dateexplode[0]].' '.$dateexplode[1].' '.$tabmois[$dateexplode[2]].' '.$dateexplode[3];
							$objPHPExcel3->setActiveSheetIndex($ktr)->setCellValue('A'.$curseur, $v2['num_delib']);
							$objPHPExcel3->setActiveSheetIndex($ktr)->setCellValue('B'.$curseur, $newformatdate);
							$objPHPExcel3->setActiveSheetIndex($ktr)->setCellValue('C'.$curseur, $v2['libelle']);
							$objPHPExcel3->setActiveSheetIndex($ktr)->setCellValue('D'.$curseur, $v2['folio']);
							$objPHPExcel3->setActiveSheetIndex($ktr)->setSharedStyle($styleAll, "A".$curseur.":D".$curseur);
							$curseur++;
						}
					}
				}
			}
		}
	}
	$objWriter3 = PHPExcel_IOFactory::createWriter($objPHPExcel3, 'Excel2007');
	$objWriter3->save('fichiers/delibchronocomplet2.xlsx');
	unset($objPHPExcel3);
	/*fin fichier chrono complet*/

	
	
	
	
	
	


	require_once('head.php');
	require_once('nav.php');
?>
	<section id="content">
		<h2>Téléchargement</h2>
		<form action="" method="post">
			<label for="annee1">Année 1 : </label><input type="text" maxlength="4" value="<?php if(isset($_POST['annee1']) && $_POST['annee1']!='') echo $_POST['annee1']; ?>" id="annee1" name="annee1"/>
			<label for="annee2">Année 2 : </label><input type="text" maxlength="4" value="<?php if(isset($_POST['annee2']) && $_POST['annee2']!='') echo $_POST['annee2']; ?>" id="annee2" name="annee2"/>
			<label for="annee3">Année 3 : </label><input type="text" maxlength="4" value="<?php if(isset($_POST['annee3']) && $_POST['annee3']!='') echo $_POST['annee3']; ?>" id="annee3" name="annee3"/>
			<label for="typer">Type réunion : </label>
			<?php
				$listecompletetr = $tr->getTypesReunion();
				if(count($listecompletetr)!=0){
					echo '<select name="typer"><option value="0"></option>';
					foreach($listecompletetr as $ktrc => $vtrc){
						$selected='';
					if(isset($_POST['typer']) && $_POST['typer']==$vtrc['id_type_reunion']){ $selected='selected="selected"';}
						echo '<option value="'.$vtrc['id_type_reunion'].'" '.$selected.'>'.$vtrc['libelle'].'</option>';
					}
					echo '</select>';
				}
			?>
			<input type="submit" name="generer" value="Générer"/>
		</form><br/>
		<?php if(isset($_POST['generer'])){?><p><a href="fichiers/delibthema2.xlsx" >Télécharger le fichier classement thématique 3 ans</a></p><br/>
		<p><a href="fichiers/delibchrono.xlsx" >Télécharger le fichier classement chronologique 3 ans</a></p><br/><?php }?>
		<p><a href="fichiers/delibthemacomplet2.xlsx" >Télécharger le fichier classement thématique complet</a></p><br/>
		<p><a href="fichiers/delibchronocomplet2.xlsx" >Télécharger le fichier classement chronologique complet</a></p><br/>
	
	</section>
<?php

require_once('footer.php');

?>
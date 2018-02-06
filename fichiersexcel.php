<?php 
	session_start();
	include_once('classes/connect.php');
	include_once('classes/Deliberation.php');
	include_once('classes/Charte.php');
	include_once('classes/PHPExcel.php');
	
	$current='fichiersexcel';
	$tabjour=array('Monday'=>'Lundi', 'Tuesday'=>'Mardi', 'Wednesday'=>'Mercredi', 'Thursday'=>'Jeudi', 'Friday'=>'Vendredi', 'Saturday'=>'Samedi', 'Sunday'=>'Dimanche');
	$tabmois = array('January'=>'Janvier', 'February'=>'Février', 'March'=>'Mars', 'April'=>'Avril', 'May'=>'Mai', 'June'=>'Juin', 'July'=>'Juillet', 'August'=>'Août', 'September'=>'Septembre', 'October'=>'Octobre', 'November'=>'Novembre', 'December'=>'Décembre');
	$curseur = 1;
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->getProperties()->setCreator("Mélanie Nourrisson")
			->setLastModifiedBy("Mélanie Nourrisson")
			->setTitle("Thema ")
			->setSubject("Thema ");
	$styleNiveau1 = new PHPExcel_Style();
	$styleAll = new PHPExcel_Style();

	$styleNiveau1->applyFromArray(
			array(
					'font' => array(
								'bold'=>true,
								'size'=>16
					),
					'fill' 	=> array(
										'type'		=> PHPExcel_Style_Fill::FILL_SOLID,
										'color'		=> array('argb' => 'FF92d050')
									),
				  'borders' => array(
										'bottom'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
										'top'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
										'left'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
										'right'		=> array('style' => PHPExcel_Style_Border::BORDER_THIN)
									),
					'alignment'=>array(
									'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
									'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
					)				
				 ));		

	$styleAll->applyFromArray(
			array( 'borders' => array(
										'bottom'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
										'top'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
										'left'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
										'right'		=> array('style' => PHPExcel_Style_Border::BORDER_THIN)
									),
					'alignment'=>array(
									'wrap' => true
					)
				 ));	
	 
	$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); //inch
	$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.6); //inch
	$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.25); //inch
	$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.25); //inch
	$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.6); //inch
	$objPHPExcel->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);
	$objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddFooter('&RPage &P / &N');
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(35);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(75);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(30);
	$objPHPExcel->getActiveSheet()->setSharedStyle($styleAll, "A".$curseur.":E".$curseur);
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$curseur, 'Num');
	$objPHPExcel->getActiveSheet()->setCellValue('B'.$curseur, 'Num délib');
	$objPHPExcel->getActiveSheet()->setCellValue('C'.$curseur, 'Date');
	$objPHPExcel->getActiveSheet()->setCellValue('D'.$curseur, 'Titre');
	$objPHPExcel->getActiveSheet()->setCellValue('E'.$curseur, 'Folio');
	$curseur++;
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
	function mafunction($liste,$cle,$parent,$d,$niveau,$objPHPExcel,&$curseur){
		$tabjour=array('Monday'=>'Lundi', 'Tuesday'=>'Mardi', 'Wednesday'=>'Mercredi', 'Thursday'=>'Jeudi', 'Friday'=>'Vendredi', 'Saturday'=>'Samedi', 'Sunday'=>'Dimanche');
		$tabmois = array('January'=>'Janvier', 'February'=>'Février', 'March'=>'Mars', 'April'=>'Avril', 'May'=>'Mai', 'June'=>'Juin', 'July'=>'Juillet', 'August'=>'Août', 'September'=>'Septembre', 'October'=>'Octobre', 'November'=>'Novembre', 'December'=>'Décembre');
		$styleNiveau2 = new PHPExcel_Style();

		$styleNiveau2->applyFromArray(
			array('font' => array(
								'bold'=>true,
								'size'=>12
					),
					'fill' 	=> array(
										'type'		=> PHPExcel_Style_Fill::FILL_SOLID,
										'color'		=> array('argb' => 'FFd9d9d9')
									),
				  'borders' => array(
										'bottom'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
										'top'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
										'left'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
										'right'		=> array('style' => PHPExcel_Style_Border::BORDER_THIN)
									)
				 ));	
		$styleNiveau3 = new PHPExcel_Style();

		$styleNiveau3->applyFromArray(
			array('font' => array(
								'bold'=>true,
								'size'=>8
					),
					'fill' 	=> array(
										'type'		=> PHPExcel_Style_Fill::FILL_SOLID,
										'color'		=> array('argb' => 'FFfcd5b4')
									),
				  'borders' => array(
										'bottom'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
										'top'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
										'left'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
										'right'		=> array('style' => PHPExcel_Style_Border::BORDER_THIN)
									)
				 ));	
		$styleAll = new PHPExcel_Style();
		$styleAll->applyFromArray(
			array( 'borders' => array(
										'bottom'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
										'top'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
										'left'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
										'right'		=> array('style' => PHPExcel_Style_Border::BORDER_THIN)
									),
					'alignment'=>array(
									'wrap' => true,
									'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
					)	
				 ));	
	 				 
		if(isset($liste[$cle]) && count($liste[$cle])!=0){
			
			foreach($liste[$cle] as $k => $v){
				if($niveau==2)
				{	
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($styleNiveau2, "A".$curseur.':E'.$curseur);
					$objPHPExcel->setActiveSheetIndex(0)->getRowDimension($curseur)->setRowHeight(34);
				}
				elseif($niveau==3)
				{$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($styleNiveau3, "A".$curseur.':E'.$curseur);}
				$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$curseur.':E'.$curseur);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$curseur, html_entity_decode($v,ENT_QUOTES));
				$curseur++;
				$listedelib=$d->getDelibByAxe2($k);
				
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
				mafunction($liste,$k,$cle,$d,3,$objPHPExcel,$curseur);
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
		foreach($listes2[0] as $k => $v){
			
			$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($styleNiveau1, "A".$curseur.':E'.$curseur);
			$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$curseur.':E'.$curseur);
			$objPHPExcel->setActiveSheetIndex(0)->getRowDimension($curseur)->setRowHeight(39);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$curseur, html_entity_decode($v,ENT_QUOTES));
			$curseur++;
			//verif delib attache
			$listedelib=$d->getDelibByAxe2($k);
			if(count($listedelib)!=0){
				foreach($listedelib as $k1 => $v1){
					$dateexplode=explode(' ',strftime("%A %d %B %Y", strtotime($v1['date'])));
					
					$newformatdate = $tabjour[$dateexplode[0]].' '.$dateexplode[1].' '.$tabmois[$dateexplode[2]].' '.$dateexplode[3];
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$curseur, $v1['num']);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$curseur, $v1['num_delib']);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$curseur, $newformatdate);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$curseur, $v1['libelle']);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$curseur, $v1['folio']);
					$curseur++;
				}
			}
			mafunction($listes2,$k,0,$d,2,$objPHPExcel,$curseur);
			
		}
		
	}
	else{ $montext='<p>Pas de délibérations.</p>';}		
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('fichiers/delibthema.xlsx');
	unset($objPHPExcel);
	
	$curseur=0;
	$objPHPExcel2 = new PHPExcel();
	$objPHPExcel2->getProperties()->setCreator("Mélanie Nourrisson")
			->setLastModifiedBy("Mélanie Nourrisson")
			->setTitle("Chrono")
			->setSubject("Chrono");
	$styleNiveau1 = new PHPExcel_Style();
	$styleAll = new PHPExcel_Style();

	$styleNiveau1->applyFromArray(
			array(
					'font' => array(
								'bold'=>true,
								'size'=>16
					),
					'fill' 	=> array(
										'type'		=> PHPExcel_Style_Fill::FILL_SOLID,
										'color'		=> array('argb' => 'FF92d050')
									),
				  'borders' => array(
										'bottom'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
										'top'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
										'left'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
										'right'		=> array('style' => PHPExcel_Style_Border::BORDER_THIN)
									),
					'alignment'=>array(
									'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
									'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
					)				
				 ));		

	$styleAll->applyFromArray(
			array( 'borders' => array(
										'bottom'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
										'top'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
										'left'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
										'right'		=> array('style' => PHPExcel_Style_Border::BORDER_THIN)
									),
					'alignment'=>array(
									'wrap' => true
					)
				 ));	
	 
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
	
	$taby = array(date('Y')-3,date('Y')-2,date('Y')-1);
	foreach($taby as $k => $v){
		$objPHPExcel2->getActiveSheet()->setSharedStyle($styleNiveau1, "A".$curseur.':D'.$curseur);
		$objPHPExcel2->getActiveSheet()->mergeCells('A'.$curseur.':D'.$curseur);
		$objPHPExcel2->getActiveSheet()->getRowDimension($curseur)->setRowHeight(39);
		$objPHPExcel2->getActiveSheet()->setCellValue('A'.$curseur, $v);
		$curseur++;
		$listedelib=$d->getDelibChrono($v);
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
	
	$curseur=0;
	$objPHPExcel3 = new PHPExcel();
	$objPHPExcel3->getProperties()->setCreator("Mélanie Nourrisson")
			->setLastModifiedBy("Mélanie Nourrisson")
			->setTitle("Chrono")
			->setSubject("Chrono");
	$styleNiveau1 = new PHPExcel_Style();
	$styleAll = new PHPExcel_Style();

	$styleNiveau1->applyFromArray(
			array(
					'font' => array(
								'bold'=>true,
								'size'=>16
					),
					'fill' 	=> array(
										'type'		=> PHPExcel_Style_Fill::FILL_SOLID,
										'color'		=> array('argb' => 'FF92d050')
									),
				  'borders' => array(
										'bottom'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
										'top'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
										'left'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
										'right'		=> array('style' => PHPExcel_Style_Border::BORDER_THIN)
									),
					'alignment'=>array(
									'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
									'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
					)				
				 ));		

	$styleAll->applyFromArray(
			array( 'borders' => array(
										'bottom'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
										'top'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
										'left'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
										'right'		=> array('style' => PHPExcel_Style_Border::BORDER_THIN)
									),
					'alignment'=>array(
									'wrap' => true
					)
				 ));	
	 
	$objPHPExcel3->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	$objPHPExcel3->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
	$objPHPExcel3->getActiveSheet()->getPageMargins()->setHeader(0.2); //inch
	$objPHPExcel3->getActiveSheet()->getPageMargins()->setTop(0.6); //inch
	$objPHPExcel3->getActiveSheet()->getPageMargins()->setRight(0.25); //inch
	$objPHPExcel3->getActiveSheet()->getPageMargins()->setLeft(0.25); //inch
	$objPHPExcel3->getActiveSheet()->getPageMargins()->setBottom(0.6); //inch
	$objPHPExcel3->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);
	$objPHPExcel3->getActiveSheet()->getHeaderFooter()->setOddFooter('&RPage &P / &N');
	$objPHPExcel3->getActiveSheet()->getColumnDimension('A')->setWidth(10);
	$objPHPExcel3->getActiveSheet()->getColumnDimension('B')->setWidth(35);
	$objPHPExcel3->getActiveSheet()->getColumnDimension('C')->setWidth(75);
	$objPHPExcel3->getActiveSheet()->getColumnDimension('D')->setWidth(10);
	$objPHPExcel3->getActiveSheet()->getRowDimension('1')->setRowHeight(30);
	$objPHPExcel3->getActiveSheet()->setSharedStyle($styleAll, "A".$curseur.":D".$curseur);
	$objPHPExcel3->getActiveSheet()->setCellValue('A'.$curseur, 'Num délib');
	$objPHPExcel3->getActiveSheet()->setCellValue('B'.$curseur, 'Date');
	$objPHPExcel3->getActiveSheet()->setCellValue('C'.$curseur, 'Titre');
	$objPHPExcel3->getActiveSheet()->setCellValue('D'.$curseur, 'Folio');
	$curseur++;
	
	$taby = array(date('Y')-3,date('Y')-2,date('Y')-1,date('Y'));
	foreach($taby as $k => $v){
		$objPHPExcel3->getActiveSheet()->setSharedStyle($styleNiveau1, "A".$curseur.':D'.$curseur);
		$objPHPExcel3->getActiveSheet()->mergeCells('A'.$curseur.':D'.$curseur);
		$objPHPExcel3->getActiveSheet()->getRowDimension($curseur)->setRowHeight(39);
		$objPHPExcel3->getActiveSheet()->setCellValue('A'.$curseur, $v);
		$curseur++;
		$listedelib=$d->getDelibChrono($v);
		if(count($listedelib)!=0){
			foreach($listedelib as $k2 => $v2){
				$dateexplode=explode(' ',strftime("%A %d %B %Y", strtotime($v2['date'])));
				$newformatdate = $tabjour[$dateexplode[0]].' '.$dateexplode[1].' '.$tabmois[$dateexplode[2]].' '.$dateexplode[3];
				$objPHPExcel3->getActiveSheet()->setCellValue('A'.$curseur, $v2['num_delib']);
				$objPHPExcel3->getActiveSheet()->setCellValue('B'.$curseur, $newformatdate);
				$objPHPExcel3->getActiveSheet()->setCellValue('C'.$curseur, $v2['libelle']);
				$objPHPExcel3->getActiveSheet()->setCellValue('D'.$curseur, $v2['folio']);
				$objPHPExcel3->setActiveSheetIndex(0)->setSharedStyle($styleAll, "A".$curseur.":D".$curseur);
				$curseur++;
			}
		}
		
	}
	$objWriter3 = PHPExcel_IOFactory::createWriter($objPHPExcel3, 'Excel2007');
	$objWriter3->save('fichiers/delibchronocomplet.xlsx');
	unset($objPHPExcel3);
	
	
	$curseur=0;
	$objPHPExcel4 = new PHPExcel();
	$objPHPExcel4->getProperties()->setCreator("Mélanie Nourrisson")
			->setLastModifiedBy("Mélanie Nourrisson")
			->setTitle("Thema ")
			->setSubject("Thema ");
	$styleNiveau1 = new PHPExcel_Style();
	$styleAll = new PHPExcel_Style();

	$styleNiveau1->applyFromArray(
			array(
					'font' => array(
								'bold'=>true,
								'size'=>16
					),
					'fill' 	=> array(
										'type'		=> PHPExcel_Style_Fill::FILL_SOLID,
										'color'		=> array('argb' => 'FF92d050')
									),
				  'borders' => array(
										'bottom'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
										'top'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
										'left'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
										'right'		=> array('style' => PHPExcel_Style_Border::BORDER_THIN)
									),
					'alignment'=>array(
									'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
									'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
					)				
				 ));		

	$styleAll->applyFromArray(
			array( 'borders' => array(
										'bottom'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
										'top'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
										'left'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
										'right'		=> array('style' => PHPExcel_Style_Border::BORDER_THIN)
									),
					'alignment'=>array(
									'wrap' => true
					)
				 ));	
	 
	$objPHPExcel4->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	$objPHPExcel4->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
	$objPHPExcel4->getActiveSheet()->getPageMargins()->setHeader(0.2); //inch
	$objPHPExcel4->getActiveSheet()->getPageMargins()->setTop(0.6); //inch
	$objPHPExcel4->getActiveSheet()->getPageMargins()->setRight(0.25); //inch
	$objPHPExcel4->getActiveSheet()->getPageMargins()->setLeft(0.25); //inch
	$objPHPExcel4->getActiveSheet()->getPageMargins()->setBottom(0.6); //inch
	$objPHPExcel4->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);
	$objPHPExcel4->getActiveSheet()->getHeaderFooter()->setOddFooter('&RPage &P / &N');
	$objPHPExcel4->getActiveSheet()->getColumnDimension('A')->setWidth(8);
	$objPHPExcel4->getActiveSheet()->getColumnDimension('B')->setWidth(10);
	$objPHPExcel4->getActiveSheet()->getColumnDimension('C')->setWidth(35);
	$objPHPExcel4->getActiveSheet()->getColumnDimension('D')->setWidth(75);
	$objPHPExcel4->getActiveSheet()->getColumnDimension('E')->setWidth(10);
	$objPHPExcel4->getActiveSheet()->getRowDimension('1')->setRowHeight(30);
	$objPHPExcel4->getActiveSheet()->setSharedStyle($styleAll, "A".$curseur.":E".$curseur);
	$objPHPExcel4->getActiveSheet()->setCellValue('A'.$curseur, 'Num');
	$objPHPExcel4->getActiveSheet()->setCellValue('B'.$curseur, 'Num délib');
	$objPHPExcel4->getActiveSheet()->setCellValue('C'.$curseur, 'Date');
	$objPHPExcel4->getActiveSheet()->setCellValue('D'.$curseur, 'Titre');
	$objPHPExcel4->getActiveSheet()->setCellValue('E'.$curseur, 'Folio');
	$curseur++;
	
	function mafunction2($liste,$cle,$parent,$d,$niveau,$objPHPExcel4,&$curseur){
		$tabjour=array('Monday'=>'Lundi', 'Tuesday'=>'Mardi', 'Wednesday'=>'Mercredi', 'Thursday'=>'Jeudi', 'Friday'=>'Vendredi', 'Saturday'=>'Samedi', 'Sunday'=>'Dimanche');
		$tabmois = array('January'=>'Janvier', 'February'=>'Février', 'March'=>'Mars', 'April'=>'Avril', 'May'=>'Mai', 'June'=>'Juin', 'July'=>'Juillet', 'August'=>'Août', 'September'=>'Septembre', 'October'=>'Octobre', 'November'=>'Novembre', 'December'=>'Décembre');
		$styleNiveau2 = new PHPExcel_Style();

		$styleNiveau2->applyFromArray(
			array('font' => array(
								'bold'=>true,
								'size'=>12
					),
					'fill' 	=> array(
										'type'		=> PHPExcel_Style_Fill::FILL_SOLID,
										'color'		=> array('argb' => 'FFd9d9d9')
									),
				  'borders' => array(
										'bottom'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
										'top'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
										'left'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
										'right'		=> array('style' => PHPExcel_Style_Border::BORDER_THIN)
									)
				 ));	
		$styleNiveau3 = new PHPExcel_Style();

		$styleNiveau3->applyFromArray(
			array('font' => array(
								'bold'=>true,
								'size'=>8
					),
					'fill' 	=> array(
										'type'		=> PHPExcel_Style_Fill::FILL_SOLID,
										'color'		=> array('argb' => 'FFfcd5b4')
									),
				  'borders' => array(
										'bottom'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
										'top'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
										'left'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
										'right'		=> array('style' => PHPExcel_Style_Border::BORDER_THIN)
									)
				 ));	
		$styleAll = new PHPExcel_Style();
		$styleAll->applyFromArray(
			array( 'borders' => array(
										'bottom'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
										'top'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
										'left'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
										'right'		=> array('style' => PHPExcel_Style_Border::BORDER_THIN)
									),
					'alignment'=>array(
									'wrap' => true,
									'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
					)	
				 ));	
	 				 
		if(isset($liste[$cle]) && count($liste[$cle])!=0){
			
			foreach($liste[$cle] as $k => $v){
				if($niveau==2)
				{	
					$objPHPExcel4->setActiveSheetIndex(0)->setSharedStyle($styleNiveau2, "A".$curseur.':E'.$curseur);
					$objPHPExcel4->setActiveSheetIndex(0)->getRowDimension($curseur)->setRowHeight(34);
				}
				elseif($niveau==3)
				{$objPHPExcel4->setActiveSheetIndex(0)->setSharedStyle($styleNiveau3, "A".$curseur.':E'.$curseur);}
				$objPHPExcel4->setActiveSheetIndex(0)->mergeCells('A'.$curseur.':E'.$curseur);
				$objPHPExcel4->setActiveSheetIndex(0)->setCellValue('A'.$curseur, html_entity_decode($v,ENT_QUOTES));
				$curseur++;
				$listedelib=$d->getDelibByAxe($k);
				
				if(count($listedelib)!=0){
					foreach($listedelib as $k1 => $v1){
						$dateexplode=explode(' ',strftime("%A %d %B %Y", strtotime($v1['date'])));
						$newformatdate = $tabjour[$dateexplode[0]].' '.$dateexplode[1].' '.$tabmois[$dateexplode[2]].' '.$dateexplode[3];
						
						$objPHPExcel4->getActiveSheet()->setCellValue('A'.$curseur, $v1['num']);
						$objPHPExcel4->getActiveSheet()->setCellValue('B'.$curseur, $v1['num_delib']);
						$objPHPExcel4->getActiveSheet()->setCellValue('C'.$curseur, $newformatdate);
						$objPHPExcel4->getActiveSheet()->setCellValue('D'.$curseur, $v1['libelle']);
						$objPHPExcel4->getActiveSheet()->setCellValue('E'.$curseur, $v1['folio']);
						$objPHPExcel4->getActiveSheet()->setSharedStyle($styleAll, "A".$curseur.":E".$curseur);
						$curseur++;
					}
				}
				mafunction2($liste,$k,$cle,$d,3,$objPHPExcel4,$curseur);
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
		foreach($listes2[0] as $k => $v){
			
			$objPHPExcel4->getActiveSheet()->setSharedStyle($styleNiveau1, "A".$curseur.':E'.$curseur);
			$objPHPExcel4->getActiveSheet()->mergeCells('A'.$curseur.':E'.$curseur);
			$objPHPExcel4->getActiveSheet()->getRowDimension($curseur)->setRowHeight(39);
			$objPHPExcel4->getActiveSheet()->setCellValue('A'.$curseur, html_entity_decode($v,ENT_QUOTES));
			$curseur++;
			//verif delib attache
			$listedelib=$d->getDelibByAxe($k);
			if(count($listedelib)!=0){
				foreach($listedelib as $k1 => $v1){
					$dateexplode=explode(' ',strftime("%A %d %B %Y", strtotime($v1['date'])));
					
					$newformatdate = $tabjour[$dateexplode[0]].' '.$dateexplode[1].' '.$tabmois[$dateexplode[2]].' '.$dateexplode[3];
					$objPHPExcel4->getActiveSheet()->setCellValue('A'.$curseur, $v1['num']);
					$objPHPExcel4->getActiveSheet()->setCellValue('B'.$curseur, $v1['num_delib']);
					$objPHPExcel4->getActiveSheet()->setCellValue('C'.$curseur, $newformatdate);
					$objPHPExcel4->getActiveSheet()->setCellValue('D'.$curseur, $v1['libelle']);
					$objPHPExcel4->getActiveSheet()->setCellValue('E'.$curseur, $v1['folio']);
					$curseur++;
				}
			}
			mafunction2($listes2,$k,0,$d,2,$objPHPExcel4,$curseur);
			
		}
		
	}
	else{ $montext='<p>Pas de délibérations.</p>';}		
	
	$objWriter4 = PHPExcel_IOFactory::createWriter($objPHPExcel4, 'Excel2007');
	$objWriter4->save('fichiers/delibthemacomplet.xlsx');
	unset($objPHPExcel4);
	
	require_once('head.php');
	require_once('nav.php');
?>
	<section id="content">
		<a href="fichiers/delibchronocomplet.xlsx" class="boutonlien boutonthema">Tableau année en cours chrono</a>
		<a href="fichiers/delibthemacomplet.xlsx" class="boutonlien boutonthema bouton2">Tableau année en cours théma</a>
		<h2>Fichiers délib pour les 3 dernières années</h2>
		<p>Fichiers générés pour les années <?php echo date('Y')-3; ?>, <?php echo date('Y')-2; ?> et <?php echo date('Y')-1; ?></p><br/>
		<p><a href="fichiers/delibthema.xlsx" >Télécharger le fichier classement thématique</a></p><br/>
		<p><a href="fichiers/delibchrono.xlsx" >Télécharger le fichier classement chronologique</a></p>
	
	</section>
<?php

require_once('footer.php');

?>
<?php 
class Deliberation
{
	private $id_deliberation;
	private $id_reunion;
	private $libelle;
	private $num;
	private $num_delib;
	private $id_axe;
	private $folio;
	private $id_fichier;
	
	public function __construct()
	{
		
	}
	
	public function addDeliberation($id_reunion,$libelle,$num,$num_delib,$id_axe,$folio)
	{
		global $bdd;
		$q = $bdd->prepare("INSERT INTO deliberation (id_reunion,libelle, num, num_delib, id_axe, folio,id_fichier,id_budget,id_emargement) VALUES(:id_reunion,:libelle, :num, :num_delib, :id_axe, :folio,0,0,0)");
		
		$q->bindValue(":id_reunion", $id_reunion);
		$q->bindValue(":libelle", $libelle);
		$q->bindValue(":num", $num);
		$q->bindValue(":num_delib", $num_delib);
		$q->bindValue(":id_axe", $id_axe);
		$q->bindValue(":folio", $folio);
		$q->execute();	
		$dernierid = $bdd->lastInsertId();
		return $dernierid;

	}
	
	public function upDeliberation($id,$id_reunion,$libelle,$num,$num_delib,$id_axe,$folio)
	{
		global $bdd;
		$q = $bdd->prepare("UPDATE deliberation SET id_reunion = :id_reunion, libelle=:libelle, num = :num, num_delib = :num_delib, id_axe = :id_axe, folio = :folio WHERE id_deliberation=:id_deliberation");
		$q->bindValue(":id_deliberation", $id);
		$q->bindValue(":id_reunion", $id_reunion);
		$q->bindValue(":libelle", $libelle);
		$q->bindValue(":num", $num);
		$q->bindValue(":num_delib", $num_delib);
		$q->bindValue(":id_axe", $id_axe);
		$q->bindValue(":folio", $folio);
		$q->execute();
		if($q->rowCount()==1){
			$msg=true;
		}else{
			//print_r($q->errorInfo()); die();
			$msg=false;
		}
		return $msg;
	}
	
	public function upDeliberationFichierDelib($id,$id_fichier)
	{
		global $bdd;
		$q = $bdd->prepare("UPDATE deliberation SET id_fichier=:id_fichier WHERE id_deliberation=:id_deliberation");
		$q->bindValue(":id_deliberation", $id);
		$q->bindValue(":id_fichier", $id_fichier);	
		$q->execute();
		if($q->rowCount()==1){
			$msg=true;
		}else{
			print_r($q->errorInfo()); die();
			$msg=false;
		}
		return $msg;
	}
	
	
	public function upDeliberationFichierBudget($id,$id_fichier)
	{
		global $bdd;
		$q = $bdd->prepare("UPDATE deliberation SET id_budget=:id_fichier WHERE id_deliberation=:id_deliberation");
		$q->bindValue(":id_deliberation", $id);
		$q->bindValue(":id_fichier", $id_fichier);	
		$q->execute();
		if($q->rowCount()==1){
			$msg=true;
		}else{
			print_r($q->errorInfo()); die();
			$msg=false;
		}
		return $msg;
	}
	public function upDeliberationFichierEmargement($id,$id_fichier)
	{
		global $bdd;
		$q = $bdd->prepare("UPDATE deliberation SET id_emargement=:id_fichier WHERE id_deliberation=:id_deliberation");
		$q->bindValue(":id_deliberation", $id);
		$q->bindValue(":id_fichier", $id_fichier);	
		$q->execute();
		if($q->rowCount()==1){
			$msg=true;
		}else{
			print_r($q->errorInfo()); die();
			$msg=false;
		}
		return $msg;
	}
	
	public function getInfosDeliberationByDate($dater)
	{
		global $bdd;
		
		$req = $bdd->prepare('SELECT d.*, r.* FROM deliberation d INNER JOIN reunion r ON r.id_reunion=d.id_reunion WHERE date = :date ORDER BY date');
		$req->bindValue(":date", $dater);
		$req->execute();
		$data=$req->fetchAll();
		return $data;
	}
	public function getInfosDelibById($id)
	{
		global $bdd;
		
		$req = $bdd->prepare('SELECT * FROM deliberation d WHERE id_deliberation = :id');
		$req->bindValue(":id", $id);
		$req->execute();
		$data=$req->fetch();
		return $data;
	}
	public function getDelibByReunion($reunion){
		global $bdd;
		
		$req = $bdd->prepare('SELECT * FROM deliberation d WHERE id_reunion = :reunion ORDER BY libelle');
		$req->bindValue(":reunion", $reunion);
		$req->execute();
		$data=$req->fetchAll();
		return $data;
		
	}
	public function getDelibByAxe($axe){
		global $bdd;
		
		$req = $bdd->prepare('SELECT d.*, r.date, tp.libelle as libeltp,f.nom_reel FROM deliberation d INNER JOIN reunion r ON d.id_reunion=r.id_reunion INNER JOIN type_reunion tp ON r.id_type_reunion=tp.id_type_reunion LEFT JOIN fichier f on d.id_fichier=f.id_fichier WHERE id_axe = :axe ORDER BY d.num ASC, r.date ASC, d.libelle ASC');
		$req->bindValue(":axe", $axe);
		$req->execute();
		$data=$req->fetchAll();
		return $data;
		
	}
	public function getDelibByAxe2($axe){
		global $bdd;
		
		$req = $bdd->prepare('SELECT d.*, r.date, tp.libelle as libeltp,f.nom_reel FROM deliberation d INNER JOIN reunion r ON d.id_reunion=r.id_reunion INNER JOIN type_reunion tp ON r.id_type_reunion=tp.id_type_reunion LEFT JOIN fichier f on d.id_fichier=f.id_fichier WHERE id_axe = :axe AND YEAR(r.date) IN(:year1,:year2,:year3)ORDER BY d.num ASC, r.date ASC, d.libelle ASC');
		$req->bindValue(":axe", $axe);
		$req->bindValue(":year1", date('Y')-3);
		$req->bindValue(":year2", date('Y')-2);
		$req->bindValue(":year3", date('Y')-1);
		$req->execute();
		$data=$req->fetchAll();
		return $data;
		
	}
	/* cherche les délib en fonction de l'axe et du type reunion et des annees*/
	public function getDelibByAxe3($axe,$id_type_reunion,$a1,$a2,$a3){
		global $bdd;
		
		$req = $bdd->prepare('SELECT d.*, r.date, tp.libelle as libeltp,f.nom_reel FROM deliberation d INNER JOIN reunion r ON d.id_reunion=r.id_reunion INNER JOIN type_reunion tp ON r.id_type_reunion=tp.id_type_reunion LEFT JOIN fichier f on d.id_fichier=f.id_fichier WHERE id_axe = :axe AND YEAR(r.date) IN(:year1,:year2,:year3) AND tp.id_type_reunion=:id_type_reunion ORDER BY d.num ASC, r.date ASC, d.libelle ASC');
		$req->bindValue(":axe", $axe);
		$req->bindValue(":id_type_reunion", $id_type_reunion);
		$req->bindValue(":year1", $a1);
		$req->bindValue(":year2", $a2);
		$req->bindValue(":year3", $a3);
		$req->execute();
		$data=$req->fetchAll();
		return $data;
		
	}
	/* cherche les délib en fonction de l'axe et du type reunion*/
	public function getDelibByAxe4($axe,$id_type_reunion){
		global $bdd;
		
		$req = $bdd->prepare('SELECT d.*, r.date, tp.libelle as libeltp,f.nom_reel FROM deliberation d INNER JOIN reunion r ON d.id_reunion=r.id_reunion INNER JOIN type_reunion tp ON r.id_type_reunion=tp.id_type_reunion LEFT JOIN fichier f on d.id_fichier=f.id_fichier WHERE id_axe = :axe AND tp.id_type_reunion=:id_type_reunion ORDER BY d.num ASC, r.date ASC, d.libelle ASC');
		$req->bindValue(":axe", $axe);
		$req->bindValue(":id_type_reunion", $id_type_reunion);
		$req->execute();
		$data=$req->fetchAll();
		return $data;
		
	}
	public function getDelibChrono($year){
		global $bdd;
		
		$req = $bdd->prepare('SELECT d.*, r.date, tp.libelle as libeltp,f.nom_reel FROM deliberation d INNER JOIN reunion r ON d.id_reunion=r.id_reunion INNER JOIN type_reunion tp ON r.id_type_reunion=tp.id_type_reunion LEFT JOIN fichier f on d.id_fichier=f.id_fichier WHERE YEAR(r.date) = :year ORDER BY r.date ASC, d.libelle ASC');
		$req->bindValue(":year", $year);
		$req->execute();
		$data=$req->fetchAll();
		return $data;
		
	}
	public function getDelibChrono2($year,$objet){
		global $bdd;
		
		$req = $bdd->prepare('SELECT d.*, r.date, tp.libelle as libeltp,f.nom_reel FROM deliberation d INNER JOIN reunion r ON d.id_reunion=r.id_reunion INNER JOIN type_reunion tp ON r.id_type_reunion=tp.id_type_reunion LEFT JOIN fichier f on d.id_fichier=f.id_fichier WHERE YEAR(r.date) = :year AND tp.id_type_reunion=:objet ORDER BY r.date ASC, d.libelle ASC');
		$req->bindValue(":year", $year);
		$req->bindValue(":objet", $objet);
		$req->execute();
		$data=$req->fetchAll();
		return $data;
		
	}
	
	public function getDelibChrono3($year,$objet){
		global $bdd;
		
		$req = $bdd->prepare('SELECT d.*, r.date, tp.libelle as libeltp,f.nom_reel FROM deliberation d INNER JOIN reunion r ON d.id_reunion=r.id_reunion INNER JOIN type_reunion tp ON r.id_type_reunion=tp.id_type_reunion LEFT JOIN fichier f on d.id_fichier=f.id_fichier WHERE YEAR(r.date) = :year AND tp.id_type_reunion=:objet AND id_charte = (SELECT id_charte FROM charte WHERE defaut="1") ORDER BY r.date ASC, d.libelle ASC');
		$req->bindValue(":year", $year);
		$req->bindValue(":objet", $objet);
		$req->execute();
		$data=$req->fetchAll();
		return $data;
		
	}
	public function getDelibChronoAnnee($objet){
		global $bdd;
		
		$req = $bdd->prepare('SELECT DISTINCT(YEAR(r.date)) as annee FROM deliberation d INNER JOIN reunion r ON d.id_reunion=r.id_reunion INNER JOIN type_reunion tp ON r.id_type_reunion=tp.id_type_reunion LEFT JOIN fichier f on d.id_fichier=f.id_fichier WHERE tp.id_type_reunion=:objet AND id_charte = (SELECT id_charte FROM charte WHERE defaut="1") ORDER BY r.date ASC, d.libelle ASC');
		$req->bindValue(":objet", $objet);
		$req->execute();
		$data=$req->fetchAll();
		return $data;
		
	}
}	

class Fichier{
	private $id_fichier;
	private $nom_affichage;
	private $nom_reel;
	
	public function __construct()
	{
		
	}
	
	public function addFichier($nom_affichage,$nom_reel)
	{
		global $bdd;
		$q = $bdd->prepare("INSERT INTO fichier (nom_affichage, nom_reel) VALUES(:nom_affichage, :nom_reel)");
		$q->bindValue(":nom_affichage", $nom_affichage);
		$q->bindValue(":nom_reel", $nom_reel);
		$q->execute();	
		$dernierid = $bdd->lastInsertId();
		return $dernierid;

	}
	
	public function upFichier($id,$nom_affichage,$nom_reel)
	{
		global $bdd;
		$q = $bdd->prepare("UPDATE fichier SET nom_affichage = :nom_affichage, nom_reel = :nom_reel WHERE id_fichier=:id_fichier");
		$q->bindValue(":id_fichier", $id);
		$q->bindValue(":nom_affichage", $nom_affichage);
		$q->bindValue(":nom_reel", $nom_reel);
		$q->execute();
		if($q->rowCount()==1){
			$msg=true;
		}else{
			print_r($q->errorInfo()); die();
			$msg=false;
		}
		return $msg;
	}
	public function delFichierById($id)
	{
		global $bdd;
		$q = $bdd->prepare("DELETE FROM fichier WHERE id_fichier=:id_fichier");
		$q->bindValue(":id_fichier", $id);
		$q->execute();
		if($q->rowCount()==1){
			$msg=true;
		}else{
			print_r($q->errorInfo()); die();
			$msg=false;
		}
		return $msg;
	}
	
	
	public function getFichierById($id)
	{
		global $bdd;
		
		$req = $bdd->prepare('SELECT * FROM fichier WHERE id_fichier = :id');
		$req->bindValue(":id", $id);
		$req->execute();
		$data=$req->fetch();
		return $data;
	}
	
}	
?>
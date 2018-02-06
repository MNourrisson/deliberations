<?php 
class Reunion
{
	private $id_reunion;
	private $id_type_reunion;
	private $date;
	
	public function __construct()
	{
		
	}
	
	public function getReunions()
	{
		global $bdd;
		
		$req = $bdd->prepare("SELECT * FROM reunion");
		$req->execute();
		$data=$req->fetchAll();
	
		return $data;
	}
	public function addReunion($id_type,$date)
	{
		global $bdd;
		$q = $bdd->prepare("INSERT INTO reunion (id_type_reunion, date, id_charte) VALUES(:id_type,:date,(SELECT id_charte FROM charte WHERE defaut='1'))");
		$q->bindValue(":id_type", $id_type);
		$q->bindValue(":date", $date);
		$q->execute();	
		$dernierid = $bdd->lastInsertId();
		return $dernierid;

	}
	
	public function upReunion($id,$id_type,$date)
	{
		global $bdd;
		$q = $bdd->prepare("UPDATE reunion SET id_type_reunion = :id_type, date = :date WHERE id_reunion=:id");
		$q->bindValue(":id", $id);
		$q->bindValue(":id_type", $id_type);
		$q->bindValue(":date", $date);
		try{
			$q->execute();
			$msg=true;
		}catch(Exception $e){
			$msg=false;
		}
		return $msg;
	}
	public function getInfosReunionByDate()
	{
		global $bdd;
		
		$req = $bdd->prepare('SELECT r.*, tr.* FROM reunion r INNER JOIN type_reunion tr ON tr.id_type_reunion=r.id_type_reunion  WHERE id_charte = (SELECT id_charte FROM charte WHERE defaut="1") ORDER BY date DESC ');
		// $req = $bdd->prepare('SELECT r.id_reunion, r.id_type_reunion, r.date, tr.libelle FROM reunion r INNER JOIN type_reunion tr ON tr.id_type_reunion=r.id_type_reunion WHERE id_reunion IN (SELECT id_reunion FROM deliberation WHERE id_axe IN (SELECT id_axe FROM axe, charte WHERE charte.defaut="1" and charte.id_charte=axe.id_charte)) ORDER BY date DESC ');
		$req->execute();
		$data=$req->fetchAll();
		return $data;
	}
	public function getInfosReunionById($id_reunion)
	{
		global $bdd;
		
		$req = $bdd->prepare('SELECT r.id_reunion, r.id_type_reunion, r.date, tr.libelle FROM reunion r INNER JOIN type_reunion tr ON tr.id_type_reunion=r.id_type_reunion WHERE id_reunion=:id_reunion ');
		$req->bindValue(":id_reunion", $id_reunion);
		$req->execute();
		$data=$req->fetch();
		return $data;
	}
	public function getIdReunionByDate($date,$id_reunion)
	{
		global $bdd;
		
		$req = $bdd->prepare('SELECT r.*, tr.libelle FROM reunion r INNER JOIN type_reunion tr on r.id_type_reunion = tr.id_type_reunion WHERE date=:date and id_reunion=:id_reunion');
		$req->bindValue(":date", $date);
		$req->bindValue(":id_reunion", $id_reunion);
		$req->execute();
		$data=$req->fetch();
		return $data;
	}
	public function getIdReunionByDate2($date,$idtr)
	{
		global $bdd;
		
		$req = $bdd->prepare('SELECT r.*, tr.libelle FROM reunion r INNER JOIN type_reunion tr on r.id_type_reunion = tr.id_type_reunion WHERE date=:date and r.id_type_reunion = :id');
		$req->bindValue(":date", $date);
		$req->bindValue(":id", $idtr);
		$req->execute();
		$data=$req->fetch();
		return $data;
	}
}		

class TypeReunion
{
	private $id_type_reunion;
	private $libelle;
	private $parent;
	
	public function __construct()
	{
		
	}
	
	public function getTypesReunion()
	{
		global $bdd;
		
		$req = $bdd->prepare("SELECT * FROM type_reunion");
		$req->execute();
		$data=$req->fetchAll();
	
		return $data;
	}
	public function getDescendances($parent)
	{
		global $bdd;
		
		$req = $bdd->prepare("SELECT * FROM type_reunion WHERE parent=:parent");
		$req->bindValue(":parent", $parent);
		$req->execute();
		$data=$req->fetchAll();
		
		return $data;
	}
	public function getInfoTypeReunion($id)
	{
		global $bdd;
		
		$req = $bdd->prepare("SELECT * FROM type_reunion WHERE id_type_reunion=:id");
		$req->bindValue(":id", $id);
		$req->execute();
		$data=$req->fetch();
		
		return $data;
	}
	
	public function addTypeReunion($libelle,$parent)
	{
		global $bdd;
		$q = $bdd->prepare("INSERT INTO type_reunion (libelle,parent) VALUES(:libelle,:parent)");
		$q->bindValue(":libelle", $libelle);
		$q->bindValue(":parent", $parent);
		$q->execute();	
		$dernierid = $bdd->lastInsertId();
		return $dernierid;

	}
	
	public function upTypeReunion($id,$libelle,$parent)
	{
		global $bdd;
		$q = $bdd->prepare("UPDATE type_reunion SET libelle = :libelle, parent=:parent WHERE id_type_reunion=:id");
		$q->bindValue(":id", $id);
		$q->bindValue(":libelle", $libelle);
		$q->bindValue(":parent", $parent);
		try{
			$q->execute();
			$msg=true;
		}catch(Exception $e){
			$msg=false;
		}
		return $msg;
	}
	public function getTR(){
		global $bdd;
		
		$req = $bdd->prepare('SELECT distinct(tp.id_type_reunion), tp.libelle as libeltp FROM deliberation d INNER JOIN reunion r ON d.id_reunion=r.id_reunion INNER JOIN type_reunion tp ON r.id_type_reunion=tp.id_type_reunion LEFT JOIN fichier f on d.id_fichier=f.id_fichier ORDER BY id_type_reunion ASC');
		$req->execute();
		$data=$req->fetchAll();
		return $data;
		
	}
	
}		
?>
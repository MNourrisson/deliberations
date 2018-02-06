<?php 

class Charte
{
	private $id_charte;
	private $libelle;
	private $defaut;
	
	public function __construct()
	{
		
	}
	
	public function getChartes()
	{
		global $bdd;
		
		$req = $bdd->prepare("SELECT * FROM charte");
		$req->execute();
		$data=$req->fetchAll();
	
		return $data;
	}
	
	public function getCharteById($id)
	{
		global $bdd;
		
		$req = $bdd->prepare("SELECT * FROM charte WHERE id_charte=:id");
		$req->bindValue(":id", $id);
		$req->execute();
		$data=$req->fetch();
	
		return $data;
	}
	
	public function addCharte($libelle,$defaut)
	{
		global $bdd;
		$q = $bdd->prepare("INSERT INTO charte (libelle,defaut) VALUES(:libelle,:defaut)");
		$q->bindValue(":libelle", $libelle);
		$q->bindValue(":defaut", $defaut);
		$q->execute();	
		$dernierid = $bdd->lastInsertId();
		return $dernierid;

	}
	
	public function upCharte($id,$libelle,$defaut)
	{
		global $bdd;
		$q = $bdd->prepare("UPDATE charte SET libelle = :libelle, defaut =:defaut WHERE id_charte=:id");
		$q->bindValue(":id", $id);
		$q->bindValue(":libelle", $libelle);
		$q->bindValue(":defaut", $defaut);
		try{
			$q->execute();
			$msg=true;
		}catch(Exception $e){
			$msg=false;
		}
		return $msg;
	}
	public function upCharteDefaut()
	{
		global $bdd;
		$q = $bdd->prepare("UPDATE charte SET defaut =:defaut WHERE 1");
		$q->bindValue(":defaut", '0');
		try{
			$q->execute();
			$msg=true;
		}catch(Exception $e){
			$msg=false;
		}
		return $msg;
	}
	
}		

class Axe
{
	private $id_axe;
	private $libelle;
	private $id_charte;
	private $parent;
	private $niveau;
	
	public function __construct()
	{
		
	}
	
	public function getAxes()
	{
		global $bdd;
		
		$req = $bdd->prepare("SELECT a.* FROM axe a ,charte c WHERE defaut = 1 ORDER BY id_axe, parent");
		$req->execute();
		$data=$req->fetchAll();
	
		return $data;
	}
	
	public function getAxeById($id)
	{
		global $bdd;
		
		$req = $bdd->prepare("SELECT * FROM axe WHERE id_axe=:id");
		$req->bindValue(":id", $id);
		$req->execute();
		$data=$req->fetch();
	
		return $data;
	}
	
	public function addAxe($libelle,$id_charte,$parent,$niveau)
	{
		global $bdd;
		$q = $bdd->prepare("INSERT INTO axe (libelle,id_charte,parent,niveau) VALUES(:libelle,:charte,:parent,:niveau)");
		$q->bindValue(":libelle", $libelle);
		$q->bindValue(":charte", $id_charte);
		$q->bindValue(":parent", $parent);
		$q->bindValue(":niveau", $niveau);
		$q->execute();	
		$dernierid = $bdd->lastInsertId();
		return $dernierid;

	}
	
	public function upAxe($id,$libelle,$id_charte,$parent,$niveau)
	{
		global $bdd;
		$q = $bdd->prepare("UPDATE axe SET libelle = :libelle, id_charte=:id_charte, parent=:parent, niveau=:niveau WHERE id_axe=:id");
		$q->bindValue(":id", $id);
		$q->bindValue(":libelle", $libelle);
		$q->bindValue(":id_charte", $id_charte);
		$q->bindValue(":parent", $parent);
		$q->bindValue(":niveau", $niveau);
		try{
			$q->execute();
			$msg=true;
		}catch(Exception $e){
			$msg=false;
		}
		return $msg;
	}
	
	public function getDescendances($parent)
	{
		global $bdd;
		
		$req = $bdd->prepare("SELECT * FROM axe WHERE parent=:parent AND id_axe IN(SELECT id_axe FROM axe, charte WHERE charte.defaut='1' and charte.id_charte=axe.id_charte)");
		$req->bindValue(":parent", $parent);
		$req->execute();
		$data=$req->fetchAll();
		
		return $data;
	}
	
}		
?>
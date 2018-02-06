<?php 

class Utilisateur
{
	private $id_utilisateur;
	private $email;
	private $mdp;
	
	public function __construct()
	{
		
	}
	public function identification($mail)
	{
		global $bdd;
		$query=$bdd->prepare('SELECT id_utilisateur, mdp, email FROM utilisateur WHERE email = :mail');
		$query->bindValue(':mail',$mail, PDO::PARAM_STR);
		$query->execute();
		$data=$query->fetchAll();
		return $data;
	}
	public function getVerif($id)
	{
		global $bdd;
		
		$req = $bdd->prepare("SELECT email FROM utilisateur WHERE id_utilisateur =:id");
		$req->bindValue(":id", $id);
		$req->execute();
		$data=$req->fetch();
	
		return $data;
	}
	
	public function addPersonne($email,$pass)
	{
		global $bdd;
		$q = $bdd->prepare("INSERT INTO utilisateur (email,mdp) VALUES(:email,:pass)");
		$q->bindValue(":email", $email);
		$q->bindValue(":pass", $pass);
		$q->execute();	
		$dernierid = $bdd->lastInsertId();
		return $dernierid;

	}
	
}		
?>
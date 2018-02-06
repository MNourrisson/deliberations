<?php
	try
	{
		$bdd = new PDO('mysql:host=your-host;dbname=your-dbname', 'your-name', 'your-password');
		$bdd->exec("SET CHARACTER SET utf8");
	}
	 
	catch(Exception $e)
	{
		echo 'Erreur : '.$e->getMessage().'<br />';
		echo 'N° : '.$e->getCode();
	}

	
	
?>

<nav id="nav">
	<ul>
<?php
if((isset($_SESSION['email']) && $_SESSION['droit']==1)||(isset($_COOKIE['email']) && $_COOKIE['droit']==1))
{
?>
		<li <?php if($current=='accueil' ||$current=='recherchedeliberation' || $current=='ajoutdeliberation' || $current=='modifdeliberation'){ echo 'class="current"';} ?>><a href="recherchedeliberation.php"><span>Délibérations</span></a>
			<ul>
				<li <?php if($current=='ajoutdeliberation') echo 'class="current"';?>><a href="ajoutdeliberation.php">Ajouter</a></li>
			</ul>
		</li>
		<li <?php if($current=='listereunion' || $current=='ajoutreunion' || $current=='listetype' || $current=='ajouttype'|| $current=='modiftype'){ echo 'class="current"';} ?>><a href="listereunion.php"><span>Comité/Bureau</span></a>
			<ul>
				<li <?php if($current=='ajoutreunion') echo 'class="current"';?>><a href="ajoutreunion.php">Ajouter</a></li>
				<li <?php if($current=='listetype') echo 'class="current"';?>><a href="listetype.php">Type réunion</a></li>
			</ul>
		</li>
		<li <?php if($current=='listecharte' || $current=='ajoutcharte' || $current=='modifcharte'){ echo 'class="current"';} ?>><a href="listecharte.php"><span>Charte</span></a></li>
		<li <?php if($current=='listeaxe' || $current=='ajoutaxe' || $current=='modifaxe'){ echo 'class="current"';} ?>><a href="listeaxe.php"><span>Axe</span></a></li>
		
		<li <?php if($current=='fichiersexcel'){ echo 'class="current"';} ?>><a href="excel2.php"><span>Téléchargement</span></a></li>
<?php
}
else
{ ?>
		<li <?php if($current=='chrono' || $current=='thema' || $current=='delib' || $current=='recherchedeliberation'){ echo 'class="current"';} ?>><a href="index.php"><span>Voir les délibérations</span></a>
		</li>	
<?php	
}
?>	
	</ul>
</nav>
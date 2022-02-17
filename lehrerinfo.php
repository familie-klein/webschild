<?php 
require_once ('includes/header.inc.php'); 

//Daten aus der Mysql-db holen
	try 
	{
		$result = $database->query("SELECT * FROM k_lehrer WHERE ID='" .$_GET['id']. "'");
	}
	catch (DatabaseException $e) 
	{
		echo ("Keine Datenbankabfrage möglich.");
	}

$obj =  $database->fetchObject ($result);


//schulfunktion und klassne/ stufenleitung zusammenfassen
if ($obj->SchulFunktion == 'Schulleit')
{
	$schul_funktion = 'Schulleitung';
}
elseif ($obj->SchulFunktion == 'Vertret')
{
	$schul_funktion = 'stellv. Schulleitung';
}
//todo ... klassenlehrertätigkeit, stufenkoordinator


echo 
('
	<div class="container theme-showcase" role="main">
		<div class="jumbotron">
			<p />
   		<div class="row">
    	      <div class="col-sm-3">  	
		  	<h3>'. $obj->Vorname.' '.  $obj->Nachname.'</h3>
			' .$obj->Faecher.'
			<p>' . $schul_funktion .'
			<img src="" alt=""> 
		
		
		  </div>
		   <div class="col-sm-3">
			<br>' . $obj->Strasse . '
			<br>' . $obj->PLZ . ' '. plz_to_ort($database,$obj->PLZ) .'
			<br>
			<br> Tel.: ' . $obj->Tel . '
			<br>' . $obj->Handy . ' 
			<br>		   
		   
		    ');
		   
			
/*			
[ToDo]: Datenschutzrechtlich bedenklich ... 	#
[ToDo]: Foto einfügen ... 		
*/
echo ('			<br><a href="mailto:'.$obj->EMailDienstlich.'">
					<button type="button" class="btn btn-info">
					<span class="glyphicon glyphicon-envelope" title="mail schreiben">
					</span>
					</button>
					</a>	
'. $obj->EMailDienstlich .'
			
		  </div>
		</div>
	</div>
	</div>	
');
	
//todo: fotos reinladen ... 
require_once ('includes/footer.inc.php');

?>

<?php include('includes/header.inc.php'); 

//phpsuchabfrage programmierne ... 

if (strlen($_GET['q'])>2)
{
	//etwaige vorausgewählte SuSgruppen "entladen:"
	$_SESSION['schuelergruppe'] = '';
	$_SESSION['liste']	='';
	
	$searchsql = trim ($_GET['q']);

//	TODO: Sql abfrage der Umlautbehafteten namen (Müller) klappt nicht ... 

	//Daten aus der Mysql-db holen
	//Abfrage der Schülertabelle
	try 
	{
		$result = $database->query("SELECT Name, Vorname, ID, Klasse FROM schueler WHERE Status LIKE '2' AND Geloescht LIKE '-' AND ( Name LIKE '".$searchsql."%' OR Vorname LIKE '".$searchsql."%' )");
	}
	catch (DatabaseException $e) 
	{
		echo ("Keine Datenbankabfrage möglich.");
	}

	while ($obj = $database->fetchObject ($result) ) 
	{	
		
		$schuelerliste[$obj->ID][Name] = $obj->Name;
		$schuelerliste[$obj->ID][Vorname] = $obj->Vorname;
		$schuelerliste[$obj->ID][Klasse] = $obj->Klasse;
		$schuelerliste[$obj->ID][ID] = $obj->ID;
	}	
	asort($schuelerliste);

	//Abfrage der lehrertabelle
	try 
	{
		$result = $database->query("SELECT Nachname, Vorname, ID FROM k_lehrer WHERE sichtbar='+'  AND ( Nachname LIKE '".$searchsql."%' OR Vorname LIKE '".$searchsql."%' )  ");
	}
	catch (DatabaseException $e) 
	{
		echo ("Keine Datenbankabfrage möglich.");
	}

	while ($obj = $database->fetchObject ($result) ) 
	{	
		
		$lehrerliste[$obj->ID][Name] = $obj->Nachname;
		$lehrerliste[$obj->ID][Vorname] = $obj->Vorname;
		$lehrerliste[$obj->ID][ID] = $obj->ID;
	}	
	asort($lehrerliste);

	//Abfrage der Eltern
//"SELECT ID, Schueler_ID, Name1, Vorname1, Name2, Vorname2 FROM schuelererzadr WHERE Name1 LIKE '".$sql_search."' OR Vorname1 LIKE '".$sql_search."' OR Name2 LIKE '".$sql_search."' OR Vorname2 LIKE '".$sql_search."' ORDER BY ".$sortierung."", $DatabasePointer);	
	try 
	{
		$result = $database->query("SELECT ID, Schueler_ID, Name1, Vorname1, Name2, Vorname2 FROM schuelererzadr WHERE Name1 LIKE '".$searchsql."%' OR Vorname1 LIKE '".$searchsql."%' OR Name2 LIKE '".$searchsql."%' OR Vorname2 LIKE '".$searchsql."%' ");
	}
	catch (DatabaseException $e) 
	{
		echo ("Keine Datenbankabfrage möglich.");
	}

	while ($obj = $database->fetchObject ($result) ) 
	{	
		$elternliste[$obj->Schueler_ID][ID] = $obj->Schueler_ID;
		$elternliste[$obj->Schueler_ID][Name1] = $obj->Name1;
		$elternliste[$obj->Schueler_ID][Vorname1] = $obj->Vorname1;
		$elternliste[$obj->Schueler_ID][Name2] = $obj->Name2;
		$elternliste[$obj->Schueler_ID][Vorname2] = $obj->Vorname2;
	}	
	rsort($elternliste);

}
elseif (strlen($_GET['q'])>0)
{

	echo '	
		<div class="container theme-showcase" role="main">
			<h2>Bitte geben Sie mindestens 3 Zeichen als Suchbegriff ein.<h2>
		</div>
	';
	die;

}



?> 





<div class="container theme-showcase" role="main">

<?php
if (!$_GET)
{
	echo '
	<div class="row">
		<div class="col-sm-8">
		Bitte beachten Sie die Datenschutzbestimmungen gemäß BASS, BDSG und DS-GVO!
		
		</div>
	</div>	
	
	';
}

?>



<div class="row">
	<div class="col-sm-4">  	
		<div class="list-group">
			<?php
			//array auslesen und in liste darstellen
			if ($schuelerliste) 
			{
				echo '<a class="list-group-item active">Schüler</a>';
			}
			foreach($schuelerliste as $schueler)
			{
				echo '<a href="leistungsuebersichteinzel.php?id='.$schueler[ID]  .' " class="list-group-item"> '. $schueler[Name] .', '. $schueler[Vorname] .'</a>';
			}

			?>
		</div>
	</div>
	
	<div class="col-sm-4">  	
		<div class="list-group">
			
			<?php
			//array auslesen und in liste darstellen
			if ($lehrerliste)
			{
				echo '<a class="list-group-item active">Lehrer</a>';
			}			
			foreach($lehrerliste as $lehrer)
			{
				echo '<a href="lehrerinfo.php?id='.$lehrer[ID]  .' " class="list-group-item"> '. $lehrer[Name] .', '. $lehrer[Vorname] .'</a>';
			}

			?>
		</div>
	</div>

	<div class="col-sm-4">  	
		<div class="list-group">
			<?php
			//array auslesen und in liste darstellen
			if ($elternliste)			
			{
				echo '<a class="list-group-item active">Erziehungsberechtigte (auch Ehemalige)</a>';
			}
			foreach($elternliste as $eltern)
			{
				echo '<a href="schuelerinfo.php?id='.$eltern[ID]  .' " class="list-group-item"> '. $eltern[Name1] .', '. $eltern[Vorname1] ;
				if ($eltern[Name2]) 
				{
					echo '  |  '. $eltern[Name2] .', '. $eltern[Vorname2] ;
				}
				echo '</a>';
			}

			?>
		</div>
	</div>


</div>



</div>
	

<?php include('includes/footer.inc.php');?>

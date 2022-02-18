<?php
	//hier befinden sich die einmaligen Abfragen beim start der Session
	
	//$_SESSION['sid'] liefert die session id, falls benötigt
	$_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];	

	//************************************ Abfrage der Schulbezeichnung **********************************************************
	
	try 
	{
		$result = $database->query("SELECT Bezeichnung1, Bezeichnung2, SchulNr, Schuljahr, SchuljahrAbschnitt FROM EigeneSchule ");
	}
	catch (DatabaseException $e) 
	{
		echo ("Keine Datenbankabfrage möglich.");
    }


	while ($obj = $database->fetchObject($result)) 
	{	
		$_SESSION['schulbezeichnung'] = $obj->Bezeichnung1;
		$_SESSION['schulbezeichnung2'] = $obj->Bezeichnung2;
		$_SESSION['schul_nr'] = $obj->SchulNr;
		$_SESSION['schuljahr'] = $obj->Schuljahr;
		$_SESSION['abschnitt'] = $obj->SchuljahrAbschnitt;
	}


	//************************************ Array mit Fach_ID und Fachbezeichnung erstellen ***************************************
	try 
	{
		$result = $database->query("SELECT ID, FachKrz, Sichtbar FROM EigeneSchule_Faecher WHERE Sichtbar='+'");
	}
	catch (DatabaseException $e) 
	{
		echo ("Keine Datenbankabfrage möglich.");
    }
	
	while ($obj = $database->fetchObject($result)) 
	{
		$_SESSION['faecher_zuordnung'][$obj->ID] = $obj->FachKrz;
		$_SESSION['faecher_zuordnung_invers'][$obj->FachKrz] = $obj->ID;
	}

//************************************ Array mit Jahrgangsstufen erstellen *******************************************************

	try 
	{
		$result = $database->query("SELECT InternKrz, ASDJahrgang , Sichtbar FROM EigeneSchule_Jahrgaenge WHERE Sichtbar='+'");
	}
	catch (DatabaseException $e) 
	{
		echo ("Keine Datenbankabfrage möglich.");
    }

	while ($obj = $database->fetchObject($result)) 
	{
		$_SESSION['stufenliste'][$obj->InternKrz]['name'] = $obj->InternKrz;
	}
//++++++++++++++++++++++++++++++++++++ array mit 

//************************************ Array mit Klasseninfos erstellen **********************************************************

	try 
	{
		$result = $database->query("SELECT Klasse, Bezeichnung, Sichtbar, KlassenlehrerKrz, StvKlassenlehrerKrz FROM Versetzung WHERE Sichtbar='+'");
	}
	catch (DatabaseException $e) 
	{
		echo ("Keine Datenbankabfrage möglich.");
    }

	while ($obj = $database->fetchObject($result)) 
	{
		
		$_SESSION['klassenliste'][$obj->Klasse]['name'] = $obj->Klasse;
		$_SESSION['klassenliste'][$obj->Klasse]['lehrer'] = $obj->KlassenlehrerKrz;
		$_SESSION['klassenliste'][$obj->Klasse]['stellv'] = $obj->StvKlassenlehrerKrz;
		//falls die Klassenräume unter Bemerkungen speichert werden ... kann man nach Untis verknüpfen!
		$_SESSION['klassenliste'][$obj->Klasse]['raum'] = $obj->Bezeichnung;
		//todo: klassenraum und ggf schülerzahl.
		 	
		
	}
//+++++++++++++++++++++++++++++++++++ Array mit Zensurenliste erstellen, falls der user ein Lehrerkrz hat ++++++++++++++++++++++
//+++++++++++++++++++++++++++++++++++ Abfrage ob Klassenlehrer oder Stellv.+++++++++++++++++++++++++++++++++++++++++++++++++++++


if($_SESSION['krz'])
{
	
	$_SESSION['zensurenliste'] = zensurenliste_erstellen($database,$_SESSION['krz'],$_SESSION['schuljahr'],$_SESSION['abschnitt']);
	$_SESSION['notenarray'] = get_notenarray($database,$_SESSION['zensurenliste']);

	
	foreach($_SESSION['klassenliste']as $klasse)
	{
		if ($klasse['lehrer'] == $_SESSION['krz'])	
		{
			$_SESSION['klassenlehrer'] = $klasse['name'];		
		}
		elseif ($klasse['stellv'] == $_SESSION['krz'])	
		{
			$_SESSION['stellv_klassenlehrer'] = $klasse['name'];		
		}
	}
	
}





?>

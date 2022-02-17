<?php 
include('includes/header.inc.php'); 

if ($_SESSION['username'] == 'Admin')
			{
				
			

//########################################################
// ist die Noteneingabe in der Schild DB gesperrt? 
// -> die variable $eingabe wird entsprechend gesetzt
try 
	{
		$result = $database->query("SELECT NotenGesperrt FROM eigeneschule ");
	}
	catch (DatabaseException $e) 
	{
		echo ("Keine Datenbankabfrage möglich.");
    }

	$obj = $database->fetchObject($result);
    if ($obj->NotenGesperrt == "-")
    {
        $eingabe = true;
    }
    else
    {
        $eingabe = false;
    }
//########################################################
// POST Variablen auswerten

if ($_POST)
{
	if ($_POST['noteneingabe'])
	{
		//Eingabe zu einem Json zusammenführen
		foreach ($_SESSION['stufenliste'] as $stufe)
		{
			if ($_POST[$stufe['name']])
			{			
				$freigabe .= $stufe['name'] . ',';
			}
			//letztes Komma noch weglöschen
			$freigaben = substr($freigabe, 0, -1);
		}
		$result = $database->query("UPDATE `webschild_einstellungen` SET `noteneingabe`= '" . $freigaben . "' ");	
	}
}



//#######################################################################
// Einstellungen für die Noteneingabe für webschild aus DB holen

$noteneinstellungen = get_noteneinstellungen($database);





echo '
<div class="container theme-showcase" role="main">
	<h3>Einstellungen:</h3>

	<div class="jumbotron">

	<h3>Noteneingabe:</h3>
		';

		
		//########### Noteneingabeeinstellungen #######################
		if ($eingabe)
		{
			echo ('
   	 		<form method="POST" action="'.$_SERVER['PHP_SELF'].'">
				Erlaubt für Jahrgangsstufe:
				<div class="checkbox">'
			);

			foreach ($_SESSION['stufenliste'] as $stufe)
			{
			
				echo '
				<label><input type="checkbox" name="' . $stufe['name'] . '"';
				
				foreach ($noteneinstellungen as $entry)
				{
					if ($entry == $stufe['name'])
					{
						echo ' checked';
					}
				}
				echo '> ' . $stufe['name'] . '</label>
				';
			}
    		echo ('
				</div>
				<input type= "hidden" name="noteneingabe" value="1">
	  	  		<button type="submit" class="btn btn-default">Speichern</button>
	  			</form>'
			);
		}
		else 
		{
		echo ('<p>Noteneingabe ist über SchILD gesperrt.</p>
				Wenden Sie sich an den SchILD-Administrator.');
		}
		//#############################################################
		echo '	
				</div>
			</div>	
			';			
	}
else
	{
		echo '<h1>Sie sind nicht als admin angemeldet!</h1>';
	}

include('includes/footer.inc.php');?>

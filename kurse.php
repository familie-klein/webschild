<?php include('includes/header.inc.php'); ?>

<div class="container theme-showcase" role="main">
	<div class="no-print">
	<div class="row">
		<div class="col-sm-4">  
		<nav aria-label="Page navigation">
			<ul class="pagination">
			<form class="form" role="search" action="kurse.php" method="get" target="_self">
   				<div class="input-group">
					<input type="text" class="form-control" placeholder="Kursbez." name="kurssuche"  tabindex="1">
					<div class="input-group-btn">
	           			<button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search"></i></button>
            		</div>
        		</div>
        	</form>
        	</ul>
        	</div>
			<div class="col-sm-8">  
			<ul class="pagination">
			<?php
				foreach ($_SESSION['stufenliste'] as $jahrgangsstufe){			
					echo '<li class="page-item"><a class="page-link" href="'.$_SERVER['PHP_SELF'].'?jahrgangsstufe=' .$jahrgangsstufe['name'] . '">'. $jahrgangsstufe['name'] .'</a></li>			
							';
				}
				echo '
				<li class="page-item"><a class="page-link" href="'.$_SERVER['PHP_SELF'].'?jahrgangsstufe=uebergreifend">Ag\'s & co</a></li>				
				';								
			?>
			</ul>
			</nav>
			</div>
		</div>

	
	</div>
<div class="row">&nbsp;</div>

	<div class="row">

		<?php	
			if (($_GET["jahrgangsstufe"] or $_GET["kurssuche"]) and (!$_GET['id']))
			{
				echo'	
		    	<div class="col-sm-4">  	
					<div class="list-group">
						<a href="#" class="list-group-item active">';
				
				if ($_GET["jahrgangsstufe"]) 
				{
					if (($_GET["jahrgangsstufe"] == 'uebergreifend')){
						echo "Ag's & co";
						# Variable zum weiterreichen der get Anfrage, falls ein Kurs ausgewählt wird:
						$question= 'jahrgangsstufe='. $_GET["jahrgangsstufe"];

						# mysql Abfrage der Kurse im aktiven Schulhalbjahr
						//Daten aus der Mysql-db holen
						try 
						{
							$result = $database->query("SELECT * FROM Kurse WHERE Jahr LIKE '".$_SESSION['schuljahr']."' AND Abschnitt LIKE '".$_SESSION['abschnitt'] ."'AND Jahrgang_ID IS NULL;");
						}
						catch (DatabaseException $e) 
						{
							echo ("Keine Datenbankabfrage möglich.");
						}
					}
					else{
						echo 'Jahrgangsstufe: '. $_GET["jahrgangsstufe"];

						# Variable zum weiterreichen der get Anfrage, falls ein Kurs ausgewählt wird:
						$question= 'jahrgangsstufe='. $_GET["jahrgangsstufe"];

						# mysql Abfrage der Kurse im aktiven Schulhalbjahr
						//Daten aus der Mysql-db holen
						try 
						{
							$result = $database->query("SELECT * FROM Kurse WHERE Jahr LIKE '".$_SESSION['schuljahr']."' AND Abschnitt LIKE '".$_SESSION['abschnitt'] ."'AND ASDJahrgang LIKE '". $_GET["jahrgangsstufe"] ."';");
						}
						catch (DatabaseException $e) 
						{
							echo ("Keine Datenbankabfrage möglich.");
						}
					}					
				}
				else
				{
					echo 'Kurssuche: "'. $_GET["kurssuche"] . '"';
					#Variable zum Weiterreichen der get Anfrage, falls ein Kurs ausgewählt wird:
					$question= 'kurssuche='. $_GET["kurssuche"];
					# mysql Abfrage der Kurse im aktiven Schulhalbjahr
					//Daten aus der Mysql-db holen
					try 
					{
						$result = $database->query("SELECT * FROM Kurse WHERE Jahr LIKE '".$_SESSION['schuljahr']."' AND Abschnitt LIKE '".$_SESSION['abschnitt'] ."'AND KurzBez LIKE '". $_GET["kurssuche"] ."%';");
					}
					catch (DatabaseException $e) 
					{
						echo ("Keine Datenbankabfrage möglich.");
					}
				}
				echo'	</a>';

				//array zusammenstellen - id als key. 
				while ($obj = $database->fetchObject ($result) ) 
				{	
					$kursliste[$obj->ID][kursbezeichnung] = $obj->KurzBez;
					$kursliste[$obj->ID][jahrgang] = $obj->ASDJahrgang;		
					$kursliste[$obj->ID][lehrer] = $obj->LehrerKrz;		
				}	
				
				asort($kursliste);

				$linklist = array_to_linklist($kursliste,'kurse.php',$question);
				echo $linklist;

		   		echo '</div>
			  	</div>
				';
			}

			if ($_GET["id"])
			{

				//kursbezeichnung holen, falls das skript extern aufgerufen wurde ... 
				try 
					{
						$query = "SELECT * FROM Kurse WHERE ID LIKE '". $_GET["id"]."';";
						$result = $database->query($query);
					}
					catch (DatabaseException $e) 
					{
						echo ("Keine Datenbankabfrage möglich.");
					}
				while ($obj = $database->fetchObject ($result) ) 
				{	
					$kursbezeichnung[kursbezeichnung] = $obj->KurzBez;
					$kursbezeichnung[jahrgang] = $obj->ASDJahrgang;		
					$kursbezeichnung[lehrer] = $obj->LehrerKrz;		
				}	
				
				//rekursivabfrage: Abschnitt_ID in den Leistungsdaten ist die Eindeutige ID in der Tabelle Schülerabschnittsdaten
				try 
					{
						$query = 'SELECT Abschnitt_ID, Kurs_ID, Kursart FROM SchuelerLeistungsdaten WHERE Kurs_ID=' . $_GET["id"];
						$result = $database->query($query);
					}
					catch (DatabaseException $e) 
					{
						echo ("Keine Datenbankabfrage möglich.");
					}
				$anzahl=0;
			 	while ($obj = $database->fetchObject ($result) ) 
				{	
						$kursart = $obj->Kursart;
					try 
					{
						$query = "SELECT ID, Schueler_ID FROM SchuelerLernabschnittsdaten WHERE ID='". $obj->Abschnitt_ID ."'";
						$result2 = $database->query($query);
					}
					catch (DatabaseException $e) 
					{
						echo ("Keine Datenbankabfrage möglich.");
					}
					while ($obj = $database->fetchObject ($result2) ) 
					{	
					//Schülerdaten aus der Mysql-db holen
					try 
						{
							$result3 = $database->query("SELECT * FROM Schueler WHERE ID LIKE '".$obj->Schueler_ID."' AND Status LIKE '2' AND Geloescht LIKE '-' ORDER BY Name");
						}
						catch (DatabaseException $e) 
						{
						echo ("Keine Datenbankabfrage möglich.");
						}
	
						//array zusammenstellen - id als key. 
						while ($obj2 = $database->fetchObject ($result3) ) 
						{								
							$liste[$obj->Schueler_ID][Name] = $obj2->Name;
							$liste[$obj->Schueler_ID][Vorname] = $obj2->Vorname;
							$liste[$obj->Schueler_ID][Telefon] = $obj2->Telefon;
							$liste[$obj->Schueler_ID][Geschlecht] = geschlecht($obj2->Geschlecht);
							$liste[$obj->Schueler_ID][Geburtsdatum] = datums_wandler($obj2->Geburtsdatum);
						}	
						if ( ($kursart == 'GKM') or ($kursart == 'GKS') or ($kursart == 'AB3') or ($kursart == 'AB4')) 
						{
							$liste[$obj->Schueler_ID][Kursart]=$kursart;
						}
					}
				
				$anzahl++;	
				}					
				//sortieren des arrays für das erste Erscheinen der Liste in alphabet. Reichenfolge der Nachnamen 
				$liste = array_sort_german2($liste);

				//speichern der liste in der Session variablen für den download und das Weiterklicken bei der individualansicht
				$_SESSION['liste'] = $liste;	
				//ersten eintrag in der schülerliste: 
				$id_list= array_keys($liste);	

				$getkursid = 'kursid='. $_GET["id"];
				$_SESSION['schuelergruppe'] = $kursbezeichnung[kursbezeichnung].' '.$kursbezeichnung[jahrgang].' '.$kursbezeichnung[lehrer].' (' .$anzahl.')';
			echo'	
	  			<div class="container theme-showcase" role="main">
					<h2>
						Schülerliste: '. $_SESSION['schuelergruppe'] .'
						<div class="no-print">
						<button type="button" class="btn btn-info pull-right" onclick="printThisWindow()" title="drucken"><span class="glyphicon glyphicon-print"></span></button>
							
				
						<a href="export.php?download=schuelerliste&lerngruppe='. $kursbezeichnung[jahrgang].'-'. $kursbezeichnung[kursbezeichnung] .'_'.$kursbezeichnung[lehrer].'" >
						<button type="button" class="btn btn-info pull-right">
			 			<span class="glyphicon glyphicon-download" title="download"></span></button>
						</a>						
						<a href="leistungsuebersichteinzel.php?id='. $id_list[0] .'">
						<button type="button" class="btn btn-info pull-right">
			 			<span class="glyphicon glyphicon glyphicon-th" title="Leistungsübersicht"></span></button>
						</a>
						</div>		
					</h2>
		';	

				$table = array_to_clickable_table($liste,'leistungsuebersichteinzel.php',$getkursid);
				echo $table;
						
   				echo'	
	  			</div>
				';
			}
		?>
	</div>
</div>
	

<?php include('includes/footer.inc.php');?>

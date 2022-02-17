<?php include('includes/header.inc.php'); 

// Foto aus der Mysql holen

	try	
	{
		$FotoPointer = $database->query("SELECT * FROM schuelerfotos WHERE Schueler_ID='".$_GET['id']."'");
		$foto_obj =  $database->fetchObject ($FotoPointer);
	}	
	catch (DatabaseException $e) 
	{
		echo ("Keine Datenbankabfrage möglich.");
	}
		

// Elterndaten aus der mysql-db holen

	


// next und previous in liste ermitteln:
	// array mit allen ids auslesen:
	$liste = array_keys($_SESSION['liste']);
	//pionter auf actuellen Eintrag ausrichten und anzahl der listenelemente finden:
	$pointer = array_search($_GET['id'],$liste);
	//Gesammtanzahl der liste ermitteln
	$anzahl = count ($liste);

	if (($pointer>0) and ($pointer < $anzahl-1) )
	{
		$previous = $liste[($pointer-1)];
		$next = $liste[($pointer+1)];
	}
	elseif ($pointer == '0')
	{
		$previous = $liste[$anzahl-1];
		$next = $liste[($pointer+1)];
	}
	elseif ($pointer == $anzahl-1)	
	{
		$previous = $liste[$pointer-1];
		$next = $liste[0];
	}

	//schüler-Daten aus der Mysql-db holen
	try 
	{
		$result = $database->query("SELECT * FROM schueler WHERE ID='" .$_GET['id']. "'");
		$obj =  $database->fetchObject ($result);
	}
	catch (DatabaseException $e) 
	{
		echo ("Keine Datenbankabfrage möglich.");
	}

	//Variablen aufbereiten:
	$vorname = ($obj->Vorname);
	$nachname = ($obj->Name);
	$geb = datums_wandler($obj->Geburtsdatum);
	$ort = plz_to_ort($database,$obj->PLZ);
	$strasse = ($obj->Strasse);
	if ($obj->ZustimmungFoto == '+')
	{
		$zustimmungFoto = true;	
	} 
	$number_in_list = $pointer+1 . '/' . $anzahl;

	//elterninformationen abfragen: 
	try 
	{
		$result = $database->query("SELECT * FROM schuelererzadr WHERE Schueler_ID LIKE '" .$_GET['id']. "'");
	}
	catch (DatabaseException $e) 
	{
		echo ("Keine Datenbankabfrage möglich.");
	};

	while ($erzobj = $database->fetchObject ($result) ) 
				{	
					$erzber[$erzobj->ID][anrede1] = $erzobj->Anrede1;
					$erzber[$erzobj->ID][titel1] = $erzobj->Titel1;
					$erzber[$erzobj->ID][vorname1] = $erzobj->Vorname1;
					$erzber[$erzobj->ID][name1] = $erzobj->Name1;	
					$erzber[$erzobj->ID][email1] = $erzobj->ErzEMail;
					$erzber[$erzobj->ID][anrede2] = $erzobj->Anrede2;
					$erzber[$erzobj->ID][titel2] = $erzobj->Titel2;
					$erzber[$erzobj->ID][vorname2] = $erzobj->Vorname2;
					$erzber[$erzobj->ID][name2] = $erzobj->Name2;	
					$erzber[$erzobj->ID][email2] = $erzobj->ErzEMail2;
					$erzber[$erzobj->ID][plz] = $erzobj->ErzPLZ;
					$erzber[$erzobj->ID][ort] = plz_to_ort($database,$erzobj->ErzPLZ);
					$erzber[$erzobj->ID][strasse] = $erzobj->ErzStrasse;
																	
				}	

	$erzids = array_keys($erzber);


	//telefonnummern abfragen: 
	try 
	{
		$result = $database->query("SELECT * FROM schuelertelefone WHERE Schueler_ID LIKE '" .$_GET['id']. "'");
	}
	catch (DatabaseException $e) 
	{
		echo ("Keine Datenbankabfrage möglich.");
	};

	while ($telobj = $database->fetchObject ($result) ) 
				{	
					$telnummer[$telobj->TelefonArt_ID] = $telobj->Telefonnummer;
					//$erzber[$erzobj->ID][ort] = telnummerbez($database,$teobj->ErzPLZ);
				}	

	$telnummerids = array_keys($telnummer);


	//schulabschnittsdaten abfragen:
	
	try 
	{
		//[TODO]: die Datenbankabfrage liefert nur den letzten Zurück ... doof ... die alte Abfrage läuft auch nicht mit der PDO-classe. 
		//$DatabasePointer = mysql_connect($db_host, $db_user, $db_pass);
		//$ResPointer = mysql_query("SELECT * FROM schuelerlernabschnittsdaten WHERE Schueler_ID='".$schueler_ID."'", $DatabasePointer);
		//$treffer = mysql_num_rows($ResPointer);
	}
	catch (DatabaseException $e) 
	{
		echo ("Keine Datenbankabfrage möglich.");
	};


//html-output zusammenbasteln: 
?>
	<div class="container theme-showcase" role="main">
			<div class="jumbotron">
				
				<?php
				if ($_SESSION['liste'])	{
					echo ('			
					<div class="row"> 
					<div class="col-sm-3">  	
						<a href="'. $_SERVER['PHP_SELF'] .'?id='. $previous .'"><button type="button" class="btn btn-primary""><span class="glyphicon glyphicon-chevron-left"></span></button></a>		
						<a href="'. $_SERVER['PHP_SELF'] .'?id='. $next .'"><button type="button" class="btn btn-primary""><span class="glyphicon glyphicon-chevron-right"></span></button></a>
					</div>
					</div>
					<div class="row"> 
					<div class="col-sm-3"> <h4> 
					'. $_SESSION['schuelergruppe'] .'
						</h4>
						Ldf Nr.: '. $number_in_list .'
					</div>
					</div>
					');
				}
				?>
	
			
			

		
			<div class="row">
				<?php
				echo '
						
						<a href="leistungsuebersichteinzel.php?id='. $_GET['id'] .'">
						<button type="button" class="btn btn-info pull-right">
			 			<span class="glyphicon glyphicon glyphicon-list-alt"></span> leistungsübersicht</button>
						</a>
						
					';
				?>
				<div class="col-sm-6">
					<h3><b> <?= $vorname ?> <?= $nachname ?>, <?= $obj->Klasse ?></b> </h3>	
					<?= $geb ?>   			
					<p>					
					<?php
						if ($zustimmungFoto)
						{
							echo "Zustimmung Fotorechte";
						}
						else {
							echo "- keine Zustimmung Fotorechte -";
						}
					
					?> 
					<p>	
					
										
					
	  			
					<?= $strasse ?><br>
					<?= $obj->PLZ ?> <?= $ort ?><p>
					<?php 
						if ($obj->Telefon) 
						{
							echo 'Telefon: ' . $obj->Telefon . '<br>
							';
						}
						
						
							
							if ($obj->Handy) 
							{
								echo $obj->Handy. '<br>
								';
							}
					 		if ($obj->EMail) 
							{
								echo '								
								<a href="mailto:'.$obj->EMail.'">
								<button type="button" class="btn btn-info">
								<span class="glyphicon glyphicon-envelope" title="mail schreiben">
								</span>
								</button>
								</a>'	.								
								$obj->EMail. '<br>
								';
							}
							if (count($telnummer)>1) 
							{ 
								echo '<div class="well well-sm">';	
								foreach ($telnummerids as $id)  
								{	
									if ($telnummer[$id]!=$obj->Telefon) 
									{	
										echo telnummerid_to_text ($database,$id) . ': ';
										echo $telnummer[$id]. '<br>
										';	
									}				
								}
								echo '</div>';
							}
						
					echo '<p> Erziehungsberechtigte: <br>';
						
							foreach ($erzids as $id)  
							{	
								echo '<div class="well well-sm">';									
								echo '<b>' . $erzber[$id][anrede1] . ' ' . $erzber[$id][titel1] . ' ' .  $erzber[$id][vorname1] . ' ' .  $erzber[$id][name1] . '</b><br>
								' ;
								if ($erzber[$id][name2]) 
									{
									echo '<b>' . $erzber[$id][anrede2] . ' ' . $erzber[$id][titel2] . ' ' .  $erzber[$id][vorname2] . ' ' .  $erzber[$id][name2] . '</b><br>
									';							
									}
								echo $erzber[$id][strasse] .  '<br/>
								';							
								echo $erzber[$id][plz] . ' ' . $erzber[$id][ort] .  '<br/>
								';	
								if ($erzber[$id][email1]) {
									echo 
									'<a href="mailto:'.$erzber[$id][email1].'">
									<button type="button" class="btn btn-info">
									<span class="glyphicon glyphicon-envelope" title="mail schreiben">
									</span>
									</button>
									</a>'.$erzber[$id][email1] .  '<br/>
									';}								
								if ($erzber[$id][email2]) {
									echo 
									'<a href="mailto:'.$erzber[$id][email2].'">
									<button type="button" class="btn btn-info">
									<span class="glyphicon glyphicon-envelope" title="mail schreiben">
									</span>
									</button>
									</a>'.$erzber[$id][email2] .  '<br/>
									';								
								}
								echo '</div>
								';
							}

					?>
					
		  		</div>

				<?php
				//bild einfügen
	
				if ($foto_obj->Foto)
				{	
					echo '<div  class="col-sm-3">';
					echo '<img width=100% src="data:image/jpg;base64,';
					print base64_encode($foto_obj->Foto); 
					echo '" alt="" />';
					echo '</div>';
				}		
			  	?>


				
			</div>
	
		</div>
	</div>	


	


<?php




?>



<?php include('includes/footer.inc.php');?>

<?php include('includes/header.inc.php'); 



if($_GET['klasse'])
{
	$_SESSION['schuelergruppe'] = $_GET['klasse'];
	//*****************************************************************************	
	//SI oder SII?
}

	
	$SII=array('EF','Q1','Q2','11','12','13');
	if (in_array($_GET['klasse'], $SII))
	{
		$secI=false;
	}	
	else
	{
		$secI=true;	
	}	
	
	
	//*****************************************************************************	
	//Fächerliste holen und eine umfassende liste mit Namen und Fächern und Noten erstellen:
	
	try 
	{
		$result = $database->query("SELECT * FROM schueler WHERE Klasse LIKE '". $_GET['klasse'] ."' AND Status LIKE '2' AND Geloescht LIKE '-' ");
	}
	catch (DatabaseException $e) 	
	{
		echo ("Keine Datenbankabfrage möglich.");
	}
	$notenart = array ('KA_1','KA_2','KA_3','Somi_1','Somi_2','Somi_Ges','Klausur_1', 'Klausur_2', 'Schr_Ges', 'Endnote', 'Fehlstd', 'uFehlstd');
	$teilnotenart = array ('KA_1','KA_2','KA_3','Somi_1','Somi_2','Somi_Ges','Klausur_1', 'Klausur_2', 'Schr_Ges');
	//$endnotenart = array ( 'Endnote', 'Fehlstd', 'uFehlstd'); // Damit kann man auch die Fehlstd anzeigen lassen. 
	$endnotenart = array ( 'Endnote', );
		
	//array zusammenstellen - id als key. 
	while ($obj = $database->fetchObject ($result) ) 
	{
		$liste[$obj->ID][Name] = $obj->Name;
		$liste[$obj->ID][Vorname] = $obj->Vorname;	
		$liste[$obj->ID][ID] = $obj->ID;	
		$jahrgangsid = get_lernabschnitt_ID($database,$obj->ID,$_SESSION['schuljahr'],$_SESSION['abschnitt']);
		$liste[$obj->ID]['faecherliste'] = get_faecherliste($database,$jahrgangsid);
		//Erfassung, welche Notenart für welches Fach gibt es überhaupt?
		foreach($liste[$obj->ID]['faecherliste'] as $fach)
		{
			foreach($notenart as $item)			
			{
				if ($fach[$item])
				{
					//echo $item;
					$faecheruebersicht[$fach['Fach_ID']]['Fach_ID'] = $fach['Fach_ID'];
					$faecheruebersicht[$fach['Fach_ID']]['Kursart'] = $fach['Kursart'];
					$faecheruebersicht[$fach['Fach_ID']][$item] = $item;
				}			
			}
		}
		
	}	
	asort($liste);
	//speichern der liste in der Session variablen für den download und das Weiterklicken bei der individualansicht z.B.:leistungsübersichtgruppe
	$_SESSION['liste'] = $liste;

		
		
//echo "<pre>";
//print_r($_SESSION['liste']);
//echo "</pre>";
		
	//***********************************************************************************************
	//falls nichts ausgewählt ist, wähle dmfs, also Deutsch Mathe Fremdsprachen ... 
	
		if( !($_GET['dmfs'] or $_GET['WPII'] or $_GET['ZUV'] or $_GET['others'] or $_GET['einzelfach'] or $_GET['PUTPUK']) )
		{
			$_GET['dmfs'] = 'checked';
			if($secI) 
			{
				$_GET['WPII'] = 'checked';
			}
		}	
	
	
	// auswahl, welche fächer angezeigt werden sollen
	$angezeigte_faecher = array();
	if($_GET['dmfs'])
	{
		array_push($angezeigte_faecher,'D','M','E','L','F','S');
		
	}
	if($_GET['GKLK'])
	{	
		foreach ($faecheruebersicht as $fach)
		{
			
			if($fach['Kursart']== 'ZK' or $fach['Kursart']== 'GK' or $fach['Kursart']== 'LK') 
			{
				array_push($angezeigte_faecher, $_SESSION['faecher_zuordnung'][$fach['Fach_ID']]);
			}		
		}
	}
	
	if($_GET['WPII'])
	{	
		foreach ($faecheruebersicht as $fach)
		{
			
			if($fach['Kursart']== 'WPII') 
			{
				array_push($angezeigte_faecher, $_SESSION['faecher_zuordnung'][$fach['Fach_ID']]);
			}		
		}
	}
	
	if($_GET['ZUV'])
	{	
		foreach ($faecheruebersicht as $fach)
		{
			
			if($fach['Kursart']== 'ZUV') 
			{
				array_push($angezeigte_faecher, $_SESSION['faecher_zuordnung'][$fach['Fach_ID']]);
			}		
		}
	}
	
	if($_GET['PUTPUK'])
	{	
		foreach ($faecheruebersicht as $fach)
		{
			
			if($fach['Kursart']== 'PUT' or $fach['Kursart']== 'PUK') 
			{
				array_push($angezeigte_faecher, $_SESSION['faecher_zuordnung'][$fach['Fach_ID']]);
			}		
		}
	}
	
	
	
	if($_GET['others'])
	{	
		foreach ($faecheruebersicht as $fach)
		{
			
			if(!($fach['Kursart']== 'GK' or $fach['Kursart']== 'ZK' or $fach['Kursart']== 'LK' or $fach['Kursart']== 'ZUV' or $fach['Kursart']== 'PUT' or $fach['Kursart']== 'PUK' or $fach['Kursart']== 'WPI' or $fach['Kursart']== 'WPII')) 
			{
				array_push($angezeigte_faecher, $_SESSION['faecher_zuordnung'][$fach['Fach_ID']]);
			}		
		}
	}
	
	if($_GET['einzelfach'])
	{	
		$_GET['dmfs'] = '';
		$_GET['WPII'] = '';
		$_GET['PUKPUT'] = '';
		$_GET['ZUV'] = '';		
		$_GET['others'] = '';
		$angezeigte_faecher = array($_GET['einzelfach']);
		$_GET['einzelfach'] = '';
		
	}
	//if ($) TODO: Get anweisung ausführen. und ein wenig anpassen	
		
		

	//***********************************************************************************************	
	//tabelle output zusammenbasteln: 
	$table = '
		<style>
		thead th 
		{ 
			position: sticky; 
			top: 0;
			background-color: lightgrey; 
		}
		</style>
		<table class="table table-bordered text-nowrap">	
			<thead>
			';		
		
		//Überschhrift generieren
		$table .='
				<tr>
				<th>Name</th>
				<th>Vorname</th>
				';
		
		foreach($faecheruebersicht as $fach)
		{
		



			if (in_array($_SESSION['faecher_zuordnung'][$fach['Fach_ID']],$angezeigte_faecher))
				{		
				
			
					$table .= '
						<th>
							';
				
					//echo $_SESSION['faecher_zuordnung'][$fach['Fach_ID']];
						$table .= '					
								
									'. $_SESSION['faecher_zuordnung'][$fach[Fach_ID]] .'
								';
								
						$table .= '					
								
								<br />
								';
								$formatting= '';
								foreach($teilnotenart as $eintrag)
								{
																						
									if ($fach[$eintrag])
									{
										//kompakt K1 statt Klausur 1
										$kurzform=$eintrag[0] . $eintrag[strlen($eintrag)-1];
				
										if(!$formatting)
										{
											$table .= '' . $kurzform . '';
											$formatting = '&nbsp;|&nbsp;';
										}
										else{
											$table .= '' . $formatting . $kurzform . '';										
										}
									}
									else{
									//	$table .= '<td>&nbsp;</td>';
									}
										
								}		
								foreach($endnotenart as $eintrag)
								{		
									if($eintrag) 
									{
										if ($eintrag == 'Endnote')
										{
											$printit = 'ZN';										
										}
										elseif($eintrag == 'Fehlstd') 
										{
											$printit = 'FS';
										}		
										elseif($eintrag == 'uFehlstd') 
										{
											$printit = 'uFS';
										}	
										else 
										{
											$printit = $eintrag;
										}									
										$table .= '<b style="color: blue;">' . $formatting . $printit . '</b>';
										$formatting = '&nbsp;|&nbsp;';
									}
								}									
								
									
			$table .='	
								
			</th>';	
			}
		}     
		
		
		
		$table.='
      		</tr>   
      	</thead>
      		<tbody>';
		//tablebody aufbauen: 
		foreach ($liste as $datensatz)
		{		
			$table.='
      		<tr>	
          		<td><a href="leistungsuebersichteinzel.php?id='. $datensatz['ID'].'">'.$datensatz['Name'].'</a></td>
          		<td><a href="leistungsuebersichteinzel.php?id='. $datensatz['ID'].'">'.$datensatz['Vorname'].'</a></td>
          		';
         foreach($faecheruebersicht as $fach)
			{
				//Fächer in der Reihefolge s.o. abrufen
				
				if (in_array($_SESSION['faecher_zuordnung'][$fach['Fach_ID']],$angezeigte_faecher))
				{	
					$table .= '
									<td>';
					
					
					foreach($datensatz['faecherliste'] as $zensuren)
					{	
						if($zensuren['Fach_ID'] == $fach['Fach_ID']) 
						{
							$formatting='';
							//teilnoteneinträge abbilden...
							foreach($teilnotenart as $eintrag)
							{
																						
								if ($zensuren[$eintrag])
								{
									if(!$formatting)
									{
										$table .= '' . $zensuren[$eintrag] . '';
										$formatting = '&nbsp;|&nbsp;';
									}
									else{
										$table .= '' . $formatting . $zensuren[$eintrag] . '';										
									}
								}
								else{
									//$table .= '<td>&nbsp;</td>';
								}
										
							}	
							foreach($endnotenart as $eintrag)
							{		
								if($zensuren[$eintrag]) 
								{
									$table .= '<b style="color: blue;">' . $formatting . $zensuren[$eintrag] . '</b>';
									$formatting = '&nbsp;|&nbsp;';
								}
							}	
						}	
						
					}
					$table .= '
									</td>';
				
				}
						
         } 		
      	$table.='</tr>   	
				';      	
		}      
      
      $table.='
      		</tbody>
      	</table>
	';
	
	//*************************************************************************************************************************
	//interaktives auwahlmenue:
	echo '
	
	<div class="container" style="padding-bottom: 20px">

		<div class="row">

			<h2>Kompaktübersicht '. $_GET['klasse'] .' - Stand: ' . date("d.m.y").'			</h2>
		</div>		
		<div class="no-print">
		<div class="row">
			<div class="col-sm-2>
			</div>
			<div class="col-sm-8>  
			<div class="input-group">
 				<form method="get">
 				
 				Auswahl:
 				<select name="klasse">'
 				;
			foreach ($_SESSION['klassenliste'] as $kl) 
			{  
				echo '
					<option value="' . $kl['name'] ;
				if ($kl['name'] == $_GET['klasse']) 
				{
					echo '" selected > ' . $kl['name'] . '</option>    
					';
				}
				else 
				{
				echo '"  > ' . $kl['name'] . '</option>    
				';
				}	
			}
	
		
		echo '
  			</select>
  			<input type="checkbox" name="dmfs" value="checked" '. $_GET['dmfs'].'> D,M,FS, </input> 
  			';
  	if ($secI)
	{		
  		echo'	
  			<input type="checkbox" name="WPII" value="checked" '. $_GET['WPII'].'> WPII, </input> 
  			<input type="checkbox" name="PUTPUK" value="checked" '. $_GET['PUTPUK'].'> Klassenunterricht, </input> 
  			<input type="checkbox" name="ZUV" value="checked" '. $_GET['ZUV'].'> ZUV, </input>
  			<input type="checkbox" name="others" value="checked" '. $_GET['others'].'> LZ, Fö & co </input>';
  	}
  	else 
	{  	
  		echo '
  			<input type="checkbox" name="GKLK" value="checked" '. $_GET['GKLK'].'> GKs & LKs, </input> 
  			<input type="checkbox" name="others" value="checked" '. $_GET['others'].'> PJK, VTF & co</input>
  			';		
	}  					
  		echo 'oder nur das Fach:
			<select name="einzelfach">
				<option value =""';
				if(!$_GET['einzelfach']) 
				{
					echo ' selected> -- </option>
					';
				}
				else
				{
					echo '> </option>
					';
				
				}
  			foreach($faecheruebersicht as $fach)
  			{
				echo '<option value="' . $_SESSION['faecher_zuordnung'][$fach['Fach_ID']] ;	
				if ($_SESSION['faecher_zuordnung'][$fach['Fach_ID']]== $_GET['einzelfach']) 
				{
					echo '" selected > ' . $_SESSION['faecher_zuordnung'][$fach['Fach_ID']] . '</option>    
					';
				}
				else 
				{
					echo '"  > ' . $_SESSION['faecher_zuordnung'][$fach['Fach_ID']]. '</option>    
				';
				}	
  			}
	
	
  	echo ' </select>
  			<input type="submit" value="anzeigen">';
  	echo'
  			
		</form>		
		</div>
		<button type="button" class="btn btn-info pull-right" onclick="printThisWindow()" title="drucken"><span class="glyphicon glyphicon-print"></span></button>
		
		</div>
		</div>
		
		<div class="row">
		&nbsp;
		</div>	
		</div>

';
echo '<div class"container container-fluid">';
$_SESSION['leistungsdaten'] = $table;
if($_GET['klasse'])
{
	echo $table;
}
echo '</div>';



include('includes/footer.inc.php');
?>

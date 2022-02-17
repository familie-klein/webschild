<?php 

include('includes/header.inc.php'); 

//####################################################################################################

// ist die Noteneingabe in der Schild DB gesperrt? -> die variable $eingabe wird entsprechend gesetzt
$eingabe = get_schild_noteneinstellungen($database);

// ist die Noteneingabe einzelner Jahrgangstufen über WebSchild gesperrt?
$noteneinstellungen = get_noteneinstellungen($database);

//link zur eigenen Seite inkl. Get vars
//$url=getPageLink();	
$url = $_SERVER['PHP_SELF'] .'?';
$getArrayKeys = array_keys($_GET);
foreach($getArrayKeys as $key)
{
	if($key!='notenart')
	{
 		$url .= $key. '=' . $_GET[$key] . '&';
	}
}
$url = substr($url, 0, -1);

if(($eingabe == true))
{
	//Session vars löschen für die links in der liste
	unset($_SESSION['liste']);	
	
	//####################################################################################################
	//############ Zensurenliste zusammenstellen bzw. aktualisieren ######################################
	//####################################################################################################
	if ($_SESSION['zensurenliste'])
	{
		$_SESSION['zensurenliste'] = update_zensurenliste($database,$_SESSION['zensurenliste']);
	}
	else
	{
		//zensurenliste erstellen und als Sessionvariable speichern
		$_SESSION['zensurenliste'] = zensurenliste_erstellen($database,$_SESSION['krz'],$_SESSION['schuljahr'],$_SESSION['abschnitt']);
	}


	//####################################################################################################
	//################### Get Variablen verarbeiten und table zusammensetzen #############################
	//####################################################################################################
	
	if ($_GET['kursunterricht'])
	{
		$kursarten_teilnoten_SI = array ('WPI','WPII');
		$kursarten_teilnoten_SII = array ('LK','GK','ZK');
		
		if  (in_array($_SESSION['kursunterrichtliste'][$_GET['id']]['kursart'], $kursarten_teilnoten_SII))
		{
			$notenart = array ( 'Klausur_1', 'Klausur_2', 'Schr_Ges','Somi_1','Somi_2','Somi_Ges', 'Endnote', 'Fehlstd', 'uFehlstd');
		}
		elseif(in_array($_SESSION['kursunterrichtliste'][$_GET['id']]['kursart'], $kursarten_teilnoten_SI))
		{
			$notenart = array ('KA_1','KA_2','KA_3', 'Endnote', 'Fehlstd', 'uFehlstd');
		}		
		else
		{
			$notenart = array ('Endnote', 'Fehlstd', 'uFehlstd');
		}

		$ueberschrift = $_SESSION['kursunterrichtliste'][$_GET['id']]['langbezeichnung'] ;
		$_SESSION['ueberschrift'] = $ueberschrift;
		$getvar_downloadbutton = 'kursunterricht=1&id=' . $_GET['id'];
	}
	elseif($_GET['klassenunterricht'])
	{
		$unterrichtsarten_teilnoten = array ('M','D','E','L','F','S');
		$fachklasse = explode('-', $_GET['id']);
		$_SESSION['ueberschrift'] = $fachklasse[0];

		if  ((in_array($_SESSION['klassenunterrichtliste'][$_GET['id']]['fach'], $unterrichtsarten_teilnoten)) or (in_array($_SESSION['klassenunterrichtliste'][$_GET['id']]['unterrichtsart'], $unterrichtsarten_teilnoten)))
		{
			$notenart = array ('KA_1','KA_2','KA_3', 'Endnote', 'Fehlstd', 'uFehlstd');
		}
		else
		{
			$notenart = array ('Endnote', 'Fehlstd', 'uFehlstd');
		}
		$ueberschrift=$_SESSION['klassenunterrichtliste'][$_GET['id']]['klassenunterricht'] ;
		$getvar_downloadbutton = 'klassenunterricht=1&id=' . $_GET['id'];
	}
	else
	{
		$kommentar = '
				Es liegt ein Fehler vor: Es wurde weder ein Kurs noch eine Klasse ausgewählt.
			';	
	}


	//Auswertung welche Teilnotenart eingetragen werden soll ... erstellen der Eingabefelder
	//sql-Abfrage der schon eingetragenen Teilnoten
	//Notenarray zusammenbasteln: 
	if(!$_SESSION['notenarray'])
	{	
		$_SESSION['notenarray'] = get_notenarray($database,$_SESSION['zensurenliste']);
	}
	else {
		//performanceproblem: hier müsste man vorher die ZensurenIDs setzen und nur diese Herausfiltern und updaten.
		$_SESSION['notenarray'] = get_notenarray($database,$_SESSION['zensurenliste']);		
	}		

//echo "<pre>";
//print_r($_SESSION['zensurenliste']);	
//echo "</pre>";

		
//echo "<pre>";
//print_r($_SESSION['notenarray']);
//echo "</pre>";		
		

		//Tabellen-header
		$table = '
			
			<form id="frm">
		
			<input type="hidden" name="mysql-table" value="schuelerleistungsdaten">
			<table class="table table-hover">
				<thead>
					<tr bgcolor=#dddddd>
						<th>Name</th>
						<th>Vorname</th>
				
			';
				
			//tabellenbody: klickbare pseudoüberschriften & nachher die Felder 
			$table .= '
					<th>&nbsp;</th>
							';
				//klickbare pseudo-überschriften um sie Notenart auszuwählen 
				foreach($notenart as $item)
				{
					$table .= '								
								<th><a href='. $url .'&notenart='. $item .'>'. str_replace('_',' ',$item) .'</a></th>
					';
				}
			$table .= '
					</tr>
					';
				//pdf und cvs download buttons
				$table .= '
						<tr bgcolor=#ffffff>
							<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
							';
				//buttons für den download der Notenblätter 
				$downloadliste = array('KA_1', 'KA_2','KA_3','Klausur_1','Klausur_2');
				foreach($notenart as $item)
				{
					
					if (in_array($item,$downloadliste))
					{					
						//Alternative: <a href=export.php?download=notenliste_csv&notenart='. $item .'&lerngruppe='. $ueberschrift .' >csv</a>
						$table .= '								
								<td>	
									<a href=print_UebersichtSchriftlicheArbeit.php?notenart='. $item .'&lerngruppe='. $ueberschrift .' >
									<button type="button" class="btn btn-info"><span class="glyphicon glyphicon-print" title="Übersicht schriftliche Arbeit"></span></button>
									</a>								
									<a href='. $url .'&notenart='. $item .'>
									<button type="button" class="btn btn-primary"><span class="glyphicon glyphicon glyphicon-pencil"></span></button>
									</a>
								</td>
						';
					}
					else
					{
						$table .= '								
								<td>
									<a href='. $url .'&notenart='. $item .'>
									<button type="button" class="btn btn-primary"><span class="glyphicon glyphicon glyphicon-pencil"></span></button>
									</a>								
								</td>
						';
					}	
				}
				
			$table .= '
					</tr></thead><tbody>
					';

				$lfdnr = 0;			
				// Vergleicht, ob im Array Noteneinstellungen der Jahrgang aufgeführt ist, um ggf die Eingabe zu sperren				
				foreach ($_SESSION['zensurenliste'] as $zensur) 
				{			
					$counter = 0;
				
		
					foreach ($noteneinstellungen as $jgst)
					{
 						$jgst = str_replace("0", "", $jgst);
						if ($jgst == $zensur['Jahrgang'])
						{
						$counter++;
						}
					}	
		 
					if ($counter < 1)
					{
					$inputstatus = 'disabled';
					$eingabestatus = 'Eingabe gesperrt';
					}
					else 
					{
					$inputstatus = '';	
					$eingabestatus = '';
					}

					
					//Einträge in der Tabelle mit den schon gesetzten Noten aus der DB...
					if (($zensur['Kurs_ID'] == $_GET['id']) or (($fachklasse['1'] == $zensur['Fach_ID']) and  ($fachklasse['0'] == $zensur['Klasse'])))
					{	
						$lfdnr++;	
						$liste[$zensur['Schueler_ID']][ID]=$zensur['ID'];	
						$liste[$zensur['Schueler_ID']][Name]=$zensur['Name'];	
						$liste[$zensur['Schueler_ID']][Vorname]=$zensur['Vorname'];	
							
					//	$_SESSION['liste'][$zensur['Schueler_ID']] = $zensur['Name'];			
						//[Todo:die Sessionvariable wie bei Leistungsübersicht.php setzen und die Kursbezeichnung auch damit der link auf die weiterbuttons richtig setzt]
						$table .= '
						<tr>	
						<td>'. $zensur['Name'].'</td>
						<td>'. $zensur['Vorname'].'<input type="hidden" name="' . $zensur['ID'] . '-Jahrgang" id="' . $zensur['ID'] . '-Jahrgang" value="' . $zensur['Jahrgang'] . '"></td>
						<td>'. $zensur['Kursart'].'</td>						
							';						
						
					
						foreach($notenart as $item)
						{
							
							//Einträge mit Löschungsvermerkt und "0" Fehlstd auf unsichtbar setzen. 							
							if($_SESSION['notenarray'][$zensur['ID'] ][$item] == '0') {
								$eintrag='';
							}
							elseif($_SESSION['notenarray'][$zensur['ID'] ][$item] == 'L') {
								$eintrag='';
							}
							else {
								$eintrag=$_SESSION['notenarray'][$zensur['ID'] ][$item];								
								
							}
							
							//[TODO]: $inputtype auf disabled setzen bei den GKM-Wahlen in den KA_1 und KA_2 und Sch_GES fällen.  
							// erzeugt ein Eingabefeld, wenn diese Notenart ausgewählt ist.
							//if (($item == $_GET['notenart']) and ($zensur['Kursart'] != GKM ) and ($zensur['Kursart'] != AB4 ))
							if ($item == $_GET['notenart'])								
							{
								//if(	( (($zensur['Kursart'] != GKM ) or ($zensur['Kursart'] != AB4 )) and ( ($item != 'Klausur_1') and ($item != 'Klausur_2') and ($item != 'Schr_Ges'))) ) {
									$table .= '
									<td>
									<input type="text" size="3" name="' . $zensur['ID'] . '-'.$item.'" id="' . $zensur['ID'] . '-'.$item.'" value="' . $eintrag . '" '. $inputstatus .'>
									</td>
									';
								//}
							}
							else 
							{
								$table .= '
								<td>
								' . $eintrag .  '
								</td>
								';
							}
				
						}

						//eingabe sperren, falls dies per webschild_einstellungen in der DB so eingestellt war...
						$table .= '
							<td>' . $eingabestatus . '</td>					
							</tr>
						';
					}
				}

				//tabellen-footer
				$table .= 
					//''''''''''''''''''''				
				
				'
					</tbody>	
				</table>
					
						<input type="button" class="btn btn-danger pull-right" id="submit" value="speichern">
					
				</form>	
				';	

				//liste für den download bereitstellen:
				
				$_SESSION['liste']=$liste;
				
				//echo "<pre>";
				//print_r($liste);
				//echo "</pre>";



	//####################################################################################################
	//html output zusammenbasteln:
	//####################################################################################################
	
	
	echo ('
	<div class="container theme-showcase" role="main">
		<div class="row" >		 
				<h2  style="color:blue;">' . $ueberschrift . ' ('.$lfdnr.')</h2>
				<h3>' . $kommentar . '</h3>
				
				'. $menue .'
				<small> mögliche Einträge: Noten / E1, E2 ,E3 bei VTK und AGs / NT: nicht teilgenommen / AT: Attest / NB: nicht bewertbar / L: Teilnote löschen </small>
				'. $table .'
		</div>
		<div class="row" >	
			<p id="alert" style="display:none;" class="alert alert-success test-center" name="feedback"><i class="glyphicon glyphicon-ok"></i><span id="show"></span>
			</p>
				
					
		
		</div>
	</div>
	');
}
else
{
	echo ('
	<div class="container theme-showcase" role="main">
		<div class="row">
			<div class="col-sm-9">  
				<h2>Dateneingabe über SchILD gesperrt</h2>
				<a href="index.php">
				<button type="button" class="btn btn-success pull-right">weiter</button>
				</a>	
			</div>
		</div>
	</div>
	');
}

?>
<script>

$(document).ready(function(e){
	$('#submit').click(function(){
		$.ajax({
			url	:"insert.php",
			type	:'POST',	
			data	:$("#frm").serialize(),
			success	:function(result)			
			{
				$('#alert').show();
				$('#alert').focus();
				$('#weiter').show();				
				$('#zurueck').show();
				$('#myTable').hide();
				$('#submit').hide();
				
				alert(result);
				window.location = document.documentURI;
				//alert(result);
				//$('#show').html(result);
			}
		});

	
	});

});

</script>
	

<?php include('includes/footer.inc.php');?>

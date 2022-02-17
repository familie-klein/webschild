<?php
include('includes/header.inc.php'); 

//##################################################################################################
// hier wird alles was gedruckt werden soll in eine Variable $hmtl geschrieben.
// Achtung: die Bootsraps ansicht wird größtenteils gekickt

$gebrochene_noten=array("1+","1","1-","2+","2","2-","3+","3","3-","4+","4","4-","5+","5","5-","6",);
//$ganze_noten=array("1","2","3","4","5","6");
	
// Zensuren und Teinoten aus der DB holen und Variablen aktualisieren: 
$_SESSION['zensurenliste'] = update_zensurenliste($database,$_SESSION['zensurenliste']);
$_SESSION['notenarray'] = get_notenarray($database,$_SESSION['zensurenliste']);	
	
//html zusammensetzen:
//header und style
	
	
	
$html = '
	
	<h2>Notenübersicht: ' . $_SESSION['ueberschrift'] .' - ' . $_SESSION['krz'] . ' - '. $_GET['notenart'] . '</h2>
	';

$html .= '
	<table border="0" width="100%">
		<tr><td>

			<table border="1">
			';
					
	
	foreach ($gebrochene_noten as $note)
	{
		$statistikarray[$note]=0;
	}
	//Daten aus den Session-arrays zusamensetzen
	//echo "<pre>";
		//print_r($_SESSION['notenarray']);
	//echo "</pre>";
	
	foreach ($_SESSION['liste'] as $zensur)
	{
		//echo "<pre>";
		//print_r($zensur);
		//echo "</pre>";


		
			$html .= '
						<tr>
						';
			$html .= '<td style="padding-right:1em;">
						'. $zensur['Name'] .'
						</td>
						';
			$html .= '<td style="padding-right:1em;">
						'.$zensur['Vorname']. '
						</td>
						';
			$html .= '<td style="padding-left:1em;padding-right:1em;">
						'.$_SESSION['notenarray'][$zensur['ID']][$_GET['notenart']]. '
						</td>';
			$html .= '
						</tr>
						';
			
			//statistikarray befüllen: 
			if($_SESSION['notenarray'][$zensur['ID']][$_GET['notenart']]) {
				$statistikarray[$_SESSION['notenarray'][$zensur['ID']][$_GET['notenart']]]++;		
			}
			else{
				$NT++;			
			}	
	}

	//Zeilenumbruch:
	


	
	$html .= '
			
			</table>
			</td><td>&nbsp; </td>
			<td style="vertical-align: top;">
			
			<table border="0" class="pull-right"><tr><td>
			';
	$bild = notenDiagramm ($statistikarray,"350","250");
	$statistikarray['NT']=$NT;	
	$html .= '	
			<div class="pull-right">
			<img src="data:image/png;base64,'.$bild.'">	
			</td></tr>
			<tr><td>&nbsp;</td></tr>
			<tr style="text-align: right;"><td style="text-align: center;">';
			
	$html .= notenTabelle($statistikarray);			
			
	$html .= '</td></tr>
	
				<tr><td>
				<table class="pull-right" style="border:0 !important;">
			<tr><td>&nbsp; </td><td></td></tr>
			<tr><td>Klausurdatum:</td><td>___________________________________</td></tr>
			<tr><td>&nbsp; </td><td></td></tr>
			<tr><td>Klausurthema: </td><td>___________________________________</td></tr>
			<tr><td>&nbsp; </td><td></td></tr>
			<tr><td>Unterschrift: </td><td>___________________________________</td></tr>
			<tr><td>&nbsp; </td><td></td></tr>
			<tr><td>Unterschrift SL:</td><td>___________________________________</td></tr>		
		</table>				
				
				</td></tr>	
	
			</table>
		</td></tr>
		<tr><td style="vertical-align: top;">Bemerkungen:</td><td></td><td style="text-align: right;">';
		
	$html .= '	
		
		
		
		</td></tr>
		</table>
				</div>	
			';
			
		
		
	
	
// ################################################################################
// webseite mit Druckvoransicht und allen Steuerelementen, die nicht gedruckt werden sollen, zusammenbasteln:
	
	echo '
		
		<div class="container theme-showcase" role="main">
			
				<div class="no-print">
				<button type="button" class="btn btn-info pull-right" onclick="printThisWindow()" title="drucken"><span class="glyphicon glyphicon-print"></span></button>
				</div>
			'; 

	
	echo $html;
	
	echo '	
				
			</div>
		</div>
		';

include('includes/footer.inc.php');
?>

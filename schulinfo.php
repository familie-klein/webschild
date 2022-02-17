<?php 
include('includes/header.inc.php'); 

try 
	{
		$result = $database->query("SELECT * FROM schueler WHERE Status LIKE '2' AND Geloescht LIKE '-' ");
	}
	catch (DatabaseException $e) 
	{
		echo ("Keine Datenbankabfrage möglich.");
	}
	
	//array zusammenstellen - id als key. 
	while ($obj = $database->fetchObject ($result) ) 
	{	
		$statistik[Anzahl]++;
		$statistik[Geschlecht][geschlecht($obj->Geschlecht)]++;
		$statistik[Jahrgang][$obj->ASDJahrgang]++;	
		$statistik[Klasse][$obj->Klasse]++;	
		if($obj->Migrationshintergrund == "+")	
		{
			$statistik[Migrationshintergrund]++;
		}
		$statistik[Herkunkt][$obj->LSSchulForm]++;
		$statistik[Religion][$obj->ReligionAbk]++;
		
		$jahrgangsid = get_lernabschnitt_ID($database,$obj->ID,$_SESSION['schuljahr'],$_SESSION['abschnitt']); 
		$faecher = get_faecherliste($database,$jahrgangsid);	
		foreach($faecher as $fach)	
		{
			if($fach[KursartAllg]=='WPI') {
				$statistik[WPI][$obj->ASDJahrgang][$_SESSION['faecher_zuordnung'][$fach[Fach_ID]]]++;
			}
			if($fach[KursartAllg]=='WPII') {
				$statistik[WPII][$obj->ASDJahrgang][$_SESSION['faecher_zuordnung'][$fach[Fach_ID]]]++;
			}
			if($fach[KursartAllg]=='GK') {
				$statistik[GK][$obj->ASDJahrgang][$_SESSION['faecher_zuordnung'][$fach[Fach_ID]]]++;
			}
			if($fach[KursartAllg]=='LK') {
				$statistik[LK][$obj->ASDJahrgang][$_SESSION['faecher_zuordnung'][$fach[Fach_ID]]]++;
			}
		}		
	}

	//table zusammenbasteln: 
	
$table ='
	<table class="table ">
	<tbody>
		<tr>
			<td class="col-md-1">Schülerzahl</td>
			<td class="col-md-1">' . $statistik[Anzahl] . '</td> 
			<td class="col-md-1"></td>
			<td class="col-md-1" >männlich:</td>
			<td class="col-md-1" >' . $statistik[Geschlecht][m] . '</td>
			<td class="col-md-1" ></td>
			<td class="col-md-1" >weiblich:</td>
			<td class="col-md-1">' . $statistik[Geschlecht][w] . '</td>
			<td></td>
		</tr>	
		<tr></tr>
		<tr>
			<td class="col-md-1">Jahrgang</td>
			';
			$keys = array_keys ( $statistik[Jahrgang] );
			foreach ($keys as $item)
			{
				$table .= '<td class="col-md-1"> '  . $item . '</td>';		 
			}		
$table .= '
		</tr>
		<tr><td></td>

';			
			foreach ($statistik[Jahrgang] as $item)
			{
				$table .= '<td class="col-md-1"> '  . $item . '</td>';		
			}
			
$table .= '					
		</tr><tr></tr>
		<tr>
			<td></td>
		</tr>		
		
	</tbody>	
	</table>	
';
		


?>

<div class="container theme-showcase" role="main">
	<div class="jumbotron">
	   	<div class="row">
			<div class="col-sm-2">  	
				<img src="./graphics/logo.png" alt="Bitte das Schullogo unter ./graphics/logo.png speichern." width="75%"> 
			</div>
			<div class="col-sm-8">
				<h2><?php echo ($_SESSION['schulbezeichnung']); ?></h2>
				<h3><?php echo ($_SESSION['schulbezeichnung2']); ?> </h3>
				<h4>Schuljahr: <?php echo ($_SESSION['schuljahr'] . '.' . $_SESSION['abschnitt']); ?> </h4>
			</div>
			
		</div>
	</div>
	<?=$table; ?>
<pre>
<?php print_r($statistik);  ?>
</pre>

</div>	
	

<?php include('includes/footer.inc.php');?>

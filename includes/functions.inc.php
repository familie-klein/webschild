<?php

//#####################################################################################################

function getPageLink()
{
	if($_SERVER['HTTPS'])
	{
		$output = 'https://' . $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	}
	else
	{
		$output =  'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	}
return $output;
}
//####################################################################################################




function array_sort_german2($inputarray)
{

	function umlErsetzen ($inputstring)
	{
		$outputstring = strtolower($inputstring);
		$outputstring = preg_replace("/\s+/", "", $outputstring);
		$umlautevorher = array('ä','ö','ü','ß','Ä','Ö','Ü','´','é','-',' ');
		$umlautenachher = array('ae','oe','ue','ss','ae','oe','ue','','e','','');
		$outputstring = str_replace($umlautevorher , $umlautenachher , $outputstring);
		

		return $outputstring; 
	}



	// erwartet wir hier ein Array in einer besimmten Struktur:
	// Keys sind die Datenbank ids, Namen und Vornamen sind Einträge bitte auf die Syntax achten. 
	// eindimensionales Array!

	//ein Keyarray erzeugen
	$id_list= array_keys($inputarray);
	$first_value = reset($inputarray);
	$keys= array_keys($first_value);
	
	foreach ($id_list as $value)
	{
		$sortarray[$value]['sortierung'] = umlErsetzen($inputarray[$value]['Name'] . $inputarray[$value]['Vorname'] );	
		//alle anderen eintragungen übernehmen		
		foreach ($keys as $key)
		{
			
			$sortarray[$value][$key] = $inputarray[$value][$key];
		}
	}


	//sortieren des aarys für das erste Erscheinen der Liste in alphabet. Reichenfolge der Nachnamen 
	asort($sortarray, 3 );



	//sortieungseintrag bbzw. erste zeile wiederlöschen
	foreach ($id_list as $value)
	{
		unset($sortarray[$value]['sortierung']);
	}
	

	return $sortarray; 
}

//#####################################################################################################

function array_sort_kuerzel($inputarray)
{

	function umlErsetzen ($inputstring)
	{
		$outputstring = strtolower($inputstring);
		$outputstring = preg_replace("/\s+/", "", $outputstring);
		$umlautevorher = array('ä','ö','ü','ß','Ä','Ö','Ü','´','é','-');
		$umlautenachher = array('ae','oe','ue','ss','Ae','Oe','Ue','','e','');
		$outputstring = str_replace($umlautevorher , $umlautenachher , $outputstring);

		return $outputstring; 
	}


	// erwartet wir hier ein Array in einer besimmten Struktur:
	// Keys sind die Datenbank ids, Namen und Vornamen sind Einträge bitte auf die Syntax achten. 
	// eindimensionales Array!

	//ein Keyarray erzeugen
	$id_list= array_keys($inputarray);
	$first_value = reset($inputarray);
	$keys= array_keys($first_value);
	
	foreach ($id_list as $value)
	{
		$sortarray[$value]['sortierung'] = umlErsetzen($inputarray[$value]['Kürzel']);	
		//alle anderen eintragungen übernehmen		
		foreach ($keys as $key)
		{
			
			$sortarray[$value][$key] = $inputarray[$value][$key];
		}
	}
	
	//sortieren des aarys für das erste Erscheinen der Liste in alphabet. Reichenfolge der Nachnamen 
	asort($sortarray, 3 );
	

	//sortieungseintrag bbzw. erste zeile wiederlöschen
	foreach ($id_list as $value)
	{
		unset($sortarray[$value]['sortierung']);
	}
	

	return $sortarray; 
}



//#####################################################################################################
	
function urlExists($url=NULL)  
{  
    if($url == NULL) return false;  
    $ch = curl_init($url);  
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);  
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
    $data = curl_exec($ch);  
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);  
    curl_close($ch);  
    if($httpcode>=200 && $httpcode<300){  
        return true;  
    } else {  
        return false;  
    }  
}  

//#####################################################################################################
		

function HomepageLaden($url, $postdata)
        {
        //$agent = "Meine Browserkennung v1.0 :)";
        //$header[] = "Accept: text/vnd.wap.wml,*.*";
        $ch = curl_init($url);

        if ($ch)
            {
            curl_setopt($ch,    CURLOPT_RETURNTRANSFER, 1);
            //curl_setopt($ch,    CURLOPT_USERAGENT, $agent);
            //curl_setopt($ch,    CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch,    CURLOPT_FOLLOWLOCATION, 1);

            # mit den nächsten 2 Zeilen könnte man auch Cookies
            # verwenden und in einem DIR speichern
            #curl_setopt($ch,    CURLOPT_COOKIEJAR, "cookie.txt");
            #curl_setopt($ch,    CURLOPT_COOKIEFILE, "cookie.txt");

            if (isset($postdata))
                {
                curl_setopt($ch,    CURLOPT_POST, 1);
                curl_setopt($ch,    CURLOPT_POSTFIELDS, $postdata);
                }

            $tmp = curl_exec ($ch);
            curl_close ($ch);
            }
        return $tmp;
        } 

//#####################################################################################################
		

function datums_wandler ($Datum)
	{	
	// ***************************************************************
	//Diese Funktion formatiert das Geb-datum in mysql-format in ein deutsches Datum um und gibt dieses zurück.
	// ***************************************************************
    if(strlen($Datum) == 10)
	    {
        $GewandeltesDatum = substr($Datum, 8, 2);
        $GewandeltesDatum .= ".";
        $GewandeltesDatum .= substr($Datum, 5, 2);
        $GewandeltesDatum .= ".";
        $GewandeltesDatum .= substr($Datum, 0, 4);
        return $GewandeltesDatum;
	    }
    elseif(strlen($Datum) == 19)
	    {
        $GewandeltesDatum = substr($Datum, 8, 2);
        $GewandeltesDatum .= ".";
        $GewandeltesDatum .= substr($Datum, 5, 2);
        $GewandeltesDatum .= ".";
        $GewandeltesDatum .= substr($Datum, 0, 4);
        //$GewandeltesDatum .= substr($Datum, 10);
        return $GewandeltesDatum;
	    }
    else
	    {
        return FALSE;
	    }
	}


//#####################################################################################################
		

function geschlecht($int)
	/*****************************************************************
	Diese Funtion liefert m oder w für männlich oder weiblich zurück. 
	... und ersetzt damit diese dämlichen Zahlenkürzel in den Schild DB. 
	********************************************************************/
	{
	if ($int == 3)
		{
		return 'm';
		}
	elseif ($int == 4)
		{
		return 'w';	
		}
	else
		{
		return false;
		}
	}



//#####################################################################################################
		




function array_to_clickable_table ($data,$link)
	{
	/***********************************************************
	Der Funktion wird ein Array übergeben mit den den internen IDs der mysqlDB als keys 
	und den darzustellenden Daten als internes array in diesem array. 
	$data[id_number] gibt die darzustellenden Daten der Person mit dieser ID heraus. 
	Bsp.: $data[3][Telefon] liefert  '+492161******'  
	Der link soll mit der ID als Get Variable versehen zeilenweise aufgerufen werden können.

	*************************************************************/

	$id_list= array_keys($data);
	$first_value = reset($data);
	$keys= array_keys($first_value);

 
	$table = '
		<table class="table table-hover">
			<thead>
				<tr>
				';
	
				foreach ($keys as $value)
				{
								$table .= '<th>' . $value . '</th>';	
					
				}

				$table .=  '
				</tr>
			</thead>
			<tbody>';

			$i=0;
			
			foreach($data as $list_data)
			{ 
			$table .= '
				<tr onclick="window.location.href=\'' . $link . '?id='. $id_list[$i] .'\'">';
				
					foreach($keys as $key)	
					{								
							$table .= '	
								<td>'. $list_data[$key].'</td>';
					}
				$table .= '				
				</tr>
				'; 
				$i++;
			}		



			$table .=  '
		</table>';

	
	return $table;
	}





//#####################################################################################################
		

function array_to_linklist ($data,$link,$getvar)
	{
	/***************************************************************************************************
	Der Funktion wird ein Array of Arrays übergeben mit den den internen IDs der mysqlDB als keys 
	und den darzustellenden Daten als internes array in diesem array. Die zu verlinkende Webseite 
	wird als $link übergeben und es wird als Ausgabewert eine Liste von Links erzeugt, die als 
	Get-Variable die ID führt und die Daten darstellt.
	***************************************************************************************************/

	$id_list= array_keys($data);
	$first_value = reset($data);
	$keys= array_keys($first_value);	
	
	$i=0;
	foreach($data as $list_data)
	{ 
		$linklist .= '<a href="' . $link . '?'. $getvar .'&id='. $id_list[$i] .'" class="list-group-item">';
		foreach ($list_data as $value){
			$linklist .= $value . ' '; 
		}
		$linklist .= '</a>';		
		$i++;
	}		

	

	return $linklist;
	}


//#####################################################################################################
				

function array_to_linklist2 ($data,$link,$getvar)
	{
	/***************************************************************************************************
	Im Gegensatz zu Version 1 (s.o.) ist hier die $getvar Variable der name der Index laufvariablen 
	***************************************************************************************************/

	$id_list= array_keys($data);
	$first_value = reset($data);
	$keys= array_keys($first_value);	
	
	$i=0;
	foreach($data as $list_data)
	{ 
		$linklist .= '<a href="' . $link . '?'. $getvar .'='. $id_list[$i] .'" class="list-group-item">';
		foreach ($list_data as $value){
			$linklist .= $value . ' '; 
		}
		$linklist .= '</a>';		
		$i++;
	}		

	

	return $linklist;
	}





//#####################################################################################################
		

function usergruppen($schild_gruppen, $LehrerKuerzel)
{
	//usergruppe als einzelne gruppen in lesbarer Form zurückgeben

	//Hier werden die Einträge von Schild NRW in den gegebenen Rechten abgefragt. 

	for($i=0; $i<count(schild_gruppen); $i++)
	{
		if($schild_gruppen[$i] == '1') {
			$group['admin'] = '1';
			$_SESSION['LehrerKuerzel'] = '';
			}
		if($schild_gruppen[$i] == '3') {
			$group['lehrer'] = '1';
			$_SESSION['LehrerKuerzel'] = $LehrerKuerzel;		
			}
	}
return $group;
}

//#####################################################################################################
		
function schild_crypt($password)
{

	// Diese Funktion stellt die Verschüsselung des Passworts durch SchildNRW nach. 
	//Die Schildverschlüsselung behält bei einer Ascii-Darstellung der Buschstaben in Hexform den ersten Teil bei und spiegelt den zweiten Teil. Sie ist daher umkehrbar ... und man braucht keine zusätzlich encryption.

	function hex2str($hex)
		{
	      	for($i=0;$i<strlen($hex);$i+=2)
	      		{
	        	$str.=chr(hexdec(substr($hex,$i,2)));
	      }
	      return $str;
   	}

	function str2hex($string)
             	{
		for($i=0;$i<strlen($string);$i+=1)
	      		{
	        	$hex.=dechex(ord(substr($string,$i,1)));
	      	}
	      	return $hex;
	}

	function change($string)
	     	{
		$spiegelarray = array("0"=>"f", "1"=>"e","2"=>"d","3"=>"c","4"=>"b","5"=>"a", "6"=>"9","7"=>"8","8"=>"7","9"=>"6","a"=>"5","b"=>"4","c"=>"3", "d"=>"2","e"=>"1","f"=>"0",);
		for($i=0;$i<strlen($string);$i+=2)
	      		{
			//den ersten Teil des hex-codes übernehmen
			$hex.= substr($string,$i,1);
			//jeweils den zweiten Teil des hex-codes spiegeln:
			$zahl = substr($string,$i+1,1);
			$hex.= $spiegelarray[$zahl];	        	
	      	}
	      	return $hex;
	}


	$code = str2hex($password);
	$code = change($code);
	$code = hex2str($code);
	
	return $code;
}


//#####################################################################################################

function txt_erstellen ($result, $file)
{

	// Diese Funktion erstellt eine txt-tabelle (pseudo-cvs) ... und gibt als result die Zeilen zurück.

    $i = 1;      //Row Zähler
    $fc = "";   //File Content
	for($i=0; $i<count($result); $i++)	{
	        $fc.= $result[$i]['Name'];
		$fc.="\t";
		$fc.= $result[$i]['Vorname'];
		$fc.="\t";
		$fc.= $result[$i]['Klasse'];
		$fc.="\t";
		$fc.="\n";
		
        }
        $fc.="\n";
        $i++;
	echo $fc;
    
    file_put_contents($file,$fc);
    return $i;
}


//###################################################################################################################

function notenTabelle($statistikarray) 
{
	
	$notenbezeichnung = array_keys($statistikarray);
	
	$output = '
		<table border="1">
			<tr>
	';
	//Tabellenüberschriften aus Arraybezeichnung herausholen
	
	foreach ($notenbezeichnung as $note)
	{	
		$output .= '
				<td style="padding-left:0.5em; padding-right:0.5em;">' . $note . '</td>';
	}
	
	$output .= '
			</tr>
			<tr>
	';
	
	foreach ($statistikarray as $notenValue)
	{	
		$output .= '
				<td style="padding-left:0.5em;padding-right:0.5em;">' . $notenValue . '</td>';
	}
	$output .= '
			</tr>
		</table>
	';
	return $output;
}


//#####################################################################################################


function notenDiagramm ($data,$width,$height)
{
	//als $data soll ein Array geliefert werden, welches als Keys die Einträge der Notenbezeichnungen hat und als  
	$anzahlEintraege = count($data);
	$sum = array_sum($data);
	$maxValue=max($data);
	$notenBezeichnung = array_keys($data);
		
	
	
	if ($anzahlEintraege>0)
		{
			
        	$im = imagecreate($width,$height); // width , height px

			$white 	= imagecolorallocate($im,255,255,255); 
        	$black 	= imagecolorallocate($im,0,0,0);   
        	$red 	= imagecolorallocate($im,255,0,0);   
			$gray 	= imagecolorallocate ($im,0xcc,0xcc,0xcc);
	
        	//imageline($im, 10, 5, 10, 230, $black);
        	//imageline($im, 10, 230, 300, 230, $black);
    	
			$x_raster=round($width/(4*$anzahlEintraege + 3), $precision = null);
			$x_width = 3*$x_raster;
			$luecke = $x_raster;
			$x = round(($width - (($x_width + $luecke) * $anzahlEintraege)- $luecke ) /2, $precision = null)+$luecke; //startwert
			    
			$y = $height-35;  
			
       	$i=0;
        	foreach($data as $value)
			{
        	  $y_ht = ($value/$maxValue)* ($y-10);    
        	  	imagefilledrectangle($im,$x,$y,$x+$x_width,($y-$y_ht),$gray);
        	   imagestring( $im,2,$x+(($x_width-$luecke) / 2) ,$y+10,$notenBezeichnung[$i],$black);
            $i++;  
        	  $x += ($x_width+$luecke);  
         }
        
        	
		# Bild in Speicher schreiben
		ob_start();
		ImagePNG($im);
		# Speicher leeren:
		imagedestroy ($im);
		$bild=ob_get_contents(); 
		# Speicher leeren:
		ob_clean();
		# Bild base64-kodieren:
		$im1=base64_encode ($bild);
	}
	else
	{
		$im1 = '';
	}

	return $im1;
}

//#####################################################################################################

function noten2punkte($data){
	$out = array('0','0','0','0','0','0','0','0','0','0','0','0','0','0');
	$out[15] = $data['1+'] ;
	$out[14] = $data['1'] ;	
	$out[13] = $data['1-'];
	$out[12] = $data['2+'];
	$out[11] = $data['2'];
	$out[10] = $data['2-'];
	$out[9] = $data['3+'];
	$out[8] = $data['3'];
	$out[7] = $data['3-'];
	$out[6] = $data['4+'];
	$out[5] = $data['4'];
	$out[4] = $data['4-'];
	$out[3] = $data['5+'];
	$out[2] = $data['5'];
	$out[1] = $data['5-'];
	$out[0] = $data['6'];
	
	return $out;
}

//#####################################################################################################

function notenuebersicht_oberstufe ($data)
{

	// read the post data
	$sum = array_sum($data);
	if ($sum>0)
	{
           $height = 245;
	        $width = 245;
	        
	        $im = imagecreate($width,$height); // width , height px
	
	        $white 	= imagecolorallocate($im,255,255,255); 
	        $black 	= imagecolorallocate($im,0,0,0);   
	        $red 	= imagecolorallocate($im,255,0,0);   
			  $gray 	= imagecolorallocate ($im,0xcc,0xcc,0xcc);
		
	        //imageline($im, 10, 5, 10, 230, $black);
	        //imageline($im, 10, 230, 300, 230, $black);
	    
	
	        $x = 15;   
	        $y = 200;   
	        $x_width = 10;  
	        $y_ht = 0;
			  $luecke = 5; 
	       
	        for ($i=1;$i<16;$i++)
				{
	        
					$y_ht = ($data[$i]/$sum)* $height;    
	          	imagefilledrectangle($im,$x,$y,$x+$x_width,($y-$y_ht),$gray);
			 		//imagerectangle($im,$x,$y,$x+$x_width,($y-$y_ht),$red);
       			imagestring( $im,2,$x+(($x_width-$luecke) / 2) ,$y+10,$i,$black);
       	       
     	   		$x += ($x_width+$luecke);  
         
        	}
        
        
		# Bild in Speicher schreiben
		ob_start();
		ImagePNG($im);
		# Speicher leeren:
		imagedestroy ($im);
		$bild=ob_get_contents(); 
		# Speicher leeren:
		ob_clean();
		# Bild base64-kodieren:
		$im1=base64_encode ($bild);
	}
	else
	{
		$im1 = '';
	}

		return $im1;
}

//#####################################################################################################

function notenuebersicht_KA ($data)
{

	// read the data
	$sum = array_sum($data);
	if ($sum>0)
	{
        
	        $height = 245;
	        $width = 245;
	        
	        $im = imagecreate($width,$height); // width , height px
	
	        $white 	= imagecolorallocate($im,255,255,255); 
	        $black 	= imagecolorallocate($im,0,0,0);   
	        $red 	= imagecolorallocate($im,255,0,0);   
				$gray 	= imagecolorallocate ($im,0xcc,0xcc,0xcc);
		
	        //imageline($im, 10, 5, 10, 230, $black);
	        //imageline($im, 10, 230, 300, 230, $black);
	    
	
	        $x = 15;   
	        $y = 200;   
	        $x_width = 10;  
	        $y_ht = 0;
				$luecke = 5; 
	       
	        for ($i=1;$i<16;$i++)
				{
	        
					$y_ht = ($data[$i]/$sum)* $height;    
	          	imagefilledrectangle($im,$x,$y,$x+$x_width,($y-$y_ht),$gray);
			 		//imagerectangle($im,$x,$y,$x+$x_width,($y-$y_ht),$red);
       			imagestring( $im,2,$x+(($x_width-$luecke) / 2) ,$y+10,$i,$black);
       	       
     	   		$x += ($x_width+$luecke);  
         
        	}
        
        
		# Bild in Speicher schreiben
		ob_start();
		ImagePNG($im);
		# Speicher leeren:
		imagedestroy ($im);
		$bild=ob_get_contents(); 
		# Speicher leeren:
		ob_clean();
		# Bild base64-kodieren:
		$im1=base64_encode ($bild);
	}
	else
	{
		$im1 = '';
	}

		return $im1;
}




//#####################################################################################################


function check_input($varname,$value,$jahrgang)
{
	
	//Freizeichen und co entfernen
	$value=strtoupper(trim($value));

	//Filterbedingungen in praktische arrays packen: 
	$gebrochene_noten=array("1+","1","1-","2+","2","2-","3+","3","3-","4+","4","4-","5+","5","5-","6","E1","E2","E3","NT","AT","NB","L");
	$ganze_noten=array("1","2","3","4","5","6","E1","E2","E3","NT","AT","NB","L");
	$erlaubte_varnames_noten = array ('KA_1','KA_2','KA_3', 'Somi_1','Somi_2','Somi_Ges','Klausur_1', 'Klausur_2', 'Schr_Ges', 'Endnote');
	$erlaubte_varnames_sonstige = array ('Fehlstd', 'uFehlstd');


	if ( in_array($varname,$erlaubte_varnames_noten))  
	{
		
		if (($varname != 'Endnote') or ($jahrgang == 'Q1') or ($jahrgang == 'Q2') )
		{
			//vgl Note mit erlaubten gebrochenen Noten
			$returnvar = in_array($value,$gebrochene_noten);
		}
		else 
		{
			//nur in SI&EF Endnote auf ganze Noten prüfen
			$returnvar = in_array($value,$ganze_noten);
		}
		//kein Löschungsvermerk als Endnote möglich: 
		if (($varname == 'Endnote') and ( $value == 'L'))
		{
			$returnvar = false;		
		}
		
	}
	elseif(in_array($varname,$erlaubte_varnames_sonstige) )
	{	
		$returnvar = settype($value, "integer");
	}
	else
	{
		$returnvar = false;
	}

	return $returnvar;
}

//##########################################################################################################################################
//############################################### ab hier kommen Funktionen mit Datenbank abfragen #########################################
//##########################################################################################################################################




//********************* holt die Ortsbezeichnung aus der Datenbank ************
function plz_to_ort ($database,$plz)
{
	try	
	{
		$result = $database->query("SELECT Bezeichnung FROM K_Ort WHERE PLZ='" .$plz. "'");
	}
	catch (DatabaseException $e) 
	{
		echo ("Keine Datenbankabfrage möglich.");
	}
	
	$ort = $database->fetchObject ($result);
	
	return $ort->Bezeichnung;
}


function telnummerid_to_text ($database,$id)
{
	try	
	{
		$result = $database->query("SELECT Bezeichnung FROM K_Telefonart WHERE ID='" .$id. "'");
	}
	catch (DatabaseException $e) 
	{
		echo ("Keine Datenbankabfrage möglich.");
	}
	
	$bez = $database->fetchObject ($result);
	
	return $bez->Bezeichnung;
}


###################################################################################

function zensurenliste_erstellen ($database,$lehrer_kuerzel,$schuljahr,$abschnitt)
{

	function umlErsetzen ($inputstring)
	{
		$outputstring = strtolower($inputstring);
		$outputstring = preg_replace("/\s+/", "", $outputstring);
		$umlautevorher = array('ä','ö','ü','ß','Ä','Ö','Ü','´','é','-');
		$umlautenachher = array('ae','oe','ue','ss','ae','oe','ue','','e','');
		$outputstring = str_replace($umlautevorher , $umlautenachher , $outputstring);
		

		return $outputstring; 
	}


  //Kursbezeichnungen herausfiltern
	try 
	{
		$result = $database->query("SELECT * FROM Kurse WHERE Jahr LIKE '".$schuljahr."' AND Abschnitt LIKE '".$abschnitt ."'AND LehrerKrz LIKE '".$lehrer_kuerzel."';");
	}
	catch (DatabaseException $e) 
	{
		echo ("Keine Datenbankabfrage möglich.");
	}

	while ($obj = $database->fetchObject ($result) ) 
				{	
					$kurse[$obj->ID][kursbezeichnung] = $obj->KurzBez;
					$kurse[$obj->ID][jahrgang] = $obj->ASDJahrgang;		
					$kurse[$obj->ID][lehrer] = $obj->LehrerKrz;		
				}	

	// Unterricht aus der DB Filtern 
	//
	// alle Leistungsdaten des Lehrers herausfiltern
	try 
	{
		$result = $database->query("SELECT * FROM SchuelerLeistungsdaten WHERE FachLehrer='". $lehrer_kuerzel ."';");
	}
	catch (DatabaseException $e) 
	{
		echo ("Keine Datenbankabfrage möglich.");
	}

	while ($obj = $database->fetchObject ($result) ) 
	{	
		//bei jedem schuelerleistungsdatensatz das lernabschnittsdatum abfragen. 		
		$result_la = $database->query("SELECT * FROM SchuelerLernabschnittsdaten WHERE ID='". $obj->Abschnitt_ID ."' AND Jahr LIKE '".$schuljahr."' AND Abschnitt LIKE '".$abschnitt."';");
		$la_daten = $database->fetchObject ($result_la);

		//falls der schuelerleistungsdatensatz im aktuellen Halbjahr liegt diesen in die Zensurenliste schreiben.
		if ($la_daten)
		{	
			//Schulerdaten abfragen
			$result_schueler = $database->query("SELECT Name,Vorname FROM Schueler WHERE ID='". $la_daten->Schueler_ID ."' AND Status LIKE '2' AND Geloescht LIKE '-';");
			$schueler_daten = $database->fetchObject ($result_schueler);
			if($schueler_daten->Name)
			{			
				//Zensurenliste. array zusammenbasteln
				$zensurenliste[$obj->ID][ID] = $obj->ID;
				$zensurenliste[$obj->ID][Schueler_ID] = $la_daten->Schueler_ID;						
				$zensurenliste[$obj->ID][Abschnitt_ID] = $obj->Abschnitt_ID;							
				$zensurenliste[$obj->ID][Fach_ID] = $obj->Fach_ID;
				$zensurenliste[$obj->ID][Name] = $schueler_daten->Name;
				$zensurenliste[$obj->ID][Vorname] = $schueler_daten->Vorname;
				$zensurenliste[$obj->ID][Klasse] = $la_daten->Klasse;
				$zensurenliste[$obj->ID][Jahrgang] = $la_daten->ASDJahrgang;
				$zensurenliste[$obj->ID][Kursart] = $obj->Kursart;
				$zensurenliste[$obj->ID][KursartAllg] = $obj->KursartAllg;
				$zensurenliste[$obj->ID][Kurs_ID] = $obj->Kurs_ID;
				$zensurenliste[$obj->ID][Kurs_Bez] = $kurse[$obj->Kurs_ID][kursbezeichnung];
				$zensurenliste[$obj->ID][NotenKrz] = $obj->NotenKrz;
				$zensurenliste[$obj->ID][Fehlstd] = $obj->Fehlstd;
				$zensurenliste[$obj->ID][uFehlstd] = $obj->uFehlstd;
				$zensurenliste[$obj->ID][Warnung] = $obj->Warnung;
				//array, um die Daten des zensurenliste-arrays in alphabetischer Reihenfolge zu sortieren: 
				$sortierungsarray[$obj->ID] = umlErsetzen($schueler_daten->Name . $schueler_daten->Vorname);
			}
		}
	}


	//array alphabetisch sortieren:
	asort($sortierungsarray);

	//array mit Keys in der richtigen Reihenfolge ausgeben:
	$id_list= array_keys($sortierungsarray);

		
	foreach($id_list as $key)
	{		
		$zensurenliste_sortiert[$key] = $zensurenliste[$key];	
	}
	
	

	
	return $zensurenliste_sortiert;
	
}


##################################################################################################################################################

function update_zensurenliste($database,$zensurenliste)
{

	foreach ($zensurenliste as $zensur)
	{					
		$result = $database->query("SELECT * FROM SchuelerLeistungsdaten WHERE ID='".$zensur[ID]."';");
		//echo "<pre>";
		//print_r($zensur);
		//echo "</pre>";		
				
		while ($obj = $database->fetchObject ($result)) 
			{		
				//$zensurenliste[$obj->ID][ID] = $obj->ID;
				$zensurenliste[$obj->ID][NotenKrz] = $obj->NotenKrz;
				$zensurenliste[$obj->ID][Fehlstd] = $obj->Fehlstd;
				$zensurenliste[$obj->ID][uFehlstd] = $obj->uFehlstd;
			}
	}
	return $zensurenliste;
}


##################################################################################################################################################

function get_lernabschnitt_ID($database,$ID,$Jahr,$Abschnitt)
{
	$result = $database->query("SELECT ID FROM SchuelerLernabschnittsdaten WHERE Schueler_ID='".$ID."' AND Jahr='".$Jahr."' AND Abschnitt='".$Abschnitt."';");	
	$obj = $database->fetchObject ($result);

	return $obj->ID;
}

function get_fehlstd($database,$ID,$Jahr,$Abschnitt)
{
	$result = $database->query("SELECT SumFehlStd FROM SchuelerLernabschnittsdaten WHERE Schueler_ID='".$ID."' AND Jahr='".$Jahr."' AND Abschnitt='".$Abschnitt."';");	
	$obj = $database->fetchObject ($result);

	return $obj->SumFehlStd;
}

function get_fehlstdu($database,$ID,$Jahr,$Abschnitt)
{
	$result = $database->query("SELECT SumFehlStdU FROM SchuelerLernabschnittsdaten WHERE Schueler_ID='".$ID."' AND Jahr='".$Jahr."' AND Abschnitt='".$Abschnitt."';");	
	$obj = $database->fetchObject ($result);

	return $obj->SumFehlStdU;
}




//###################################################################################################################

function schuelerDbAbfrage ($database,$sqlquery, $spaltenarray)
{
	// Diese Funktion gibt die Schülerdaten aufgrund einer sql abfrage als array zurück. 
	// Die Form des Arrays ist angepasst an den Standart array[IDdesSuS][Spalte]
	// unter arrayname[1345][Name] findet man dann z.B. Mustermann
	
	//Daten aus der Mysql-db holen
	try 
	{
		$result = $database->query($sqlquery);
	}
	catch (DatabaseException $e) 
	{
		echo ("Keine Datenbankabfrage möglich.");
	}
	
	//array zusammenstellen - id als key. 
	while ($obj = $database->fetchObject ($result) ) 
	{	
			
		$liste[$obj->ID][Name] = $obj->Name;
		$liste[$obj->ID][Vorname] = $obj->Vorname;
		
		foreach($spaltenarray as $spalte)
		{
			// ein paa sonderfälle ...
			if ($spalte == 'Geschlecht')
			{
				$eintrag = geschlecht($obj->$spalte);	
			}
			elseif ($spalte == 'Geburtsdatum') 
			{
				$eintrag = datums_wandler($obj->$spalte);
			}
			elseif ($spalte == 'kursart') 
			{
				if ( ($kursart == 'GKM') or ($kursart == 'GKS') or ($kursart == 'AB3') or ($kursart == 'AB4')) 
				{
					$eintrag=$kursart;
					$spalte = 'Kursart';
				}
				else
				{
					$eintrag = NULL;
				}
				
			}
			else
			{
				$eintrag = $obj->$spalte;
			}

			if ($eintrag){
				$liste[$obj->ID][$spalte] = $eintrag;
			}
		}
		
	}
	
	return $liste;

}





##################################################################################################################################################

function liste_aller_abschnitte($database,$ID)
{
	$result = $database->query("SELECT ID FROM SchuelerLernabschnittsdaten WHERE Schueler_ID='".$ID."';");	
	$i = 0;
	while ($obj = $database->fetchObject ($result)) 
	{				
		$returnarray[$i]=$obj->ID;
		$i++;
	}
	
	return $returnarray;
}


##################################################################################################################################################

function get_faecherliste($database,$jahrgangsid)
{
	$result = $database->query("SELECT * FROM SchuelerLeistungsdaten WHERE Abschnitt_ID='".$jahrgangsid."';");	
	
	while ($obj = $database->fetchObject ($result)) 
	{				
		$returnarray[$obj->ID]['zensur']=$obj->ID;
		$returnarray[$obj->ID]['Fach_ID']=$obj->Fach_ID;
		$returnarray[$obj->ID]['lehrer']=$obj->FachLehrer;
		$returnarray[$obj->ID]['KursartAllg']=$obj->KursartAllg;		
		$returnarray[$obj->ID]['Kursart']=$obj->Kursart;		
		$returnarray[$obj->ID]['Kurs_ID']=$obj->Kurs_ID;
		
		/* Dieser Teil ist nicht aktiv, wenn sie nicht diese Zusätzliche Tabelle webschidl_teilnoten eingestellt haben. 
		// dies ist aber auch outdated - wird nicht weiterentsickelt, da nun auch in Schild voll implementiert
		//abfrage der zugehörigen Teilnoten:
		 $resulttn = $database->query("SELECT * FROM webschild_teilnoten WHERE noten_id='". $obj->ID ."';");	
		 while ($objtn = $database->fetchObject ($resulttn)) 
		{	
			$returnarray[$obj->ID][$objtn->notentyp] = $objtn->note;
		}
		*/
		$returnarray[$obj->ID]['Endnote']=$obj->NotenKrz;
		$returnarray[$obj->ID]['Fehlstd']=$obj->Fehlstd;
		$returnarray[$obj->ID]['uFehlstd']=$obj->uFehlstd;
		

	}
	
	return $returnarray;
}


##################################################################################################################################################

function get_notenarray($database,$zensurenliste)
{
	
	foreach ($zensurenliste as $zensur)
	{	
			
		$notenarray[$zensur['ID']]['Endnote'] = $zensur['NotenKrz'];
		$notenarray[$zensur['ID']]['Fehlstd'] = $zensur['Fehlstd'];
		$notenarray[$zensur['ID']]['uFehlstd'] = $zensur['uFehlstd'];
	
		/*
		$result = $database->query("SELECT * FROM webschild_teilnoten WHERE noten_id='".$zensur[ID]."';");	
				
		while ($obj = $database->fetchObject ($result)) 
			{		
				$notenarray[$zensur['ID']][$obj->notentyp] = $obj->note;
				//echo "<pre>";
				//print_r($obj);
				//echo "</pre>";	
			}
		*/
	}
	return $notenarray;
}

##################################################################################################################################################

function update_notenarray($database,$zensurenliste)
{
	
	foreach ($zensurenliste as $zensur)
	{	
			
		$notenarray[$zensur['ID']]['Endnote'] = $zensur['NotenKrz'];
		$notenarray[$zensur['ID']]['Fehlstd'] = $zensur['Fehlstd'];
		$notenarray[$zensur['ID']]['uFehlstd'] = $zensur['uFehlstd'];
		
		/*
		$result = $database->query("SELECT * FROM webschild_teilnoten WHERE noten_id='".$zensur[ID]."';");	
				
		while ($obj = $database->fetchObject ($result)) 
			{		
				$notenarray[$zensur['ID']][$obj->notentyp] = $obj->note;
				//echo "<pre>";
				//print_r($obj);
				//echo "</pre>";	
			}
		*/
	}
	return $notenarray;
}

##################################################################################################################################################





function get_noteneinstellungen($database)
{

	try 
	{
		$result = $database->query("SELECT * FROM `webschild_einstellungen`");
	}
	catch (DatabaseException $e) 
	{
		echo ("Keine Datenbankabfrage möglich.");
   }

	$webschild_einstellungen = $database->fetchObject($result);
	
	//array mit datenbankeinträgen erstellen
	$noteneinstellungen = explode(",",$webschild_einstellungen->noteneingabe);
	return $noteneinstellungen;
}

##################################################################################################################################################

function get_schild_noteneinstellungen($database)
{
	try 
	{
		$result = $database->query("SELECT NotenGesperrt FROM EigeneSchule ");
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
	return $eingabe;
}


####################################################################################################################################################

function getNotentable ($teilnotenart, $faecher) {
	
	$roteNoten = array('6','5-','5','5+','4-');

	$table = '
		<table class="table table-bordered">
			<thead>
				<tr><td></td><td></td><td></td>';
				
	foreach($teilnotenart as $item)
	{			
		$table .='	
			<td><b>' . $item . '</b></td>
			';
	}
	$table .= '
				</tr>
			</thead>
			';
	$table .= '
			<tbody>
		
			';	
	foreach($faecher as $fach)
	{			
		if(substr($fach['Kursart'],0,2)=='LK') {
			$table .='<tr style="background-color: #eeeeee">';
		}
		else {
			$table .='<tr>';
		}			
	
	
		$table .='	
				<td><b>' . $_SESSION['faecher_zuordnung'][$fach['Fach_ID']] . '</b></td>
				<td><small>' . $fach['Kursart'] . '</small></td>
				<td><small>' . $fach['lehrer'] . '</small></td>
			';
			
			foreach($teilnotenart as $item)
			{
				//prüfe auf Löschungsvermerk	und Nullen (in den Fehlstunden)			
				if ( ($fach[$item] == 'L') or ($fach[$item] == '0') )
				{
					$fach[$item]='';
				}		
				if($item =='Endnote') 
				{
					$begin_big = '<big>';
					$end_big	=	'</big>';
				}
				else{
					$begin_big = '';
					$end_big	=	'';
				}				
				
				$table .='	
				<td>';
				if (in_array($fach[$item],$roteNoten) and ($item != 'Fehlstd') and ($item != 'uFehlstd'))
				{
					$table .= '<b>'.$begin_big.'<font color="red"> ' . $fach[$item] . '</font>'.$end_big.'</b>';
				}
				elseif($item =='Endnote') 
				{
					$table .= '<b><big><font color="blue"> ' . $fach[$item] . '</font></big></b>';
				}
				else 				
				{
					$table .= $fach[$item];
				} 
				$table .= '</td>
				';
			}

		$table .='			
			</tr>
			';
	}		
			
	$table .= '
				
			</tbody>
		</table>	
			';			
			
	return $table;			
}


function getNotentableadmin ($teilnotenart, $faecher, $vorname, $nachname, $schueler_id, $schuelerJahrgang) {


	
	$fetteNoten = array('6','5-','5','5+','4-');

	$table = '
		<table class="table table-bordered">
			<thead>
				<tr><td></td><td></td><td></td>';
				
	foreach($teilnotenart as $item)
	{			
		$table .='	
			<td><b>' . $item . '</b></td>
			';
	}
	$table .= '
				</tr>
			</thead>
			';
	$table .= '
			<tbody>
		
			';	
	foreach($faecher as $fach)
	{	
		if(substr($fach['Kursart'],0,2)=='LK') {
			$table .='<tr style="background-color: #eeeeee">';
		}
		else {
			$table .='<tr>';
		}		
		$table .='	
				<td><b>' . $_SESSION['faecher_zuordnung'][$fach['Fach_ID']] . '</b></td>
				<td><small>' . $fach['Kursart'] . '</small></td>
				<td><small>' . $fach['lehrer'] . '</small></td>
			';
			
			foreach($teilnotenart as $item)
			{
				if($item =='Endnote') 
				{
					$begin_big = '<big>';
					$end_big	=	'</big>';
				}
				else{
					$begin_big = '';
					$end_big	=	'';
				}				
				
				$table .='	
				<td>';
				if (in_array($fach[$item],$fetteNoten) and ($item != 'Fehlstd') and ($item != 'uFehlstd'))
				{
					$table .= '<b>'.$begin_big.'<font color="red"> ' . $fach[$item] . '</font>'.$end_big.'</b>';
				}
				elseif($item =='Endnote') 
				{
					$table .= '<b><big><font color="blue"> ' . $fach[$item] . '</font></big></b>';
				}
				else 				
				{
					$table .= $fach[$item];
				} 
				$table .= '<a href="' .$_SERVER['PHP_SELF']. '?noten_id='.$fach[zensur]. '&fach=' . $fach['Fach_ID'] . '&note='.trim($fach[$item]). '&schueler_id='.$schueler_id. '&vorname='.$vorname. '&nachname='.$nachname. '&item='.$item.'&lehrer=admin&schuelerJahrgang='. $schuelerJahrgang .'"  ><button type="button" class="btn btn-default btn-sm pull-right"><span class="glyphicon glyphicon-pencil"></span></button></a></td>
				';
			}

		$table .='			
			</tr>
			';
	}		
			
	$table .= '
				
			</tbody>
		</table>	
			';			
			
	return $table;			
}

?>


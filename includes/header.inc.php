<?php session_start(); 
require_once ('dbconnect.inc.php');
require_once ('functions.inc.php');
require_once ('includes/db_functions.inc.php');
require_once ('config.inc.php');
$loc_de = setlocale(LC_ALL,  'de_DE.utf-8', 'de_DE@euro', 'de_DE.iso885915@euro', 'de_DE', 'deu_deu');
date_default_timezone_set('UTC');


// login Bearbeitung: Gegen Datenbank checken und in session registrieren

if (isset ($_POST['login']) ) 
{

	if (empty ($_POST['username']) || empty ($_POST['password']) ) 
	{
		//Fehlermeldung: unvollständige Eingabe!
		echo 
		('	<html lang="de">
			<head>
				<title>webschild</title>
	
				<meta charset="utf-8">
				<meta http-equiv="X-UA-Compatible" content="IE=edge">
				<meta name="viewport" content="width=device-width, initial-scale=1">
				<meta name="description" content="Schulverwaltungssoftware SchILD in OpenSource gedacht">
				<meta name="author" content="root" >

				<link rel="icon" href="./graphics/logo.ico"> 
				<link href="css/bootstrap.min.css" rel="stylesheet">
				<link href="css/bootstrap-theme.min.css" rel="stylesheet">
				<link href="css/theme.css" rel="stylesheet">
	
			</head>	
			<body role="document">

				<script type="text/javascript" src="js/jquery-3.2.1.min.js"></script> 
				<script type="text/javascript" src="js/bootstrap.min.js"></script> 
				<script type="text/javascript" src="js/jquery.tablesorter.min.js"></script>
				
				
				<div class="container theme-showcase" role="main"><h3>Benutzer oder Passwort nicht angegeben!</h3>
				<a href="index.php"><button type="button" class="btn btn-info pull-left">zurück zur Anmeldung</button></a>
				</div>
			</body>
			</html>
		');
		die;						
	}
	else
	{
		$username = $_POST['username'];
		$password_clear = $_POST['password'];
		
		$password = schild_crypt($password_clear);
		

		//userdaten holen ...
		try 
		{
			$result = $database->query("SELECT * FROM `users` WHERE US_LoginName='$username'");
		}
		catch (DatabaseException $e) 
		{
			echo ("Keine Datenbankabfrage möglich.");
			die;
    	}

		$i=0;

		//$row = mysql_fetch_row($result);
		while ($obj = $database->fetchObject ($result) ) 
		{

			$i++;
			//username festsetzen ...
			if($obj->US_Password != $password) 
			{
				//Fehlermeldung: Falsches Passwort!
				sleep(4);				
				echo 
				('	
					<html lang="de">
					<head>
						<title>webschild</title>
		
						<meta charset="utf-8">
						<meta http-equiv="X-UA-Compatible" content="IE=edge">
						<meta name="viewport" content="width=device-width, initial-scale=1">
						<meta name="description" content="Schulverwaltungssoftware SchILD in OpenSource gedacht">
						<meta name="author" content="root" >
	
						<link rel="icon" href="./graphics/logo.ico"> 
						<link href="css/bootstrap.min.css" rel="stylesheet">
						<link href="css/bootstrap-theme.min.css" rel="stylesheet">
						<link href="css/theme.css" rel="stylesheet">
	
					</head>	
					<body role="document">

						<script type="text/javascript" src="js/jquery-3.2.1.min.js"></script> 
						<script type="text/javascript" src="js/bootstrap.min.js"></script> 
						<script type="text/javascript" src="js/jquery.tablesorter.min.js"></script>
						<script type="text/javascript" src="js/insert.js"></script>
						<script type="text/javascript" src="js/include.js"></script>
						<div class="container theme-showcase" role="main"><h3>Passwort falsch!</h3>
						<a href="index.php"><button type="button" class="btn btn-info pull-left">zurück zur Anmeldung</button></a>
						</div>
					</body>
					</html>
				');
				die;				
			}
			else 
			{
				//Erfolgreiche Anmeldung!!!
				//user anmelden: Session Variablen setzen.
				$_SESSION['username'] = $obj->US_LoginName;
				$_SESSION['fullname'] = $obj->US_Name;
				$_SESSION['userid'] = $obj->ID;
				//usergroup festsetzen ...
				$_SESSION['groupmember'] = $obj->US_UserGroups;
				$_SESSION['privileges'] = $obj->US_Privileges;
				//[TODO] admin abfrage mit groumembers koppeln
					
				require_once ('./includes/session_start.inc.php');
			}
		}

		if ($i == 0)
		{
			//Fehlermeldung: Benutzer unbekannt!

			//fallback auf einen anderen Authenifizierungsmodus: Über aufgebohrte Ldap authentifizeirung per json-objekt
			//spezialentwicklung für die Marienschule MG
		
			


			$_url = "https://lernen.marienschule.de/login/get_name.php";
		 	$_buffer = HomepageLaden($_url, "username=". $_POST['username'] ."&password=". $_POST['password'] ."&status=lehrer");
		    $userdata=json_decode($_buffer); 
			if (!urlExists($_url) )
				{
				echo
					('
					<html lang="de">
					<head>
						<title>webschild</title>
		
						<meta charset="utf-8">
						<meta http-equiv="X-UA-Compatible" content="IE=edge">
						<meta name="viewport" content="width=device-width, initial-scale=1">
						<meta name="description" content="Schulverwaltungssoftware SchILD in OpenSource gedacht">
						<meta name="author" content="root" >
	
						<link rel="icon" href="./graphics/logo.ico"> 
						<link href="css/bootstrap.min.css" rel="stylesheet">
						<link href="css/bootstrap-theme.min.css" rel="stylesheet">
						<link href="css/theme.css" rel="stylesheet">
	
					</head>	
					<body role="document">

						<script type="text/javascript" src="js/jquery-3.2.1.min.js"></script> 
						<script type="text/javascript" src="js/bootstrap.min.js"></script> 
						<script type="text/javascript" src="js/jquery.tablesorter.min.js"></script>
						<script type="text/javascript" src="js/include.js"></script>
						<div class="container theme-showcase" role="main"><h3>Keine Internetverbindung zum Anmeldeserver</h3>
						<a href="index.php"><button type="button" class="btn btn-info pull-left">zurück zur Anmeldung</button></a>
						</div>
					</body>
					</html>
					');
				die;

				}

			//echo "<pre>";	
			//print_r($userdata);
			//echo "</pre>";


			if ($userdata->login == '1')
				{
					//user anmelden: Session Variablen setzen.
					$_SESSION['username'] = $userdata->sn;
					$_SESSION['loginname'] = $userdata->cn;
					$_SESSION['givenname'] = $userdata->givenname;
					//passenden Lehrer aus der Datenbank finden: Prüfung erfolgt über gleich Vor und Nachnamen ... dies muss also in ldapDB und Schild übereinstimmen
					//Daten aus der Mysql-db holen
					try 
					{
						$result = $database->query("SELECT * FROM k_lehrer WHERE sichtbar='+' AND Vorname='".$userdata->givenname ."'AND Nachname='".$userdata->sn."'");
					}
					catch (DatabaseException $e) 
					{
						echo ("Keine Datenbankabfrage möglich.");
					}
					$obj =  $database->fetchObject ($result);
					$_SESSION['krz'] = $obj->Kuerzel;
					$_SESSION['userid'] = $obj->ID;
						
				
					//TODO:
					//usergroup festsetzen ...
					//$_SESSION['groupmember'] = $obj->US_UserGroups;
					//$_SESSION['privileges'] = $obj->US_Privileges;
					//[TODO] admin abfrage mit groumembers koppeln
							
				require_once ('./includes/session_start.inc.php');
				}
			else
				{

				//********************************************

				sleep(4);
				echo
					('
					<html lang="de">
					<head>
						<title>webschild</title>
		
						<meta charset="utf-8">
						<meta http-equiv="X-UA-Compatible" content="IE=edge">
						<meta name="viewport" content="width=device-width, initial-scale=1">
						<meta name="description" content="Schulverwaltungssoftware SchILD in OpenSource gedacht">
						<meta name="author" content="root" >
	
						<link rel="icon" href="./graphics/logo.ico"> 
						<link href="css/bootstrap.min.css" rel="stylesheet">
						<link href="css/bootstrap-theme.min.css" rel="stylesheet">
						<link href="css/theme.css" rel="stylesheet">
	
					</head>	
					<body role="document">

						<script type="text/javascript" src="js/jquery-3.2.1.min.js"></script> 
						<script type="text/javascript" src="js/bootstrap.min.js"></script> 
						<script type="text/javascript" src="js/jquery.tablesorter.min.js"></script>
						<script type="text/javascript" src="js/include.js"></script>
						<div class="container theme-showcase" role="main"><h3>Benutzer unbekannt bzw. Passwort falsch!</h3>
						<a href="index.php"><button type="button" class="btn btn-info pull-left">zurück zur Anmeldung</button></a>
						</div>
					</body>
					</html>
					');
				die;

				}							
		}
	}
}



?>
<!DOCTYPE html>
<html lang="de">
<head>
	<title>webschild</title>
	
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="Schulverwaltungssoftware SchILD in OpenSource gedacht">
	<meta name="author" content="root" >

	<link rel="icon" href="./graphics/logo.ico"> 
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/bootstrap-theme.min.css" rel="stylesheet">
	<link href="css/theme.css" rel="stylesheet">
	
</head>



<body role="document">

	<script type="text/javascript" src="js/jquery-3.2.1.min.js"></script> 
	<script type="text/javascript" src="js/bootstrap.min.js"></script> 
	<script type="text/javascript" src="js/jquery.tablesorter.min.js"></script>
	<script type="text/javascript" src="js/include.js"></script>
	

	<?php 

		include ('navigator.inc.php');

		// user handling
		if ($_GET["logout"]) 
		{
			$_SESSION['sid'] = '';
			session_destroy();
			echo '<div class="container theme-showcase" role="main"><h3>Sie sind abgemeldet!</h3></div>';
			require_once('./includes/footer.php');
			die;		
		}
		elseif($_SESSION['username']) 
		{
			//hier gehts nur weiter, wenn es eine Session-Variable username gibt ... 
			//alles andere wird mit die abgebrochen
		}
		else 
		{
			echo '
				<div class="container theme-showcase" role="main">
					<h3>Willkommen bei Webschild!</h3>
					Dies ist ein Webplugin für die MySQL Datenbank von SchILD-NRW<br>
					Gnu-Public License - written by David Klein
				</div>';
			require_once('./includes/footer.php');
			die;
		};

	?>

	
	
		

	
	
			
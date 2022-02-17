


	<?php
		if (isset($_SESSION['username']))
		{
		
			if ($_SESSION['username'] == 'Admin')
			{
				$admin_dropdown_menue = 
				'
					<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">Administration<b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><a href="leistungsuebersicht.php">Leistungübersicht Lerngruppen</a></li>
								<li><a href="leistungsuebersichtadmin.php">Notenänderungen</a></li>								
								<li><a href="#">Fotoimport</a></li>
								<li><a href="einstellungen.php">Einstellungen</a></li>
							</ul>
						</li>
				';
			}


			//vorspann ...
			echo ('
				<nav class="navbar navbar-default" role="navigation">

				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
				</div>
				<a class="navbar-brand collapse navbar-collapse" href="schulinfo.php"><img height="25em" src="./graphics/logo.png" alt="Bitte logo unter .graphics/logo.png speichern"></a>
				
				<div class="container"> 
			');
				//Eingabe: Suche 
			echo ('
				<div class="col-sm-3 col-md-3">        
					<form class="navbar-form" role="search" action="index.php" method="get" target="_self">
        				<div class="input-group">
            				<input type="text" class="form-control" placeholder="Suche" name="q" autofocus>
            				<div class="input-group-btn">
                				<button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search"></i></button>
            				</div>
        				</div>
        			</form>
				</div>
				');

			//dynamische Menue-items
			echo ('    
				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<ul class="nav navbar-nav">
						<li><a href="klassen.php">Klassen</a></li>
						<li><a href="stufen.php">Stufen</a></li>
						<li><a href="kurse.php">Kurse</a></li>
						<li><a href="lehrer.php">Lehrer</a></li>
						 <li class="divider"></li>');
			if ($_SESSION['krz'])
			{				
				echo ('<li><a href="unterricht.php">mein Unterricht</a></li>');
				//echo ('<li><a href="noteneingabe.php">Noteneingabe</a></li>');

			}		
			if ($_SESSION['klassenlehrer'])
			{
				echo ('<li><a href="leistungsuebersicht.php?klasse='. $_SESSION['klassenlehrer'] .'"> '. $_SESSION['klassenlehrer'] .'</a></li>');
			}
			if ($_SESSION['stellv_klassenlehrer'])
			{
				echo ('<li><a href="leistungsuebersicht.php?klasse='. $_SESSION['stellv_klassenlehrer'] .'"> '. $_SESSION['stellv_klassenlehrer'] .'</a></li>');
			}
			
				
			echo ('		</ul>
					<ul class="nav navbar-nav navbar-right">
						' . $admin_dropdown_menue . '						
						
						<li><a href="logout.php?logout=1" title="logout" ><span class="glyphicon glyphicon-off"></span></a></li>
      				</ul>
				</div>
			');
			
		}	
		else
		{
			echo ('
				<nav class="navbar navbar-default" role="navigation">

				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
				</div>
	
				<div class="container"> 
			');



			echo ('
				<ul class="nav navbar-nav">	
					<form method="post" id="signin" class="navbar-form navbar-right" role="form" name="loginfeld" action=' . $_SERVER['PHP_SELF'] . '>

						<div class="input-group">
							<span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
							<input id="username" type="username" autofocus class="form-control" name="username" value="" placeholder="Benutzername">                                        
						</div>
						<div class="input-group">
							<span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
							<input id="password" type="password" class="form-control" name="password" value="" placeholder="Passwort">                                        
						</div>

						<button type="submit" name="login" class="btn btn-primary">Login</button>
					</form>
				</ul>
			');
		}	
	?>
	
	</div>
</nav>


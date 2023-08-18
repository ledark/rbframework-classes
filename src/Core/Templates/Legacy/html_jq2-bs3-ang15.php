<!doctype html>
<html ng-app="mainApp">
<head>
	<!--//MetaTags//-->
	<meta charset="windows-8859-1"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
	
	<title>{nomeFantasia}</title>
	

	
	<!--//Jquery//-->
	<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css"/>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>	
	
	<!--//Bootstrap3//-->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"/>
	<!--//<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css"/>	//-->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->	
	
	
	
	<!--//AngularJs//-->
	<script src="https://code.angularjs.org/1.5.8/angular.min.js"></script>
	

	
	<!--//FavIcon//-->
	<link rel="icon" type="image/x-icon" href="{httpSite}img/favicon.png" />


	<!--//Custom//-->
	<script src="{httpSite}_app/js/localDatabase.min.js"></script>
    
	
		
	<script language="javascript">
		var httpAjax = '{httpAjax}';
		var httpSite = '{httpSite}';
		var httpLink = '{httpLink}';
		var httpData = '{httpData}';
	</script>
	
	<?php $file = null; $type = 'css'; include('_app/render.php');  ?>
	<?php $file = null; $type = 'js'; include('_app/render.php');  ?>
	
</head>
<body ng-controller="Conteudo">


{body}
	

	
</body>
</html>
<?php //echo smart_replace(); ?>
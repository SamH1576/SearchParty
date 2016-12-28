<?php
/***********************************************************************/
/*  PHP file to search for events
/***********************************************************************/
//Set database connection parameters
include 'Database-config.php';

/***********************************************************************/
/*  Set exception handler
/***********************************************************************/
set_exception_handler(function($e){
	$code = $e->getCode() ?: 400;
	header("Content-Type: application/json", NULL, $code);
	echo json_encode(["error" => $e->getMessage()]);
	exit;
});

/***********************************************************************/
/*  Input validation
/***********************************************************************/

/***********************************************************************/
/*  Database functions
/***********************************************************************/

/***********************************************************************/
/*  Main
/***********************************************************************/
//handle requestions by verb and path
$verb = $_SERVER['REQUEST_METHOD'];
//$url_pieces= explode('/', $_SERVER['PATH_INFO']);

//can be searchby title, date range, venue etc
$searchby = $_POST["searchby"];

//handle the different methods of searching
switch($searchby){
	case "title":
		//handle title
		break;
	case "daterange":
		//handle daterange
		break;
	case "venue":
		//handle venue
		break;
	case "host":
		//handle host
		break;
	default:
		//handle if not specified (ie return an error)
		break;
}

//call some output function, for example returning some JSON

?>
<?php



switch ($location) {
	case "public":
		$loc = "../sharedFiles";
		break;
	case "root":
		$loc = ".";
		break;
	case "home":
		$loc = "/home";
		break;
	default:
		exit("This location doesn't exist!");
}

if (!is_dir($loc)) {
	trigger_error("Il path inserito nella variabile non è raggiungibile!", E_USER_NOTICE);
	exit("This location doesn't exist!");
}



?>

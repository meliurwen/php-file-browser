<?php



switch ($location) {
	case "sharedFiles":
		$loc = "../sharedFiles";
		break;
	case "root":
		$loc = ".";
		break;
	case "home":
		$loc = "/home";
		break;
	default:
		exit("Location inesistente!");
}

if (!is_dir($loc)) {
	trigger_error("Il path inserito nella variabile non è raggiungibile!", E_USER_NOTICE);
	exit("Location inesistente!");
}



?>

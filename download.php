<?php

    $location = $_GET["loc"];
    $fileRelativePath = urldecode($_GET["file"]);

    #Per disabilitare il login commentare la riga quì sotto
    #include "checkLogin.php";

    #In caso la riga sopra sia commentata
    if (!isset($isAuthenticated)){
        $isAuthenticated = 1;
    }

    if($isAuthenticated) {

        #Usando $location come parametro di input restituisce
        #il path assoluto delle directory attraverso il
        #parametro $loc come output
        include "folderLocations.php";

        if (substr($fileRelativePath, 0, 2) == "./") {
            $fileRelativePath = substr($fileRelativePath, 2);
        }

        // In order to be able to concatenate to the header string
        // it's necessary to escape the escape and the double and
        // the single quotation mark
        $fileName = str_replace("'", "\\'", str_replace('"', '\\"', str_replace("\\", "\\\\", basename($fileRelativePath))));

        // In order to not allow the user to get outside the shared directory
        if (strpos($fileRelativePath, '../') !== false) {
            $allowedPath = False;
        } else {
            $allowedPath = True;
        }

        $fileAbsolutePath = $loc  . "/" . $fileRelativePath;

        if(file_exists ($fileAbsolutePath) and $allowedPath){

            // Set up the download system...
            header('Content-Description: File Transfer');
            header('Content-Type: '. mime_content_type($fileAbsolutePath));
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: '.filesize($fileAbsolutePath));

            // Flush the cache
            ob_clean();
            flush();

            // Send file to browser
            readfile($fileAbsolutePath);

            // DO NOT DO ANYTHING AFTER FILE DOWNLOAD
            exit;


        } else {
            header("HTTP/1.0 404 Not Found");
            echo "Error 404 - File not found.\n";
            die();
        }

    } else{
        #Se l'utente non è loggato viene fatto il redirect alla login page
        header('Location: login.php?redirect='. urlencode(currentUrl()));
    }


?>

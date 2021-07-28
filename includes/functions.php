
<?php
die('OKKKKKKKKKKKKKKKKKKKKKKKKKKK');
function ConnectBDD($type="mysql"){
	 $host = "localhost";$db = "okapiCollegeDB"; $user = "postgres"; $password = "0kapic0llege";
	 try {
                $dsn = "pgsql:host=$host;port=5432;dbname=$db;";
                $conn = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

	} catch (PDOException $e) {
        	echo "Connection failed: " . $e->getMessage();
		die($e->getMessage());
	}
    	return $conn;
}
    /*$servername = "localhost";
    $username = "root";
    $password = NULL;
    $bd="okapiCollegeDB";
    $conn=null;
    try {
    	$dns="mysql:host=$servername;dbname=".$bd;
    	if($type=="pgsql"){
        	$password = "0kapic0llege";
        	$username = "postgres";
        	$dns = "pgsql:host=$servername;port=5432;dbname=$bd;";
    	}
    	$conn = new PDO($dns,$username,$password);
    // set the PDO error mode to exception
    	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "Connected successfully";
    } catch(PDOException $e) {
    	echo "Connection failed: " . $e->getMessage();
    }
    return $conn;
}*/
?>

<?php
if(!isset($_SESSION)) 
    { 
        session_start(); 
    } 

//$serverName = "CGOMEZ-IT"; //serverName\instanceName
//$connectionInfo = array( "Database"=>"CONSIS", "UID"=>"prueba", "PWD"=>"12345", 'CharacterSet' => 'UTF-8');
$serverName = "IT-SRV-DB03\SQLINFASA"; //serverName\instanceName
$connectionInfo = array( "Database"=>"CONSIS1512", "UID"=>"db.user", "PWD"=>"db.user@inFasa90", 'CharacterSet' => 'UTF-8');
//$serverName = "it-srv-db1\SQLINFASA"; //serverName\instanceName
//$connectionInfo = array( "Database"=>"CONSIS", "UID"=>"db.user", "PWD"=>"db.user@inFasa90", 'CharacterSet' => 'UTF-8');
$cnx = sqlsrv_connect( $serverName, $connectionInfo);
if( $cnx ) {
    
    }
else{
    $json['msj']     =dbGetErrorMsg();
    $json['success'] = false;
}
?>

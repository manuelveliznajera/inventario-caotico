<?php
session_name('app_caotico');
if(!isset($_SESSION)) 
    { 
        session_start(); 
    } 
class db {
	function loguear($usuario){
		//$sql= "exec SpInventarioCaotico 'U', '', '$usuario'";
		$squel="select top(1) CONCAT(rtrim(nombre1),' ', rtrim(apellido1)) as nombrerec,nombre as completo,fotoplanil,mailinfasa,sexo,reloj,perfil,idacceso* from DatosPersEmplea where  reloj= '$usuario'";
		global $cnx;
		$result = sqlsrv_query($cnx,$sql);
		return $result;
	}
	function login($usuario,$pin){
		$sql= "exec SpGruposPicking 'L', '$usuario', '$pin'";
		global $cnx;
		$result = sqlsrv_query($cnx,$sql);
		return $result;
	}
	
	 function getGrupoPicking($tipoop, $grupo,$reloj, $cunico,$pedido,$idcarreta,$iddetgrupo)
	{
		$sql = "EXEC SpGruposPicking '$tipoop','$grupo', '$reloj', '$cunico', '$pedido', '$idcarreta', '$iddetgrupo'";
		//echo $sql;
		global $cnx;
		$result = sqlsrv_query($cnx,$sql);
		return $result;
	}
	function getEncabezados($tipoop)
	{
		$sql = "EXEC SPINVENTARIOCAOTICO '$tipoop'";
		global $cnx;
		$result = sqlsrv_query($cnx,$sql);
		return $result;
	}
	function getEncabezadosreingresobod($tipoop,$correlativo, $codigo,$lote )
	{
		$sql = "EXEC SPREINGREBODEGA '$tipoop', '$correlativo', '$codigo', '$lote'";
		global $cnx;
		$result = sqlsrv_query($cnx,$sql);
		return $result;
	}
	function getEncabezadosgrupo($tipoop,$usuario)
	{
		$sql = "EXEC SPINVENTARIOCAOTICO '$tipoop','','$usuario'";
		global $cnx;
		$result = sqlsrv_query($cnx,$sql);
		return $result;
	}
	 function setcolocar($tipoop,$ordentarea,$responsable,$codprod){
		$sql = "EXEC SPINVENTARIOCAOTICO '$tipoop', '$ordentarea','$responsable','0','$codprod'";
		global $cnx;
		$result = sqlsrv_query($cnx,$sql);
		return $result;
	}
	function getDetalleEgreso($cunico){
		$sql = "EXEC SpInventarioCaotico 'E2',0 ,0 ,'$cunico'";
		global $cnx;
		$result = sqlsrv_query($cnx,$sql);
		return $result;
	}
	 function setdetalletarea($tipoop,$ordentarea,$reloj,$codprod){
		$sql = "EXEC SPINVENTARIOCAOTICO '$tipoop', '$ordentarea', '$reloj' ,'0','$codprod'  ";
		global $cnx;
		$result = sqlsrv_query($cnx,$sql);
		return $result;
	}
	 function GetdetalletareaHD($tipoop,$ordentarea,$reloj,$cunico,$codprod,$idcarreta,$taked,$xidpos){ // habilitar desabilitar tares de picking
	 	$sql = "EXEC SPINVENTARIOCAOTICO '$tipoop', '$ordentarea', '$reloj' ,'$cunico','$codprod',$idcarreta ,$taked ,$xidpos";		
	 	//echo $sql;
		global $cnx;
		$result = sqlsrv_query($cnx,$sql);
		return $result;
	}
	function gettotaldoc(){
		$sql="EXEC SPINVENTARIOCAOTICO 'X'";
		global $cnx;
		$result = @sqlsrv_query($cnx,$sql);
		return $result;
	}

	function getexistencias($tipoop, $codigo){
		$sql="EXEC SPINVENTARIOCAOTICO '$tipoop','','','','','','','$codigo'";
		global $cnx;
		$result = @sqlsrv_query($cnx,$sql);
		return $result;
	}
}
?>


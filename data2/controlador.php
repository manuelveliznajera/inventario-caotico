<?php
/* ------ registro de funcion de minificado html, css, js ------  */

function loadminificador($class)
{
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    include __DIR__.'/phpwee2/src/'.$class.'.php';
}

spl_autoload_register('loadminificador');

//$html = file_get_contents("http://en.wikipedia.org/wiki/Minification_%28programming%29");
//$minified =PHPWee\PHPWee::html($html);


/* ------ */
session_name('app_caotico');
if (!isset($_SESSION)) {
	session_start();
}
if (isset($_GET["action"])) {
	$_SESSION['page'] = $_GET["action"];
} 
if (!isset($_SESSION["cunicolist"])) {
	$_SESSION['cunicolist'] = "[]";
} 
if (isset($_GET["cunicolist"])) {
	$_SESSION['cunicolist'] = $_GET["cunicolist"];	
}

$_SESSION['APP']        = 'INVENTARIO_CAOTICO';
$_SESSION['empresa']    = '0001';
$_SESSION['navegador']  = $_SERVER['HTTP_USER_AGENT'];
$_SESSION['dircliente'] = $_SERVER['REMOTE_ADDR'];
if (!isset($_SESSION["usuario"])) {
	$_SESSION["vistaactual"] = 0;
}
// este bloque de condición indica a la actualización en que proceso se quedo en la ultima acción. 
if ($_GET["action"] == 0) {
	cargar_pagina($_SESSION["vistaactual"]);
} 

function cargar_pagina($opt)
{
	switch ($opt) {
		case 0:
			// selecciona usuario /Logueo 
			$_SESSION['page'] = 4;
			break;
		case 1:
			//selecciona tipo de operacion
			$_SESSION['page'] = 6;
			break;
		case 2:
			//selecciona lote a preparar
			$_SESSION['page'] = 3;
			break;
		case 3:
			//en proceso de despacho de lote
			$_SESSION['page'] = 1;
			break;
		case 4:
			$_SESSION['page'] = 14; // encabezados de egresos
			break;
		case 5:
			$_SESSION['page'] = 15; // detalle de egreso
			break;
		case 6:
			$_SESSION['page'] = 17; // encabezados de traslados
			break;
		case 7:
			$_SESSION['page'] = 18; // encabezados de traslados
			break;
		case 8:
			$_SESSION['page'] = 99; // encabezados de grupos picking
			//$_SESSION['page'] = 99; // encabezados de grupos picking
			break;	
		case 9:
			$_SESSION['page'] = 25; // detalle de seleccion facturas
			break;
		case 10:
			$_SESSION['page'] = 21; // detalle de productos a pickear  de grupos 
			break;				
		case 11:
			$_SESSION['page'] = 32; // encabezado de ordenes pendientes de packing
			break;				
		case 12:
			$_SESSION['page'] = 33; // DETALLE de ordenes pendientes  PACKING
			break;
		case 13:
			$_SESSION['page'] = 34; // Traslados  de codificado sin generar
			break;
		case 14:
			$_SESSION['page'] = 36; // tareas de etiquetado
			break;
		case 15:
			$_SESSION['page'] = 40; // Existencias
			break;
		default:
			//reinicializa todos los datos 
			break;
	} 
}
require_once 'data/conexion.php';
require_once 'modelos.php';
switch ($_SESSION['page']) {

	
	case 1: // detalle de orden a realizar proceso
		$json            = array();
		$detalle         = '';
		$libs            = new db();
		$json['msj']     = 'Sin detalle';
		$json['success'] = true;
		echo json_encode($json);
		break;
	case 2: // actualiza el ingreso a posiciones de Rack
		$json            = array();
		$detalle         = '';
		$libs            = new db();
		$json['msj']     = 'No es posible Actualizar';
		$json['success'] = false;
		$xcontador       = 0;
		if (isset($_SESSION['estado']) && isset($_SESSION['usuario'])  && isset($_POST['codprod']) ) {
			$resultado = $libs->setcolocar('I', $_POST['ordentarea'], $_SESSION['usuario'], $_POST['codprod']);
			if ($resultado == true) {
				$json['success'] = true;
				$json['msj']     = 'Ubicación Actualizada';
			} 
			else {
				$json['msj']     = dbGetErrorMsg();
				$json['success'] = false;
			}
			echo json_encode($json);
		}
		else {
			$json['msj']     = 'Vuelva a cargar tipo de operacion y Usuario';
			$json['success'] = false;
			echo json_encode($json);
		}
		break;
	case 3: // encabezado de ingresos a bodega
		$json            = array();
		$detalle         = '';
		$libs            = new db();
		$json['msj']     = 'Ordenes Pendientes';
		$json['success'] = true;
		if (isset($_POST['tipoop'])) {$_SESSION['estado'] = $_POST['tipoop']; } 
		if (isset($_POST['descripcion'])) { $_SESSION['proceso'] = $_POST['descripcion'];} 
		if (isset($_POST['tipomov'])) {$_SESSION['tipomov'] = $_POST['tipomov'];} 
		$xcontador = 0;
		$resultado = $libs->getEncabezados($_SESSION['estado']);
		if ($resultado == true) {
			$json['msj']             = 'Datos generados';
			$_SESSION["vistaactual"] = 2;
			if (!sqlsrv_has_rows($resultado)) {
				$encabezado = "<h3>&ldquo;Sin Ingresos pendientes&rdquo; </h3>";
			} 
			else {
				$encabezado = "<h3>" . $_SESSION['proceso'] . "</h3>";
				while ($obj = sqlsrv_fetch_object($resultado)) {
					$refingreso     = $obj->refingreso;
					$codigo         = $obj->codigo;
					$lote           = trim($obj->lote);
					$fecha          = $obj->fecha;
					$OrdenTarea     = ($obj->OrdenTarea);
					$cantidad       = $obj->cantidad;
					$habilitado     = $obj->habilitado;
					$idposestante   = trim($obj->idposestante);
					$cantcajas      = $obj->cantcajas;
					$VENCE          = $obj->VENCE;
					$nombre         = trim($obj->nombre);
					$Posicion       = trim($obj->Posicion);
					$xcontador      = $xcontador + 1;
					$claseprioridad = '';
					$xdetalle2      = '';
					$xdetalle       = '
						<div  class="col-md-12   btncargartarea"  idposestante="' . $idposestante . '"refingreso="' . $refingreso . '" ordentarea= "' . $OrdenTarea . '" id= "' . $OrdenTarea . '" codprod= "' . $codigo . '">
					      <table class="appfull tareaTI">
					         <tbody>
					            <tr >
					              <td class="txtizq"><b>Lote:</b> ' . $lote . ' </td>
					              <td class="txtizq"><b>Vence:</b> ' . $VENCE . '</td>
					            </tr>
					            <tr  >
					              <td class="txtizq">Cajas: <b class="txtcantidad">' . $cantcajas . '</b></td>
					              <td class="txtizq">Cantidad: <b class="txtcantidad">' . $cantidad . '</b></td>
					            </tr>
					            <tr >
					               <td colspan="2"  class="txtizq">[ ' . $codigo . ' ]. ' . $nombre . '.</td>
					            </tr>
					             <tr >
					               <td  class="txtizq"><h10><b>Desde:</b> </h10> <h6>' . $fecha . '</h6></td>
					               <td class="txtder"><b>Posición: </b><h3> ' . $Posicion . ' </h3></td>
					            </tr>
					         </tbody>
					      </table>
					      <br>
					   </div>';
					$detalle        = $detalle . ' ' . $xdetalle . $xdetalle2;
				}
			}
			$pietab          = '
				<script type="text/javascript">
					$(document).ready(function(){ $("#menug").load("menu.php"); 
					formatearnumeros();
					});
				</script>	
				 <script type="text/javascript">
				 	$(document).ready(function() {
					 	$(".botonF1").hover(function(){
						  $(".btn2").addClass("animacionVer");
						})
					});
				 </script>';
			//$json['detalle'] = $encabezado . ' ' . $detalle . ' ' . $pietab . ' ' . initws();
			$json['detalle'] = minificado($encabezado . ' ' . $detalle . ' ' . $pietab . ' ' . initws());
		} 
		else {
			$json['msj']     = dbGetErrorMsg();
			$json['success'] = false;
		}

		echo json_encode($json);
		break;
	case 4: //formulario de logueo 
		$json            = array();
		$json['success'] = true;
		$json['msj']     = 'Ingrese/Escanee su código de usuario';
		$json['detalle'] = '
		<div class="card col-12" style="align-items: center;" >
		  <img class="card-img-top" src="img\0001.png" alt="Card image cap" style="MAX-WIDTH: 45%;">
		  <div class="card-body">
		    <h5 class="card-title">Ingrese/Escanee su código de Usuario</h5>
		    <p class="card-text">
		     <div class="form-group">
				<label for="idusuarioinicia">Inicio de Sesión</label>
				<input type="number" class="form-control" id="idusuarioinicia" aria-describedby="codigoHelp" placeholder="Código" style="font-size: x-large";>
				<br>
				<small id="codigoHelp" class="form-text text-muted"> </small>
			</div>
		    <a href="#" class="btn btn-primary btningresar" id="btnidusuarioinicia">Ingresar</a>
		  </div>
		</div>
		<script type="text/javascript">
			$(document).ready(function() {
				$("#idusuarioinicia" ).focus();
			});
		</script>';
		echo json_encode($json);
		break;
	case 5: // verificacin de login
		$json            = array();
		$detalle         = '';
		$libs            = new db();
		$json['msj']     = 'No existe el usuario';
		$json['success'] = false;
		$resultado       = $libs->loguear($_POST['xusuario']);
		if ($resultado == true) {
			if (sqlsrv_has_rows($resultado)) {
				$json['success'] = true;
				//  para protección: CSRF significa Cross Site Request 
				// [Forgery o Falsificación de Petición en Sitios Cruzados]
				$_SESSION['dircliente'] = $_SERVER['REMOTE_ADDR']; 

				$_SESSION["vistaactual"] = 1;
				$_SESSION["usuario"]     = $_POST['xusuario'];
				while ($obj = sqlsrv_fetch_object($resultado)) {
					$nombrerec                 = $obj->nombrerec;
					$_SESSION['nombreusuario'] = $nombrerec;
					$fotoplanil                = $obj->fotoplanil;
					$mail                      = $obj->mailinfasa;
					$perfil                    = $obj->perfil;
					$idacceso                  = $obj->idacceso;
					
					$_SESSION['perfil'] = $perfil; 
					$_SESSION["idacceso"] = $idacceso;
					$sexo                      = $obj->sexo;
					$json['msj']               = 'Bienvenido ' . $nombrerec;
					$json['detalle']           = cargaropciones();
				} 
			} 
		} 
		else {
			$json['success'] = false;
			$json['msj']     = 'No existe el usuario';
		}
		echo json_encode($json);
		break;
	case 6: // carga menu principal, verificar si aún esta en uso.
		$json            = array();
		$json['success'] = true;
		$json['msj']     = 'Seleccione el tipo de operación ';
		$json['detalle'] = cargaropciones();
		echo json_encode($json);
		break;
	case 7: // cerrar sesión
		$json['msj']     = 'Finalizada';
		$json['success'] = true;
		session_destroy();
		echo json_encode($json);
		break;
	case 8: // opción controladora de ir hacia atras
		$json['msj']     = 'Finalizada';
		$json['success'] = true;
		if ($_POST['optmenu'] == 'mnseleop') {
			// SI TIPO DE VISTA = 9 // DETALLE DE GRUPO. ENTONCES REGRESA A VISTA DE GRUPOS
			switch ($_SESSION["vistaactual"]) {
				case 9:
					$_SESSION['estado']='E0';
					$_SESSION["vistaactual"] = 8;
					break;
				case 10:
					$_SESSION["proceso"]='Egresos de bodega';
					$_SESSION["vistaactual"] = 9;
					//$_SESSION['estado']='E1';
					break;
				case 12:
					$_SESSION["proceso"]='Packing';
					$_SESSION["vistaactual"] = 11;
					$_SESSION['estado']='PK';
					break;
				default:
					$_SESSION["vistaactual"] = 1;
					break;
			}
			$_SESSION['cunicolist'] ='[]';
		} 
		if ($_POST['optmenu'] == 'mnselelote') {
			$_SESSION["vistaactual"] = 1;
		}
		if ($_POST['optmenu'] == 'mnfullscreen') {
			$json['msj'] = 'openFullscreen();';
		}
		if ($_POST['optmenu'] == 'mnsalir') {
			session_destroy();
		}
		echo json_encode($json);
		break;
	case 9: // opcion obsoleta
		$json['msj']     = 'opción obsoleta';
		$json['success'] = false;
		echo json_encode($json);
		break;
	case 10: // funcion obsoleta
		$json['msj']     = 'opción obsoleta';
		$json['success'] = false;
		echo json_encode($json);
		break;
	case 11: // salir a menú principal
		$json['msj']     = 'Finalizada';
		$json['success'] = true;
		Cargar_pagina(6);
		$_SESSION["vistaactual"]   = 1;
		echo json_encode($json);
		break;
	case 12: //opción de # de documentos pendients
		$libs            = new db();
		$json['msj']     = 'Finalizada';
		$json['success'] = false;
		$resultado       = $libs->gettotaldoc();
		if ($resultado == true) {
			while ($obj = sqlsrv_fetch_object($resultado)) {
				$cantingresos          = $obj->INGRESOS;
				$cantdespachos         = $obj->EGRESOS;
				$canttraslados         = $obj->TRASLADOS;
				$json['cantingresos']  = $cantingresos;
				$json['cantdespachos'] = $cantdespachos;
				$json['canttraslados'] = $canttraslados;
				if ($cantingresos + $cantdespachos + $canttraslados >= 1) {
					$json['success'] = true;
					$json['msj']     = 'Existen ordenes pendientes';
				}
				else {
					$json['success'] = false;
				}
			}
		}
		echo json_encode($json);
		break;
	case 13:// función 
		$libs      = new db() ;
		$resultado = $libs->consulta_alarma();
		if ($resultado == true) {
			while ($obj = sqlsrv_fetch_object($resultado)) {
				$alarma = $obj->alarma;
				echo $alarma;
			}
		}
		session_destroy();
		break;
	case 15: // DETALLE de ordenes pendientes  E2 EGRESOS DE BODEGA
		$json            = array();
		$detalle         = '';
		$libs            = new db();
		$json['msj']     = 'Detalle de Salida';
		$json['success'] = true;
		if (isset($_POST['descripcion'])) { $_SESSION['proceso'] = $_POST['descripcion'];} 
		if (isset($_POST['clasempresa'])) { $_SESSION['clasempresa'] = $_POST['clasempresa'];} 
		if (isset($_POST['cunico'])) { 		$_SESSION['cunico'] = $_POST['cunico'];}
		$xcontador = 0;
		$resultado = $libs->getDetalleEgreso($_SESSION['cunico']);
		if ($resultado == true) {
			$json['msj']             = 'Datos generados';
			$_SESSION["vistaactual"] = 5;
			$encabezado              = "<h3 CLASS= txt" . $_SESSION['clasempresa'] . ">" . $_SESSION['proceso'] . "</h3>";
			$tipoproductoA           = '';
			while ($obj = sqlsrv_fetch_object($resultado)) {
				$refingreso     = trim($obj->refingreso);
				$codigo         = trim($obj->codigo);
				$lote           = trim($obj->lote);
				$fecha          = $obj->fecha;
				$OrdenTarea     = $obj->OrdenTarea;
				$cantidad       = $obj->cantidad;
				$habilitado     = $obj->habilitado;
				$idposestante   = $obj->idposestante;
				$vence          = $obj->VENCE;
				$nombre         = $obj->nombre;
				$posicion       = $obj->Posicion;
				$preciopub      = $obj->preciopub;
				$xcontador      = $xcontador + 1;
				$PICKER			= $obj->PICKER;
				$classpicker     = '';
				if ($PICKER == 1) { $classpicker     = 'pickeado';	} 
				else{ $classpicker     = ''; }
				/************* separador************/
				$separador      = '';
				$claseprioridad = '';
				$tipoproducto   = "Liquido";
				// si tipo producto cambia su valor, se genera una división <hr>
				if ($tipoproducto != $tipoproductoA) {
					$tipoproductoA = $tipoproducto;
					$separador     = '<hr class= "separador hr' . $_SESSION['clasempresa'] . '" tipoproducto=' . $tipoproducto . '>';
				} 
				$claseprioridad = '';
				$xdetalle2      = '';
				$xdetalle       = $separador . '
				  	<div class="btncargartarea_detalle container card mb-3  margen15 semitransparente '.$classpicker.'" ordentarea= "' . $OrdenTarea . '" cunico="' . $refingreso . '" id="' . $OrdenTarea . '" codprod= "' . $codigo . '" PICKER= "' . $PICKER . '" >
				      <div class="row " >
				        <div class="col">
				          <div class=" txtizq"><h5 class="text-info">L: ' . $lote . '</h5></div>
				        </div>
				        <div class="col">
				          <div class=" txtder"><h5 class="text-primary">V: ' . $vence . '</h5></div>
				        </div>
				      </div>
				      <div class="row">
				        <div class="col txtizq">
				          <h6><span class="text-mutedx">Producto :</span></h6>
				        </div>
				        <div class="col txtder">
				          <h6> [' . $codigo . ']</h6>
				        </div>
				      </div>
				      <div class="row">
				        <div class="col txtizq">
				          <h4> ' . $nombre . '</h4>
				        </div>
				      </div>
				      <div class="row">
				        <div class="col txtizq">
				          <h5><span class="text-mutedx">Cantidad: </span>' . $cantidad . ' </h5>
				        </div>
				        <div class="col txtder">
				          <h5><span class="text-mutedx">Q. </span>' . $preciopub . '</h5>
				        </div>
				      </div>
				    </div>';
				$detalle        = $detalle . ' ' . $xdetalle . $xdetalle2;
			} 
			$pietab          = '
				<button type="button" class="btn btn-primary btn-lg btn-block optgrande  " id="btn_enviar_detalle" descripcion="Enviar detalle">
					<span class="fas fa-paper-plane"></span> Grabar pedido 
				</button>
				<script type="text/javascript">
					$(document).ready(function(){ $("#menug").load("menu.php"); });
				</script>	
				 <script type="text/javascript">
				 	$(document).ready(function() {
					 	$("body").removeClass("bgINFASA");
	                    $("body").removeClass("bgDIGELASA");
	                    $("body").addClass("bg' . $_SESSION['clasempresa'] . '");
					 	$(".botonF1").hover(function(){
						  $(".btn2").addClass("animacionVer");
						});
						$(window).scrollTop(0);
					});
				 </script>';
			//$json['detalle'] = $encabezado . ' ' . $detalle . '' . $pietab . ' ' . initws();
			$json['detalle'] = minificado($encabezado . ' ' . $detalle . '' . $pietab . ' ' . initws());
				  
		} 
		else {
			$json['msj']     = dbGetErrorMsg();
			$json['success'] = false;
		}
		echo json_encode($json);
		break;
	case 16: // envio de detalle de tarea
		$json            = array();
		$detalle         = '';
		$libs            = new db();
		$json['msj']     = 'No es posible enviar la tarea';
		$json['success'] = false;
		if (isset( $_POST['ordentarea']) && isset($_SESSION['usuario'])  && isset($_POST['codprod']) ) {
			$resultado       = $libs->setdetalletarea('E4', $_POST['ordentarea'], $_SESSION['usuario'], $_POST['codprod']);
			if ($resultado) {
				$json['success'] = true;
				$json['msj']     = 'Tarea completada correctamente2 ';
			} 
			else {
				$json['msj']     = dbGetErrorMsg();
				$json['success'] = false;
			}
		} 
		else {
			$json['msj']     = 'No es posible actualizar,Intente de nuevo o actualice esta página ';
			$json['success'] = false;
			echo json_encode($json);
		}
		echo json_encode($json);
		break;
	case 17: // encabezado de ordenes pendientes de proceso E1 EGRESOS DE BODEGA
		$json            = array();
		$detalle         = '';
		$libs            = new db();
		$json['msj']     = 'Ordenes Pendientes';
		$json['success'] = true;
		if (isset($_POST['tipoop'])) { 	$_SESSION['estado'] = $_POST['tipoop']; } 
		if (isset($_POST['descripcion'])) { $_SESSION['proceso'] = $_POST['descripcion'];}
		if (isset($_POST['tipomov'])) { $_SESSION['tipomov'] = $_POST['tipomov'];}
		$xcontador = 0;
		$resultado = $libs->getEncabezados($_SESSION['estado']);
		if ($resultado) {
			if (!sqlsrv_has_rows($resultado)) {
				$encabezado              = '<h3 class= "padre">&ldquo;Sin traslados pendientes&rdquo; </h3>';
				$_SESSION["vistaactual"] = 6;
			} 
			else {
				$json['msj']             = 'Datos generados';
				$_SESSION["vistaactual"] = 6;
				$encabezado              = '<h3 class= "padre">' . $_SESSION['proceso'] . '</h3>';
				$tareaingreso            = 0;
				$tareaegreso             = 0;
				$tareaingreso_pos        = '';
				$tareaegreso_pos         = '';
				$idposestante            = '';
				$idposestante2           = '';
				$paso                    = 0;
				$existencia 			 = 0;
				while ($obj = sqlsrv_fetch_object($resultado)) {
					$NOMOBJETO  	  = trim($obj->NOMOBJETO);
					$refsalida  	  = $obj->refsalida;
					$EJECUTAIND 	  = $obj->EJECUTAIND;
					$existencia		  = $obj->Existencia;
					$cantidad         = $obj->cantidad;
					if ($EJECUTAIND != 0) {
						$desabilitado = "desabilitado";
						$paso         = $paso + 1;
					} 
					else { 	$desabilitado = "";	}
					
					if ($refsalida != 0) {
						$tareaegreso     = $refsalida;
						$tareaegreso_pos = $NOMOBJETO;
						$tipomov         = "E";
						$idposestante    = $obj->idposestante;
						$OrdenTarea      = $obj->OrdenTarea;
						$lineaexis='<h6><span class="text-mutedx">Existencia: </span>' . $existencia . ' </h6>';
						if ($existencia==$cantidad ){	$resaltar="clprio";	}
						else { 	$resaltar="";}
					} 
					$refingreso = $obj->refingreso;
					if ($refingreso != 0) {
						$tareaingreso     = $refingreso;
						$tareaingreso_pos = $NOMOBJETO;
						$idposestante2    = $obj->idposestante;
						$OrdenTarea2      = $obj->OrdenTarea;
						$tipomov          = "I";
						$resaltar="";
						$lineaexis="";
					} //$refingreso != 0

					
					
					$codigo           = $obj->codigo;
					$lote             = trim($obj->lote);
					$fecha            = $obj->fecha;
					$ordentareacomodin = $obj->OrdenTarea;
					
					$habilitado       = $obj->habilitado;
					$IDTRASLADO       = $obj->IDTRASLADO;
					$TARSALI          = $obj->TARSALI;
					$TARINGRE         = $obj->TARINGRE;
					$VENCE            = $obj->VENCE;
					$nombre           = trim($obj->nombre);
					$xdetalle2        = '';
					// construir los dos detalles (salida y entrada)
					$xdetalle         = '
				  	<div class="btndetalle_traslado container card mb-3  margen15 semitransparente hijo hijo' . $IDTRASLADO . ' hijo' . $IDTRASLADO . $tipomov . ' ' . $desabilitado . ' '.$resaltar.'" IDTRASLADO="' . $IDTRASLADO . '" ordentarea= "' . $ordentareacomodin . '" refsalida="' . $refsalida . '" refingreso="' . $refingreso . '" id="' . $ordentareacomodin . '" paso=' . $paso . ' tipomov= ' . $tipomov . ' codprod="' . $codigo . '" >
				      <div class="row " >
				        <div class="col">
				          <div class=" txtizq"><h5 class="text-info">L: ' . $lote . '</h5></div>
				        </div>
				        <div class="col">
				          <div class=" txtder"><h5 class="text-primary">V: ' . $VENCE . '</h5></div>
				        </div>
				      </div>
				      <div class="row">
				        <div class="col txtizq">
				          <h6><span class="text-mutedx">Producto :</span></h6>
				        </div>
				        
				        <div class="col txtder">
				          <h6> [' . $codigo . ']</h6>
				        </div>
				      </div>
				      <div class="row">
				        <div class="col txtizq">
				          <h4> ' . $nombre . '</h4>
				        </div>
				      </div>
				      <div class="row">
				        <div class="col txtizq">
				          <h5><span class="text-mutedx">Cantidad: </span>' . $cantidad . ' </h5>
				          '.$lineaexis.'
				        </div>
				        <div class="col txtder">';
					if ($refsalida != 0)
						$xdetalle = $xdetalle . '<h5><span class="text-mutedx">Extraer de: </span> ' . $NOMOBJETO . '</h5>';
					if ($refingreso != 0)
						$xdetalle = $xdetalle . '<h5><span class="text-mutedx">Almacenar en: </span> ' . $NOMOBJETO . ' </h5>';
					$xdetalle = $xdetalle . '
				        </div>
				      </div>
				    </div>';
					// cuando ingreso y egreso esten formandos, incluir  el nuevo registro
					if ($tareaingreso != 0 && $tareaegreso != 0) {
						$encabezado       = $encabezado . '
					  	<div class="btncargartareatraslado_detalle container card mb-3  margen15 semitransparente padre padre' . $IDTRASLADO . '" IDTRASLADO="' . $IDTRASLADO . '" ordentarea= "' . $OrdenTarea . '" refsalida="' . $tareaegreso . '" refingreso="' . $tareaingreso . '" id="' . $OrdenTarea . '" idposestante= "' . $idposestante . '" idposestante2="' . $idposestante2 . '" paso=' . $paso . ' ordentarea="' . $OrdenTarea . '" ordentarea2="' . $OrdenTarea2 . '" >
					      <div class="row " >
					        <div class="col">
					          <div class=" txtizq"><h5 class="text-info">L: ' . $lote . '</h5></div>
					        </div>
					        <div class="col">
					          <div class=" txtder"><h5 class="text-primary">V: ' . $VENCE . '</h5></div>
					        </div>
					      </div>
					      <div class="row">
					        <div class="col txtizq">
					          <h6><span class="text-mutedx">Producto :</span></h6>
					        </div>
					        
					        <div class="col txtder">
					          <h6> [' . $codigo . ']</h6>
					        </div>
					      </div>
					      <div class="row">
					        <div class="col txtizq">
					          <h4> ' . $nombre . '</h4>
					        </div>
					      </div>
					      <div class="row">
					        <div class="col txtizq">
					          <h5><span class="text-mutedx">Cantidad: </span>' . $cantidad . ' </h5>
					        </div>
					        <div class="col txtder">
					        <h5><span class="text-mutedx">De: </span> ' . $tareaegreso_pos . ' <span class="text-mutedx">&rArr; </span>' . $tareaingreso_pos . '</h5>
					        </div>
					      </div>
					    </div>';
						$tareaingreso     = 0;
						$tareaegreso      = 0;
						$tareaingreso_pos = '';
						$tareaegreso_pos  = '';
						$paso             = 0;
					} 
					$detalle = $detalle . ' ' . $xdetalle . $xdetalle2;
				} 
			}
			$pietab             = '
				 <script type="text/javascript">
				 	$(document).ready(function() {
				 		$("#menug").load("menu.php");
					 	$(".botonF1").hover(function(){
						  $(".btn2").addClass("animacionVer");
						});
						$(window).scrollTop(0);
					});
				 </script>';
			//$json['detalle']    = $encabezado . ' ' . $pietab . ' ' . initws();
			//$json['encabezado'] = $detalle;
			$json['detalle']    = minificado($encabezado . ' ' . $pietab . ' ' . initws());
			$json['encabezado'] = minificado($detalle);
				 
		} //$resultado
		else {
			//$json['msj']     = ucfirst(strtolower(utf8_encode(mssql_get_last_message())));
			$json['msj']     = dbGetErrorMsg();
			$json['success'] = false;
		}
		echo json_encode($json);
		break;
	case 18: // encabezado de TARIMAS PENDIENTES DE ARMAR
		$json            = array();
		$detalle         = '';
		$libs            = new db();
		$json['msj']     = 'Tarimas pendientes de armar';
		$json['success'] = false;
		if (isset($_POST['tipoop'])) {$_SESSION['estado'] = $_POST['tipoop']; }
		if (isset($_POST['descripcion'])) {$_SESSION['proceso'] = $_POST['descripcion'];}
		if (isset($_POST['tipomov'])) {$_SESSION['tipomov'] = $_POST['tipomov'];}
		$xcontador = 0;
		$resultado = $libs->getEncabezados($_SESSION['estado']);
		if ($resultado == true) {
			$json['success'] = true;
			$json['msj']             = 'Datos generados';
			$_SESSION["vistaactual"] = 7;
			if (!sqlsrv_has_rows($resultado)) {
				$encabezado = "<h3>&ldquo;Sin Tarimas pendientes&rdquo; </h3>";
			}
			else {
				$encabezado = "<h3>" . $_SESSION['proceso'] . "</h3>";
				while ($obj = sqlsrv_fetch_object($resultado)) {
					$refingreso     = $obj->refingreso;
					$codigo         = $obj->codigo;
					$lote           = trim($obj->lote);
					$fecha          = $obj->fecha;
					$OrdenTarea     = ($obj->OrdenTarea);
					$cantidad       = $obj->cantidad;
					$habilitado     = $obj->habilitado;
					$idposestante   = trim($obj->idposestante);
					$cantcajas      = $obj->cantcajas;
					$VENCE          = $obj->VENCE;
					$nombre         = trim($obj->nombre);
					$Posicion       = trim($obj->Posicion);
					$xcontador      = $xcontador + 1;
					$claseprioridad = '';
					$xdetalle2      = '';
					$xdetalle       = '
						<div  class="col-md-12   btncargartarea_tarima"  idposestante="' . $idposestante . '"refingreso="' . $refingreso . '" ordentarea= "' . $OrdenTarea . '" id= "' . $OrdenTarea . '" codprod= "' . $codigo . '">
					      <table class="appfull tareaTA">
					         <tbody>
					            <tr >
					              <td class="txtizq"><b>Lote:</b> ' . $lote . ' </td>
					              <td class="txtizq"><b>Vence:</b> ' . $VENCE . '</td>
					            </tr>
					            <tr  >
					              <td class="txtizq">Cajas: <b class="txtcantidad">' . $cantcajas . '</b></td>
					              <td class="txtizq">Cantidad: <b class="txtcantidad">' . $cantidad . '</b></td>
					            </tr>
					            <tr >
					               <td colspan="2"  class="txtizq">[ ' . $codigo . ' ]. ' . $nombre . '.</td>
					            </tr>
					             <tr >
					               <td  class="txtizq"><h10><b>Desde:</b> </h10> <h6>' . $fecha . '</h6></td>
					               <td class="txtder"><b>Etiquetar Posición: </b><h3><i class="fas fa-tag"></i> ' . $Posicion . ' </h3></td>
					            </tr>
					         </tbody>
					      </table>
					      
					   </div><br/>';
					$detalle        = $detalle . ' ' . $xdetalle . $xdetalle2;
				}
			}
			$pietab          = '
				<script type="text/javascript">
					$(document).ready(function(){ $("#menug").load("menu.php"); 
					formatearnumeros();
					});
				</script>	
				 <script type="text/javascript">
				 	$(document).ready(function() {
					 	$(".botonF1").hover(function(){
						  $(".btn2").addClass("animacionVer");
						})
					});
				 </script>';
			//$json['detalle'] = $encabezado . ' ' . $detalle . ' ' . $pietab . ' ' . initws();
			$json['detalle'] = minificado($encabezado . ' ' . $detalle . ' ' . $pietab . ' ' . initws());
		} 
		else {
			$json['msj']     = dbGetErrorMsg();
			$json['success'] = false;
		}
		echo json_encode($json);
		break;
	case 19: // envio de detalle de tarea
		$json            = array();
		$detalle         = '';
		$libs            = new db();
		$json['msj']     = 'No es posible enviar la tarea';
		$json['success'] = false;
		$resultado       = $libs->setdetalletarea('TL', $_POST['ordentarea'], $_SESSION['usuario'], $_POST['codprod']);
		if ($resultado) {
			$json['success'] = true;
			$json['msj']     = 'Tarima completada';
		} 
		else {
			$json['msj']     = dbGetErrorMsg();
			$json['success'] = false;
		}
		echo json_encode($json);
		break;
	case 20: // envio de detalle de tarea
		$json            = array();
		$detalle         = '';
		$libs            = new db();
		$json['msj']     = 'No es posible enviar la tarea';
		$json['success'] = false;
		if (isset( $_POST['ordentarea']) && isset($_SESSION['usuario']) && isset($_POST['codprod']) ) {
			if ($_POST['codprod'] !=0 ) {
				$resultado       = $libs->GetdetalletareaHD('E3', $_POST['ordentarea'], $_SESSION['usuario'], $_POST['codprod']);
				$json['deshabilitar'] = true;
			}
			else {
			 	$resultado       = $libs->GetdetalletareaHD('E3', $_POST['ordentarea'], $_SESSION['usuario'], 0);
			 	$json['deshabilitar'] = false;
			 }
			if ($resultado) {
				$json['success'] = true;
				$json['msj']     = 'Tarea completada correctamente2 ';
			}
			else {
				$json['msj']     = dbGetErrorMsg();
				$json['msj2']     = 'Error en la consulta';
				$json['success'] = false;
			}
		}
		else {
			$json['msj']     = 'No es posible actualizar,Intente de nuevo o actualice esta página ';
			$json['success'] = false;
			echo json_encode($json);
		}
		echo json_encode($json);
		break;
	case 21: // DETALLE de ordenes pendientes  E2 EGRESOS DE BODEGA
		$json            = array();
		$detalle         = ''; 
		$detallepadre	 = '';
		$cantidadtotal	 = 0;
		$xdetallepadre      = '';
		$libs            = new db();
		$json['msj']     = 'Detalle de Salida';
		$json['success'] = true;	
		$_SESSION['clasempresa']='INFASA';
		$xcontador = 0;
		$resultado = $libs->getGrupoPicking('E1', $_SESSION['grupopicking'],'','','','','');
		if ($resultado == true) {
			$json['msj']             = 'Datos generados';
			$_SESSION["vistaactual"]=10;
			$encabezado              = "<h3 CLASS= txt" . $_SESSION['clasempresa'] . ">Grupo #  ". $_SESSION['grupopicking']." </h3>";
			$tipoproductoA           = '';
			$loteA					 = '';
			$codigoA				 = '';
			while ($obj = sqlsrv_fetch_object($resultado)) {
				$cunico     = trim($obj->cunico);
				$pedido     = trim($obj->pedido);
				$codigo         = trim($obj->codigo);
				$lote           = trim($obj->lote);
				if ($lote != $loteA) {
					$cantidadtotal= 0; 
				} 
				$fecha          = $obj->fecha;
				$OrdenTarea     = $obj->OrdenTarea;
				$cantidad       = $obj->cantidad;
				$cantidadtotal	= $cantidadtotal + $cantidad;
				$habilitado     = $obj->habilitado;
				$idposestante   = $obj->idposestante;
				$vence          = $obj->Vence;
				$nombre         = trim($obj->nombre);
				$Posicion       = trim($obj->Posicion);
				$preciopub      = $obj->preciopub;
				$taked			= $obj->taked;
				$idCarreta		= $obj->idCarreta;
				$idAgrupa		= $obj->idAgrupa;
				$acceso			= $obj->acceso;
				$Area			= $obj->Area;
				$tipoproducto	= $obj->formulaf;
				$ordenCarreta	= $obj->ordenCarreta;
				$Contenedor		= $obj->Contenedor;
				$Posicioncomp =trim($obj->Area).' '. trim($obj->Posicion);
				$xcontador      = $xcontador + 1;
				///************* DESABILITANDO LINEAS DE PEDIDO POR PICKER************
				$classpicker     = '';
				if ($taked == 1) {
					$classpicker     = 'pickeado';	
				} 
				else
				{
					$classpicker     = '';
				}
				//************* separador************
				$separador      = '';
				$claseprioridad = '';
				// si tipo producto cambia su valor, se genera una división <hr>
				if ($tipoproducto != $tipoproductoA) {
					$tipoproductoA = $tipoproducto;
					$separador     = '<hr class= "separador  hr' . $_SESSION['clasempresa'] . ' contenedorsep"  tipoproducto=' . $tipoproducto . '>';
					$xdetallepadre = $xdetallepadre . $separador;
				} 
				$claseprioridad = '';
				/*------------------------------------*/
				// si se produce cambio, se agrega el lote anterior 
				if ($lote != $loteA or $codigo!= $codigoA) {
					$loteA = trim($lote);
					$codigoA=$codigo;
					$detallepadre        = $detallepadre . ' ' . $xdetallepadre;
				} 
				$xdetallepadre       =  '
				  	<div class="btncargar_grupos container card mb-3  margen15 semitransparente '.$classpicker.' padre'.trim($lote).trim($codigo).'" ordentarea= "' . $OrdenTarea . '" cunico="' . $cunico . '" id="' . $OrdenTarea . '" codprod= "' . $codigo . '" taked= "' . $taked . '" lote= "'.$lote.'" idposestante="'.$idposestante.'">
				      <div class="row " >
				        <div class="col">
				          <div class=" txtizq"><h5 class="text-info"> <span class="fas fa-search"></span>' . $Posicioncomp . '</h5></div>
				        </div>
				        <div class="col">
				          <div class=" txtder"><h5 class="text-primary">V: ' . $vence . '</h5></div>
				        </div>
				      </div>
				      <div class="row">
				        <div class="col txtizq">
				          <h6><span class="text-mutedx">Producto :</span></h6>
				        </div>
				        <div class="col txtder">
				          <h6> [' . $codigo . ']</h6>
				        </div>
				      </div>
				      <div class="row">
				        <div class="col txtizq">
				          <span class="badge badge-dark" style= "font-size: 1.5em;font-weight:600;">' . $cantidadtotal . '</span> <span style= "font-size: 1.5em;font-weight:300;"> ' . $nombre . '</span>
				        </div>
				      </div>
				      <div class="row">
				        <div class="col txtizq">
				          <h5><span class="text-mutedx"> </span>' . '' . ' </h5>
				        </div>
				        <div class="col txtder">
				          <h5><span class="text-mutedx">Q. </span>' . $preciopub . '</h5>
				        </div>
				        <div class="col txtder">
				          <h5><span class="text-mutedx "> </span>L:' . $lote . '</h5>
				        </div>
				      </div>
				    </div>';				
				$xdetalle       = '
				  	<div class="container card mb-3  margen15 semitransparente '.$classpicker.' hijo'.trim($lote).trim($codigo).'" ordentarea= "' . $OrdenTarea . '" cunico="' . $cunico . '" id="' . $OrdenTarea . '" codprod= "' . $codigo . '" taked= "' . $taked . '" carreta= "'. $ordenCarreta.'" posicion= "'.$idposestante.'"" >
				      
				      <div class="row">
				        
				        <div class="col txtizq">
				          <h6><span class="text-mutedx">Cantidad:</span></h6>
				        </div>
				        <div class="col txtizq">
				          <h6><span class="text-mutedx">Guardar en: </span></h6>
				        </div>
				      </div>
				      <div class="row">
				        
				        <div class="col txtizq">
				          <h4>' . $cantidad . ' </h4>
				        </div>
				        <div class="col txtizq">
				          <h4> ' . $Contenedor .' '.  $idCarreta.'</h4>
				        </div>
				      </div>
				      <div class="row">
				      	
				      </div>
				    </div>';
				$detalle        = $detalle . ' ' . $xdetalle ;
			} //$obj = sqlsrv_fetch_object($resultado)
			$pietab          = '
				<button type="button" class="btn btn-primary btn-lg btn-block optgrande  " id="btn_finalizargrupo" descripcion="Enviar detalle">
					<span class="fas fa-paper-plane"></span> Finalizar Grupo 
				</button>
				<script type="text/javascript">
					$(document).ready(function(){ $("#menug").load("menu.php"); });
				</script>	
				 <script type="text/javascript">
				 	$(document).ready(function() {
					 	$("body").removeClass("bgINFASA");
	                    $("body").removeClass("bgDIGELASA");
	                    $("body").addClass("bg' . $_SESSION['clasempresa'] . '");
					 	$(".botonF1").hover(function(){
						  $(".btn2").addClass("animacionVer");
						});
						$(window).scrollTop(0);
					});
				 </script>
				 ';
			$detallepadre        = $detallepadre . ' ' . $xdetallepadre;
			//$json['detalle'] = $encabezado . ' ' . $detalle . '' . $pietab . ' ' . initws();
			$oculto= '<div class= "oculto">';
			//$json['detalle'] = $encabezado .' ' .$detallepadre. ' '.$oculto . $detalle . '</div>'. ' ' . $pietab . ' ' . initws();
			$json['detalle'] = minificado($encabezado .' ' .$detallepadre. ' '.$oculto . $detalle . '</div>'. ' ' . $pietab . ' ' . initws());
			
		} 
		else {
			$json['msj']     = dbGetErrorMsg();
			$json['success'] = false;
		}
		echo json_encode($json);
		break;
	case 22: // encabezado grupos de (utilizada opción: 99)
		$json            = array();
		$detalle         = '';
		$detallefac         = '';
		$cadenabusq		= '';
		$libs            = new db();
		$json['msj']     = 'Detalle de Grupos generados';
		$json['success'] = true;
		if (isset($_POST['tipoop'])) {$_SESSION['estado'] = $_POST['tipoop']; } 
		if (isset($_POST['descripcion'])) { $_SESSION['proceso'] = $_POST['descripcion'];} 
		if (isset($_POST['tipomov'])) {$_SESSION['tipomov'] = $_POST['tipomov'];} 
		$xcontador = 0;
		$resultado = $libs->getEncabezadosgrupo($_SESSION['estado'],$_SESSION['usuario']);
		if ($resultado) {
			$enc_superior='
		<div class=" container card mb-3  margen15 " style="padding-top: 15px;" >
          <div class="row ">
            <div class="col">
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text" id="basic-addon1"><i class="fas fa-search"></i></span>
                </div>
                <input id= "txtBuscapedido" type="text" class="form-control" placeholder="Ubicar pedido" aria-label="Ubicar Pedido" aria-describedby="basic-addon1">
              </div>
              
            </div>
            <div class="col">
                 <button type="button" class="btn btn-block  btn-success" id= "btnnuevogrupo">Nuevo.Grupo</button>
            </div>
          </div>
        </div>';
        	// es el modelo de el nuevo grupo. 
			$conteoculto= '
			<div id="modelonuevogrupo" class="oculto">
				<div class="btncargartarea_nuevogrupo container card mb-3 DIGELASA margen15 " empresa="DIGELASA" grupo = "0" id= "modelogruponuevo" >
		          <div class="row ">
		            <div class="col">
		              <div class=" txtizq"><h3 class="text-info">Grupo ID: #1454</h3></div>
		            </div>
		            <div class="col">
		              <div class=" txtder "><h5 class="text-primary">Inicio: 08/08/2019</h5></div>
		            </div>
		          </div>
		          <div class="row">
		            <div class="col txtizq txtDIGELASA">
		              <h6>Facturas :</h6>
		            </div>
		          </div>
		          <div class="row">
		            <div class="col txtizq txtDIGELASA">
		              <h6></h6><h6> </h6>
		            </div>
		            <div class="col txtder  txtDIGELASA">
		              <span class="text-muted">Estatus:</span><h6>En proceso</h6>
		            </div>
		          </div>
		        </div>
		    </div>'; // modelo oculto de nuevo grupo.

			if (!sqlsrv_has_rows($resultado)) {
				$encabezado              = "
				<div id= 'detallegrupo'>
				<h3>&ldquo;Sin grupos pendientes&rdquo; </h3>
				</div>";
				$_SESSION["vistaactual"] = 8; // punto de anclaje, si se actualiza explorador
			}
			else {
				$anteriorgrupo= 0;
				$anteriorcunico='';
				$grupoagregar='';
				$json['msj']             = 'Datos generados';
				$_SESSION["vistaactual"] = 8;
				$encabezado              = "<h3>" . $_SESSION['proceso'] . "</h3>";
				$detcarreta='';
				while ($obj = sqlsrv_fetch_object($resultado)) {
					$idagrupa   = ($obj->idAgrupa);
					$cunico   	= ($obj->cunico);
					if ($anteriorgrupo==0 ){ $anteriorgrupo= $idagrupa; }
					if ($anteriorcunico==0 ){ $anteriorcunico= $cunico; }
					$iduser		= ($obj->idUser);
					$inicio   	= ($obj->INICIO);
					$iniciogt   	= ($obj->INICIO);
					$iddetagrupa= ($obj->idDetAgrupa);
					
					$pedido   	= ($obj->pedido);
					$xcontador  = $xcontador + 1;
					$idCarreta= ($obj->idCarreta);
					// DETALLE ACUMULATIVO.
					if ($anteriorgrupo== $idagrupa){
						// verificar si Cunico no cambia
						if ($anteriorcunico== $cunico){
							$detcarreta=$detcarreta.'
									<div class="col txtizq txtDIGELASA">
						                <h4> '.$idCarreta.'</h4>
						            </div>';
						} 
						else{
							$detcarreta='';
							$detcarreta=$detcarreta.'
									<div class="col txtizq txtDIGELASA">
						                <h4> '.$idCarreta.'</h4>
						            </div>';

							}

							$cadenabusq= $cadenabusq . $cunico.$pedido;
							$detallefac=$detallefac.'
								<div class="row">
						            <div class="col txtizq txtDIGELASA">
						                <h4> '.$cunico.'</h4>
						            </div>
						            <div class="col txtizq txtDIGELASA">
						                <h4> '.$pedido.'</h4>
						            </div>
						            '.$detcarreta.'
						        </div>';
							$xdetalle='
								<div id="detallegrupo">
								    <div class="btncargartarea_seleccionagrupo container card mb-3 DIGELASA margen15 " empresa="DIGELASA" idAgrupa="'.$idagrupa.'" dadatabus="'.$cadenabusq.'">
								        <div class="row ">
								            <div class="col">
								                <div class=" txtizq">
								                    <h3 class="text-info">Grupo ID: #'.$idagrupa.'</h3></div>
								            </div>
								            <div class="col">
								                <div class=" txtder ">
								                    <h5 class="text-primary">Inicio: '.$iniciogt.'</h5></div>
								            </div>
								        </div>
								        <div class="row">
								            <div class="col txtizq txtDIGELASA">
								                <h6>Facturas :</h6>
								            </div>
								            <div class="col txtizq txtDIGELASA">
								                <h6>Pedido :</h6>
								            </div>
								        </div>
								       '.$detallefac.'
								        <div class="row">
								            <div class="col txtizq txtDIGELASA">
								                <h6></h6>
								                <h6></h6>
								            </div>
								            <div class="col txtder  txtDIGELASA">
								                <span class="text-muted">Estatus:</span>
								                <h6>Sin finalizar</h6>
								            </div>
								        </div>
								    </div>
								</div>';
							$grupoagregar= $xdetalle;
						//}

					}
					else{
						$detcarreta='';
						$anteriorgrupo= $idagrupa;
						$anteriorcunico= $cunico;
						$detalle    = $detalle . $grupoagregar ;
						$detallefac='';
						$cadenabusq='';
						
						$cadenabusq= $cadenabusq . $cunico.$pedido;
						$detcarreta=$detcarreta.'
									<div class="col txtizq txtDIGELASA">
						                <h4> '.$idCarreta.'</h4>
						            </div>';
						$detallefac=$detallefac.'
							<div class="row">
					            <div class="col txtizq txtDIGELASA">
					                <h4> '.$cunico.'</h4>
					            </div>
					            <div class="col txtizq txtDIGELASA">
					                <h4> '.$pedido.'</h4>
					            </div>
					            '.$detcarreta.'
					        </div>';
						$xdetalle='
							<div id="detallegrupo">
							    <div class="btncargartarea_seleccionagrupo container card mb-3 DIGELASA margen15 " empresa="DIGELASA" idAgrupa="'.$idagrupa.'" dadatabus="'.$cadenabusq.'">
							        <div class="row ">
							            <div class="col">
							                <div class=" txtizq"><h3 class="text-info">Grupo ID: #'.$idagrupa.'</h3></div>
							            </div>
							            <div class="col">
							                <div class=" txtder ">
							                    <h5 class="text-primary">Inicio: '.$iniciogt.'</h5></div>
							            </div>
							        </div>
							        <div class="row">
							            <div class="col txtizq txtDIGELASA">
							                <h6>Facturas :</h6>
							            </div>
							            <div class="col txtizq txtDIGELASA">
							                <h6>Pedido :</h6>
							            </div>
							        </div>
							       '.$detallefac.'
							        <div class="row">
							            <div class="col txtizq txtDIGELASA">
							                <h6></h6>
							                <h6></h6>
							            </div>
							            <div class="col txtder  txtDIGELASA">
							                <span class="text-muted">Estatus:</span>
							                <h6>Sin finalizar</h6>
							            </div>
							        </div>
							    </div>
							</div>';
						$grupoagregar= $xdetalle;
					}
				} 
				
				$detalle    = $detalle . $grupoagregar ;
			}
			$pietab          = '
				<script type="text/javascript">
					$(document).ready(function(){ $("#menug").load("menu.php"); });
				</script>	
				 <script type="text/javascript">
				 	$(document).ready(function() {
						for(var i in cunicolist){
						   $("#"+cunicolist[i]).addClass("seleccionado");
						}
					});
				 </script>
				 ';
			//$json['detalle'] = $enc_superior.' '. $encabezado . ' ' . $detalle . '' . $pietab .'' . $conteoculto. ' ' . initws();
			$json['detalle'] = minificado($enc_superior.' '. $encabezado . ' ' . $detalle . '' . $pietab .'' . $conteoculto. ' ' . initws());
				 
		} //$resultado
		else {
			//$json['msj']     = ucfirst(strtolower(utf8_encode(mssql_get_last_message())));
			$json['msj']     = dbGetErrorMsg();
			$json['success'] = false;
		}
		echo json_encode($json);
		break;
	case 23: // Generar núevo ID de código para agrupar
		$json            = array();
		$detalle         = '';
		$libs            = new db();
		$json['msj']     = 'No es posible generar grupo.';
		$json['success'] = true;
		$resultado = $libs-> getGrupoPicking('G1', '',$_SESSION['usuario'],'','','',''); //
		if ($resultado) {
			if (!sqlsrv_has_rows($resultado)) {
				$json['msj']     = 'No es posible generar grupo.';
				$json['grupo']     = 0;
				$json['success'] = false;
			} 
			else {
				$json['success'] = true;
				$json['msj']             = 'Grupo generado';
				while ($obj = sqlsrv_fetch_object($resultado)) {
					$json['grupo'] = $obj->idAgrupa;
					$_SESSION['grupopicking']= $obj->idAgrupa;
				} 
			}
		}
		else { 		
			$json['msj']     = dbGetErrorMsg();		
			$json['success'] = false;
		}
		echo json_encode($json);
		break;
	case 24: // generar listado de Cunicos marcados en sesión de el grupo 
		$json            = array();
		$detalle         = '';
		$libs            = new db();
		$json['msj']     = 'Listado posiciones Guardado';
		$json['success'] = true;
		$cunicolistaux2='';
		if (isset($_POST["cunicolist"])) {
			$cunicolistaux= explode(",",$_POST["cunicolist"]);
	        for($i = 0; $i < count($cunicolistaux); $i++){
	      		if (rtrim(ltrim($cunicolistaux[$i]))!= ''){
	      			$cunicolistaux2= $cunicolistaux2. '"'.$cunicolistaux[$i].'",';
	          	}
			}
			$json['msj']     = 'Listado posiciones Guardado|[' . $cunicolistaux2. ']';
			$_SESSION['cunicolist'] ='['. $cunicolistaux2.']';
		} 
		echo json_encode($json);
		break;
	case 25: // encabezado de ordenes pendientes de proceso E1 EGRESOS DE BODEGA nueva funcion sustituye case 14 
		$json            = array();
		$detalle         = '';
		$libs            = new db();
		$json['msj']     = 'Ordenes Pendientes';
		$json['success'] = true;
		if (isset($_POST['grupopicking'])) {$_SESSION['grupopicking'] = $_POST['grupopicking']; } 
		if (isset($_POST['tipoop'])) {$_SESSION['estado'] = $_POST['tipoop']; } 
		if (isset($_POST['descripcion'])) { $_SESSION['proceso'] = $_POST['descripcion'];} 
		if (isset($_POST['tipomov'])) {$_SESSION['tipomov'] = $_POST['tipomov'];} 
		if (isset($_POST['esnuevogrupo'])) {$_SESSION['esnuevogrupo'] = $_POST['esnuevogrupo'];} 
		
		$xcontador = 0;
		if ($_SESSION['esnuevogrupo']==0){
			
			//$resultado = $libs->getGrupoPicking($_SESSION['estado'],$_SESSION['grupopicking']);
			$resultado = $libs->getGrupoPicking($_SESSION['estado'], $_SESSION['grupopicking'],'','','','','');
		}
		else{
			$resultado = $libs->getEncabezados($_SESSION['estado']);
		}
		
		if ($resultado) {
			if (!sqlsrv_has_rows($resultado)) {
				$encabezado              = "<h3>&ldquo;Sin Salidas pendientes&rdquo; </h3>";
				$_SESSION["vistaactual"] = 9;
			} 
			else {
				$json['msj']             = 'Datos generados';
				$_SESSION["vistaactual"] = 9;
				$encabezado              = "<h3>" . $_SESSION['proceso'] . "</h3>";
				while ($obj = sqlsrv_fetch_object($resultado)) {
					$nombre     = trim($obj->nombre);
					$cunico     = trim($obj->cunico);
					$idx        = ($obj->idx);
					$prioridad  = $obj->prioridad;
					$empresa    = $obj->empresa;
					$nomempresa = trim($obj->nomempresa);
					$opera      = $obj->opera;
					$pedido     = trim($obj->pedido);
					$empresacli = $obj->empresacli;
					$codcliente = $obj->codcliente;
					$corre      = $obj->corre;
					$correl     = $obj->correl;
					$vendedor   = trim($obj->vendedor);
					$factura    = trim($obj->factura);
					$CANTMAX     = $obj->CANTMAX;
					$PRESENTACIONES     = $obj->PRESENTACIONES;
					$LINEAS     = $obj->LINEAS;

					//$SOLIDOS  	= $obj->SOLIDOS;
					$SOLIDOS  	= 0;
					//$LIQUIDOS  	= $obj->LIQUIDOS;
					$LIQUIDOS  	= 0;


					$idDetAgrupa     = 0;
					if ($idDetAgrupa== 0){ $claseseleccionado='';}
					else{ $claseseleccionado='seleccionado'; }
					$esfact     = $obj->esfact;
					$xcontador  = $xcontador + 1;
					$xdetalle2  = '';
					$xdetalle   = '
					  	<div class="btncargartarea_egreso_marcar container card mb-3 ' . $nomempresa . ' margen15  '.$claseseleccionado.'" empresa= "' . $nomempresa . '" cunico="' . $cunico . '" id="' . $cunico . '" pedido= "'.$pedido.'" idDetAgrupa="'.$idDetAgrupa.'">
					      <div class="row " >
					        <div class="col">
					          <div class=" txtizq"><h3 class="text-info">' . $corre . '</h3></div>
					        </div>
					        <div class="col">
					          <div class=" txtizq"><h3 class="text-info">Prod:' . $PRESENTACIONES . '</h3></div>
					        </div>
					        <div class="col">
					          <div class=" txtizq"><h3 class="text-info">Lineas:' . $LINEAS . '</h3></div>
					        </div>
					        <div class="col">
					          <div class=" txtder "><h5 class="text-primary">Ped: ' . $pedido . '</h5></div>
					        </div>
					      </div>
					     <div class="row " >
					        <div class="col">
					          <div class=" txtizq"><h3 class="text-info"></h3></div>
					        </div>
					        <div class="col">
					          <div class=" txtizq"><h3 class="text-info"><i class="fas fa-tablets"></i>    ' . $SOLIDOS . '</h3></div>
					        </div>
					        <div class="col">
					          <div class=" txtizq"><h3 class="text-info"><i class="fas fa-tint"></i>    ' . $LIQUIDOS . '</h3></div>
					        </div>
					        <div class="col">
					          <div class=" txtder "><h5 class="text-primary"></h5></div>
					        </div>
					     </div>
					     <div class="row">
					        <div class="col txtizq txt' . $nomempresa . '">
					          <h6>Cliente :</h6>
					        </div>
					        <div class="col txtder  txt' . $nomempresa . '">
					          <h6> ' . $factura . '</h6>
					        </div>
					      </div>
					      <div class="row">
					        <div class="col txtizq txt' . $nomempresa . '">
					          <h4> ' . $nombre . '</h4>
					        </div>
					      </div>
					      <div class="row">
					        <div class="col txtizq txt' . $nomempresa . '">
					          <h6>Vendedor :</h6><h6>' . $vendedor . ' </h6>
					        </div>
					        <div class="col txtizq txt' . $nomempresa . '  	 	">			          
					        </div>
					        <div class="col txtder  txt' . $nomempresa . '">
					          <span class="text-muted">Empresa:</span><h6>' . $nomempresa . '</h6>
					        </div>
					      </div>
					    </div>';
					$detalle    = $detalle . ' ' . $xdetalle . $xdetalle2;
				} 
			}
			$pietab          = '
				<script type="text/javascript">
					$(document).ready(function(){ $("#menug").load("menu.php"); });
				</script>	
				 <script type="text/javascript">
				 	$(document).ready(function() {
					 	$(".botonF1").hover(function(){
						  $(".btn2").addClass("animacionVer");
						});
						$(window).scrollTop(0);
					});
				 </script>
				<div class="contenedor">
					<button class=" botonF1 btncargartareas_egreso">
					   <span class="fas fa-shopping-cart"></span> 
					</button>
				</div>
				<style type="text/css">
					.botonF1{
						width:60px;
						height:60px;
						border-radius:100%;
						background:#007bff;
						right:0;
						bottom:0;
						position:fixed;
						margin-right:16px;
						margin-bottom:16px;
						border:none;
						outline:none;
						color:#FFF;
						font-size:2em;
						box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23);
						transition:.3s;  
						/*background-image: url(uni.png)!important;
						background-position: center;*/
					}
					.animacionVer{
						transform:scale(1);
					}
				 </style>
				 <script type="text/javascript">
				 	$(document).ready(function() {
					 	$(".botonF1").hover(function(){
						  $(".btn2").addClass("animacionVer");
						})
						cunicolist=  '.$_SESSION['cunicolist'].';
						for(var i in cunicolist){
						   $("#"+cunicolist[i]).addClass("seleccionado");
						}
					});
				 </script>
				 ';
			//$json['detalle'] = $encabezado . ' ' . $detalle . '' . $pietab . ' ' . initws();
			$json['detalle'] = minificado($encabezado . ' ' . $detalle . '' . $pietab . ' ' . initws());
			
		}
		else {
			$json['msj']     = dbGetErrorMsg();
			$json['success'] = false;
		}
		echo json_encode($json);
		break;
	case 26: // agregar factura a grupo 
		$json            = array();
		$detalle         = '';
		$libs            = new db();
		$json['msj']     = 'Listado posiciones Guardado';
		$json['success'] = true;
		$cunico= $_POST['cunico'];
		$pedido= $_POST['pedido'];
		$resultado = $libs-> getGrupoPicking('G2', $_SESSION['grupopicking'],'',$cunico,$pedido,'',''); 
		if ($resultado) {
				while ($obj = sqlsrv_fetch_object($resultado)) {
					$idDetAgrupa = ($obj->idDetAgrupa);
				}
				$json['idDetAgrupa'] = $idDetAgrupa;
				$json['msj']     = 'Agregado al grupo. No: .::' . $_SESSION['grupopicking'] . '::. ';
				$json['success'] = true;
		}
		else { 		
			$json['msj']     = dbGetErrorMsg();		
			$json['success'] = false;
		}
		echo json_encode($json);
		break;
	case 27: // agregar contenedor a factura/pedido de grupo
		$json            = array();
		$detalle         = '';
		$libs            = new db();
		$json['msj']     = 'Listado posiciones Guardado';
		$json['success'] = true;
		$contenedor= $_POST['contenedor'];
		$idDetAgrupa= $_POST['idDetAgrupa'];
		$resultado = $libs-> getGrupoPicking('G3', '','','','',$contenedor,$idDetAgrupa); // Agrega un contenedor a factura /pedido 
		if ($resultado) {
				$json['msj']     = 'Agregado a la factura';
				$json['success'] = true;
		}
		else { 		
			$json['msj']     = dbGetErrorMsg();		
			$json['success'] = false;
		}
		echo json_encode($json);
		break;
	case 28: // asignar el # de grupo a sesión
		$json            = array();
		$detalle         = '';
		$json['msj']     = 'Grupo seleccionado';
		$json['success'] = true;
		$_SESSION['cunicolist'] ='[]';
		$_SESSION['grupopicking']= $_POST['grupopicking'];
		echo json_encode($json);
		break;
	case 30: // envio de detalle de tarea
		$json            = array();
		$detalle         = '';
		$libs            = new db();
		$json['success'] = false;
		if (isset( $_POST['ordentarea']) && isset($_SESSION['usuario'])  && isset($_POST['codprod'])  && isset($_POST['posicion']) ) {
			$resultado       = $libs->GetdetalletareaHD('E3', $_POST['ordentarea'], $_SESSION['usuario'],$_POST['cunico'], $_POST['codprod'], $_POST['carreta'], $_POST['taked'], $_POST['posicion']);
			if ($resultado) {
				$json['success'] = true;
				$json['msj']     = 'Tarea completada correctamente de grupo ';
			} //$resultado
			else {
				$json['msj']     = 'error en consulta'.dbGetErrorMsg();
				$json['success'] = false;
			}
		} 
		else {
			$json['msj']     = 'Actualice esta vista e intente de nuevo ';
			$json['success'] = false;
			echo json_encode($json);
		}
		echo json_encode($json);
		break;
	case 31: // Finalizar grupo de picking
		$json            = array();
		$detalle         = '';
		$libs            = new db();
		$json['msj']     = 'No es posible Finalizar el grupo.';
		$json['success'] = true;
		$resultado = $libs-> getGrupoPicking('FG', $_SESSION['grupopicking'],'','','','',''); // finaliza grupo
		if ($resultado) {
				$json['msj']     = 'Grupo finalizado';
				$_SESSION['estado'] = 'E0'; 
				$_SESSION["vistaactual"] = 8;
				$_SESSION['cunicolist'] ='[]';
				$json['success'] = true;
		} 
		else {
			$json['msj']     = dbGetErrorMsg();		
			$json['success'] = false;
		}
		echo json_encode($json);
		break;
	case 32: // encabezado de ordenes pendientes de packing
		$json            = array();
		$detalle         = '';
		$detallefac         = '';
		$detcontenedor         = '';
		$cadenabusq		= '';
		$cunicoant		= 0;
		$libs            = new db();
		$json['msj']     = 'Detalle de Grupos generados';
		$json['success'] = true;
		$xdetalle='';
		$scriptbusqueda='';
		if (isset($_POST['tipoop'])) {$_SESSION['estado'] = $_POST['tipoop']; } 
		if (isset($_POST['descripcion'])) { $_SESSION['proceso'] = $_POST['descripcion'];} 
		if (isset($_POST['tipomov'])) {$_SESSION['tipomov'] = $_POST['tipomov'];} 
		$xcontador = 0;
		$resultado = $libs->getEncabezados('S1'); 
		if ($resultado) {
			$enc_superior='
			<div class=" container card mb-3  margen15 " style="padding-top: 15px;" >
	          <div class="row ">
	            <div class="col">
	              <div class="input-group mb-3">
	                
	                <input id= "txtBuscapedido2" type="text" class="form-control" placeholder="Ubicar pedido" aria-label="Ubicar Pedido" aria-describedby="basic-addon1">
	              </div>
	              
	            </div>
	            
	          </div>
	        </div>';
			$conteoculto= '';
		    if (!sqlsrv_has_rows($resultado)) {
				$encabezado              = "
				<div id= 'detallegrupo'>
				<h3>&ldquo;Sin grupos pendientes&rdquo; </h3>
				</div>";
				$_SESSION["vistaactual"] = 8; // punto de anclaje, si se actualiza explorador
			}
			else {
				$anteriorgrupo= 0;
				$grupoagregar='';
				$cunicoant=0;
				$json['msj']             = 'Datos generados';
				$_SESSION["vistaactual"] = 11;
				$encabezado              = "<h3>" . $_SESSION['proceso'] . "</h3>";
				$detcompleto='';
				
				while ($obj = sqlsrv_fetch_object($resultado)) {
					$idagrupa   = ($obj->pedido);		
					$cunico   	= trim($obj->cunico);		
					$factura   	= trim($obj->factura);
					//$iduser		= ($obj->idUser);	
					$inicio   	= '';	
					$iniciogt   	= ' ';
					$iddetagrupa= ($obj->pedido);
					$pedido   	= ($obj->pedido);
					$porcenpedi = ($obj->porcenpedi);
					$nombre = ($obj->nombre);
					$PorceFact	= ($obj->PorceFact);
					$idCarreta= ($obj->idCarreta);
					$prioridad= ($obj->prioridad);
					if ($prioridad== 0){
						$alerta= "";	
					}
					else{
						//$alerta= "progress-bar-striped";
						$alerta= "prioridad";
					}

					$xcontador  = $xcontador + 1; 
					
					if ($anteriorgrupo!= $idagrupa){
						/**********************************************/
						if ($anteriorgrupo== 0){
							// no agrega el pie al principio
						}
						else{
							// agregar el pie
							$xdetalle= $xdetalle .'
							        <div class="row">
							            <div class="col txtizq txtDIGELASA">
							                <h6></h6>
							                <h6></h6>
							            </div>
							            <div class="col txtder  txtDIGELASA">
							                <span class="text-muted">Estatus:</span>
							                <h6>Sin finalizar</h6>
							            </div>
							        </div>
							    </div>
							</div>';
						}
						$xdetalle=$xdetalle.'
							    <div id= "'.$idagrupa.'" class="btncargartarea_seleccionapedido container card mb-3 DIGELASA margen15 '.$alerta.' " empresa="DIGELASA" idpedido="'.$idagrupa.'" >
							        <div class="row ">
							            <div class="col">
							                <div class=" txtizq">
							                    <h3 class="text-info">Pedido: #'.$idagrupa.'</h3></div>
							            </div>
							            <div class="col">
							                <div class=" txtder ">
							                    <h5 class="text-primary">Inicio: '.$iniciogt.'</h5></div>
							            </div>
							        </div>
							        <div class="row ">
							            <div class="col">
							                <div class=" txtizq">
							                    <h6 class="txtDIGELASA">Cliente: '.$nombre.'</h6>
							                </div>
							            </div>
							            
							        </div>
							        <div class="row">
							        	<div class="col txtizq txtDIGELASA">
								        	<div class=" progress">
											  <div class="progress-bar progress-bar-striped" role="progressbar" style="width: '.$porcenpedi.'%;" aria-valuenow="'.$porcenpedi.'" aria-valuemin="0" aria-valuemax="100">'.$porcenpedi.'%</div>
											</div>
											<hr class= "hrgeneral">
										</div>
							        </div>

							        <div class="row">
							            <div class="col txtizq txtDIGELASA">
							                <h6>Facturas :</h6>
							            </div>
							            <div class="col txtder txtDIGELASA">
							                <h6>Avance:</h6>
							            </div>
							            
							        </div>
							        ';
						$scriptbusqueda=$scriptbusqueda. '$("#'.$anteriorgrupo.'").attr("databus","'.$cadenabusq.'");';
						$anteriorgrupo=$idagrupa;
						$detcompleto='';
						$cadenabusq='';
					}
					if ($cunicoant!= $cunico){
						$cadenabusq= $cadenabusq . $cunico.$pedido;
						$xdetalle= $xdetalle .'
							<div class="row">
					            <div class="col txtizq txtDIGELASA">
					                <h4> '.$cunico.'</h4>
					            </div>
					            <div class="col txtder txtDIGELASA">
						            <div class="progress">
									  <div class="progress-bar  bg-success" role="progressbar" style="width: '.$PorceFact.'%;" aria-valuenow="'.$PorceFact.'" aria-valuemin="0" aria-valuemax="100">'.$PorceFact.'%</div>
									</div>
								</div>
					        </div>';

						$cunicoant= $cunico;
					}
					$xdetalle= $xdetalle .'';
				}
				$xdetalle= $xdetalle .'
							        <div class="row">
							            <div class="col txtizq txtDIGELASA">
							                <h6></h6>
							                <h6></h6>
							            </div>
							            <div class="col txtder  txtDIGELASA">
							                <span class="text-muted">Estatus:</span>
							                <h6>Sin finalizar</h6>
							            </div>
							        </div>
							    </div>
							</div>';
							$scriptbusqueda=$scriptbusqueda. '$("#'.$anteriorgrupo.'").attr("databus","'.$cadenabusq.'");';
			} 

			
			$json['detalle']=$xdetalle;
		} 
		else {

			$json['msj']     = dbGetErrorMsg();
			$json['success'] = false;
		}
		$pietab          = '
				<script type="text/javascript">
					$(document).ready(function(){ $("#menug").load("menu.php"); });
				</script>	
				 <script type="text/javascript">
				 	$(document).ready(function() {
				 		'.$scriptbusqueda.'
						for(var i in cunicolist){
						   $("#"+cunicolist[i]).addClass("seleccionado");
						}
					});
				 </script>
				 ';
			//$json['detalle'] = $enc_superior.' <div id="detallegrupo">'. $xdetalle . '</div>' . $pietab .'' . $conteoculto. ' ' . initws();
			$json['detalle'] = minificado($enc_superior.' <div id="detallegrupo">'. $xdetalle . '</div>' . $pietab .'' . $conteoculto. ' ' . initws());
			
		echo json_encode($json);
		break;
	case 33: // DETALLE de ordenes pendientes  PACKING
		$json            = array();
		$detalle         = ''; 
		$detallepadre	 = '';
		$cantidadtotal	 = 0;
		$xdetallepadre   = '';
		$libs            = new db();
		$json['msj']     = 'Detalle de Salida';
		$json['success'] = true;	
		$_SESSION['clasempresa']='INFASA';
		$xcontador = 0;
		if (isset($_POST['pedidopacking'])) { 
			$_SESSION['pedido'] = $_POST['pedidopacking'];
		} 
		//$_SESSION['grupopicking']=324; // provisional
		//privisional
		//$_SESSION['pedido'] ='';
							
		$resultado = $libs->GetdetalletareaHD('S2','','',$_SESSION['pedido'],0,0,0,0);
		//$resultado = $libs->getGrupoPicking('E1', $_SESSION['grupopicking'],'','','','',''); // sustituir por función real
		if ($resultado == true) {
			$json['msj']             = 'Datos generados';
			$_SESSION["vistaactual"]=12;
			$encabezado              = "<h3 CLASS= txt" . $_SESSION['clasempresa'] . ">Orden #  ". $_SESSION['pedido']." </h3>";
			$idCarretaA           = '';
			$loteA					 = '';
			$codigoA				 = '';
			$cunicoA 		 = '';
			$xdetalle 				='';
			while ($obj = sqlsrv_fetch_object($resultado)) {
				$cunico     = trim($obj->cunico);
				//$pedido     = trim($obj->pedido);
				$codigo         = trim($obj->codigo);
				$lote           = trim($obj->lote);
				if ($lote != $loteA) {
					$cantidadtotal= 0; 
				} 
				$fecha          = $obj->fecha;
				$OrdenTarea     = $obj->OrdenTarea;
				$cantidad       = $obj->cantidad;
				$cantidadtotal	= $cantidadtotal + $cantidad;
				$habilitado     = $obj->habilitado;
				$idposestante   = $obj->idposestante;
				$vence          = $obj->Vence;
				$nombre         = trim($obj->nombre);
				//$Posicion       = trim($obj->Posicion);
				$Posicion       = '';
				$preciopub      = $obj->preciopub;
				$taked			= $obj->taked;	// si ya fue piqueado
				$taked2			= $obj->taked2;	// si ya fue empacado.
				$idCarreta		= $obj->idCarreta;
				$idAgrupa		= $obj->idAgrupa;
				//$acceso			= $obj->acceso;
				$acceso			= '';
				//$Area			= $obj->Area;
				$Area			= '';
				//$tipoproducto	= $obj->formulaf;
				$tipoproducto	= '';

				$ordenCarreta	= $obj->ordenCarreta;
				$Contenedor		= $obj->Contenedor;
				//$Posicioncomp =trim($obj->Area).' '. trim($obj->Posicion);
				$Posicioncomp ='';
				$xcontador      = $xcontador + 1;
				///************* DESABILITANDO LINEAS DE PEDIDO POR PICKER************
				$classpicker     = '';
				if ($taked == 1) { 	$classpicker     = 'pickeado';	} 
				else { $classpicker     = ''; }
				//************* separador************
				$separador      = '';
				$claseprioridad = '';
				/*if ($cunico != $cunicoA) {
					$cunicoA = $cunico;
					$separador     = '<hr class= "separador  contenedorsep hr' . $_SESSION['clasempresa'] . ' facturasep text-left"  tipoproducto="' . $cunico . '"></br>';
					$detalle = $detalle . $separador;
				} */
				// si tipo producto cambia su valor, se genera una división <hr>
				if ($idCarreta != $idCarretaA) {
					$idCarretaA = $idCarreta;
					$separador     = '<hr class= "separador hr' . $_SESSION['clasempresa'] . '  '.$Contenedor.'sep" tipoproducto="' . $idCarreta . '" idCarreta= "'.$idCarreta.'">';
					$detalle = $detalle . $separador;
				} 
				$claseprioridad = '';
				$xdetalle       =  '
				  	<div class="btncargar_grupos container card mb-3  margen15 semitransparente '.$classpicker.' padre'.trim($lote).trim($codigo).'" ordentarea= "' . $OrdenTarea . '" cunico="' . $cunico . '" id="' . $OrdenTarea . '" codprod= "' . $codigo . '" taked= "' . $taked . '" lote= "'.$lote.'" >
				      <div class="row " >
				        <div class="col">
				          <div class=" txtizq"><h5 class="text-info"> <span class="fas fa-search"></span>' . $Posicioncomp . '</h5></div>
				        </div>
				        <div class="col">
				          <div class=" txtder"><h5 class="text-primary">V: ' . $vence . '</h5></div>
				        </div>
				      </div>
				      <div class="row">
				        <div class="col txtizq">
				          <h6><span class="text-mutedx">Producto :</span></h6>
				        </div>
				        <div class="col txtder">
				          <h6> [' . $codigo . ']</h6>
				        </div>
				      </div>
				      <div class="row">
				        <div class="col txtizq">
				          <h4> ' . $nombre . '</h4>
				        </div>
				      </div>
				      <div class="row">
				        <div class="col txtizq">
				          <h5><span class="text-mutedx">Cantidad: </span>' . $cantidad . ' </h5>
				        </div>
				        <div class="col txtder">
				          <h5><span class="text-mutedx">Q. </span>' . $preciopub . '</h5>
				        </div>
				        <div class="col txtder">
				          <h5><span class="text-mutedx "> </span>L:' . $lote . '</h5>
				        </div>
				      </div>
				    </div>';							
				$detalle        = $detalle . ' ' . $xdetalle ;
			} 
			$pietab          = '
				<button type="button" class="btn btn-primary btn-lg btn-block optgrande  " id="btn_finalizarpedido" descripcion="Finalizar pedido">
					<span class="fas fa-paper-plane"></span> Finalizar Pedido 
				</button>
				<script type="text/javascript">
					$(document).ready(function(){ $("#menug").load("menu.php"); });
				</script>	
				 <script type="text/javascript">
				 	$(document).ready(function() {
					 	$("body").removeClass("bgINFASA");
	                    $("body").removeClass("bgDIGELASA");
	                    $("body").addClass("bg' . $_SESSION['clasempresa'] . '");
					 	$(".botonF1").hover(function(){
						  $(".btn2").addClass("animacionVer");
						});
						$(window).scrollTop(0);
					});
				 </script>
				 ';
			//$json['detalle'] = $encabezado . $detalle . $pietab . ' ' . initws();
				 $json['detalle'] = minificado($encabezado . $detalle . $pietab . ' ' . initws());
				 
		} 
		else {
			$json['msj']     = dbGetErrorMsg();
			$json['success'] = false;
		}
		echo json_encode($json);
		break;
	case 34: // PENDIENTES DE GENERAR EN TRASLADO SIN ETIQUETA
		$json            = array();
		$detalle         = '';
		$libs            = new db();
		$json['msj']     = 'Ordenes Pendientes';
		$json['success'] = true;
		$x=0;
		if (isset($_POST['tipoop'])) { 	$_SESSION['estado'] = $_POST['tipoop']; } 
		if (isset($_POST['descripcion'])) { $_SESSION['proceso'] = $_POST['descripcion'];}
		if (isset($_POST['tipomov'])) { $_SESSION['tipomov'] = $_POST['tipomov'];}
		$xcontador = 0;
		$resultado = $libs->getEncabezadosreingresobod($_SESSION['estado'],'','','');
		$detalle='';
		if ($resultado) {
			if (!sqlsrv_has_rows($resultado)) {
				$encabezado              = '<h3 class= "padre">&ldquo;Sin Reingresos con leyenda pendientes&rdquo; </h3>';
				$xmensajebtn='Ir a Traslados entre bodegas';
				$_SESSION["vistaactual"] = 13;
			} 
			else {
				$xmensajebtn='Generar Tareas de traslado';
				$json['msj']             = 'Datos generados';
				$_SESSION["vistaactual"] = 13;
				$encabezado              = '<h3 class= "padre">' . $_SESSION['proceso'] . '</h3>
				<table class="table">
					<thead class="thead-dark">
					    <tr>
					      <th scope="col" >#</th>
					      <th scope="col">Código</th>
					      <th scope="col" class="txtizq">Nombre</th>
					      <th scope="col" class="txtizq">Cantidad</th>
					    </tr>
				  	</thead>
					<tbody>
				';
				
				
				while ($obj = sqlsrv_fetch_object($resultado)) {
					$x= $x+1;
					$CODIGOSALE  	  = trim($obj->CODIGOSALE);
					$NOMBRE  	  = $obj->NOMBRE;
					$CantidadSale 	  = $obj->CantidadSale;
					$xdetalle = '
						<tr>
					      <th scope="row">'.$x.'</th>
					      <td>'.$CODIGOSALE.'</td>
					      <td>'.$NOMBRE.'</td>
					      <td>'.$CantidadSale.'</td>
					    </tr>';
					$detalle= $detalle.$xdetalle;
				} 
			}
			$pietab             = '
					</tbody>
				</table>
				</br>
				<button class="btn btn-primary btn-lg" id="btngenerartareasetiquetado">'.$xmensajebtn.'<i class="fas  fa-edit pl-1"></i></button>
				</br>
				 <script type="text/javascript">
				 	$(document).ready(function() {
				 		$("#menug").load("menu.php");
					});
				 </script>';
			$json['detalle']    = $encabezado . $detalle . $pietab . ' ' . initws();
			$json['encabezado'] = $detalle;
		} 
		else {
			$json['msj']     = dbGetErrorMsg();
			$json['success'] = false;
		}
		echo json_encode($json);
		break;
	case 35: // RECODIFICADO DE TRASLADOS
		$json            = array();
		$detalle         = '';
		$libs            = new db();
		$json['msj']     = 'Tareas Generadas';
		$json['success'] = true;
		$resultado = $libs->getEncabezadosreingresobod('RL','','','');
		$detalle='';
		if ($resultado) {
			$_SESSION["vistaactual"] = 6;
			$_SESSION['estado']  = 'TR';
			$_SESSION['proceso'] = 'Traslados entre bodegas';
			$_SESSION['tipomov'] = 'EGRESO';
			$json['msj']     = 'Tareas Generadas correctamente';
		} 
		else {
			$json['msj']     = dbGetErrorMsg();
			$json['success'] = false;
		}
		echo json_encode($json);
		break;
	case 36: // PENDIENTES DE ETIQUETAR
		$json            = array();
		$detalle         = '';
		$libs            = new db();
		$json['msj']     = 'Etquetados Pendientes';
		$json['success'] = true;
		if (isset($_POST['tipoop'])) { 	$_SESSION['estado'] = $_POST['tipoop']; } 
		if (isset($_POST['descripcion'])) { $_SESSION['proceso'] = $_POST['descripcion'];}
		if (isset($_POST['tipomov'])) { $_SESSION['tipomov'] = $_POST['tipomov'];}
		$xcontador = 0;
		$resultado = $libs->getEncabezadosreingresobod($_SESSION['estado'],'','','');
		if ($resultado) {
			if (!sqlsrv_has_rows($resultado)) {
				$encabezado              = '<h3 class= "padre">&ldquo;Sin tareas de etiquetado&rdquo; </h3>';
				$_SESSION["vistaactual"] = 14;
			}
			else {
				$json['msj']             = 'Datos generados';
				$_SESSION["vistaactual"] = 14;
				$detalle='';
				$encabezado              = '<h3 class= "padre">' . $_SESSION['proceso'] . '</h3>';
				while ($obj = sqlsrv_fetch_object($resultado)) {
					$correl  	  	  = $obj->correl;
					$codigosale  	  = trim($obj->codigosale);
					$cantidadsale  	  = $obj->cantidadsale;
					$lote 	  		  = trim($obj->lote);
					$leyenda		  = trim($obj->leyenda);
					
					$detalle       = $detalle . '
				  	<div class="btndetalleetiquetado container card mb-3  margen15 semitransparente " correl="' . $correl . '" codigosale= "' . $codigosale . '" lote="' . $lote . '" id="' . $correl . '">
				      <div class="row " >
				        <div class="col">
				          <div class=" txtizq"><h5 class="text-info">[' . $codigosale . ']</h5></div>
				        </div>
				        <div class="col">
				          <div class=" txtder"><h5 class="text-info"> Lote' . $lote . '</h5></div>
				        </div>
				      </div>
				      <div class="row">
				        <div class="col txtizq">
				          <h6><span class="text-mutedx">Leyenda a Imprimir:</span></h6>
				        </div>
				        
				        <div class="col txtder">
				          <h6> </h6>
				        </div>
				      </div>
				      <div class="row">
				        <div class="col txtizq">
				          <h4> &ldquo;' . $leyenda . '&rdquo;</h4>
				        </div>
				      </div>
				      <div class="row">
				        <div class="col txtizq">
				          <h5><span class="text-mutedx">Cantidad: </span>' . $cantidadsale . ' </h5>
				        </div>
				        
				      </div>
				    </div>';
				}
				//$detalle = $encabezado . ' ' . $detalle . $xdetalle2;
			}
			$pietab	= '
				 <script type="text/javascript">
				 	$(document).ready(function() {
				 		$("#menug").load("menu.php");
				 		$(window).scrollTop(0);
					});
				 </script>';
			$json['detalle']    = $encabezado . $detalle. $pietab . initws();
		}
		else {
			$json['msj']     = dbGetErrorMsg();
			$json['success'] = false;
		}
		echo json_encode($json);
		break;
	case 37: // confirmar tarea
		$json            = array();
		$detalle         = '';
		$libs            = new db();
		$json['msj']     = 'Tareas Generadas';
		$json['success'] = true;
		$resultado = $libs->getEncabezadosreingresobod('RU',$_POST['correl'],$_POST['codigosale'],$_POST['lote']);
		if ($resultado) {
			$json['success'] = true;
			$json['msj']     = 'Etiquetado guardado exitosamente';
		} 
		else {
			$json['msj']     = dbGetErrorMsg();
			$json['success'] = false;
		}
		echo json_encode($json);
		break;

	case 40: // existencias
		
			$json = array();
			$json['msj']     = 'Finalizada';
			$json['success'] = false;
			$detalle = '';
			$codigo='0';
			$libs = new db(); //carga de funciones del modelo
			$json['detalle'] = ' '; // mensaje por defecto vacio.
		//	$json['success'] = true; //iniciado de posición "Succes" con false por defecto (no obligatorio)
			$x=0;
			
					if (isset($_POST['tipoop'])) {$_SESSION['estado'] = $_POST['tipoop']; }
					//if (isset($_POST['canal'])) { 	$_SESSION[''] = $_POST['canal']; } 
					if (isset($_POST['codigobarra'])) { $_SESSION['codigobarra'] = $_POST['codigobarra'];}
					if (isset($_POST['descripcion'])) { $_SESSION['proceso'] = $_POST['descripcion'];}
					if (isset($_POST['tipomov'])) { $_SESSION['tipomov'] = $_POST['tipomov'];}
					$_SESSION["vistaactual"] = 15;
		
		$xcontador = 0;
	
			
			$resultado = $libs->getexistencias($_SESSION['estado'],705);

			
					
			
			$enc_superior = '
			<div id= "btnCodigoexistencia" class=" container-fluid container card mb-3  margen15 " style="padding-top: 15px;" >
	          <div class="row ">
	            <div class="col">
	              <div class="input-group mb-3">
	                <div class="input-group-prepend">
	                  <span class="input-group-text" id="basic-addon1"><i class="fas fa-search"></i></span>
					</div> 
				
	                <input id= "txtBuscapedido" " value="" type="text" class="form-control" placeholder="Codigo de Producto" aria-label="Codigo de producto" aria-describedby="basic-addon1">
					<input id="btnenviarexistencia" type="submit"  value="Consultar">

				

					</div>
	              
				</div>
				
	           
	          </div>
			</div>';
			
			if ($resultado == true) {
				$_SESSION["vistaactual"] = 15;
				$json['success'] = true;
			
			

				//inicia
				if (!sqlsrv_has_rows($resultado)) {
					$encabezado              = '<h3 class= "padre">&ldquo;ESCANEAR CODIGO&rdquo; </h3>';
					$xmensajebtn='POSICION VACIA';
					$_SESSION["vistaactual"] = 15;
				} 
				else {

					$xmensajebtn='del Codigo: ';
					$json['msj']             = 'Datos generados';
					$_SESSION["vistaactual"] =15;
					$encabezado              = '<h3 class= "padre">' . $_SESSION['proceso'] . '</h3>
					<div class="container " ><table class="table">
						<thead class="thead-dark">
							<tr>
							  <th scope="col" >#</th>
							  <th scope="col">Código</th>
							  <th scope="col" class="txtizq">Nombre</th>
							  <th scope="col" class="txtizq">Existencia</th>
							  <th scope="col" class="txtizq">Lote</th>
							</tr>
						  </thead>
						<tbody> </div>
					';
					
					
					while ($obj = sqlsrv_fetch_object($resultado)) {
						$_SESSION["vistaactual"] = 15;
						$x= $x+1;
						$CODIGOSALE  	  = trim($obj->CODIPRESEN);
						$NOMBRE  	  = $obj->NOMBRE;
						$EXISTEN 	  = $obj->EXISTEN;
						$LOTE 	  = $obj->LOTE;
						$xdetalle = '
							<tr>
							  <th scope="row">'.$x.'</th>
							  <td>'.$CODIGOSALE.'</td>
							  <td>'.$NOMBRE.'</td>
							  <td>'.$EXISTEN.'</td>
							  <td>'.$LOTE.'</td>
							</tr>';
						$detalle= $detalle.$xdetalle;
					} 
				}

				//finaliza








			$pietab          = '
				<script type="text/javascript">
				
					$(document).ready(function(){ 
					$("#menug").load("menu.php"); 
					
					
					});
					
					

				</script>	
				
				 ';
			
			}
			else {
				$json['detalle']     = dbGetErrorMsg();
				$json['success'] = false;
			}
			$_SESSION["vistaactual"] = 15;
			
			$json['detalle']=minificado($enc_superior . $encabezado . $detalle .$pietab.$xmensajebtn .''. initws());
			
		
		
		
		echo json_encode($json);
			
			break;
	case 99: // encabezado grupos de picking
		$json            = array();
		$detalle         = '';
		$detallefac         = '';
		$detcontenedor         = '';
		$cadenabusq		= '';
		$cunicoant		= 0;
		$libs            = new db();
		$json['msj']     = 'Detalle de Grupos generados';
		$json['success'] = true;
		$xdetalle='';
		$scriptbusqueda='';
		if (isset($_POST['tipoop'])) {$_SESSION['estado'] = $_POST['tipoop']; } 
		if (isset($_POST['descripcion'])) { $_SESSION['proceso'] = $_POST['descripcion'];} 
		if (isset($_POST['tipomov'])) {$_SESSION['tipomov'] = $_POST['tipomov'];} 
		$xcontador = 0;
		$resultado = $libs->getEncabezadosgrupo($_SESSION['estado'],$_SESSION['usuario']);
		//$resultado = $libs->getEncabezadosgrupo('E0',20121);
		if ($resultado) {
			$enc_superior='
			<div class=" container card mb-3  margen15 " style="padding-top: 15px;" >
	          <div class="row ">
	            <div class="col">
	              <div class="input-group mb-3">
	                <div class="input-group-prepend">
	                  <span class="input-group-text" id="basic-addon1"><i class="fas fa-search"></i></span>
	                </div>
	                <input id= "txtBuscapedido" type="text" class="form-control" placeholder="Ubicar pedido" aria-label="Ubicar Pedido" aria-describedby="basic-addon1">
	              </div>
	              
	            </div>
	            <div class="col">
	                 <button type="button" class="btn btn-block  btn-success" id= "btnnuevogrupo">Nuevo.Grupo</button>
	            </div>
	          </div>
	        </div>';
			$conteoculto= '
			<div id="modelonuevogrupo" class="oculto">
				<div class="btncargartarea_nuevogrupo container card mb-3 DIGELASA margen15 " empresa="DIGELASA" grupo = "0" id= "modelogruponuevo" >
		          <div class="row ">
		            <div class="col">
		              <div class=" txtizq"><h3 class="text-info">Grupo ID: #1454</h3></div>
		            </div>
		            <div class="col">
		              <div class=" txtder "><h5 class="text-primary">Inicio: </h5></div>
		            </div>
		          </div>
		          <div class="row">
		            <div class="col txtizq txtDIGELASA">
		              <h6>Facturas :</h6>
		            </div>
		          </div>
		          <div class="row">
		            <div class="col txtizq txtDIGELASA">
		              <h6></h6><h6> </h6>
		            </div>
		            <div class="col txtder  txtDIGELASA">
		              <span class="text-muted">Estatus:</span><h6>En proceso</h6>
		            </div>
		          </div>
		        </div>
		    </div>'; // modelo oculto de nuevo grupo.
		    if (!sqlsrv_has_rows($resultado)) {
				$encabezado              = "
				<div id= 'detallegrupo'>
				<h3>&ldquo;Sin grupos pendientes&rdquo; </h3>
				</div>";
				$_SESSION["vistaactual"] = 8; // punto de anclaje, si se actualiza explorador
			}
			else {
				$anteriorgrupo= 0;
				$grupoagregar='';
				$cunicoant=0;
				$json['msj']             = 'Datos generados';
				$_SESSION["vistaactual"] = 8;
				$encabezado              = "<h3>" . $_SESSION['proceso'] . "</h3>";
				$detcompleto='';
				
				while ($obj = sqlsrv_fetch_object($resultado)) {
					
					$idagrupa   = ($obj->idAgrupa);		
					$cunico   	= ($obj->cunico);		
					$iduser		= ($obj->idUser);	
					$inicio   	= ($obj->INICIO);	
					$xdate = new DateTime($inicio);
					$iniciogt   	= $xdate->format('d-m-y H:i');
					$iddetagrupa= ($obj->idDetAgrupa);
					$pedido   	= ($obj->pedido); 
					$idCarreta= ($obj->idCarreta);
					$xcontador  = $xcontador + 1; 
					
					if ($anteriorgrupo!= $idagrupa){
						/**********************************************/
						if ($anteriorgrupo== 0){
							// no agrega el pie al principio
						}
						else{
							// agregar el pie
							$xdetalle= $xdetalle .'
							</div>
							        <div class="row">
							            <div class="col txtizq txtDIGELASA">
							                <h6></h6>
							                <h6></h6>
							            </div>
							            <div class="col txtder  txtDIGELASA">
							                <span class="text-muted">Estatus:</span>
							                <h6>Sin finalizar</h6>
							            </div>
							        </div>
							    </div>
							</div>
							';
						}
						$xdetalle=$xdetalle.'
							
							    <div id= "'.$idagrupa.'" class="btncargartarea_seleccionagrupo container card mb-3 DIGELASA margen15 " empresa="DIGELASA" idAgrupa="'.$idagrupa.'" >
							        <div class="row ">
							            <div class="col">
							                <div class=" txtizq">
							                    <h3 class="text-info">Grupo ID: #'.$idagrupa.'</h3></div>
							            </div>
							            <div class="col">
							                <div class=" txtder ">
							                    <h5 class="text-primary"><i class="fas fa-user-clock"></i>: '.$iniciogt.'</h5></div>
							            </div>
							        </div>
							        <div class="row">
							            <div class="col txtizq txtDIGELASA">
							                <h6>Facturas :</h6>
							            </div>
							            <div class="col txtder txtDIGELASA">
							                <h6>Pedido :</h6>
							            </div>
							            <div class="col txtder txtDIGELASA">
							                <h6>Contenedores :</h6>
							            </div>
							        </div>
							        <div class="row">';
						$scriptbusqueda=$scriptbusqueda. '$("#'.$anteriorgrupo.'").attr("databus","'.$cadenabusq.'");';
						$anteriorgrupo=$idagrupa;
						$detcompleto='';
						$cadenabusq='';
					}
					if ($cunicoant!= $cunico){
						$cadenabusq= $cadenabusq . $cunico.$pedido;
						$xdetalle= $xdetalle .'
					            <div class="col txtizq txtDIGELASA">
					                <h4> '.$cunico.'</h4>
					            </div>
					            <div class="col txtizq txtDIGELASA">
					                <h4> '.$pedido.'</h4>
					            </div>';

						$cunicoant= $cunico;
						//echo $detcontenedor.'</br>';
						//$detcontenedor='';
					}

					//echo $idCarreta.'<br>';
					$xdetalle= $xdetalle .'
					<row>
						<div class="col txtizq txtDIGELASA">
					         <h4> '.$idCarreta.'</h4>
					    </div>
					</row>';
				}
				$xdetalle= $xdetalle .'
							</div>
							        <div class="row">
							            <div class="col txtizq txtDIGELASA">
							                <h6></h6>
							                <h6></h6>
							            </div>
							            <div class="col txtder  txtDIGELASA">
							                <span class="text-muted">Estatus:</span>
							                <h6>Sin finalizar</h6>
							            </div>
							        </div>
							    </div>
							</div>
							';
							$scriptbusqueda=$scriptbusqueda. '$("#'.$anteriorgrupo.'").attr("databus","'.$cadenabusq.'");';



			} 

			
			$json['detalle']=$xdetalle;
		} 
		else {

			$json['msj']     = dbGetErrorMsg();
			$json['success'] = false;
		}
		$pietab          = '
				<script type="text/javascript">
					$(document).ready(function(){ $("#menug").load("menu.php"); });
				</script>	
				 <script type="text/javascript">
				 	$(document).ready(function() {
				 		'.$scriptbusqueda.'
						for(var i in cunicolist){
						   $("#"+cunicolist[i]).addClass("seleccionado");
						}
					});
				 </script>
				 ';
		$json['detalle'] = $enc_superior.' <div id="detallegrupo">'. $xdetalle . '</div>' . $pietab .'' . $conteoculto. ' ' . initws();
		//$json['detalle'] = minificado($enc_superior.' <div id="detallegrupo">'. $xdetalle . '</div>' . $pietab .'' . $conteoculto. ' ' . initws());
				 
		echo json_encode($json);
		break;




		//prueba
		
} 
function dbGetErrorMsg() // obtiene el mensaje devuelto por el controlador de DB cliente
	{
	$retVal = sqlsrv_errors();
	$retVal = $retVal[0]["message"];
	$retVal = ucfirst(strtolower(utf8_encode(preg_replace('/\\[Microsoft]\\[SQL Server Native Client [0-9]+.[0-9]+](\\[SQL Server\\])?/', '', $retVal))));
	return $retVal;
	}
function destruir($sessionVariableName)
	{
	unset($_SESSION[$sessionVariableName]);
	}
function cargaropciones()
	{
		$libs      = new db();
		$resultado = $libs->gettotaldoc();
		if ($resultado == true) {
			while ($obj = sqlsrv_fetch_object($resultado)) {
				$cantingresos  = $obj->INGRESOS;
				$cantdespachos = $obj->EGRESOS;
				$canttraslados = $obj->TRASLADOS;
				$cantarmados   = $obj->TARIMAS;
				$EGRESOSPR	   = $obj->EGRESOSPR;
				//$EXISTENCIAS   = $obj->EXISTENCIAS;
				$cantetiqueta  = 0;
			}
		} 
		else {
			$cantingresos  = '?';
			$cantdespachos = '?';
			$canttraslados = '?';
			$cantarmados   = '?';
			$EGRESOSPR	   = '?';
			$cantetiqueta  = '?';
			//$EXISTENCIAS ='?';
		}
		$html= ' 
		<div class="card col-12" style="align-items: center;" >
		  <img class="card-img-top" src="img\0001.png" alt="Card image cap" style="MAX-WIDTH: 45%;">
		  <div class="card-body appfull">
		    <h5 class="card-title">Seleccione el Tipo de Tarea</h5>
		    <p class="card-text">
		     <div class="form-group">';
		if ($_SESSION['idacceso']>=4){
			$html= $html.' 
		     	<button type="button" class="btn btn-secondary btn-lg btn-block optgrande btncargararmados pastel1" id="btncargararmados"  tipoop="TA"descripcion="Armado de tarimas" tipomov="EGRESO">
					<span class= "fas fa-boxes"></span> Armado de tarimas <span class="badge badge-light" id = "cantarmados">' . $cantarmados . '</span>
				</button>';
		}
		if ($_SESSION['idacceso']>=4){
			$html= $html.' 
				<button type="button" class="btn btn-secondary btn-lg btn-block optgrande btncargaringresos pastel2" id="btnrevisar" tipoop="TI" descripcion="Ingresos a Bodega" tipomov="EGRESO">
					<span class= "fas fa-pallet"></span> Ingresos a Bodega <span class="badge badge-light" id ="cantingresos">' . $cantingresos . '</span>
				</button>';
		}
		if ($_SESSION['idacceso']>=2){
			$html= $html.' 
				<button type="button" class="btn btn-primary btn-lg btn-block optgrande btncargaregresos pastel3" id="btnentregar"  tipoop="E0"descripcion="Egresos de bodega2" tipomov="EGRESO">
					<span class= "fas fa-dolly-flatbed"></span> Despachos de bodega <span class="badge badge-light" id = "cantdespachos">' . $cantdespachos . '</span>
				</button>';
		}
		if ($_SESSION['idacceso']>=3){
			$html= $html.' 
				<button type="button" class="btn btn-primary btn-lg btn-block optgrande btncargaregresos pastel3" id="btnentregar"  tipoop="PK" descripcion="Packing" tipomov="EGRESO">
					<span class= "fas fa-dolly-flatbed"></span> Packing <span class="badge badge-light" id = "cantdespachos">' . $cantdespachos . '</span>
				</button>';
		}
		if ($_SESSION['idacceso']>=4){
		$html= $html.' 
				<button type="button" class="btn btn-primary btn-lg btn-block optgrande btncargartraslado pastel4" id="btntraslado"  tipoop="TR"descripcion="Traslados entre bodegas" tipomov="EGRESO">
					<span class= "fas fa-exchange-alt"></span> Traslados entre bodegas <span class="badge badge-light" id = "canttraslados">' . $canttraslados . '</span>
				</button>';
		}
		if ($_SESSION['idacceso']>=4){
		$html= $html.' 
				<button type="button" class="btn btn-primary btn-lg btn-block optgrande btncargarrecodificado pastel4" id="btnrecodificado"  tipoop="RD"descripcion="Recodificado con Leyenda" tipomov="EGRESO">
					<span class= "fas fa-exchange-alt"></span> Recodificado con leyenda <span class="badge badge-light" id = "canttraslados">' .$EGRESOSPR. '</span>
				</button>';
		}
		if ($_SESSION['idacceso']>=1){
			$html= $html.' 
				<button type="button" class="btn btn-primary btn-lg btn-block optgrande btnetiquetado pastel4" id="btnetiquetado"  tipoop="RE" descripcion="Etiquetado" tipomov="EGRESO">
					<span class= "fas fa-exchange-alt"></span> Etiquetado <span class="badge badge-light" id = "canttraslados">' .$cantetiqueta. '</span>
				</button>';
		}
		if ($_SESSION['idacceso']>=4){
			$html= $html.' 
			<button type="button" class="btn btn-primary btn-lg btn-block optgrande btnexistencia pastel4" id="btnexistencia"  tipoop="SA" descripcion="Existencias" tipomov="EGRESO">
			<span class= "fas fa-exchange-alt"></span> Existencias en bodega<span class="badge badge-light" id = "canttraslados"></span>
		</button>';
		}
	

		$html= $html.' 

				<hr/>
				<button type="button" class="btn btn-danger btn-lg btn-block optgrande btnsalir" id= "btnsalir"><span class= "fas fa-times-circle"></span> Cerrar sesión
				</button>
			</div>
		  </div>
		</div>
		<div class="contenedor">
			<button class="btnrefresh botonF1">
			   <span class="fas fa-sync-alt"></span>
			</button>
		</div>
		<style type="text/css">
			.botonF1{
				width:60px;
				height:60px;
				border-radius:100%;
				background:#007bff;
				right:0;
				bottom:0;
				position:fixed;
				margin-right:16px;
				margin-bottom:16px;
				border:none;
				outline:none;
				color:#FFF;
				font-size:2em;
				box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23);
				transition:.3s;  
				/*background-image: url(uni.png)!important;
				background-position: center;*/
			}
			.animacionVer{
				transform:scale(1);
			}
		 </style>
		 <script type="text/javascript">
		 	$(document).ready(function() {
		 		
			 	$(".botonF1").hover(function(){
				  $(".btn2").addClass("animacionVer");
				})
			});
		 </script>
		';
		
		//return $minified.'original:'.$original.'|| minificado: '.$minificado.'|| reducción: '.$improvement;
		return minificado($html);
	}
function initws() // suscribe a websocket
	{
		$dato = 'Error en initws';
		if (isset($_SESSION['estado'])) {
			$dato = '
				<script type="text/javascript">	
					$(document).ready(function() { 
						if (typeof(socket)=="undefined"){
							console.log("WS creado");
					        websocket("' . $_SESSION['estado'] . '");
					    }
					    else{
					    	websocket("' . $_SESSION['estado'] . '");
					    }
					});
				</script>';
		}
		return $dato;
	}
function minificado($xhtmlx,$estadisticas=true){
	$original=  strlen($xhtmlx);
	$minified = PHPWee\PHPWee::html($xhtmlx);
	$minificado=  strlen($minified);
	$reduccion =  100 * (1-($minificado/$original));
	if ($estadisticas){
		$minified=$minified.'original:'.$original.'|| minificado: '.$minificado.'|| reducción: '.$reduccion;
	}
	//return $xhtmlx;
	return $minified;
}
function clonadox($variable){
	$xvar = $variable;
	return $xvar;
	}
?>

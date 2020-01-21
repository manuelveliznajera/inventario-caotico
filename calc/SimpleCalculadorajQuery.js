/*
 * jQuery SimpleCalculadora
 * @author dimti28@gmail.com - http://develoteca.com
 * @version 1.0
 * @date Julio 10, 2015
 * @category jQuery plugin
 * @copyright (c) 2015 dimti28@gmail.com (http://develoteca.com)
 * @license CC Attribution-NonCommercial-ShareAlike 3.0 Unported (CC BY-NC-SA 3.0) - http://creativecommons.org/licenses/by-nc-sa/3.0/
 */
 jQuery.fn.extend({Calculadora: function(op) {
					var LaCalculadora=this;
					var idInstancia=$(LaCalculadora).attr('id');
					var NombreBotonesClase=idInstancia+'tcl';
					var Clase;
    				var Botones;
					var Signos;
					
					defaults = {
						TituloHTML:'',
						Botones:["7","8","9","/","4","5","6","*","3","2","1","-","0",".","=","+"],
						Signos:["/", "*", "-", "+"],
						ClaseBtns1: 'white',
						ClaseBtns2: 'primary2',
						ClaseBtns3: 'primary2',
						ClaseColumnas:'col-3 col-md-3 col-xs-3 mbottom',
						ClaseBotones:'btn3d btn-lg btn-block btn btn-',
						txtSalida:idInstancia+'txtResultado',
						txttot:idInstancia+'txttot',
						ClasetxtSalida:'form-control txtr2',
						ClasetxtTotal:'form-control txtr',
						InputBorrar:idInstancia+'Borrar',
						ClaseInputBorrar:'btn3d btn btn-primary2 btn-lg btn-block',
						EtiquetaBorrar:'CE'
					}				
                    var op = $.extend({}, defaults, op);
					Botones=op.Botones;
					Signos=op.Signos;
                    $(LaCalculadora).append('<input type="text" class="'+op.ClasetxtSalida+'" id="'+op.txtSalida+'" value="0" >');
                    $(LaCalculadora).append('<input type="text" class="'+op.ClasetxtTotal+'" id="'+op.txttot+'" value="0"  disabled="true" >');
					$(LaCalculadora).append('<div class="row" id="'+idInstancia+'btns"></div>');					$.each(Botones, function(index,value) {	
						Clase=op.ClaseBtns1			
						if(Signos.indexOf(value)>-1){Clase=op.ClaseBtns2;}
						if(value=='='){Clase=op.ClaseBtns3;}
						$('#'+idInstancia+'btns').append('<div class="'+op.ClaseColumnas+'"><input type="button" class="'+NombreBotonesClase+' '+op.ClaseBotones+Clase+'" value="'+value+'"/></div>');
					});
					$(LaCalculadora).append('<div class="row"><input type="button" id="'+op.InputBorrar+'" class="'+op.ClaseInputBorrar+'" value="'+op.EtiquetaBorrar+'"></div>');
					$(LaCalculadora).html('<div class="panel panel-primary2 btn-block calculadoraBase  mtop">'+op.TituloHTML+'<div class="panel-body"><div class="col-md-12">'+$(LaCalculadora).html()+'</div></div> </div>');
					
					$('.'+NombreBotonesClase).click(function(){
						var vTecla=$(this).val();
						var salida=$('#'+op.txtSalida);
						var totales=$('#'+op.txttot);
						if(vTecla=='='){
							salida.val(eval(salida.val()));
							//LLENADO de la caja de texto que invoca.
							$("#modalcalc").modal('hide');
							$('#'+$("#objetollama").attr("idobjeto")).attr("value",salida.val());
							$('#'+$("#objetollama").attr("idobjeto")).change();
							salida.val(0.00000);
							totales.val(0.00000);
						}
						else{
							if((salida.val()==0)){
								if(Signos.indexOf(vTecla)>-1){
									salida.val(0.00000)
									totales.val(0.00000)
									}
								else{
									salida.val(vTecla);
									}
						}
						else{
							salida.val(salida.val()+vTecla);
							try {
								//if(vTecla=='=' ||  vTecla=='+' || vTecla=='-' || vTecla=='*' || vTecla=='/' ){
									totales.val(eval(salida.val()).toFixed(5));
								//}
							    
							}
							catch(err) {
							    console.log( err.message);
							}

							
							

							} 
						}
					});
					$("#"+op.InputBorrar).click(function(){
						$('#'+op.txtSalida).val("0");
						$('#'+op.txttot).val("0");
					});		
	}
});
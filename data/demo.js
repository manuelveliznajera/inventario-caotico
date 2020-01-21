$(document).ready(function(){
    $("#menug").load("menu.php"); 
    $("#menug").load("menu.php"); 
    $( "#txtBuscapedido" ).focusout(function() {
                var numero=0;
                numero = $( "#txtBuscapedido" ).val();
                 var codigo = parseInt(numero);
                    if(isNaN(codigo)){
                        alertify.error("Ingrese Una Posicion Valida");
                    }
                    else{

                 
                    
                console.log("focus out bien");
                    $.ajax({ async:true,
                    url:"controlador.php?action=40",
                    type: "post",
                    data: {
                    "codigobarra":codigo                                       
                           },
                    dataType: "json",
                    success: function(data) {
                        if (data.success == true) {
                            $("#contenidopg_oculto").html(data.encabezado);
                               $("#contenidopg").html(data.detalle);
                               limpiar();
                            
                                        if(data.msj=="POSICION VACIA"){
                                                                                //	alertify.error(data.msj);
                                            }else{
                                                console.log(data.msj);
                                                                                //	alertify.success(data.msj); 
                                                    }
                                                                                    
                                    } else {
                                            alertify.error("aqui en el ajax del 40");
                                            }
                    },
                    error: function(jqXHR, textStatus, error) {
                                //	alertify.error("INGRESE UNA POSICION VALIDA");
                                
                    }
                                                            
                    });
                }
            });

                                                
          
                function limpiar(){
                        
                                $("#txtBuscapedido").val(0);
                                $( "#txtBuscapedido" ).focus();
                                                          }
});	
	
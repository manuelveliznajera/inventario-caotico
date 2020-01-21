var barras = "";
var cuadromodalabierto = 0;
var corriendo = 0; //  ingresos a bodega
var corriendo2 = 0; // traslados entre bodega
var corriendo3 = 0; // Armado de tarimas
var corriendo4 = 0; // Despachos de bodega (picking)
var cunico = "";
var idposestante;
var idposestante2;
var codprod="";
var objetomover;
var ordentarea ;
var ordentarea2;
var idtraslado;
var enviotexto = 0;
var objetoxx="";
var ocultos="";
var codprod ="";
var socket;
var updt_inmediatamente=0;





$(function() {
    var ENV_WEBROOT = "../";
    var objeto;




    // funciones armados tarima
    $(document).off("click",".btncargararmados");
    $(document).on("click", ".btncargararmados",function(e) {
        var xtipoop = $(this).attr("tipoop"); //canal
        var xdescripcion = $(this).attr("descripcion");
        var xtipomov = $(this).attr("tipomov"); 
        $.ajax({ async:true,
            url: 'controlador.php?action=18',
            type: 'post',
            data: {
                'tipoop': xtipoop,
                'tipomov': xtipomov,
                'descripcion': xdescripcion
            },
            dataType: 'json',
            success: function(data) {
                if (data.success == true) {
                    //alertify.success(data.msj);                    
                    $("#contenidopg").html(data.detalle);
                } else {
                    alertify.error(data.msj);
                }
            },
            error: function(jqXHR, textStatus, error) {
                alertify.error(error);
            }
        });
    }); 
    $(document).off("click",".btncargartarea_tarima"); // sustituir por boton de selecciona detalle
    $(document).on("click",".btncargartarea_tarima", function(e) {
        var refingreso = $(this).attr("refingreso");
        ordentarea = $(this).attr("id");
        idposestante = $(this).attr("idposestante");
        codprod  = $(this).attr("codprod");
        objeto = $(this).clone();
        objetomover = $(this);
        $("#detalle_tarea4").html(objeto);
        $("#modal_detalletarea4").modal();
        //$("#txtingresobarra").val("") 
        //barras = "";
        $("#modal_detalletarea4").on("shown.bs.modal", function() {
            $("#btn_guardar_tarima").trigger("focus");

            cuadromodalabierto = 1;
            if (corriendo3 == 0) {
                lectura_tecladof3();
            }
            barras = "";
            
        });
        $("#modal_detalletarea4").on("hide.bs.modal", function() {
            cuadromodalabierto = 0;
            //cargapagina()
        });
    });
    //$(document).off("click",".btnexistencia");
    
   function codigoproductos(numero,xtipoop,xtipomov,xdescripcion){

   // console.log("Hola muchachos");
    $.ajax({ async:true,
        url: 'controlador.php?action=40',
        type: 'post',
        data: {
            'codigobarra':numero, 
            'tipoop': xtipoop,
            'tipomov': xtipomov,
            'descripcion': xdescripcion
        },
        dataType: 'json',
        success: function(data) {
            if (data.success == true) {
                $("#contenidopg_oculto").html(data.encabezado);
                $("#contenidopg").html(data.detalle);
                $( "#txtBuscapedido" ).focusout(function() {
                        
                    var numero = $( "#txtBuscapedido" ).val();
                    var codigo = parseInt(numero);
                    codigoproduc(codigo,xtipoop,xtipomov,xdescripcion);
                    console.log(codigo);
                    
                   });
               
            } else {
                alertify.error(data.msj);
            }
        },
        error: function(jqXHR, textStatus, error) {
            alertify.error(error);
        }
    });
   }
    
    
   function codigoproduc(numero,xtipoop,xtipomov,xdescripcion){

   // console.log("Hola muchachos");
    $.ajax({ async:true,
        url: 'controlador.php?action=40',
        type: 'post',
        data: {
            'codigobarra':numero, 
            'tipoop': xtipoop,
            'tipomov': xtipomov,
            'descripcion': xdescripcion
        },
        dataType: 'json',
        success: function(data) {
            if (data.success == true) {
                $("#contenidopg_oculto").html(data.encabezado);
                $("#contenidopg").html(data.detalle);
                $( "#txtBuscapedido" ).focusout(function() {
                        
                    var numero = $( "#txtBuscapedido" ).val();
                    var codigo = parseInt(numero);
                    codigoproductos(codigo,xtipoop,xtipomov,xdescripcion);
                    console.log(codigo);
                    
                   });

            } else {
                alertify.error(data.msj);
            }
        },
        error: function(jqXHR, textStatus, error) {
            alertify.error(error);
        }
    });
   }
 /*  $(document).ready(function(){
    $("#txtBuscapedido").keypress(function(e) {
        //no recuerdo la fuente pero lo recomiendan para
        //mayor compatibilidad entre navegadores.
        var code = (e.keyCode ? e.keyCode : e.which);
        if(code==13){
            obtenerDatos();
        }
    });
});*/
      $(document).off("click","#btnexistencia");
    $(document).on("click", "#btnexistencia", function(e){
        let codigoBarra = 0;
        console.log(codigoBarra);
        var xtipoop = $(this).attr("tipoop");
        var xdescripcion = $(this).attr("descripcion");
        var xtipomov = $(this).attr("tipomov");
        $.ajax({ async:true,
            url: 'controlador.php?action=40',
            type: 'post',
            data: {
                'codigobarra':codigoBarra,
                'tipoop': xtipoop,
                'tipomov': xtipomov,
                'descripcion': xdescripcion
              
            },
            dataType: 'json',
            success: function(data) {
                if (data.success == true) {
                 //   $("#contenidopg_oculto").html(data.encabezado);
                    $("#contenidopg").html(data.detalle);
                    wsenviarcambio(1);
                  //  wsenviarcambio(1);
                    $( "#txtBuscapedido" ).focusout(function() {
                        
                        var numero = $( "#txtBuscapedido" ).val();
                        var codigo = parseInt(numero);
                        codigoproductos(codigo,xtipoop,xtipomov,xdescripcion);
                        console.log(codigo);
                        
                       });

                       $("#txtBuscapedido").keypress(function(e) {
                        //no recuerdo la fuente pero lo recomiendan para
                        //mayor compatibilidad entre navegadores.
                        var code = (e.keyCode ? e.keyCode : e.which);
                        var numero = $( "#txtBuscapedido" ).val();
                        var codigo = parseInt(numero);
                        if(code==13){
                            codigoproductos(codigo,xtipoop,xtipomov,xdescripcion);
                            console.log(codigo);
                        }
                    });
                    
                   
                } else {
                    alertify.error(data.msj);
                }
            },
            error: function(jqXHR, textStatus, error) {
                alertify.error(error);
            }
        });

        
        

      function obtenercodigoexistencia(){

        $("#txtBuscapedido").focusout(function() {
						
						
        });
      }
    });


    $(document).off("click","#btn_guardar_detalle_tarima");
    $(document).on("click","#btn_guardar_detalle_tarima", function(e) {
        //enviotexto=1;
        $.ajax({ async:true,
            url: 'controlador.php?action=19',
            type: 'post',
            data: {
                'ordentarea': ordentarea,
                'codprod': codprod
            },
            dataType: 'json',
            success: function(data) {
                if (data.success == true) {
                    $("#modal_detalletarea4").modal('hide');
                    objetomover.toggle("slow");
                    wsenviarcambio(1);
                } else {
                    alertify.error(data.msj);
                }
            },
            error: function(jqXHR, textStatus, error) {
                alertify.error(error);
            }
        });
    });
    
    // fin funciones armados tarima

    // funciones de TRASLADOS
    $(document).off("click",".btncargartraslado");
    $(document).on("click",".btncargartraslado", function(e) {
        var xtipoop = $(this).attr("tipoop");
        var xdescripcion = $(this).attr("descripcion");
        var xtipomov = $(this).attr("tipomov");
        $.ajax({ async:true,
            url: 'controlador.php?action=17',
            type: 'post',
            data: {
                'tipoop': xtipoop,
                'tipomov': xtipomov,
                'descripcion': xdescripcion
            },
            dataType: 'json',
            success: function(data) {
                if (data.success == true) {
                    $("#contenidopg").html(data.detalle);
                    $("#contenidopg_oculto").html(data.encabezado);
                    

                } else {
                    alertify.error(data.msj);
                }
            },
            error: function(jqXHR, textStatus, error) {
                alertify.error(error);
            }
        });
    });
    // funciones etiquetado
    
    $(document).off("click",".btnetiquetado");
    $(document).on("click",".btnetiquetado", function(e) {
        var xtipoop = $(this).attr("tipoop");
        var xdescripcion = $(this).attr("descripcion");
        var xtipomov = $(this).attr("tipomov");
        $.ajax({ async:true,
            url: 'controlador.php?action=36',
            type: 'post',
            data: {
                'tipoop': xtipoop,
                'tipomov': xtipomov,
                'descripcion': xdescripcion
            },
            dataType: 'json',
            success: function(data) {
                if (data.success == true) {
                    $("#contenidopg").html(data.detalle);
                    $("#contenidopg_oculto").html(data.encabezado);
                } else {
                    alertify.error(data.msj);
                }
            },
            error: function(jqXHR, textStatus, error) {
                alertify.error(error);
            }
        });
    });

    $(document).off("click",".btndetalleetiquetado");
    $(document).on("click",".btndetalleetiquetado", function(e) {
        correl = $(this).attr("correl");
        codigosale= $(this).attr("codigosale");
        lote = $(this).attr("lote");
        $(this).clone().prependTo("#detalle_tareaetiquetado");
        $("#modal_detalletareaetiquetado").modal();
        //agregando info a procesar a lote
        $("#cmdconfirmaetiqueta").attr("correl",correl);
        $("#cmdconfirmaetiqueta").attr("codigosale",codigosale);
        $("#cmdconfirmaetiqueta").attr("lote",lote);
        if (corriendo2 == 0) {
            lectura_tecladof2();
        }
        barras = "";
        $("#modal_detalletareaetiquetado").on("shown.bs.modal", function() {
            $("#cmdconfirmaetiqueta").trigger("focus");
            cuadromodalabierto=1;
        });
        $("#modal_detalletareaetiquetado").on("hide.bs.modal", function() {
            cuadromodalabierto=0;
            $("#detalle_tareaetiquetado").html("");
           // cargapagina();
        });
    });
    // enviar tarea de etiqueta
    $(document).off("click","#cmdconfirmaetiqueta");
    $(document).on("click","#cmdconfirmaetiqueta", function(e) {
        correl = $(this).attr("correl");
        codigosale= $(this).attr("codigosale");
        lote = $(this).attr("lote");
        $.ajax({ async:true,
            url: 'controlador.php?action=37',
            type: 'post',
            data: {
                'correl': correl,
                'codigosale': codigosale,
                'lote': lote
            },
            dataType: 'json',
            success: function(data) {
                if (data.success == true) {
                    $("#modal_detalletareaetiquetado").modal('hide');
                    $("#detalle_tareaetiquetado").html("");
                    cargapagina();

                } else {
                    alertify.error(data.msj);
                }
            },
            error: function(jqXHR, textStatus, error) {
                alertify.error(error);
            }

        });
    });
    
    // funcion de codificado
    $(document).off("click",".btncargarrecodificado");
    $(document).on("click",".btncargarrecodificado", function(e) {
        var xtipoop = $(this).attr("tipoop");
        var xdescripcion = $(this).attr("descripcion");
        var xtipomov = $(this).attr("tipomov");
        $.ajax({ async:true,
            url: 'controlador.php?action=34',
            type: 'post',
            data: {
                'tipoop': xtipoop,
                'tipomov': xtipomov,
                'descripcion': xdescripcion
            },
            dataType: 'json',
            success: function(data) {
                if (data.success == true) {
                    $("#contenidopg").html(data.detalle);
                    $("#contenidopg_oculto").html(data.encabezado);
                } else {
                    alertify.error(data.msj);
                }
            },
            error: function(jqXHR, textStatus, error) {
                alertify.error(error);
            }
        });
    });
    //
    $(document).off("click","#btngenerartareasetiquetado");
    $(document).on ("click","#btngenerartareasetiquetado", function(e) {
        //var xtipomov = $(this).attr("tipomov");
        $.ajax({ async:true,
            url: 'controlador.php?action=35',
            type: 'post',
            data: {
                'descripcion': 'generación de tareas'
            },
            dataType: 'json',
            success: function(data) {
                if (data.success == true) {
                    location.reload();
                } else {
                    alertify.error(data.msj);
                }
            },
            error: function(jqXHR, textStatus, error) {
                alertify.error(error);
            }
        });
    });



    $(document).off("click",".btncargartareatraslado_detalle");
    $(document).on("click",".btncargartareatraslado_detalle", function(e) {
        ordentarea = $(this).attr("ordentarea");
        ordentarea2= $(this).attr("ordentarea2");
        idposestante = $(this).attr("idposestante");
        idposestante2 = $(this).attr("idposestante2");
        idtraslado = $(this).attr("idtraslado");
        objetoxx = $(".hijo" + idtraslado).clone();
        paso= $(this).attr("paso");
        $("#detalle_tarea3").html(objetoxx);
        $("#modal_detalletarea3").modal();
        if (corriendo2 == 0) {
            lectura_tecladof2();
        }
        barras = "";
        $("#modal_detalletarea3").on("shown.bs.modal", function() {
            $("#btndetalle_traslado").trigger("focus");
            cuadromodalabierto=1;
        });
        $("#modal_detalletarea3").on("hide.bs.modal", function() {
            cuadromodalabierto=0;
            $("#detalle_tarea3").html("");
        });
    });
    $(document).off("click",".btndetalle_traslado");
    $(document).on("click", ".btndetalle_traslado",function(e) {
        objetodesa=  $(this);
        ordentarea = $(this).attr("id");
        refsalida=   $(this).attr("refsalida");
        refingreso=  $(this).attr("refingreso");
        idtraslado=  $(this).attr("idtraslado");
        tipomov=     $(this).attr("tipomov");
        codprod=     $(this).attr("codprod");
        paginafunc= "0";
        paso= $(this).attr("paso");
        if (refsalida=="0" && paso=="0"){
            alertify.error("Complete la tarea de salida antes de continuar");
            return 0;}
        if (refsalida!=0)
            {paginafunc= "16";}
        else
            {paginafunc= "2";}
            alertify.confirm('Finalizar proceso', '¿Confirmar desea completar el paso actual?', function(){
            $.ajax({ async:true,
                url: 'controlador.php?action='+paginafunc,
                type: 'post',
                data: {
                    'ordentarea': ordentarea,
                    'codprod':codprod
                },
                dataType: 'json',
                success: function(data) {
                    if (data.success == true) {
                        $(".hijo"+idtraslado+tipomov).addClass("desabilitado");
                        $(".hijo"+idtraslado).attr("paso",String(parseInt(paso)+1));
                        $(".padre"+idtraslado).attr("paso",String(parseInt(paso)+1));
                        // verificar si es la ultima tarea: cerrar y eliminar tarea padre
                        if((parseInt(paso)+1)>=2){
                            $("#modal_detalletarea3").modal('hide');
                            $(".padre"+idtraslado).toggle("slow");
                            // fin verificar tarea padre
                        }
                        wsenviarcambio(1);
                        alertify.success(data.msj);} 
                        else {
                        alertify.error(data.msj);}
                },
                error: function(jqXHR, textStatus, error) {
                    alertify.error(error);
                }
            }, function() {
                alertify.success("funcion");
            });
            }
            ,function(){
            
            }
            ).setting({
            'labels': {ok: 'Si',cancel: 'No, continuar con este proceso'},
            'defaultFocus': 'cancel',
            transition:'zoom',
            'closableByDimmer': true
        });
    });
    // fin funciones TRASLADOS

    // funciones de egresos
    $(document).off("click",".btncargaregresos");
    $(document).on("click",".btncargaregresos", function(e) {
        var xtipoop = $(this).attr("tipoop");
        var xdescripcion = $(this).attr("descripcion");
        var xtipomov = $(this).attr("tipomov");
        switch(xtipoop) {
          case 'E0':
            xpaginaconsulta='99'; // proceso que muestra grupos de despacho
            break;
          case 'PK':
            xpaginaconsulta='32'; // encabezados de Paking
            break;
          default:
            xpaginaconsulta='14'; // encabezados de piking
        }
        //websocket(xtipoop);
        $.ajax({ async:true,
            url: 'controlador.php?action=99',
            type: 'post',
            data: {
                'tipoop': xtipoop,
                'tipomov': xtipomov,
                'descripcion': xdescripcion
            },
            dataType: 'json',
            success: function(data) {
                if (data.success == true) {
                    //alertify.success(data.msj);                    
                    $("#contenidopg").html(data.detalle);
                } else {
                    alertify.error(data.msj);
                }
            },
            error: function(jqXHR, textStatus, error) {
                alertify.error(error);
            }
        });
    });
    
    $(document).off("click",".btncargartarea_nuevogrupo");
    $(document).on("click",".btncargartarea_nuevogrupo", function(e) {
        var xtipoop = 'E1';
        var xdescripcion = 'Egresos de bodega';
        var xtipomov ='Egreso';
        clasempresa = 'DIGELASA';
        $.ajax({ async:true,
            url: 'controlador.php?action=25',
            type: 'post',
           data: {
                'tipoop': xtipoop,
                'tipomov': xtipomov,
                'descripcion': xdescripcion,
                'esnuevogrupo':1
            },
            dataType: 'json',
            success: function(data) {
                if (data.success == true) {
                    //alertify.success(data.msj);                    
                    $("#contenidopg").html(data.detalle);
                } else {
                    alertify.error(data.msj);
                }
            },
            error: function(jqXHR, textStatus, error) {
                alertify.error(error);
            }
        });
        barras = "";
    }); 

    // funcion para boton de  cargar tareas seleccionadas en facturas de egreso (boton de carrito)
    $(document).off("click",".btncargartareas_egreso");
    $(document).on("click",".btncargartareas_egreso", function(e) {
        $.ajax({ async:true,
            url: 'controlador.php?action=21',
            type: 'post',
            data: {
                'cunicolist': JSON.stringify(cunicolist),
                'clasempresa': clasempresa
            },
            dataType: 'json',
            success: function(data) {
                if (data.success == true) {
                    $("body").removeClass("bgINFASA");
                    $("body").removeClass("bgDIGELASA");
                    $("body").addClass("bg" + clasempresa);
                    $("#contenidopg").html(data.detalle);
                } else {
                    alertify.error(data.msj);
                }
            },
            error: function(jqXHR, textStatus, error) {
                alertify.error(error);
            }
        });
        barras = "";
    }); 

    // tarea de marcar facturas de egreso a enviar en carrito [función obsoleta. ahora controlado por php]
    $(document).off("click",".btncargartarea_egreso_marcar");
    $(document).on("click",".btncargartarea_egreso_marcar", function(e) {
        idDetAgrupa= $(this).attr("idDetAgrupa");
        cunico = $(this).attr("id");
        pedido = $(this).attr("pedido");
        $("#lista_contenedores").load("detallecontenedores.php",{'cunico':cunico, 'pedido':pedido,  'idDetAgrupa': idDetAgrupa});
        $("#modalcontenedor").modal();
        $("#modalcontenedor").on("shown.bs.modal", function() {
            $("#controlcontenedor").trigger("focus");
        });        
    });
    // sustituido por opción de cargar btncargar_grupos
    $(document).off("click",".btncargartarea_detalle");
    $(document).on("click",".btncargartarea_detalle", function(e) {
        ordentarea = $(this).attr("id");
        picker= $(this).attr("picker");
        if (picker==1){
            codprod= 0;   
        }
        else{
            codprod= $(this).attr("codprod");
        }
        objeto = $(this).clone();
        objeto.removeClass("margen15");
        objetomover = $(this);
        $("#detalle_tarea2").html(objeto);
        $("#modal_detalletarea2").modal();
        barras = "";
        //alertify.error("procedimiento de check");
        $("#modal_detalletarea2").on("shown.bs.modal", function() {
            $("#btn_guardar_posicion").trigger("focus");
            cuadromodalabierto = 1;
        });
        $("#modal_detalletarea2").on("hide.bs.modal", function() {
            cuadromodalabierto = 0;
            objeto.remove();
            //cargapagina()
        });
    });

    $(document).off("click",".btncargar_grupos");
    $(document).on("click",".btncargar_grupos", function(e) {
        if (cuadromodalabierto==0){
            $taked = $(this).attr("taked");
            $lote = $(this).attr("lote");
            $codprod= $(this).attr("codprod");
            idposestante= $(this).attr("idposestante");
            objetop= $(this).clone();
            objeto = $(".hijo"+$lote+$codprod).clone();
            objeto.addClass("hijoenmodal");
            objeto.removeClass("margen15");
            objetop.removeClass("margen15");
            objetomover = $(this);
            $("#detallegrupo").html('');
            $("#detallegrupo").prepend(objetop);
            $("#detallegrupo").append(objeto);
            $("#modal_detallegrupo").modal();
            // control si tacked esta habilitado desaparecer control
            if ($taked==1){
                $("#btn_guardar_detallegrupo").addClass("desabilitado")
            }
            else{
                $("#btn_guardar_detallegrupo").removeClass("desabilitado")
            }
            $("#btn_guardar_detallegrupo").attr("lote",$lote+$codprod)
            barras = "";
            //alertify.error("procedimiento de check");
            $("#modal_detallegrupo").on("shown.bs.modal", function() {
                $("#btn_guardar_posicion").trigger("focus");
                cuadromodalabierto = 1;
                if (corriendo4 == 0) {
                    lectura_tecladof4();
                }
                 barras = "";
            });
            $("#modal_detallegrupo").on("hide.bs.modal", function() {
                cuadromodalabierto = 0;
                objetop.remove();
                objeto.remove();
                //cargapagina()
            });    
        }
    });


    $(document).off("click","#btn_guardar_detalle");
    $(document).on("click","#btn_guardar_detalle", function(e) {
        $.ajax({ async:true,
            url: 'controlador.php?action=20',
            type: 'post',
            data: {
                'ordentarea': ordentarea,
                'codprod': codprod
            },
            dataType: 'json',
            success: function(data) {
                if (data.success == true) {
                    if(data.deshabilitar==true){
                        // agregar clase de desabilitar
                        objetomover.addClass("pickeado");
                        objetomover.attr("picker",1);
                    }
                    if(data.deshabilitar==false){
                        // quitar clase de desabilitar
                        objetomover.removeClass("pickeado");
                        objetomover.attr("picker",0);
                    }
                    $("#modal_detalletarea2").modal('hide');
                    wsenviarcambio();
                } else {
                    alertify.error(data.msj);
                }
            },
            error: function(jqXHR, textStatus, error) {
                alertify.error(error);
            }
        });
    });

    /************************* funcion guardar detalle de grupo***************/
    $(document).off("click","#btn_guardar_detallegrupo");
    $(document).on("click","#btn_guardar_detallegrupo", function(e) {
        var $lote= $(this).attr("lote")
        cuadromodalabierto= 1;
        $(".hijoenmodal").each(function(indice,elemento){
            //console.log(ordentarea);
            objetomover = $(elemento);
            ordentarea  = $(elemento).attr("ordentarea");
            codigopro   = $(elemento).attr("codprod");
            taked       = 1;
            carreta     = $(elemento).attr("carreta");
            posicion    = $(elemento).attr("posicion");
            cunico    = $(elemento).attr("cunico");
            objeto
            //console.log(ordentarea);
            $.ajax({ async:false,
                url: 'controlador.php?action=30',
                type: 'post',
                data: {
                    'ordentarea': ordentarea,
                    'codprod': codigopro,
                    'taked': taked,
                    'carreta': carreta,
                    'posicion':posicion,
                    'cunico':cunico
                },
                dataType: 'json',
                success: function(data) {
                    if (data.success == true) {
                        // desabilitar padre 
                        $(".padre"+$lote).addClass("pickeado");
                        //colocar clase de desabilitado
                        $(elemento).addClass("desabilitado");
                        //cambiar estado de tacked
                        $(elemento).attr("taked","1");
                        $(".padre"+$lote).attr("taked","1");
                        $("#modal_detallegrupo").modal('hide');
                        wsenviarcambio();
                    } else {
                        alertify.error(data.msj);
                    }
                },
                error: function(jqXHR, textStatus, error) {
                    alertify.error(error);
                }
            });
        });
    });

    // sustituido por boton : btn_finalizargrupo
    $(document).off("click","#btn_enviar_detalle");
    $(document).on("click", "#btn_enviar_detalle",function(e) {
        cuadromodalabierto= 1;
        if (verificatareas()==1){
            $("html, body").animate({ scrollTop: 0 }, "slow");
            $(".btncargartarea_detalle").each(function(indice,elemento){
                objetomover = $(elemento);
                ordentarea  = $(elemento).attr("id");
                picker      = $(elemento).attr("picker");
                if (picker==1){ codprod= 0; }
                else{ codprod= $(elemento).attr("codprod"); }
                 $.ajax({ async:false,
                    url: 'controlador.php?action=16',
                    type: 'post',
                    data: {
                        'ordentarea': ordentarea,
                        'codprod': codprod
                    },
                    dataType: 'json',
                    success: function(data) {
                        if (data.success == true) {
                            objetomover.toggle("slow");
                        } else {
                            alertify.error(data.msj);
                        }
                    },
                    error: function(jqXHR, textStatus, error) {
                        alertify.error(error);
                    }
                });
                wsenviarcambio();
                alertify.alert().setting({'title':'Egresos de bodega','label':'Continuar','message': 'Se a completado el proceso, sera redirigido al menú principal ', 'defaultFocus': 'ok' , 'onok': function(){ cuadromodalabierto= 0; salir_a_menu(); } }).show();
            });
        }
        else{
            alertify.error("Complete todas los egresos de la tarea para poder enviar este documento");
        }
    });
     // sustituido por boton : btn_finalizargrupo
    $(document).off("click","#btn_finalizargrupo");
    $(document).on("click","#btn_finalizargrupo", function(e) {
        cuadromodalabierto= 1;
        alertify.alert().setting({'title':'Egresos de bodega',
            'label':'Finallizar Grupo',
            'message': 'Al completar el grupo, esta dejara de ser visible para su usuario.¿Desea continuar? ', 
            'defaultFocus': 'ok' , 
            'onok': function()
                { 
                cuadromodalabierto= 0;
                finalizagrupo();

                } 
            }).show();             
    });
    $(document).off("click","#btn_finalizarpedido");
    $(document).on("click","#btn_finalizarpedido", function(e) {
        cuadromodalabierto= 1;
        console.log("función en proceso de desarrollo.");
    });
    // fin funciones egresos

    // funciones ingresos
    $(document).off("click",".btncargaringresos");
    $(document).on("click", ".btncargaringresos",function(e) {
        var xtipoop = $(this).attr("tipoop");
        var xdescripcion = $(this).attr("descripcion");
        var xtipomov = $(this).attr("tipomov");
        $.ajax({ async:true,
            url: 'controlador.php?action=3',
            type: 'post',
            data: {
                'tipoop': xtipoop,
                'tipomov': xtipomov,
                'descripcion': xdescripcion
            },
            dataType: 'json',
            success: function(data) {
                if (data.success == true) {
                    //alertify.success(data.msj);                    
                    $("#contenidopg").html(data.detalle);
                } else {
                    alertify.error(data.msj);
                }
            },
            error: function(jqXHR, textStatus, error) {
                alertify.error(error);
            }
        });
    });
//para existencias

   
    $(document).off("click",".btncargartarea");
    $(document).on("click",".btncargartarea", function(e) {
        if (corriendo == 0) {
            lectura_tecladof1();
        }
        var refingreso = $(this).attr("refingreso");
        ordentarea = $(this).attr("id");
        idposestante = $(this).attr("idposestante");
        codprod  = $(this).attr("codprod");
        objeto = $(this).clone();
        objetomover = $(this);
        $("#detalle_tarea").html(objeto);
        $("#modal_detalletarea").modal();
        //$("#txtingresobarra").val(idposestante)  
        barras = "";
        $("#modal_detalletarea").on("shown.bs.modal", function() {
            $("#btn_guardar_posicion").trigger("focus");
            cuadromodalabierto = 1;
        });
        $("#modal_detalletarea").on("hide.bs.modal", function() {
            cuadromodalabierto = 0;
            //cargapagina()
        });
    });
    $(document).off("click","#btn_guardar_posicion");
    $(document).on("click","#btn_guardar_posicion", function(e) {
        if ($("#txtingresobarra").val() != "") {
            enviotexto = 1;
            barras = $("#txtingresobarra").val();
        }
        if ((barras != "")) {
            $(document).keypress();
        }
    });
    //  LOGIN
    $(document).off("click","#btnidusuarioinicia");
    $(document).on("click", "#btnidusuarioinicia",function(e) {
        var xusuario = $("#idusuarioinicia").val();
        if (xusuario != '') {
            $.ajax({ async:true,
                url: 'controlador.php?action=5',
                type: 'post',
                data: {
                    'xusuario': xusuario
                },
                dataType: 'json',
                success: function(data) {
                    if (data.success == true) {
                        $("#contenidopg").html(data.detalle);
                        alertify.alert("Preparación de ordenes", data.msj);
                    } else {
                        alertify.error(data.msj)
                    }
                },
                error: function(jqXHR, textStatus, error) {
                    alertify.error(error);
                }
            });
        } else {
            alertify.alert('Ingrese un código valido')
        }
    });
    $(document).off("enterKey", "#idusuarioinicia");
    $(document).on("enterKey", "#idusuarioinicia",function(e) {
        $("#btnidusuarioinicia").click();
    });
    $(document).off("keyup",'#idusuarioinicia');
    $(document).on("keyup",'#idusuarioinicia',function(e) {
        if (e.keyCode == 13) {
            $(this).trigger("enterKey");
        }
    });
    //  FIN LOGIN
    
    // FUNCIONAMIENTO DE MENÚ   
    $(document).off("click",".links_menu");
    $(document).on("click",".links_menu", function(e) {
        consultar_tarea();
        var optmenu = $(this).attr("id");
        //console.log(optmenu);
        if (optmenu == "mnfullscreen") {
            openFullscreen();
        } else {
            $.ajax({ async:true,
                url: 'controlador.php?action=8',
                type: 'post',
                data: {
                    'optmenu': optmenu
                },
                dataType: 'json',
                success: function(data) {
                    if (data.success == true) {
                        location.reload();
                    } else {
                        alertify.confirm('Proceso sin completar', 'Hay un proceso pendiente, Desea cancelar la preparación?', function() {
                            salir_a_menu();
                            }, function() {
                                //console.log("Continue agregando datos")
                                }).setting({
                                'labels': {
                                    ok: 'Si',
                                    cancel: 'No, Seguir con preparación!'
                                },
                                'defaultFocus': 'cancel'
                        });
                    }
                },
                error: function(jqXHR, textStatus, error) {
                    alertify.error(error);
                }
            });
        }
        $('.navbar-collapse').collapse('hide');
    });
    $(document).off("click","#btnseleop");
    $(document).on("click","#btnseleop", function(e) {
        $("#mnseleop").click();
    });
    $(document).off("click",".btnsalir");
    $(document).on("click",".btnsalir", function(e) {
        $.ajax({ async:true,
            url: 'controlador.php?action=7',
            type: 'post',
            data: {
                'x': 0
            },
            dataType: 'json',
            success: function(data) {
                location.reload();
            },
            error: function(jqXHR, textStatus, error) {
                alertify.error(error);
            }
        });
    });
    $(document).off("click",".btnrefresh");
    $(document).on("click",".btnrefresh", function(e) {
        //consultar_tarea();
        location.reload();
        consultar_tarea();
    });
// boton en obtener codigo de producto


    $(document).off("click","#btnnuevogrupo");
    $(document).on("click","#btnnuevogrupo", function(e) {
        var $modelo = $("#modelogruponuevo").clone(true);
        var $contenedor= $("#detallegrupo");
        $.ajax({ async:true,
            url: 'controlador.php?action=23',
            type: 'post',
            data: {
                'x': 0
            },
            dataType: 'json',
            success: function(data) {
                 if (data.success == true) {
                    alertify.success(data.msj);
                    //console.log(data.grupo);
                     $contenedor.prepend($modelo);
                     setTimeout(function() 
                      {
                        $(".btncargartarea_nuevogrupo").trigger("click") ;
                      }, 500)
                    
                } else {
                    alertify.error(data.msj);
                }
            },
            error: function(jqXHR, textStatus, error) {
                alertify.error(error);
                //console.log();
            }
        });
    });
    $(document).off("click",".btncargartarea_seleccionagrupo");
    $(document).on("click",".btncargartarea_seleccionagrupo", function(e) {
        var $idagrupa = $(this).attr("idagrupa");
        var xtipoop = 'E2';
        var xdescripcion = 'Egresos de bodega';
        var xtipomov ='Egreso';
        clasempresa = 'DIGELASA';
        $.ajax({ async:true,
            url: 'controlador.php?action=25',
            type: 'post',
           data: {
                'grupopicking': $idagrupa,
                'tipoop': xtipoop,
                'tipomov': xtipomov,
                'descripcion': xdescripcion,
                'esnuevogrupo':0
            },
            dataType: 'json',
            success: function(data) {
                if (data.success == true) {
                    //alertify.success(data.msj);                    
                    $("#contenidopg").html(data.detalle);
                } else {
                    alertify.error(data.msj);
                }
            },
            error: function(jqXHR, textStatus, error) {
                alertify.error(error);
            }
        });
        barras = "";
    });
    
    $(document).off("keyup","#txtBuscapedido");
    $(document).on("keyup","#txtBuscapedido", function(e) {
        filtradivs("btncargartarea_seleccionagrupo",$(this).val(),['databus'])
    });
    // detalle de packing
    $(document).off("click",".btncargartarea_seleccionapedido");
    $(document).on("click",".btncargartarea_seleccionapedido", function(e) {
        var $idpedido = $(this).attr("idpedido");
        $.ajax({ async:true,
            url: 'controlador.php?action=33', // selecciona grupo
            type: 'post',
            data: {'pedidopacking': $idpedido},
            dataType: 'json',
            success: function(data) {
                if (data.success == true) {
                    //alertify.success(data.msj);                    
                    $("#contenidopg").html(data.detalle);
                } else {
                    alertify.error(data.msj);
                }
            },
            error: function(jqXHR, textStatus, error) {
                alertify.error(error);
            }
        
        });
    });
    
    $(document).off("keyup","#txtBuscapedido2");
    $(document).on("keyup","#txtBuscapedido2", function(e) {
        filtradivs("btncargartarea_seleccionapedido",$(this).val(),['databus'])
    });

    //PRUEBA
    
    // FIN FUNCIONAMIENTO DE MENÚ
});

function enviarcunicolist($xculist) {
    //console.log($xculist.toString());
    $.ajax({ async:true,
        url: 'controlador.php?action=24', // almacenar en 
        type: 'post',
        data: {'cunicolist':$xculist.toString()},
        dataType: 'json',
        success: function(data) {
            if (data.success == true) {
                alertify.success(data.msj);
            }
        }
    });
}
function grupoegreso($cunico, $pedido) {
    ////console.log("Asignando factura a grupo" + $cunico);
    $valretorno= 0;
    $.ajax({ async:false,
        url: 'controlador.php?action=26', // almacenar en 
        type: 'post',
        data: {'cunico':$cunico, 'pedido':$pedido},
        dataType: 'json',
        success: function(data) {
            if (data.success == true) {
                alertify.success(data.msj);
                //console.log("generado dede grupoegreso", data.idDetAgrupa);
                $valretorno= data.idDetAgrupa;
            }
        }
    });
    return $valretorno;
}

function lectura_tecladof1() {
    console.log("ejecucion de captura teclado 1");
    corriendo = 1;
    $(document).off("keypress",document);
    $(document).on("keypress",function(e) {
        if (cuadromodalabierto == 1) {
            if (e.which == 13 || enviotexto == 1) {
                console.log("barras: " + barras);
                if ($.trim(idposestante) == $.trim(barras).padStart(5,"0")) {
                    $.ajax({ async:true,
                        url: 'controlador.php?action=2',
                        type: 'post',
                        data: {
                            'ordentarea': ordentarea,
                            'codprod':codprod
                        },
                        dataType: 'json',
                        success: function(data) {
                            if (data.success == true) {
                                $("#modal_detalletarea").modal('hide');
                                objetomover.toggle("slow");
                                alertify.success(data.msj);
                                wsenviarcambio(1);
                            } else {
                                alertify.error(data.msj);
                            }
                        },
                        error: function(jqXHR, textStatus, error) {
                            alertify.error(error);
                        }
                    });
                    /* fin ajax */
                } else {
                    alertify.error("La posición seleccionada no corresponde");
                }
                barras = "";
                $("#txtingresobarra").val("");
                enviotexto = 0;
            } else {
                barras = barras + e.key;
            }
        }
    });
}
function lectura_tecladof2() {
    corriendo2 = 1;
    $(document).off("keypress",document);
    $(document).on("keypress",function(e) {
        if (cuadromodalabierto == 1) {
            if (e.which == 13  || enviotexto == 1) {
                if (($.trim(idposestante) == $.trim(barras).padStart(5,"0"))||($.trim(idposestante2) == $.trim(barras).padStart(5,"0"))) 
                {
                    if (($.trim(idposestante) == $.trim(barras).padStart(5,"0")))
                        {
                            paginafunc= "16";
                            ordentarea=ordentarea;
                            tipomov="E";
                         } 
                    if (($.trim(idposestante2) == $.trim(barras).padStart(5,"0")))
                        {
                            if (paso=="0"){
                                alertify.error("Complete la tarea de salida antes de continuar");
                                return 0;
                            }
                            paginafunc= "2";
                            ordentarea=ordentarea2;
                            tipomov="I";
                        } 
                        codprod=     $(".hijo"+idtraslado+tipomov).attr("codprod");
                    $.ajax({ async:true,
                        url: 'controlador.php?action='+paginafunc,
                        type: 'post',
                        data: {
                            'ordentarea': ordentarea,
                            'codprod':codprod
                        },
                        dataType: 'json',
                        success: function(data) {
                            if (data.success == true) {
                                $(".hijo"+idtraslado+tipomov).addClass("desabilitado");
                                $(".hijo"+idtraslado).attr("paso",String(parseInt(paso)+1));
                                $(".padre"+idtraslado).attr("paso",String(parseInt(paso)+1));
                                // verificar si es la ultima tarea: cerrar y eliminar tarea padre
                                if((parseInt(paso)+1)>=2){
                                    $("#modal_detalletarea3").modal('hide');
                                    $(".padre"+idtraslado).toggle("slow");
                                    paso= paso+1;
                                }
                                 wsenviarcambio(1);
                                alertify.success(data.msj);} 
                                else {
                                alertify.error(data.msj);}
                        },
                        error: function(jqXHR, textStatus, error) {
                            alertify.error(error);
                        }
                    }, function() {
                        alertify.success("Error en ajax");
                    });
                    /*----------------------------------------*/
                } 

                else {
                    alertify.error("La posición seleccionada no corresponde");
                    //alertify.success(barras);
                }
                barras = "";
            } else {
                barras = barras + e.key;
            }
        }
    });
}



function lectura_tecladof3() { // Armado de tarimas
    corriendo3 = 1;
    $(document).off("keypress",document);
    $(document).on("keypress",function(e) {
        if (cuadromodalabierto == 1) {
            if (e.which == 13) {
                if (($.trim(idposestante) == $.trim(barras).padStart(5,"0")))
                {
                    // envio por ajax
                    alertify.success("coincidente pos estante");
                    
                    $.ajax({ async:true,
                        url: 'controlador.php?action=19',
                        type: 'post',
                        data: {
                            'ordentarea': ordentarea,
                            'codprod': codprod
                        },
                        dataType: 'json',
                        success: function(data) {
                            if (data.success == true) {
                                $("#modal_detalletarea4").modal('hide');
                                objetomover.toggle("slow");
                                wsenviarcambio(1);
                            } else {
                                alertify.error(data.msj);
                            }
                        },
                        error: function(jqXHR, textStatus, error) {
                            alertify.error(error);
                        }
                    });
                }
                else{
                    alertify.error("La posición seleccionada no corresponde");
                }
                barras = "";
                enviotexto = 0;

            }
            else {
                // escritura por teclado
                barras = barras + e.key;
            }  
        }
    });              
}               
                       
/*------------------------------------------------------------------*/
function lectura_tecladof4() { // grupo picking
    corriendo4 = 1;
    $(document).off("keypress",document);
    $(document).on("keypress",function(e) {
        if (cuadromodalabierto == 1) {
            if (e.which == 13) {
                if (($.trim(idposestante) == $.trim(barras).padStart(5,"0")))
                {
                    alertify.success("coincidente pos estante");
                    cuadromodalabierto= 1;
                    $(".hijoenmodal").each(function(indice,elemento){
                        objetomover = $(elemento);
                        ordentarea  = $(elemento).attr("ordentarea");
                        codigopro   = $(elemento).attr("codprod");
                        taked       = 1;
                        carreta     = $(elemento).attr("carreta");
                        posicion    = $(elemento).attr("posicion");
                        cunico    = $(elemento).attr("cunico");
                    });
                }
                else{
                    alertify.error("La posición seleccionada no corresponde");
                }
                barras = "";
                enviotexto = 0;
            }
            else {
                // escritura por teclado
                barras = barras + e.key;
            }  
        }
    });              
}               
/*------------------------------------------------------------------*/
function salir_a_menu() {
    $.ajax({ async:true,
        url: 'controlador.php?action=11',
        type: 'post',
        data: {},
        dataType: 'json',
        success: function(data) {
            if (data.success == true) {
                location.reload();
            }
        }
    });
}

function cargapagina() {
    //console.log("tarea respaldo");
    if (cuadromodalabierto==0){
        $.ajax({ async:true,
            url: 'controlador.php?action=0',
            type: 'post',
            data: {},
            dataType: 'json',
            success: function(data) {
                if (data.success == true) {
                    // uso de funcion MD5, se compara el ultimo contenido cargado, si este es diferente produce cambio
                    if (ultimocargado==$.md5(data.detalle)){
                           console.log("DEBUG: sin cambios. no es necesario cargar datos");
                    }
                    else{
                        console.log("DEBUG: cambio detectado");
                        $("#contenidopg").html(data.detalle);
                        $("#contenidopg_oculto").html(data.encabezado);
                    }
                    ultimocargado= $.md5(data.detalle);
                }
            },
            error: function(jqXHR, textStatus, error) {
                alertify.error(error);
            }
        });    
    }
    
}
function wsenviarcambio(){
     try {
            window.socket.send(1);
         }
    catch(ex){ 
        //console.log(ex); 
    }
    
}
function websocket(opcionsocket){
    //console.log("intentando conectar");
    var host = "ws://10.32.8.3:9005/?canal="+opcionsocket; // DIRECCIÓN DE SERVIDOR
    //var host = "ws://10.32.9.8:9005/?canal="+opcionsocket; //
    try {
        if (typeof(socket)=="undefined"){
            socket = new WebSocket(host);    
        }
        else{
            if ((socket.url!=host )|| (socket.readyState==3)){
                socket = new WebSocket(host);    
            }
        }
        socket.onopen    = function(msg) { 
                            if(tarea_consulta) {
                                }
                           };
        socket.onmessage = function(msg) { 
                               console.log(msg);
                               if (msg.data=="1" && cuadromodalabierto==0){
                                //alertify.error("recibiendo respuesta de serv: actualizar")
                                cargapagina(); 
                               }
                               else{
                                if (cuadromodalabierto==0){
                                    alertify.error(" NO actualizar")
                                }
                               }
                           };
        socket.onclose   = function(msg) { 
                                window.clearInterval(tarea_consulta);
                                tarea_consulta=setInterval(cargapagina, 15000);     
                           };       
    }
    catch(ex){ 
        console.log(ex); 
    }
}


function finalizagrupo(){
     $.ajax({ async:false,
        url: 'controlador.php?action=31',
        type: 'post',
        data: {
            'X': 0
        },
        dataType: 'json',
        success: function(data) {
            if (data.success == true) {
                wsenviarcambio();
                alertify.success(data.msj);
                 setTimeout(function() {cargapagina();}, 1500)

            } else {
                alertify.error(data.msj);
            }
        },
        error: function(jqXHR, textStatus, error) {
            alertify.error(error);

        }
    });
}
function formatearnumeros(){
    $('.txtcantidad').each(function () {
        var item = $(this).text();
        var num = Number(item).toLocaleString('en');    
        $(this).text(num);
    });    
}

function prueba_notificacion() {
    if (Notification) {
        if (Notification.permission !== "granted") {
            Notification.requestPermission()
        }
        var title = "IPS INFASA"
        var extra = {
            icon: "http://cmfinfasa.net/email/pushnot.png",
            body: "Hay nuevas Listas de preparación"
        }
        var noti = new Notification(title, extra)
        noti.onclick = {
            // Al hacer click
        }
        noti.onclose = {
            // Al cerrar
        }
        setTimeout(function() {
            noti.close()
        }, 10000)
    };
}
function verificatareas(){
    contad=0;
    tarea= 1;
     $(".btncargartarea_detalle").each(function(indice,elemento){
        contad= contad+1;
        picker      = $(elemento).attr("picker");
        if (picker==0){
            tarea= 0;
        }
      });
     return tarea;
}

function filtradivs($claseafiltrar, $busca,$arrCamposbusqueda){
    $buscado= $busca.toUpperCase();
    ////console.log($busca);
    cadenaeval=''
    instruccion=" cadenaeval= ";
    if ($buscado==''){
          $("."+$claseafiltrar).show();
    }
    else{
        // formar palabras a buscar
        for (x=0;x<$arrCamposbusqueda.length;x++){
            instruccion=instruccion+ "$(this).attr('"+$arrCamposbusqueda[x]+"')";
            if (x+1<$arrCamposbusqueda.length){
                instruccion=instruccion+ " + ";
            }        
        }
        instruccion=instruccion+";";
        //console.log(instruccion);
        $("."+$claseafiltrar).each(function(indice,elemento){
            eval(instruccion);
            if (cadenaeval.indexOf($buscado) > -1){
                $(elemento).show("slow");
            }
            else{
                $(elemento).hide("slow");
            }
           ////console.log(cadenaeval.indexOf($buscado) > -1);

        });
    }
    //return tarea;
}


function consultar_tareabk(){
    $.ajax({
        url: 'controlador.php?action=12',
        type: 'post',       
        data: {
            'X': 0},
        dataType: 'json',
        success: function(data) {
            $json['cantingresos']  =  $cantingresos;
            $json['cantdespachos'] =  $cantdespachos;
            $json['canttraslados'] =  $canttraslados;
            $json['cantarmados']   =  $cantarmados;
            $('#cantingresos').text( data.cantingresos);
            $('#cantdespachos').text( data.cantdespachos );
            $('#canttraslados').text( data.canttraslados );
            if (data.success == true) {
               // snd.play();
               cantingresos
            }
        }
    });
    //-----------------------------------------------------
}

function consultar_tarea(){
    $.ajax({ async:true,
        url: 'controlador.php?action=12',
        type: 'post',
        data: {
            'X': 0
        },
        dataType: 'json',
        success: function(data) {
            if (data.success == true) {
                $('#cantingresos').text( data.cantingresos);
                $('#cantdespachos').text( data.cantdespachos );
                $('#canttraslados').text( data.canttraslados );
            } else {
                alertify.error(data.msj);
            }
        },
        error: function(jqXHR, textStatus, error) {
            alertify.error(error);
        }
    });
}




!function(n){"use strict";function t(n,t){var r=(65535&n)+(65535&t);return(n>>16)+(t>>16)+(r>>16)<<16|65535&r}function r(n,r,e,u,o,c){return t((f=t(t(r,n),t(u,c)))<<(i=o)|f>>>32-i,e);var f,i}function e(n,t,e,u,o,c,f){return r(t&e|~t&u,n,t,o,c,f)}function u(n,t,e,u,o,c,f){return r(t&u|e&~u,n,t,o,c,f)}function o(n,t,e,u,o,c,f){return r(t^e^u,n,t,o,c,f)}function c(n,t,e,u,o,c,f){return r(e^(t|~u),n,t,o,c,f)}function f(n,r){n[r>>5]|=128<<r%32,n[14+(r+64>>>9<<4)]=r;var f,i,a,h,g,l=1732584193,d=-271733879,v=-1732584194,C=271733878;for(f=0;f<n.length;f+=16)i=l,a=d,h=v,g=C,l=e(l,d,v,C,n[f],7,-680876936),C=e(C,l,d,v,n[f+1],12,-389564586),v=e(v,C,l,d,n[f+2],17,606105819),d=e(d,v,C,l,n[f+3],22,-1044525330),l=e(l,d,v,C,n[f+4],7,-176418897),C=e(C,l,d,v,n[f+5],12,1200080426),v=e(v,C,l,d,n[f+6],17,-1473231341),d=e(d,v,C,l,n[f+7],22,-45705983),l=e(l,d,v,C,n[f+8],7,1770035416),C=e(C,l,d,v,n[f+9],12,-1958414417),v=e(v,C,l,d,n[f+10],17,-42063),d=e(d,v,C,l,n[f+11],22,-1990404162),l=e(l,d,v,C,n[f+12],7,1804603682),C=e(C,l,d,v,n[f+13],12,-40341101),v=e(v,C,l,d,n[f+14],17,-1502002290),l=u(l,d=e(d,v,C,l,n[f+15],22,1236535329),v,C,n[f+1],5,-165796510),C=u(C,l,d,v,n[f+6],9,-1069501632),v=u(v,C,l,d,n[f+11],14,643717713),d=u(d,v,C,l,n[f],20,-373897302),l=u(l,d,v,C,n[f+5],5,-701558691),C=u(C,l,d,v,n[f+10],9,38016083),v=u(v,C,l,d,n[f+15],14,-660478335),d=u(d,v,C,l,n[f+4],20,-405537848),l=u(l,d,v,C,n[f+9],5,568446438),C=u(C,l,d,v,n[f+14],9,-1019803690),v=u(v,C,l,d,n[f+3],14,-187363961),d=u(d,v,C,l,n[f+8],20,1163531501),l=u(l,d,v,C,n[f+13],5,-1444681467),C=u(C,l,d,v,n[f+2],9,-51403784),v=u(v,C,l,d,n[f+7],14,1735328473),l=o(l,d=u(d,v,C,l,n[f+12],20,-1926607734),v,C,n[f+5],4,-378558),C=o(C,l,d,v,n[f+8],11,-2022574463),v=o(v,C,l,d,n[f+11],16,1839030562),d=o(d,v,C,l,n[f+14],23,-35309556),l=o(l,d,v,C,n[f+1],4,-1530992060),C=o(C,l,d,v,n[f+4],11,1272893353),v=o(v,C,l,d,n[f+7],16,-155497632),d=o(d,v,C,l,n[f+10],23,-1094730640),l=o(l,d,v,C,n[f+13],4,681279174),C=o(C,l,d,v,n[f],11,-358537222),v=o(v,C,l,d,n[f+3],16,-722521979),d=o(d,v,C,l,n[f+6],23,76029189),l=o(l,d,v,C,n[f+9],4,-640364487),C=o(C,l,d,v,n[f+12],11,-421815835),v=o(v,C,l,d,n[f+15],16,530742520),l=c(l,d=o(d,v,C,l,n[f+2],23,-995338651),v,C,n[f],6,-198630844),C=c(C,l,d,v,n[f+7],10,1126891415),v=c(v,C,l,d,n[f+14],15,-1416354905),d=c(d,v,C,l,n[f+5],21,-57434055),l=c(l,d,v,C,n[f+12],6,1700485571),C=c(C,l,d,v,n[f+3],10,-1894986606),v=c(v,C,l,d,n[f+10],15,-1051523),d=c(d,v,C,l,n[f+1],21,-2054922799),l=c(l,d,v,C,n[f+8],6,1873313359),C=c(C,l,d,v,n[f+15],10,-30611744),v=c(v,C,l,d,n[f+6],15,-1560198380),d=c(d,v,C,l,n[f+13],21,1309151649),l=c(l,d,v,C,n[f+4],6,-145523070),C=c(C,l,d,v,n[f+11],10,-1120210379),v=c(v,C,l,d,n[f+2],15,718787259),d=c(d,v,C,l,n[f+9],21,-343485551),l=t(l,i),d=t(d,a),v=t(v,h),C=t(C,g);return[l,d,v,C]}function i(n){var t,r="";for(t=0;t<32*n.length;t+=8)r+=String.fromCharCode(n[t>>5]>>>t%32&255);return r}function a(n){var t,r=[];for(r[(n.length>>2)-1]=void 0,t=0;t<r.length;t+=1)r[t]=0;for(t=0;t<8*n.length;t+=8)r[t>>5]|=(255&n.charCodeAt(t/8))<<t%32;return r}function h(n){var t,r,e="";for(r=0;r<n.length;r+=1)t=n.charCodeAt(r),e+="0123456789abcdef".charAt(t>>>4&15)+"0123456789abcdef".charAt(15&t);return e}function g(n){return unescape(encodeURIComponent(n))}function l(n){return function(n){return i(f(a(n),8*n.length))}(g(n))}function d(n,t){return function(n,t){var r,e,u=a(n),o=[],c=[];for(o[15]=c[15]=void 0,u.length>16&&(u=f(u,8*n.length)),r=0;r<16;r+=1)o[r]=909522486^u[r],c[r]=1549556828^u[r];return e=f(o.concat(a(t)),512+8*t.length),i(f(c.concat(e),640))}(g(n),g(t))}n.md5=function(n,t,r){return t?r?d(t,n):h(d(t,n)):r?l(n):h(l(n))}}("function"==typeof jQuery?jQuery:this);
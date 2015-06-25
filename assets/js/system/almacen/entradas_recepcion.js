jQuery(document).ready(function(){
	var Buscar = jQuery('#search-query');
	Buscar.focus();
	Buscar.keyup(function(e) { if(e.which == 13) buscar(); });
});

function buscar(){
	var filtro = jQuery('#search-query').val();
	jQuery.ajax({
		type:"POST",
		url: path()+"almacen/entradas_recepcion/listado",
		dataType: "json",
		data: {filtro : filtro},
		beforeSend : function(){
			jQuery("#loader").html('<img src="'+path()+'assets/images/loaders/loader.gif"/>');
		},
		success : function(data){
			var funcion = 'buscar';
        	jQuery("#loader").html('');
        	jQuery('#a-1').html(data+input_keypress('search-query', funcion));
			jQuery('#search-query').val(filtro).focus();
			tool_tips();
		}
	})
}
function load_content(uri, id_content){
	jQuery('#ui-id-2').hide('slow');
	jQuery('#ui-id-3').hide('slow');
	var filtro = jQuery('#search-query').val();
	var functions = [];
    jQuery.ajax({
        type: "POST",
        url: uri,
        dataType: 'json',
        data: {filtro : filtro, tabs:1},
        success: function(data){
           if(id_content==1){
           		var funcion = 'buscar';
           		jQuery('#a-1').html(data+input_keypress('search-query', funcion));
           		jQuery('#search-query').val(filtro).focus();
           		tool_tips();
           }else{
           		functions.push('jQuery(".chzn-select").chosen();');
          	 	functions.push('calendar_actual("fecha_factura")');
          	 	//jQuery('#a-'+id_content).html(data);
          	 	jQuery('#a-'+id_content).html(data+include_script(functions));

           }
        }
    });
}
function agregar(){
	var btn          = jQuery("button[name='save_entreda']");
	btn.attr('disabled','disabled');
	jQuery('#mensajes').hide();
	var incomplete   	 = values_requeridos();
    var no_orden      	 = jQuery('#no_orden').val();
    var no_factura  	 = jQuery('#no_factura').val();
    var fecha_factura  	 = jQuery('#fecha_factura').val();
    var fecha_recepcion  = jQuery('#fecha_recepcion').val();
    
	jQuery.ajax({
		type:"POST",
		url: path()+"almacen/entradas_recepcion/get_data_orden",
		dataType: "json",
		data: {incomplete :incomplete, no_orden:no_orden, no_factura:no_factura, fecha_factura:fecha_factura, fecha_recepcion:fecha_recepcion },
		beforeSend : function(){
			jQuery("#registro_loader").html('<img src="'+path()+'assets/images/loaders/loader.gif"/>');
		},
		success : function(data){
			btn.removeAttr('disabled');

			var data = data.split('|');
			if(data[0]==1){
				clean_formulario();
			}
			jQuery("#registro_loader").html('');
		    jQuery("#mensajes").html(data[1]).show('slow');
		}
	})
}
function articulos(id_compras_orden){	
	var functions=[];
	jQuery.ajax({
        type: "POST",
        url: path()+"almacen/entradas_recepcion/articulos",
        dataType: 'json',
        data: {id_compras_orden : id_compras_orden},
        success: function(data){
        	jQuery('#a-0').html('');
        	functions.push('jQuery(".chzn-select").chosen();');
          	functions.push('calendar_actual("fecha_factura")');
        	jQuery('#a-3').html(data+include_script(functions));
        	jQuery('#ui-id-3').show('slow');
        	jQuery('#ui-id-3').click();
        }
    });
}
function calculos(){
	var moneda = jQuery('#moneda').val();
	var subtotal=0;
	var descuento=0;
	var impuesto=0;
	var total =0;
	var valor=[];
	var valor_2=[];
	var valor_3=[];
	var valor_4=[];
	var varl;
	var result;
	var result_2;
	var result_3;
	jQuery('input[name="aceptar[]"]:checked').each(function() {
		valor.push(parseFloat(jQuery('#subtotal__hidden'+jQuery(this).val()).val()));
		valor_2.push(parseFloat(jQuery('#descuento_'+jQuery(this).val()).val()));
		valor_3.push(parseFloat(jQuery('#costo_x_cantidad_hidden'+jQuery(this).val()).val()));
		valor_4.push(parseFloat(jQuery('#valor_hidden_impuesto_'+jQuery(this).val()).val()));
	});
	jQuery(valor).each(function(index,value){
		result=parseFloat(value);
		subtotal= subtotal+result;
	});
	//CALCULA EL DESCUENTO
	jQuery(valor_2).each(function(index,value){
		result_2=parseFloat((parseFloat(valor_3[index])*parseFloat(valor_2[index]))/100);
		descuento= descuento+result_2;
	});
	//CALCULA IMPUESTO
	jQuery(valor_4).each(function(index,value){
		result_3=parseFloat(value);
		impuesto= parseFloat(impuesto)+parseFloat(result_3);
	});
	//CALCULA SUBTOTAL
	total=(subtotal-descuento)+impuesto;
	jQuery('#subtotal').val(subtotal);
	jQuery('#value_subtotal').html('<strong>'+ moneda+' '+ numeral(subtotal).format('0,0.00') +'</strong>');
	jQuery('#descuento_total').val(descuento);
	jQuery('#value_descuento').html('<strong> - '+ moneda+' '+numeral(descuento).format('0,0.00')+'</strong>');
	jQuery('#impuesto_total').val(impuesto);
	jQuery('#value_impuesto').html('<strong>'+ moneda+' '+ numeral(impuesto).format('0,0.00')+'</strong>');
	jQuery('#total_data').val(total);
	jQuery('#value_total').html('<strong>'+ moneda+' '+ numeral(total).format('0,0.00')+'</strong>');

}
function calcula_totla_pagar(){
	var total;
	var subtotal 	= jQuery('#subtotal_final').val();
	var descuento 	= jQuery('#descuento_final').val();
	var impuesto 	= jQuery('#impuesto_final').val();
	total=parseFloat((subtotal-descuento))+parseFloat(impuesto);
	jQuery('#value_total').html(total);
}
function aceptar_orden(){
	var cont=0;
	jQuery('input[name="aceptar[]"]').each(function() {
		if(jQuery(this).is(':checked')){
		}
		else{cont++;}
	});
	if(cont>0){
		jQuery('#comentario').addClass('requerido');
	}else{
		jQuery('#comentario').removeClass('requerido');
	}
}
function volver_orden(){
	
}
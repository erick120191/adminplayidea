jQuery(document).ready(function() {
	jQuery('#search-query').keypress(function(event){
			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13'){  buscar_articulo(); 
		} 
	});
});
function agregar_articulo(){
	var articulo    = jQuery("#articulo").val();
	var clave_corta = jQuery("#clave_corta").val();
	var descripcion = jQuery("#descripcion").text();
	jQuery.ajax({
		type:"POST",
		url: path()+"compras/catalogos/agregar_articulo",
		dataType: "json",
		data: {ajax : 1, articulo : articulo, clave_corta:clave_corta, descripcion:descripcion},
		beforeSend : function(){
			jQuery("#registro_loader").html('<img src="'+path()+'assets/images/loaders/loader27.gif"/>');
		},
		success : function(msg){
			
		    jQuery("#mensajes").html(msg).show('slow');
			jQuery("#registro_loader").html('');
		}
	});

}
function editar_articulo(){
	var id_articulo = jQuery("#id_articulo").val();
	var articulo    = jQuery("#articulo").val();
	var clave_corta = jQuery("#clave_corta").val();
	var descripcion = jQuery("#descripcion").text();
	
	jQuery.ajax({
		type:"POST",
		url: path()+"compras/catalogos/actualizar_articulo",
		dataType: "json",
		data: {id_articulo:id_articulo, articulo : articulo, clave_corta:clave_corta, descripcion:descripcion},
		beforeSend : function(){
			jQuery("#registro_loader").html('<img src="'+path()+'assets/images/loaders/loader27.gif"/>');
		},
		success : function(msg){
			
			jQuery("#mensajes").html(msg).show('slow');
			jQuery("#registro_loader").html('');
		}
	});

}
function buscar_articulo(){
	var filtro = jQuery('#search-query').val();
	if(filtro !== ''){
		jQuery.ajax({
	        type: "POST",
	        url: path()+"compras/catalogos/articulos",
	        dataType: 'json',
	        data: {filtro: filtro},
	        beforeSend : function(){
	        	jQuery("#loader").html('<img src="'+path()+'assets/images/loaders/loader27.gif"/>');
	        },
	        success: function(view){
	        	var funcion = 'buscar_articulo';
	        	jQuery("#loader").html('');
	        	jQuery('#a-1').html(view+input_keypress('search-query', funcion));				
	        	tool_tips();
	        }
	    });
	}
}
function detalle_articulo(id_articulo){
	jQuery('#ui-id-2').click();
	jQuery.ajax({
        type: "POST",
        url: path()+"compras/catalogos/detalle_articulo",
        dataType: 'json',
        data: {id_articulo: id_articulo},
      
        success: function(view){
        	//jQuery('#ui-id-2').show();
        	jQuery('#a-2').html(view);
        }
    });
}
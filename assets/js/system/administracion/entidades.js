jQuery(document).ready(function(){
	jQuery('#search-query').focus();
	jQuery('#search-query').keypress(function(event){
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(keycode == '13'){  
			buscar();
		} 
	});
})

function load_content(uri, id_content){
	jQuery('#ui-id-2').hide('slow');
	var filtro = jQuery('#search-query').val();
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
           		jQuery('#a-'+id_content).html(data);
           		var chosen  = 'jQuery(".chzn-select").chosen();';
           		jQuery('#a-'+id_content).html(data+include_script(chosen));
           }
        }
    });
}

function buscar()
{
	var filtro = jQuery('#search-query').val();
	jQuery.ajax({
		type: 'POST',
		url: path()+"administracion/entidades/listado",
		dataType: "json",
		data: {filtro:filtro},
		beforeSend : function(){
			jQuery("#loader").html('<img src="'+path()+'assets/images/loaders/loader.gif"/>');
		},
		success : function(data){
			var funcion = 'buscar';
			jQuery("#loader").html('');
			jQuery('#a-1').html(data+input_keypress('search-query',funcion));
			jQuery('#search-query').val(filtro).focus();
			tool_tips();
		}
	});
}

function detalle(id_entidad){
	jQuery('#ui-id-2').click();
	jQuery.ajax({
        type: "POST",
        url: path()+"administracion/entidades/detalle",
        dataType: 'json',
        data: {id_entidad : id_entidad},
        success: function(data){
        	var chosen = 'jQuery(".chzn-select").chosen();';
        	jQuery('#a-0').html('');
        	jQuery('#a-2').html(data);
        	jQuery('#a-2').html(data+include_script(chosen));
        	jQuery('#ui-id-2').show('slow');
        }
    });
}

function actualizar()
{
	jQuery('#mensajes_update').hide();
	var btn             = jQuery("button[name='actualizar']");
	btn.attr('disabled','disabled');
	var btn_text        = btn.html();	
	var incomplete      = values_requeridos();
	var id_entidad = jQuery('#id_entidad').val();
	var entidad =  jQuery('#entidad').val();
	var clave_corta = jQuery('#clave_corta').val();
	var abreviatura = jQuery('#abrev').val();
	jQuery.ajax({
		type:"POST",
		url: path()+"administracion/entidades/actualizar",
		dataType: "json",
		data: {incomplete:incomplete, id_entidad:id_entidad, entidad:entidad, clave_corta:clave_corta, abreviatura:abreviatura},
		beforeSend : function(){
			jQuery("#update_loader").html('<img src="'+path()+'assets/images/loaders/loader.gif"/>');
		},
		success : function(data){
			btn.removeAttr('disabled');		
			jQuery("#mensajes_update").html(data.contenido).show('slow');
			jQuery("#update_loader").html('');
		}
	})
}

function agregar()
{
	var btn = jQuery("button[name='save_entidad']");
	btn.attr('disabled','disabled');
	jQuery('#mensajes').hide();
	var incomplete = values_requeridos();
	var entidad = jQuery('#entidad').val();
	var clave_corta = jQuery('#clave_corta').val();
	var abreviatura = jQuery('#abrev').val();
	jQuery.ajax({
		type: 'POST',
		url: path()+'administracion/entidades/insert_entidad',
		dataType: 'json',
		data: {incomplete:incomplete, entidad, clave_corta, abreviatura},
		beforeSend: function(){
			jQuery('#update_loader').html('img src="'+path()+'assets/images/loaders/loader.gif />"');
		},
		success: function(data){
			btn.removeAttr('disabled');
			var data = data.split('|');
			if(data[0] == 1)
			{
				clean_formulario();
			}
			jQuery('#registro_loader').html('');
			jQuery("#mensajes").html(data[1]).show('slow');
		}
	});
}
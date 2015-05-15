jQuery(document).ready(function(){
  jQuery('#search-query').focus();
  jQuery('#search-query').keypress(function(event){
    var keycode = (event.keyCode ? event.keyCode : event.which);
    if(keycode == '13'){  
      buscar_vendedores();
    } 
  });
})
function buscar_vendedores(){
  var filtro = jQuery('#search-query').val();
  jQuery.ajax({
    type:"POST",
    url: path()+"ventas/vendedores/listado_vendedores",
    dataType: "json",
    data: {filtro : filtro},
    beforeSend : function(){
      jQuery("#loader").html('<img src="'+path()+'assets/images/loaders/loader.gif"/>');
    },
    success : function(data){
      var funcion = 'buscar_vendedores';
          jQuery("#loader").html('');
          jQuery('#a-1').html(data+input_keypress('search-query', funcion));
      jQuery('#search-query').val(filtro).focus();
      tool_tips();
    }
  })
}
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
           		var funcion = 'buscar_vendedores';
           		jQuery('#a-1').html(data+input_keypress('search-query', funcion));
           		jQuery('#search-query').val(filtro).focus();
           		tool_tips();
           }else{
          	 	var chosen  = 'jQuery(".chzn-select").chosen();';
           		jQuery('#a-'+id_content).html(data+include_script(chosen));
           }
        }
    });
}
function insert_vendedor(){
 var btn          = jQuery("button[name='save_vendedor']");
  btn.attr('disabled','disabled');
  jQuery('#mensajes').hide();
  
  var incomplete    = values_requeridos();    

  var  nombre       = jQuery('#nombre').val();
  var  clave_corta  = jQuery('#clave_corta').val();
  var  rfc          = jQuery('#rfc').val();
  var  calle        = jQuery('#calle').val();
  var  num_int      = jQuery('#num_int').val();
  var  num_ext      = jQuery('#num_ext').val();
  var  colonia      = jQuery('#colonia').val();
  var  municipio    = jQuery('#municipio').val(); 
  var  cp           = jQuery('#cp').val();
  var  telefonos    = jQuery('#telefonos').val();
  var  email        = jQuery('#email').val();

  var entidad = jQuery("select[name='lts_entidades'] option:selected").val();
  var sucursal      = jQuery("select[name='lts_sucursales'] option:selected").val();


  jQuery.ajax({
    type:"POST",
    url: path()+"ventas/vendedores/insert_vendedor",
    dataType: "json",
    data: {
            incomplete :incomplete,nombre:nombre,clave_corta:clave_corta,rfc:rfc,calle:calle,num_int:num_int,num_ext:num_ext,colonia:colonia,municipio:municipio,entidad:entidad,sucursal:sucursal,cp:cp,telefonos:telefonos,email:email},
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
function detalle_vendedor(id_vendedor){
  jQuery('#ui-id-2').click();
  jQuery.ajax({
        type: "POST",
        url: path()+"ventas/vendedores/detalle_vendedor",
        dataType: 'json',
        data: {id_vendedor : id_vendedor},
        success: function(data){
          var chosen = 'jQuery(".chzn-select").chosen();';
          jQuery('#a-0').html('');
          jQuery('#a-2').html(data+include_script(chosen));
          jQuery('#ui-id-2').show('slow');
        }
    });
}
function update_vendedor(){
  var btn          = jQuery("button[name='update_vendedor']");
 /* btn.attr('disabled','disabled');*/
  jQuery('#mensajes').hide();
  
  var incomplete    = values_requeridos();    
  var  id_vendedor   = jQuery('#id_vendedor').val();
  var  nombre       = jQuery('#nombre').val();
  var  clave_corta  = jQuery('#clave_corta').val();
  var  rfc          = jQuery('#rfc').val();
  var  calle        = jQuery('#calle').val();
  var  num_int      = jQuery('#num_int').val();
  var  num_ext      = jQuery('#num_ext').val();
  var  colonia      = jQuery('#colonia').val();
  var  municipio    = jQuery('#municipio').val();  
  var  cp           = jQuery('#cp').val();
  var  telefonos    = jQuery('#telefonos').val();
  var  email        = jQuery('#email').val();
  var entidad       = jQuery("select[name='lts_entidades'] option:selected").val();
  var sucursal      = jQuery("select[name='lts_sucursales'] option:selected").val();


  jQuery.ajax({
    type:"POST",
    url: path()+"ventas/vendedores/update_vendedor",
    dataType: "json",
    data: {
            incomplete :incomplete,id_vendedor:id_vendedor,nombre:nombre,clave_corta:clave_corta,rfc:rfc,calle:calle,num_int:num_int,num_ext:num_ext,colonia:colonia,municipio:municipio,entidad:entidad,sucursal:sucursal,cp:cp,telefonos:telefonos,email:email},
    beforeSend : function(){
      jQuery("#registro_loader").html('<img src="'+path()+'assets/images/loaders/loader.gif"/>');
    },
    success : function(data){
      btn.removeAttr('disabled');
      var data = data.split('|');
      if(data[0]==1){
      }
      jQuery("#update_loader").html('');
        jQuery("#mensajes_update").html(data[1]).show('slow');
      
    }
  });
}
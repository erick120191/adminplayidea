<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class regiones extends Base_Controller
{
	private $modulo;
	private $submodulo;
	private $seccion;
	private $view_content;
	private $path;
	private $icon;

	private $offset, $limit_max;
	private $tab, $tab1, $tab2, $tab3;

	public function __construct()
	{
		parent::__construct();
		$this->modulo 			= 'administracion';
		$this->submodulo		= 'catalogos';
		$this->seccion          = 'regiones';
		$this->icon 			= 'fa fa-map-marker'; 
		$this->path 			= $this->modulo.'/'.$this->seccion.'/'; #administracion/regiones
		$this->view_content 	= 'content';
		$this->limit_max		= 5;
		$this->offset			= 0;
		// Tabs
		$this->tab1 			= 'agregar';
		$this->tab2 			= 'listado';
		$this->tab3 			= 'detalle';
		// DB Model
		$this->load->model($this->modulo.'/'.$this->seccion.'_model','db_model');
		$this->load->model($this->modulo.'/entidades_model','db_model2');
		// Diccionario
		$this->lang->load($this->modulo.'/'.$this->seccion,"es_ES");
	}

	public function config_tabs()
	{
		$tab_1 	= $this->tab1;
		$tab_2 	= $this->tab2;
		$tab_3 	= $this->tab3;
		$path  	= $this->path;
		$pagina =(is_numeric($this->uri_segment_end()) ? $this->uri_segment_end() : "");
		// Nombre de Tabs
		$config_tab['names']    = array(
										 $this->lang_item($tab_1) 
										,$this->lang_item($tab_2) 
										,$this->lang_item($tab_3) 
								); 
		// Href de tabs
		$config_tab['links']    = array(
										 $path.$tab_1             
										,$path.$tab_2.'/'.$pagina 
										,$tab_3                   
								); 
		// Accion de tabs
		$config_tab['action']   = array(
										 'load_content'
										,'load_content'
										,''
								);
		// Atributos 
		$config_tab['attr']     = array('','', array('style' => 'display:none'));
		return $config_tab;
	}
	private function uri_view_principal()
	{
		return $this->modulo.'/'.$this->view_content;
	}

	public function index()
	{
		$tabl_inicial 			  = 2;
		$view_listado    		  = $this->listado();	
		$contenidos_tab           = $view_listado;
		$data['titulo_seccion']   = $this->lang_item($this->seccion);
		$data['titulo_submodulo'] = $this->lang_item("titulo_submodulo");
		$data['icon']             = $this->icon;
		$data['tabs']             = tabbed_tpl($this->config_tabs(),base_url(),$tabl_inicial,$contenidos_tab);	
		
		$js['js'][]  = array('name' => $this->seccion, 'dirname' => $this->modulo);
		$this->load_view($this->uri_view_principal(), $data, $js);
	}
	public function listado($offset=0)
	{

	}

	public function agregar()
	{
		$seccion = $this->modulo.'/'.$this->seccion.'/'.$this->seccion.'_save';
		$entidades_array = array(
					 'data'		=> $this->db_model2->get_entidades_default()
					,'value' 	=> 'id_administracion_entidad'
					,'text' 	=> array('entidad','clave_corta')
					,'name' 	=> "lts_entidades"
					,'class' 	=> "requerido"
					);
		$entidades = dropMultiselect_tpl($entidades_array);

		$btn_save = form_button(array('class'=>'btn btn-primary', 'name'=>'save_region', 'onclick'=>'agregar()','content'=>$this->lang_item("btn_guardar")));
		$btn_reset = form_button(array('class'=>'btn btn_primary', 'name'=>'reset','onclick'=>'clean_formulario()','content'=>$this->lang_item('btn_limpiar')));
		
		$tab_1['lbl_region'] = $this->lang_item("lbl_region");
		$tab_1['lbl_clave_corta'] = $this->lang_item("lbl_clave_corta");
		$tab_1['lbl_descripcion'] = $this->lang_item("lbl_descripcion");
		$tab_1['lbl_entidades'] = $this->lang_item("lbl_entidades");
		$tab_1["list_entidad"] = $entidades;	
		$tab_1['nombre_area'] = $this->lang_item("nombre_area");
		$tab_1['area'] = $this->lang_item('area');
		$tab_1['cvl_corta'] = $this->lang_item('clave_corta');
		$tab_1['desc'] = $this->lang_item('descripcion');

		$tab_1['button_save'] = $btn_save;
		$tab_1['button_reset'] = $btn_reset;

		if($this->ajax_post(false))
		{
			echo json_encode($this->load_view_unique($seccion,$tab_1,true));
		}
		else
		{
			return $this->load_view_unique($seccion, $tab_1, true);
		}
	}

	public function insert_region(){
		$incomplete  = $this->ajax_post('incomplete');
		if($incomplete>0){
			$msg = $this->lang_item("msg_campos_obligatorios",false);
			echo json_encode('0|'.alertas_tpl('error', $msg ,false));
		}else{
			$region      = $this->ajax_post('region');
			$clave_corta = $this->ajax_post('clave_corta');
			$descripcion = $this->ajax_post('descripcion');
			$entidades   = $this->ajax_post('entidades');
			$data_insert = array('region'          => $region
								,'clave_corta'     => $clave_corta
								,'descripcion'     => $descripcion
								,'id_usuario'      => $this->session->userdata('id_usuario')
								,'registro'        => $this->timestamp());
			$insert = $this->db_model->db_insert_data($data_insert);
			
			$region = $this->db->insert_id($insert);
			foreach($entidades as $item => $valor)
			{
				$insertar = array('id_entidad' => $valor,
								  'id_region'  => $region);
				//$insert = (isset($valor))?$this->db_model->db_insert_entidades($insertar):false;
				$insert = $this->db_model->db_insert_entidades($insertar);
			}

			if($insert){
				$msg = $this->lang_item("msg_insert_success",false);
				echo json_encode('1|'.alertas_tpl('success', $msg ,false));
			}else{
				$msg = $this->lang_item("msg_err_clv",false);
				echo json_encode('0|'.alertas_tpl('', $msg ,false));
			}
		}
	}
}
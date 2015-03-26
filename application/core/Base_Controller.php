<?php
class Base_Controller extends CI_Controller {
 
    public function __construct() {
        parent::__construct();
    }

    /**
    * Carga la base de datos de acuerdo al pais de origen
    * del usuario (mx,cr,etc)
    * @param string $db
    * @return void
    */
    public function load_database($bd){
    	$load = $this->load->database($bd,TRUE);
    	if(!$load){
    		return true;
    	}
    }

    /**
    * unifica las vistas header & footer con las vistas parseadas
    * de la seccion seleccionada
    * @param string $view
    * @param array $data
    * @param array $data_includes
    * @param string $ext
    * @return void
    */
    public function load_view($view, $data=array(), $data_includes=array() ,$ext = '.html'){
	
		$ext      = ($ext!='.html') ? '': $ext;
		$items    = $this->session->userdata('modulos');
		$uri      = $this->uri->segment_array();
		$includes = $this->load_includes($data_includes);

		$dataheader['data_js']        = (!empty($includes)) ? $includes['js']  : '';
		$dataheader['data_css']       = (!empty($includes)) ? $includes['css'] : '';
		$dataheader['base_url']       = base_url();
		$dataheader['panel_navigate'] = $this->buil_panel_navigate($items,$uri);
		$dataheader['avatar_user']    = $this->session->userdata('avatar_user');
		$dataheader['avatar_pais']    = $this->session->userdata('avatar_pais');
		$dataheader['user_mail']      = $this->session->userdata('mail');
		$dataheader['user_name']      = $this->session->userdata('name');
		$dataheader['user_perfil']    = $this->session->userdata('perfil');
		$dataheader['uri_string']     = ucwords(strtolower(str_replace('/','&nbsp;<span class="separator"></span>&nbsp;',$this->uri->uri_string())));

		$datafooter = array();
		
		$this->parser->parse('includes/header.html', $dataheader);
		$this->parser->parse($view.$ext, $data);
		$this->parser->parse('includes/footer.html',$datafooter); 
	}

	/**
    * Carga archivos js & css en el header
    * @param array $data
    * @return array
    */
	public function load_includes($data){
		if(empty($data)){
			return '';
		}
		$files_js  = '';
		$files_css = '';
		$url_js    = base_url().'assets/js/system';
		$url_css   = base_url().'assets/css/system';
		if (array_key_exists('js', $data)) {
			foreach ($data['js'] as $key => $value) {
				$js_name = $value['name'];
				$js_dir  = $value['dirname'];
				$files_js.= "<script type='text/javascript' src='$url_js/$js_dir/$js_name.js'></script>";
			}
		}
		if (array_key_exists('css', $data)) {
			foreach ($data['css'] as $key => $value) {
				$css_name = $value['name'];
				$css_dir  = $value['dirname'];
				$files_css.= "<link rel='stylesheet' href='$url_css/$css_dir/$css_name.css' type='text/css'  />";
			}
		}

		$data_load['js']  = $files_js;
		$data_load['css'] = $files_css;

		return $data_load;
	}
	/**
    * Carga una vista unica sin integrar el header 
    * ni el footer, puede servir para la carga de 
    * paginas de error
    * @param string $view
    * @param array $data
    * @param array $data_includes
    * @param string $ext
    * @return void
    */
	public function load_view_unique($view, $data=array(), $data_includes=array() ,$ext = '.html'){
		$ext      = ($ext!='.html') ? '': $ext;
		$includes = $this->load_includes($data_includes);

		$data['data_js']  = (!empty($includes)) ? $includes['js']  : '';
		$data['data_css'] = (!empty($includes)) ? $includes['css'] : '';
		$data['base_url'] = base_url();
		
		$this->parser->parse($view.$ext, $data);
	}

	/**
    * Carga la vista de login
    * @return void
    */
	public function load_view_login(){
		$att_fopen = array('id' => 'login');
		$att_hiden = array(
                            'name'    => 'id_user',
                            'id'      => 'id_user',
                            'type'    => 'hidden'
                        );  
		$att_user = array(
                            'name'    => 'user',
                            'id'      => 'user'
                        );  
		$att_pwd = array(
                            'name'    => 'pwd',
                            'id'      => 'pwd'
                        ); 
		$att_btn = array(
                            'name'    => 'button',
                            'id'      => 'button_login',
                            'value'   => 'true',
                            'content' => 'Entrar'
                        );

		$data['base_url']          = base_url();
		$data['form_open']         = form_open('', $att_fopen);
		$data['form_input_hidden'] = form_input($att_hiden);
		$data['form_input_user']   = form_input($att_user);
		$data['form_input_pwd']    = form_password($att_pwd, '', 'placeholder="Password"');
		$data['form_button']       = form_button($att_btn);
		$data['form_close']        = form_close();
		
		$this->parser->parse('login.html', $data);
	}

	/**
    * Contruye el Panel de navegacion
    * @param array $items
    * @param array $uri
    * @param bolean $sub
    * @return string
    */
	public function buil_panel_navigate($items, $uri, $sub = false) {
	    $panel = "";
	    if($sub){$panel .= "<ul class=''>";}
	    foreach ($items as $item => $subitems) {
	        $mod_dropdown = "";
	       	$content      = "";	
	       	$routes       = "";
	       	$active       = "";
	       	if(in_array(strtolower($item), $uri)){
	        	$active  = "active";
	        } 
	        if(array_key_exists('content', $subitems)){
	        	$mod_dropdown = "dropdown";
	        	$content .= $this->buil_panel_navigate($subitems['content'],$uri,$sub = true);
	        }else{
	        	$routes = base_url().$subitems['routes'];
	        }
    		$panel .= "<li class='$mod_dropdown $active '><a href='$routes'>".ucwords(strtolower($item))."</a>";
	        $panel .= $content;
	       	$panel .= "</li>";
	    }
	    if($sub){$panel .= "</ul>";}
	    return $panel;
	}
                		

    /**
	* imprime un arreglo formateado para debug
	* @return array $array
	*/
	public function print_format($array){
		echo '<pre>';
		print_r($array);
		echo '</pre>';
	}

	/**
	* convierte un objeto a un arreglo
	* @param object $obj
	* @return array
	*/
	public function object_to_array($obj){
		$reaged = (array)$obj;
		foreach($reaged as $key => &$field){
			if(is_object($field))
				$field = $this->object_to_array($field);
		}
		return $reaged;
	}

	/**
	* elimina el cache almacenado
	* @return void
	*/
	public function removeCache(){
        $this->output->set_header('Last-Modified:'.gmdate('D, d M Y H:i:s').'GMT');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
        $this->output->set_header('Cache-Control: post-check=0, pre-check=0',false);
        $this->output->set_header('Pragma: no-cache');
    }

    
}

?>
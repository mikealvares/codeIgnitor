<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {
	function __construct() {
        parent::__construct();
        $this->load->library('pagination');
        $this->load->model('user_model');
    }
    public function index(){
    	$this->session->unset_userdata('SORT_DATA');
    	redirect('User/list');
    }
	public function list(){
		/* Search */
		$KEY = $this->input->post('serKey',TRUE);
		$FIL = $this->input->post('filKey',TRUE);
		$config['base_url'] 		= base_url().'Industry/list';
		$config['total_rows'] 		= $this->user_model->cntUsers($KEY,$FIL);
		$config['per_page'] 		= 20;
		$config['uri_segment'] 		= 3;
		$config['num_links'] 		= 5;
		$config['full_tag_open'] 	= '<nav aria-label="Page navigation example"><ul class="pagination justify-content-center">';
		$config['full_tag_close'] 	= '</ul></nav>';
		$config['num_tag_open'] 	= '<li class="page-item">';
		$config['num_tag_close'] 	= '</li>';
		$config['cur_tag_open'] 	= '<li class="page-item active"><span class="page-link">';
		$config['cur_tag_close'] 	= '</span></li>';
		$config['prev_tag_open'] 	= '<li class="page-item">';
		$config['prev_tag_close'] 	= '</li>';
		$config['first_link'] 		= '<i class="fas fa-angle-double-left"></i>';
		$config['first_tag_open'] 	= '<li class="page-item">';
		$config['first_tag_close'] 	= '</li>';
		$config['last_link'] 		= '<i class="fas fa-angle-double-right"></i>';
		$config['last_tag_open'] 	= '<li class="page-item">';
		$config['last_tag_close'] 	= '</li>';
		$config['prev_link'] 		= '<i class="fas fa-angle-left"></i>';
		$config['prev_tag_open'] 	= '<li class="page-item">';
		$config['prev_tag_close'] 	= '</li>';
		$config['next_link'] 		= '<i class="fas fa-angle-right"></i>';
		$config['next_tag_open'] 	= '<li class="page-item">';
		$config['next_tag_close'] 	= '</li>';
		$config['attributes']  		= array('class' => 'page-link');
		$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
		$this->pagination->initialize($config);
		$TMPDATA = $this->session->SORT_DATA;
		$SDATA = '';
		if($TMPDATA!='')
			foreach($TMPDATA as $key => $value){
				if($SDATA=='') $SDATA = $key.' '.$value;
				else $SDATA .=', '.$key.' '.$value;
			}
		$data['PLNK'] = $this->pagination->create_links();

		$data['URES'] = $this->user_model->getPgUsers($config['per_page'], $page, $KEY, $SDATA,$FIL);
		$data['ERR'] = '';
		if($this->session->ERR!=''){
			$data['ERR'] = $this->session->ERR;
			$session_data = array('ERR' => '');
			$this->session->set_userdata($session_data);
		}
		$this->load->view('back/user_management/list', $data);
	}
	public function validUser(){
		$unm = $this->input->post('user_name',TRUE);
    	$eml = $this->input->post('user_email',TRUE);
    	$pwd = md5($this->input->post('user_pwd',TRUE));
    	$typ = (int)$this->input->post('user_type',TRUE);
		$data = array(
			'user_email' 	=> $eml,
			'user_name' 	=> $unm, 
			'user_password' => $pwd, 
			'user_type' 	=> $typ
		);
		$res = $this->user_model->addUser($data);
		$session_data = array('ERR' => 'User added');
		$this->session->set_userdata($session_data);
		redirect('user');
	}
}
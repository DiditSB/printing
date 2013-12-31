<?php

class Login extends CI_Controller {
	function __construct()
	{
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $this->load->library('session');
        $this->load->helper(array('form', 'url','date'));
        $this->load->model('login_model','',TRUE);
    }

    function index()
    {
        $sesi=$this->session->userdata('email');
        if($sesi=='')
        {

    		$details['list_roles'] = $this->login_model->get_list_role();
            $this->load->view('login',$details);
        }
        else
        {
            redirect(base_url('adminmuntira'));
        }

    }
    
    function verifikasi()
    {
        //This method will have the credentials validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email,xss_clean');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|callback_check_database');
 		$this->form_validation->set_message('required', 'isi dengan lengkap bro');
        if($this->form_validation->run() == FALSE)
        {
        	$details['list_roles'] = $this->login_model->get_list_role();
            $this->load->view('login',$details);
        }
        else
        {
	       //Go to private area
            if($this->session->userdata('role')==1)
            {
            	redirect('kasir.php');
            }
            else if($this->session->userdata('role')==2)
            {
            	redirect('manager.php');
            }
            else
            {
            	redirect('index.html');
            }
        }
    }
    
    function check_database($password)
    {
	   //Field validation succeeded.  Validate against database
        $email = $this->input->post('email');
        $role = $this->input->post('role');
	   //query the database
        $result = $this->login_model->cek_login($email,$password,$role);
        if($result)
        {
            $this->session->set_userdata(array(
            								'id' => $result[0]->uacc_id,
                                            'email'=> $result[0]->uacc_email,
                                            'name' => $result[0]->uacc_name,
                                            'no_pegawai'=> $result[0]->uacc_no_peg,
                                            'tgl_masuk'=> $result[0]->uacc_date_added,
                                            'role'=> $result[0]->uacc_group_fk
                                        )
                            );
            return TRUE;
        }
        else
        {
            $this->form_validation->set_message('check_database', 'Invalid email or password');
            return FALSE;
        }
    }
    
    function logout()
    {
        $this->session->sess_destroy();
        redirect('login');
    }
    

}

/* End of file login.php */
/* Location: ./application/controllers/login.php */
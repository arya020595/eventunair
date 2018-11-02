<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {
        
        public $status; 
        public $roles;
    
        function __construct(){
            parent::__construct();
            $this->load->model('user_model', 'user_model', TRUE);
            $this->load->library('form_validation');    
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            $this->status = $this->config->item('status'); 
            $this->roles = $this->config->item('roles');
            $this->load->model('Rekening_model');
        }      
    
	public function index()
	{   
        if(empty($this->session->userdata['email'])){
                redirect(site_url().'user/login/');
            }            
            /*front page*/
            $data = $this->session->userdata; 
                     
            $this->load->view('back/dashboard', $data);
            redirect('admin/dashboard', 'refresh');
	}
        
        
        public function register()
        {
            
            $this->form_validation->set_rules('username', 'Usename', 'required|min_length[5]|max_length[12]');   
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email');    
                       
            if ($this->form_validation->run() == FALSE) {   
                $this->data['title'] = 'Halaman Register';
                $this->load->view('front/user/register', $this->data);
             
            }else{                
                if($this->user_model->isDuplicate($this->input->post('username'))){
                    $this->session->set_flashdata('flash_message', 'Username already exists');
                    redirect(site_url().'user/register');
                }elseif ($this->user_model->isDuplicate1($this->input->post('email'))){
                    $this->session->set_flashdata('flash_message', 'Email already exists');
                    redirect(site_url().'user/register');
                }else{
                    
                    $clean = $this->security->xss_clean($this->input->post(NULL, TRUE));
                    $id = $this->user_model->insertUser($clean); 
                    $token = $this->user_model->insertToken($id);                                        
                    
                    $qstring = $this->base64url_encode($token);                    
                    $url = site_url() . 'user/complete/token/' . $qstring;
                    $link = '<a href="' . $url . '">' . $url . '</a>'; 
                               
                    $message = '';                     
                    $message .= '<strong>You have signed up with our website</strong><br>';
                    $message .= '<strong>Please click:</strong> ' . $link;                          

                    // echo"$message";
     //               //EMAIL	
                    $this->load->library('email');
							
					$config['protocol'] = 'smtp';
					$config['smtp_host'] = 'ssl://smtp.gmail.com';
					$config['smtp_user'] = 'info1@rektor.unair.ac.id';  //change it
					$config['smtp_pass'] = 'tanyapakrektor1'; //change it
                    $config['smtp_port'] = '465';
					$config['charset'] = 'utf-8';
					$config['newline'] = "\r\n";
					$config['mailtype'] = 'html';
					$config['wordwrap'] = TRUE;
					$this->email->initialize($config);
					$this->email->set_newline("\r\n");
					$this->email->from('adm@pih.unair.ac.id', 'EVENT UNAIR');
					$this->email->to($this->input->post('email'));
					$this->email->subject('Konfirmasi Akun');
                    $this->email->message($message);   
                            
                    if($this->email->send()) // IF EMAIL SUCCESS SEND
                    {
                       echo"<div style='text-align:center'>Registration data has been entered, please confirm your account via email <strong>".$this->input->post('email')."</strong>
                       <br><br>
                       <a href=".base_url().">Back to home</a>
                       </div>";
                    }
                    else // IF EMAIL GAGAL
                    {
                    show_error($this->email->print_debugger());
                    }//send this in email
                    exit;
                };              
            }
        }
        
        
        protected function _islocal(){
            return strpos($_SERVER['HTTP_HOST'], 'local');
        }
        
        public function complete()
        {                                   
            $token = base64_decode($this->uri->segment(4));       
            $cleanToken = $this->security->xss_clean($token);
            
            $user_info = $this->user_model->isTokenValid($cleanToken); //either false or array();           
            
            if(!$user_info){
                $this->session->set_flashdata('flash_message', 'Token is invalid or expired');
                redirect(site_url().'user/login');
            }            
            $data = array(
                'username'=> $user_info->username, 
                'email'=>$user_info->email, 
                'user_id'=>$user_info->id, 
                'token'=>$this->base64url_encode($token),
                'title'=> 'Halaman Konfirmasi',
            );
           
            $this->form_validation->set_rules('password', 'password', 'required|min_length[5]');
            $this->form_validation->set_rules('passconf', 'password Confirmation', 'required|matches[password]');              
            
            if ($this->form_validation->run() == FALSE) {   
                
                $this->load->view('front/user/complete', $data);
             
            }else{
                
                $this->load->library('password');                 
                $post = $this->input->post(NULL, TRUE);
                
                $cleanPost = $this->security->xss_clean($post);
                
                $hashed = $this->password->create_hash($cleanPost['password']);                
                $cleanPost['password'] = $hashed;
                unset($cleanPost['passconf']);
                $userInfo = $this->user_model->updateUserInfo($cleanPost);
                
                if(!$userInfo){
                    $this->session->set_flashdata('flash_message', 'There was a problem updating your record');
                    redirect(site_url().'user/login');
                }
                
                unset($userInfo->password);
                
                foreach($userInfo as $key=>$val){
                    $this->session->set_userdata($key, $val);
                }
                redirect(site_url().'user/');
                
            }
        }
        
        public function login()
        {

            $this->form_validation->set_rules('username', 'Username', 'required');   
            $this->form_validation->set_rules('password', 'password', 'required'); 
            
            if($this->form_validation->run() == FALSE) {
                $this->data['title'] = 'Halaman Login';
                $this->load->view('front/user/login', $this->data);
                // Jangan Lupa deklarasikan $this->data pada $this->load->view

            }else{
                
                $post = $this->input->post();  
                $clean = $this->security->xss_clean($post);
                
                $userInfo = $this->user_model->checkLogin($clean);
                
                if(!$userInfo){
                    $this->session->set_flashdata('flash_message', 'The login was unsucessful');
                    redirect(site_url().'user/login');
                }                
                foreach($userInfo as $key=>$val){
                    $this->session->set_userdata($key, $val);
                }
                redirect(site_url().'user/');
            }
            
        }
        
        public function logout()
        {
            $this->session->sess_destroy();
            redirect(site_url());
        }
        
        public function forgot()
        {
            
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email'); 
            
            if($this->form_validation->run() == FALSE) {
         
                $this->load->view('front/user/forgot');
       
            }else{
                $email = $this->input->post('email');  
                $clean = $this->security->xss_clean($email);
                $userInfo = $this->user_model->getUserInfoByEmail($clean);
                
                if(!$userInfo){
                    $this->session->set_flashdata('flash_message', 'We cant find your email address');
                    redirect(site_url().'user/login');
                }   
                
                if($userInfo->status != $this->status[1]){ //if status is not approved
                    $this->session->set_flashdata('flash_message', 'Your account is not in approved status');
                    redirect(site_url().'user/login');
                }
                
                //build token 
				
                $token = $this->user_model->insertToken($userInfo->id);                        
                $qstring = $this->base64url_encode($token);                  
                $url = site_url() . 'user/reset_password/token/' . $qstring;
                $link = '<a href="' . $url . '">' . $url . '</a>'; 
                
                $message = '';                     
                $message .= '<strong>A password reset has been requested for this email account</strong><br>';
                $message .= '<strong>Please click:</strong> ' . $link;             
                // echo "$message";

     //               //EMAIL	
					$this->load->library('email');
							
                    $config['protocol'] = 'smtp';
                    $config['smtp_host'] = 'ssl://smtp.gmail.com';
                    $config['smtp_user'] = 'info1@rektor.unair.ac.id';  //change it
                    $config['smtp_pass'] = 'tanyapakrektor1'; //change it
                    $config['smtp_port'] = '465';
                    $config['charset'] = 'utf-8';
                    $config['newline'] = "\r\n";
                    $config['mailtype'] = 'html';
                    $config['wordwrap'] = TRUE;
                    $this->email->initialize($config);
                    $this->email->set_newline("\r\n");
                    $this->email->from('adm@pih.unair.ac.id', 'EVENT UNAIR');
                    $this->email->to($this->input->post('email'));
                    $this->email->subject('Lupa password');
                    $this->email->message($message);   

                    if($this->email->send()) // IF EMAIL SUCCESS SEND
                    {
                       echo"Password change request has been entered, please set your password via email <strong>".$this->input->post('email')."<strong>";
                    }
                    else // IF EMAIL GAGAL
                    {
                    show_error($this->email->print_debugger());
                    }//send this in email
                
                exit;
                
            }
            
        }
        
        public function reset_password()
        {
            $token = $this->base64url_decode($this->uri->segment(4));                  
            $cleanToken = $this->security->xss_clean($token);
            
            $user_info = $this->user_model->isTokenValid($cleanToken); //either false or array();               
            
            if(!$user_info){
                $this->session->set_flashdata('flash_message', 'Token is invalid or expired');
                redirect(site_url().'user/login');
            }            
            $data = array(
                'username'=> $user_info->username, 
                'email'=>$user_info->email, 
//                'user_id'=>$user_info->id, 
                'token'=>$this->base64url_encode($token)
            );
           
            $this->form_validation->set_rules('password', 'password', 'required|min_length[5]');
            $this->form_validation->set_rules('passconf', 'password Confirmation', 'required|matches[password]');              
            
            if ($this->form_validation->run() == FALSE) {   

                $this->load->view('front/user/reset_password', $data);

            }else{
                                
                $this->load->library('password');                 
                $post = $this->input->post(NULL, TRUE);                
                $cleanPost = $this->security->xss_clean($post);                
                $hashed = $this->password->create_hash($cleanPost['password']);                
                $cleanPost['password'] = $hashed;
                $cleanPost['user_id'] = $user_info->id;
                unset($cleanPost['passconf']);                
                if(!$this->user_model->updatepassword($cleanPost)){
                    $this->session->set_flashdata('flash_message', 'There was a problem updating your password');
                }else{
                    $this->session->set_flashdata('flash_message', 'Your password has been updated. You may now login');
                }
                redirect(site_url().'user/login');                
            }
        }
        
    public function base64url_encode($data) { 
      return rtrim(strtr(base64_encode($data), '+/', '-_'), '='); 
    } 

    public function base64url_decode($data) { 
      return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT)); 
    }       
}

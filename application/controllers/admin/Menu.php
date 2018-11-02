<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Menu extends CI_Controller
{
  function __construct()
  {
    parent::__construct();
    $this->load->model('Menu_model');
// Ternyata module buat nampilin kata pada breadcrumb
    $this->data['module'] = 'menu';    

    /* cek login */
    if (empty($this->session->userdata['email'])){
      // apabila belum login maka diarahkan ke halaman login
      redirect('user', 'refresh');
    }
    // elseif($this->ion_auth->is_user()){
    //   // apabila belum login maka diarahkan ke halaman login
    //   redirect('admin/auth/login', 'refresh');
    // }
  }

  public function index()
  {
    $this->data['title'] = "Data menu";

    // tampilkan data
    $this->data['menu_data'] = $this->Menu_model->get_all();
    $this->load->view('back/menu/menu_list', $this->data);
  }

  public function create() 
  {
    $this->data['title']          = 'Tambah menu Baru';
    $this->data['action']         = site_url('admin/menu/create_action');
    $this->data['button_submit']  = 'Submit';
    $this->data['button_reset']   = 'Reset';

    $this->data['id_menu'] = array(
      'name'  => 'id_menu',
      'id'    => 'id_menu',
      'type'  => 'hidden',
    );

    $this->data['no_urut'] = array(
      'name'  => 'no_urut',
      'id'    => 'no_urut',
      'type'  => 'text',
      'class' => 'form-control',
      'value' => $this->form_validation->set_value('no_urut'),
      'onkeypress' => 'return isNumberKey(event)',
    );

    $this->data['judul_menu'] = array(
      'name'  => 'judul_menu',
      'id'    => 'judul_menu',
      'type'  => 'text',
      'class' => 'form-control',
      'value' => $this->form_validation->set_value('judul_menu'),
    );

    $this->data['link_menu'] = array(
      'name'  => 'link_menu',
      'id'    => 'link_menu',
      'type'  => 'text',
      'class' => 'form-control',
      'value' => $this->form_validation->set_value('link_menu'),
    ); 

    $this->data['is_main_menu'] = array(
      'name'  => 'is_main_menu',
      'id'    => 'is_main_menu',
      'type'  => 'text',
      'class' => 'form-control',
      'value' => $this->form_validation->set_value('is_main_menu'),
    );    

// Tadi kesalahannya modelnya belum dipanggil
    $this->data['get_combo_menu'] = $this->Menu_model->get_combo_menu(); 
    
    $this->load->view('back/menu/menu_add', $this->data);
  }
  
  public function create_action() 
  {
    $this->_rules();

    if ($this->form_validation->run() == FALSE) 
    {
      $this->create();
    } 
      else 
      {
        $data = array(

          // Tadi gak bisa ngecreate karena pada form is_main_menu lupa belum dipanggil atau belum di deklarasikan. sehingga datanya muncul NULL
          'no_urut'     => $this->input->post('no_urut'),
          'judul_menu'  => strip_tags($this->input->post('judul_menu')),
          'link_menu'     => $this->input->post('link_menu'),
          'is_main_menu'  => $this->input->post('is_main_menu'),
        );

        // eksekusi query INSERT
        $this->Menu_model->insert($data);
        // set pesan data berhasil dibuat
        $this->session->set_flashdata('message', 'Data berhasil dibuat');
        redirect(site_url('admin/menu'));
      }  
  }
  
  public function update($id) 
  {
    $row = $this->Menu_model->get_by_id($id);
    $this->data['menu'] = $this->Menu_model->get_by_id($id);
    //$this->data['menu'] itu untuk deklrasi untuk memparsing data terakhir berdasarkan ID ke form input di menu_edit.

    if ($row) 
    {
      $this->data['title']          = 'Update menu';
      $this->data['action']         = site_url('admin/menu/update_action');
      $this->data['button_submit']  = 'Update';
      $this->data['button_reset']   = 'Reset';

      $this->data['id_menu'] = array(
      'name'  => 'id_menu',
      'id'    => 'id_menu',
      'type'  => 'hidden',
    );

    $this->data['no_urut'] = array(
      'name'  => 'no_urut',
      'id'    => 'no_urut',
      'type'  => 'text',
      'class' => 'form-control',
      'onkeypress' => 'return isNumberKey(event)',
    );

    $this->data['judul_menu'] = array(
      'name'  => 'judul_menu',
      'id'    => 'judul_menu',
      'type'  => 'text',
      'class' => 'form-control',
    );

    $this->data['link_menu'] = array(
      'name'  => 'link_menu',
      'id'    => 'link_menu',
      'type'  => 'text',
      'class' => 'form-control',
     
    ); 

    $this->data['is_main_menu'] = array(
      'name'  => 'is_main_menu',
      'id'    => 'is_main_menu',
      'class' => 'form-control',
    ); 

    // kelupaan untuk mendeklarasikan variabel get_combo_menu sehingga muncul terjadinya error pada bagian menu_edit
    // Message: Undefined variable: get_combo_menu
      $this->data['get_combo_menu'] = $this->Menu_model->get_combo_menu(); 
      $this->load->view('back/menu/menu_edit', $this->data);
    } 
      else 
      {
        $this->session->set_flashdata('message', 'Data tidak ditemukan');
        redirect(site_url('admin/menu'));
      }
  }
  
  public function update_action() 
  {
    $this->_rules();

    if ($this->form_validation->run() == FALSE) 
    {
      $this->update($this->input->post('id_menu'));
    } 
      else 
      {
        $data = array(
          'no_urut'     => $this->input->post('no_urut'),
          'judul_menu'  => strip_tags($this->input->post('judul_menu')),
          'link_menu'     => $this->input->post('link_menu'),
          'is_main_menu'  => $this->input->post('is_main_menu')
        );

        $this->Menu_model->update($this->input->post('id_menu'), $data);
        $this->session->set_flashdata('message', 'Edit Data Berhasil');
        redirect(site_url('admin/menu'));
      }
  }
  
  public function delete($id) 
  {
    $row = $this->Menu_model->get_by_id($id);
    
    if ($row) 
    {
      $this->Menu_model->delete($id);
      $this->session->set_flashdata('message', 'Data berhasil dihapus');
      redirect(site_url('admin/menu'));
    } 
      // Jika data tidak ada
      else 
      {
        $this->session->set_flashdata('message', 'Data tidak ditemukan');
        redirect(site_url('admin/menu'));
      }
  }

  public function _rules() 
  {
    $this->form_validation->set_rules('no_urut', 'No. Urut', 'trim|required');
    $this->form_validation->set_rules('judul_menu', 'Judul Menu', 'trim|required');
    $this->form_validation->set_rules('link_menu', 'Link Menu', 'trim|required');
    // set pesan form validasi error
    $this->form_validation->set_message('required', '{field} wajib diisi');

    $this->form_validation->set_rules('id_menu', 'id_menu', 'trim');
    $this->form_validation->set_error_delimiters('<div class="alert alert-danger alert">', '</div>');
  }

}

/* End of file menu.php */
/* Location: ./application/controllers/menu.php */
/* Please DO NOT modify this information : */
/* Generated by Harviacode Codeigniter CRUD Generator 2016-10-17 02:19:21 */
/* http://harviacode.com */
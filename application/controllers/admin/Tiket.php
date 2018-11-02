<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Tiket extends CI_Controller
{
  function __construct()
  {
    parent::__construct();
    $this->load->model('Acara_model');
    $this->load->model('Tiket_model');

    $this->data['module'] = 'Tiket';    

    /* cek login */
    if (empty($this->session->userdata['email'])){
      // apabila belum login maka diarahkan ke halaman login
      redirect('user', 'refresh');
    }
    // elseif(!$this->ion_auth->is_superadmin()){
    //   // apabila belum login maka diarahkan ke halaman login
    //  // redirect them to the home page because they must be an administrator to view this
    //  return show_error('You must be an administrator to view this page.');
    // }
  }

  public function index()
  {
    $this->data['title'] = "Data tiket";

    // tampilkan data
    if($this->session->userdata('usertype') == 'users'){
      $this->data['tiket_data'] = $this->Tiket_model->get_all_user();  
    }
    else {
      $this->data['tiket_data'] = $this->Tiket_model->get_all(); 
    }
    $this->load->view('back/tiket/tiket_list', $this->data);
    }

  public function create() 
  {
    $this->data['title']          = 'Tambah tiket Baru';
    $this->data['action']         = site_url('admin/tiket/create_action');
    $this->data['button_submit']  = 'Submit';
    $this->data['button_reset']   = 'Reset';

    $this->data['id_tiket'] = array(
      'name'  => 'id_tiket',
      'id'    => 'id_tiket',
      'type'  => 'hidden',
    );

    $this->data['jenis_tiket'] = array(
      'name'  => 'jenis_tiket',
      'id'    => 'jenis_tiket',
      'type'  => 'text',
      'class' => 'form-control',
      'value' => $this->form_validation->set_value('jenis_tiket'),
    );

    $this->data['harga_tiket'] = array(
      'name'  => 'harga_tiket',
      'id'    => 'harga_tiket',
      'type'  => 'text',
      'class' => 'form-control',
      'onkeypress' => 'return isNumberKey(event)',
      'placeholder' => 'isikan dengan angka',
      'value' => $this->form_validation->set_value('harga_tiket'),
    );

    $this->data['quota'] = array(
      'name'  => 'quota',
      'id'    => 'quota',
      'type'  => 'text',
      'class' => 'form-control',
      'onkeypress' => 'return isNumberKey(event)',
      'placeholder' => 'isikan dengan angka',
      'value' => $this->form_validation->set_value('quota'),
    );   

    $this->data['batas_penjualan'] = array(
      'name'  => 'batas_penjualan',
      'id'    => 'batas_penjualan',
      'type'  => 'text',
      'class' => 'form-control tanggal',
      'value' => $this->form_validation->set_value('batas_penjualan'),
    );

    $this->data['tanggal_berakhir_acara'] = array(
      'name'  => 'tanggal_berakhir_acara',
      'id'    => 'tanggal_berakhir_acara',
      'type'  => 'text',
      'class' => 'form-control',
      'placeholder' => 'isikan tanggal berakhir acara',
    );

    // $this->data['status_tiket'] = array(
    //   'Ready'    => 'Ready',
    //   'Closed' => 'Closed',
    // );    

    $this->data['status_tiket_css'] = array(
      'name'  => 'status_tiket',
      'id'    => 'status_tiket',
      'type'  => 'text',
      'class' => 'form-control',
    );

    $this->data['get_combo_acara_css'] = array(
      'name'  => 'get_combo_acara',
      'id'    => 'get_combo_acara',
      'type'  => 'text',
      'class' => 'form-control selectpicker',
      'data-show-subtext' => 'true',
      'data-live-search' => 'true',
      'value' => $this->form_validation->set_value('status_tiket'),
    ); 

    $this->data['get_combo_acara'] = $this->Acara_model->get_combo_acara_tiket_user(); 
    
    $this->load->view('back/tiket/tiket_add', $this->data);
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
          'id_tiket'         => $this->input->post('id_tiket'),
          'jenis_tiket'  => strip_tags($this->input->post('jenis_tiket')),
          'jenis_tiket_seo'  => url_title(strip_tags($this->input->post('jenis_tiket')), 'dash', TRUE),
          'harga_tiket'         => $this->input->post('harga_tiket'),
          'harga_tiket_jual'  => ((($this->input->post('harga_tiket')*5)/100)+($this->input->post('harga_tiket'))),
          'quota'  => $this->input->post('quota'),
          'batas_penjualan'         => $this->input->post('batas_penjualan'),
          'id_acara'      => $this->input->post('get_combo_acara'),
        );

        // eksekusi query INSERT
        $this->Tiket_model->insert($data);
        // set pesan data berhasil dibuat
        $this->session->set_flashdata('message', 'Data berhasil dibuat');
        redirect(site_url('admin/tiket'));
      }  
  }
  
  public function update($id) 
  {

    // Hak akses update
    if($this->session->userdata('usertype') == 'superadmin'){
    $row = $this->Tiket_model->get_by_id($id);
    $this->data['tiket'] = $this->Tiket_model->get_by_id($id);      
    }
    else {
    $row = $this->Tiket_model->get_by_id_user($id);
    $this->data['tiket'] = $this->Tiket_model->get_by_id_user($id); 
    }

    if ($row) 
    {
      // Apabila statusnya users, maka tidak diperbolehkan mengedit data tiket 
      if ($this->session->userdata('usertype') == 'users') {
      $this->session->set_flashdata('message', 'Tidak boleh mengedit data');
      redirect(site_url('admin/tiket'));
      } 

      $this->data['title']          = 'Update tiket';
      $this->data['action']         = site_url('admin/tiket/update_action');
      $this->data['button_submit']  = 'Update';
      $this->data['button_reset']   = 'Reset';

      $this->data['id_tiket'] = array(
        'name'  => 'id_tiket',
        'id'    => 'id_tiket',
        'type'  => 'hidden',
      );

      $this->data['jenis_tiket'] = array(
        'name'  => 'jenis_tiket',
        'id'    => 'jenis_tiket',
        'type'  => 'text',
        'class' => 'form-control',
      );

      $this->data['harga_tiket'] = array(
        'name'  => 'harga_tiket',
        'id'    => 'harga_tiket',
        'type'  => 'text',
        'class' => 'form-control',
        'onkeypress' => 'return isNumberKey(event)',
      );

      $this->data['quota'] = array(
        'name'  => 'quota',
        'id'    => 'quota',
        'type'  => 'text',
        'class' => 'form-control',
        'onkeypress' => 'return isNumberKey(event)',
      );   

      $this->data['batas_penjualan'] = array(
        'name'  => 'batas_penjualan',
        'id'    => 'batas_penjualan',
        'type'  => 'text',
        'class' => 'form-control tanggal',
      );

      // $this->data['status_tiket'] = array(
      //   'Ready'    => 'Ready',
      //   'Closed' => 'Closed',
      // );    

      $this->data['status_tiket_css'] = array(
        'name'  => 'status_tiket',
        'id'    => 'status_tiket',
        'type'  => 'text',
        'class' => 'form-control',
      );

      $this->data['get_combo_acara_css'] = array(
        'name'  => 'get_combo_acara',
        'id'    => 'get_combo_acara',
        'type'  => 'text',
        'class' => 'form-control',
        'readonly' => 'readonly',
      ); 

     $this->data['get_combo_acara'] = $this->Acara_model->get_combo_acara_tiket_user(); 

      $this->load->view('back/tiket/tiket_edit', $this->data);
    } 
      else 
      {
        $this->session->set_flashdata('message', 'Data tidak ditemukan');
        redirect(site_url('admin/tiket'));
      }
  }
  
  public function update_action() 
  {
    $this->_rules();

    if ($this->form_validation->run() == FALSE) 
    {
      $this->update($this->input->post('id_tiket'));
    } 
      else 
      {
        $data = array(
          'jenis_tiket'  => strip_tags($this->input->post('jenis_tiket')),
          'jenis_tiket_seo'  => url_title(strip_tags($this->input->post('jenis_tiket')), 'dash', TRUE),
          'harga_tiket'         => $this->input->post('harga_tiket'),
          'harga_tiket_jual'  => ((($this->input->post('harga_tiket')*5)/100)+($this->input->post('harga_tiket'))),
          'quota'  => $this->input->post('quota'),
          'batas_penjualan'         => $this->input->post('batas_penjualan'),
        );

        $this->Tiket_model->update($this->input->post('id_tiket'), $data);
        $this->session->set_flashdata('message', 'Edit Data Berhasil');
        redirect(site_url('admin/tiket'));
      }
  }
  
  public function delete($id) 
  {
        // Hak akses update
    if($this->session->userdata('usertype') == 'superadmin'){
    $row = $this->Tiket_model->get_by_id($id);
    $this->data['tiket'] = $this->Tiket_model->get_by_id($id);      
    }
    else {
    $row = $this->Tiket_model->get_by_id_user($id);
    $this->data['tiket'] = $this->Tiket_model->get_by_id_user($id); 
    }
    
    if ($row) 
    {
      // Apabila statusnya users, maka tidak diperbolehkan menghapus data tiket 
      if ($this->session->userdata('usertype') == 'users') {
      $this->session->set_flashdata('message', 'Tidak boleh menghapus data');
      redirect(site_url('admin/tiket'));
      } 

      $this->Tiket_model->delete($id);
      $this->session->set_flashdata('message', 'Data berhasil dihapus');
      redirect(site_url('admin/tiket'));
    } 
      // Jika data tidak ada
      else 
      {
        $this->session->set_flashdata('message', 'Data tidak ditemukan');
        redirect(site_url('admin/tiket'));
      }
  }
  
    // Ini untuk menampilkan sugesti pencarian
  function ajax_cek(){
$pegawai = $this->Acara_model->get_cari_acara($_GET['term']);
$data_pegawai = array('judul_acara'   	=>  $pegawai['judul_acara'],
              		'id_acara'  	=>  $pegawai['id_acara'],
              		'id_kategori'    		=>  $pegawai['id_kategori'],);
 echo json_encode($data_pegawai);
  }

  public function _rules() 
  {
    // set pesan form validasi error
    $this->form_validation->set_message('required', '{field} wajib diisi');

    $this->form_validation->set_rules('id_tiket', 'id_tiket', 'trim');
    $this->form_validation->set_rules('jenis_tiket', 'Jenis tiket', 'trim|required');
    $this->form_validation->set_rules('harga_tiket', 'Harga tiket', 'trim|required');
    $this->form_validation->set_rules('quota', 'Quota', 'trim|required');
    $this->form_validation->set_rules('get_combo_acara', 'Acara', 'trim|required');
    $this->form_validation->set_rules('batas_penjualan', 'Acara', 'trim|required');
    $this->form_validation->set_error_delimiters('<div class="alert alert-danger alert">', '</div>');
  }

}

/* End of file tiket.php */
/* Location: ./application/controllers/tiket.php */
/* Please DO NOT modify this information : */
/* Generated by Harviacode Codeigniter CRUD Generator 2016-10-17 02:19:21 */
/* http://harviacode.com */
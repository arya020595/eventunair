<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Checkin extends CI_Controller
{
  function __construct()
  {
    parent::__construct();
    $this->load->model('Acara_model');
    $this->load->model('Transaksi_acara_model');
    $this->load->model('Checkin_model');

    $this->data['module'] = 'Checkin';    

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
    $this->data['title'] = "Data Checkin";
    if($this->session->userdata('usertype') == 'superadmin')
    {
    $this->data['get_tiket_sum']     = $this->Checkin_model->get_tiket_sum();
    }
    else
    {
    $this->data['get_tiket_sum']     = $this->Checkin_model->get_tiket_sum_user();
    }
    // $this->data['get_tiket_sum']     = $this->Acara_model->get_tiket_sum($id);
    $this->load->view('back/checkin/checkin_list', $this->data);
  }

  public function create($id) 
  {
    if ($this->session->userdata('usertype') == 'superadmin') {
      $row = $this->Acara_model->get_by_id($id);
      $this->data['acara'] = $this->Acara_model->get_by_id($id);
    } else {
      $row = $this->Acara_model->get_by_id_user($id);
      $this->data['acara'] = $this->Acara_model->get_by_id_user($id);
    }

    if ($row) 
    {
      $this->data['title']          = 'Check In Acara';
      $this->data['action']         = site_url('admin/checkin/create_action/'.$id);
      $this->data['total_saldo']    = $this->Checkin_model->total_rows($id);

      $this->data['id_barcode_tiket'] = array(
        'name'  => 'id_barcode_tiket',
        'id'    => 'id_barcode_tiket',
        'placeholder' => 'Masukan ID Barcode',
        'class'  => 'form-control',
        'onkeypress' => 'return isNumberKey(event)',
      );

      $this->load->view('back/checkin/checkin_add', $this->data);
      } 
      else 
      {
        $this->session->set_flashdata('message', 'Data tidak ditemukan');
        redirect(site_url('admin/checkin'));
      }
  }
  
  public function create_action($id) {
    $this->_rules();

    if ($this->form_validation->run() == FALSE) 
    {
      $this->create($id);
    } 
      else 
      {
        $post = $this->input->post('id_barcode_tiket');  
        $kode = $this->security->xss_clean($post);
        $check_tiket = $this->Checkin_model->check_tiket($kode, $id);
        $check_duplicate = $this->Checkin_model->check_duplicate($kode, $id);

        if(!$check_tiket){
          $this->session->set_flashdata('message', 'Tiket not found');
          redirect(site_url('admin/checkin/create/'.$id));
        } 
        elseif(!$check_duplicate) {
           $data = array(
          'id_barcode_tiket'        => $this->input->post('id_barcode_tiket'),
          );
          // eksekusi query INSERT
          $this->Checkin_model->insert($data);
          // set pesan data berhasil dibuat
          $this->session->set_flashdata('message', 'Tiket valid');
          redirect(site_url('admin/checkin/create/'.$id));
        }
        else {
          $this->session->set_flashdata('message', 'Tiket has been checked');
          redirect(site_url('admin/checkin/create/'.$id));
        }
      }  
    
    }

  public function _rules() 
  {
    $this->form_validation->set_rules('id_barcode_tiket', 'ID Barkode Tiket', 'trim|required');
    // set pesan form validasi error
    $this->form_validation->set_message('required', '{field} wajib diisi');
    $this->form_validation->set_error_delimiters('<div class="alert alert-danger alert">', '</div>');
  }

  }

/* End of file acara.php */
/* Location: ./application/controllers/acara.php */
/* Please DO NOT modify this information : */
/* Generated by Harviacode Codeigniter CRUD Generator 2016-10-17 02:19:21 */
/* http://harviacode.com */
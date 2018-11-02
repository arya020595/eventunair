<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Penarikan extends CI_Controller
{
  function __construct()
  {
    parent::__construct();
    $this->load->model('Penarikan_model');
    $this->load->model('Acara_model');
    $this->load->model('User_model');
    $this->load->model('Rekening_model');
    $this->load->model('Transaksi_acara_model');

    $this->data['module'] = 'Penarikan';    

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
    $this->data['title'] = "Data Penarikan";
    // Hapus data otomatis penarikan ketika jumlah penarikan melebihi jumlah saldo demgam status_penarikan 'pending'
    $this->Penarikan_model->delete_overlimit();
    // // Ubah saldo otomatis
    // $this->data['total_penarikan']   = $this->Penarikan_model->total_penarikan();
    // $this->data['total_pembayaran']  = $this->Penarikan_model->total_pembayaran();

    // $saldo = $total_pembayaran - $total_penarikan;
    // $data = array(
    //   'saldo' => $saldo, 
    // );

    // // Mengupdate saldo
    // $this->User_model->update($this->session->userdata('id'), $data);

    // Tampilkan data berdasarkan perlakuan seperti superadmin atau users
    if($this->session->userdata('usertype') == 'superadmin'){
    //Tampilkan Semua Data
    $this->data['penarikan_data'] = $this->Penarikan_model->get_all();
    }
    else {
      //Tampilkan Data Sesuai dengan yang dimiliki masing-masing user
      $this->data['penarikan_data'] = $this->Penarikan_model->get_all_user();
    }
      $this->load->view('back/penarikan/penarikan_list', $this->data);
  }

  public function create() 
  {
    $row = $this->User_model->get_by_id_user1();
    // Apabila ada data yang diambil maka definisikan data
    if($row) {
      // Apabila data rekening belum di isi semua, maka kembali ke halaman list penarikan
    if ($row->nama_pemilik_rekening == NULL OR $row->nomor_rekening == 0 OR $row->cabang_bank == NULL) {
      $this->session->set_flashdata('message', 'Mohon isikan rekening terlebih dahulu pada halaman profil');
      redirect(site_url('admin/penarikan'));
    } 
    // Definisikan Data
    $this->data['user']            = $this->User_model->get_by_id_user1();
    $this->data['title']           = 'Tambah Penarikan Baru';
    $this->data['action']          = site_url('admin/penarikan/create_action');
    $this->data['button_submit']   = 'Submit';
    $this->data['button_reset']    = 'Reset';

    $this->data['id_penarikan'] = array(
      'name'  => 'id_penarikan',
      'id'    => 'id_penarikan',
      'type'  => 'hidden',
    );

    $this->data['jumlah_penarikan'] = array(
      'name'  => 'jumlah_penarikan',
      'id'    => 'jumlah_penarikan',
      'type'  => 'text',
      'class' => 'form-control',
      'placeholder' => 'Masukan jumlah yang ditarik',
      'onkeypress' => 'return isNumberKey(event)',
      'value' => $this->form_validation->set_value('jumlah_penarikan'),
    );

    $this->data['nomor_rekening'] = array(
      'name'  => 'nomor_rekening',
      'id'    => 'nomor_rekening',
      'type'  => 'text',
      'class' => 'form-control',
      'readonly' => 'readonly',
    );

    $this->data['jenis_bank'] = array(
      'name'  => 'jenis_bank',
      'id'    => 'jenis_bank',
      'type'  => 'text',
      'class' => 'form-control',
      'readonly' => 'readonly',
    );

    $this->data['saldo'] = array(
      'name'  => 'saldo',
      'id'    => 'saldo',
      'type'  => 'text',
      'class' => 'form-control',
      'readonly' => 'readonly',
    );       

    $this->load->view('back/penarikan/penarikan_add', $this->data);  
    } else {
    $this->session->set_flashdata('message', 'Data tidak ditemukan');
    redirect(site_url('admin/penarikan')); 
    }
  } 

  public function create_action() 
  {
    $this->_rules();

    if ($this->form_validation->run() == FALSE) 
    {
      $this->create($this->session->userdata('id'));
    } 
      else 
      {
        $row = $this->User_model->get_by_id_user1();

        if ($this->input->post('jumlah_penarikan') > $row->saldo) {
        $this->session->set_flashdata('message', 'Jumlah penarikan melebih saldo anda');
        redirect(site_url('admin/penarikan/'));
        }
        
        $data = array(
          'id_penarikan'        => $this->input->post('id_penarikan'),
          'jumlah_penarikan'    => $this->input->post('jumlah_penarikan'),
          'id'                  => $this->session->userdata('id'),
          'status_penarikan'    => 'pending'
        );
        // eksekusi query INSERT
        $this->Penarikan_model->insert($data);
        // set pesan data berhasil dibuat
        $this->session->set_flashdata('message', 'Permintaan penarikan telah terkirim');
        redirect(site_url('admin/penarikan/'));
      }  
  }

  public function update($id)  
  {
    if ($this->session->userdata('usertype') == 'superadmin') {
    $row = $this->Penarikan_model->get_by_id_penarikan($id);

    // Apabila Status transaksi acara == success, maka tidak diperbolehkan mengubah data konfirmasi lagi
    if ($row->status_penarikan == 'sent') {
    $this->session->set_flashdata('message', 'Tidak boleh mengubah data konfirmasi lagi');
    redirect(site_url('admin/penarikan'));
    } else {
    // Apabila data row ditemukan, maka definisikan semua data
    $this->data['penarikan']      = $this->Penarikan_model->get_by_id_penarikan($id);
    $this->data['penarikan1']      = $this->Penarikan_model->get_by_id($id);
    $this->data['action']         = site_url('admin/penarikan/update_action/');
    $this->data['title']          = 'Konfirmasi Penarikan';
    $this->data['button_submit']  = 'Submit';
    $this->data['button_reset']   = 'Reset';

    $this->data['id_penarikan'] = array(
      'name'  => 'id_penarikan',
      'id'    => 'id_penarikan',
      'type'  => 'hidden',
    );

    $this->data['jumlah_penarikan'] = array(
      'name'  => 'jumlah_penarikan',
      'id'    => 'jumlah_penarikan',
      'type'  => 'hidden',
    );

    $this->data['id'] = array(
      'name'  => 'id',
      'id'    => 'id',
      'type'  => 'hidden',
    );

    $this->data['status_penarikan'] = array(
      'pending' => 'pending',
      'sent'    => 'sent',
    );

    $this->data['status_penarikan_css'] = array(
      'name'  => 'status_penarikan_css',
      'id'    => 'status_penarikan_css',
      'type'  => 'text',
      'class' => 'form-control',
    );

    $this->load->view('back/penarikan/penarikan_edit', $this->data);  
    }
  }
  else 
  {
    $this->session->set_flashdata('message', 'Data tidak ditemukan');
    redirect(site_url('admin/penarikan')); 
  }
}

public function update_action() 
  {
    // Definisikan tanggal dan waktu sekarang
    $now = date('Y-m-d H:i:s');
    // Definisikan data apa saja yang akan di masukan kedalam database Penarikan
    $data = array(
    'status_penarikan'       => $this->input->post('status_penarikan_css'), 
    'verifikator'  => $this->session->userdata('username'),
    'time_verif'   => $now,
    );
    // Update data penarikan dari definisi $data
    $this->Penarikan_model->update($this->input->post('id_penarikan'), $data);
    // Mendefiniskan data penarikan berdasarkan ID penarikan yang terklik/terGET
    $row  = $this->Penarikan_model->get_by_id($this->input->post('id_penarikan'));
    // Mendefinisikan status penarikan
    if ($row->status_penarikan == 'sent') {
    $data1 = $this->input->post('jumlah_penarikan');
    // Update data saldo users dari definisi $data1
    $this->User_model->update_saldo_penarikan($this->input->post('id'), $data1);
    }
    // Setelah data masuk berhasil, maka tampilkan pesan
    $this->session->set_flashdata('message', 'Konfirmasi Berhasil');
    redirect(site_url('admin/penarikan'));
  }

public function delete($id)  
{
  $row = $this->Penarikan_model->get_by_id($id);
  // Apabila ada data yang diambil maka definisikan data
  if ($row) {
  // Apabila yg login superadmin, maka tidak boleh menghapus data dan arahkan ke list penarikan
  if ($this->session->userdata('usertype') == 'superadmin') {
  $this->session->set_flashdata('message', 'Tidak boleh menghapus data');
  redirect(site_url('admin/penarikan'));
  }
  // Apabila yg status_penarikan penarikan sudah 'sent', maka tidak boleh menghapus data dan arahkan ke list penarikan 
  elseif($row->status_penarikan == "sent"){
  $this->session->set_flashdata('message', 'Permintaan penarikan sudah di Konfirmasi, tidak boleh menghapus data');
  redirect(site_url('admin/penarikan'));
  }
  // Jika tidak, fungsi delete dijalankan
  else {
    $this->Penarikan_model->delete($id);
    $this->session->set_flashdata('message', 'Permintaan berhasil dibatalkan');
    redirect(site_url('admin/penarikan'));  
  }}
  else {
    $this->session->set_flashdata('message', 'Data tidak ditemukan');
    redirect(site_url('admin/penarikan'));
    } 
  // Apabila  
  // else{
  // $this->session->set_flashdata('message', 'Data tidak ditemukan');
  // redirect(site_url('admin/penarikan'));
  // }
}
public function _rules() 
{

  $this->form_validation->set_rules('jumlah_penarikan', 'jumlah_penarikan', 'required');
  // $this->form->validation->set_rules('something','Something','xss_clean|is_unique['tbl'.users]');

  $this->form_validation->set_error_delimiters('<div class="alert alert-danger alert">', '</div>');
}

}
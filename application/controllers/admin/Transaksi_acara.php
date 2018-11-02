<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Transaksi_acara extends CI_Controller
{
  function __construct()
  {
    parent::__construct();
    $this->load->model('Acara_model');
    $this->load->model('Transaksi_acara_model');
    $this->load->model('User_model');

    $this->data['module'] = 'transaksi_acara';    

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
      $this->data['title'] = "Data transaksi_acara";
      $this->Transaksi_acara_model->delete_expired();
      // Tampilkan data berdasarkan perlakuan seperti superadmin atau users
      if($this->session->userdata('usertype') == 'superadmin'){
      //Tampilkan Semua Data
      $this->data['transaksi_acara_data'] = $this->Transaksi_acara_model->get_all();
      }
      else {
      //Tampilkan Data Sesuai dengan yang dimiliki masing-masing user
      $this->data['transaksi_acara_data'] = $this->Transaksi_acara_model->get_all_user();
      }
      $this->load->view('back/transaksi_acara/transaksi_acara_list', $this->data);
  }

  public function create() 
  {
    $this->data['title']          = 'Tambah transaksi_acara Baru';
    $this->data['action']         = site_url('admin/transaksi_acara/create_action');
    $this->data['button_submit']  = 'Submit';
    $this->data['button_reset']   = 'Reset';

    $this->data['id_transaksi_acara'] = array(
      'name'  => 'id_transaksi_acara',
      'id'    => 'id_transaksi_acara',
      'type'  => 'hidden',
    );

    $this->data['jumlah_pembelian_tiket'] = array(
      'name'  => 'jumlah_pembelian_tiket',
      'id'    => 'jumlah_pembelian_tiket',
      'type'  => 'text',
      'class' => 'form-control',
      'value' => $this->form_validation->set_value('jumlah_pembelian_tiket'),
    );   

    $this->data['total_pembayaran'] = array(
      'name'  => 'total_pembayaran',
      'id'    => 'total_pembayaran',
      'type'  => 'text',
      'class' => 'form-control',
      'value' => $this->form_validation->set_value('total_pembayaran'),
    );

    $this->data['get_combo_acara_css'] = array(
      'name'  => 'get_combo_acara',
      'id'    => 'get_combo_acara',
      'type'  => 'text',
      'class' => 'form-control',
    ); 

    $this->data['get_combo_tiket'] = $this->Transaksi_acara_model->get_combo_acara_transaksi_acara_user();
    $this->data['get_combo_user']  = $this->Transaksi_acara_model->get_combo_user_transaksi_acara_user();  
    
    $this->load->view('back/transaksi_acara/transaksi_acara_add', $this->data);
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
          'id_transaksi_acara'        => $this->input->post('id_transaksi_acara'),
          'jumlah_pembelian_tiket'    => $this->input->post('jumlah_pembelian_tiket'),
          'total_pembayaran'          => $this->input->post('total_pembayaran'),
          'id_tiket'                  => $this->input->post('get_combo_tiket'),
          'id'                        => $this->input->post('get_combo_id'),
        );

        // eksekusi query INSERT
        $this->Transaksi_acara_model->insert($data);
        // set pesan data berhasil dibuat
        $this->session->set_flashdata('message', 'Data berhasil dibuat');
        redirect(site_url('admin/transaksi_acara'));
      }  
  }
  
  public function update($id) 
  {

    if ($this->session->userdata('usertype') == 'superadmin') {
      $row = $this->Transaksi_acara_model->get_by_id($id);
      $this->data['transaksi_acara'] = $this->Transaksi_acara_model->get_by_id($id);
    } else {
      $row = $this->Transaksi_acara_model->get_by_id_user($id);
      $this->data['transaksi_acara'] = $this->Transaksi_acara_model->get_by_id_user($id);
    }

    if ($row) {
      // Deklarasi waktu sekarang
      $now = date('Y-m-d');
      // Deklarasi Total tiket yang dipesan pada ID transaksi tertentu
      // $row2 = $this->Transaksi_acara_model->get_by_id_sum($as);
      // // Sisa Tiket = Jumlah Quota - Total Pembelian Tiket Where Status == Sukses
      // $sisatiket = $row->quota - $row2->jumlah_pembelian_tiket;
      // Apabila Status transaksi acara == success, maka tidak diperbolehkan mengubah data konfirmasi lagi
      if ($row->status_transaksi_acara !== 'Menunggu Pembayaran' AND $this->session->userdata('usertype') == 'users') {
        $this->session->set_flashdata('message', 'Tidak boleh mengubah data konfirmasi lagi');
        redirect(site_url('admin/transaksi_acara'));
      }
      // Apabila acara telah berakhir, maka munculkan message acara telah berakhir
      elseif($row->tanggal_berakhir_acara < $now AND $this->session->userdata('usertype') == 'users') {
        $this->session->set_flashdata('message', 'Acara telah berakhir');
        redirect(site_url('admin/transaksi_acara'));
      }
      // Apabila jumlah pemesanan melebihi sisa kuota terakhir, maka hapus data pemesanan
      // elseif($row->jumlah_pembelian_tiket > $sisatiket AND $this->session->userdata('usertype') == 'users') {
      //   $this->Transaksi_acara_model->delete_limit_kuota($id, $as);
      //   $this->session->set_flashdata('message', 'Oops ! Jumlah tiket yang kamu pesan melebihi kuota yang tersisa, Mohon lakukan pemesanan ulang');
      //   redirect(site_url('admin/transaksi_acara'));
      // }
      // Tampilkan data
      else {
        $this->data['transaksi_acara'] = $this->Transaksi_acara_model->get_by_id($id);
        $this->data['transaksi_acara1'] = $this->Transaksi_acara_model->get_by_id_1($id);
        $this->data['title']          = 'Update transaksi_acara';
        $this->data['action']         = site_url('admin/transaksi_acara/update_action/');
        $this->data['action1']        = site_url('admin/transaksi_acara/update_action_verifikasi/');
        $this->data['button_submit']  = 'Submit';
        $this->data['button_reset']   = 'Reset';

        $this->data['id_transaksi_acara'] = array(
        'name'  => 'id_transaksi_acara',
        'id'    => 'id_transaksi_acara',
        'type'  => 'hidden',
      );
      
      // Ini untuk meng-grab ID user yang empunya acara 
        $this->data['id'] = array(
          'name'  => 'id',
          'id'    => 'id',
          'type'  => 'hidden',
        );   

      // $this->data['total_pembayaran'] = array(
      //   'name'  => 'total_pembayaran',
      //   'id'    => 'total_pembayaran',
      //   'type'  => 'text',
      //   'class' => 'form-control',
      // );

      // $this->data['get_combo_acara_css'] = array(
      //   'name'  => 'get_combo_acara',
      //   'id'    => 'get_combo_acara',
      //   'type'  => 'text',
      //   'class' => 'form-control',
      // );

      $this->data['status_transaksi_acara_css'] = array(
        'name'  => 'status_transaksi_acara',
        'id'    => 'status_transaksi_acara',
        'type'  => 'text',
        'class' => 'form-control',
      );

      $this->data['status_transaksi_acara'] = array(
        'Menunggu Verifikasi'    => 'Waiting for verification',
        'Success' => 'Success',
        'Rejected' => 'Rejected',
      );    

      $this->data['nama_pengirim_transfer'] = array(
        'name'  => 'nama_pengirim_transfer',
        'id'    => 'nama_pengirim_transfer',
        'type'  => 'text',
        'class' => 'form-control',
      );

      $this->data['nominal_transfer'] = array(
        'name'  => 'nominal_transfer',
        'id'    => 'nominal_transfer',
        'type'  => 'text',
        'onkeypress' => 'return isNumberKey(event)',
        'class' => 'form-control',
      );

      $this->data['rekening_tujuan_css'] = array(
        'name'  => 'rekening_tujuan',
        'id'    => 'rekening_tujuan',
        'class' => 'form-control',
      );

      $this->data['rekening_tujuan'] = array(
        'Mandiri'    => 'Mandiri a/n Arya Rifqi Pratama',
        'BCA'        => 'BCA a/n Arya Rifqi Pratama',
      );

      $this->data['tanggal_transfer'] = array(
        'name'  => 'tanggal_transfer',
        'id'    => 'tanggal_transfer',
        'type'  => 'text',
        'class' => 'form-control',
      );

      $this->data['bukti_transfer'] = array(
        'name'  => 'bukti_transfer',
        'id'    => 'bukti_transfer',
        'type'  => 'text',
        'class' => 'form-control',
      );

      if ($this->session->userdata('usertype') == 'superadmin') {
        $this->load->view('back/transaksi_acara/transaksi_acara_verifikasi', $this->data);
      } 
      else {
        $this->load->view('back/transaksi_acara/transaksi_acara_edit', $this->data);
        // var_dump($row2->jumlah_pembelian_tiket);
      }
    // Akhir dari else
    }
      // Data tidak ditemukan (Akhir dari $row)
      } else {
        $this->session->set_flashdata('message', 'Data tidak ditemukan');
        redirect(site_url('admin/transaksi_acara')); 
      }
    // Akhir dari kurung kurawal
  }


  public function update_action() 
  {
    $this->_rules();

    if ($this->form_validation->run() == FALSE) 
    {
      // Ini yang menyebabkan error, karena tidak ada parameter
      $this->update($this->input->post('id_transaksi_acara'));
    } 
      else 
      {
        $nmfile = $this->input->post('id_transaksi_acara');
        $id['id_transaksi_acara'] = $this->input->post('id_transaksi_acara'); 
        
        /* Jika file upload diisi */
        if ($_FILES['userfile']['error'] <> 4) 
        {
          // select column yang akan dihapus (gambar) berdasarkan id
          $this->db->select("bukti_transfer");
          $this->db->where($id);
          $query = $this->db->get('transaksi_acara');
          $row = $query->row();        

          // menyimpan lokasi gambar dalam variable
          $dir = "assets/images/transaksi_acara/".$row->bukti_transfer;

          // Jika ada foto lama, maka hapus foto kemudian upload yang baru
          if($dir)
          {
            $nmfile = $this->input->post('id_transaksi_acara');
            
            // Hapus foto
            // Message: unlink(assets/images/transaksi_acara/): Permission denied
            
            unlink($dir);

            //load uploading file library
            $config['upload_path']      = './assets/images/transaksi_acara/';
            $config['allowed_types']    = 'jpg|jpeg|png|gif';
            $config['max_size']         = '2048'; // 2 MB
            $config['max_width']        = '2000'; //pixels
            $config['max_height']       = '2000'; //pixels
            $config['file_name']        = $nmfile; //nama yang terupload nantinya

            $this->load->library('upload', $config);
            
            // Jika file gagal diupload -> kembali ke form update
            if (!$this->upload->do_upload())
            {   
              $this->update();
            } 
              // Jika file berhasil diupload -> lanjutkan ke query INSERT
              else 
              { 
                $userfile = $this->upload->data();

                if ($this->session->userdata('usertype') == 'superadmin') {
                    $status = $this->input->post('status_transaksi_acara');
                } 
                  else {
                    $status = 'Menunggu Verifikasi';
                }
                $data = array(
                  'nama_pengirim_transfer' => strip_tags($this->input->post('nama_pengirim_transfer')),
                  'nominal_transfer' => $this->input->post('nominal_transfer'),
                  'rekening_tujuan' => $this->input->post('rekening_tujuan'),
                  'tanggal_transfer' => $this->input->post('tanggal_transfer'),                  
                  'bukti_transfer' => $userfile['file_name'],
                  'status_transaksi_acara' => $status,
                );
                $this->Transaksi_acara_model->update($this->input->post('id_transaksi_acara'), $data);
                // var_dump($data);
                $this->session->set_flashdata('message', 'Konfirmasi Data Berhasil');
                redirect(site_url('admin/transaksi_acara'));
              }
          }
            // Jika tidak ada foto pada record, maka upload foto baru
            else
            {
              //load uploading file library
              $config['upload_path']      = './assets/images/transaksi_acara/';
              $config['allowed_types']    = 'jpg|jpeg|png|gif';
              $config['max_size']         = '2048'; // 2 MB
              $config['max_width']        = '2000'; //pixels
              $config['max_height']       = '2000'; //pixels
              $config['file_name']        = $nmfile; //nama yang terupload nantinya

              $this->load->library('upload', $config);
              
              // Jika file gagal diupload -> kembali ke form update
              if (!$this->upload->do_upload())
              {   
                $this->update();
              } 
                // Jika file berhasil diupload -> lanjutkan ke query INSERT
                else 
                { 
                  $userfile = $this->upload->data();

                  if ($this->session->userdata('usertype') == 'superadmin') {
                    $status = $this->input->post('status_transaksi_acara');
                  } 
                  else {
                    $status = 'Menunggu Verifikasi';
                  }

                  $data = array(
                  'nama_pengirim_transfer' => strip_tags($this->input->post('nama_pengirim_transfer')),
                  'nominal_transfer' => $this->input->post('nominal_transfer'),
                  'rekening_tujuan' => $this->input->post('rekening_tujuan'),
                  'tanggal_transfer' => $this->input->post('tanggal_transfer'),                  
                  'bukti_transfer' => $userfile['file_name'],
                  'status_transaksi_acara' => $status,
                  );

                  $this->Transaksi_acara_model->update($this->input->post('id_transaksi_acara'), $data);
                  // var_dump($data);
                  $this->session->set_flashdata('message', 'Konfirmasi Data Berhasil');
                  redirect(site_url('admin/transaksi_acara'));
                }
            }
        }
          // Jika file upload kosong
          else 
          {

            if ($this->session->userdata('usertype') == 'superadmin') {
              $status = $this->input->post('status_transaksi_acara');
            } 
            else {
              $status = 'Menunggu Verifikasi';
            }

            $data = array(
              'nama_pengirim_transfer' => strip_tags($this->input->post('nama_pengirim_transfer')),
              'nominal_transfer' => $this->input->post('nominal_transfer'),
              'rekening_tujuan' => $this->input->post('rekening_tujuan'),
              'tanggal_transfer' => $this->input->post('tanggal_transfer'),
              'status_transaksi_acara' => $status,                  
            );

            $this->Transaksi_acara_model->update($this->input->post('id_transaksi_acara'), $data);
            // var_dump($data);
            $this->session->set_flashdata('message', 'Konfirmasi Data Berhasil');
            redirect(site_url('admin/transaksi_acara'));
          }
      }  
  }

  // Merubah Status Transaksi
  public function update_action_verifikasi()
  // IDnya ini pakai query yang membuat event
  {
    $data = array('status_transaksi_acara' => $this->input->post('status_transaksi_acara'));
    $this->Transaksi_acara_model->update($this->input->post('id_transaksi_acara'), $data);
    // Memanggil semua laba/total penjualan tiket yang berhasil
    $row  = $this->Transaksi_acara_model->get_by_id($this->input->post('id_transaksi_acara'));
    // Mendefinisikan jumlah $saldo
    if ($row->status_transaksi_acara == 'Success') {
    // Deklarasi harga tiket asli untuk dimasukan ke saldo pemiliki acara
    $data1 = $row->total_harga_tiket_asli;
    // Deklarasi kode untuk dimasukan ke dalam database barcode tiket
    $data2 = array();
        $count = $row->jumlah_pembelian_tiket;
        for($i=0; $i<$count; $i++) {
            $data2[] = array(
              'id_barcode_tiket'      => $row->id_transaksi_acara.$row->id_tiket.$row->id_acara.$row->id.$i,
              'id_transaksi_acara'    => $row->id_transaksi_acara,
            );
         }
    // Update data saldo users dari definisi $data1
    $this->User_model->update_saldo($this->input->post('id'), $data1);
    $this->Transaksi_acara_model->insert_barcode_tiket($data2);
    }
    // Setelah upload berhasil, maka tampilkan pesan
    $this->session->set_flashdata('message', 'Ubah Status Berhasil');
    redirect(site_url('admin/transaksi_acara'));
    // print_r($data2);
  }
  
  public function delete($id) 
  {

   if ($this->session->userdata('usertype') == 'superadmin') {
      $row = $this->Transaksi_acara_model->get_by_id($id);
      $this->data['transaksi_acara'] = $this->Transaksi_acara_model->get_by_id($id);
    } else {
      $row = $this->Transaksi_acara_model->get_by_id_user($id);
      $this->data['transaksi_acara'] = $this->Transaksi_acara_model->get_by_id_user($id);
    }

    $this->db->select("bukti_transfer");
    $this->db->where($row);
    $query = $this->db->get('transaksi_acara');
    $row2 = $query->row();        

    // menyimpan lokasi gambar dalam variable
    $dir = "assets/images/transaksi_acara/".$row2->bukti_transfer;
    
    if ($row) 
      {
        if ($row->status_transaksi_acara == 'Menunggu Verifikasi') {
         $this->session->set_flashdata('message', 'Jangan hapus daku dong, kan sayang duitnya');
         redirect(site_url('admin/transaksi_acara'));
        } else {
         unlink($dir);
         $this->Transaksi_acara_model->delete($id);
         $this->session->set_flashdata('message', 'Data berhasil dihapus');
         redirect(site_url('admin/transaksi_acara'));
        }
          
      }      
      // Jika data tidak ada
      else 
      {
        $this->session->set_flashdata('message', 'Data tidak ditemukan');
        redirect(site_url('admin/transaksi_acara'));
      }
  }

  public function transaksi_acara_pdf($id)
  {
      $row = $this->Transaksi_acara_model->get_by_id_user($id);
      if ($row) {

        if ($row->status_transaksi_acara !== 'Success') {
          $this->session->set_flashdata('message', 'Tidak boleh download tiket');
          redirect(site_url('admin/transaksi_acara'));
        }
          $namafile = 'tiket-'.$row->id_transaksi_acara.'.'.'pdf';
          $this->data['transaksi_acara'] = $this->Transaksi_acara_model->get_by_id($id);
          $this->data['transaksi_acara1'] = $this->Transaksi_acara_model->get_by_id_1($id);
          // print_r($namafile);
          // $this->load->view('back/transaksi_acara/transaksi_acara_pdf', $this->data);
          $this->pdf->load_view('back/transaksi_acara/transaksi_acara_pdf', $this->data);
          $this->pdf->render();
          $this->pdf->stream("$namafile");
      } 
      else {
        $this->session->set_flashdata('message', 'Data tidak ditemukan');
        redirect(site_url('admin/transaksi_acara'));
      }
  }

function bikin_barcode($kode)
{
//kita load library nya ini membaca file Zend.php yang berisi loader
//untuk file yang ada pada folder Zend
$this->load->library('zend');
 
//load yang ada di folder Zend
$this->zend->load('Zend/Barcode');
 
//generate barcodenya
//$kode = 12345abc;
Zend_Barcode::render('code128', 'image', array('text'=>$kode), array());
}

  public function _rules() 
  {
    // set pesan form validasi error
    $this->form_validation->set_message('required', '{field} wajib diisi');
    // set perlakuan error
    $this->form_validation->set_rules('id_transaksi_acara', 'id_transaksi_acara', 'trim');
    $this->form_validation->set_rules('nama_pengirim_transfer', 'nama_pengirim_transfer', 'required|trim');
    $this->form_validation->set_rules('nominal_transfer', 'nominal_transfer', 'min_length[3]|required|trim');
    $this->form_validation->set_rules('rekening_tujuan', 'rekening_tujuan', 'required|trim');
    $this->form_validation->set_rules('tanggal_transfer', 'tanggal_transfer', 'required|trim');
    $this->form_validation->set_error_delimiters('<div class="alert alert-danger alert">', '</div>');
  }

}

/* End of file transaksi_acara.php */
/* Location: ./application/controllers/transaksi_acara.php */
/* Please DO NOT modify this information : */
/* Generated by Harviacode Codeigniter CRUD Generator 2016-10-17 02:19:21 */
/* http://harviacode.com */
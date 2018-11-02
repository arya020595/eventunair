<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Komentar extends CI_Controller
{
  function __construct()
  {
    parent::__construct();

    /* memanggil model untuk ditampilkan pada masing2 modul */
    $this->load->model('Komentar_model');

    /* menyiapkan data yang akan disertakan/ ditampilkan pada view */
    $this->data['module'] = 'Komentar';    

    /* cek login */
    if (empty($this->session->userdata['email'])){
      // apabila belum login maka diarahkan ke halaman login
      redirect('user', 'refresh');
    }
   elseif(!$this->session->userdata('usertype') == 'superadmin'){
      // apabila belum login maka diarahkan ke halaman login
     // redirect them to the home page because they must be an administrator to view this
    return show_error('You must be an administrator to view this page.');
    }
  }

  public function index()
  {
    /* menyiapkan data yang akan disertakan/ ditampilkan pada view */
    $this->data['title'] = "Data Komentar";
    
    /* memanggil function dari model yang akan digunakan */
    $this->data['komentar_data'] = $this->Komentar_model->get_all();

    /* memanggil view yang telah disiapkan dan passing data dari model ke view*/
    $this->load->view('back/komentar/komentar_list', $this->data);
  }

  public function pending()
  {
    /* menyiapkan data yang akan disertakan/ ditampilkan pada view */
    $this->data['title'] = "Komentar Pending";

    /* memanggil function dari model yang akan digunakan */
    $this->data['komentar_pending_data'] = $this->Komentar_model->get_all_komentar_pending();

    /* memanggil view yang telah disiapkan dan passing data dari model ke view*/
    $this->load->view('back/komentar/komentar_pending', $this->data);
  }

  public function terima($id)
  {
    /* mengambil/ menangkap data komentar berdasarkan id */
    $row = $this->Komentar_model->get_by_id($id);
      
    /* mengecek data yang ada */
    if ($row) 
    {
      /* menyimpan data ke dalam array */
      $data = array(
        'status'        => 'ya',
        'verifikator'   => $this->session->userdata('username')
      );

      /* proses update data ke model dengan function terima */
      $this->Komentar_model->terima($id, $data);

      /* atur pesan set_flashdata */
      $this->session->set_flashdata('message', 'Komentar berhaisl diterima');

      /* mengarahkan ke halaman tujuan */
      redirect(site_url('admin/komentar'));
    } 
      else 
      {
        /* atur pesan set_flashdata */
        $this->session->set_flashdata('message', 'Data tidak ditemukan');

        /* mengarahkan ke halaman tujuan */
        redirect(site_url('admin/komentar'));
      }
  }

  public function tolak($id)
  {
    /* mengambil/ menangkap data komentar berdasarkan id */
    $row = $this->Komentar_model->get_by_id($id);
      
    /* mengecek data yang ada */
    if ($row) 
    {
      /* proses hapus data ke model dengan function delete */
      $this->Komentar_model->delete($id);

      /* atur pesan set_flashdata */
      $this->session->set_flashdata('message', 'Data berhasil dihapus');

      /* mengarahkan ke halaman tujuan */
      redirect(site_url('admin/komentar/pending'));
    } 
      // Jika data tidak ada
      else 
      {
        /* atur pesan set_flashdata */
        $this->session->set_flashdata('message', 'Data tidak ditemukan');

        /* mengarahkan ke halaman tujuan */
        redirect(site_url('admin/komentar/pending'));
      }
  }

}

/* End of file Komentar.php */
/* Location: ./application/controllers/Komentar.php */
/* Please DO NOT modify this information : */
/* Generated by Harviacode Codeigniter CRUD Generator 2016-10-17 02:19:21 */
/* http://harviacode.com */
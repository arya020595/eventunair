<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Kategori extends CI_Controller
{
  function __construct()
  {
    parent::__construct();
    $this->load->model('Kategori_model');

    $this->data['module'] = 'Kategori';    

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
    $this->data['title'] = "Data Kategori";

    $this->data['kategori_data'] = $this->Kategori_model->get_all();
    $this->load->view('back/kategori/kategori_list', $this->data);
  }

  public function create() 
  {
    $this->data['title']          = 'Tambah Kategori Baru';
    $this->data['action']         = site_url('admin/kategori/create_action');
    $this->data['button_submit']  = 'Submit';
    $this->data['button_reset']   = 'Reset';

    $this->data['id_kategori'] = array(
      'name'  => 'id_kategori',
      'id'    => 'id_kategori',
      'type'  => 'hidden',
    );

    $this->data['judul_kategori'] = array(
      'name'  => 'judul_kategori',
      'id'    => 'judul_kategori',
      'type'  => 'text',
      'class' => 'form-control',
      //fungsi set_value supaya ketika terjadi error, nilai yang di isi sebelumnya masih ada di inputannya
      'value' => $this->form_validation->set_value('judul_kategori'),
    );

    $this->data['deskripsi_kategori'] = array(
      'name'  => 'deskripsi_kategori',
      'id'    => 'ckeditor',
      'class' => 'ckeditor',
      //fungsi set_value supaya ketika terjadi error, nilai yang di isi sebelumnya masih ada di inputannya
      'value' => $this->form_validation->set_value('judul_kategori'),
    );

    $this->load->view('back/kategori/kategori_add', $this->data);
  }
  
  public function create_action() 
  {

  $this->load->helper('judul_seo_helper');
  $this->_rules();

  if ($this->form_validation->run() == FALSE) 
  {
    $this->create();
  } 
    else 
    {
      /* Jika file upload tidak kosong*/
      /* 4 adalah menyatakan tidak ada file yang diupload*/
      if ($_FILES['userfile']['error'] <> 4) 
        // Tadi penyebab tidak bisa upload karena pada bagian form seharusnya pakai form_open_multipart supaya bisa upload gambar
      {
        $nmfile = strip_tags($this->input->post('judul_kategori'));

        /* memanggil library upload ci */
        $config['upload_path']      = './assets/images/kategori/';
        $config['allowed_types']    = 'jpg|jpeg|png|gif';
        $config['max_size']         = '2048'; // 2 MB
        $config['max_width']        = '2000'; //pixels
        $config['max_height']       = '2000'; //pixels
        $config['file_name']        = $nmfile; //nama yang terupload nantinya

        $this->load->library('upload', $config);
        
        if (!$this->upload->do_upload())
        {   //file gagal diupload -> kembali ke form tambah
          $this->create();
        } 
          //file berhasil diupload -> lanjutkan ke query INSERT
          else 
          { 
            $userfile = $this->upload->data();

            $data = array(
              'judul_kategori'  => strip_tags($this->input->post('judul_kategori')),
              'kategori_seo'    => url_title(strip_tags($this->input->post('judul_kategori')), 'dash', TRUE),
              'deskripsi_kategori'  => $this->input->post('deskripsi_kategori'),
              'userfile'      => $nmfile,
              'userfile_type' => $userfile['file_ext'],
              'userfile_size' => $userfile['file_size'],
            );

           // eksekusi query INSERT
           $this->Kategori_model->insert($data);
           // Jangan lupa titik koma

           $this->session->set_flashdata('message', 'Data berhasil dibuat');
           redirect(site_url('admin/kategori'));  
            
            // $this->User_model->insert_tabel($data1);
            // set pesan data berhasil dibuat
          }
        }
        else // Jika file upload kosong
        {
        $data = array(
              'judul_kategori'  => strip_tags($this->input->post('judul_kategori')),
              'kategori_seo'    => url_title(strip_tags($this->input->post('judul_kategori')), 'dash', TRUE),
              'deskripsi_kategori'  => $this->input->post('deskripsi_kategori'),
        );
        // eksekusi query INSERT
        $this->Kategori_model->insert($data);

        $this->session->set_flashdata('message', 'Data berhasil dibuat');
        redirect(site_url('admin/kategori')); 
      }
    }  
}

  
  public function update($id) 
  {
    $row = $this->Kategori_model->get_by_id($id);
    $this->data['kategori'] = $this->Kategori_model->get_by_id($id);

    if ($row) 
    {
      $this->data['title']          = 'Update Kategori';
      $this->data['action']         = site_url('admin/kategori/update_action');
      $this->data['button_submit']  = 'Update';
      $this->data['button_reset']   = 'Reset';

      $this->data['id_kategori'] = array(
        'name'  => 'id_kategori',
        'id'    => 'id_kategori',
        'type'  => 'hidden',
      );

      $this->data['judul_kategori'] = array(
        'name'  => 'judul_kategori',
        'id'    => 'judul_kategori',
        'type'  => 'text',
        'class' => 'form-control',
      );

      $this->data['deskripsi_kategori'] = array(
      'name'  => 'deskripsi_kategori',
      'id'    => 'ckeditor',
      'class' => 'ckeditor',
      //fungsi set_value supaya ketika terjadi error, nilai yang di isi sebelumnya masih ada di inputannya
    );

      $this->load->view('back/kategori/kategori_edit', $this->data);
    } 
      else 
      {
        $this->session->set_flashdata('message', 'Data tidak ditemukan');
        redirect(site_url('admin/kategori'));
      }
  }
  
  public function update_action() 
  {
   $this->load->helper('judul_seo_helper');
    $this->_rules();

    if ($this->form_validation->run() == FALSE) 
    {
      $this->update($this->input->post('id_kategori'));
    } 
      else 
      {
        $nmfile = url_title(strip_tags($this->input->post('judul_kategori')), 'dash', TRUE);
        $id['id_kategori'] = $this->input->post('id_kategori'); 
        
        /* Jika file upload diisi */
        if ($_FILES['userfile']['error'] <> 4) 
        {
          // select column yang akan dihapus (gambar) berdasarkan id
          $this->db->select("userfile, userfile_type");
          $this->db->where($id);
          $query = $this->db->get('kategori');
          $row = $query->row();        

          // menyimpan lokasi gambar dalam variable
          $dir = "assets/images/kategori/".$row->userfile.$row->userfile_type;
          // $dir_thumb = "assets/images/kategori/".$row->userfile.'_thumb'.$row->userfile_type;

          // Jika ada foto lama, maka hapus foto kemudian upload yang baru
          if($dir)
          {
            $nmfile = url_title(strip_tags($this->input->post('judul_kategori')), 'dash', TRUE);
            
            // Hapus foto
            unlink($dir);
            // unlink($dir_thumb);

            //load uploading file library
            $config['upload_path']      = './assets/images/kategori/';
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

                $data = array(
                  'judul_kategori'  => strip_tags($this->input->post('judul_kategori')),
                  'kategori_seo'    => url_title(strip_tags($this->input->post('judul_kategori')), 'dash', TRUE),
                  'deskripsi_kategori'  => $this->input->post('deskripsi_kategori'),
                  'userfile'      => $nmfile,
                  'userfile_type' => $userfile['file_ext'],
                  'userfile_size' => $userfile['file_size'],
                );

                $this->Kategori_model->update($this->input->post('id_kategori'), $data);
                $this->session->set_flashdata('message', 'Edit Data Berhasil');
                redirect(site_url('admin/kategori'));
              }
          }
            // Jika tidak ada foto pada record, maka upload foto baru
            else
            {
              //load uploading file library
              $config['upload_path']      = './assets/images/kategori/';
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

                  $data = array(
                    'judul_kategori'  => strip_tags($this->input->post('judul_kategori')),
                    'kategori_seo'    => url_title(strip_tags($this->input->post('judul_kategori')), 'dash', TRUE),
                    'deskripsi_kategori'  => $this->input->post('deskripsi_kategori'),
                    'userfile'      => $nmfile,
                    'userfile_type' => $userfile['file_ext'],
                    'userfile_size' => $userfile['file_size'],
                  );

                  $this->Kategori_model->update($this->input->post('id_kategori'), $data);
                  $this->session->set_flashdata('message', 'Edit Data Berhasil');
                  redirect(site_url('admin/kategori'));
                }
            }
        }
          // Jika file upload kosong
          else 
          {
            $data = array(
              'judul_kategori'  => strip_tags($this->input->post('judul_kategori')),
              'kategori_seo'    => url_title(strip_tags($this->input->post('judul_kategori')), 'dash', TRUE),
              'deskripsi_kategori'  => $this->input->post('deskripsi_kategori'),
            );

            $this->Kategori_model->update($this->input->post('id_kategori'), $data);
            $this->session->set_flashdata('message', 'Edit Data Berhasil');
            redirect(site_url('admin/kategori'));
          }
      }
  }
  
  public function delete($id) 
  {
    $row = $this->Kategori_model->get_by_id($id);

    $this->db->select("userfile, userfile_type");
    $this->db->where($row);
    $query = $this->db->get('kategori');
    $row2 = $query->row();

    // menyimpan lokasi gambar dalam variable
    $dir = "assets/images/kategori/".$row2->userfile.$row2->userfile_type;
    // $dir_thumb = "assets/images/berita/".$row2->userfile.'_thumb'.$row2->userfile_type;
    
    // Jika data ditemukan, maka hapus foto dan record nya
    if ($row) 
    {
       // Hapus foto
      unlink($dir);
      // unlink($dir_thumb);
      // var_dump($dir);
      $this->Kategori_model->delete($id);
      $this->session->set_flashdata('message', 'Data berhasil dihapus');
      redirect(site_url('admin/kategori'));
    } 
      // Jika data tidak ada
      else 
      {
        $this->session->set_flashdata('message', 'Data tidak ditemukan');
        redirect(site_url('admin/kategori'));
      }
  }

  public function _rules() 
  {
    $this->form_validation->set_rules('judul_kategori', 'Judul Kategori', 'trim|required');

    // set pesan form validasi error
    $this->form_validation->set_message('required', '{field} wajib diisi');

    $this->form_validation->set_rules('id_kategori', 'id_kategori', 'trim');
    $this->form_validation->set_error_delimiters('<div class="alert alert-danger alert">', '</div>');
  }

}

/* End of file Kategori.php */
/* Location: ./application/controllers/Kategori.php */
/* Please DO NOT modify this information : */
/* Generated by Harviacode Codeigniter CRUD Generator 2016-10-17 02:19:21 */
/* http://harviacode.com */
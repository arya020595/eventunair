<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Acara extends CI_Controller
{
  function __construct()
  {
    parent::__construct();
    $this->load->model('Acara_model');
    $this->load->model('Kategori_model');

    $this->data['module'] = 'acara';    

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

    $this->data['title'] = "Data Acara";
    if($this->session->userdata('usertype') == 'superadmin')
    {
    $this->data['acara_data'] = $this->Acara_model->get_all();
    }
    else
    {
    $this->data['acara_data'] = $this->Acara_model->get_all_user(); 
    }
    $this->load->view('back/acara/acara_list', $this->data);
  }

  public function create() 
  {
    $this->data['title']          = 'Tambah Acara Baru';
    $this->data['action']         = site_url('admin/acara/create_action');
    $this->data['button_submit']  = 'Submit';
    $this->data['button_reset']   = 'Reset';

    $this->data['id_acara'] = array(
      'name'  => 'id_acara',
      'id'    => 'id_acara',
      'type'  => 'hidden',
    );

    $this->data['judul_acara'] = array(
      'name'  => 'judul_acara',
      'id'    => 'judul_acara',
      'type'  => 'text',
      'class' => 'form-control',
      'placeholder' => 'isikan judul acara',
      'value' => $this->form_validation->set_value('judul_acara'),
    );

    $this->data['isi_acara'] = array(
      'name'  => 'isi_acara',
      'id'    => 'ckeditor',      
      'class' => 'ckeditor',
      'value' => $this->form_validation->set_value('isi_acara'),
    );

    $this->data['kategori'] = array(
      'name'  => 'kategori',
      'id'    => 'kategori',
      'type'  => 'text',
      'class' => 'form-control',
      'value' => $this->form_validation->set_value('kategori'),
    );

    $this->data['tanggal_mulai_acara'] = array(
      'name'  => 'tanggal_mulai_acara',
      'id'    => 'tanggal_mulai_acara',
      'type'  => 'text',
      'class' => 'form-control tanggal',
      'placeholder' => 'isikan tanggal mulai acara',
      'value' => $this->form_validation->set_value('tanggal_acara'),
    );

    $this->data['tanggal_berakhir_acara'] = array(
      'name'  => 'tanggal_berakhir_acara',
      'id'    => 'tanggal_berakhir_acara',
      'type'  => 'text',
      'class' => 'form-control',
      'placeholder' => 'isikan tanggal berakhir acara',
      'value' => $this->form_validation->set_value('tanggal_acara'),
    );

    $this->data['jam_mulai_acara'] = array(
      'name'  => 'jam_mulai_acara',
      'id'    => 'jam_mulai_acara',
      'type'  => 'text',
      'placeholder' => 'jam mulai acara',
      'class' => 'form-control clockpicker',
    );

    $this->data['jam_berakhir_acara'] = array(
      'name'  => 'jam_berakhir_acara',
      'id'    => 'jam_berakhir_acara',
      'type'  => 'text',
      'class' => 'form-control clockpicker',
      'placeholder' => 'jam berakhir acara',
    );

    $this->data['author'] = array(
      'name'  => 'author',
      'id'    => 'author',
      'type'  => 'text',
      'class' => 'form-control',
      'value' => $this->form_validation->set_value('author'),
      'placeholder' => 'Penyelenggara acara',
    );

     $this->data['lokasi_acara'] = array(
      'name'  => 'lokasi_acara',
      'id'    => 'lokasi_acara',      
      'class' => 'form-control',
      'placeholder' => 'lokasi acara',
      'value' => $this->form_validation->set_value('lokasi_acara'),
    );

    $this->data['publish'] = array(
      'Ya'    => 'Ya',
      'Tidak' => 'Tidak',
    );    

    $this->data['publish_css'] = array(
      'name'  => 'publish',
      'id'    => 'publish',
      'type'  => 'text',
      'class' => 'form-control',
    );

    $this->data['jenis_event'] = array(
      'Free'    => 'Free',
      'Paid' => 'Paid',
    );    

    $this->data['jenis_event_css'] = array(
      'name'  => 'jenis_event',
      'id'    => 'jenis_event',
      'type'  => 'text',
      'class' => 'form-control',
    );

    $this->data['jenis_tiket'] = array(
      'name'  => 'jenis_tiket',
      'id'    => 'jenis_tiket',
      'type'  => 'text',
      'class' => 'form-control',
      'placeholder' => 'Isikan jenis tiket',
      'value' => $this->form_validation->set_value('jenis_tiket'),
    );

    $this->data['quota'] = array(
      'name'  => 'quota',
      'id'    => 'quota',
      'type'  => 'text',
      'class' => 'form-control',
      'placeholder' => 'Isikan jumlah quota',
      'value' => $this->form_validation->set_value('quota'),
      'onkeypress' => 'return isNumberKey(event)',
    );
    
    $this->data['harga_tiket'] = array(
      'name'  => 'harga_tiket',
      'id'    => 'harga_tiket',
      'type'  => 'text',
      'class' => 'form-control',
      'placeholder' => 'Isikan 0 apabila event tidak berbayar',
      'value' => $this->form_validation->set_value('harga_tiket'),
      'onkeypress' => 'return isNumberKey(event)',
    );

    $this->data['batas_penjualan'] = array(
      'name'  => 'batas_penjualan',
      'id'    => 'batas_penjualan',
      'type'  => 'text',
      'class' => 'form-control tanggal',
      'placeholder' => 'Isikan batas akhir penjualan tiket',
      'value' => $this->form_validation->set_value('batas_penjualan'),
    );

    $this->data['get_combo_kategori'] = $this->Kategori_model->get_combo_kategori(); 
    
    $this->load->view('back/acara/acara_add', $this->data);
  }
  
  public function create_action() 
    {

      $this->_rules();

      if ($this->form_validation->run() == FALSE)
      {
      $this->create();
    }

    else {
    // Jika file rundown dan file gambar tidak kosong
    if ($_FILES['userfile']['error'] <> 4 AND $_FILES['userfile1']['error'] <> 4) {
    // setting konfigurasi upload
    $nmfile = url_title(strip_tags($this->input->post('judul_acara')), 'dash', TRUE);
        /* memanggil library upload ci */
        $config['upload_path']      = './assets/images/acara/';
        $config['allowed_types']    = 'jpg|jpeg|png|gif|pdf';
        $config['max_size']         = '2048000'; // 500 kb
        $config['max_width']        = '3000'; //pixels
        $config['max_height']       = '3000'; //pixels
        $config['file_name']        = $nmfile; //nama yang terupload nantinya
        // load library upload
        $this->load->library('upload', $config);

        // upload gambar 1
        $this->upload->do_upload('userfile1');
        $result1 = $this->upload->data();
        // upload gambar 2
        $this->upload->do_upload('userfile');
        $result2 = $this->upload->data();
        // menyimpan hasil upload
        $result = array('userfile1'=>$result1,'userfile'=>$result2,);

        $userfile = $this->upload->data();
        $thumbnail                = $config['file_name']; 
        // library yang disediakan codeigniter
        $config['image_library']  = 'gd2'; 
        // gambar yang akan dibuat thumbnail
        $config['source_image']   = './assets/images/acara/'.$userfile['file_name'].''; 
        $config['create_thumb'] = TRUE;  
        $config['maintain_ratio'] = TRUE;  
        $config['quality']= '50%';
        $config['width'] = 800;  
        $config['height'] = 300;

        $this->load->library('image_lib', $config);
        $this->image_lib->resize();
        
        // menampilkan hasil upload
    // echo "<pre>";
  //       print_r($result);
  //       echo "</pre>";
  //       // cara akses file name dari gambar 1
  //       echo  $result['userfile1']['file_name'];
  //       // cara akses file name dari gambar 1
  //       echo  $result['userfile']['file_name'];

    $data = array(
          'judul_acara'  => strip_tags($this->input->post('judul_acara')),
          'judul_seo'     => url_title(strip_tags($this->input->post('judul_acara')), 'dash', TRUE),
          'isi_acara'    => $this->input->post('isi_acara'),
          'id_kategori'      => $this->input->post('kategori'),
          'tanggal_mulai_acara' => $this->input->post('tanggal_mulai_acara'),
          'tanggal_berakhir_acara' => $this->input->post('tanggal_berakhir_acara'),
          'jam_mulai_acara'    => $this->input->post('jam_mulai_acara'),
          'jam_berakhir_acara'    => $this->input->post('jam_berakhir_acara'),
          'author'        => strip_tags($this->input->post('author')),
          'publish'       => $this->input->post('publish'),
          // 'jenis_event'   => $this->input->post('jenis_event'),
          'lokasi_acara'  => strip_tags($this->input->post('lokasi_acara')),
          'userfile'      => $nmfile,
          'userfile_type' => $result2['file_ext'],
          'userfile_size' => $result2['file_size'],
          'uploader'      => $this->session->userdata('username'),
          'id'      => $this->session->userdata('id'),
          'file' => $nmfile.''.$result1['file_ext'],
        );

      // eksekusi query INSERT
        $this->Acara_model->insert_acara($data);
        // $this->Acara_model->insert_tabel($data1);
        // set pesan data berhasil dibuat
        $this->session->set_flashdata('message', 'Data berhasil dibuat');
        redirect(site_url('admin/acara'));
    }

    elseif($_FILES['userfile']['error'] <> 4) // Jika hanya file gambar yang tidak kosong
    {
      // setting konfigurasi upload
    $nmfile = url_title(strip_tags($this->input->post('judul_acara')), 'dash', TRUE);
        /* memanggil library upload ci */
        $config['upload_path']      = './assets/images/acara/';
        $config['allowed_types']    = 'jpg|jpeg|png|gif|pdf';
        $config['max_size']         = '2048000'; // 2 MB
        $config['max_width']        = '2000'; //pixels
        $config['max_height']       = '2000'; //pixels
        $config['file_name']        = $nmfile; //nama yang terupload nantinya
        // load library upload
        $this->load->library('upload', $config);

        // upload gambar 1
        $this->upload->do_upload('userfile1');
        $result1 = $this->upload->data();
        // upload gambar 2
        $this->upload->do_upload('userfile');
        $result2 = $this->upload->data();
        // menyimpan hasil upload
        $result = array('userfile1'=>$result1,'userfile'=>$result2,);

          $userfile = $this->upload->data();
          $thumbnail                = $config['file_name']; 
        // library yang disediakan codeigniter
          $config['image_library']  = 'gd2'; 
           // gambar yang akan dibuat thumbnail
          $config['source_image']   = './assets/images/acara/'.$userfile['file_name'].''; 
          $config['create_thumb'] = TRUE;  
          $config['maintain_ratio'] = TRUE;  
          $config['quality']= '50%';
          $config['width'] = 800;  
          $config['height'] = 300;

        $this->load->library('image_lib', $config);
        $this->image_lib->resize();

    $data = array(
          'judul_acara'  => strip_tags($this->input->post('judul_acara')),
          'judul_seo'     => url_title(strip_tags($this->input->post('judul_acara')), 'dash', TRUE),
          'isi_acara'    => $this->input->post('isi_acara'),
          'id_kategori'      => $this->input->post('kategori'),
          'tanggal_mulai_acara' => $this->input->post('tanggal_mulai_acara'),
          'tanggal_berakhir_acara' => $this->input->post('tanggal_berakhir_acara'),
          'jam_mulai_acara'    => $this->input->post('jam_mulai_acara'),
          'jam_berakhir_acara'    => $this->input->post('jam_berakhir_acara'),
          'author'        => strip_tags($this->input->post('author')),
          'publish'       => $this->input->post('publish'),
          // 'jenis_event'   => $this->input->post('jenis_event'),
          'lokasi_acara'  => strip_tags($this->input->post('lokasi_acara')),
          'userfile'      => $nmfile,
          'userfile_type' => $result2['file_ext'],
          'userfile_size' => $result2['file_size'],
          'uploader'      => $this->session->userdata('username'),
          'id'      => $this->session->userdata('id'),
        );

        // eksekusi query INSERT
          $this->Acara_model->insert_acara($data);
        // $this->Acara_model->insert_tabel($data1);
        // set pesan data berhasil dibuat
        $this->session->set_flashdata('message', 'Data berhasil dibuat');
        redirect(site_url('admin/acara'));
    }

    elseif($_FILES['userfile1']['error'] <> 4) // Jika hanya file pdf yang tidak kosong
    {
      // setting konfigurasi upload
    $nmfile = url_title(strip_tags($this->input->post('judul_acara')), 'dash', TRUE);
        /* memanggil library upload ci */
        $config['upload_path']      = './assets/images/acara/';
        $config['allowed_types']    = 'jpg|jpeg|png|gif|pdf';
        $config['max_size']         = '2048000'; // 2 MB
        $config['max_width']        = '2000'; //pixels
        $config['max_height']       = '2000'; //pixels
        $config['file_name']        = $nmfile; //nama yang terupload nantinya
        // load library upload
        $this->load->library('upload', $config);

        // upload gambar 1
        $this->upload->do_upload('userfile1');
        $result1 = $this->upload->data();
        // upload gambar 2
        $this->upload->do_upload('userfile');
        $result2 = $this->upload->data();
        // menyimpan hasil upload
        $result = array('userfile1'=>$result1,'userfile'=>$result2,);

        $userfile = $this->upload->data();
        $thumbnail                = $config['file_name']; 
        // library yang disediakan codeigniter
        $config['image_library']  = 'gd2'; 
        // gambar yang akan dibuat thumbnail
        $config['source_image']   = './assets/images/acara/'.$userfile['file_name'].''; 
        $config['create_thumb'] = TRUE;  
        $config['maintain_ratio'] = TRUE;
        $config['quality']= '50%'; 
        
        $config['width'] = 800;  
        $config['height'] = 300;

        $this->load->library('image_lib', $config);
        $this->image_lib->resize();

    $data = array(
          'judul_acara'  => strip_tags($this->input->post('judul_acara')),
          'judul_seo'     => url_title(strip_tags($this->input->post('judul_acara')), 'dash', TRUE),
          'isi_acara'    => $this->input->post('isi_acara'),
          'id_kategori'      => $this->input->post('kategori'),
          'tanggal_mulai_acara' => $this->input->post('tanggal_mulai_acara'),
          'tanggal_berakhir_acara' => $this->input->post('tanggal_berakhir_acara'),
          'jam_mulai_acara'    => $this->input->post('jam_mulai_acara'),
          'jam_berakhir_acara'    => $this->input->post('jam_berakhir_acara'),
          'author'        => strip_tags($this->input->post('author')),
          'publish'       => $this->input->post('publish'),
          // 'jenis_event'   => $this->input->post('jenis_event'),
          'lokasi_acara'  => strip_tags($this->input->post('lokasi_acara')),
          'uploader'      => $this->session->userdata('username'),
          'id'      => $this->session->userdata('id'),
          'file' => $nmfile.''.$result1['file_ext'],

        );
        // eksekusi query INSERT
          $this->Acara_model->insert_acara($data);
        // $this->Acara_model->insert_tabel($data1);
        // set pesan data berhasil dibuat
        $this->session->set_flashdata('message', 'Data berhasil dibuat');
        redirect(site_url('admin/acara'));
    }

    else // Jika file upload kosong
        {
          $data = array(
            'judul_acara'  => strip_tags($this->input->post('judul_acara')),
            'judul_seo'     => url_title(strip_tags($this->input->post('judul_acara')), 'dash', TRUE),
            'isi_acara'    => $this->input->post('isi_acara'),
            'id_kategori'      => $this->input->post('kategori'),
            'tanggal_mulai_acara' => $this->input->post('tanggal_mulai_acara'),
            'tanggal_berakhir_acara' => $this->input->post('tanggal_berakhir_acara'),
            'jam_mulai_acara'    => $this->input->post('jam_mulai_acara'),
            'jam_berakhir_acara'    => $this->input->post('jam_berakhir_acara'),
            'author'        => strip_tags($this->input->post('author')),
            'lokasi_acara'  => strip_tags($this->input->post('lokasi_acara')),
            'publish'        => $this->input->post('publish'),
            // 'jenis_event'   => $this->input->post('jenis_event'),
            'uploader'      => $this->session->userdata('username'),
            'id'      => $this->session->userdata('id')
          );

              // eksekusi query INSERT
              $this->Acara_model->insert_acara($data);

          // set pesan data berhasil dibuat
          $this->session->set_flashdata('message', 'Data berhasil dibuat');
          redirect(site_url('admin/acara'));
        }
  }  
        
   }
  
  public function update($id) 
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
      $this->data['title']          = 'Update acara';
      $this->data['action']         = site_url('admin/acara/update_action');
      $this->data['button_submit']  = 'Update';
      $this->data['button_reset']   = 'Reset';

      $this->data['id_acara'] = array(
        'name'  => 'id_acara',
        'id'    => 'id_acara',
        'type'=> 'hidden',
      );

      $this->data['judul_acara'] = array(
        'name'  => 'judul_acara',
        'id'    => 'judul_acara',
        'type'  => 'text',
        'class' => 'form-control',
        'placeholder' => 'Isikan judul acara',
      );

      $this->data['judul_acara_update'] = array(
        'name'  => 'judul_acara_update',
        'id'    => 'judul_acara_update',
        'type'  => 'hidden',
      );

      $this->data['isi_acara'] = array(
        'name'  => 'isi_acara',
        'id'    => 'ckeditor',      
        'class' => 'ckeditor',
      );

      $this->data['kategori_css'] = array(
        'name'  => 'kategori',
        'id'    => 'kategori',
        'class' => 'form-control',
      );

      $this->data['tanggal_mulai_acara'] = array(
      'name'  => 'tanggal_mulai_acara',
      'id'    => 'tanggal_mulai_acara',
      'type'  => 'text',
      'class' => 'form-control tanggal',
      'placeholder' => 'Isikan tanggal mulai acara',
    );

      $this->data['tanggal_berakhir_acara'] = array(
        'name'  => 'tanggal_berakhir_acara',
        'id'    => 'tanggal_berakhir_acara',
        'type'  => 'text',
        'class' => 'form-control',
        'placeholder' => 'Isikan tanggal berakhir acara',
      );

      $this->data['jam_mulai_acara'] = array(
        'name'  => 'jam_mulai_acara',
        'id'    => 'jam_mulai_acara',
        'type'  => 'text',
        'placeholder' => 'Isikan jam mulai acara',
        'class' => 'form-control clockpicker',
      );

      $this->data['jam_berakhir_acara'] = array(
        'name'  => 'jam_berakhir_acara',
        'id'    => 'jam_berakhir_acara',
        'type'  => 'text',
        'class' => 'form-control clockpicker',
        'placeholder' => 'Isikan jam berakhir acara',
      );

      $this->data['author'] = array(
        'name'  => 'author',
        'id'    => 'author',
        'type'  => 'text',
        'class' => 'form-control',
        'placeholder' => 'Isikan Penyelenggara acara',
      );

      $this->data['lokasi_acara'] = array(
      'name'  => 'lokasi_acara',
      'id'    => 'lokasi_acara',      
      'class' => 'form-control',
      );

      $this->data['publish_option'] = array(
        'Ya'    => 'Ya',
        'Tidak' => 'Tidak',
      );  

      $this->data['publish_css'] = array(
        'name'  => 'publish',
        'id'    => 'publish',
        'type'  => 'text',
        'class' => 'form-control',
      ); 

      $this->data['jenis_event_option'] = array(
        'Free'    => 'Free',
        'Paid' => 'Paid',
      );    

      $this->data['jenis_event_css'] = array(
        'name'  => 'jenis_event',
        'id'    => 'jenis_event',
        'type'  => 'text',
        'class' => 'form-control',
      );

      $this->data['jenis_tiket'] = array(
        'name'  => 'jenis_tiket',
        'id'    => 'jenis_tiket',
        'type'  => 'text',
        'class' => 'form-control',
      );

      $this->data['quota'] = array(
        'name'  => 'quota',
        'id'    => 'quota',
        'type'  => 'text',
        'class' => 'form-control',
        'placeholder' => 'Isikan quota acara',
        'onkeypress' => 'return isNumberKey(event)',
      );
      
      $this->data['harga_tiket'] = array(
        'name'  => 'harga_tiket',
        'id'    => 'harga_tiket',
        'type'  => 'text',
        'class' => 'form-control',
        'placeholder' => 'Isikan harga tiket',
        'onkeypress' => 'return isNumberKey(event)',
      );

      $this->data['batas_penjualan'] = array(
        'name'  => 'batas_penjualan',
        'id'    => 'batas_penjualan',
        'type'  => 'text',
        'placeholder' => 'Isikan batas akhir penjualan tiket',
        'class' => 'form-control tanggal',
      );

      $this->data['get_combo_kategori'] = $this->Kategori_model->get_combo_kategori(); 

      $this->load->view('back/acara/acara_edit', $this->data);
      } 
      else 
      {
        $this->session->set_flashdata('message', 'Data tidak ditemukan');
        redirect(site_url('admin/acara'));
      }
  }
  
  public function update_action() {
   
    $this->_rules_update();

    if ($this->form_validation->run() == FALSE) 
    {
      $this->update($this->input->post('id_acara'));
    } 
      else 
      {
        $nmfile = url_title(strip_tags($this->input->post('judul_acara')), 'dash', TRUE);
        $id['id_acara'] = $this->input->post('id_acara'); 
        
        /* Jika file gambar dan file pdf diisi */
        if ($_FILES['userfile1']['error'] <> 4 and $_FILES['userfile']['error'] <> 4) 
        {
          // select column yang akan dihapus (gambar) berdasarkan id
          $this->db->select("userfile, userfile_type, file");
          $this->db->where($id);
          $query = $this->db->get('acara');
          $row = $query->row();        

          // menyimpan lokasi gambar dan file pdf dalam variable
          $dir = "assets/images/acara/".$row->userfile.$row->userfile_type;
          $dir_thumb = "assets/images/acara/".$row->userfile.'_thumb'.$row->userfile_type;
          $file = "assets/images/acara/".$row->file;
          // Jika ada foto dan pdf lama, maka hapus foto dan pdf kemudian upload yang baru
          if($dir)
          {
            $nmfile = url_title(strip_tags($this->input->post('judul_acara')), 'dash', TRUE);
            
             // Hapus foto
            unlink($dir);
            unlink($dir_thumb);
            unlink($file);

            //load uploading file library
            $config['upload_path']      = './assets/images/acara/';
            $config['allowed_types']    = 'jpg|jpeg|png|gif|pdf';
            $config['max_size']         = '2048000'; // 2 MB
            $config['max_width']        = '2000'; //pixels
            $config['max_height']       = '2000'; //pixels
            $config['file_name']        = $nmfile; //nama yang terupload nantinya

            $this->load->library('upload', $config);
                // upload gambar 1
            $this->upload->do_upload('userfile1');
            $result1 = $this->upload->data();
            // upload gambar 2
            $this->upload->do_upload('userfile');
            $result2 = $this->upload->data();
            // menyimpan hasil upload
            $result = array('userfile1'=>$result1,'userfile'=>$result2,);

            $userfile = $this->upload->data();
            $thumbnail                = $config['file_name']; 
            // library yang disediakan codeigniter
            $config['image_library']  = 'gd2'; 
            // gambar yang akan dibuat thumbnail
            $config['source_image']   = './assets/images/acara/'.$userfile['file_name'].''; 
            $config['create_thumb'] = TRUE;  
            $config['maintain_ratio'] = TRUE;  
            $config['quality']= '50%';
            $config['width'] = 800;  
            $config['height'] = 300;

            $this->load->library('image_lib', $config);
            $this->image_lib->resize();

            $data = array(
              'judul_acara'  => strip_tags($this->input->post('judul_acara')),
              'judul_seo'     => url_title(strip_tags($this->input->post('judul_acara')), 'dash', TRUE),
              'isi_acara'    => $this->input->post('isi_acara'),
              'id_kategori'      => $this->input->post('kategori'),
              'tanggal_mulai_acara' => $this->input->post('tanggal_mulai_acara'),
              'tanggal_berakhir_acara' => $this->input->post('tanggal_berakhir_acara'),
              'jam_mulai_acara'    => $this->input->post('jam_mulai_acara'),
              'jam_berakhir_acara'    => $this->input->post('jam_berakhir_acara'),
              'author'        => strip_tags($this->input->post('author')),
              'lokasi_acara'  => strip_tags($this->input->post('lokasi_acara')),
              'publish'        => $this->input->post('publish'),
              // 'jenis_event'   => $this->input->post('jenis_event'),
              'userfile'      => $nmfile,
              'userfile_type' => $result2['file_ext'],
              'userfile_size' => $result2['file_size'],
              'time_update'   => date('Y-m-d'),
              'updater'       => $this->session->userdata('username'),
              'id'      => $this->session->userdata('id'),
              'file' => $result1['file_name'],
            );

            $this->Acara_model->update($this->input->post('id_acara'), $data);
            // $this->Acara_model->update1($this->input->post('id_acara'), $data1);
            $this->session->set_flashdata('message', 'Edit Data Berhasil');
            redirect(site_url('admin/acara'));
            // print_r($data);
          }
            // Jika tidak ada foto pada record, maka upload foto baru
            else
            {
              //load uploading file library
              $config['upload_path']      = './assets/images/acara/';
              $config['allowed_types']    = 'jpg|jpeg|png|gif|pdf';
              $config['max_size']         = '2048000'; // 2 MB
              $config['max_width']        = '3000'; //pixels
              $config['max_height']       = '3000'; //pixels
              $config['file_name']        = $nmfile; //nama yang terupload nantinya

              $this->load->library('upload', $config);
              // upload gambar 1
              $this->upload->do_upload('userfile1');
              $result1 = $this->upload->data();
              // upload gambar 2
              $this->upload->do_upload('userfile');
              $result2 = $this->upload->data();
              // menyimpan hasil upload
              $result = array('userfile1'=>$result1,'userfile'=>$result2,);

              $userfile = $this->upload->data();
              $thumbnail                = $config['file_name']; 
              // library yang disediakan codeigniter
              $config['image_library']  = 'gd2'; 
              // gambar yang akan dibuat thumbnail
              $config['source_image']   = './assets/images/acara/'.$userfile['file_name'].''; 
              $config['create_thumb'] = TRUE;  
              $config['maintain_ratio'] = TRUE;  
              $config['quality']= '50%';
              $config['width'] = 800;  
              $config['height'] = 300;

              $this->load->library('image_lib', $config);
              $this->image_lib->resize();

              $data = array(
                'judul_acara'  => strip_tags($this->input->post('judul_acara')),
                'judul_seo'     => url_title(strip_tags($this->input->post('judul_acara')), 'dash', TRUE),
                'isi_acara'    => $this->input->post('isi_acara'),
                'id_kategori'      => $this->input->post('kategori'),
                'tanggal_mulai_acara' => $this->input->post('tanggal_mulai_acara'),
                'tanggal_berakhir_acara' => $this->input->post('tanggal_berakhir_acara'),
                'jam_mulai_acara'    => $this->input->post('jam_mulai_acara'),
                'jam_berakhir_acara'    => $this->input->post('jam_berakhir_acara'),
                'author'        => strip_tags($this->input->post('author')),
                'lokasi_acara'  => strip_tags($this->input->post('lokasi_acara')),
                'publish'        => $this->input->post('publish'),
                // 'jenis_event'   => $this->input->post('jenis_event'),
                'userfile'      => $nmfile,
                'userfile_type' => $result2['file_ext'],
                'userfile_size' => $result2['file_size'],
                'time_update'   => date('Y-m-d'),
                'updater'      => $this->session->userdata('username'),
                'id'      => $this->session->userdata('id'),
                'file' => $result1['file_name'],
              );

              $this->Acara_model->update($this->input->post('id_acara'), $data);
              // $this->Acara_model->update1($this->input->post('id_acara'), $data1);
              $this->session->set_flashdata('message', 'Edit Data Berhasil');
              redirect(site_url('admin/acara'));
              // print_r($data);
            }
        }

        /* Jika hanya file gambar yang diisi */
        elseif ($_FILES['userfile']['error'] <> 4) 
        {
          // select column yang akan dihapus (gambar) berdasarkan id
          $this->db->select("userfile, userfile_type");
          $this->db->where($id);
          $query = $this->db->get('acara');
          $row = $query->row();        

          // menyimpan lokasi gambar dalam variable
          $dir = "assets/images/acara/".$row->userfile.$row->userfile_type;
          $dir_thumb = "assets/images/acara/".$row->userfile.'_thumb'.$row->userfile_type;
         
          // Jika ada foto lama, maka hapus foto kemudian upload yang baru
          if($dir)
          {
            $nmfile = url_title(strip_tags($this->input->post('judul_acara')), 'dash', TRUE);
            
            // Hapus foto
            unlink($dir);
            unlink($dir_thumb);
           
            //load uploading file library
            $config['upload_path']      = './assets/images/acara/';
            $config['allowed_types']    = 'jpg|jpeg|png|gif|pdf';
            $config['max_size']         = '2048000'; // 2 MB
            $config['max_width']        = '3000'; //pixels
            $config['max_height']       = '3000'; //pixels
            $config['file_name']        = $nmfile; //nama yang terupload nantinya

            $this->load->library('upload', $config);
            // upload gambar 1
            $this->upload->do_upload('userfile1');
            $result1 = $this->upload->data();
            // upload gambar 2
            $this->upload->do_upload('userfile');
            $result2 = $this->upload->data();
            // menyimpan hasil upload
            $result = array('userfile1'=>$result1,'userfile'=>$result2,);

            $userfile = $this->upload->data();
            $thumbnail                = $config['file_name']; 
            // library yang disediakan codeigniter
            $config['image_library']  = 'gd2'; 
            // gambar yang akan dibuat thumbnail
            $config['source_image']   = './assets/images/acara/'.$userfile['file_name'].''; 
            $config['create_thumb'] = TRUE;  
            $config['maintain_ratio'] = TRUE;  
            $config['quality']= '50%';
            $config['width'] = 800;  
            $config['height'] = 300;

            $this->load->library('image_lib', $config);
            $this->image_lib->resize();

            $data = array(
              'judul_acara'  => strip_tags($this->input->post('judul_acara')),
              'judul_seo'     => url_title(strip_tags($this->input->post('judul_acara')), 'dash', TRUE),
              'isi_acara'    => $this->input->post('isi_acara'),
              'id_kategori'      => $this->input->post('kategori'),
              'tanggal_mulai_acara' => $this->input->post('tanggal_mulai_acara'),
              'tanggal_berakhir_acara' => $this->input->post('tanggal_berakhir_acara'),
              'jam_mulai_acara'    => $this->input->post('jam_mulai_acara'),
              'jam_berakhir_acara'    => $this->input->post('jam_berakhir_acara'),
              'author'        => strip_tags($this->input->post('author')),
              'lokasi_acara'  => strip_tags($this->input->post('lokasi_acara')),
              'publish'        => $this->input->post('publish'),
              // 'jenis_event'   => $this->input->post('jenis_event'),
              'userfile'      => $nmfile,
              'userfile_type' => $result2['file_ext'],
              'userfile_size' => $result2['file_size'],
              'time_update'   => date('Y-m-d'),
              'updater'       => $this->session->userdata('username'),
              'id'      => $this->session->userdata('id'),
            );

            $this->Acara_model->update($this->input->post('id_acara'), $data);
            // $this->Acara_model->update1($this->input->post('id_acara'), $data1);
            $this->session->set_flashdata('message', 'Edit Data Berhasil');
            redirect(site_url('admin/acara'));
            // print_r($data);
      }
            
            else
            {
              //load uploading file library
              $config['upload_path']      = './assets/images/acara/';
              $config['allowed_types']    = 'jpg|jpeg|png|gif|pdf';
              $config['max_size']         = '2048000'; // 2 MB
              $config['max_width']        = '3000'; //pixels
              $config['max_height']       = '3000'; //pixels
              $config['file_name']        = $nmfile; //nama yang terupload nantinya

              $this->load->library('upload', $config);
              // upload gambar 1
              $this->upload->do_upload('userfile1');
              $result1 = $this->upload->data();
              // upload gambar 2
              $this->upload->do_upload('userfile');
              $result2 = $this->upload->data();
              // menyimpan hasil upload
              $result = array('userfile1'=>$result1,'userfile'=>$result2,);

              $userfile = $this->upload->data();
              $thumbnail                = $config['file_name']; 
              // library yang disediakan codeigniter
              $config['image_library']  = 'gd2'; 
              // gambar yang akan dibuat thumbnail
              $config['source_image']   = './assets/images/acara/'.$userfile['file_name'].''; 
              $config['create_thumb'] = TRUE;  
              $config['maintain_ratio'] = TRUE;  
              $config['quality']= '50%';
              $config['width'] = 800;  
              $config['height'] = 300;

              $this->load->library('image_lib', $config);
              $this->image_lib->resize();

              $data = array(
                'judul_acara'  => strip_tags($this->input->post('judul_acara')),
                'judul_seo'     => url_title(strip_tags($this->input->post('judul_acara')), 'dash', TRUE),
                'isi_acara'    => $this->input->post('isi_acara'),
                'id_kategori'      => $this->input->post('kategori'),
                'tanggal_mulai_acara' => $this->input->post('tanggal_mulai_acara'),
                'tanggal_berakhir_acara' => $this->input->post('tanggal_berakhir_acara'),
                'jam_mulai_acara'    => $this->input->post('jam_mulai_acara'),
                'jam_berakhir_acara'    => $this->input->post('jam_berakhir_acara'),
                'author'        => strip_tags($this->input->post('author')),
                'lokasi_acara'  => strip_tags($this->input->post('lokasi_acara')),
                'publish'        => $this->input->post('publish'),
                // 'jenis_event'   => $this->input->post('jenis_event'),
                'userfile'      => $nmfile,
                'userfile_type' => $result2['file_ext'],
                'userfile_size' => $result2['file_size'],
                'time_update'   => date('Y-m-d'),
                'updater'      => $this->session->userdata('username'),
                'id'      => $this->session->userdata('id')
              );

              $this->Acara_model->update($this->input->post('id_acara'), $data);
              // $this->Acara_model->update1($this->input->post('id_acara'), $data1);
              $this->session->set_flashdata('message', 'Edit Data Berhasil');
              redirect(site_url('admin/acara'));
              // print_r($data);

                }
            }


        // jika file pdf saja yang terupload
        elseif ($_FILES['userfile1']['error'] <> 4) 
        {
          // select column yang akan dihapus dan pdf berdasarkan id
          $this->db->select("file");
          $this->db->where($id);
          $query = $this->db->get('acara');
          $row = $query->row();        

          // menyimpan lokasi pdf dalam variable
          $file = "assets/images/acara/".$row->file;

          // Jika ada foto lama, maka hapus foto kemudian upload yang baru
          if($file)
          {
            $nmfile = url_title(strip_tags($this->input->post('judul_acara')), 'dash', TRUE);
            
            // Hapus pdf
            unlink($file);

            //load uploading file library
            $config['upload_path']      = './assets/images/acara/';
            $config['allowed_types']    = 'jpg|jpeg|png|gif|pdf';
            $config['max_size']         = '2048000'; // 2 MB
            $config['max_width']        = '3000'; //pixels
            $config['max_height']       = '3000'; //pixels
            $config['file_name']        = $nmfile; //nama yang terupload nantinya

            $this->load->library('upload', $config);
            // upload gambar 1
            $this->upload->do_upload('userfile1');
            $result1 = $this->upload->data();
            // upload gambar 2
            $this->upload->do_upload('userfile');
            $result2 = $this->upload->data();
            // menyimpan hasil upload
            $result = array('userfile1'=>$result1,'userfile'=>$result2,);

            $userfile = $this->upload->data();
            $thumbnail                = $config['file_name']; 
            // library yang disediakan codeigniter
            $config['image_library']  = 'gd2'; 
            // gambar yang akan dibuat thumbnail
            $config['source_image']   = './assets/images/acara/'.$userfile['file_name'].''; 
            $config['create_thumb'] = TRUE;  
            $config['maintain_ratio'] = TRUE;  
            $config['quality']= '50%';
            $config['width'] = 800;  
            $config['height'] = 300;

            $this->load->library('image_lib', $config);
            $this->image_lib->resize();

            $data = array(
              'judul_acara'  => strip_tags($this->input->post('judul_acara')),
              'judul_seo'     => url_title(strip_tags($this->input->post('judul_acara')), 'dash', TRUE),
              'isi_acara'    => $this->input->post('isi_acara'),
              'id_kategori'      => $this->input->post('kategori'),
              'tanggal_mulai_acara' => $this->input->post('tanggal_mulai_acara'),
              'tanggal_berakhir_acara' => $this->input->post('tanggal_berakhir_acara'),
              'jam_mulai_acara'    => $this->input->post('jam_mulai_acara'),
              'jam_berakhir_acara'    => $this->input->post('jam_berakhir_acara'),
              'author'        => strip_tags($this->input->post('author')),
              'lokasi_acara'  => strip_tags($this->input->post('lokasi_acara')),
              'publish'        => $this->input->post('publish'),
              // 'jenis_event'   => $this->input->post('jenis_event'),
              'time_update'   => date('Y-m-d'),
              'updater'       => $this->session->userdata('username'),
              'id'      => $this->session->userdata('id'),
              'file' => $nmfile.''.$result1['file_ext'],
            );

            $this->Acara_model->update($this->input->post('id_acara'), $data);
            // $this->Acara_model->update1($this->input->post('id_acara'), $data1);
            $this->session->set_flashdata('message', 'Edit Data Berhasil');
            redirect(site_url('admin/acara'));
            // print_r($data);
          }
            // Jika tidak ada foto pada record, maka upload foto baru
            else
            {
              //load uploading file library
              $config['upload_path']      = './assets/images/acara/';
              $config['allowed_types']    = 'jpg|jpeg|png|gif|pdf';
              $config['max_size']         = '2048000'; // 2 MB
              $config['max_width']        = '3000'; //pixels
              $config['max_height']       = '3000'; //pixels
              $config['file_name']        = $nmfile; //nama yang terupload nantinya

              $this->load->library('upload', $config);
              // upload gambar 1
              $this->upload->do_upload('userfile1');
              $result1 = $this->upload->data();
              // upload gambar 2
              $this->upload->do_upload('userfile');
              $result2 = $this->upload->data();
              // menyimpan hasil upload
              $result = array('userfile1'=>$result1,'userfile'=>$result2,);

              $userfile = $this->upload->data();
              $thumbnail                = $config['file_name']; 
              // library yang disediakan codeigniter
              $config['image_library']  = 'gd2'; 
              // gambar yang akan dibuat thumbnail
              $config['source_image']   = './assets/images/acara/'.$userfile['file_name'].''; 
              $config['create_thumb'] = TRUE;  
              $config['maintain_ratio'] = TRUE;  
              $config['quality']= '50%';
              $config['width'] = 800;  
              $config['height'] = 300;

              $this->load->library('image_lib', $config);
              $this->image_lib->resize();

              $data = array(
                'judul_acara'  => strip_tags($this->input->post('judul_acara')),
                'judul_seo'     => url_title(strip_tags($this->input->post('judul_acara')), 'dash', TRUE),
                'isi_acara'    => $this->input->post('isi_acara'),
                'id_kategori'      => $this->input->post('kategori'),
                'tanggal_mulai_acara' => $this->input->post('tanggal_mulai_acara'),
                'tanggal_berakhir_acara' => $this->input->post('tanggal_berakhir_acara'),
                'jam_mulai_acara'    => $this->input->post('jam_mulai_acara'),
                'jam_berakhir_acara'    => $this->input->post('jam_berakhir_acara'),
                'author'        => strip_tags($this->input->post('author')),
                'lokasi_acara'  => strip_tags($this->input->post('lokasi_acara')),
                'publish'        => $this->input->post('publish'),
                // 'jenis_event'   => $this->input->post('jenis_event'),
                'userfile'      => $nmfile,
                'userfile_type' => $userfile['file_ext'],
                'userfile_size' => $userfile['file_size'],
                'time_update'   => date('Y-m-d'),
                'updater'      => $this->session->userdata('username'),
                'id'      => $this->session->userdata('id')
              );

              $this->Acara_model->update($this->input->post('id_acara'), $data);
              // $this->Acara_model->update1($this->input->post('id_acara'), $data1);
              $this->session->set_flashdata('message', 'Edit Data Berhasil');
              redirect(site_url('admin/acara'));
              // print_r($data);

            }
        }

          // Jika file upload kosong
          else 
          {
            $data = array(
              'judul_acara'  => strip_tags($this->input->post('judul_acara')),
              'judul_seo'     => url_title(strip_tags($this->input->post('judul_acara')), 'dash', TRUE),
              'isi_acara'    => $this->input->post('isi_acara'),
              'id_kategori'      => $this->input->post('kategori'),
              'tanggal_mulai_acara' => $this->input->post('tanggal_mulai_acara'),
              'tanggal_berakhir_acara' => $this->input->post('tanggal_berakhir_acara'),
              'jam_mulai_acara'    => $this->input->post('jam_mulai_acara'),
              'jam_berakhir_acara'    => $this->input->post('jam_berakhir_acara'),
              'lokasi_acara'  => strip_tags($this->input->post('lokasi_acara')),
              'author'        => strip_tags($this->input->post('author')),
              'publish'       => $this->input->post('publish'),
              // 'jenis_event'   => $this->input->post('jenis_event'),
              'updater'       => $this->session->userdata('username'),
              'id'      => $this->session->userdata('id')
            );

            $this->Acara_model->update($this->input->post('id_acara'), $data);
            // $this->Acara_model->update1($this->input->post('id_acara'), $data1);
            $this->session->set_flashdata('message', 'Edit Data Berhasil');
            redirect(site_url('admin/acara'));
            // print_r($data);
          }
      }  
  }
  
    public function delete($id) 
    {
      if ($this->session->userdata('usertype') == 'superadmin') {
        $row = $this->Acara_model->get_by_id_acara($id);
        $this->data['acara'] = $this->Acara_model->get_by_id_acara($id);
      } else {
        $row = $this->Acara_model->get_by_id_acara_user($id);
        $this->data['acara'] = $this->Acara_model->get_by_id_acara_user($id);
      }

      $this->db->select("userfile, userfile_type, file");
      $this->db->where($row);
      $query = $this->db->get('acara');
      $row2 = $query->row();        

      // menyimpan lokasi gambar dalam variable
      $dir = "assets/images/acara/".$row2->userfile.$row2->userfile_type;
      $dir_thumb = "assets/images/acara/".$row2->userfile.'_thumb'.$row2->userfile_type;
      $file = "assets/images/acara/".$row2->file;

      // Jika data ditemukan, maka hapus foto dan record nya
      if ($row) 
      {
        // Hapus foto
        unlink($dir);
        unlink($dir_thumb);
        unlink($file);

        $this->Acara_model->delete($id);
        $this->Acara_model->delete_tiket($id);
        $this->session->set_flashdata('message', 'Data berhasil dihapus');
        redirect(site_url('admin/acara'));
      } 
        // Jika data tidak ada
        else 
        {
          $this->session->set_flashdata('message', 'Data tidak ditemukan');
          redirect(site_url('admin/acara'));
        }
    }

    public function _rules() 
    {
      $this->form_validation->set_rules('judul_acara', 'Judul acara', 'trim|required|is_unique[acara.judul_acara]');
      $this->form_validation->set_rules('isi_acara', 'Isi acara', 'trim');
      $this->form_validation->set_rules('author', 'Penyelenggara', 'trim|required');
      $this->form_validation->set_rules('lokasi_acara', 'Lokasi Acara', 'trim|required');
      $this->form_validation->set_rules('tanggal_mulai_acara', 'Tanggal Mulai Acara', 'trim|required');
      $this->form_validation->set_rules('tanggal_berakhir_acara', 'Tanggal Berakhir Acara', 'trim|required');
      $this->form_validation->set_rules('jam_mulai_acara', 'Jam Mulai Acara', 'trim|required');
      $this->form_validation->set_rules('jam_berakhir_acara', 'Jam Berakhir Acara', 'trim|required');
      // $this->form_validation->set_rules('jenis_tiket', 'Jenis Tiket', 'trim|required');
      // $this->form_validation->set_rules('quota', 'Judul acara', 'trim|required');
      // $this->form_validation->set_rules('harga_tiket', 'Harga Tiket', 'trim|required');
      $this->form_validation->set_rules('id_acara', 'id_acara', 'trim');
      // set pesan form validasi error
      $this->form_validation->set_message('required', '{field} wajib diisi');
      $this->form_validation->set_message('is_unique', '{field} telah terpakai');

      $this->form_validation->set_error_delimiters('<div class="alert alert-danger alert">', '</div>');
    }

    public function _rules_update() 
    {
      // set pesan form validasi error
      if($this->input->post('judul_acara_update') != strip_tags($this->input->post('judul_acara'))) {
         $is_unique =  '|is_unique[acara.judul_acara]';
      } else {
         $is_unique =  '';
      }

      $this->form_validation->set_rules('judul_acara', 'Judul Acara', 'required|xss_clean|trim'.$is_unique);
      $this->form_validation->set_rules('isi_acara', 'Isi acara', 'trim');
      $this->form_validation->set_rules('author', 'Penyelenggara', 'trim|required');
      $this->form_validation->set_rules('lokasi_acara', 'Lokasi Acara', 'trim|required');
      $this->form_validation->set_rules('tanggal_mulai_acara', 'Tanggal Mulai Acara', 'trim|required');
      $this->form_validation->set_rules('tanggal_berakhir_acara', 'Tanggal Berakhir Acara', 'trim|required');
      $this->form_validation->set_rules('jam_mulai_acara', 'Jam Mulai Acara', 'trim|required');
      $this->form_validation->set_rules('jam_berakhir_acara', 'Jam Berakhir Acara', 'trim|required');
      // $this->form_validation->set_rules('jenis_tiket', 'Jenis Tiket', 'trim|required');
      // $this->form_validation->set_rules('quota', 'Judul acara', 'trim|required');
      // $this->form_validation->set_rules('harga_tiket', 'Harga Tiket', 'trim|required');
      $this->form_validation->set_rules('id_acara', 'id_acara', 'trim');
      // set pesan form validasi error
      $this->form_validation->set_message('required', '{field} wajib diisi');
      $this->form_validation->set_message('is_unique', '{field} telah terpakai');

      $this->form_validation->set_error_delimiters('<div class="alert alert-danger alert">', '</div>');
    }

  }

/* End of file acara.php */
/* Location: ./application/controllers/acara.php */
/* Please DO NOT modify this information : */
/* Generated by Harviacode Codeigniter CRUD Generator 2016-10-17 02:19:21 */
/* http://harviacode.com */
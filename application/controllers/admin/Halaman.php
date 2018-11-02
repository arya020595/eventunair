<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Halaman extends CI_Controller
{
  function __construct()
  {
    parent::__construct();
    $this->load->model('Halaman_model');

    $this->data['module'] = 'Halaman';    

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

    $this->data['title'] = "Data Halaman";
    
    $this->data['halaman_data'] = $this->Halaman_model->get_all(); 
    
    $this->load->view('back/halaman/halaman_list', $this->data);
  }

  public function create() 
  {
    $this->data['title']          = 'Tambah Halaman Baru';
    $this->data['action']         = site_url('admin/halaman/create_action');
    $this->data['button_submit']  = 'Submit';
    $this->data['button_reset']   = 'Reset';

    $this->data['id_halaman'] = array(
      'name'  => 'id_halaman',
      'id'    => 'id_halaman',
      'type'  => 'hidden',
    );

    $this->data['judul_halaman'] = array(
      'name'  => 'judul_halaman',
      'id'    => 'judul_halaman',
      'type'  => 'text',
      'class' => 'form-control',
      'value' => $this->form_validation->set_value('judul_halaman'),
    );

    $this->data['isi_halaman'] = array(
      'name'  => 'isi_halaman',
      'id'    => 'ckeditor',      
      'class' => 'ckeditor',
      'value' => $this->form_validation->set_value('isi_halaman'),
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
    
    $this->load->view('back/halaman/halaman_add', $this->data);
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
        {
          $nmfile = url_title(strip_tags($this->input->post('judul_halaman')), 'dash', TRUE);
          $link_halaman = 'halaman/read/'.url_title(strip_tags($this->input->post('judul_halaman')), 'dash', TRUE).'';

          /* memanggil library upload ci */
          $config['upload_path']      = './assets/images/halaman/';
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
              $thumbnail                = $config['file_name']; 
              // library yang disediakan codeigniter
              $config['image_library']  = 'gd2'; 
              // gambar yang akan dibuat thumbnail
              $config['source_image']   = './assets/images/halaman/'.$userfile['file_name'].''; 
              // membuat thumbnail
              $config['create_thumb']   = TRUE;               
              // rasio resolusi
              $config['maintain_ratio'] = FALSE; 
              // lebar
              $config['width']          = 400; 
              // tinggi
              $config['height']         = 200; 

              $this->load->library('image_lib', $config);
              $this->image_lib->resize();

              $data = array(
                'judul_halaman'  => strip_tags($this->input->post('judul_halaman')),
                'judul_seo_halaman'     => url_title(strip_tags($this->input->post('judul_halaman')), 'dash', TRUE),
                'isi_halaman'    => $this->input->post('isi_halaman'),
                'link_halaman'    => $link_halaman,
                'publish'       => $this->input->post('publish'),
                'userfile'      => $nmfile,
                'userfile_type' => $userfile['file_ext'],
                'userfile_size' => $userfile['file_size'],
              );

              // eksekusi query INSERT
              $this->Halaman_model->insert($data);
              // kesalahannya terletak pada pemanggilan model. Harusnya model diawali dengan huruf besar ketika di panggil
              // $data = array(
              //   'title' => 'My title',
              //   'name' => 'My Name',
              //   'date' => 'My date'
              //   );
              // Produces: INSERT INTO mytable (title, name, date) VALUES ('My title', 'My name', 'My date')

              // set pesan data berhasil dibuat
              $this->session->set_flashdata('message', 'Data berhasil dibuat');
              redirect(site_url('admin/halaman'));
            }
        }
        else // Jika file upload kosong
        {
          $link_halaman = 'halaman/read/'.url_title(strip_tags($this->input->post('judul_halaman')), 'dash', TRUE).'';
          $data = array(
            'judul_halaman'  => strip_tags($this->input->post('judul_halaman')),
            'judul_seo_halaman'     => url_title(strip_tags($this->input->post('judul_halaman')), 'dash', TRUE),
            'isi_halaman'    => $this->input->post('isi_halaman'),
            'publish'       => $this->input->post('publish'),
            'link_halaman'    => $link_halaman,
          );

          // eksekusi query INSERT
          $this->Halaman_model->insert($data);
          // set pesan data berhasil dibuat
          $this->session->set_flashdata('message', 'Data berhasil dibuat');
          redirect(site_url('admin/halaman'));
        }
      }  
  }
  
  public function update($id) 
  {
    $row = $this->Halaman_model->get_by_id($id);
    $this->data['halaman'] = $this->Halaman_model->get_by_id($id);

    if ($row) 
    {
      $this->data['title']          = 'Update halaman';
      $this->data['action']         = site_url('admin/halaman/update_action');
      $this->data['button_submit']  = 'Update';
      $this->data['button_reset']   = 'Reset';

      $this->data['id_halaman'] = array(
        'name'  => 'id_halaman',
        'id'    => 'id_halaman',
        'type'  => 'hidden',
      );

      $this->data['judul_halaman_update'] = array(
        'name'  => 'judul_halaman_update',
        'id'    => 'judul_halaman_update',
        'type'  => 'text',
        'class' => 'form-control',
      );

      $this->data['judul_halaman'] = array(
        'name'  => 'judul_halaman',
        'id'    => 'judul_halaman',
        'type'  => 'hidden',
      );

      $this->data['link_halaman'] = array(
        'name'  => 'link_halaman',
        'id'    => 'link_halaman',
        'type'  => 'text',
        'class' => 'form-control',
      );

      $this->data['isi_halaman'] = array(
        'name'  => 'isi_halaman',
        'id'    => 'ckeditor',      
        'class' => 'ckeditor',
      );

      // $this->data['author'] = array(
      //   'name'  => 'author',
      //   'id'    => 'author',
      //   'type'  => 'text',
      //   'class' => 'form-control',
      // ); 

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

      $this->load->view('back/halaman/halaman_edit', $this->data);
    } 
      else 
      {
        $this->session->set_flashdata('message', 'Data tidak ditemukan');
        redirect(site_url('admin/halaman'));
      }
  }
  
  public function update_action() 
  {
    $this->load->helper('judul_seo_helper');
    $this->_rules_update();

    if ($this->form_validation->run() == FALSE) 
    {
      $this->update($this->input->post('id_halaman'));
    } 
      else 
      {
        $nmfile = url_title($this->input->post('judul_halaman_update'), 'dash', TRUE);
        $id['id_halaman'] = $this->input->post('id_halaman');
        $link_halaman = 'halaman/read/'.url_title($this->input->post('judul_halaman_update'), 'dash', TRUE).''; 
        
        /* Jika file upload diisi */
        if ($_FILES['userfile']['error'] <> 4) 
        {
          // select column yang akan dihapus (gambar) berdasarkan id
          $this->db->select("userfile, userfile_type");
          $this->db->where($id);
          $query = $this->db->get('halaman');
          $row = $query->row();        

          // menyimpan lokasi gambar dalam variable
          $dir = "assets/images/halaman/".$row->userfile.$row->userfile_type;
          $dir_thumb = "assets/images/halaman/".$row->userfile.'_thumb'.$row->userfile_type;

          // Jika ada foto lama, maka hapus foto kemudian upload yang baru
          if($dir)
          {
            $nmfile = url_title($this->input->post('judul_halaman_update'), 'dash', TRUE);
            
            // Hapus foto
            unlink($dir);
            unlink($dir_thumb);

            //load uploading file library
            $config['upload_path']      = './assets/images/halaman/';
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
                // library yang disediakan codeigniter
                $thumbnail                = $config['file_name']; 
                //nama yang terupload nantinya
                $config['image_library']  = 'gd2'; 
                // gambar yang akan dibuat thumbnail
                $config['source_image']   = './assets/images/halaman/'.$userfile['file_name'].''; 
                // membuat thumbnail
                $config['create_thumb']   = TRUE;               
                // rasio resolusi
                $config['maintain_ratio'] = FALSE; 
                // lebar
                $config['width']          = 400; 
                // tinggi
                $config['height']         = 200; 

                $this->load->library('image_lib', $config);
                $this->image_lib->resize();

                $data = array(
                  'judul_halaman'  => $this->input->post('judul_halaman_update'),
                  'judul_seo_halaman'     => url_title($this->input->post('judul_halaman_update'), 'dash', TRUE),
                  'isi_halaman'    => $this->input->post('isi_halaman'),
                  'link_halaman'    => $link_halaman,
                  // 'author'        => $this->session->userdata('identity'),
                  'publish'        => $this->input->post('publish'),
                  'userfile'      => $nmfile,
                  'userfile_type' => $userfile['file_ext'],
                  'userfile_size' => $userfile['file_size'],
                );

                $this->Halaman_model->update($this->input->post('id_halaman'), $data);
                $this->session->set_flashdata('message', 'Edit Data Berhasil');
                redirect(site_url('admin/halaman'));
              }
          }
            // Jika tidak ada foto pada record, maka upload foto baru
            else
            {
              //load uploading file library
              $config['upload_path']      = './assets/images/halaman/';
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
                  // library yang disediakan codeigniter
                  $thumbnail                = $config['file_name']; 
                  //nama yang terupload nantinya
                  $config['image_library']  = 'gd2'; 
                  // gambar yang akan dibuat thumbnail
                  $config['source_image']   = './assets/images/halaman/'.$userfile['file_name'].''; 
                  // membuat thumbnail
                  $config['create_thumb']   = TRUE;               
                  // rasio resolusi
                  $config['maintain_ratio'] = FALSE; 
                  // lebar
                  $config['width']          = 400; 
                  // tinggi
                  $config['height']         = 200; 

                  $this->load->library('image_lib', $config);
                  $this->image_lib->resize();

                  $data = array(
                    'judul_halaman'  => $this->input->post('judul_halaman_update'),
                    'judul_seo_halaman'     => url_title($this->input->post('judul_halaman_update'), 'dash', TRUE),
                    'isi_halaman'    => $this->input->post('isi_halaman'),
                    'link_halaman'    => $link_halaman,
                    // 'author'        => $this->session->userdata('identity'),
                    'publish'        => $this->input->post('publish'),
                    'userfile'      => $nmfile,
                    'userfile_type' => $userfile['file_ext'],
                    'userfile_size' => $userfile['file_size'],
                  );

                  $this->Halaman_model->update($this->input->post('id_halaman'), $data);
                  $this->session->set_flashdata('message', 'Edit Data Berhasil');
                  redirect(site_url('admin/halaman'));
                }
            }
        }
          // Jika file upload kosong
          else 
          {
            $data = array(
              'judul_halaman'  => $this->input->post('judul_halaman_update'),
              'judul_seo_halaman' => url_title($this->input->post('judul_halaman_update'), 'dash', TRUE),
              'isi_halaman'    => $this->input->post('isi_halaman'),
              'link_halaman'    => $link_halaman,
              // 'author'        => $this->session->userdata('identity'),
              'publish'        => $this->input->post('publish'),
            );

            $this->Halaman_model->update($this->input->post('id_halaman'), $data);
            $this->session->set_flashdata('message', 'Edit Data Berhasil');
            redirect(site_url('admin/halaman'));
          }
      }  
  }
  
  public function delete($id) 
  {
    $row = $this->Halaman_model->get_by_id($id);

    $this->db->select("userfile, userfile_type");
    $this->db->where($row);
    $query = $this->db->get('halaman');
    $row2 = $query->row();        

    // menyimpan lokasi gambar dalam variable
    $dir = "assets/images/halaman/".$row2->userfile.$row2->userfile_type;
    $dir_thumb = "assets/images/halaman/".$row2->userfile.'_thumb'.$row2->userfile_type;

    // Jika data ditemukan, maka hapus foto dan record nya
    if ($row) 
    {
      // Hapus foto
      unlink($dir);
      unlink($dir_thumb);

      $this->Halaman_model->delete($id);
      $this->session->set_flashdata('message', 'Data berhasil dihapus');
      redirect(site_url('admin/halaman'));
    } 
      // Jika data tidak ada
      else 
      {
        $this->session->set_flashdata('message', 'Data tidak ditemukan');
        redirect(site_url('admin/halaman'));
      }
  }

  public function _rules() 
  {
    $this->form_validation->set_rules('judul_halaman', 'Judul halaman', 'trim|is_unique[halaman.judul_halaman]|required');
    $this->form_validation->set_rules('isi_halaman', 'Isi halaman', 'trim|required');

    // set pesan form validasi error
    $this->form_validation->set_message('required', '{field} wajib diisi');
    $this->form_validation->set_message('is_unique', '{field} telah terpakai');

    $this->form_validation->set_rules('id_halaman', 'id_halaman', 'trim');
    $this->form_validation->set_error_delimiters('<div class="alert alert-danger alert">', '</div>');
  }

  public function _rules_update() 
  {
    // set pesan form validasi error
    if(strip_tags($this->input->post('judul_halaman')) != $this->input->post('judul_halaman_update')) {
       $is_unique =  '|is_unique[halaman.judul_halaman]';
    } else {
       $is_unique =  '';
    }

    $this->form_validation->set_rules('judul_halaman_update', 'User Name', 'required|xss_clean|trim'.$is_unique);
    $this->form_validation->set_rules('isi_halaman', 'Isi halaman', 'trim|required');

    // set pesan form validasi error
    $this->form_validation->set_message('required', '{field} wajib diisi');
    $this->form_validation->set_message('is_unique', '{field} telah terpakai');

    $this->form_validation->set_rules('id_halaman', 'id_halaman', 'trim');
    $this->form_validation->set_error_delimiters('<div class="alert alert-danger alert">', '</div>');
  }

}

/* End of file halaman.php */
/* Location: ./application/controllers/halaman.php */
/* Please DO NOT modify this information : */
/* Generated by Harviacode Codeigniter CRUD Generator 2016-10-17 02:19:21 */
/* http://harviacode.com */
<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller
{
  function __construct()
  {
    parent::__construct();
    $this->load->model('User_model');
    $this->load->model('Rekening_model');

    $this->data['module'] = 'User';    

    /* cek login */
    if (empty($this->session->userdata['email'])){
      // apabila belum login maka diarahkan ke halaman login
      redirect('user', 'refresh');
    }
    // else($this->session->userdata('usertype') == 'user');
    // {
    //   // apabila belum login maka diarahkan ke halaman login
    //  // redirect them to the home page because they must be an administrator to view this
    //  return show_error('You must be an administrator to view this page.');
    // }
  }

  public function index()
  {
    $this->data['title'] = "Data user";

    // tampilkan data
    if($this->session->userdata('usertype') == 'superadmin'){
      $this->data['user_data'] = $this->User_model->get_all();  
    }
    else {
     return show_error('You must be an administrator to view this page.');
    }
    $this->load->view('back/user/user_list', $this->data);
  }

  public function create() 
  {
    $this->data['title']          = 'Tambah User Baru';
    $this->data['action']         = site_url('admin/user/create_action');
    $this->data['button_submit']  = 'Submit';
    $this->data['button_reset']   = 'Reset';

      $this->data['id'] = array(
        'name'  => 'id',
        'id'    => 'id',
        'type'  => 'hidden',
      );
      $this->data['nama'] = array(
        'name'  => 'nama',
        'id'    => 'nama',
        'type'  => 'text',
        'class'  => 'form-control',
        'value' => $this->form_validation->set_value('nama'),
      );
      $this->data['username'] = array(
        'name'  => 'username',
        'id'    => 'username',
        'type'  => 'text',
        'class'  => 'form-control',
        'value' => $this->form_validation->set_value('username'),
      );
      $this->data['alamat'] = array(
        'name'  => 'alamat',
        'id'    => 'ckeditor',
        'class'  => 'ckeditor',
        'value' => $this->form_validation->set_value('alamat'),
      );
      $this->data['email'] = array(
        'name'  => 'email',
        'id'    => 'email',
        'type'  => 'text',
        'class'  => 'form-control',
        'value' => $this->form_validation->set_value('email'),
      );
      $this->data['phone'] = array(
        'name'  => 'phone',
        'id'    => 'phone',
        'type'  => 'text',
        'class'  => 'form-control',
        'value' => $this->form_validation->set_value('phone'),
        'onkeypress' => 'return isNumberKey(event)',
      );
      $this->data['usertype_css'] = array(
        'name'  => 'usertype',
        'id'    => 'usertype',
        'type'  => 'text',
        'class'  => 'form-control',
      );
      $this->data['password'] = array(
        'name'  => 'password',
        'id'    => 'password',
        'type'  => 'password',
        'class'  => 'form-control',
        'value' => $this->form_validation->set_value('password'),
      );
      $this->data['password_confirm'] = array(
        'name'  => 'password_confirm',
        'id'    => 'password_confirm',
        'type'  => 'password',
        'class'  => 'form-control',
        'value' => $this->form_validation->set_value('password_confirm'),
      );
      $this->data['nama_pemilik_rekening'] = array(
        'name'  => 'nama_pemilik_rekening',
        'id'    => 'nama_pemilik_rekening',
        'type'  => 'text',
        'class'  => 'form-control',
        'value' => $this->form_validation->set_value('nama_pemilik_rekening'),
      );
      $this->data['nomor_rekening'] = array(
        'name'  => 'nomor_rekening',
        'id'    => 'nomor_rekening',
        'type'  => 'text',
        'class'  => 'form-control',
        'value' => $this->form_validation->set_value('nomor_rekening'),
        'onkeypress' => 'return isNumberKey(event)',
      );
      $this->data['get_combo_bank_css'] = array(
          'name'  => 'get_combo_bank_css',
          'id'    => 'get_combo_bank_css',
          'type'  => 'text',
          'class' => 'form-control',
      );
      $this->data['cabang_bank'] = array(
        'name'  => 'cabang_bank',
        'id'    => 'ckeditor',
        'class'  => 'ckeditor',
        'value' => $this->form_validation->set_value('cabang_bank'),
      ); 
      $this->data['get_combo_bank'] = $this->Rekening_model->get_combo_bank();
      $this->data['get_all_users_group'] = $this->User_model->get_all_users_group();
      
      $this->load->view('back/user/user_add', $this->data);
      // $this->data bukan $this->$data. AKA Gak pakai Variabel
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
        /* Jika file upload tidak kosong*/
        /* 4 adalah menyatakan tidak ada file yang diupload*/
        if ($_FILES['userfile']['error'] <> 4) 
          // Tadi penyebab tidak bisa upload karena pada bagian form seharusnya pakai form_open_multipart supaya bisa upload gambar
        {
          $nmfile = $this->input->post('username');

          /* memanggil library upload ci */
          $config['upload_path']      = './assets/images/user/';
          $config['allowed_types']    = 'jpg|jpeg|png|gif|pdf';
          $config['max_size']         = '2048000'; // 2000 kb
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
              $config['source_image']   = './assets/images/user/'.$userfile['file_name'].''; 
              // membuat thumbnail
              $config['create_thumb']   = TRUE;               
              // rasio resolusi
              $config['maintain_ratio'] = TRUE; 
              // lebar
              $config['width']          = 250; 
              // tinggi
              $config['height']         = 50; 

              $this->load->library('image_lib', $config);
              $this->image_lib->resize();
              
              $this->load->library('password');

              $post = $this->input->post('password');
              $hashed = $this->password->create_hash($post);


              $data = array(
                  'nama'  => $this->input->post('nama'),
                  'username'     => $this->input->post('username'),
                  'password'    => $hashed,
                  'email'      => $this->input->post('email'),
                  'phone' => $this->input->post('phone'),
                  'alamat' => $this->input->post('alamat'),
                  'usertype'    => $this->input->post('usertype'),
                  'created_on'    => date('Y-m-d h:i:s A'),
                  'status'   => 'approved',
                  'userfile'      => $nmfile,
                  'userfile_type' => $userfile['file_ext'],
                  'userfile_size' => $userfile['file_size'],
                  'cabang_bank'  => $this->input->post('cabang_bank'),
                  'nama_pemilik_rekening'  => $this->input->post('nama_pemilik_rekening'),
                  'nomor_rekening' => $this->input->post('nomor_rekening'),
                  'id_bank'  => $this->input->post('get_combo_bank_css'),
              );

             // eksekusi query INSERT
                  $this->User_model->insert($data);
                  // Jangan lupa titik koma

                  $this->session->set_flashdata('message', 'Data berhasil dibuat');
                  redirect(site_url('admin/user'));  
              
              // $this->User_model->insert_tabel($data1);
              // set pesan data berhasil dibuat
            }
        }
        else // Jika file upload kosong
        {
          $this->load->library('password');
          $post = $this->input->post('password');
              $hashed = $this->password->create_hash($post);

          $data = array(
                'nama'  => $this->input->post('nama'),
                'username'     => $this->input->post('username'),
                'password'    => $hashed,
                'email'      => $this->input->post('email'),
                'phone' => $this->input->post('phone'),
                'alamat' => $this->input->post('alamat'),
                'usertype'    => $this->input->post('usertype'),
                'created_on'    => date('Y-m-d h:i:s A'),
                'status'   => 'approved',
                'cabang_bank'  => $this->input->post('cabang_bank'),
                'nama_pemilik_rekening'  => $this->input->post('nama_pemilik_rekening'),
                'nomor_rekening' => $this->input->post('nomor_rekening'),
                'id_bank'  => $this->input->post('get_combo_bank_css'),
          );

              // eksekusi query INSERT
              $this->User_model->insert($data);

              $this->session->set_flashdata('message', 'Data berhasil dibuat');
              redirect(site_url('admin/user')); 
        }
      }  
  }
  
  public function update($id) 
  {
    if ($this->session->userdata('usertype') == 'superadmin') {
      $row = $this->User_model->get_by_id($id);
      $this->data['user'] = $this->User_model->get_by_id($id);
    } else {
      $row = $this->User_model->get_by_id_user($id);
      $this->data['user'] = $this->User_model->get_by_id_user($id);
    }

    if ($row) 
    {
      $this->data['title']          = 'Update user';
      $this->data['action']         = site_url('admin/user/update_action');
      $this->data['button_submit']  = 'Update';
      $this->data['button_reset']   = 'Reset';

      $this->data['id'] = array(
        'name'  => 'id',
        'id'    => 'id',
        'type'  => 'hidden',
      );
      $this->data['nama'] = array(
        'name'  => 'nama',
        'id'    => 'nama',
        'type'  => 'text',
        'class'  => 'form-control',
      );
      $this->data['username'] = array(
        'name'  => 'username',
        'id'    => 'username',
        'type'  => 'hidden',
      );
      $this->data['username_update'] = array(
        'name'  => 'username_update',
        'id'    => 'username_update',
        'type'  => 'text',
        'class'  => 'form-control',
      );
      $this->data['alamat'] = array(
        'name'  => 'alamat',
        'id'    => 'ckeditor',
        'type'  => 'text',
        'class'  => 'ckeditor',
      );
      $this->data['email'] = array(
        'name'  => 'email',
        'id'    => 'email',
        'type'  => 'hidden',
        'class'  => 'form-control',
      );
      $this->data['email_update'] = array(
        'name'  => 'email_update',
        'id'    => 'email_update',
        'type'  => 'text',
        'class'  => 'form-control',
      );
      $this->data['phone'] = array(
        'name'  => 'phone',
        'id'    => 'phone',
        'type'  => 'text',
        'class'  => 'form-control',
        'onkeypress' => 'return isNumberKey(event)',
      );
      $this->data['usertype_css'] = array(
        'name'  => 'usertype',
        'id'    => 'usertype',
        'type'  => 'text',
        'class'  => 'form-control',
      );
      
      $this->data['password'] = array(
        'name'  => 'password',
        'id'    => 'password',
        'type'  => 'password',
        'placeholder' => 'Ganti password baru',
        'class'  => 'form-control',
       
      );
      $this->data['password_confirm'] = array(
        'name'  => 'password_confirm',
        'id'    => 'password_confirm',
        'type'  => 'password',
        'placeholder' => 'Konfirmasi password baru',
        'class'  => 'form-control',
      
      );
      $this->data['nama_pemilik_rekening'] = array(
        'name'  => 'nama_pemilik_rekening',
        'id'    => 'nama_pemilik_rekening',
        'type'  => 'text',
        'class'  => 'form-control',
      );
      $this->data['nomor_rekening'] = array(
        'name'  => 'nomor_rekening',
        'id'    => 'nomor_rekening',
        'type'  => 'text',
        'class'  => 'form-control',
        'onkeypress' => 'return isNumberKey(event)',
      );
      $this->data['get_combo_bank_css'] = array(
          'name'  => 'get_combo_bank_css',
          'id'    => 'get_combo_bank_css',
          'type'  => 'text',
          'class' => 'form-control',
      );
      $this->data['cabang_bank'] = array(
        'name'  => 'cabang_bank',
        'id'    => 'ckeditor',
        'class'  => 'ckeditor',
        );

      $this->data['get_combo_bank'] = $this->Rekening_model->get_combo_bank();
      $this->data['get_all_users_group'] = $this->User_model->get_all_users_group();
      
      $this->load->view('back/user/user_edit', $this->data);
    } 
      else 
      {
        $this->session->set_flashdata('message', 'Data tidak ditemukan');
        redirect(site_url('admin/user'));
      }
  }
  
  public function update_action() 
  {
    $this->_rules_update();

    if ($this->form_validation->run() == FALSE) 
    {
      $this->update($this->input->post('id'));
    } 
      else 
      {
         $nmfile = $this->input->post('username_update');
         $id['id'] = $this->input->post('id');

        /* Jika file upload diisi */
        if ($_FILES['userfile']['error'] <> 4) 
        {
          // select column yang akan dihapus (gambar) berdasarkan id
          $this->db->select("userfile, userfile_type");
          $this->db->where($id);
          $query = $this->db->get('users');
          $row = $query->row();        

          // menyimpan lokasi gambar dalam variable
          $dir = "assets/images/user/".$row->userfile.$row->userfile_type;
          $dir_thumb = "assets/images/user/".$row->userfile.'_thumb'.$row->userfile_type;

         // Jika ada foto lama, maka hapus foto kemudian upload yang baru
          if($dir)
          {
            $nmfile = $this->input->post('username_update');
            
            // Hapus foto
            unlink($dir);
            unlink($dir_thumb);

            //load uploading file library
            $config['upload_path']      = './assets/images/user/';
            $config['allowed_types']    = 'jpg|jpeg|png|gif|pdf';
            $config['max_size']         = '2048000'; // 2000 kb
            $config['max_width']        = '2000'; //pixels
            $config['max_height']       = '2000'; //pixels
            $config['file_name']        = $nmfile; //nama yang terupload nantinya


            $this->load->library('upload', $config);
          
          if (!$this->upload->do_upload())
            {   //file gagal diupload -> kembali ke form tambah
              $this->update();
            } 
            //file berhasil diupload -> lanjutkan ke query INSERT
            else 
            { 
              $userfile = $this->upload->data();
              // library yang disediakan codeigniter
              $thumbnail                = $config['file_name']; 
              //nama yang terupload nantinya
              $config['image_library']  = 'gd2'; 
              // gambar yang akan dibuat thumbnail
              $config['source_image']   = './assets/images/user/'.$userfile['file_name'].''; 
              // membuat thumbnail
              $config['create_thumb']   = TRUE;               
              // rasio resolusi
              $config['maintain_ratio'] = TRUE; 
              // lebar
              $config['width']          = 250; 
              // tinggi
              $config['height']         = 50; 

              $this->load->library('image_lib', $config);
              $this->image_lib->resize();
              
              $this->load->library('password');

              $post = $this->input->post('password');
              $hashed = $this->password->create_hash($post);

              
              if ($this->session->userdata('usertype') == 'superadmin') {
                  $usertype = $this->input->post('usertype');
                  } 
              else {
                  $usertype = 'users';
              }

              $data = array(
                  'nama'  => $this->input->post('nama'),
                  'username'     => $this->input->post('username_update'),
                  'email'      => $this->input->post('email_update'),
                  'phone' => $this->input->post('phone'),
                  'usertype' => $usertype,
                  'alamat' => $this->input->post('alamat'),
                  'created_on'    => date('Y-m-d h:i:s A'),
                  'status'   => 'approved',
                  'userfile'      => $nmfile,
                  'userfile_type' => $userfile['file_ext'],
                  'userfile_size' => $userfile['file_size'],
                  'cabang_bank'  => $this->input->post('cabang_bank'),
                  'nama_pemilik_rekening'  => $this->input->post('nama_pemilik_rekening'),
                  'nomor_rekening' => $this->input->post('nomor_rekening'),
                  'id_bank'  => $this->input->post('get_combo_bank_css'),
              );

                // jika password terisi
                if ($this->input->post('password')){
                  $data['password'] = $hashed;
                }

              $this->User_model->update($this->input->post('id'), $data);

              if($this->session->userdata('usertype') == 'superadmin'){
                $this->session->set_flashdata('message', 'Edit Data Berhasil');
                redirect(site_url('admin/user')); 
              }
              else 
              {
                 $this->session->set_flashdata('message', 'Edit Data Berhasil');
                 redirect(site_url('admin/user/update/'.$this->session->userdata('id'))); 
              }

              $this->load->view('back/user/user_list', $this->data);
              }
              
            }
                 // Jika tidak ada foto pada record, maka upload foto baru
            else
            {
              //load uploading file library
              $config['upload_path']      = './assets/images/user/';
              $config['allowed_types']    = 'jpg|jpeg|png|gif';
              $config['max_size']         = '2048000'; // 2000 kb
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
                  $config['source_image']   = './assets/images/user/'.$userfile['file_name'].''; 
                  // membuat thumbnail
                  $config['create_thumb']   = TRUE;               
                  // rasio resolusi
                  $config['maintain_ratio'] = TRUE; 
                  // lebar
                  $config['width']          = 250; 
                  // tinggi
                  $config['height']         = 50; 

                  $this->load->library('image_lib', $config);
                  $this->image_lib->resize();
                  $this->load->library('password');

                  $post = $this->input->post('password');
                  $hashed = $this->password->create_hash($post);

                  // Apabila superadmin login, maka set data usertype sesuai dengan isian form post. Jika tidak, maka set usertype menjadii users
                  if ($this->session->userdata('usertype') == 'superadmin') {
                  $usertype = $this->input->post('usertype');
                  } 
                  else {
                  $usertype = 'users';
                  }

                  $data = array(
                  'nama'  => $this->input->post('nama'),
                  'username'     => $this->input->post('username_update'),
                  'email'      => $this->input->post('email_update'),
                  'phone' => $this->input->post('phone'),
                  'alamat' => $this->input->post('alamat'),
                  'usertype' => $usertype,
                  'created_on'    => date('Y-m-d h:i:s A'),
                  'status'   => 'approved',
                  'userfile'      => $nmfile,
                  'userfile_type' => $userfile['file_ext'],
                  'userfile_size' => $userfile['file_size'],
                  'cabang_bank'  => $this->input->post('cabang_bank'),
                  'nama_pemilik_rekening'  => $this->input->post('nama_pemilik_rekening'),
                  'nomor_rekening' => $this->input->post('nomor_rekening'),
                  'id_bank'  => $this->input->post('get_combo_bank_css'),
                   );

                          // jika password terisi
                    if ($this->input->post('password')){
                      $data['password'] = $hashed;
                    }


                  $this->User_model->update($this->input->post('id'), $data);

                  if($this->session->userdata('usertype') == 'superadmin'){
                      $this->session->set_flashdata('message', 'Edit Data Berhasil');
                      redirect(site_url('admin/user')); 
                  }
                  else 
                  {
                     $this->session->set_flashdata('message', 'Edit Data Berhasil');
                     redirect(site_url('admin/user/update/'.$this->session->userdata('id')), 'refresh'); 
                  }
                }
            }
        }
          // Jika file upload kosong
          else 
          {
             $this->load->library('password');
             $post = $this->input->post('password');
             $hashed = $this->password->create_hash($post);

              // Apabila superadmin login, maka set data usertype sesuai dengan isian form post. Jika tidak, maka set usertype menjadii users
              if ($this->session->userdata('usertype') == 'superadmin') {
              $usertype = $this->input->post('usertype');
              } 
              else {
              $usertype = 'users';
              }

              $data = array(
                    'nama'  => $this->input->post('nama'),
                    'username'     => $this->input->post('username_update'),
                    'email'      => $this->input->post('email_update'),
                    'phone' => $this->input->post('phone'),
                    'alamat' => $this->input->post('alamat'),
                    'usertype' => $usertype,
                    'created_on'    => date('Y-m-d h:i:s A'),
                    'status'   => 'approved',
                    'cabang_bank'  => $this->input->post('cabang_bank'),
                    'nama_pemilik_rekening'  => $this->input->post('nama_pemilik_rekening'),
                    'nomor_rekening' => $this->input->post('nomor_rekening'),
                    'id_bank'  => $this->input->post('get_combo_bank_css'),
              );

               // jika password terisi
              if ($this->input->post('password')){
                $data['password'] = $hashed;
              }

            $this->User_model->update($this->input->post('id'), $data);

              if($this->session->userdata('usertype') == 'superadmin'){
                  $this->session->set_flashdata('message', 'Edit Data Berhasil');
                  redirect(site_url('admin/user')); 
              }
              else 
              {
                 $this->session->set_flashdata('message', 'Edit Data Berhasil');
                 redirect(site_url('admin/user/update/'.$this->session->userdata('id'))); 
              }
      }
  }

}
  
  public function delete($id) 
  {
    $row = $this->User_model->get_by_id($id);
    
    if ($row) 
    {
      $this->User_model->delete($id);
      $this->session->set_flashdata('message', 'Data berhasil dihapus');
      redirect(site_url('admin/user'));
    } 
      // Jika data tidak ada
      else 
      {
        $this->session->set_flashdata('message', 'Data tidak ditemukan');
        redirect(site_url('admin/user'));
      }
  }

  public function banned($id)
  {
    /* mengambil/ menangkap data komentar berdasarkan id */
    $row = $this->User_model->get_by_id($id);
      
    /* mengecek data yang ada */
    if ($row) 
    {
      /* menyimpan data ke dalam array */
      $data = array(
        'status'        => 'banned'
      );

      /* proses update data ke model dengan function banned */
      $this->User_model->banned($id, $data);

      /* atur pesan set_flashdata */
      $this->session->set_flashdata('message', 'User berhasil dibanned');

      /* mengarahkan ke halaman tujuan */
      redirect(site_url('admin/user'));
    } 
      else 
      {
        /* atur pesan set_flashdata */
        $this->session->set_flashdata('message', 'User tidak ditemukan');

        /* mengarahkan ke halaman tujuan */
        redirect(site_url('admin/user'));
      }
  }

  public function unbanned($id)
  {
    /* mengambil/ menangkap data komentar berdasarkan id */
    $row = $this->User_model->get_by_id($id);
      
    /* mengecek data yang ada */
    if ($row) 
    {
      /* menyimpan data ke dalam array */
      $data = array(
        'status'        => 'approved'
      );

       /* proses update data ke model dengan function unbanned */
      $this->User_model->unbanned($id, $data);

      /* atur pesan set_flashdata */
      $this->session->set_flashdata('message', 'User berhasil di un-banned');

      /* mengarahkan ke halaman tujuan */
      redirect(site_url('admin/user'));
    } 
      // Jika data tidak ada
      else 
      {
        /* atur pesan set_flashdata */
        $this->session->set_flashdata('message', 'User tidak ditemukan');

        /* mengarahkan ke halaman tujuan */
        redirect(site_url('admin/user'));
      }
  }

  public function _rules_update() 
  {
    // set pesan form validasi error
    if($this->input->post('username') != $this->input->post('username_update')) {
       $is_unique =  '|is_unique[users.username]';
    } else {
       $is_unique =  '';
    }

    if($this->input->post('email') != $this->input->post('email_update')) {
       $is_unique_email =  '|is_unique[users.email]';
    } else {
       $is_unique_email =  '';
    }

    // update password jika dimasukkan/ diisi
    if ($this->input->post('password')){
      $this->form_validation->set_rules('password', 'Password', 'required|xss_clean|min_length[5]');
      $this->form_validation->set_rules('password_confirm', 'Password konfirmasi', 'required|xss_clean|matches[password]');
    }
    // Kesalahannya karena pada bagian set_rule valuenya tidak menggunakan username_update sehingga terjadi kesalahan
    // Alhamdulillah

    $this->form_validation->set_rules('username_update', 'User Name', 'required|xss_clean|trim'.$is_unique);
    $this->form_validation->set_rules('email_update', 'Email', 'valid_email|xss_clean|required|trim'.$is_unique_email);
    $this->form_validation->set_rules('id', 'id', 'trim');
    

    $this->form_validation->set_message('required', '{field} wajib diisi');
    $this->form_validation->set_message('is_unique', '{field} telah terpakai');
    $this->form_validation->set_message('matches', '{field} tidak cocok ');
    // $this->form->validation->set_rules('something','Something','xss_clean|is_unique['tbl'.users]');

    $this->form_validation->set_error_delimiters('<div class="alert alert-danger alert">', '</div>');
  }

    public function _rules() 
  {

    $this->form_validation->set_rules('username', 'User Name', 'required|xss_clean|trim|is_unique[users.username]|min_length[5]|max_length[12]');
    $this->form_validation->set_rules('email', 'Email', 'valid_email|xss_clean|required|trim|is_unique[users.email]');
    $this->form_validation->set_rules('id', 'id', 'trim');
    $this->form_validation->set_rules('password', 'Password', 'required|xss_clean|min_length[8]');
    $this->form_validation->set_rules('password_confirm', 'Password konfirmasi', 'required|xss_clean|matches[password]');

    $this->form_validation->set_message('required', '{field} wajib diisi');
    $this->form_validation->set_message('is_unique', '{field} telah terpakai');
    $this->form_validation->set_message('matches', '{field} tidak cocok ');
    // $this->form->validation->set_rules('something','Something','xss_clean|is_unique['tbl'.users]');

    $this->form_validation->set_error_delimiters('<div class="alert alert-danger alert">', '</div>');
  }



}

/* End of file user.php */
/* Location: ./application/controllers/user.php */
/* Please DO NOT modify this information : */
/* Generated by Harviacode Codeigniter CRUD Generator 2016-10-17 02:19:21 */
/* http://harviacode.com */
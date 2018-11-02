<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Acara extends CI_Controller {

	function __construct()
  {
    parent::__construct();
    /* memanggil model untuk ditampilkan pada masing2 modul */
    $this->load->model('Acara_model');
    $this->load->model('Featured_model');
    $this->load->model('Kategori_model');
    $this->load->model('Tiket_model');
    $this->load->model('Transaksi_acara_model');
    $this->load->model('Menu_model');

    /* memanggil function dari masing2 model yang akan digunakan */
    $this->data['get_all_acara_sidebar']   = $this->Acara_model->get_all_acara_sidebar();
    $this->data['get_all_kategori_sidebar'] = $this->Kategori_model->get_all_kategori_sidebar();
    $this->data['get_all_komentar_sidebar'] = $this->Acara_model->get_all_komentar_sidebar();
    $this->data['get_kategori']      = $this->Kategori_model->get_all();
    $this->data['get_all_tiket']      = $this->Tiket_model->get_all();
  }

  // Ini untuk ajax show data
  private $perPage = 5;

	public function read($id) // Mengambil parameter $id
	{
    /* mengambil data berdasarkan id */
	  $row = $this->Acara_model->get_by_id($id);
    /* melakukan pengecekan data, apabila ada maka akan ditampilkan */
  	if ($row)
    {
      /* memanggil function dari masing2 model yang akan digunakan */
      $this->data['acara']             = $this->Acara_model->get_by_id($id);
      $this->data['get_komentar']      = $this->Acara_model->get_komentar($id);
      $this->data['get_all_random']    = $this->Acara_model->get_all_random();
      $this->data['post_new_data']     = $this->Acara_model->get_all();
      $this->data['post_new_data1']    = $this->Acara_model->get_all_acara_sidebar_upcoming();
      $this->data['post_new_data2']    = $this->Acara_model->get_all_acara_sidebar_trending();
      $this->data['get_tiket']         = $this->Acara_model->get_tiket($id);
      $this->data['get_top_user']	   = $this->Acara_model->get_top_user();
      $this->data['menu']              = $this->Menu_model->get_all();
      
      // Mengambil tabel hits artikel
      $tgl = date('Y-m-d');
      $this->db->select('*');
      $this->db->where('judul_seo', $id);
      $this->db->where('tanggal', $tgl);
      $query = $this->db->get('hits_acara');
      $row = $query->num_rows();

      // Apabila data pada hits tidak ada, maka insert datanya. Jika sudah ada, tinggal diupdate
      if($row > 0) {
        $this->Acara_model->get_hit($id);
      } else {
        $this->Acara_model->get_hit1($id);
      }
      /* memanggil view yang telah disiapkan dan passing data dari model ke view*/
			$this->load->view('front/acara/body', $this->data);
		}
		else
    	{
  		$this->session->set_flashdata('message', '<div class="alert alert-dismissible alert-danger">
          <button type="button" class="close" data-dismiss="alert">&times;</button>acara tidak ditemukan</b></div>');
        	redirect(base_url());
   	 	}
    }

    public function transaksi_acara($id) //Ini ngambil tabel tiket (Kesulitannya mengambil parameter transaksi)
	  {

    $this->data['title'] = "Event Unair";

    /* mengambil data berdasarkan id */
    $row = $this->Acara_model->get_tiket_by_id($id);

    /* melakukan pengecekan data, apabila ada maka akan ditampilkan */
    if ($row)
    {
      /* memanggil function dari masing2 model yang akan digunakan */
        $this->data['acara'] = $this->Acara_model->get_tiket_by_id($id);
        $this->data['transaksi'] = $this->Transaksi_acara_model->get_by_id($id);
        $this->data['post_new_data']     = $this->Acara_model->get_all();
        $this->data['post_new_data1']    = $this->Acara_model->get_all_acara_sidebar_upcoming();
        $this->data['post_new_data2']    = $this->Acara_model->get_all_acara_sidebar_trending();
        $this->data['get_top_user'] = $this->Acara_model->get_top_user();        
        $this->data['get_tiket_acara']   = $this->Acara_model->get_tiket_acara($id);
        $this->data['menu']                     = $this->Menu_model->get_all();

        $this->data['id_tiket'] = array(
          'name'  => 'id_tiket',
          'id'    => 'id_tiket',
          'type'  => 'hidden',
          'class'  => 'form-control',
        );

        $this->data['harga_tiket'] = array(
          'name'  => 'harga_tiket',
          'id'    => 'harga_tiket',
          'type'  => 'hidden',
          'class'  => 'form-control',
        );

         $this->data['total_pembayaran'] = array(
          'name'  => 'total_pembayaran',
          'id'    => 'total_pembayaran',
          'type'  => 'text',
          'class'  => 'form-control angka',
          'readonly' => 'readonly',
        );

        $this->data['jumlah_pembelian_tiket'] = array(
          'name'  => 'jumlah_pembelian_tiket',
          'id'    => 'jumlah_pembelian_tiket',
          'type'  => 'text',
          'class'  => 'form-control angka',
          'placeholder' => 'Isikan dengan angka',
          'onkeypress' => 'return isNumberKey(event)',
          'value' => $this->form_validation->set_value('jumlah_pembelian_tiket'),
        );

      $get_tiket_acara = $this->Acara_model->get_tiket_acara($id);
      $sisatiket = $row->quota - $get_tiket_acara->jumlah_pembelian_tiket;
      $now = date('Y-m-d');
      // Apabila user belum login, maka akan diarahkan ke halaman read
	    if(empty($this->session->userdata['email'])){
	       $this->session->set_flashdata('message', 'You must login first');
	  	   redirect(base_url('acara/read/'.$row->judul_seo));
	    }
        // Apabila tiket sudah expired, maka akan diarahkan ke halaman read
        elseif ($row->batas_penjualan < $now ) {
        	$this->session->set_flashdata('message', 'Ticket is Expired');
	      	redirect(base_url('acara/read/'.$row->judul_seo));
        }
        // Apabila tiket sudah habis, maka akan diarahkan ke halaman read
        elseif ($sisatiket <= 0) {
        $this->session->set_flashdata('message', 'Tickets have been exhausted');
        redirect(base_url('acara/read/'.$row->judul_seo));
        }
        else {
        	/* memanggil view yang telah disiapkan dan passing data dari model ke view*/
		    $this->load->view('front/acara/transaksi_acara', $this->data);
        }
    }
    else
    {
      $this->session->set_flashdata('message', 'Ticket not found');
      redirect(base_url());
    }
  }


  public function transfer_info($id)
    {
    if(empty($this->session->userdata['email'])){
    redirect(base_url());
    }
    else {
    $this->_rules();
    if ($this->form_validation->run() == FALSE)
    {
      $this->session->set_flashdata('message', 'Jumlah tiket must be filled in');
      redirect(base_url('acara/transaksi_acara/'.$id));
    }
    else {
      $row = $this->Acara_model->get_tiket_by_id($id);
    if ($row)
    {
	    $this->data['acara'] = $this->Acara_model->get_tiket_by_id($id);
	      /* memanggil function dari masing2 model yang akan digunakan */
	    $this->data['post_new_data']     = $this->Acara_model->get_all();
	    $this->data['post_new_data1']    = $this->Acara_model->get_all_acara_sidebar_upcoming();
	    $this->data['post_new_data2']    = $this->Acara_model->get_all_acara_sidebar_trending();
      $this->data['get_top_user']      = $this->Acara_model->get_top_user();
      $this->data['menu']                     = $this->Menu_model->get_all();

      $get_tiket_acara = $this->Acara_model->get_tiket_acara($id);
      $sisatiket = $row->quota - $get_tiket_acara->jumlah_pembelian_tiket;

      if ($this->input->post('jumlah_pembelian_tiket') > $sisatiket) {
        $this->session->set_flashdata('message', 'Oops ! Jumlah tiket yang kamu pesan melebihi kuota yang tersisa, Mohon lakukan pemesanan ulang');
        redirect(base_url('acara/transaksi_acara/'.$id));
      }

	    $jumlah_pembelian_tiket = $this->input->post('jumlah_pembelian_tiket');
	    // $total_pembayaran = $jumlah_pembelian_tiket x
      $total = $jumlah_pembelian_tiket * $row->harga_tiket;
	    $data = array(
	          'id_tiket'  => $this->input->post('id_tiket'),
	          'id'      => $this->session->userdata('id'),
	          'status_transaksi_acara' => 'Menunggu Pembayaran',
	          'jumlah_pembelian_tiket'        => $this->input->post('jumlah_pembelian_tiket'),
	          'total_pembayaran'        => $this->input->post('total_pembayaran'),
            'total_harga_tiket_asli'        => $total
	        );
          /* proses insert ke database melalui function yang ada pada model */
       // var_dump($data);
       $this->Acara_model->insert_transaksi_acara($data);
       $this->data['acara_max'] = $this->Acara_model->get_transaksi();
       $aa =  $this->Acara_model->get_transaksi();
        // Pengiriman invoice melalui email
        // Deklarasi atau Menampung Pesan kedalam variabel $message                   
        $message = "
        <p>Your number invoice is <strong>#".$aa->id_transaksi_acara."</strong></p>
        <p>Event Name       : <strong>".$aa->judul_acara."</strong></p>
        <p>Type Ticket      : <strong>#".$aa->jenis_tiket."</strong></p>
        <p>Purchase Amount  : <strong>".$aa->jumlah_pembelian_tiket."</strong></p>
        <p>Transfer Amount  : <strong>".rupiah($aa->total_pembayaran)."</strong></p>
        <p>Please make payment immediately through the available account number. </p>
        <hr>
        <h3>Mandiri</h3>
        <p>No. Rekening    : <strong>1420-0135-00318 (A/n Arya Rifqi Pratama)</strong></p>
        <hr>
        <p>If you have transferred, please confirm the payment. <a class='btn btn-success btn-xs' href='" . base_url() . "acara/konfirmasi/$aa->id_transaksi_acara'> Here </a> or through your dashboard page.</p>
        ";                       
        // Set up email 
        $this->load->library('email');
        $config['protocol'] = 'smtp';
        $config['smtp_host'] = 'ssl://smtp.gmail.com';
        $config['smtp_port'] = '465';
        $config['smtp_user'] = 'info1@rektor.unair.ac.id';  //change it
        $config['smtp_pass'] = 'tanyapakrektor1'; //change it
        $config['charset'] = 'utf-8';
        $config['newline'] = "\r\n";
        $config['mailtype'] = 'html';
        $config['wordwrap'] = TRUE;
        $this->email->initialize($config);
        $this->email->set_newline("\r\n");
        $this->email->from('adm@pih.unair.ac.id', 'EVENT UNAIR');
        $this->email->to($this->session->userdata('email'));
        $this->email->subject('Invoice Event Unair');             
        $this->email->message($message);   
        $this->email->send();
       } else {
       redirect(base_url());
        }
	     $this->load->view('front/acara/transfer_info', $this->data);
      }
    }
  }


    public function konfirmasi($id) // Mengambil para meter $id
	{
		/* menyiapkan data yang akan disertakan/ ditampilkan pada view */
    $this->data['title'] = "Event Unair";

    /* mengambil data berdasarkan id */
    $row = $this->Acara_model->get_transaksi_max($id);

    /* melakukan pengecekan data, apabila ada maka akan ditampilkan */
    if ($row)
    {
    $this->data['action']         = site_url('acara/update_action');
         /* memanggil function dari masing2 model yang akan digunakan */
    $this->data['acara'] = $this->Acara_model->get_transaksi_max($id);
    $this->data['post_new_data']     = $this->Acara_model->get_all();
    $this->data['post_new_data1']    = $this->Acara_model->get_all_acara_sidebar_upcoming();
    $this->data['post_new_data2']    = $this->Acara_model->get_all_acara_sidebar_trending();
    $this->data['menu']                     = $this->Menu_model->get_all();

	$this->data['id_transaksi_acara'] = array(
      'name'  => 'id_transaksi_acara',
      'id'    => 'id_transaksi_acara',
      'type'  => 'hidden',
    );

    $this->data['id_tiket'] = array(
      'name'  => 'id_tiket',
      'id'    => 'id_tiket',
      'type'  => 'hidden',
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
      'class' => 'form-control',
      'onkeypress' => 'return isNumberKey(event)',
    );

    $this->data['rekening_tujuan_css'] = array(
      'name'  => 'rekening_tujuan',
      'id'    => 'rekening_tujuan',
      'class' => 'form-control',
    );

    $this->data['rekening_tujuan'] = array(
      'Mandiri'    => 'Mandiri a/n Arya Rifqi Pratama',
      'BCA' => 'BCA a/n Arya Rifqi Pratama',
      'BNI' => 'BNI a/n Arya Rifqi Pratama',
      'BRI' => 'BRI a/n Arya Rifqi Pratama',
    );

    $this->data['tanggal_transfer'] = array(
      'name'  => 'tanggal_transfer',
      'id'    => 'tanggal_transfer',
      'type'  => 'text',
      'class' => 'form-control tanggal',
    );

    $this->data['bukti_transfer'] = array(
      'name'  => 'bukti_transfer',
      'id'    => 'bukti_transfer',
      'type'  => 'text',
      'class' => 'form-control',
    );

      /* memanggil view yang telah disiapkan dan passing data dari model ke view*/
      $this->load->view('front/acara/konfirmasi', $this->data);
    }
    else
    {
	  $this->session->set_flashdata('message', 'Data tidak ditemukan');
        redirect(site_url());
    }
 }

  public function komen($id)
  {

    $row = $this->Acara_model->get_by_id($id);
    /* melakukan pengecekan data, apabila ada maka akan ditampilkan */
    if ($row)
    {
    // validasi form
    $this->form_validation->set_rules('isi_komentar', ' ', 'trim|required');
    
    $this->form_validation->set_error_delimiters('<div class="text-danger">', '</div>');

    $recaptcha = $this->input->post('g-recaptcha-response');
    $response = $this->recaptcha->verifyResponse($recaptcha);

    if ($this->form_validation->run() == FALSE || !isset($response['success']) || $response['success'] <> true) {
        $this->session->set_flashdata('message', 'Mohon sesuaikan captcha terlebih dahulu');
        redirect(base_url('acara/'.$row->judul_seo));
    } 
        else {
        $now = date('Y-m-d H:m:s');
        $data = array(
          'id_acara'      => $this->input->post('id_acara'),
          'isi_komentar'  => $this->input->post('isi_komentar'),
          'nama'          => $this->session->userdata('username'),
          'status'        => 'ya',
          'verifikator'   => 'arya020595',
          'time_verif'    => $now
        );
        /* proses insert ke database melalui function yang ada pada model */
        $this->Acara_model->insert_komentar($data);
        // $this->session->set_flashdata('message', 'Komentar berhasil terkirim dan akan diverifikasi Admin terlebih dahulu');
        // lakukan proses validasi login disini
        // pada contoh ini saya anggap login berhasil dan saya hanya menampilkan pesan berhasil
        // tapi ini jangan di contoh ya menulis echo di controller hahahaha
        redirect(base_url('acara/'.$row->judul_seo));
        }
     }

  }

	public function cari_acara()
  {
    /* menyiapkan data yang akan disertakan/ ditampilkan pada view */
    $this->data['title']       = 'Event Unair - Hasil Pencarian';
    $this->data['description']       = 'Temukan event sesuai keinginganmu';
    $this->data['image']       = base_url('assets/images/upload/logo_event_unair.png');
    $this->data['site_name']       = 'Event Unair';
    $this->data['url']       = base_url();
    $this->data['type']       = 'article';
    $this->data['admin']       = '195716694382596';


    // Deklarasi $cari_acara sesuai dengan apa yang di input oleh user. Kalau tidak di deklarasikan malah error
    $cari_acara=$this->input->post('cari_acara');
    // Nilai dari $title dimasukan kedalam model untuk diolah dan di deklarasikan kedalam variabel $data
    $this->data['hasil_pencarian']    =$this->Acara_model->get_cari_acara($cari_acara);
    // Siderbar
    $this->data['post_new_data1']     = $this->Acara_model->get_all_acara_sidebar_upcoming();
    $this->data['post_new_data2']     = $this->Acara_model->get_all_acara_sidebar_trending();
    $this->data['get_top_user']       = $this->Acara_model->get_top_user();
    // Menu
    $this->data['menu']               = $this->Menu_model->get_all();

    /* memanggil view yang telah disiapkan dan passing data dari model ke view*/
    $this->load->view('front/home/hasil_pencarian', $this->data);
  }

  // Ini untuk menampilkan sugesti pencarian
  public function get_autocomplete(){
    // kalimat term ini darimana ?
    if (isset($_GET['term'])) {
        $result = $this->Acara_model->get_cari_acara($_GET['term']);
        if (count($result) > 0) {
        foreach ($result as $row)
          $arr_result[] = array(
          'label' => $row->judul_acara,
        );
          echo json_encode($arr_result);
        }
    }
  }

  public function kategori($id)
  {
    /* mengambil data berdasarkan id */
    $row = $this->Kategori_model->get_by_id_front_row($id);
    /* melakukan pengecekan data, apabila ada maka akan ditampilkan */
    if ($row)
    {
    /* mengambil uri segment ke-3 dan mengubah huruf awal menjadi kapital/ cetak */
    $kat = ucfirst($this->uri->segment(3));
    /* menyiapkan data yang akan disertakan/ ditampilkan pada view */
    $this->data['title']       = 'Event Unair - '. $row->judul_kategori;
    $this->data['description']       = $row->deskripsi_kategori;
    $this->data['image']       = base_url('assets/images/kategori/'.$row->nama_gambar_kategori.$row->type_gambar_kategori);
    $this->data['site_name']       = 'Event Unair';
    $this->data['url']       = base_url('acara/kategori/'.$row->kategori_seo);
    $this->data['type']       = 'article';
    $this->data['admin']       = '195716694382596';

    /* memanggil library pagination (membuat halaman) */
    $this->load->library('pagination');
    $jumlah = $this->Kategori_model->total_rows_kategori($id);

    // Mengatur base_url
    $config['base_url'] = base_url('acara/kategori/'.$row->kategori_seo);
    //menghitung total baris
    $config['total_rows'] = $jumlah; 
    //mengatur total data yang tampil per halamannya
    $config['per_page'] = 9;     
    // tag pagination bootstrap
    $config['full_tag_open']    = "<ul class='pagination'>";
    $config['full_tag_close']   = "</ul>";
    $config['num_tag_open']     = "<li>";
    $config['num_tag_close']    = "</li>";
    $config['cur_tag_open']     = "<li class='disabled'><li class='active'><a href='#'>";
    $config['cur_tag_close']    = "<span class='sr-only'></span></a></li>";
    $config['next_link']        = "Selanjutnya";
    $config['next_tag_open']    = "<li>";
    $config['next_tagl_close']  = "</li>";
    $config['prev_link']        = "Sebelumnya";
    $config['prev_tag_open']    = "<li>";
    $config['prev_tagl_close']  = "</li>";
    $config['first_link']       = "Awal";
    $config['first_tag_open']   = "<li>";
    $config['first_tagl_close'] = "</li>";
    $config['last_link']        = 'Terakhir';
    $config['last_tag_open']    = "<li>";
    $config['last_tagl_close']  = "</li>";
    
    // mengambil uri segment ke-4
    $dari = $this->uri->segment('4');

    /* eksekusi library pagination ke model penampilan data */
    $this->data['kategori'] = $this->Kategori_model->get_all_arsip($config['per_page'],$dari, $id);
    $this->pagination->initialize($config);

    /* memanggil function dari model yang akan digunakan */
    $this->data['post_new_data1']    = $this->Acara_model->get_all_acara_sidebar_upcoming();
    $this->data['post_new_data2']    = $this->Acara_model->get_all_acara_sidebar_trending();
    $this->data['get_top_user']      = $this->Acara_model->get_top_user();
    $this->data['menu']                     = $this->Menu_model->get_all();

    /* memanggil view yang telah disiapkan dan passing data dari model ke view*/
    $this->load->view('front/kategori/body', $this->data);
    } 
    else 
    {
      echo "gagal";
    }
  }

   public function list_acara()
  {
    /* menyiapkan data yang akan disertakan/ ditampilkan pada view */
    $this->data['page'] = "List acara";
    $this->data['title']       = 'Event Unair - Wadahnya Event Mahasiswa';
    $this->data['description']       = 'Event Unair merupakan tempat berkumpulnya event-event mahasiswa di seluruh indonesia';
    $this->data['image']       = base_url('assets/images/upload/logo_event_unair.png');
    $this->data['site_name']       = 'Event Unair';
    $this->data['url']       = base_url('acara/list_acara/halaman/');
    $this->data['type']       = 'article';
    $this->data['admin']       = '195716694382596';
    $this->data['menu']                     = $this->Menu_model->get_all();
    
    /* memanggil library pagination (membuat halaman) */
    $this->load->library('pagination');

    /* menghitung jumlah total data */
    $jumlah = $this->Acara_model->total_rows_front();

    // Mengatur base_url
    $config['base_url'] = base_url().'acara/list_acara/halaman/';    
    //menghitung total baris
    $config['total_rows'] = $jumlah; 
    //mengatur total data yang tampil per halamannya
    $config['per_page'] = 20;     
    // tag pagination bootstrap
    $config['full_tag_open']    = "<ul class='pagination'>";
    $config['full_tag_close']   = "</ul>";
    $config['num_tag_open']     = "<li>";
    $config['num_tag_close']    = "</li>";
    $config['cur_tag_open']     = "<li class='disabled'><li class='active'><a href='#'>";
    $config['cur_tag_close']    = "<span class='sr-only'></span></a></li>";
    $config['next_link']        = "Selanjutnya";
    $config['next_tag_open']    = "<li>";
    $config['next_tagl_close']  = "</li>";
    $config['prev_link']        = "Sebelumnya";
    $config['prev_tag_open']    = "<li>";
    $config['prev_tagl_close']  = "</li>";
    $config['first_link']       = "Awal";
    $config['first_tag_open']   = "<li>";
    $config['first_tagl_close'] = "</li>";
    $config['last_link']        = 'Terakhir';
    $config['last_tag_open']    = "<li>";
    $config['last_tagl_close']  = "</li>";
    
    // mengambil uri segment ke-4
    $dari = $this->uri->segment('4');

    /* eksekusi library pagination ke model penampilan data */
    $this->data['list_acara'] = $this->Acara_model->get_all_arsip($config['per_page'],$dari);
    $this->pagination->initialize($config);

    /* memanggil view yang telah disiapkan dan passing data dari model ke view*/
    $this->load->view('front/acara/list_acara', $this->data);
  }

// Mencoba Setting Ajax
  public function list_event() {
  
  if(!empty($this->input->get("page"))){

    $start = ceil($this->input->get("page") * $this->perPage);
    // Deklarasi GET Database
    $query = $this->db->limit($this->perPage, $start)->get("acara");
    $data['acara'] = $query->result();

    $result = $this->load->view('home/data', $data);

    echo json_encode($result);
  }else{

    $query = $this->db->limit($this->perPage, 0)->get("posts");

    $data['posts'] = $query->result();

    $this->load->view('template/header');
    $this->load->view('home/index', $data);
    $this->load->view('template/footer');

    }

  }

  public function _rules() 
  {
    $this->form_validation->set_rules('jumlah_pembelian_tiket', 'Jumlah pembelian tiket', 'trim|required');
    // set pesan form validasi error
    $this->form_validation->set_message('required', '{field} wajib diisi');

    $this->form_validation->set_error_delimiters('<div class="alert alert-danger alert">', '</div>');
  }

}

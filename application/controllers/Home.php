<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

 function __construct()
  {
    parent::__construct();
    $this->load->model('Acara_model');
    $this->load->model('Kategori_model');
    $this->load->model('Featured_model');
    $this->load->model('Menu_model');
    $this->load->model('Transaksi_acara_model');
  }

	public function index()
	{
    // Hapus data otomatis berdasarkan tiket expired
     $this->Transaksi_acara_model->delete_expired();

    /* menyiapkan data yang akan disertakan/ ditampilkan pada view */
    $this->data['title']       = 'Event Unair - Wadahnya Event Mahasiswa';
    $this->data['description']       = 'Event Unair merupakan tempat berkumpulnya event-event mahasiswa di seluruh indonesia';
    $this->data['image']       = base_url('assets/images/upload/logo_event_unair.png');
    $this->data['site_name']       = 'Event Unair';
    $this->data['url']       = base_url();
    $this->data['type']       = 'article';
    $this->data['admin']       = '195716694382596';

		/* memanggil function dari masing2 model yang akan digunakan */
    $this->data['total_acara']              = $this->Acara_model->total_rows_users();
    $this->data['post_new_data']            = $this->Acara_model->get_all_acara_kategori();
    $this->data['get_top_user']             = $this->Acara_model->get_top_user();
		$this->data['get_all_komentar_sidebar'] = $this->Acara_model->get_all_komentar_sidebar();
		$this->data['get_all_acara_sidebar']  	= $this->Acara_model->get_all_Acara_sidebar();
		$this->data['get_combo_kat'] 		     	  = $this->Kategori_model->get_combo_kat();
    $this->data['get_kategori']             = $this->Kategori_model->get_all(); 
		$this->data['post_featured_data'] 		  = $this->Featured_model->get_all();
    $this->data['post_new_data1']           = $this->Acara_model->get_all_Acara_sidebar_upcoming();
    $this->data['post_new_data2']           = $this->Acara_model->get_all_Acara_sidebar_trending();
    $this->data['menu']                     = $this->Menu_model->get_all();
    
		$this->data['kategori'] = array(
      	'name'  => 'kategori',
      	'id'    => 'kategori',
      	'type'  => 'text',
      	'class' => 'form-control',
      	);

      	$this->data['sortir_css'] = array(
      	'name'  => 'sortir',
      	'id'    => 'sortir',
      	'type'  => 'text',
      	'class' => 'form-control',
      	);

      	$this->data['sortir'] = array(
      	'' => 'Sort By',
      	'Newest'  => 'Newest',
      	'Upcoming'    => 'Upcoming',
      	'Trending'  => 'Trending',
      	);

      	$this->data['carievent'] = array(
      	'name'  => 'carievent',
      	'id'    => 'carievent',
      	'type'  => 'text',
      	'class' => 'form-control',
      	'placeholder' => 'Event Keyword',
      	);

    
		/* memanggil view yang telah disiapkan dan passing data dari model ke view*/
		$this->load->view('front/home/body', $this->data);
	}

}
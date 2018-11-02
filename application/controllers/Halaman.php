<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Halaman extends CI_Controller {

	function __construct()
  {
    parent::__construct();
    /* memanggil model untuk ditampilkan pada masing2 modul */
    $this->load->model('Halaman_model');
    $this->load->model('Kategori_model');
    $this->load->model('Menu_model');

    $this->data['get_kategori']      = $this->Kategori_model->get_all();
  }

	public function read($id) // Mengambil parameter $id
	{
    /* mengambil data berdasarkan id */
	  $row = $this->Halaman_model->get_by_id($id);
    /* melakukan pengecekan data, apabila ada maka akan ditampilkan */
  	if ($row)
    {
      /* memanggil function dari masing2 model yang akan digunakan */
      $this->data['halaman']             = $this->Halaman_model->get_by_id($id);
      $this->data['menu']                = $this->Menu_model->get_all();
     
      /* memanggil view yang telah disiapkan dan passing data dari model ke view*/
			$this->load->view('front/halaman/body', $this->data);
		}
		else
    	{
  		$this->session->set_flashdata('message', '<div class="alert alert-dismissible alert-danger">
          <button type="button" class="close" data-dismiss="alert">&times;</button>Halaman tidak ditemukan</b></div>');
        	redirect(base_url());
   	 	}
    }

  public function _rules() 
  {
// Bentar, nanti diisi
  }

}

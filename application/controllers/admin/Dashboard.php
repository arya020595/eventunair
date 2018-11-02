<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {
	// Kesalahan tadi karena modelnya tidak dideklarasikan di awal atau di index
	// 	Severity: Notice
	// Message: Undefined property: Dashboard::$Transaksi_acara_model
	// Filename: admin/Dashboard.php
	// Line Number: 34
	// Backtrace:
	// File: C:\xampp\htdocs\unairevent\application\controllers\admin\Dashboard.php
	// Line: 34
	// Function: _error_handler
	// File: C:\xampp\htdocs\unairevent\index.php
	// Line: 315
	// Function: require_once

	public function index(){
	$this->load->model('Acara_model');
    $this->load->model('Kategori_model');
    $this->load->model('Komentar_model');
    $this->load->model('Featured_model');
    $this->load->model('User_model');
    $this->load->model('Transaksi_acara_model');

 //    // Update saldo otomatis berdasarkan perkalian antara jumlah pembelian tiket dengan harga asli tiket ketika acara telah berakhir
 //    $jumlah_pembelian_tiket = select Transaksi_acara_model where username = userdata where status = sukses 
 //    $untung = $row->harga_tiket - $get_tiket_acara->jumlah_pembelian_tiket;
 //    $untung = 

 //    $data = array(
 //  	'saldo' => 'saldo - ' . $pencairan,
	// );
	// // Pencairan adalah dana yang ditarik dari saldo virtual website 
 //    $this->Transaksi_acara_model->update_saldo($data);

    /* cek status login */
	if (empty($this->session->userdata['email'])){
		// apabila belum login maka diarahkan ke halaman login
		redirect('user', 'refresh');
	}
	elseif($this->session->userdata('usertype') == 'users'){
		// apabila belum login maka diarahkan ){
		$this->data = array(
    	'title' 							=> 'Dashboard',
        'button' 							=> 'Tambah',
	    'total_acara' 						=> $this->Acara_model->total_rows_users(),
	    'total_penjualan' 					=> $this->Transaksi_acara_model->total_rows_penjualan(),
	    'total_saldo' 				 		=> $this->User_model->total_saldo(),
	    'total_laba'					    => $this->Transaksi_acara_model->total_penjualan(),
		); 
		// Jangan lupa ditandai tanda titik koma (;) hehe :D
	}
	else {
		$this->data = array(
        'title' 							=> 'Dashboard',
        'button' 							=> 'Tambah',
	    'total_komen' 						=> $this->Komentar_model->get_total_row_kategori(),
	    'total_komen_pending'				=> $this->Komentar_model->get_total_row_kategori_pending(),
	    'total_featured' 					=> $this->Featured_model->total_rows(),
	    'total_kategori' 					=> $this->Kategori_model->total_rows(),
	    'total_acara' 						=> $this->Acara_model->total_rows(),
	    'total_user'						=> $this->User_model->total_rows(),
		);
	}
		$this->Transaksi_acara_model->delete_expired();
		$this->load->view('back/dashboard',$this->data);	
	}
	
}

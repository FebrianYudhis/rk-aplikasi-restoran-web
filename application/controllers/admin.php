<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {
    public function __construct(){
        parent::__construct();
        apakah_sudah_masuk();
        admin_only();
        $this->load->model('Pesan');
        $this->load->model('Akun');
    }

    public function index(){
        $data['judul'] = "Home";
        $sekarang = date('Y-m');
        $data['jumlahpendapatan'] = $this->db->query("SELECT sum(total) FROM `order` WHERE tanggal_pemesanan BETWEEN '{$sekarang}-1' AND '{$sekarang}-31' AND status='Sudah dibayar'")->row_array();
        $data['jumlahpemesanan'] = $this->db->query("SELECT * FROM `order` WHERE status='Sudah dibayar'")->num_rows();
        $data['jumlahmenu'] = $this->db->count_all('menu');
        $data['semuaorder'] = $this->db->get_where('order',['status'=>"Sudah dibayar"]);

        $this->form_validation->set_rules('dari','dari','required');
        $this->form_validation->set_rules('sampai','sampai','required');
        if($this->form_validation->run() == false){
            $this->load->view('template/admin/header',$data);
            $this->load->view('admin/index',$data);
            $this->load->view('template/admin/footer');
        }else{
            $a = $this->input->post('dari');
            $b = $this->input->post('sampai');
            redirect('Admin/cek/'.$a.'/'.$b);
        }
    }

    public function cek($a,$b){
        $data['semuaorder'] = $this->db->query("SELECT * FROM `ORDER` WHERE status='Sudah dibayar' AND tanggal_pemesanan BETWEEN '$a' and '$b'");
        $data['judul'] = "Home";
        $sekarang = date('Y-m');
        $data['jumlahpendapatan'] = $this->db->query("SELECT sum(total) FROM `order` WHERE tanggal_pemesanan BETWEEN '{$sekarang}-1' AND '{$sekarang}-31' AND status='Sudah dibayar'")->row_array();
        $data['jumlahpemesanan'] = $this->db->query("SELECT * FROM `order` WHERE status='Sudah dibayar'")->num_rows();
        $data['jumlahmenu'] = $this->db->count_all('menu');
        $this->load->view('template/admin/header',$data);
        $this->load->view('admin/index',$data);
        $this->load->view('template/admin/footer');
    }

    public function list(){
        $data['judul'] = "List akun";
        $data['akun'] = $this->db->get('akun');
        $this->load->view('template/Admin/header',$data);
        $this->load->view('Admin/list',$data);
        $this->load->view('template/Admin/footer');
    }

    public function invoice($a){
        $data['judul'] = "Invoice";
        $data['lihat'] = $this->db->get_where('order',['no_invoice'=>$a]);
        $cek = $data['lihat']->row_array();
        $status = $cek['status'];
        if($status == "Sudah dibayar"){
            $this->load->view('template/admin/header',$data);
            $this->load->view('admin/invoice',$data);
            $this->load->view('template/admin/footer');
        }else{
            $this->session->set_flashdata('pesan','<div class="alert alert-danger col-lg-12">Akses ditolak</div>');
            redirect('Admin');
        }
    }

    public function hapus($a){
        $a = $this->db->delete('akun',['username'=>$a]);
        if($a){
            $this->session->set_flashdata('pesan','<div class="alert alert-success">Akun berhasil dihapus</div>');
            redirect('Admin/list');
        }else{
            $this->session->set_flashdata('pesan','<div class="alert alert-danger">Akun gagal dihapus,terikat dengan aplikasi</div>');
            redirect('Admin/list');
        }
    }

    public function keluar(){
        $this->Akun->keluar();
    }
}
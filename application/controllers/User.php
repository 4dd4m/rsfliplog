<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class User extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Usermodel');
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="alert alert-danger alert-dismissible" role="alert">
							  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
							  <span aria-hidden="true">&times;</span></button>
							  <strong>Warning!</strong>   ', '</div>');
        $this->sessiondata = $this->session->userdata('logged_in');
    }

    public function index() {
        if ($this->input->post('signin') !== null)
            $this->login();
    }

    protected function login() {
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $this->form_validation->set_rules('username', 'Username', 'trim|required|callback_isValidUser');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');
        if ($this->form_validation->run() == true) {
            if ($this->Usermodel->isValidPassword($username, $password) == true) {
                redirect($_SERVER['HTTP_REFERER']);
            } else {
                $this->session->set_flashdata('wrongpass', '<div class="alert alert-danger alert-dismissible" role="alert">
							  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
							  <span aria-hidden="true">&times;</span></button>
							  <strong>Wrong Password</strong></div>');
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $this->session->set_flashdata('missing', '<div class="alert alert-danger alert-dismissible" role="alert">
							  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
							  <span aria-hidden="true">&times;</span></button>
							  <strong>All required!</strong></div>');
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function register() {
        $this->load->view('header');
        $this->load->view('register');
        $this->load->view('footer');
    }

    public function logout() {
        $this->session->unset_userdata('logged_in');
        redirect('/');
    }

    public function isValidUser() {
        if ($this->Usermodel->isValidUser($this->input->post('username')) == true) {
            return true;
        } else {
            $this->form_validation->set_message('isValidUser', 'This user doesn\'t exists');
            echo 'not valid user';
        }
    }

    public function isValidPassword($username, $password) {
        if ($this->Usermodel->isvalidPassword == true) {
            return true;
        } else {
            echo 'not valid password';
        }
    }

}
?>


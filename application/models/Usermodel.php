<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Usermodel extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function isValidUser($username) {
        $username = trim($username);
        $query = $this->db->select('username')
                ->where('username', $username)
                ->from('users');
        $result = $query->count_all_results();
        if ($result == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function isValidPassword($username, $password) {
        $username = trim($username);
        $password = md5($password);

        $query = $this->db->select('*')
                ->where('username', $username)
                ->where('password', $password)
                ->get('users');
        $result = $query->result_array();
        //if found result
        if (count($result) == 1) {
            //initialize the sess_array
            $sess_array = array();
            foreach ($result as $data) {
                $sess_array['userid'] = $data['id'];
                $sess_array['username'] = $data['username'];
                $sess_array['registered'] = $data['registered'];
                $sess_array['logintime'] = time();
            }
            if (!isset($sess_array['bank'])) {
                $sess_array['bank'] = $this->Logmodel->updateBank($sess_array['userid']);
            }
            //grab user avatar and save

            $mypath = $_SERVER["DOCUMENT_ROOT"] . "/images/chat/" . $username . ".gif"; #path to chatheads

            if (!file_exists("images/chat/" . $username . ".gif")) {


                $url = 'http://services.runescape.com/m=avatar-rs/' . $username . '/chat.gif';
                $result = file_put_contents($mypath, file_get_contents($url));
                if ($result) {
                    $sess_array['avatar'] = true;
                } else {
                    $sess_array['avatar'] = false;
                }
            } else {
                $sess_array['avatar'] = true;
            }

            $this->session->set_userdata('logged_in', $sess_array);

            //and give it back
            return true;
        } else {
            //not valid password
            return false;
        }
    }

}

?>
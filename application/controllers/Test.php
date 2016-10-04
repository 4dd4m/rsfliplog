<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Test extends CI_Controller {

    public function index() {
        $this->Logmodel->copyLimitstoApidb();
    }

    public function showDocumentRoot() {
        echo $_SERVER["DOCUMENT_ROOT"];
    }

    public function isChatheadExists($pic = 'adam.gif') {
        if (!file_exists("images/chat/" . $pic)) {
            echo "Chathead doesnt exists";
        } else {
            echo "Chathead exists";
        }
    }

}

?>
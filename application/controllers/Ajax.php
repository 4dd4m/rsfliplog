<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Ajax extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model("Logmodel");
        $this->sessiondata = $this->session->userdata('logged_in');
    }

    public function searchitem() {
        // search in item in the trade view buy section
        // gives back the whole itemlist to autocomplete
        $query = $this->Logmodel->autocomplete($this->input->get("term"));
        echo json_encode($query);
    }

    public function itemlimit() {
        // search the item's limit in the trade view buy section (on focus out)
        $item = $this->input->get("term");
        (int) $limit = $this->Logmodel->getItemLimitFromName($item);
        if ($limit > 0) {
            echo $limit;
        } else {
            echo 'Item Limit';
        }
    }

    public function RemainingLimit() {
        // search the item's limit in the trade view buy section (on focus out)
        $itemid = (int) $this->Logmodel->itemExists($this->input->get('term'));
	$remaining = $this->Logmodel->limitRemaining($itemid, $this->sessiondata['userid']);
        echo $remaining;
    }

    public function countItemInBank($term) {
        if (!isset($term) || $term == "null" || !$this->sessiondata['userid']) {
            return false;
        }
        $bank = $this->Logmodel->updateBank($this->sessiondata['userid']);
        $inBank = (int) $bank[$term]['data']['availablequantity'];
        if ($inBank > 0) {
            echo $inBank;
        } else {
            echo 'You have';
        }
    }

}

?>
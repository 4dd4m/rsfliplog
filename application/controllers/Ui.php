<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Ui extends CI_Controller {

    public function __construct() {
	parent::__construct();
	$this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
	('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
	$this->output->set_header('Pragma: no-cache');
	$this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	$this->sessiondata = $this->session->userdata('logged_in');
	$this->form_validation->set_error_delimiters('<div class="alert alert-danger alert-dismissible" role="alert">
							  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
							  <span aria-hidden="true">&times;</span></button>
							  <strong>Warning!</strong>   ', '</div>');
    }

    public function index() {
	$this->load->view('header');
	$this->load->view('news');
	$this->load->view('footer');
    }

    public function bank() {
	$data['bank'] = $this->Logmodel->updateBank($this->sessiondata['userid']);
	#$data ['bank'] = $this->Logmodel->generate_bank($this->sessiondata['userid']);
	$this->load->view('header');
	$this->load->view('bank', $data);
	$this->load->view('footer');
    }

    public function corenews() {
	$this->load->view('header');
	$this->load->view('corenews');
	$this->load->view('footer');
    }

    public function history() {
	$data['history'] = $this->Logmodel->generateHistory($this->sessiondata['userid']);
//	echo "<pre>";
//	var_dump($data['history']);
//	echo "</pre>";
	$this->load->view('header');
	$this->load->view('history', $data);
	$this->load->view('footer');
    }

    public function trade() {
	// validations if post done
	$data['bank'] = $this->Logmodel->updateBank($this->sessiondata['userid']);
	$data ['form_success'] = '';
	if ($this->input->post('buy') !== null) {
	    $this->load->library('form_validation');
	    $this->form_validation->set_rules("item", "Search", "required|callback_check_item_exists");
	    $this->form_validation->set_rules("quantity", "Quantity", "required|numeric|greater_than[0]");
	    $this->form_validation->set_rules("price", "Total price", "required|numeric|greater_than[0]|callback_is_it_over1gp");
	    if ($this->form_validation->run() == true) {
		$price = (int) $this->input->post('price');
		$quantity = (int) $this->input->post('quantity');
		$each = number_format($price / $quantity);
		$item = $this->input->post('item');
		$time = time();


		if ($this->Logmodel->recordbuy($item, $price, $quantity, $time, $this->sessiondata['userid']) == true) {
		    $this->session->set_flashdata('buy_success', '<div class="alert alert-success alert-dismissible" role="alert">
                                                              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                              <strong>Success!   </strong>' . number_format($this->input->post('quantity')) .
			    ' piece(s) of ' . $this->input->post('item') .
			    ' has been added to your bank which cost ' .
			    number_format($this->input->post('price')) . ' in total. (~ ' . $each .
			    ' gp each).</div>');
		    $data['reset'] = true;
		    redirect('ui/trade');
		} else {
		    $data['reset'] = false;
		}
	    } else {
		$data['reset'] = false;
	    }
	} else {
	    $data ['reset'] = false;
	}


	if ($this->input->post('sell') !== null) {
	    $this->load->library('form_validation');
	    $price = (int) $this->input->post('sellprice');
	    $quantity = (int) $this->input->post('quantitytosell');
	    if ($price !== 0 && $quantity !== 0) {
		$each = number_format($price / $quantity);
	    } else {
		$each = 0;
	    }
	    $item = $this->input->post('itemtosell');
	    #EZ A TIME ITT MINDIG ÁTMEGY,....
	    $time = time();
	    $this->form_validation->set_rules("itemtosell", "Item Dropdown", "required|callback_item_is_sellable");
	    $this->form_validation->set_rules("sellprice", "Total Price", "required|numeric|greater_than[0]");
	    $this->form_validation->set_rules("quantitytosell", "Quantity", "required|numeric|greater_than[0]");
	    if ($this->form_validation->run() == true) {
		if ($this->Logmodel->recordSell($item, $price, $quantity, $time, $this->sessiondata['userid']) == true) {
		    $this->session->set_flashdata('sell_success', '<div class="alert alert-success alert-dismissible" role="alert">
                                                              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                              <strong>Success! You sold   </strong>' . number_format($this->input->post('quantitytosell')) .
			    ' piece(s) of ' . $data['bank'][$item]['data']['name'] .
			    ' for ' .
			    number_format($this->input->post('sellprice')) . ' in total. (~ ' . $each .
			    ' gp each).</div>');
		    $data['reset'] = true;
		    $data['bank'] = $this->Logmodel->updateBank($this->sessiondata['userid']);

		    #redirect('ui/trade');
		} else {
		    //recordSell False
		    //create warning message
		    $error = '<div class="alert alert-warning" role="alert">
                        <strong>Warning! You can\'t sell </strong>' . number_format($this->input->post('quantitytosell')) .
			    ' piece(s) of ' . $data['bank'][$this->input->post('itemtosell')]['data']['name'] . '. You have only ' . number_format($data['bank'][$this->input->post('itemtosell')]['data']['availablequantity']) . ' =/</div>';
		    $this->session->set_flashdata('sell_success', $error);
		    $data['reset'] = false;
		}
	    } else {
		$data['reset'] = false;
	    }
	}

	$this->load->view("header");
	$this->load->view("tradeform", $data);
	$this->load->view("footer");
    }

    public function listbuys($itemid) {
	$data['bank'] = $this->Logmodel->updateBank($this->sessiondata['userid']);
	$data['itemid'] = $itemid;
	

	$this->load->view('header');
	$this->load->view('listbuys', $data);
	$this->load->view('footer');
    }

    public function test() {
	$this->Logmodel->cleartables();
//	$result =  $this->Logmodel->grabBuysFromBuyHistory($this->sessiondata['userid']);
//	echo "<pre>";
//	var_dump($result);
//	echo "</pre>";
    }

    public function bankdump() {
	$bank = $this->Logmodel->updateBank($this->sessiondata['userid']);
	echo "<pre>";
	var_dump($bank);
	echo "</pre>";
    }

    public function editBuy($itemId,$buyId) {
	$data['bank'] = $this->Logmodel->updateBank($this->sessiondata['userid']);
	
	if(!isset($data['bank'][$itemId]['buydata'][$buyId])){
	    echo 'Invalid data';
	    exit;
	}
	
	$data['itemId'] = $itemId;
	$data['buyId'] = $buyId;
	
	
	$data['buydata'] = $this->Logmodel->getBuyDataById($buyId, $this->sessiondata['userid']);
	$data['selldata'] = $this->Logmodel->getSellDataById($buyId, $this->sessiondata['userid']);
	$data['alreadySold'] = (int) $this->Logmodel->getSoldQuantityByBuyId($buyId, $this->sessiondata['userid']);
	#post button
	if ($this->input->post('saveButton') !== null) {
	    $buydata = $data['buydata'][0];
	    $quantity = (int) $this->input->post('quantity');
	    $price = (int) $this->input->post('price');
	    $date = (int) strtotime($this->input->post('date'));
	    $validquantity = (int) $data['alreadySold'] - 1;
	    $validprice = (int) $buydata['price'] - 1;
	    $this->load->library('form_validation');
	    $this->form_validation->set_rules("quantity", "Quantity", "required|greater_than[" . $validquantity . "]");
	    $this->form_validation->set_rules("price", "Cost", "required|callback_is_it_over1gp");
	    $this->form_validation->set_rules("date", "Date", "required|callback_date_is_higher");

	    if ($data['alreadySold'] == $this->input->post('quantity')) {
		$moveToHistory = TRUE;
	    } else {
		$moveToHistory = FALSE;
	    }


	    if ($this->form_validation->run() == true) {
		if ($moveToHistory == TRUE) {
		    #we have to move to the history after the update
		    //DELETE THE CURRENT BUY
		    #$update = $this->updateBank($userid);
		    $todb = [
			'price' => $price,
			'quantity' => $quantity,
			'date' => strtotime($this->input->post('date'))
		    ];

		    $this->db->where('id', $buyId)->where('userid',$this->sessiondata['userid'])->update('buy',$todb);
		    $this->Logmodel->moveToHistory($buyId,  $this->sessiondata['userid']);

		    $message = '<div class="alert alert-success alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <strong>Success!</strong>  ' . $buydata['itemname'] . ' This flip has been succesfully edited and placed into your history.</div>';
		    $this->session->set_flashdata('deleteMessage', $message);
		    redirect('ui/bank');
		} else {
		    $todb = [
			'price' => $price,
			'quantity' => $quantity,
			'date' => strtotime($this->input->post('date'))
		    ];
		    $this->db->where('id', $buyId)->where('userid', $this->sessiondata['userid'])->update('buy', $todb);
		    $data['updated'] = true;
		    ##another update
		    $data['buydata'] = $this->Logmodel->getBuyDataById($buyId, $this->sessiondata['userid']);
		    $data['selldata'] = $this->Logmodel->getSellDataById($buyId, $this->sessiondata['userid']);
		    $data['bank'] = $this->Logmodel->updateBank($this->sessiondata['userid']);
		    $data['alreadySold'] = (int) $this->Logmodel->getSoldQuantityByBuyId($buyId, $this->sessiondata['userid']);
		}
	    }
	}

	$this->load->view('header');
	$this->load->view('editbuy', $data);
	$this->load->view('footer');
    }

    public function editSell($itemid, $buyId, $uid) {
	#assemble the variables for the view
	$data = []; #initialize
	$bank = $this->Logmodel->updateBank($this->sessiondata['userid']); #get bank
	$data['bank'] = $bank[$itemid];
	$data['buydata'] = $bank[$itemid]['buydata'][$buyId];
	$data['itemid'] = (int) $itemid;
	$data['buyid'] = (int) $buyId;
	$data['uid'] = (int) $uid;
	$data['mindate'] = $bank[$itemid]['buydata'][$buyId]['date'];
	$data['itemname'] = $bank[$itemid]['data']['name'];
	$min = 1;
	
	#PARAMETER VALIDATION FROM USER
	
	if (
		!isset($itemid) or
		!isset($buyId) or 
		!isset($uid) or 
		!isset($bank[$itemid]) or 
		!isset($bank[$itemid]['buydata'][$buyId]) or
		!isset($bank[$itemid]['solddata'][$uid])
			
	    ) {
	    echo 'Invalid data';
	    exit;
	}
	
	$data['max'] = (int) 0; #this value limits the quantity in the input value
	foreach ($bank[$itemid]['solddata'] as $key => $value) {
	    if ($value['uid'] == $uid) {
		$data['max'] += (int) $value['quantity'] + $bank[$itemid]['buydata'][$buyId]['available'];
	    }
	}

	#after post
	if ($this->input->post('saveButton') !== null) { #if pushed the saveButton
	    $validMaxQuantity = (int) $data['max'] + 1; #this variable used in the form validation rules
	    $quantity = (int) $this->input->post('quantity');
	    $price = (int) $this->input->post('price');
	    $date = strtotime($this->input->post('date'));
	    #setting up the rules
	    $this->form_validation->set_rules("quantity", "Quantity", "required|greater_than[0]|less_than[" . $validMaxQuantity . "]");
	    $this->form_validation->set_rules("price", "Cost", "required|less_than[2147483648]|callback_is_it_over1gp");
	    $this->form_validation->set_rules("date", "Date", "required|callback_date_is_lower");


	    if ($this->form_validation->run() == true) {
		$postdate = strtotime($this->input->post('date'));
		
		if ($data['max'] == $this->input->post('quantity')) {
		    $moveToHistory = True;
		} else {
		    $this->Logmodel->updateSell($data['uid'], $price, $quantity, $postdate);
		    $data['updated'] = true;
		}
	    } else {
		$data['updated'] = false;
	    }

	    $bank = $this->Logmodel->updateBank($this->sessiondata['userid']); #get bank
	    $data['bank'] = $bank[$itemid];
	    $data['buydata'] = $bank[$itemid]['buydata'][$buyId];
	    $data['itemid'] = (int) $itemid;
	    $data['buyid'] = (int) $buyId;
	    $data['uid'] = (int) $uid;
	    $data['mindate'] = $bank[$itemid]['buydata'][$buyId]['date'];
	    $data['itemname'] = $bank[$itemid]['data']['name'];
	    $min = 1;
	    $data['max'] = (int) 0; #this value limits the quantity in the input value
	    foreach ($bank[$itemid]['solddata'] as $key => $value) {
		if ($value['uid'] == $uid) {
		    $data['max'] += (int) $value['quantity'] + $bank[$itemid]['buydata'][$buyId]['available'];
		}
	    }
	}else{
	    $data['updated'] = false;
	}
	#gomb után
	#csak 1 sell van
	##ha változtat, rögzít
	##ha maxot ír be akkor a historyba
	#van több sell de ekkor le kell limitálni a max értéket.
	##ha változtat akkor beír
	##ha változtat akkor a historyba

	$this->load->view('header');
	$this->load->view('editsell', $data);
	$this->load->view('footer');
    }
    
        public function editHistoryBuy($buyId) {
	$prehistory = $this->Logmodel->generateHistory($this->sessiondata['userid']);
	$data['history'][$buyId] = $prehistory[$buyId];
	$price = (int) $this->input->post('price');
	$quantity = (int) $this->input->post('quantity');
	$date = (int) strtotime($this->input->post('date'));
	
	$validQuantity = $data['history'][$buyId]['quantity'] - 1; #this because validation the quantity at the form
	
	if (count($data['history'][$buyId]['selldata']) > 1) {
	    $data['alreadySold'] = true;
	} else {
	    $data['alreadySold'] = false;
	}

	if ($this->input->post('saveButton') !== NULL) {
	    $this->load->library('form_validation');
	    $this->form_validation->set_rules("quantity", "Quantity", "required|greater_than[" . $validQuantity . "]");
	    $this->form_validation->set_rules("price", "Cost", "required|callback_is_it_over1gp");
	    $this->form_validation->set_rules("date", "Date", "required|callback_date_is_higher_history");
	    
	    if ($this->form_validation->run() == true) {
		
		if ($this->input->post('quantity') > $data['history'][$buyId]['quantity']) {
		    
		    #We have to move back to the bank
		    $message = '<div class="alert alert-success alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <strong>Success!</strong>  ' . $buydata['itemname'] . ' This flip has been succesfully edited and placed back to your bank.</div>';
		    $this->session->set_flashdata('deleteMessage', $message);
		    $this->Logmodel->moveTobank($buyId, $this->sessiondata['userid']);
		    $todb = [
			'price' => $price,
			'quantity' =>$quantity,
			'date' => $date
		    ];
		    
		    
		    $this->db->where('id',$buyId)->where('userid',  $this->sessiondata['userid'])->update('buy',$todb);
		    redirect('ui/bank');
		} else {
		    $message = '<div class="alert alert-success alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <strong>Success!</strong>  ' . $data['history'][$buyId]['quantity'] - $data['history'][$buyId]['quantity'] . 'of' . $data['history'][$buyId]['name'] . ' This flip has been succesfully edited and placed into your history.</div>';
		    $this->session->set_flashdata('deleteMessage', $message);
		    
		    $todb = [
			'buyprice' => $price,
			'quantity' =>$quantity,
			'date' => $date
		    ];
		    
		    
		    $this->db->where('userid',  $this->sessiondata['userid'])->where('buyid',$buyId)->update('historybuy',$todb);
		    $this->Logmodel->updateSellpriceInHistory($buyId,  $this->sessiondata['userid']);
		    
		    
		    
		    
		    redirect('ui/edithistorybuy/' . $buyId);
		}
	    }
	}



	$this->load->view('header');
	$this->load->view('edithistorybuy', $data);
	$this->load->view('footer');
    }

    public function editHistorySell($itemId, $buyId, $uId) {
	#no in use, just define to avoid error
	$prehistory = $this->Logmodel->generateHistory($this->sessiondata['userid']);

	#PARAMETER VALIDATION FROM USER
	if(
		!isset($itemId) or
		!isset($buyId) or 
		!isset($uId) or
		!isset($prehistory[$buyId]) or 
		!isset($prehistory[$buyId]['selldata'][$uId]) or 
		$prehistory[$buyId]['selldata'][$uId]['itemid'] !== $itemId
	    ){
	    echo 'You entered an invalid data.If it was unexpected, please report this as a bug.';
	    exit;
	}
	
	$data['updated'] = false;
	$data['itemId'] = (int) $itemId;
	$data['buyId'] = (int) $buyId;
	$data['uId'] = (int) $uId;
	$data['history'][$buyId] = $prehistory[$buyId];
	$data['itemName'] = $data['history'][$buyId]['name'];
	$validQuantity = (int) $data['history'][$buyId]['selldata'][$uId]['quantity'] + 1;
	$originalQuantity = (int) $data['history'][$buyId]['selldata'][$uId]['quantity'];
	
	if ($this->input->post('saveButton') !== NULL) {
	    
	    #VALIDATING: If changes made, work further, but it didnt, jst render the view.
	    if (
		    (int) $data['history'][$buyId]['selldata'][$uId]['price'] != (int) $this->input->post('price')  or 
		    (int) $data['history'][$buyId]['selldata'][$uId]['quantity'] != (int) $this->input->post('quantity') or  
		    (int) $data['history'][$buyId]['selldata'][$uId]['date'] != (int) strtotime($this->input->post('date'))
		) {
		
	    
	    
	    $this->form_validation->set_rules("quantity", "Quantity", "required|less_than[" . $validQuantity . "]");
	    $this->form_validation->set_rules("price", "Cost", "required|callback_is_it_over1gp");
	    $this->form_validation->set_rules("date", "Date", "required|callback_date_at_historysell");
	    
	    if ($this->form_validation->run() == true) {
		if ($originalQuantity > (int) $this->input->post('quantity')) {
		    echo 'Move to bank';
		    #
		    #
		    #Need a logic here to edit and place back to bank
		    #
		    #
		    #
		}else{
		    $todb = [
			'price' => (int) $this->input->post('price'),
			'quantity' => (int) $this->input->post('quantity'),
			'date' => (int) strtotime($this->input->post('date'))
		    ];
		    #update in the db and update the sellprice
		    $this->db->where('uid',$uId)->where('userid',  $this->sessiondata['userid'])->update('historysell',$todb);
		    $this->Logmodel->updateSellpriceInHistory($buyId,  $this->sessiondata['userid']);
		    
		    
		    #update again for view
		    $data['history'][$buyId] = $prehistory[$buyId];
		    $prehistory = $this->Logmodel->generateHistory($this->sessiondata['userid']);
		    $data['history'][$buyId] = $prehistory[$buyId];
		    $validQuantity = (int) $data['history'][$buyId]['selldata'][$uId]['quantity'] + 1;
		    $originalQuantity = (int) $data['history'][$buyId]['selldata'][$uId]['quantity'];
		    $data['updated'] = true;
		}
	    }
		
	    
	    
	    
	    
	    
	    
	    
	    
	    
	}
	}
	
	

	
	
	$this->load->view('header');
	$this->load->view('edithistorysell', $data);
	$this->load->view('footer');
    }

    public function check_item_exists() {
	// used to create an error message in a buy
	if ($this->Logmodel->itemExists($this->input->post('item')) == 0) {
	    $this->form_validation->set_message('check_item_exists', 'This item is not valid!If you think it is, please report it <a class ="alert-link" href="/correction">here</a>. Thank you!');
	    return false;
	} else {
	    return true;
	}
    }
    
    public function date_at_historysell() {
	#date check
	$sellDate = strtotime($this->input->post('date'));
	$itemid = $this->uri->segment(3);
	$history2 = $this->Logmodel->generateHistory($this->sessiondata['userid']);
	$buyid = $this->uri->segment(4);
	$uid = $this->uri->segment(5);
	$purchaseDate = (int) $history2[$buyid]['date'];
	
	if ($sellDate <= $purchaseDate) {
	    $diff = $sellDate - $purchaseDate;
	    $this->form_validation->set_message('date_at_historysell', "The sell's date cannot be set earlier than the buy's date. The time difference is: $diff seconds");
	    return false;
	} else {
	    return true;
	}
    }

    public function date_is_lower() {
	#its return false if i try to date back a sell before the buy
	$sellDate = strtotime($this->input->post('date'));
	$itemid = $this->uri->segment(3);
	$bank = $this->Logmodel->updateBank($this->sessiondata['userid']);
	$buyid = $this->uri->segment(4);
	$purchaseDate = (int) $bank[$itemid]['buydata'][$buyid]['date'];
	
	if ($sellDate <= $purchaseDate) {
	    $diff = $sellDate - $purchaseDate;
	    $this->form_validation->set_message('date_is_lower', "The sell's date cannot be set earlier than the buy's date. The time difference is: $diff");
	    return false;
	} else {
	    return true;
	}
    }
    
    public function date_is_higher() {
	#returns false if i try to date a buy higher than the first sell
	#hibás!
	$postDate = strtotime($this->input->post('date'));
	$buyId = (int) $this->uri->segment(4);
	$sellData = $this->Logmodel->getSellDataByid($buyId, $this->sessiondata['userid']);
	if ($sellData[0]['date'] < $postDate) {
	    $this->form_validation->set_message('date_is_higher', "You dated your buy after your first sell. Your date cannot be set earlier than: " . date("Y-m-d H:i:s", $sellData[0]['date']));
	    return false;
	} else {
	    return true;
	}
    }
    
    public function date_is_higher_history() {
	#returns false if i try to date a buy higher than the first sell at historyedit
	$postDate = strtotime($this->input->post('date'));
	$buyId = $this->uri->segment(3);
	$sellData = $this->Logmodel->getHistoryellByBuyId($buyId, $this->sessiondata['userid']);

	if ($sellData[0]['date'] < $postDate) {
	    $this->form_validation->set_message('date_is_higher_history', "You dated your sell after your first sell. Your date cannot be set earlier than: " . date("Y-m-d H:i:s", $sellData[0]['date']));
	    return false;
	} else {
	    return true;
	}
    }

    public function is_it_over1gp() {
	//buy form callback check function
	if ($this->input->post('buy') !== null) {
	    $price = (int) $this->input->post('price');
	    $quantity = (int) $this->input->post('quantity');
	} elseif ($this->input->post('sell') !== null) {
	    $price = (int) $this->input->post('sellprice');
	    $quantity = (int) $this->input->post('quantitytosell');
	} elseif ($this->input->post('saveButton') !== null) {
	    $price = (int) $this->input->post('price');
	    $quantity = (int) $this->input->post('quantity');
	}

	$each = $price / $quantity;

	if ($price < $quantity) {
	    $this->form_validation->set_message('is_it_over1gp', 'Your total price is lower than the quantity.');
	    return false;
	} else {
	    if (is_float($each) or is_double($each)) {
		$this->form_validation->set_message('is_it_over1gp', 'That wasn\'t a the valid price for that quantity. The item\'s calculated each price has to be integer');
		return false;
	    } else {
		return true;
	    }
	}
    }

    public function editbuy_over1gp() {
//buy form callback check function for
//editbuy view (edit single one buy)
	$price = (int) $this->input->post('price');
	$quantity = (int) $this->input->post('quantity');

	if ($this->input->post('price') == 0 or $this->input->post('quantity') == 0) {
	    $this->form_validation->set_message('editbuy_over1gp', 'Your input contains invalid datas (null,0).');
	    return false;
	}

	#After width modificated values

	$each = ($price / $quantity); #each of all subsells and input

	if ($price < $quantity) {
	    $this->form_validation->set_message('editbuy_over1gp', 'Your total price is  lower than the quantity. Based on Your input and subsells.');
	    return false;
	} else {
	    if (is_float($each) or is_double($each)) {
		$this->form_validation->set_message('editbuy_over1gp', 'That\'s not the valid price (' . number_format($price) . ') for that quantity (' . number_format($price) . ')');
		return false;
	    } else {
		return true;
	    }
	}
    }

    function item_is_null() {
	if ($this->input->post('itemtosell') == "null") {
	    $this->form_validation->set_message('item_is_null', 'Select an item to sell.');
	    return false;
	} else {
	    return true;
	}
    }

    function item_is_sellable() {
	if ($this->input->post('sellprice') < $this->input->post('quantitytosell')) {
	    $this->form_validation->set_message('item_is_sellable', 'Selling price cannot be less than the quantity.');
	    return false;
	} else {
	    return true;
	}
    }

    public function deleteItemFromBank($item) {
	#it deletes an item from the bank completely
	$this->Logmodel->deleteItemFromBank($item, $this->sessiondata['userid']);
	$itemname = $this->Logmodel->getItemNameFromId($item);
	$message = '<div class="alert alert-success alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <strong>Success!</strong>  ' . $itemname . ' has been succesfully removed from your bank</div>';
	$this->session->set_flashdata('deleteMessage', $message);
	redirect('ui/bank');
    }

    public function deleteBuy($buyId) {
	#deletes from buy and sell table completely
	$this->Logmodel->deleteSingleBuy($buyId, $this->sessiondata['userid']);
	$message = '<div class="alert alert-success alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <strong>You removed that Single Buy!</strong></div>';
	$this->session->set_flashdata('deleteMessage', $message);
	redirect('ui/bank');
    }

    public function deleteItemFromHistory($buyid) {
	#it deletes a buy (with sells from the history)
	$this->Logmodel->deleteItemFromHistory($buyid, $this->sessiondata['userid']);
	$message = '<div class="alert alert-success alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <strong>Success!</strong>  this trade has been succesfully removed from your history</div>';
	$this->session->set_flashdata('deleteMessage', $message);
	redirect('ui/history');
    }

    public function deleteSell($sellId) {
	#delete a simple sell by id
	$this->Logmodel->deleteSell($sellId, $this->sessiondata['userid']);
	$message = '<div class="alert alert-success alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <strong>Success!</strong>  this sell has been succesfully removed. Your items are waiting in your bank to re-sell them again.</div>';
	$this->session->set_flashdata('deleteMessage', $message);
	redirect('ui/bank');
    }

    public function deleteSellFromHistory($uid) {
	#this delete a single sell from history, and put back to bank the rest
	$this->Logmodel->deleteSubSellFromHistory($uid, $this->sessiondata['userid']);
	$message = '<div class="alert alert-success alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <strong>Successfully removed a SubSell!</strong>  You can sell the rest now. The other subsels are also listed in the review section. </div>';
	$this->session->set_flashdata('deleteMessage', $message);
	redirect('ui/bank');
    }

    public function d_eleteitemfrombank() {
	$data = '';
	$data ['deletemessage'] = '';
	if ($this->input->post('delete')) {
	    if ($this->Logmodel->deleteitemfrombank($this->input->post('itemid'), $userid = 1)) {
		$data ['deletemessage'] = '<div class="alert alert-success alert-dismissible" role="alert">
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <strong>' . $this->input->post('itemaname') . ' </strong> has been succesfully deleted from your bank.</div>';
		$this->load->view("header");
		$this->load->view("bank", $data);
		$this->load->view("footer");
	    }
	} else {
	    $this->load->view("header");
	    $this->load->view("deletefrombank", $data);
	    $this->load->view("footer");
	}
    }

}

?>
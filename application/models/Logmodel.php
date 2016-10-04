<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Logmodel extends CI_Model {

    function __construct() {
	parent::__construct();
    }

    public function autocomplete() {
	// autocompletes the trade page->buy form->search item
	//show only 24 row, because of right hand side scrollbar appears -> site resize automatically =/
	$query = $this->db->select('id,name')
			->like('name', $this->input->get('term'))
			->limit(24)
			->get('apidb', 15)->result_array();

	foreach ($query as $row) {
	    $row ['value'] = htmlentities(stripslashes($row ['name']));
	    $row ['id'] = (int) $row ['id'];
	    $row_set [] = $row;
	}
	return $row_set;
    }

    public function recordbuy($item, $price, $quantity, $time, $userid) {
	if (!$userid) {
	    return false;
	}
	if ($this->itemExists($this->input->post('item')) == 0) {
	    return false;
	} else {
	    $itemid = $this->itemExists($this->input->post('item'));
	    echo $itmemid;
	}
	// this will insert into buy table
	$data = ["itemid" => $itemid, "price" => $price, "quantity" => $quantity, "userid" => $userid, "date" => $time];
	if ($this->db->insert("buy", $data)) {
	    //succesfully updated, but we need to update our bank
	    $this->updateBank($userid);
	    return true;
	} else {
	    return false;
	}
    }

    public function recordSell($item, $price, $quantity, $time, $userid) {
	if (!$userid) {
	    return false;
	}
	$bank = $this->updateBank($userid);
	if (isset($bank[$item]['data']['name']) && $price > 0 && $quantity > 0 && isset($time)) {
	    $itemid = (int) $item;
	    $item = $bank[$item];
	    $each = $price / $quantity;
	    //checking: are we bought as so much that we wanted to sell?
	    if ($quantity <= $item['data']['availablequantity']) {
		//its less than or equal.
		$involvedbuys = [];
		$remaining = $quantity;
		$finished = [];
		foreach ($item['buydata'] as $buy => $data) {
		    //ha még el kell adni és van hely
		    if ($remaining > 0 && $data['available'] !== 0) {
			//ha az eladnivaló nagyobb mint a hely
			if ($remaining >= $data['available']) {
			    //akkor eladhat nyogudtan full availablét
			    $involvedbuys[$buy] = ['available' => $data['available'],
				'quantity' => $data['available'],
				'price' => ($each * $data['available']),
				'finished' => 1];
			    $remaining -= $data['available'];
			    //viszont ha kisebb, akkor már csak a remainingot
			} else {
			    $involvedbuys[$buy] = ['available' => $data['available'],
				'quantity' => $remaining,
				'price' => ($each * $remaining), 'finished' => 0];
			    $remaining = 0;
			}
		    }
		}


//INVOLVEDBUYS TO TRADES
		foreach ($involvedbuys as $buyid => $data) {
		    $dbarray = ['buyid' => $buyid,
			'userid' => $userid,
			'itemid' => $itemid,
			'price' => $data['price'],
			'quantity' => $data['quantity'],
			'date' => $time
		    ];
		    $this->db->insert('sell', $dbarray);

//DELETE THE CURRENT BUY
		    $update = $this->updateBank($userid);
		    $bank = $update[$itemid];
		    
		    
		    if ($data['finished'] == 1) {
			//movetobank comes here and update sellprice
			$todb = ['buyid' => $buyid,
			    'userid' => $userid,
			    'itemid' => $itemid,
			    'buyprice' => $bank['buydata'][$buyid]['price'],
			    'sellprice' => 0,
			    'quantity' => $bank['buydata'][$buyid]['quantity'],
			    'date' => $bank['buydata'][$buyid]['date']];
			$this->db->insert('historybuy', $todb);



			$q = $this->db->select('*')
				->where('userid', $userid)
				->where('buyid', $buyid)
				->get('sell');
			$q2 = $q->result_array();
			foreach ($q2 as $sells) {
			    $tosellhistory = ['buyid' => $buyid,
				'userid' => $userid, 'itemid' => $itemid,
				'price' => $sells['price'],
				'quantity' => $sells['quantity'],
				'date' => $sells['date']];
			    $this->db->insert('historysell', $tosellhistory);

			    #összesíteni kell a historysell adott buyid hez tartozó pricejait

			    $query = $this->db->select('price')
				    ->where('buyid', $buyid)
				    ->where('userid', $userid)
				    ->get('historysell');

			    $sellprice = 0;
			    $result = $query->result_array();
			    foreach ($result as $calcprice) {
				$sellprice += $calcprice['price'];
			    }

			    #updatelem a priceot. a historybuyban
			    #
                            $todb = [
				'sellprice' => $sellprice
			    ];
			    $this->db->where('buyid', $buyid)
				    ->where('userid', $userid)
				    ->update('historybuy', $todb);
			    //deletefromsells
			    $this->db->delete('sell', ['buyid' => $buyid]);
			    //deletefrombuys
			    $this->db->delete('buy', ['id' => $buyid]);
			}
		    }
		}
		$this->updateBank($userid);
		return true;
	    } else {
		//too much item. we cant sell it.
		return false;
	    }
	} else {
	    return 'Missing data';
	}
    }

    public function deleteSubSellFromHistory($uId, $userId) {
	#this delete the selected subsell, and put it back to buy and sells table
	$query_uid = $this->db->select('buyid')
		->where('uid', $uId)
		->where('userid', $userId)
		->get('historysell');
	$thisUid = $query_uid->result_array();

	$buyId = $thisUid[0]['buyid'];

	#get historybuy data

	$querybuy = $this->db->select('*')
		->where('buyid', $buyId)
		->where('userid', $userId)
		->get('historybuy');
	$buyresult = $querybuy->result_array();

	#put back to buy
	foreach ($buyresult as $buy) {
	    $data = [
		'id' => $buyId,
		'userid' => $userId,
		'itemid' => $buy['itemid'],
		'price' => $buy['buyprice'],
		'quantity' => $buy['quantity'],
		'date' => $buy['date']];

	    $this->db->insert('buy', $data);
	}
	#delete from historybuy
	$this->db->where('userid', $userId)
		->where('buyid', $buyId)
		->delete('historybuy');

	#get the sells list

	$query = $this->db->select('*')
		->where('userid', $userId)
		->where('buyid', $buyId)
		->get('historysell');
	$historySellResult = $query->result_array();

	foreach ($historySellResult as $sell) {
	    if ($sell['uid'] !== $uId) {
		$data = [
		    'uid' => $sell['uid'],
		    'buyid' => $buyId,
		    'userid' => $userId,
		    'itemid' => $sell['itemid'],
		    'price' => $sell['price'],
		    'quantity' => $sell['quantity'],
		    'date' => $sell['date']
		];
		#insert on sells
		$this->db->insert('sell', $data);
	    }
	    $this->db->where('userid', $userId)
		    ->where('buyid', $buyId)
		    ->delete('historysell');
	}
    }

    public function deleteSingleBuy($buyId, $userId) {
	$this->db->where('id', $buyId)
		->where('userid', $userId)
		->delete('buy');

	$this->db->where('buyid', $buyId)
		->where('userid', $userId)
		->delete('sell');

	return true;
    }

    public function updateBank($userid) {
	if (!$userid) {
	    return false;
	}
	$boughttransactions = [];
	//The aim is for this function is to create an associative array both from sell, and both for buy transactions like this:
	//bought = [
	//['buydata' = [array with buy transactions],
	//'selldata' => [array with sell transactions,
	//'data' => [calculations and other item related statistics]
	$q = $this->db->select('*,buy.id as id,apidb.members as member,apidb.id as geid')
		->where('userid', $userid)
		->order_by('buy.id', 'ASC')
		->join('apidb', 'apidb.id=buy.itemid')
		->get('buy');
	$n = $q->result_array();
//if nothing bought yet
	if (count($n) == 0) {
	    return false;
	} else {
//if found any buy transaction we start to assemble the data
//found one or many item lets assemble the following array:
//OPEN BUY TRADE
	    foreach ($n as $result) {
		$boughttransactions[$result['itemid']]['buydata'][$result['id']] = array('quantity' => $result['quantity'],
		    'price' => $result['price'],
		    'date' => $result['date'],
		    'available' => $result['quantity'],
		    'each' => $result['price'] / $result['quantity']);

//initialize ['data'] if its not
		if (!isset($boughttransactions[$result['itemid']]['data'])) {
		    $boughttransactions[$result['itemid']]['data'] = array('availablequantity' => $result['quantity'],
			'totalbuyprice' => $result['price'],
			'totalsellprice' => 0,
			'totalquantity' => $result['quantity'],
			'remainingprice' => $result['price'],
			'name' => $result['name'],
			'buydate' => $result['date'],
			'p4h' => $result['p4h'],
			'geid' => $result['geid'],
			'icon' => "http://services.runescape.com/m=itemdb_rs/5020_obj_sprite.gif?id=" . $result['geid'],
			'member' => $result['member'],
			'description' => $result['description'],
			'each' => $result['price'] / $result['quantity']);
		} else {//or just add if exists
		    $boughttransactions[$result['itemid']]['data']['availablequantity'] += $result['quantity'];
		    $boughttransactions[$result['itemid']]['data']['remainingprice'] += $result['price'];
		    $boughttransactions[$result['itemid']]['data']['totalbuyprice'] += $result['price'];
		    $boughttransactions[$result['itemid']]['data']['totalquantity'] += $result['quantity'];
		    $boughttransactions[$result['itemid']]['data']['buydate'] = $result['date'];
		}//update total price and set available quantity
	    }
	    
//USES OF THE BANK DATA ARRAY	    
//	    ["data"]=>
//      ["availablequantity"]=> #on bank view -> show the available quantity left
//      int(1500)
//      ["totalbuyprice"]=>
//      int(25000)
//      ["totalsellprice"]=>
//      int(0)
//      ["totalquantity"]=>
//      int(1500)
//      ["remainingprice"]=>
//      int(25000)
//      ["name"]=>
//      string(5) "Bones" on many view to show item name
//      ["buydate"]=>
//      string(10) "1449717262" on bankview show the date
//      ["p4h"]=> on bankview. calculating the limit left
//      string(5) "10000"
//      ["geid"]=> used in the showicon() 
//      string(3) "526" 
//      ["icon"]=> used to display icon if not existing on my server
//      string(68) "http://services.runescape.com/m=itemdb_rs/5020_obj_sprite.gif?id=526"
//      ["member"]=> not in use but planned
//      string(1) "0"
//      ["description"]=> used in the icon alt message
//      string(22) "Bones are for burying."
//      ["each"]=> used on the bank page to show each price
//      int(15)
	
//MODIFY WITH SOLD SIDE
	    $q = $this->db->select('*')
		    ->where('userid', $userid)
		    ->order_by('date', 'ASC')
		    ->get('sell');
	    $n = $q->result_array();

	    if (count($n) !== 0) {
//ITERATE SELL SIDE IF EXISTS ANY SELL
		foreach ($n as $result) {
		    $boughttransactions[$result['itemid']]['solddata'][$result['uid']] = array('quantity' => $result['quantity'],
			'price' => $result['price'],
			'date' => $result['date'],
			'buyid' => $result['buyid'],
			'uid' => $result['uid']);


//calculate back the available quantity, prices, set dates.
		    $boughttransactions[$result['itemid']]['buydata'][$result['buyid']]['available'] -= $result['quantity'];
//reduce the sold quantity from the total
		    $boughttransactions[$result['itemid']]['data']['availablequantity'] -= $result['quantity'];
		    $boughttransactions[$result['itemid']]['data']['solddate'] = $result['date'];
//total sell and buy prices
		    $boughttransactions[$result['itemid']]['data']['totalsellprice'] += $result['price'];
		    $boughttransactions[$result['itemid']]['data']['totalbuyprice'] -= $result['price'];
		}
	    }

//calulate the total each 
	    #need to finish
	    foreach ($boughttransactions as $itemid => $itemdata) {
		if ($boughttransactions[$itemid]['data']['availablequantity'] != 0) {
		$preprice = 0;
		$prequantity = 0;
		foreach ($boughttransactions[$itemid]['buydata'] as $key => $value) {
		    $each = $boughttransactions[$itemid]['buydata'][$key]['price'] / $boughttransactions[$itemid]['buydata'][$key]['quantity'];
		    $preprice += $boughttransactions[$itemid]['buydata'][$key]['available'] * $each;
		    $prequantity += (int) $boughttransactions[$itemid]['buydata'][$key]['available'];
		}
		$boughttransactions[$itemid]['data']['each'] = $preprice / $prequantity;
		}
	    }

//IF SELL SIDE NOT EXISTS JUST RETURN
	    return $boughttransactions;
	}
    }

    public function updateSell($uid, $price, $quantity, $date) {
	$todb = [
	    'price' => $price,
	    'quantity' => $quantity,
	    'date' => $date
	];

	$this->db->where('uid', $uid)->update('sell', $todb);
    }

    public function getItemLimitFromName($item) {
//needs an itemname (because the autocomplete uses this), to get give back the Per4Hour Limit
	$query = $this->db->select("p4h")->where("name", $item)->get("apidb");
	$q = $query->result();

	if (isset($q [0])) {
	    return $q [0]->p4h;
	}
// remove for testing. if no error. should be this code deleted
//	else{
//	    return "Wrong item name. No Limit.";
//	}
    }

    public function itemExists($name) {
//autocomplete use this. return it's not exists, or gives back the item id.
	$query = $this->db->select('id')
		->where('name', $name)
		->get('apidb');
	$q = $query->result();

	if (isset($q [0])) {
	    return $q [0]->id;
	}
//commented for testing if no error. delete me pls	
//	else {
//	    return false;
//	}
    }

    public function getItemNameFromBuyId($buyId, $userId) {
	$query = $this->db->select('items.name,items.geid')
		->where('buy.id', $buyId)
		->where('buy.userid', $userId)
		->join('items', 'buy.itemid=items.id')
		->get('buy');

	$result = $query->result_array();

	if (count($result) !== 0) {
	    return $result[0];
	} else {
	    return false;
	}
    }

    public function getItemNameFromId($id) {
//autocomplete use this. Gives back the str(item name) from an (int) items.id
	$q = $this->db->select('name')
		->where('id', $id)
		->get('apidb');
	$r = $q->result();

	if (isset($r [0])) {
	    return $r [0]->name;
	} else {
	    return "This item id is invalid";
	}
    }
    
    public function generateHistory($userid) {
	//the two query generates the buy and sold side
	
	$buyquery = $this->db->select('*')
		->where('historybuy.userid', $userid)
		->join('apidb', 'historybuy.itemid=apidb.id')
		->order_by('historybuy.date', 'ASC')
		->get('historybuy');
	$buyresult = $buyquery->result_array();
	$result = [];

	if (count($buyresult) == 0) {
	    $result = NULL;
	} else {
	    foreach ($buyresult as $value) {
		$result[$value['buyid']] = ['id' => $value['id'], 
					    'buyid' => $value['buyid'], 
					    'itemid' => $value['itemid'], 
					    'buyprice' => $value['buyprice'], 
					    'sellprice' => $value['sellprice'], 
					    'quantity' => $value['quantity'], 
					    'date' => $value['date'],
					    'name' => $value['name'],
					    'p4h'=> $value['p4h'],
					    'member' => $value['members']];
	    }

	    $query = $this->db->select('*')
		    ->where('historysell.userid', $userid)
		    ->join('apidb', 'historysell.itemid=apidb.id')
		    ->order_by('historysell.date', 'ASC')
		    ->get('historysell');
	    $sellresult = $query->result_array();

	    if (count($sellresult) !== 0) {
		foreach ($sellresult as $value) {
		    $result[$value['buyid']]['selldata'][$value['uid']] = ['uid' =>$value['uid'],
									   'buyid' => $value['buyid'],
									    'itemid' => $value['itemid'],
									    'price' => $value['price'],
									    'quantity' => $value['quantity'],
									    'date' => $value['date']];   
		}
		
	    }
	}
		return $result;
    }

// Uncommented for it's maybe unused
//    public function grabSellsFromSellHistory($userid) {
//	//the two query generates the buy and sold side
//	$query = $this->db->select('*')
//		->where('historysell.userid', $userid)
//		->join('apidb', 'historysell.itemid=apidb.id')
//		->order_by('historysell.buyid', 'ASC')
//		->get('historysell');
//	$sellresult = $query->result_array();
//	if (count($sellresult) == 0) {
//	    $sellresult = NULL;
//	}
//	    return $sellresult;
//	
//    }

    public function getBuyDataById($buyId, $userId) {
	$query = $this->db->select('*,buy.id as buyid')
		->where('buy.id', $buyId)
		->where('userid', $userId)
		->join('apidb', 'apidb.id=buy.itemid')
		->get('buy');

	$result = $query->result_array();
	if (count($result) !== 0) {
	    return $result;
	} else {
	    return false;
	}
    }
    
    public function getHistoryellByBuyId($buyId, $userId) {
	$query = $this->db->select('*')
		->where('buyid', $buyId)
		->where('userid', $userId)
		->order_by('date', 'ASC')
		->get('historysell');

	$result = $query->result_array();
	if (count($result) == 0) {
	    $result = NULL;
	}
	return $result;
    }

    public function getSellDataByid($buyId, $userId) {
	$result = $this->db->select('*')->where('buyid', $buyId)->where('userid', $userId)->order_by('date', 'ASC')->get('sell')->result_array();
	if (count($result) == 0) {
	    $result = NULL;  
	}
	return $result;
    }

    public function getSellDataByUid($uid, $userId) {
	$result = $this->db->select('*')->where('uid', $uid)->where('userid', $userId)->get('sell')->result_array();
	if (count($result) !== 0) {
	    return $result;
	} else {
	    return false;
	}
    }

    public function getSoldQuantityByBuyId($buyId, $userId) {
	$q = $this->db->select('SUM(quantity) as a')->where('buyid', $buyId)->where('userid', $userId)->get('sell')->result_array();
	if ($q[0]['a'] > 0) {
	    return (int) $q[0]['a'];
	} else {
	    return false;
	}
    }

    public function getSumPriceFromSoldByBuyid($buyId, $userId) {
	$q = $this->db->select('SUM(price) as a')->where('buyid', $buyId)->where('userid', $userId)->get('sell')->result_array();
	if ($q[0]['a'] > 0) {
	    return (int) $q[0]['a'];
	} else {
	    return false;
	}
    }

    public function listBuys($itemid) {
	//this generate a table with all of specific buys from an item
	#unused
	$bank = $this->updateBank($this->sessiondata['userid']);
	return $table;
    }

    public function limitRemaining($item, $userid) {
	//request an itemid, and gives back the limit remaining
	$fourHoursBack = (int) time() - (60 * 60 * 4);
	$query = $this->db->select('quantity,apidb.p4h')
		->where('itemid', $item)
		->where('userid', $userid)
		->join('apidb', 'buy.itemid = apidb.id')
		->where('date >', $fourHoursBack)
		->get('buy');

	$query = $query->result_array();

	$historyquery = $this->db->select('quantity')
		->where('userid', $userid)
		->where('itemid', $item)
		->where('date >', $fourHoursBack)
		->get('historybuy');

	$historybuyresults = $historyquery->result_array();
	$bought = (int) 0;
	if (count($query) !== 0) {

	    foreach ($query as $arrayindex => $data) {
		$bought += (int) $data['quantity'];
	    }
	}

	if (count($historybuyresults !== 0)) {
	    foreach ($historybuyresults as $arrayindex => $data) {
		$bought += (int) $data['quantity'];
	    }
	}

	$query = $this->db->select('p4h')->where('id', $item)->get('apidb')->result_array();
	$limit = (int) $query[0]['p4h'];


	if ($limit == 0) {
	    return '<a data-toggle="tooltip" title="Oh dear... This item limit is missing in our database. But you can help. Click on the item link in the 3rd column check the runescape wikia, push this button and etner the value. After a short period of time (supervision) will be updated in our database." href = "suggestlimit/' . $item . '" class = "btn btn-primary btn-xs" role = "button">' . $this->fliplog->insertGlyph('review') . ' Help us</a>';
	}


	if ($bought > 0) {
	    if ($limit - $bought < 0) {
		return 0;
	    } else {
		return (int) $limit - $bought;
	    }
	} else {
	    return (int) $limit;
	}
    }

    function printr($data, $exit = FALSE) {
	if ($data) {
	    print ' <

                        pre>';
	    print_r($data);
	    print '</ pre>';
	}
	if ($exit) {
	    exit;
	}
    }
    
    public function updateSellpriceInHistory($buyId, $userId){
	//update sellprice
	$query = $this->db->select('price')
		->where('buyid', $buyId)
		->where('userid', $userId)
		->get('historysell');

	$sellprice = 0;
	$result = $query->result_array();
	foreach ($result as $calcprice) {
	    $sellprice += $calcprice['price'];
	}

	$todb = [
	    'sellprice' => $sellprice
	];
	$this->db->where('buyid', $buyId)
		->where('userid', $userId)
		->update('historybuy', $todb);
    }
    
    public function moveToHistory($buyId, $userId) {
	//UNFINISHED
	//get the buy details
	$q = $this->db->select('*')
		->where('userid', $userId)
		->where('id', $buyId)
		->get('buy');

	$q2 = $q->result_array();

	//assemble the data
	foreach ($q2 as $value) {
	    $tohistorybuy = [
		'buyid' => $value['id'],
		'userid' => $value['userid'],
		'itemid' => $value['itemid'],
		'buyprice' => $value['price'],
		'sellprice' => 0,
		'quantity' => $value['quantity'],
		'date' => $value['date']
	    ];
	}
	//write into historybuy
	$this->db->insert('historybuy', $tohistorybuy);


	//get the sell side
	$qs = $this->db->select('*')
		->where('userid', $userId)
		->where('buyid', $buyId)
		->get('sell');

	$qr = $qs->result_array();

	foreach ($qr as $value) {
	    $tohistorysell = [
		'buyid' => $value['buyid'],
		'userid' => $value['userid'],
		'itemid' => $value['itemid'],
		'price' => $value['price'],
		'quantity' => $value['quantity'],
		'date' => $value['date']
	    ];
	    //write in to the db
	    $this->db->insert('historysell', $tohistorysell);
	}


	$this->updateSellpriceInHistory($buyId, $userId);

	//delete historybuy
	//delete historysell
	$this->db->delete('buy', ['id' => $buyId, 'userid' => $userId]);
	$this->db->delete('sell', ['buyid' => $buyId, 'userid' => $userId]);
	$this->updateBank($userId);
    }
    
     public function moveToBank($buyId, $userId) {
	//get the buy details
	$q = $this->db->select('*')
		->where('userid', $userId)
		->where('buyid', $buyId)
		->get('historybuy');

	$q2 = $q->result_array();

	//assemble the data
	foreach ($q2 as $value) {
	    $tobuy = [
		'id' => $value['buyid'],
		'userid' => $value['userid'],
		'itemid' => $value['itemid'],
		'price' => $value['buyprice'],
		'quantity' => $value['quantity'],
		'date' => $value['date']
	    ];
	}

	//write into historybuy
	$this->db->insert('buy', $tobuy);


	//get the sell side
	$qs = $this->db->select('*')
		->where('userid', $userId)
		->where('buyid', $buyId)
		->get('historysell');

	$qr = $qs->result_array();

	foreach ($qr as $value) {
	    $tosell = [
		'uid' => $value['uid'],
		'buyid' => $value['buyid'],
		'userid' => $value['userid'],
		'itemid' => $value['itemid'],
		'price' => $value['price'],
		'quantity' => $value['quantity'],
		'date' => $value['date']
	    ];
	    //write in to the db
	    $this->db->insert('sell', $tosell);
	}

	//delete historybuy
	//delete historysell
	$this->db->delete('historybuy', ['buyid' => $buyId, 'userid' => $userId]);
	$this->db->delete('historysell', ['buyid' => $buyId, 'userid' => $userId]);
	$this->updateBank($userId);
    }

    function cleartables() {
	#use on ui/test
	#generate a specific situation for testing purposes
	$this->db->delete('historybuy', 'userid  = 1');
	$this->db->delete('historysell ', 'userid = 1');
	$this->db->delete('buy', 'userid = 1');
	$this->db->delete('sell ', 'userid = 1');
    }

    public function deleteItemFromBank($itemid, $userid) {
//it' s delete an item from the buy and sell table, and return TRUE or FALSE
	$this->db->where('userid', $userid)
		->where('itemid', $itemid)
		->delete('buy');

	$this->db->where('userid', $userid)
		->where('itemid', $itemid)
		->delete('sell');
	return true;
    }

    public function deleteItemFromHistory($buyid, $userid) {
//it's delete an item from the buy and sell table, and return TRUE or FALSE
	$this->db->where('userid', $userid)
		->where('buyid', $buyid)
		->delete('historybuy');

	$this->db->where('userid', $userid)
		->where('buyid', $buyid)
		->delete('historysell');
	return true;
    }

    public function deleteSell($sellId, $userId) {
	#delete a simple sell
	$this->db->where('userid', $userId)
		->where('uid', $sellId)
		->delete('sell');
	return true;
    }

    public function addItem($name, $id, $description, $member) {
	#its add a new entry to apidb table (api/fetchitem uses this)
	$data = [
	    'name' => $name,
	    'id' => $id,
	    'description' => $description,
	    'member' => $member
	];
	$this->db->insert('apidb', $data);

	return true;
    }

    public function todayFlipCount($userId) {
	//calculates all finished flips count(rows in historybuy) from today midnight
	//returns an int
	$midnight = strtotime('today midnight');
	$q = $this->db->select('DISTINCT(buyid) as buyid')
		->where('userid', (int) $userId)
		->where('date >', (int) $midnight)
		->get('historysell');
	$r = $q->result_array();

	return (int) count($r);
    }

    public function todayProfit($userId) {
	//calculate all profit from today mindnight
	//returns an int
	$midnight = strtotime('today midnight');
	$q = $this->db->select('buyprice,sellprice')
		->where('userid', $userId)
		->where('date >', $midnight)
		->get('historybuy');
	$r = $q->result_array();

	//iterating throught the flips and calculating the profit
	if (count($r) !== 0) {
	    $profit = 0;
	    foreach ($r as $data) {
		$profit += (int) $data['sellprice'] - (int) $data['buyprice'];
	    }

	    return (int) $profit;
	} else {
	    return 0;
	}
    }

    public function getColumnMaxValue($column = null, $table = null) {
	#api fetchitem uses this to determine, from where to continue the fetch
	if ($column == null or $table == null) {
	    return false;
	}

	$q = $this->db->select("MAX($column) as value")->get($table)->result_array();

	if ($q[0]['value']) {
	    return (int) $q[0]['value'];
	} else {
	    return false;
	}
    }

    public function showIcon($id,$name='The item\'s icon') {
	// $id = the items geid, shows a small icon, but if it's not found on my server, it's embed from the runescape website
	if (!file_exists("images/icons/" . $id . ".gif")) {
	    $image_properties = array(
		'src' => "http://services.runescape.com/m=itemdb_rs/5332_obj_sprite.gif?id=" . $id,
		'alt' => 'Icon',
		'class' => '',
		'title' => $name);
	    return img($image_properties);
	} else {
	    $image_properties = array(
		'src' => "images/icons/" . $id . ".gif",
		'alt' => 'Icon',
		'class' => '',
		'title' => 'Item icon');
	    return img($image_properties);
	}
    }
}
?>
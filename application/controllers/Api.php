<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Api extends CI_Controller {

	public function allItemCount(){
		//show all item count in my db (must be to decide later updates)
		$count = $this->db->select('SUM(catcount)')->get('categories')->result_array();
		echo 'All items in my database: ' . $count[0]['SUM(catcount)'];
	}
	
	public function updateAllItem($from=0,$to=37){
		#if it's stops try to set your max_execution_time in your php.ini to 0
		set_time_limit(0);
		echo '<h1>Fetching start</h1>';
		#get catid's
		
		#create the tables if its not exists
		$this->checkTable();
		
		$result = $this->db->select('catid')->get('categories')->result_array();
		if($to == 0){
			$to = count($result);
		}

		#generate offset
		$result = array_slice($result,$from,$to);
		#iterate throught the catid's
		foreach($result as $data){
			#fetching the category itself
			$this->fetchcategory($data['catid']);
			echo '<h3>Fetching category: '.$data['catid'].'</h3>';
		}
		echo '<h1>Finished</h1>';
	}
	
	public function fetchcategory($category){
		echo '<h1>Entering category: '.$category.'</h1>';
		//error_reporting(0);
		
		$itemcount = $this->getCategoryCount($category);

		
		foreach($this->getLettersCountByCat($category) as $letter => $count){

			if($count > 0){
				echo '<h3>Fetching letter: ' .$letter. ' (Items: '.$count.')</h3>';
				#iterate the pages
			
			if($count <= 12){
				$numofpages = 1;
			}else{
				$numofpages = ceil($count / 12);
			}
			$page = 1;
		
			while($page <= $numofpages){
				echo '<h3>Open page: ' .$page. ' ('. $page . ' out of ' . $numofpages . ')</h3>';
				if($letter == '#'){
					$letter = '%23';
				}
				$url = "http://services.runescape.com/m=itemdb_rs/api/catalogue/items.json?category=$category&alpha=$letter&page=$page";
				$file = $this->get_data($url);
				$file = json_decode($file);
				$response = get_object_vars($file);
				

				
				foreach($response['items'] as $value){
					if($value->members == "true"){
						$members = 1;
					}else{
						$members = 0;
					}
					echo 'Item: ' . $value->name . ' ';
					$todb['id'] = $value->id;
					$todb['type'] = $value->type;
					$todb['name'] = $value->name;
					$todb['description'] = $value->description;
					$todb['members'] = $members;
					
					$result = $this->db->select('id')->where('id',$value->id)->get('apidb')->result_array();
					if(!$result){
					$this->db->insert('apidb',$todb);
					echo '<img src="'.$value->icon.'"/>Added to database.<br/>';
					}else{
						echo 'Maybe already exists. Skipped. <br/>';
					}
					
					$this->saveIcons($value->id);
				}
				ob_flush();
				flush();
				$page++;
				sleep(6);
				}
				
			}else{
				echo '<h3>Skip letter: ' .$letter. ' (Items: '.$count.')</h3>';
			}
		}
	}
	
	public function updateAPICategoryItemsCount(){
		#this is update my categories itemcount from the GE APi Itemcount
		error_reporting(0);
		$categoriesarray = $this->db->select('catid')->get('categories')->result_array();
		
		foreach($categoriesarray as $category){
			$caturl = "http://services.runescape.com/m=itemdb_rs/api/catalogue/category.json?category=" . $category['catid'];
			$file = $this->get_data($caturl);
            $file = json_decode($file);
            $response = get_object_vars($file);
				$all = 0;
				foreach($response['alpha'] as $letter => $data){
					$all += $data->items;
				}
				echo 'Fetching categoryid: ' .$category['catid'] . ' Items: ' . $all . '<br/>';
				$this->db->where('catid',$category['catid'])->update('categories',['catcount' =>$all]);
				sleep(6);
				ob_flush();
				flush();
				
		}
		echo 'Fetching finished';
		ob_end_flush();		
				#var_dump($response);	
				##var_dump($response['alpha'][0]);
	}
	
	public function getLettersCountByCat($category){
		$url = "http://services.runescape.com/m=itemdb_rs/api/catalogue/category.json?category=$category";
		$file = $this->get_data($url);
        $file = json_decode($file);
        $response = get_object_vars($file);
		$alpha = [];
		#var_dump($response['alpha']);
		foreach($response['alpha'] as $letter => $data){
			$alpha[$data->letter] = $data->items;
		}
		
		return $alpha;
	}
	
	public function getCategoryCount($catid){
		#this gives back how many item belongs to a specific category (int)
		$result = $this->db->select('catcount')->get('categories')->result_array();
		return (int) $result[0]['catcount'];
	}
	
	public function checkTable(){
		#this check the apidb table. if not exists, create a new one
		
		if(!$this->db->table_exists('categories')){
			$this->db->query('CREATE TABLE `categories` (
							`catid` int(2) DEFAULT NULL,
							`catname` varchar(27) DEFAULT NULL,
							`catcount` int(1) DEFAULT NULL,
							UNIQUE KEY `catid` (`catid`)
							) ENGINE=MyISAM DEFAULT CHARSET=utf8');
			echo 'Table "categories" has been created <br/>';
			#filling up the categories table
			$categories = [ "Miscellaneous",
							"Ammo",
							"Arrows",
							"Bolts",
							"Construction materials",
							"Construction projects",
							"Cooking ingredients",
							"Costumes",
							"Crafting materials",
							"Familiars",
							"Farming produce",
							"Fletching materials",
							"Food and drink",
							"Herblore materials",
							"Hunting equipment",
							"Hunting produce",
							"Jewellery",
							"Mage armour",
							"Mage weapons",
							"Melee armour - low level",
							"Melee armour - mid level",
							"Melee armour - high level",
							"Melee weapons - low level",
							"Melee weapons - mid level",
							"Melee weapons - high level",
							"Mining and smithing",
							"Potions",
							"Prayer armour",
							"Prayer materials",
							"Range armour",
							"Range weapons",
							"Runecrafting",
							"Runes, Spells and Teleports",
							"Seeds",
							"Summoning scrolls",
							"Tools and containers",
							"Woodcutting product",
							"Pocket items"];
							
				$i = 0;			
				foreach($categories as $name){
					$this->db->insert('categories',['catid' => $i,'catname' => $name, 'catcount' => 0]);
					$i++;
				}
			
			
			echo 'Now. Determine How many item exists in a specific category. Update categories. No real item fetch happen now.';
			$this->updateAPICategoryItemsCount();
		}
		
		if(!$this->db->table_exists('apidb')){
			$this->db->query('CREATE TABLE `apidb` (
							 `id` int(11) NOT NULL,
							 `name` varchar(46) DEFAULT NULL,
							 `description` varchar(200) DEFAULT NULL,
							 `members` tinyint(1) DEFAULT NULL,
							 `limit` int(11) NOT NULL,
							 `type` varchar(50) NOT NULL,
							 PRIMARY KEY (`id`)
							) ENGINE=MyISAM DEFAULT CHARSET=utf8');
			echo 'Table "apidb" has been created<br/>';
		}
	}
	
	 public function saveIcons($icon) {
			#save icon and icon_large by the item id. skip if exists
            if (!file_exists("images/icons/" . $icon . ".gif")) {
                $url = "http://services.runescape.com/m=itemdb_rs/5026_obj_sprite.gif?id=" . $icon;
                $img = "images/icons/" . $icon . ".gif";
                $file = file($url);
                $result = file_put_contents($img, $file);
                
            }
			
			
            if (!file_exists("images/icons_large/" . $icon . ".gif")) {
                $url = "http://services.runescape.com/m=itemdb_rs/5026_obj_big.gif?id=" . $icon;
                $img = "images/icons_large/" . $icon . ".gif";
                $file = file($url);
                $result = file_put_contents($img, $file);
			}
            ob_flush();
            flush();
        }

  
    public function get_data($url) {
		#used to get the data
        $ch = curl_init();
        $timeout = 600;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
}
?>
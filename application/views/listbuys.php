<div class="col-md-8">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">
		<?php
		if ($this->sessiondata) {
		    echo "Your currently active buys on ";
		} else {
		    echo 'Acces forbidden';
		}
		?>
            </h3>
        </div>
        <div class="panel-body">
	    <?php
	    if ($this->sessiondata) {
		if (isset($deletemessage)) {
		    echo $deletemessage;
		}
		if ($this->session->flashdata('deleteMessage')) {
		    echo $this->session->flashdata('deleteMessage');
		}

		if (count($bank[$itemid]['buydata'] != 0)) {
		    $itemdata = $bank[$itemid];
		    ?>
		    <table class = "table table-hover">
			<tr style="text-align: center;">
			    <th><a href="#" data-toggle="tooltip" title="Numbering of the rows">#</a></th>
			    <th><a href="#" data-toggle="tooltip" title="The item icon">Icon</a></th>
			    <th><a href="#" data-toggle="tooltip" title="Item name">Item name</a></th>
			    <th><a href="#" data-toggle="tooltip" title="Green = Buy, Red = Sell">Direction</a></th>
			    <th><a href="#" data-toggle="tooltip" title="You bought/sold x of these item">Quantity</a></th>
			    <th><a href="#" data-toggle="tooltip" title="This is the price for this buy (or income if it's a sell) in total.">Cost</a></th>
			    <th><a href="#" data-toggle="tooltip" title="This is the item's price per each price..">Each</a></th>
			    <th><a href="#" data-toggle="tooltip" title="This is the date, when you added this flip.">Bought at</a></th>
			    <th><a href="#" data-toggle="tooltip" title="This is the quantity what you can sell.">Available</a></th>
			    <th style="text-align: center" colspan="2" ><a href="#" data-toggle="tooltip" title="Some action.">Actions</a></th>
			</tr>

			<?php
			$x = 1;
			foreach ($bank[$itemid]['buydata'] as $buyid => $data) {
			    $editBuy = '<a href="../editbuy/'. $itemid .'/' . $buyid . '" class="btn btn-primary btn-xs" role="button">' . $this->fliplog->insertGlyph('edit') . 'Edit</a>';
			    $deleteBuyButton = '<a href="' . base_url() . 'index.php/ui/deleteBuy/' . $buyid . '" onClick="if(!deleteSingleBuy(\'' . $buyid . '\',\'' . $itemdata['data']['name'] . '\')){return false};" class="btn btn-danger btn-xs" role="button">' . $this->fliplog->insertGlyph('remove') . 'Delete</a>';
			    ?>
	    		<tr style="background-color: #e5ffe5;">
	    		    <td><?= $x ?></td>
	    		    <td><?= $this->Logmodel->showIcon($itemid) ?></td>
	    		    <td>
	    			<span id="item"><a href="http://runescape.wikia.com/wiki/Special:Search?search=<?= $itemdata['data']['name'] ?>&fulltext=0"><?= $itemdata['data']['name'] ?></a></span>
	    		    </td>
	    		    <td><?= $this->fliplog->insertGlyph('right-arrow') . ' ' . $this->fliplog->insertGlyph('cart') ?></td>
	    		    <td><?= number_format($data['quantity']) ?></td>
	    		    <td><?= number_format($data['price']) ?> gp</td>
	    		    <td><?= number_format($data['price'] / $data['quantity']) ?> gp</td>
	    		    <td><span data-toggle="tooltip" title="<?= date("Y-m-d H:i:s", $data['date']) ?>"><?= $this->fliplog->time_elapsed_string($data['date']) ?></span></td>
	    		    <td><?= number_format($data['available']) ?></td>
	    		    <td><?= $editBuy ?></td>
	    		    <td><?= $deleteBuyButton ?></td>
	    		</tr>
			    <?php
			    $x++;
			    if (isset($bank[$itemid]['solddata'])) {
				    $y = 1;
				foreach ($bank[$itemid]['solddata'] as $sellarray => $selldata) {

				    if ($selldata['buyid'] == $buyid) {
					$editSell = '<a href="../editsell/' . $itemid . '/' . $buyid . '/' . $selldata['uid'] . '" class="btn btn-primary btn-xs" role="button">' . $this->fliplog->insertGlyph('edit') . 'Edit</a>';
					$deleteSell = '<a href="deletesell/' . $buyid . '" onClick="if(!confirmDeleteSell(\'' . $selldata['uid'] . '\',\'' . $itemdata['data']['name'] . '\')){return false};" class="btn btn-danger btn-xs" role="button">' . $this->fliplog->insertGlyph('remove') . 'Delete</a>';
					?>
					<tr style="background-color: #ffe5e5;">
					    <td style="text-align: center;"><?= $y ?></td>
					    <td><?= $this->Logmodel->showIcon($itemid) ?></td>
					    <td><span id="item"><a href="http://runescape.wikia.com/wiki/Special:Search?search=<?= $itemdata['data']['name'] ?>&fulltext=0"><?= $itemdata['data']['name'] ?></a></span></td>
					    <td><?= $this->fliplog->insertGlyph('cart') . ' ' . $this->fliplog->insertGlyph('right-arrow') ?></td>
					    <td><?= number_format($selldata['quantity']) ?></td>
					    <td><?= number_format($selldata['price']) ?> gp</td>
					    <td><?= number_format($selldata['price'] / $selldata['quantity']) ?> gp</td>
					    <td><span data-toggle = "tooltip" title = "<?= date("Y-m-d H:i:s", $selldata['date']) ?>"><?= $this->fliplog->time_elapsed_string($selldata['date']) ?> </span></td>
					    <td></td>
					    <td><?= $editSell ?></td>
					    <td><?= $deleteSell ?> </td>
					</tr>
					<?php
					$y++;
				    }
				    
				}
			    }
			}
		    }
		    ?>
    	    </table>
    	    <form class="form-inline" action="test" method="post" name="actionform">
    		<button type="submit" class="btn btn-primary btn-xs" name="sellfrombank">Clear Database</button>
    	    </form>
    <?php
} else {
    $this->load->view('loginrequired');
}
?>
        </div>
    </div>
</div>
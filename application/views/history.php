<div class="col-sm-10">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">
		<?php
		if ($this->sessiondata) {
		    echo "History. Here you can review your trade activity.";
		} else {
		    echo 'Acces forbidden';
		};
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

		if ($history != NULL) {
		    ?>
		    <table class = "table table-hover">
			<tr style="text-align: center;">
			    <th><a href="#" data-toggle="tooltip" title="Numbering of the rows">#</a></th>
			    <th><a href="#" data-toggle="tooltip" title="The item icon">Icon</a></th>
			    <th><a href="#" data-toggle="tooltip" title="Item name">Item name</a></th>
			    <th><a href="#" data-toggle="tooltip" title="Green = Buy, Red = Sell">Direction</a></th>
			    <th><a href="#" data-toggle="tooltip" title="You bought/sold x of these item">Quantity</a></th>
			    <th><a href="#" data-toggle="tooltip" title="This is the price for this buy (or income if it's a sell) in total.">Bought</a></th>
			    <th><a href="#" data-toggle="tooltip" title="This is the price for this buy (or income if it's a sell) in total.">Sold</a></th>
			    <th><a href="#" data-toggle="tooltip" title="The proft/loss you made in gp.">Diff</a></th>
			    <th><a href="#" data-toggle="tooltip" title="This is the item's price per each price..">Each</a></th>
			    <th><a href="#" data-toggle="tooltip" title="This is the date, when you added this flip.">Bought at</a></th>
			    <th><a href="#" data-toggle="tooltip" title="The percent you made.">Margin %</a></th>
			    <th style="text-align: center" colspan="3" ><a href="#" data-toggle="tooltip" title="Some action.">Actions</a></th>
			</tr>

			<?php
			$x = 1;
			foreach ($history as $key => $data) {
			    $each = $data['buyprice'] / $data['quantity'];
			    $editBuy = '<a href="' . base_url() . 'index.php/ui/edithistorybuy/' . $data['buyid'] . '" class="btn btn-primary btn-xs" role="button">' . $this->fliplog->insertGlyph('edit') . 'Edit</a>';
			    $deleteBuyButton = '<a href="' . base_url() . 'index.php/ui/deleteItemFromHistory/' . $data['buyid'] . '" onClick="if(!confirmDeleteHistory(\'' . $data['buyid'] . '\',\'' . $data['name'] . '\')){return false};" class="btn btn-danger btn-xs" role="button">' . $this->fliplog->insertGlyph('remove') . 'Delete</a>';
			    if ((((($data['sellprice'] / $data['buyprice'])) - 1 ) * 100 !== 0)) {
				$marginBuy = number_format(((($data['sellprice'] / $data['buyprice'])) - 1 ) * 100,2);
			    }else{
			    $marginBuy = "";
			    }
			    ?>
	    		<tr style="background-color: #e5ffe5;">
	    		    <td><?= $x ?></td>
	    		    <td><?= $this->Logmodel->showIcon($data['itemid'],$data['name']) ?></td>
	    		    <td>
	    			<span id="item"><a href="http://runescape.wikia.com/wiki/Special:Search?search=<?= $data['name'] ?>&fulltext=0"><?= $data['name'] ?></a></span>
	    		    </td>
	    		    <td><?= $this->fliplog->insertGlyph('right-arrow') . ' ' . $this->fliplog->insertGlyph('cart') ?></td>
	    		    <td><?= number_format($data['quantity']) ?></td>
	    		    <td><?= number_format($data['buyprice']) ?> gp</td>
	    		    <td><?= number_format($data['sellprice']) ?> gp</td>
	    		    <td><?= number_format($data['sellprice'] - $data['buyprice']) ?> gp</td>
	    		    <td><?= number_format($each) ?> gp</td>
	    		    <td><span data-toggle="tooltip" title="<?= date("Y-m-d H:i:s", $data['date']) ?>"><?= $this->fliplog->time_elapsed_string($data['date']) ?></span></td>
			    <td><?= $marginBuy ?></td>
	    		    <td></td>
	    		    <td><?= $editBuy ?></td>
	    		    <td><?= $deleteBuyButton ?></td>
	    		</tr>
			    <?php
			    $y = 1;
			    if (count($data['selldata']) > 1) {

				foreach ($data['selldata'] as $uid => $selldata) {
				    $editSell = '<a href="editHistorySell/' . $data['itemid'] . '/' . $data['buyid'] . '/' . $selldata['uid'] . '" class="btn btn-primary btn-xs" role="button">' . $this->fliplog->insertGlyph('edit') . 'Edit</a>';
				    $deleteSell = '<a href="deletesellFromHistory/' . $data['buyid'] . '" onClick="if(!confirmDeleteHistorySell(\'' . $selldata['uid'] . '\',\'' . $data['name'] . '\',\'' . $selldata['quantity'] . '\')){return false};" class="btn btn-danger btn-xs" role="button">' . $this->fliplog->insertGlyph('remove') . 'Delete</a>';
				    if (((($selldata['price'] / ($selldata['quantity']) * $each) - 1)) * 100 !== 0) {
					$marginSell = number_format(((($selldata['price'] / ($selldata['quantity']) * $each) - 1)) * 100 ,2);
				    } else {
					$marginSell = "";
				    }
	    ?>
		    		<tr style="background-color: #ffe5e5;"><!--Number-->
		    		    <td style="text-align: center;"><?= $y ?></td><!--Item name-->
		    		    <td><?= $this->Logmodel->showIcon($data['itemid']) ?></td><!--Icon-->
		    		    <td><span id="item"><a href="http://runescape.wikia.com/wiki/Special:Search?search=<?= $data['name'] ?>&fulltext=0"><?= $data['name'] ?></a></span></td>
		    		    <td><?= $this->fliplog->insertGlyph('cart') . ' ' . $this->fliplog->insertGlyph('right-arrow') ?></td><!--Direction-->
		    		    <td><?= number_format($selldata['quantity']) ?></td><!--Quantity-->
		    		    <td><?= number_format($selldata['quantity'] * $each) ?> gp</td><!--Bought-->
		    		    <td><?= number_format($selldata['price']) ?></td><!--Sold-->
		    		    <td><?= number_format($selldata['price'] - ($each * $selldata['quantity'])) ?> gp</td><!--Diff-->
		    		    <td></td><!--Each is empty because is the same at the buying each-->
		    		    <td><span data-toggle = "tooltip" title = "<?= date("Y-m-d H:i:s", $selldata['date']) ?>"><?= $this->fliplog->time_elapsed_string($selldata['date']) ?> </span></td>
		    		    <td><?= $marginSell ?></td><!--Margin %-->
		    		    <td></td><!--Actions-->
		    		    <td><?= $editSell ?></td><!--Actions-->
		    		    <td><?= $deleteSell ?> </td><!--Actions-->
		    		</tr>
				    <?php
				    $y++;
				}
			    }
			    $y = 1;
			    $x++;
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
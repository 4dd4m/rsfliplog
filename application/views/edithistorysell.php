<div class="col-md-8">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">
		<?php
	
		if ($this->sessiondata) {
		    echo "Edit a sell in Your history: " . $history[$buyId]['name'];
	    } else {
		    echo 'Acces forbidden';
		}
		?>
            </h3>
        </div>
        <div class="panel-body">
	    <?php
	    if ($this->sessiondata['userid']) {
		
		echo validation_errors();
		
	if (isset($history[$buyId]['selldata'])) {
	    
	    if (count($history[$buyId]['selldata']) > 1) { #if exists more than on sell exlucing this one we edit.
			echo '<div class="alert alert-warning" role="alert">
			     <strong>Warning!</strong> You already sold some items from this trade (listed below). You can max the quantity to  (the sell\'s old quantity + available quantity) in this trade. But in this case, this trade is going to be closed and moved to your history. Your sells ordered by their date, ascending. So it could be move up and down few rows if set a different date.
			     </div>';
		    }
			if ($updated == True) { #just write a confirm message if the edit was successfull
			    echo '<div class="alert alert-success" role="alert">
                    <strong>Saved!</strong> You successfully modified your trade.
                    </div>';
			}
	?>
	<form action="../../<?php echo $itemId . '/' . $buyId . '/' . $uId ?>" method="post" class="form-action form-inline">
			    <table class = "table table-hover" style="vertical-align: middle; text-align: center;">
			<tr>
			    <th style="text-align: center;" ><a href="#" data-toggle="tooltip" title="The item icon">Icon</a></th>
			    <th style="text-align: center;"><a href="#" data-toggle="tooltip" title="The item name">Item name</a></th>
			    <th style="text-align: center;"><a href="#" data-toggle="tooltip" title="You bought x of these item">Quantity</a></th>
			    <th style="text-align: center;"><a href="#" data-toggle="tooltip" title="This is the price for this buy in total.">Cost</a></th>
			    <th style="text-align: center;"><a href="#" data-toggle="tooltip" title="This is the item's price per each price..">Each</a></th>
			    <th style="text-align: center;"><a href="#" data-toggle="tooltip" title="This is the date, when you added this flip.">Bought at</a></th>
			    
			    <th style="text-align: center;" colspan="2"><a href="#" data-toggle="tooltip" title="Some action">Actions</a></th>
			</tr>
			<?php
			foreach ($history as $buyId => $buyvalue):
			
			?>
			<tr style="background-color: #e5ffe5;" style="text-align: center;">
			    <td style="text-align: center;"><?= $this->Logmodel->showIcon($buyvalue['itemid']); ?></td>
			    <td style="text-align: center;" ><span id="item"><a href="http://runescape.wikia.com/wiki/Special:Search?search=<?= $buyvalue['name']; ?>&fulltext=0"><?= $buyvalue['name']; ?></a></span></td>
			    <td style="text-align: center;"><?= $buyvalue['quantity']; ?> </td>
			    <td style="text-align: center;"><?= $buyvalue['buyprice']; ?> gp</td>
			    <td style="text-align: center;"><?= number_format($buyvalue['buyprice'] / $buyvalue['quantity']); ?> gp</td>
			    <td style="text-align: center;"><?= date("Y-m-d H:i:s", $buyvalue['date']); ?></td>
			    <td style="text-align: center;"></td>
			    <td style="text-align: center;">This is your sells parent buy.</td>
			</tr>	
			<?php
			foreach ($buyvalue['selldata'] as $sells => $sellvalue):
			    ?>
	    		<tr style="background-color: #ffe5e5;" style="text-align: center;">
	    		    <td style="text-align: center;"><?= $this->Logmodel->showIcon($buyvalue['itemid']); ?></td>
	    		    <td style="text-align: center;" ><span id="item"><a href="http://runescape.wikia.com/wiki/Special:Search?search=<?= $buyvalue['name']; ?>&fulltext=0"><?= $buyvalue['name']; ?></a></span></td>
	    		    <td style="text-align: center;">
				    <?php
				    if ($sellvalue['uid'] == $uId) { #if the sell is the sell what we need to edit. put an input form,
					?>
					<input type="number" class="form-control"
					       name="quantity"
					       value="<?= $sellvalue['quantity']; ?>"
					       min="1"
					       max="<?= $sellvalue['quantity']; ?>"
					       style="width: 125px;"
					       required/>
					       <?php
					   } else {
					       echo $sellvalue['quantity'];
					   }
					   ?>
	    		    </td>
	    		    <td style="text-align: center;">
				    <?php
				    if ($sellvalue['uid'] == $uId) { #if the sell is the sell what we need to edit. put an input form,
					?>				    
					<input type="number" class="form-control"
					       name="price"
					       required
					       value="<?= $sellvalue['price']; ?>"
					       min="1"
					       max="2147483647"
					       style="width: 125px;"/>
					       <?php
					   } else {
					       echo $sellvalue['price'];
					   }
					   ?>
	    		    </td>
	    		    <td style="text-align: center;"><?= number_format($sellvalue['price'] / $sellvalue['quantity']); ?> gp</td>
	    		    <td style="text-align: center;">

				    <?php
				    if ($sellvalue['uid'] == $uId) { #if the sell is the sell what we need to edit. put an input form,
					?>		    
					<input type="datetime-local"
					       class="form-control"
					       name="date"
					       style="width: 230px;"
					       required
					       value="<?= strftime('%Y-%m-%dT%H:%M:%S', $sellvalue['date']); ?>"/>
					       <?php
					   } else {
					       echo date("Y-m-d H:i:s",$sellvalue['date']);
					   }
					   ?>
	    		    </td>
	    		    <td style="text-align: center;" colspan="2">
				    <?php
				    if ($sellvalue['uid'] == $uId) { #if the sell is the sell what we need to edit. put an input form,
					?>			    
					<button type="submit" name="saveButton" class="btn btn-success btn-xs"><?= $this->fliplog->insertGlyph('save'); ?> Save</button>
					<?php
				    }
				    ?>



	    		    </td>
	    		</tr>

			    <?php
			    endforeach; # sells foreach
			    endforeach; # buys foreach 
			    echo '<input type="hidden" name="buyid" value="' . $buyId . '"/>';
			?>
		    </table>
    	    </form>
		    <?php
		}else{
		    echo 'Something went wrong.';
		}
		?>


    	    <p>In this view, you can only edit the item's total price and date.<br/>
    		You quantity cannot be lower than the original one.<br/>
    		Price must be a valid price but it could be lower than the orginial<br/>
    		You could date back, if you want.<br/><br/>
    		If you want to lower the orginal quantity, you have to delete all of it's subsells if it's any existing.</p>
		<?php
	    } else {
		$this->load->view('loginrequired');
	    }
	    ?>
        </div>
    </div>
</div>
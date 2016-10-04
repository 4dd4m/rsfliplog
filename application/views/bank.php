<div class="col-md-10">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">
                <?php
                if ($this->sessiondata) {
                    echo "Your bank - Buys and Sells are grouped by items";
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

    if (is_array($bank)) {
	?>
                <table class="table table-hover" style="vertical-align: middle; text-align: center; font-size: 8px !important;">
                    <tr>
                        <th style="text-align: center;"><a href="#" data-toggle="tooltip" title="Numbering of the rows">#</a></th>
                        <th style="text-align: center;"><a href="#" data-toggle="tooltip" title="Item icon">Icon</a></th>
                        <th style="text-align: center;"><a href="#" data-toggle="tooltip" title="This is the item name">Item Name</a></th>
                        <th style="text-align: center;"><a href="#" data-toggle="tooltip" title="Total amount. On Green: What You have now, On red: sold.">Bought / Sold</a></th>
                        <th style="text-align: center;"><a href="#" data-toggle="tooltip" title="Calculated Total price for that amount what You currently have.">Cost</a></th>
                        <th style="text-align: center;"><a href="#" data-toggle="tooltip" title="Actual each Price.">Price / each</a></th>
                        <th style="text-align: center;"><a href="#" data-toggle="tooltip" title="Until you reach the limit (updates on every page refresh).">Limit Remaining</a></th>
                        <th style="text-align: center;"><a href="#" data-toggle="tooltip" title="Date of the LAST bought time.">Date</a></th>
                        <th colspan="4" style="text-align: center"><a href="#" data-toggle="tooltip" title="Quick Shortcuts for Buy | Sell | Edit | Delete">Actions</a></th>
                    </tr>
			    <?php
			    $i = 1;
			    foreach ($bank as $itemid => $itemdata):
				#calculating the correct cost
				$cost = 0;
				foreach ($itemdata['buydata'] as $buykey => $buyvalue) {
				    $cost += $buyvalue['each'] * $buyvalue['available'];
				}

                        $buyform = '<form class="form-inline" action="' . base_url() . 'index.php/ui/trade" method="post" name="actionform">
                            <button type="submit" name="buymore" class="btn btn-success btn-xs">' . $this->fliplog->insertGlyph('cart') . ' Buy more</button>
                            <input type="hidden"  name="itemname" value="' . $itemdata['data']['name'] . '" />
                        </form>';
                        $sellform = ' <form class="form-inline" action="' . base_url() . 'index.php/ui/trade" method="post" name="actionform' . $i . '">
                            <input type="hidden"  name="bankitemid" value="' . $itemid . '" />
                            <input type="hidden"  name="bankitemname" value="' . $itemdata['data']['name'] . '" />
                            <input type="hidden"  name="bankquantity" value="' . $itemdata['data']['availablequantity'] . '" />
                            <input type="hidden"  name="bankprice" value="" />
                            <input type="hidden"  name="bankdate" value="' . $itemdata['data']['buydate'] . '" />
                            <button type="submit" class="btn btn-primary btn-xs" name="sellfrombank">Sell ' . $this->fliplog->insertGlyph('gbp') . '</button>
                        </form>';
                        $infoButton = '<a href="listbuys/' . $itemid . '" class="btn btn-primary btn-xs" role="button">' . $this->fliplog->insertGlyph('review') . ' Review</a>';
                        $deletebutton = '<a href="deleteitemfrombank/" onClick="if (!confirmDelete(\'' . $itemdata['data']['name'] . '\',\'' . $itemid . '\')){return false};" class="btn btn-danger btn-xs" role="button">' . $this->fliplog->insertGlyph("remove") . ' Delete</a>';
                        ?>
                        <tr style="background-color: #e5ffe5;">
                            <td><?= $i ?></td>
                            <td><?= $this->Logmodel->showIcon($itemid) ?></td>
                            <td><span id="item"><a href="http://runescape.wikia.com/wiki/Special:Search?search=<?= $itemdata['data']['name'] ?>&amp;fulltext=0"><?= $itemdata['data']['name'] ?></a></span></td>
                            <td><?= number_format($itemdata['data']['availablequantity']) ?></td>
                            <td><?= number_format($cost); ?> gp</td>
                            <td><?= number_format($itemdata['data']['each']) ?> gp</td>
                            <td><?= number_format($this->Logmodel->limitRemaining($itemid, $this->sessiondata['userid'])) ?></td>
                            <td><span data-toggle="tooltip" title="<?= date("Y-m-d H:i:s", $itemdata['data']['buydate']) ?>"><?= $this->fliplog->time_elapsed_string($itemdata['data']['buydate']) ?></span></td>
                            <td><?= $buyform ?></td>
                            <td><?= $sellform ?></td>
                            <td><?= $infoButton ?></td>
                            <td><?= $deletebutton ?></td>
                        </tr>
                        <?php
                        $i++;
                    endforeach;
		    echo '</table>';
    }
			    ?>

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
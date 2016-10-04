<div class="col-md-8">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">
                <?php
                if ($this->sessiondata) {
                    echo "Edit a specific buy";
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
                ?>
                <form action="" method="post" class="form-action form-inline">
                    <?php foreach ($buydata as $data): ?>
                        <?php
                        if ($alreadySold !== 0) {
                            echo '<div class="alert alert-warning" role="alert">
                    <strong>Warning!</strong> You already sold some items from this trade (listed below). Take care.
                    </div>';
                        }
                        if (isset($updated)) {
                            echo '<div class="alert alert-success" role="alert">
                    <strong>Saved!</strong> You successfully modified your trade.
                    </div>';
                        }
                        ?>
                        <table class = "table table-hover table-responsive" style = "vertical-align: middle; text-align: center;">
                            <tr>
                                <th style="text-align: center;" ><a href="#" data-toggle="tooltip" title="The item icon">Icon</a></th>
                                <th style="text-align: center;"><a href="#" data-toggle="tooltip" title="The item name">Item name</a></th>
                                <th style="text-align: center;"><a href="#" data-toggle="tooltip" title="You can lower the quantity to <?= $alreadySold ?> but in this case we move this trade to your history">Quantity</a></th>
                                <th style="text-align: center;"><a href="#" data-toggle="tooltip" title="You can modify it until you get a valid price (e.g. Division error)">Total Cost</a></th>
                                <th style="text-align: center;"><a href="#" data-toggle="tooltip" title="This is the item's price per each. It's not updated automatically yet.">Each</a></th>
                                <th style="text-align: center;"><a href="#" data-toggle="tooltip" title="This is the date, when you added this flip. You can set back the date but you can set forward only for the time of your first subsell.">Bought at</a></th>
                                <th style="text-align: center;"><a href="#" data-toggle="tooltip" title="Some action">Actions</a></th>
                            </tr>
                            <tr style="background-color: #e5ffe5;" style="text-align: center;">
                                <td style="text-align: center;"><?= $this->Logmodel->showIcon($data['itemid']); ?></td>
                                <td style="text-align: center;" ><span id="item"><a href="http://runescape.wikia.com/wiki/Special:Search?search=<?= $data['name']; ?>&fulltext=0"><?= $data['name']; ?></a></span></td>
                                <td style="text-align: center;"><input type="number"
                                                                       class="form-control"
                                                                       name="quantity"
                                                                       value="<?= $data['quantity']; ?>"
                                                                       min="<?= $alreadySold; ?>"
                                                                       max="2147483647"
								       required
                                                                       style="width: 125px;" /></td>
                                <td style="text-align: center;"><input type="number"
                                                                       class="form-control"
                                                                       name="price"
                                                                       value="<?= $data['price']; ?>"
                                                                       min="1"
                                                                       max="2147483647"
								       required
                                                                       style="width: 125px;"/></td>
                                <td style="text-align: center;"><?= number_format($data['price'] / $data['quantity']); ?> gp</td>
                                <td style="text-align: center;"><input type="datetime-local"
                                                                       class="form-control"
                                                                       name="date"
                                                                       style="width: 230px;"
                                                                       value="<?= strftime('%Y-%m-%dT%H:%M:%S', $data['date']); ?>"/></td>
                                <td style="text-align: center;"><button type="submit" name="saveButton" class="btn btn-success btn-xs"><?= $this->fliplog->insertGlyph('save'); ?> Save</button></td>
                            </tr>

                            <?php
                            if ($selldata !== false) {
                                foreach ($selldata as $sellid => $selldata):
                                    ?>

                                    <tr style="background-color: #ffe5e5;" style="text-align: center;">
                                        <td style="text-align: center;"><?= $this->Logmodel->showIcon($data['itemid']); ?></td>
                                        <td style="text-align: center;" ><span id="item"><a href="http://runescape.wikia.com/wiki/Special:Search?search=<?= $data['name']; ?>&fulltext=0"><?= $data['name']; ?></a></span></td>
                                        <td style="text-align: center;"><?= number_format($selldata['quantity']); ?></td>
                                        <td style="text-align: center;"><?= number_format($selldata['price']); ?> gp</td>
                                        <td style="text-align: center;"><?= number_format($selldata['price'] / $selldata['quantity']); ?> gp</td>
                                        <td style="text-align: center;"><?= date("Y-m-d H:i:s", $selldata['date']); ?></td>
                                        <td style="text-align: center;"></td>
                                    </tr>
                                    <?php
                                endforeach;
                            }
                            ?>
                        </table>
                        <?php
                        echo '<input type="hidden" name="buyid" value="' . $data['buyid'] . '"/>';
                    endforeach;
                    ?>
                </form>



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
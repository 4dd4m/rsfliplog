<div class="col-sm-10">
    <?php
    if ($this->sessiondata) {
        ?>

        <div class = "panel panel-primary">
            <div class = "panel-heading">
                <h3 class = "panel-title">Buy your items here</h3>
            </div>
            <div class = "panel-body">




                <form class = "form-horizontal" action = "<?php echo base_url(); ?>index.php/ui/trade" method = "post">
                    <?php
                    if ($this->input->post('buy') !== null) {
                        echo validation_errors();
                    }
                    if ($this->session->flashdata('buy_success')) {
                        echo $this->session->flashdata('buy_success');
                    }

                    if ($this->input->post('sellfrombank') !== null) {
                        $bankitemid = (int) $this->input->post('bankitemid');
                        $quantityfrombank = (int) $this->input->post('bankquantity');
                    } else {
                        $bankitemid = 0;
                        $quantityfrombank = 0;
                    }

                    if ($this->input->post('buymore') !== null) {
                        $item = $this->input->post('itemname');
                    } elseif ($reset) {
                        $item = "";
                    } else {
                        $item = set_value('item');
                    }
                    ?>
                    <div class="form-group">
                        <label for="search" class="col-sm-2 control-label" data-toggle="tooltip" data-palcement="top" title="Just a simple type to search and select it. The item limit displayed on the right side (if known) when you leave the input.">Item:</label>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <input type="text" name="item" autofocus value="<?php echo $item; ?>" class="form-control" id="search" placeholder="Type to search...">
                                <span style="width: 80px;" class="input-group-addon" id="limitatbuy">&nbsp;&nbsp;Limit&nbsp;&nbsp;</span>

                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="buyprice" class="col-sm-2 control-label" data-toggle="tooltip" data-palcement="top" title="The total price for an item. The GE Price button takes some time but setting up the item current price (for one)">Total price:</label>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <input type="number" min="1" max="2147483647" name="price" value="<?php echo ($reset) ? "" : set_value('price'); ?>" id="buyprice" class="form-control" placeholder="Price">
                                <span class="input-group-btn">
                                    <button style="width: 80px;" class="btn btn-default" onClick="getGePrice()" type="button">GE Price</button>
                                </span>
                            </div>

                        </div>
                    </div>

                    <div class="form-group">
                        <label for="buyquantity" data-toggle="tooltip" data-palcement="top" title="This is the amount what you want to buy. And the amount displayed on the right utnil you reach the limit. If you clicking on it, it will set it up for you. Yeah." class="col-sm-2 control-label">Quantity</label>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <input type="number" min="1" max="2147483647" name="quantity" value="<?php echo ($reset) ? "" : set_value('quantity'); ?>" id="buyquantity" class="form-control" placeholder="Quantity">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" onClick="setRemainingLimit()"  style="width: 80px;" type="button" id="limittogo" />To Go</button>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" id="buybutton" name="buy" class="btn btn-default">Trade!</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>

        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">And Sell Them Here</h3>
            </div>
            <div class="panel-body">


                <?php
                if ($this->input->post('sell') !== null) {
                    echo validation_errors();
                }
                if ($this->session->flashdata('sell_success')) {
                    echo $this->session->flashdata('sell_success');
                }
                ?>


                <form class="form-horizontal" action="<?php echo base_url(); ?>index.php/ui/trade" method="post">
                    <div class="form-group">
                        <label for="sell" data-toggle="tooltip" data-palcement="top" title="These items are in your bank. You cannot sold a non-existing item or misspelled things." class="col-sm-2 control-label">In your bank:</label>
                        <div class="col-sm-4">
                            <select class="form-control" id="sell" name="itemtosell">
                                <option value="null">Select your item...</option>
                                <?php
                                foreach ($bank as $item => $data) {
                                    if ($this->input->post('itemtosell') == $item or $bankitemid == $item) {
                                        echo '<option value="' . $item . '" selected="selected">' . $data['data']['name'] . '</option>';
                                    } else {
                                        echo '<option value="' . $item . '">' . $data['data']['name'] . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>



                    <div class="form-group">
                        <label for="sellprice" data-toggle="tooltip" data-palcement="top" title="Your total price for the item you want to sell." class="col-sm-2 control-label">Total price:</label>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <input type="number" min="1" max="2147483647" class="form-control" name="sellprice" value="<?php echo ($reset) ? "" : set_value('sellprice'); ?>"  placeholder="amount" id="sellprice">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="quantitytosell" data-toggle="tooltip" data-palcement="top" title="Quantity. On right side you have a shortcut when you want to sell them all." class="col-sm-2  control-label">Quantity</label>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <input type="number" min="1" max="2147483647"    class="form-control" id="quantitytosell" name="quantitytosell"
                                <?php
                                if ($this->input->post('sellfrombank') !== null) {
                                    echo 'value="' . $quantityfrombank . '"';
                                } else {
                                    echo ($reset) ? "" : 'value="' . set_value('quantitytosell') . '"';
                                }
                                ?>  placeholder="amount">
                                <span class="input-group-btn">
                                    <button style="width: 80px;" id="youhaveinbank" class="btn btn-default" onClick="setYouHave()" type="button">You have</button>
                                </span>







                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" id="sellbutton" name="sell" class="btn btn-default">Trade!</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
        <?php
    } else {
        echo '<div class="panel panel-primary">
                                                                <div class="panel-heading">
                                                                 <h3 class="panel-title">Acces forbidden    </h3>
                                                                 </div>
                                                                <div class="panel-body">';
        $this->load->view('loginrequired');
        echo '</div></div>';
    }
    ?>
</div>
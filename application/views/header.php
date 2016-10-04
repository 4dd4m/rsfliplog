<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" href="<?php echo base_url(); ?>css/bootstrap.min.css" />
        <!-- Optional theme -->
        <link rel="stylesheet" href="<?php echo base_url(); ?>css/bootstrap-theme.min.css" />
        <link rel="stylesheet" href="<?php echo base_url(); ?>css/custom.css">
        <script src="<?php echo base_url(); ?>js/jquery-1.10.2.js"></script>
        <script src="<?php echo base_url(); ?>js/jquery-ui.js"></script>
        <script src="<?php echo base_url(); ?>js/jquery.confirm.js"></script>
        <script src="<?php echo base_url(); ?>js/timeago.js"></script>
        <link rel="stylesheet" href="<?php echo base_url(); ?>css/jquery-ui.theme.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>css/jquery-ui.css">

        <script src="<?php echo base_url(); ?>js/bootbox.min.js"></script>
        <script src="<?php echo base_url(); ?>js/custom.js"></script>
        <!-- Latest compiled and minified CSS -->

        <!-- Latest compiled and minified JavaScript -->
        <script src="<?php echo base_url(); ?>js/bootstrap.min.js"></script>
        <link rel="shortcut icon" href="<?= base_url(); ?>/favicon.ico" type="image/x-icon" />
        <title>RsFlipLog</title>
    </head>
    <body>
        <div class="conatiner-fluid">
            <div class="row ">
                <div class="col-md-12 customheader">
                    <h1>RsFlipLog v0.1</h1>
                </div>
                <div class="menubar">
                    <ul class="nav nav-tabs nav-justified">
                        <li role="presentation" <?php echo ($this->uri->segment(2) == "" ? 'class="active"' : ""); ?>><a href="<?php echo base_url(); ?>"><?php echo $this->fliplog->insertGlyph('home');?>Home</a></li>
                        <li role="presentation" <?php echo ($this->uri->segment(2) == "trade" ? 'class="active"' : ""); ?>><a href="<?php echo base_url("index.php/ui/trade"); ?>"><?php echo $this->fliplog->insertGlyph('resize');?>Buy or Sell</a></li>
                        <li role="presentation" <?php echo ($this->uri->segment(2) == "bank" ? 'class="active"' : ""); ?>><a href="<?php echo base_url("index.php/ui/bank"); ?>"><?php echo $this->fliplog->insertGlyph('gbp');?>Your Bank</a></li>
                        <li role="presentation" <?php echo ($this->uri->segment(2) == "statistics" ? 'class="active"' : ""); ?>><a href="<?php echo base_url("index.php/ui/history"); ?>"><?php echo $this->fliplog->insertGlyph('stats');?>History</a></li>
<!--                        <li role="presentation" <?php echo ($this->uri->segment(2) == "corenews" ? 'class="active"' : ""); ?>><a href="<?php echo base_url("index.php/ui/changelog"); ?>">RsFlipLog Changelog</a></li>
                        <li role="presentation" <?php echo ($this->uri->segment(2) == "features" ? 'class="active"' : ""); ?>><a href="<?php echo base_url("index.php/ui/future"); ?>">Planned Features</a></li>
                        <li role="presentation" <?php echo ($this->uri->segment(2) == "correction" ? 'class="active"' : ""); ?>><a href="<?php echo base_url("index.php/ui/bugreport"); ?>">Item Correction</a></li>-->
                    </ul>
                </div>
            </div>
            <div class="row">
                <div class="col-md-2">
                    <?php
                    $this->load->view('leftside');
                    ?>
                </div>


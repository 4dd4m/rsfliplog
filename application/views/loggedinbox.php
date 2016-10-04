<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">Welcome <?php echo $this->sessiondata['username']; ?> !</h3>
    </div>
    <div class="panel-body centered">

        <center>

            <?php
            if ($this->sessiondata['avatar'] == true) {
                $mypath = "images/chat/" . $this->sessiondata['username'] . ".gif";

                $image_properties = array(
                    'src' => $mypath,
                    'alt' => $this->sessiondata['username'],
                    'class' => '',
                    'title' => 'Your avatar');

                echo img($image_properties);
            } else {
                echo "Chathead did not saved!";
            }






            echo form_open(base_url() . "index.php/User/logout");
            $att = array(
                "type" => "submit",
                "class" => "btn btn-success btn-xm",
                "name" => "signin",
                "value" => "Log me out!",
                "style" => "margin-top: 10px; text-align: center;"
            );
            echo form_submit($att);
            echo form_close();

            #today flipped
            $todayFlips = $this->Logmodel->todayFlipCount($this->sessiondata['userid']);
            $todayProfit = number_format($this->Logmodel->todayProfit($this->sessiondata['userid'])) . ' gp';
            ?>
        </center>
    </div>
</div>

<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">Today</h3>
    </div>
    <div class="panel-body">
        <p>Flip count: <?php echo $todayFlips; ?></p>
        <p>Total gp earned: <?php echo $todayProfit; ?></p>
        <p>Most profit on: </p>
    </div>
</div>
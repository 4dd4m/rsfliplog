<div class="col-sm-8">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">
                <?php
                if ($this->sessiondata) {
                    echo "Currently Active Buy";
                } else {
                    echo 'Acces forbidden';
                };
                ?>
            </h3>
        </div>
        <div class="panel-body">
            <?php
            if ($this->sessiondata) {

                echo $table;
                echo '<p>
                    Explanation:<br/><br/>
                    In this review, You can view your currently active trades for only one item.<br/>
                    It\'s great if You had a mistake or you just want to only review your buys. But due to edit limitations, it\'s better to<br/>
                    note down the correct values, delete the fault trade(s), and re-add them.<br/>
                    </p>';
            } else {
                $this->load->view('loginrequired');
            }
            ?>
        </div>
    </div>
</div>
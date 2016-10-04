<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">Login</h3>
    </div>
    <div class="panel-body">
        <?php
        if ($this->session->flashdata('missing')) {
            echo $this->session->flashdata('missing');
        }
        if ($this->session->flashdata('wrongpass')) {
            echo $this->session->flashdata('wrongpass');
        }
        echo form_open(base_url() . "index.php/User/");

        $att = array(
            "type" => "text",
            "class" => "form-control",
            "name" => "username",
            "placeholder" => 'username'
        );
        echo form_input($att);
        echo "<br/>";

        $att = array(
            "type" => "password",
            "class" => "form-control",
            "name" => "password",
            "placeholder" => 'password'
        );
        echo form_input($att);


        echo '<center>';
        echo '<button type="submit" class="btn btn-success btn-xm form-inline" name="signin"  style="margin-top: 10px; margin-right: 5px; text-align: center;">' . $this->fliplog->insertGlyph('ok') . 'Sign Me in!</button>';
        echo '<a href="'.base_url().'user/register/" class="btn btn-primary btn-xm" role="button" style="margin-top: 10px;">' . $this->fliplog->insertGlyph('pencil') . ' Register</a>';

        echo form_close();
        ?>
        <br/>
        <br/>
        <p><strong>Demo Login:</strong><br/>
            Username: demo<br/>
            Password: demo</p>
        </center>
    </div>
</div>
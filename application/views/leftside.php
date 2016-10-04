<?php

if ($this->sessiondata['username']) {
    $this->load->view('loggedinbox');
} else {
    $this->load->view('loginbox');
}
?>




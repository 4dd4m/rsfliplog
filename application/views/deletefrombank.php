<div class="col-sm-8">
<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title">Item deletion</h3>
		</div>
		<div class="panel-body">
		
		<div class="list-group">
		
		<a href="#" class="list-group-item">
			<h4 class="list-group-item-heading">This will remove <b>ALL</b> of these item from your bank!s</h4>
<br/>
			<p class="list-group-item-text">
                        You are going to delete the following item:<b><?php echo $this->input->post('quantity'); ?></b> x <b> <?php echo $this->input->post('itemname'); ?></b>
                        for the total price of <b><?php echo $this->input->post('price'); ?> gp</b>. Purchased on: <b><?php echo $this->input->post('date'); ?></b><br/><br/>
                        If You'd like to remove just a few, go back, and hit the 'Edit' button.<br/><br/>
                        <form class="form-inline" action="/deleteitemfrombank" method="post" id="deleteform">
                        <input type="submit"id="confirmbutton" name="delete" class="btn btn-danger btn-sm" value="Delete"/>
                        <input type="hidden" name="itemid" value="<?php echo $this->input->post('itemid'); ?>" />
                        <input type="hidden" name="itemname" value="<?php echo $this->input->post('itemname'); ?>" />
                        </form>
                        <br/>
                        <form class="form-inline" action="/bank" method="post" id="backform">
                        <button type="submit" id="back" class="btn btn-primary btn-sm">Back</button>
			</form>
			</p>
		</a>
			
		
		
		</div>
		
		
		
		
		</div>
	  </div>
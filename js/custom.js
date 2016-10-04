$(document).ready(function () {

$('[data-toggle="tooltip"]').tooltip({'placement': 'top'});

var $baseurl = 'http://localhost/projects/rsfliplog/index.php';

	$("#search").autocomplete({
		source : $baseurl + "/ajax/searchitem/",
		dataType : "json",
		minLength: 3
	});
	$("#searchtosell").autocomplete({
		source : $baseurl + "/ajax/searchinbank/",
		dataType : "json",
		minLength: 3
	});
		$("#search").focusout(function(){
			if ( $("#search").val().length >= 3){
			    $.ajax({url: $baseurl + "/ajax/itemlimit/",
						data: {"term" : $("#search").val()},
						success: function(result){
                                        $("#limitatbuy").html(Intl.NumberFormat().format(result));
					$("#limitatbuy").fadeIn("Slow");
                            }});
                        $.ajax({url: $baseurl + "/ajax/RemainingLimit/",
                            data: {"term" : $("#search").val()},
			    success: function(result){
                                        $("#limittogo").val(result);
                                        $("#limittogo").html(Intl.NumberFormat().format(result));
					$("#limittogo").fadeIn("Slow");
                            }});
			}

});
	$("#sell").change(function(){
                                        $.ajax({url: $baseurl + "/ajax/countiteminbank/" + $("#sell").val(),
                                                success: function(result){
                                        $("#youhaveinbank").html(Intl.NumberFormat().format(result));
                                        $("#youhaveinbank").val(result);
                                        $("#youhaveinbank").fadeIn("Slow");
                                        $("#quantitytosell").val("");

    }});
});

	//buyprice digits only
	$("#buyprice").keypress(function (e) {
     //if the letter is not digit then display error and don't type anything
     if (e.which !== 8 && e.which !== 0 && (e.which < 48 || e.which > 57)) {
        return false;
    }
   });
	//buyquantity digits only
	$("#buyquantity").keypress(function (e) {
     //if the letter is not digit then display error and don't type anything
     if (e.which !== 8 && e.which !== 0 && (e.which < 48 || e.which > 57)) {
        return false;
    }
   });
   	//sellprice digits only
	$("#sellprice").keypress(function (e) {
     //if the letter is not digit then display error and don't type anything
     if (e.which !== 8 && e.which !== 0 && (e.which < 48 || e.which > 57)) {
        return false;
    }
   });

   	//quantitytosell digits only
	$("#quantitytosell").keypress(function (e) {
     //if the letter is not digit then display error and don't type anything
     if (e.which !== 8 && e.which !== 0 && (e.which < 48 || e.which > 57)) {
        return false;
    }
   });
   });

   var $baseurl = 'http://rsfliplog.dupla16.hu/index.php';

      function confirmDelete(item,id){
       //confirmdeletefrombank
       bootbox.dialog({
        message: "This gonna remove ALL of these items (with the subsells as well) from your bank. If you see your item even after the deletion, it menas that you bought the item more than once and you have still available quantity",
        title: "Item delete: " + item,
        buttons: {
          success: {
            label: "Cancel",
            className: "btn-success",
            callback: function() {
              return true;
            }
          },
          danger: {
            label: "Delete",
            className: "btn-danger",
            callback: function() {
              document.location = "../ui/deleteItemFromBank/" + id;
            }
        }
        }
      });
  }

  function confirmDeleteSell(buyId,itemName){
       //Used on listbuys view
       bootbox.dialog({
        message: "This gonna remove THIS sell. These items gonna added to your bank and you can re-sell them again.",
        title: "Sell deletion: " + itemName + "( " + buyId +   ")",
        buttons: {
          success: {
            label: "Cancel",
            className: "btn-success",
            callback: function() {
              return true;
            }
          },

          danger: {
            label: "Delete",
            className: "btn-danger",
            callback: function() {
              document.location = "../deleteSell/" + buyId;
            }
        }
        }
      });
  }
  
    function confirmDeleteHistorySell(uid,itemName,quantity){
       //Used in to confirm a delete of sell in the history view.
       bootbox.dialog({
        message: "This gonna remove THIS sell. These items gonna added to your bank and you can re-sell them again. \n\
		  Sometimes (if you have many sells or you bought  large quantity of this item) your bank could be dramtically changed. But keep in mind, \n\
		  after the deletion you'd sell:<br/><br/><strong>" + quantity + "</strong> pieces of <strong>" + itemName + "</strong><br/><br/> \n\
		  You should do that after the deletetion and you avoid to get confused.",
        title: "Sell deletion: " + itemName,
        buttons: {
          success: {
            label: "Cancel",
            className: "btn-success",
            callback: function() {
              return true;
            }
          },

          danger: {
            label: "Delete",
            className: "btn-danger",
            callback: function() {
              document.location = "deleteSellFromHistory/" + uid;
            }
        }
        }
      });
  }
  
  
        function deleteSingleBuy(buyId,itemName){
       //Used on listbuys view
       bootbox.dialog({
        message: "This gonna remove THIS buy. And ALL of those sells what are involved in this trade.",
        title: "Single Buy deletion: " + itemName,
        buttons: {
          success: {
            label: "Cancel",
            className: "btn-success",
            callback: function() {
              return true;
            }
          },
          danger: {
            label: "Delete",
            className: "btn-danger",
            callback: function() {
              document.location = "../deleteBuy/" + buyId;
            }
        }
        }
      });
  }
  
  function deleteSubSellFromHistory(buyId){
       //Used on listbuys view
       bootbox.dialog({
        message: "This gonna remove THIS sub sell from your history. This trade gets back to your bank (with another subsell if it's exists any).",
        title: "SubSell deletion from your history",
        buttons: {
          success: {
            label: "Cancel",
            className: "btn-success",
            callback: function() {
              return true;
            }
          },
          danger: {
            label: "Delete",
            className: "btn-danger",
            callback: function() {
              document.location = "../ui/deleteSubSellFromHistory/" + buyId;
            }
        }
        }
      });
  }
       function confirmDeleteHistory(id,name){
                //confirmdeletefromhistory
       bootbox.dialog({
        message: "This gonna remove THIS trade from your history",
        title: "Item delete: " + name,
        buttons: {
          success: {
            label: "Cancel",
            className: "btn-success",
            callback: function() {
              return true;
            }
          },
          danger: {
            label: "Delete",
            className: "btn-danger",
            callback: function() {
              document.location = "deleteItemFromHistory/" + id;
            }
        }
        }
      });
  }
 

    function setYouHave(){
        //it sets the quantitytosell with the value you have
      $("#quantitytosell").val($("#youhaveinbank").val());
  }

  function setRemainingLimit(){
      $("#buyquantity").val($("#limittogo").val());
  }

  function setBuyQuantityMax(togo){
      $("#buyquantity").val(togo);
  }

  function getGePrice(){
    $.ajax({url: $baseurl + "/api/oneItemFetch/?term=" + escape($("#search").val()),success: function(result){
                                                                                       $("#buyprice").val(result);
			    }});

}


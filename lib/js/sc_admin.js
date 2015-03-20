(function ($) {

	function resetIndex() {
		$('.sce tbody').find('tr.sce_tr').each(function(i) {
		  $(this).find('input:hidden').val(i);
		  //console.log(this);
		});
	 }

	function makeSortable() {
		$('.sce tbody').sortable({
		  opacity: 0.6,
		  stop: function() {
			resetIndex();
		  }
		});
	 }

	var chars = "abcdefghijklmnopqrstuvwxyz";

	$(document).on('click', 'a.addmarkdown_box', function(e) {
		
		var that = $(this);
		var id = that.attr('id');
		var scetype = that.data('scetype');
		var rnd_strg = "_";
		for( var i=0; i < 7; i++ ){
			rnd_strg += chars.charAt(Math.floor(Math.random() * chars.length));
		}
		e.preventDefault();
		var numItems = $('tr.sce_tr').length
		var parent = that.closest('tr');

		var build = '<tr class="sce_tr"><td class="sce_card"><textarea class="multibox sce_card-body" id="newmb'+rnd_strg+'" name="'+id+'_sceeditor'+rnd_strg+'" autoresize></textarea></td>';
		build+= '<input class="sce_multibox order" type="hidden" name="'+id+'_order'+rnd_strg+'" value="'+numItems+'"></tr>';
		build+= '<input class="sce_multibox type" type="hidden" name="'+id+'_type'+rnd_strg+'" value="'+scetype+'"></tr>';

		parent.before(build);

		console.log(that.closest('textarea'));
	  $('#newmb'+rnd_strg).focus();
	});

	// ajax to del meta data
	$(document).on('click', '.delmulti_box', function(event) {
		event.preventDefault();
		var check = confirm("Are you sure you want to remove this?\nThis action cannot be undone.");
		var meta = $(this).attr('id');
		var tr = $(this).parents('tr');
		var postID = tr.data('pid');
		if(check){
			ajaxDeleteMeta(meta,tr,postID);
		}else{
			return false;
		}
		//console.log(meta);
	});
	
	// using wp admin ajax api
	function ajaxDeleteMeta(meta, tr, postID){
			
		jQuery.ajax({
		   type: "POST",
		   url: ajaxurl,
		   data : { action : 'delmeta', delmeta : meta, postID : postID }, 
		   success: function(msg){
			 tr.fadeOut(300, function() { $(this).remove(); });
		   }
		
		});
	
	}

	$('#publish').on('click',function(e){
		if($('#title').val()===''){
			e.preventDefault();
			alert('A title is required to publish.');
			$('#title').focus();
		}
	})

	makeSortable();
			
})(jQuery);


(function($){

// Dynamically Resize Textareas
// ==============================

// Concept ruthlessly borrowed from:
// http://maximilianhoffmann.com/posts/autoresizing-textareas

	// Find textareas with the `autoresize` attribute
	var textareas = $('textarea[autoresize]');

	// Listen to input and call
	textareas.each(function(i){
	  this.style.height = (this.scrollHeight+5)+'px';   // match existing content on ready
	  $(this).on('input blur', autoresize); 						// run on listeners
	});

	function autoresize() {
	  this.style.height = 'auto';
	  this.style.height = this.scrollHeight+'px';
	  this.scrollTop = this.scrollHeight;

		// In case it's getting longer than the window
	  window.scrollTo(window.scrollLeft,(this.scrollTop+this.scrollHeight));
	}

})(jQuery);


(function($){

// Restore Tab Key for Textareas
// ===============================

	HTMLTextAreaElement.prototype.getCaretPosition = function() { //return the caret position of the textarea
    return this.selectionStart;
	};

	HTMLTextAreaElement.prototype.setCaretPosition = function(position) { //change the caret position of the textarea
	    this.selectionStart = position;
	    this.selectionEnd = position;
	    this.focus();
	};

	var textarea = $('.sce textarea')[0];

	textarea.onkeydown = function(event) {
    
    //support tab on textarea
    if (event.keyCode == 9) { //tab was pressed
      var newCaretPosition;
      newCaretPosition = textarea.getCaretPosition() + "    ".length;
      textarea.value = textarea.value.substring(0, textarea.getCaretPosition()) + "    " + textarea.value.substring(textarea.getCaretPosition(), textarea.value.length);
      textarea.setCaretPosition(newCaretPosition);
      return false;
    }

	}

})(jQuery);
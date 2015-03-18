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
	var ft = that.attr('data-filetype');
	var rnd_strg = "_";
	for( var i=0; i < 7; i++ ){
	rnd_strg += chars.charAt(Math.floor(Math.random() * chars.length));
	}
	e.preventDefault();
	var numItems = $('.sce_markdown_boxes').length
	var parent = that.closest('tr');
	var build = '<tr><td><textarea class="multibox" id="newmb'+rnd_strg+'" name="'+id+'_sceeditor'+rnd_strg+'"></textarea></td></tr>';
	build+= '<input class="multibox order" type="hidden" name="'+id+'_order'+rnd_strg+'" value="'+numItems+'">';

	parent.before(build);																			
	});

	// ajax to del meta data
	$(document).on('click', '.delmulti_box', function(event) {
		event.preventDefault();
		var meta = $(this).attr('id');
		var tr = $(this).parents('tr');
		var postID = tr.data('pid');
		ajaxDeleteMeta(meta,tr,postID);
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

	makeSortable();
			
})(jQuery);
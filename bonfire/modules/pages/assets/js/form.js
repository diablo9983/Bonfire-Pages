(function($) {
	$(function(){

		// Generate a slug from the title
		bonfire.generate_slug('input[name="title"]', 'input[name="slug"][disabled!="disabled"]');

		// needed so that Keywords can return empty JSON
		$.ajaxSetup({
			allowEmpty: true
		});
        
        /*
		$('#meta_keywords').tagsInput({
			autocomplete_url:'admin/content/keywords/autocomplete'
		});*/
		
		// add another page chunk
		$('a.add-chunk').live('click', function(e){
			e.preventDefault();

			// The date in hexdec
			key = Number(new Date()).toString(16).substr(-5, 5);

			$('#chunks').append('<div id="' + key + '" class="control-group page-chunk" style="display:none;">' +
                '<div class="controls">' +
				'<input type="text" name="chunk_slug[' + key + ']" value="' + key + '"/> ' +
				'<select name="chunk_type[' + key + ']">' +
				'<option value="html">html</option>' +
				'<option value="wysiwyg">wysiwyg</option>' +
				'</select>' +
				'<div class="fright pr20">' +
				'<a href="javascript:void(0)" class="remove-chunk btn btn-danger"><i class="icon-remove icon-white"></i> ' + bonfire.lang.delete + '</a>' +
                '</div><br style="clear:both" /><br />' +
				'<span class="chunky"><textarea id="' + key + '" class="pages wysiwyg" rows="12" style="width:90%" name="chunk_body[' + key + ']"></textarea>' +
				'</span></div></div>');
                
            $('a.remove-chunk').show('slow',function(){
                $(this).fadeTo(500,1);    
            });
            $('#' + key).slideDown('slow');
			// initialize the editor using the view from fragments/wysiwyg.php
			//bonfire.init_ckeditor();
            
			// Update Chosen
			//bonfire.chosen();
		});

		$('a.remove-chunk').live('click', function(e) {
			e.preventDefault();
            
            if ($('#chunks .page-chunk').length > 1) {
    			var removemsg = $(this).attr('title');
            
    			if (confirm(removemsg || bonfire.lang.dialog_message))
    			{
    				$(this).closest('div.page-chunk').slideUp('slow', function(){ 
    				    $(this).remove();
                        if($('#chunks .page-chunk').length == 1) {
                           $('a.remove-chunk').fadeTo(500,0.01,function(){
                                $(this).hide();
                            }); 
                        } 
                    });	
    			}
			} else {
                alert(bonfire.lang.alert_one_chunk);
			}
		});

		$('select[name^=chunk_type]').live('change', function() {
			chunk = $(this).closest('div.page-chunk');
			textarea = $('textarea', chunk);

			// Destroy existing WYSIWYG instance
			if (textarea.hasClass('wysiwyg'))
			{
				textarea.removeClass('wysiwyg');

				var instance = CKEDITOR.instances[textarea.attr('id')];
				instance && instance.destroy();
			}

			// Set up the new instance
			textarea.addClass(this.value);

			bonfire.init_ckeditor();
		});

	});

})(jQuery);
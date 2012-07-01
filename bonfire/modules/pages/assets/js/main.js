
/*
 Prevent elements classed with "no-link" from linking
*/
//$(".no-link").click(function(e){ e.preventDefault();	});

if (typeof(bonfire) == 'undefined') {
	var bonfire = {};
}

jQuery(function($) {
    
   	$.ajaxSetup({
		//allowEmpty: true,
		converters: {
			'text json': function(text) {
				var json = jQuery.parseJSON(text);
				if (!jQuery.ajaxSettings.allowEmpty == true && (json == null || jQuery.isEmptyObject(json)))
				{
					jQuery.error('The server is not responding correctly, please try again later.');
				}
				return json;
			}
		},
		data: {
			csrf_hash_name: $.cookie(bonfire.csrf_cookie_name)
		}
	});
    
    bonfire.init = function()
    {
        // Confirmation
		$('a.confirm').live('click', function(e){
			e.preventDefault();

			var href		= $(this).attr('href'),
				removemsg	= $(this).attr('title');

			if (confirm(removemsg || bonfire.lang.dialog_message))
			{
				$(this).trigger('click-confirmed');

				if ($.data(this, 'stop-click')){
					$.data(this, 'stop-click', false);
					return;
				}
				window.location.replace(href);
			}
		});
    }
    
    
    // Used by Pages and Navigation and is available for third-party add-ons.
	// Module must load jquery/jquery.ui.nestedSortable.js and jquery/jquery.cooki.js
	bonfire.sort_tree = function($item_list, $url, $cookie, data_callback, post_sort_callback)
	{
		// collapse all ordered lists but the top level
		$item_list.find('ul').children().hide();

		// this gets ran again after drop
		var refresh_tree = function() {

			// add the minus icon to all parent items that now have visible children
			$item_list.parent().find('ul li:has(li:visible)').removeClass().addClass('minus');

			// add the plus icon to all parent items with hidden children
			$item_list.parent().find('ul li:has(li:hidden)').removeClass().addClass('plus');

			// remove the class if the child was removed
			$item_list.parent().find('ul li:not(:has(ul))').removeClass();

			// call the post sort callback
			post_sort_callback && post_sort_callback();
		}
		refresh_tree();

		// set the icons properly on parents restored from cookie
		$($.cookie($cookie)).has('ul').toggleClass('minus plus');

		// show the parents that were open on last visit
		$($.cookie($cookie)).children('ul').children().show();

		// show/hide the children when clicking on an <li>
		$item_list.find('li').live('click', function()
		{
			$(this).children('ul').children().slideToggle('fast');

			$(this).has('ul').toggleClass('minus plus');

			var items = [];

			// get all of the open parents
			$item_list.find('li.minus:visible').each(function(){ items.push('#' + this.id) });

			// save open parents in the cookie
			$.cookie($cookie, items.join(', '), { expires: 1 });

			 return false;
		});

		$item_list.nestedSortable({
			delay: 100,
			disableNesting: 'no-nest',
			forcePlaceholderSize: true,
			handle: 'div',
			helper:	'clone',
			items: 'li',
			opacity: .4,
			placeholder: 'placeholder',
			tabSize: 25,
			listType: 'ul',
			tolerance: 'pointer',
			toleranceElement: '> div',
			stop: function(event, ui) {

				post = {};
				// create the array using the toHierarchy method
				post.order = $item_list.nestedSortable('toHierarchy');

				// pass to third-party devs and let them return data to send along
				if (data_callback) {
					post.data = data_callback(event, ui);
				}

				// refresh the tree icons - needs a timeout to allow nestedSort
				// to remove unused elements before we check for their existence
				setTimeout(refresh_tree, 5);

				$.post(SITE_URL + $url, post );
			}
		});
    }
    
    // Create a clean slug from whatever garbage is in the title field
	bonfire.generate_slug = function(input_form, output_form, space_character)
	{
		var slug, value;

		$(input_form).live('keyup', function(){
			value = $(input_form).val();

			if ( ! value.length ) return;
			
			space_character = space_character || '-';
			var rx = /[a-z]|[A-Z]|[0-9]|[áàâąбćčцдđďéèêëęěфгѓíîïийкłлмñńňóôóпúùûůřšśťтвýыžżźзäæœчöøüшщßåяюжαβγδεέζηήθιίϊκλμνξοόπρστυύϋφχψωώ]/,
				value = value.toLowerCase(),
				chars = bonfire.foreign_characters,
				space_regex = new RegExp('[' + space_character + ']+','g'),
				space_regex_trim = new RegExp('^[' + space_character + ']+|[' + space_character + ']+$','g'),
				search, replace;
			

			// If already a slug then no need to process any further
		    if (!rx.test(value)) {
		        slug = value;
		    } else {
		        value = $.trim(value);

		        for (var i = chars.length - 1; i >= 0; i--) {
		        	// Remove backslash from string
		        	search = chars[i].search.replace(new RegExp('/', 'g'), '');
		        	replace = chars[i].replace;

		        	// create regex from string and replace with normal string
		        	value = value.replace(new RegExp(search, 'g'), replace);
		        };

		        slug = value.replace(/[^-a-z0-9~\s\.:;+=_]/g, '')
		        			.replace(/[\s\.:;=+]+/g, space_character)
		        			.replace(space_regex, space_character)
		        			.replace(space_regex_trim, '');
		    }

			$(output_form).val(slug);
		});
	}
    
    $(document).ready(function() {
		bonfire.init();
		//pyro.chosen();
	});        
});
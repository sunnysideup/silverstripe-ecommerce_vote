/**
 *@author Nicolaas [at] sunnysideup.co.nz
 *@description: makes the add to wish list links ajax.
 **/

;(function($) {
	$(document).ready(
		function() {
			EcommerceVote.init(".addToEcommerceVote");
		}
	);
})(jQuery);

var EcommerceVote = {

	addLinkSelector: ".addEcommerceVote",

	init: function(element) {
		jQuery(element).addEcommerceVoteAddLinks();
	},


	loadAjax: function( url, el ) {
		var clickedElement = el;
		jQuery.get(
			url,
			{},
			function(data) {
				jQuery(el).text(data);
			}
		);
		return true;
	}




}


jQuery.fn.extend({

	addEcommerceVoteAddLinks: function() {
		jQuery(this).find(EcommerceVote.addLinkSelector).click(
			function(){
				var url = jQuery(this).attr("href");
				EcommerceVote.loadAjax(url, this);
				return false;
			}
		);
	},

});


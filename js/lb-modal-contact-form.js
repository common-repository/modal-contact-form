

jQuery(function($) {
        var $wrapper = $('<div class="lb-modal-contact-form-antispam"><div class="lb-modal-contact-form-antispam-info"><label>'+lb_modal_contact_form_params.antispam_message+'</label></div><div class="lb-modal-contact-form-antispam-slider"></div></div>');
		var $submit = $('<div class="lb-modal-contact-form-submit"><p class="submit"><input class="lb-modal-contact-form-send" class="lb-modal-contact-form-btn" type="submit" name="submit" value="'+lb_modal_contact_form_params.send_button+'" /></p></div>');
		
		$('.lb-modal-contact-form').append($wrapper);
		
		$('.lb-modal-contact-form-antispam-slider').slider({
				range: "min",
				min:0,
				max:99,
				change: function(event, ui) {
                if(ui.value > 90) {
					$('.lb-modal-contact-form').append($submit);
					$('.lb-modal-contact-form-antispam').remove();
                }
            }
        });
	
});

// This file contains code used for admin page event listeners

// Event listeners for admin overlay triggers
$('.admin_overlay_trigger').on({
    // If an overlay trigger is clicked
    click : function() {
        // Trigger a popup (its nature is defined by the elements 'trigger type')
        triggerPopup($(this).data('trigger-type'));

        // Slider values for the add event form user timing range slider
        $('#add_event_form #new_event_timing').on({
            input : function() {
                sliderVal = $(this).val();
                // If the value is 0 display 'on time'
                if (sliderVal == 0) {
                    $(this).next('output').val('On Time');
                }
                // If the vlaue is greater than zero output different message
                else if (sliderVal > 0) {
                    $(this).next('output').val(sliderVal+' Minutes Late/Early');
                }
                // Otherwise just return the value like normal
                else {
                    $(this).next('output').val(sliderVal);
                }
            }
        });
        // Event listener for the 'add event form' radio inputs, to toggle the location input
        $('#add_event_form .radio_wrapper').click(function() {
            // If the sign in trigger is checked, hide the location input and clear it
            if ($('#event_in_trigger').is(':checked')) {
                $(this).parent().find('.event_location_wrapper').css('display', 'none');
                $(this).parent().find('.event_location_wrapper #location_field').val('');
            }
            // If the sigh out trigger is checked, show the location input
            else if ($('#event_out_trigger').is(':checked')) {
                $(this).parent().find('.event_location_wrapper').css('display', 'block');
            }
            // Otherwise, show the location input
            else {
                $(this).parent().find('.event_location_wrapper').css('display', 'block');                
            }
        })
    }
});

// This file contains code used for admin page event listeners

// Event listeners for admin overlay triggers
$('.admin_overlay_trigger').on({
    // If an overlay trigger is clicked
    click : function() {
        // Trigger a popup (its nature is defined by the elements 'trigger type')
        triggerPopup($(this).data('trigger-type'));
        // Detects whether a value is selected
        $('input[type=range][name=event_timing]').on({
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
    }
});

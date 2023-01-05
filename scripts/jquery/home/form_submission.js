// This file contains thee javascript code used to submit forms lcoated on the home page

// Variables
// An object used to contain the IDs of forms and their action paths
formActionPaths = {
    '#user_self_sign_out_form' : '../../pages/php/forms/sign_user_out.php',
    '#user_self_sign_in_form' : '../../pages/php/forms/sign_user_in.php'
}

// Main
$(document).ready(function() {
    
    // Adds the event listeners required for each form to function
    for (const formID in formActionPaths) {

        // Event listener for when text is input into the sign in/out form fields
        $(formID).keydown(function(e) {

            // Stores the submit button as a variable
            const submitButton = $(this).find('button:last-of-type');

            // If the enter key was not pressed and  the form is not loading
            if (e.which != 13 && !$(submitButton).hasClass('loading')) {

                // Reset the submit button
                $(submitButton).css('background', 'var(--navy)');
                $(submitButton).html('<h4>Submit</h4>');
                
            }
        });

        // for each form in the formActionPaths object
        $(formID).submit(function(e) {

            e.preventDefault(); // Do not execute the actual form submit

            // Indexes the object that stores all the action file paths with the forms ID
            var actionPath = formActionPaths[formID];
            // Stores the submit button as a variable
            const submitButton = $(this).find('button:last-of-type');

            // Ajax used to submit and process the form
            $.ajax({
                type: "POST",
                url: actionPath,
                timeout: 5000,
                async: true,
                data: $(this).serialize() + "&staff_action=0", // Serializes the form's elements (and states that this was not a staff action)
                dataType: 'json',
                beforeSend: function() {
            
                    // Animates the buttons 'loading' state
                    $(submitButton).empty();
                    $(submitButton).addClass('loading');
        
                }
            }).always(function() {
        
                // Removes the spinner from the submit button
                $(submitButton).css('display', 'block');
                $(submitButton).removeClass('loading');

            }).done(function(message) {
        
                $(submitButton).html('<h4>Submit</h4>');
                // Outputs a success notification
                // Sign out
                notification(message, 'success', 3000);
                // Proceeds to reset the forms, ready for the next user
                // This can be 'cheated' by using the back button function which does this for us
                backButton();


            }).fail(function(jqXHR, status, error) {
        
                // Changes the buttons state to failure
                $(submitButton).css('background', 'var(--red)');
                $(submitButton).html('<h4>Failure</h4>');
        
                // Displays an error message
                notification(error, 'error', 4000);
        
            }); // End of Ajax
            
        }); // End of 'on submit'

    } // End of for loop

    // Event listener for when text is input into the staff login forms fields
    $('#staff_login_form').keydown(function(e) {

        // Stores the submit button as a variable
        const submitButton = $(this).find('button:last-of-type');

        // If the enter key was not pressed and the form is not loading
        if (e.which != 13 && !$(submitButton).hasClass('loading')) {

            // Reset the submit button
            $(submitButton).css('background', 'var(--navy)');
            $(submitButton).html('<h4>Submit</h4>');
            
        }
    });
    
    // When the staff login form is submitted
    $('#staff_login_form').submit(function(e) {

        e.preventDefault(); // Do not execute the actual form submit

        // Stores the PHP file path
        var actionPath = '../../pages/php/forms/staff_login.php';
        // Stores the submit button as a variable
        const submitButton = $(this).find('button:last-of-type');

        // Ajax used to submit and process the form
        $.ajax({
            type: "POST",
            url: actionPath,
            timeout: 5000,
            async: true,
            data: $(this).serialize(), // Serializes the form's elements
            dataType: 'html',
            beforeSend: function() {
        
                // Animates the buttons 'loading' state
                $(submitButton).empty();
                $(submitButton).addClass('loading');
    
            }
        }).always(function() {
    
            // Removes the spinner from the submit button
            $(submitButton).css('display', 'block');
            $(submitButton).removeClass('loading');

        }).done(function(success) {
    
            // Resets the submit button
            $(submitButton).html('<h4>Submit</h4>');

            // Redirects the user to the staff page (after storing login cookies with the PHP)
            window.location.href = "../../pages/php/staff_page.php";

        }).fail(function(jqXHR, status, error) {
    
            // Changes the buttons state to failure
            $(submitButton).css('background', 'var(--red)');
            $(submitButton).html('<h4>Failure</h4>');
    
            // Displays an error message
            notification(error, 'error', 4000);
            console.log(error);
    
        }); // End of Ajax
        
    }); // End of 'on submit'
    
});
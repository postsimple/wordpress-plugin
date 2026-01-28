/**
 * PostSimple WordPress Integration - Admin JavaScript
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        const sendButton = $('#postsimple-send-button');
        const loadingDiv = $('#postsimple-loading');
        const statusMessage = $('#postsimple-status-message');
        const errorMessage = $('#postsimple-error-message');
        
        if (!sendButton.length) {
            return;
        }
        
        sendButton.on('click', function(e) {
            e.preventDefault();
            
            const postId = $(this).data('post-id');
            
            // Hide previous messages
            statusMessage.hide();
            errorMessage.hide();
            
            // Show loading state
            sendButton.prop('disabled', true);
            loadingDiv.show();
            
            // Make AJAX request
            $.ajax({
                url: postsimpleData.ajaxurl,
                type: 'POST',
                data: {
                    action: 'postsimple_send_post',
                    post_id: postId,
                    nonce: postsimpleData.nonce
                },
                success: function(response) {
                    loadingDiv.hide();

                    if (response.success) {
                        // Show success message
                        statusMessage.html(
                            '<strong>✓ Verzonden naar PostSimple</strong>'
                        ).show();

                        // Hide form, show view link
                        $('#postsimple-form').hide();
                        $('#postsimple-view-link').attr('href', response.data.redirect_url);
                        $('#postsimple-success-link').show();
                    } else {
                        // Show error message
                        errorMessage.html(
                            '<strong>Fout:</strong> ' + response.data.message
                        ).show();
                        sendButton.prop('disabled', false);
                    }
                },
                error: function(xhr, status, error) {
                    loadingDiv.hide();
                    errorMessage.html(
                        '<strong>Fout:</strong> Er is een onverwachte fout opgetreden. Probeer het opnieuw.'
                    ).show();
                    sendButton.prop('disabled', false);
                }
            });
        });
    });
    
})(jQuery);

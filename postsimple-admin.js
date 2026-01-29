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
                        // Replace entire meta box content with success state
                        $('#postsimple-meta-box-content').html(
                            '<div style="padding: 10px; margin: 10px 0; border-left: 4px solid #00a32a; background: #f0f6fc;">' +
                                '<strong>✓ Gelukt!</strong>' +
                                '<p style="margin: 5px 0 0 0;">' + response.data.message + '</p>' +
                            '</div>' +
                            '<a href="' + response.data.redirect_url + '" target="_blank" ' +
                                'class="button button-primary button-large" ' +
                                'style="width: 100%; text-align: center; margin-top: 10px;">' +
                                '<span class="dashicons dashicons-external" style="margin-top: 3px;"></span> ' +
                                'Bekijk op PostSimple' +
                            '</a>'
                        );
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

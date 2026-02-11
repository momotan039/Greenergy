jQuery(document).ready(function($){
    var mediaUploader;

    $('.greenergy-media-upload-button').click(function(e) {
        e.preventDefault();
        
        var button = $(this);
        var inputField = button.prev('input');
        var preview = button.siblings('.greenergy-media-preview');

        // If the uploader object has already been created, reopen the dialog
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }

        // Extend the wp.media object
        mediaUploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image/Video',
            button: {
                text: 'Choose Image/Video'
            }, multiple: false
        });

        // When a file is selected, grab the URL and set it as the text field's value
        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            inputField.val(attachment.url);
            
            // Update preview if it's an image
            if(preview.length && attachment.type === 'image') {
                preview.attr('src', attachment.url).show();
            }
        });

        // Open the uploader dialog
        mediaUploader.open();
    });
});

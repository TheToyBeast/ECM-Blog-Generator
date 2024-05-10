jQuery(document).ready(function ($) {
    function updateGenerateButtonText() {
        var contentGenerated = parseInt($('#chatgpt_content_generated').val());
        var buttonText = contentGenerated ? 'Generate Content' : 'Generate Intro';
        $('#chatgpt_generate_button').text(buttonText);
    }
    function showConclusionButton() {
        var button = ' <button type="button" id="chatgpt_generate_conclusion_button">Generate Conclusion</button>';
        $('#chatgpt_generate_button').after(button);
    }
    updateGenerateButtonText();
    $('#chatgpt_generate_button').click(function () {
        var niche = $('#chatgpt_niche').val();
        var topic = $('#chatgpt_topic').val();
        var keyword = $('#chatgpt_keyword').val();
        var existingContent = '';
        if (typeof tinyMCE !== 'undefined' && tinyMCE.get('content')) {
            existingContent = tinyMCE.get('content').getContent();
            var ed = tinyMCE.get('content');
            ed.selection.select(ed.getBody(), true);
            ed.selection.collapse(false);
        } else {
            existingContent = $('#content').val();
            var el = $('#content')[0];
            el.focus();
            el.setSelectionRange(el.value.length, el.value.length);
        }
        var data = {
            action: 'chatgpt_generate_content',
            post_id: $('#post_ID').val(),
            niche: niche,
            topic: topic,
            keyword: keyword,
            existing_content: existingContent
        };
        $('#chatgpt_status').text('Generating content...please be patient');
        $.post(ajaxurl, data, function (response) {
            if (response.success) {
                if (typeof tinyMCE !== 'undefined' && tinyMCE.get('content')) {
                    tinyMCE.get('content').insertContent(response.data.content);
                } else {
                    $('#content').val($('#content').val() + response.data.content);
                }
                $('#chatgpt_status').text('Content generated successfully!');
                // Update content generation status
                $('#chatgpt_content_generated').val('1');
                updateGenerateButtonText();
                // Show the Generate Conclusion button
                if (!$('#chatgpt_generate_conclusion_button').length) {
                    showConclusionButton();
                }
            } else {
                $('#chatgpt_status').text('Error: ' + response.data.message);
            }
        });
    });
    $(document).on('click', '#chatgpt_generate_conclusion_button', function () {
        var niche = $('#chatgpt_niche').val();
        var topic = $('#chatgpt_topic').val();
        var keyword = $('#chatgpt_keyword').val();
        var existingContent = '';
        if (typeof tinyMCE !== 'undefined' && tinyMCE.get('content')) {
            existingContent = tinyMCE.get('content').getContent();
            var ed = tinyMCE.get('content');
            ed.selection.select(ed.getBody(), true);
            ed.selection.collapse(false);
        } else {
            existingContent = $('#content').val();
            var el = $('#content')[0];
            el.focus();
            el.setSelectionRange(el.value.length, el.value.length);
        }
        var data = {
            action: 'chatgpt_generate_content',
            niche: niche,
            topic: topic,
            keyword: keyword,
            existing_content: existingContent,
            conclusion: 1
        };
        $('#chatgpt_status').text('Generating conclusion...please be patient');
        $.post(ajaxurl, data, function (response) {
            if (response.success) {
                if (typeof tinyMCE !== 'undefined' && tinyMCE.get('content')) {
                    tinyMCE.get('content').insertContent(response.data.content);
                } else {
                    $('#content').val($('#content').val() + response.data.content);
                }
                $('#chatgpt_status').text('Content generated successfully!');
                // Update content generation status
                $('#chatgpt_content_generated').val('1');
                $('#chatgpt_generate_button, #chatgpt_generate_conclusion_button').prop('disabled', true);
            } else {
                $('#chatgpt_status').text('Error: ' + response.data.message);
            }
        });
    });
    $('#chatgpt_generate_promt_button').click(function () {
        var custom_prompt = $('#chatgpt_prompt').val();
        var existingContent = '';
        if (typeof tinyMCE !== 'undefined' && tinyMCE.get('content')) {
            existingContent = tinyMCE.get('content').getContent();
            var ed = tinyMCE.get('content');
            ed.selection.select(ed.getBody(), true);
            ed.selection.collapse(false);
        } else {
            existingContent = $('#content').val();
            var el = $('#content')[0];
            el.focus();
            el.setSelectionRange(el.value.length, el.value.length);
        }
        var data = {
            action: 'chatgpt_generate_content',
            post_id: $('#post_ID').val(),
            custom_prompt: custom_prompt,
            existing_content: existingContent
        };
        $('#chatgpt_status').text('Generating prompt content...please be patient');
        $.post(ajaxurl, data, function (response) {
            if (response.success) {
                if (typeof tinyMCE !== 'undefined' && tinyMCE.get('content')) {
                    tinyMCE.get('content').insertContent(response.data.content);
                } else {
                    $('#content').val($('#content').val() + response.data.content);
                }
                $('#chatgpt_status').text('Content generated successfully!');
                // Update content generation status
                $('#chatgpt_content_generated').val('1');
                updateGenerateButtonText();
                // Show the Generate Conclusion button
                if (!$('#chatgpt_generate_conclusion_button').length) {
                    showConclusionButton();
                }
            } else {
                $('#chatgpt_status').text('Error: ' + response.data.message);
            }
        });
    });
    $('#chatgpt_rewrite_button').click(function () {
        var inputText = $('#chatgpt_input_text').val();
        var writingStyle = $('#chatgpt_writing_style').val();
        var data = {
            action: 'chatgpt_rewrite_content',
            input_text: inputText,
            style: writingStyle
        };
        $('#chatgpt_rewrite_status').text('Rewriting content...please be patient');
        $.post(ajaxurl, data).done(function (response) {
            if (response.success) {
                var rewrittenText = response.data.content;
                if (rewrittenText) {
                    $('#chatgpt_rewrite_status').text('Finished');
                    $('#chatgpt_rewrite_result').html(rewrittenText);
                    $('#chatgpt_copy_button').show();
                    $('#chatgpt_reset_button').show();
                } else {
                    $('#chatgpt_rewrite_status').text('Error: No rewritten text received from the API.');
                    $('#chatgpt_rewrite_result').html('');
                    $('#chatgpt_copy_button').hide();
                    $('#chatgpt_reset_button').hide();
                }
            }
        }).fail(function () {
            $('#chatgpt_rewrite_result').html('Error: Unable to rewrite text.');
        });
        $('#chatgpt_copy_button').click(function () {
            // Create a temporary textarea element to hold the text for copying
            var tempTextarea = $('<textarea>');
            $('body').append(tempTextarea);
            tempTextarea.val($('#chatgpt_rewrite_result').text()).select();
            document.execCommand('copy');
            tempTextarea.remove();
            // Show a message to indicate the text has been copied
            $('#chatgpt_rewrite_status').text('Text copied to clipboard');
        });
        $('#chatgpt_reset_button').click(function () {
            // Clear the input textarea, result, and status messages
            $('#chatgpt_input_text').val('');
            $('#chatgpt_rewrite_result').html('');
            $('#chatgpt_rewrite_status').text('');
            // Hide the copy button
            $('#chatgpt_copy_button').hide();
            $('#chatgpt_reset_button').hide();
        });
    });
});

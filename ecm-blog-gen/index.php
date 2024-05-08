<?php
/**
 * Plugin Name: ChatGPT Blog Generator
 * Plugin URI: 
 * Description: A plugin to generate blog posts using ChatGPT.
 * Version: 1.04
 * Author: Cristian Ibanez
 * Author URI: 
 * License: GPL-3.0
 */

function chatgpt_blog_generator_menu() {
    add_options_page('ChatGPT Blog Generator', 'ChatGPT Blog Generator', 'manage_options', 'chatgpt-blog-generator', 'chatgpt_blog_generator_options_page');
}
add_action('admin_menu', 'chatgpt_blog_generator_menu');

function chatgpt_blog_generator_options_page() {
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    ?>
    <div class="wrap">
        <h1>ChatGPT Blog Generator</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('chatgpt_blog_generator_options');
            do_settings_sections('chatgpt_blog_generator_options');
            ?>
            <table class="form-table">
    <tr valign="top">
        <th scope="row">ChatGPT API Key</th>
        <td><input style="width:420px;" type="text" name="chatgpt_api_key" value="<?php echo esc_attr(get_option('chatgpt_api_key')); ?>" /></td>
    </tr>
    <tr valign="top">
        <th scope="row">Choose Input Source</th>
        <td>
            <?php $input_source = get_option('chatgpt_input_source');
			?>
            <input type="radio" name="chatgpt_input_source" value="fields" <?php checked(empty($input_source) || $input_source === 'fields', true); ?> /> Use fields (niche, topic, keyword)<br />
            <input type="radio" name="chatgpt_input_source" value="prompt" <?php checked($input_source, 'prompt'); ?> /> Use custom prompt<br />
        </td>
    </tr>
</table>
            <?php submit_button(); ?>
        </form>
    </div>

<?php
	
}

function chatgpt_blog_generator_register_settings() {
    register_setting('chatgpt_blog_generator_options', 'chatgpt_api_key');
	register_setting('chatgpt_blog_generator_options', 'chatgpt_input_source');
}
add_action('admin_init', 'chatgpt_blog_generator_register_settings');

function chatgpt_blog_generator_post_meta_box() {
	
	add_meta_box(
        'chatgpt_blog_generator_fields',
        'ChatGPT Blog Generator',
        'chatgpt_blog_generator_meta_box_callback',
        ['post', 'page'],
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'chatgpt_blog_generator_post_meta_box');

function chatgpt_blog_generator_meta_box_callback($post) {
    wp_nonce_field('chatgpt_blog_generator_nonce', 'chatgpt_blog_generator_nonce_field');
    
    $niche = get_post_meta($post->ID, 'chatgpt_niche', true);
    $topic = get_post_meta($post->ID, 'chatgpt_topic', true);
    $keyword = get_post_meta($post->ID, 'chatgpt_keyword', true);
	$prompt = get_post_meta($post->ID, 'chatgpt_prompt', true);
	$input_source = get_option('chatgpt_input_source');
	
	if ($input_source == 'fields'){
	echo '<div class="_pre-fields">';

    echo '<label for="chatgpt_niche">Niche: </label>';
    echo '<input type="text" id="chatgpt_niche" name="chatgpt_niche" value="' . esc_attr($niche) . '" /> ';

    echo '<label for="chatgpt_topic">Topic: </label>';
    echo '<input type="text" id="chatgpt_topic" name="chatgpt_topic" value="' . esc_attr($topic) . '" /> ';

    echo '<label for="chatgpt_keyword">Keyword: </label>';
    echo '<input type="text" id="chatgpt_keyword" name="chatgpt_keyword" value="' . esc_attr($keyword) . '" /> ';

    echo '<button type="button" id="chatgpt_generate_button" class="button button-primary">Generate Intro</button>';
	echo '<input type="hidden" id="chatgpt_content_generated" name="chatgpt_content_generated" value="0" />';
	
    echo '</div>';
	
	} else {
	
	
	echo '<div class="_prompt-field">';
	
	echo '<label for="chatgpt_prompt">Prompt: </label>';
	echo '<input style="width:80%;" type="text" id="chatgpt_prompt" name="chatgpt_custom_prompt" value="' . esc_attr($prompt) . '" />';
		
	echo ' <button type="button" class="button button-primary" id="chatgpt_generate_promt_button">Generate Content</button>';
	echo '<input type="hidden" id="chatgpt_content_generated" name="chatgpt_content_generated" value="0" />';
	
	echo '</div>';
		
	}
	
	echo '<div id="chatgpt_status"></div>';
}

/* function chatgpt_blog_generator_save_post_meta($post_id) {
    if (!isset($_POST['chatgpt_blog_generator_nonce_field']) || !wp_verify_nonce($_POST['chatgpt_blog_generator_nonce_field'], 'chatgpt_blog_generator_nonce')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Get the current post object
    $post = get_post($post_id);

    // Check if it's already published; if so, don't change its status
    if ($post->post_status != 'publish') {
        // Change the post status to 'draft'
        $post->post_status = 'draft';
        // Update the post in the database
        wp_update_post($post);
    }

    update_post_meta($post_id, 'chatgpt_niche', sanitize_text_field($_POST['chatgpt_niche']));
    update_post_meta($post_id, 'chatgpt_topic', sanitize_text_field($_POST['chatgpt_topic']));
    update_post_meta($post_id, 'chatgpt_keyword', sanitize_text_field($_POST['chatgpt_keyword']));
    update_post_meta($post_id, 'chatgpt_prompt', sanitize_text_field($_POST['chatgpt_prompt']));
}
add_action('save_post', 'chatgpt_blog_generator_save_post_meta');

*/

function chatgpt_blog_generator_enqueue_scripts($hook) {
    if ('post.php' !== $hook && 'post-new.php' !== $hook) {
        return;
    }

    wp_enqueue_script('chatgpt-blog-generator-js', plugin_dir_url(__FILE__) . 'chatgpt-blog-generator.js', array('jquery'), '1.0', true);
    wp_localize_script('chatgpt-blog-generator-js', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
}
add_action('admin_enqueue_scripts', 'chatgpt_blog_generator_enqueue_scripts');

function chatgpt_generate_content() {
    if (!current_user_can('edit_posts')) {
        wp_send_json_error(array('message' => 'You do not have permission to generate content.'));
    }

    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    $niche = isset($_POST['niche']) ? sanitize_text_field($_POST['niche']) : '';
    $topic = isset($_POST['topic']) ? sanitize_text_field($_POST['topic']) : '';
    $keyword = isset($_POST['keyword']) ? sanitize_text_field($_POST['keyword']) : '';
	$custom_prompt = isset($_POST['custom_prompt']) ? sanitize_text_field($_POST['custom_prompt']) : '';
    $existing_content = isset($_POST['existing_content']) ? $_POST['existing_content'] : '';
    $conclusion = isset($_POST['conclusion']) && $_POST['conclusion'] === '1';


     try {
        $generated_content = chatgpt_generate_blog_post($niche, $topic, $keyword, $custom_prompt, $existing_content, $conclusion);

        if ($generated_content) {
            wp_send_json_success(array('content' => $generated_content));
        } else {
            wp_send_json_error(array('message' => 'Failed to generate content.'));
        }
    } catch (Exception $e) {
        wp_send_json_error(array('message' => 'Error: ' . $e->getMessage()));
    }
}
add_action('wp_ajax_chatgpt_generate_content', 'chatgpt_generate_content');


function chatgpt_generate_blog_post($niche, $topic, $keyword, $custom_prompt, $existing_content = '', $conclusion = false) {
    $api_key = get_option('chatgpt_api_key');
    $input_source = get_option('chatgpt_input_source');

    if (empty($api_key)) {
        throw new Exception('API key is missing. Please set the API key in plugin settings.');
    }

    if ($input_source == 'prompt') {
        $prompt = $custom_prompt;
    } else {
       if (empty($existing_content)) {
            $prompt = "Write a blog post with a title ending in a period, free of quotation marks, and an intro about the {$niche} niche, focusing on the topic '{$topic}' and including the keyword '{$keyword}', with a maximum word count of 100 words.";
        } elseif ($conclusion) {
            $prompt = "Write a conclusion with a title ending in a period, free of quotation marks, for the blog post. Existing content:\n\n" . $existing_content;
        } else {
            $prompt = "Write a section to the blog post with a title starting with Section: and ending in a period, free of quotation marks, about the {$niche} niche with the topic '{$topic}' and including the keyword '{$keyword}', with a minimum word count of 200 words. Existing content:\n\n" . $existing_content;
        }
    }

    $api_url = 'https://api.openai.com/v1/engines/text-davinci-003/completions';

    $response = wp_remote_post($api_url, [
        'headers' => [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $api_key,
        ],
        'body' => json_encode([
            'prompt' => $prompt,
            'max_tokens' => 3000,
            'n' => 1,
            'stop' => null,
            'temperature' => 0.7,
        ]),
        'timeout' => 60,
    ]);

    if (is_wp_error($response)) {
        throw new Exception('Request error: ' . $response->get_error_message());
    }

    $response_data = json_decode(wp_remote_retrieve_body($response), true);

   if (isset($response_data['choices']) && !empty($response_data['choices'])) {
    $generated_text = $response_data['choices'][0]['text'];
    $sentences = preg_split('/(?<=[.?!])\s+(?=[a-z])/i', $generated_text);
    $title = "";
    $content = "";

    // Extract the title using "Title:" or "Section:" as the delimiter
    if (preg_match('/(?:Section): (.+)/', $sentences[0], $matches)) {
        $title = $matches[1];
        $content = trim(implode(" ", array_slice($sentences, 1)));
    } else {
        // If the delimiter is not found, assume that the first sentence is the title
        $title = $sentences[0];
        $content = trim(implode(" ", array_slice($sentences, 1)));
    }

    return "<h2>{$title}</h2>{$content}";
} else {
        if (isset($response_data['error']) && isset($response_data['error']['message'])) {
            throw new Exception('API error: ' . $response_data['error']['message']);
        } else {
            throw new Exception('Failed to generate content. No choices received from the API.');
        }
    }
}

//ReWriter
function chatgpt_style_rewriter_meta_box() {
    add_meta_box(
        'chatgpt_style_rewriter_meta_box',
        'ChatGPT Style Rewriter',
        'chatgpt_style_rewriter_meta_box_callback',
        ['post', 'page'],
        'normal',
        'high'
    );
}

add_action('add_meta_boxes', 'chatgpt_style_rewriter_meta_box');

function chatgpt_style_rewriter_meta_box_callback($post) {
    ?>
    <table style="width:100%;">
        <tr valign="top">
            <th scope="row" style="width:100px; text-align:left;">Text:</th>
            <td><textarea style="width:100%; min-height:150px;" id="chatgpt_input_text" name="chatgpt_input_text"></textarea></td>
        </tr>
        <tr valign="top">
            <th scope="row" style="width:100px; text-align:left;">Select Style:</th>
            <td>
                <select id="chatgpt_writing_style" name="chatgpt_writing_style">
                    <option value="formal">Formal</option>
                    <option value="informal">Informal</option>
                    <option value="persuasive">Persuasive</option>
                    <option value="descriptive">Descriptive</option>
                    <option value="narrative">Narrative</option>
					<option value="comical with jokes">Comical</option>
                </select>
            </td>
        </tr>
    </table>
    <button type="button" class="button button-primary" id="chatgpt_rewrite_button">Rewrite</button>
	<button type="button" class="button button-primary" id="chatgpt_copy_button" style="display:none;">Copy</button>
    <button type="button" class="button button-secondary" id="chatgpt_reset_button" style="display:none;">Reset</button>
	<div id="chatgpt_rewrite_status" style="margin-top: 20px;"></div>
    <div id="chatgpt_rewrite_result" style="margin-top: 20px; margin-left:100px;"></div>
    <?php
}

function chatgpt_rewrite_content() {
   
    $input_text = isset($_POST['input_text']) ? $_POST['input_text'] : '';
    $style = isset($_POST['style']) ? $_POST['style'] : '';

    try {
        // Use chatgpt_rewrite_content_prompt() instead of chatgpt_rewrite_content()
        $rewrite_content = chatgpt_rewrite_content_prompt($input_text, $style);

        if ($rewrite_content) {
            wp_send_json_success(array('content' => $rewrite_content));
        } else {
            wp_send_json_error(array('message' => 'Failed to generate content.'));
        }
    } catch (Exception $e) {
        wp_send_json_error(array('message' => 'Error: ' . $e->getMessage()));
    }
}
add_action('wp_ajax_chatgpt_rewrite_content', 'chatgpt_rewrite_content');

function chatgpt_rewrite_content_prompt($input_text, $style) {
    $api_key = get_option('chatgpt_api_key');

    // Prepare prompt for the ChatGPT API
   
        if (!empty($input_text)){
           $prompt = "Rewrite the following text in a $style style: $input_text.";
        }

    // Call ChatGPT API
    $api_url = 'https://api.openai.com/v1/engines/text-davinci-003/completions';

    $response = wp_remote_post($api_url, [
        'headers' => [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $api_key,
        ],
        'body' => json_encode([
            'prompt' => $prompt,
            'max_tokens' => 3000, // You can adjust this value according to your needs
            'n' => 1,
            'stop' => null,
            'temperature' => 0.7,
        ]),
        'timeout' => 60, // Set the timeout value to 60 seconds
    ]);

    if (is_wp_error($response)) {
        throw new Exception('Request error: ' . $response->get_error_message());
    }

    $response_data = json_decode(wp_remote_retrieve_body($response), true);

    if (isset($response_data['choices']) && !empty($response_data['choices'])) {
        return $response_data['choices'][0]['text'];
    } else {
        if (isset($response_data['error']) && isset($response_data['error']['message'])) {
            throw new Exception('API error: ' . $response_data['error']['message']);
        } else {
            throw new Exception('Failed to generate content. No choices received from the API.');
        }
    }
}



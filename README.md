# ChatGPT Blog Generator for WordPress

This WordPress plugin, created by Cristian Ibanez, automates the process of blog post creation using ChatGPT. It is designed to integrate seamlessly with the WordPress admin panel, allowing users to generate and edit content directly within their website's backend.

## Features

### Admin Menu Integration
- Adds an options page titled 'ChatGPT Blog Generator' to the WordPress admin menu.
- Accessible only to users with 'manage_options' capabilities.

### Settings and Form Handling
- Users can input a ChatGPT API key and select their preferred input source for generating content.
- Two input sources available:
  - **Fields**: Utilize predefined fields (niche, topic, keyword).
  - **Custom Prompt**: Enter a custom prompt for more dynamic content generation.
- Settings are stored within the WordPress database.

### Content Generation Meta Box
- Adds a meta box to the post and page editing screens.
- Users can generate content by entering details such as niche, topic, keyword, or a custom prompt.
- Directly insert generated content into posts or pages.

### AJAX Content Generation
- Content is generated asynchronously using AJAX calls.
- Includes robust error handling for API connectivity or response issues.

### Enqueue Scripts
- Necessary scripts for AJAX functionality and UI interactions are loaded into the WordPress admin panel.

### Style Rewriter Meta Box
- Allows users to rewrite existing text into different styles (formal, informal, persuasive, etc.).
- Enhances the versatility and appeal of blog content.

## Highlights

### Security
- Ensures that only users with appropriate permissions can access content generation features.

### Flexibility
- Offers users the choice of generating content through structured fields or free-form prompts.

### User Experience
- User-friendly interface with buttons and status indicators for an interactive experience.

## Installation

1. Download the plugin from the provided link.
2. Upload it to your WordPress website via the WordPress admin panel.
3. Activate the plugin through the 'Plugins' menu in WordPress.
4. Navigate to the 'ChatGPT Blog Generator' options page to configure the plugin.

## Usage

- Go to the plugin settings page and enter your ChatGPT API key.
- Choose your input source and configure additional settings as necessary.
- Use the meta boxes added to the post or page screens to generate or rewrite content.

## License

This plugin is licensed under GPL-3.0.

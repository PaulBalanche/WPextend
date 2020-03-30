# WP Extend

- Type: wordpress-plugin
- Current version: 2.6.0
- Contributor: [Paul Balanche](mailto:paul.balanche@gmail.com)
- Homepage: https://github.com/PaulBalanche/WPextend/

## Description

**Extends basic Wordpress features such as:**
- add general settings
- easy creating custom post type
- Gutenberg support :
-- custom blocks
-- patterns

# Gutenberg

1. **Block and patterns theme support:**
In your active theme, add block files into "wpextend/
```
+-- wpextend
|   +-- blocks
	|   +-- namespace
		|   +-- my-awesome-block
			|   +-- assets
			|   +-- build
			|   +-- node_modules
			|   +-- src
			|   +-- npm-debug.log
			|   +-- package-lock.json
			|   +-- package.json
			|   +-- README.md
			|   +-- render.php
|   +-- patterns
	|   +-- namespace
		|   +-- my-awesome-pattern.json
|   +-- json
	|   +-- custom_post_type.json
	|   +-- gutenberg_block.json
	|   +-- options.json
	|   +-- site_settings.json
```
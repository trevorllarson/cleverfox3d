# Native Blocks Theme

## Assets

SCSS and JS source files are starting points. No bootstrap, no tailwind, and very few assumptions about what the project may need. 

## Theme Files and Folders

`acf-json` - Advanced Custom Fields field configurations

`classes` - Classes that get loaded via functions.php

	Actions.php - Methods tied to action hooks (required)

	Admin.php - Extends Actions for admin-only functionality (required)

    Blocks.php - Auto-loads custom blocks and whitelists core blocks (required)

	Filters.php - Methods tied to filter hooks (required)

    Glide.php - Glide PHP integration (required, but can be bypassed in wp-config)

	GravityForms.php - Gravity Forms functionality (optional)
	
	Search.php - Adjustments to default search functionality (optional)
	
	Security.php - Security enhancements (required)

    Template.php - Methods used within the theme (required)

    Update.php - WP core and plugin updater methods called by wp-cli

`parts` - Blocks and other code snippets

	element - Components that might get used in multiple locations

	hooked - Components inserted via Actions

`style.css` - Contains reset/reboot CSS rather than including in the main sheet. This doesn't change.

## Favicon

Favicon Generation: To keep with the consistency of the favicon options already set in the header, please use the following resource to generate the icon files needed:
[https://www.favicon-generator.org/](https://www.favicon-generator.org/)

Wordpess will look for the .ico in the root of the site, so all the other sizes are stored there as well. 

## Block Generating

`./wp-cli.phar ep-generate-block --name="Block Name Here"`

or, if you want to include the block object and empty data check for the admin:

`./wp-cli.phar ep-generate-block --with-block-object --with-data-check --name="Block Name Here"`

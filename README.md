# pdf_export, a ResourceSpace plugin for exporting resources to PDF

## Installation
Download the whole diretory and rename to "pdf_export". Then you can install it one of two ways:

1. Upload as rsp file
	* tar.gz it and change the extenstion to ".rsp"
	* upload the rsp file from the plugin config screen in ResourceSpace admin
	* activate the plugin
2. Upload folder to the plugins directory
	* activate the plugin from the plugin config screen in ResourceSpace admin

## Configuration Options

- Resource Types to exclude (pick from list). This will hide PDF Export link from the view pages of those resources.
- Fonts: can configure a filepath (relative to webroot, not a URL) to a custom font (ttf file) that sits on your server for heading (usually bold) and body/list text (usually regular).
- URL to image to use as header (I would keep it to around 60px tall) 
- Fields to exclude (comma separated list of metadata field IDs, ie: 67,73,78,etc)
- Image size to use in PDF. Defaults to "hdr", but for some installs this is huge and unnecessary. 


## Changelog
* `v 1.0` - Initial Release

### Other
* Thanks to Tom Gleason, whose annotate plugin was invaluable for getting this started.

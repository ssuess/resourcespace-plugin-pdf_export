# pdf_export 
**A ResourceSpace plugin for exporting resources to PDF**

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
- Fields to include (2 fields, multiselect and comma separated list of metadata field IDs, ie: 67,73,78,etc)
- Image size to use in PDF. Defaults to "hdr", but for some installs this is huge and unnecessary.
- Set image height (inches) 
- Integration with [whereabouts plugin](https://github.com/ssuess/resourcespace-plugin-whereabouts)  (if installed)


## Changelog
* `v 1.6.1` - Fix broken collections output,fix SVG cutoff problem in header,fix header zero height problem, header space tweak
* `v 1.6` - Set size and location of header. Better flow and positioning for all elements. Removed buggy newer TCPDF support, use more stable older version. 
* `v 1.5` - Set image height! Fixed horizontal image formatting on letter and legal. 
* `v 1.4.1` - bugfix, configs were only being applied to preview, not create 
* `v 1.4` - Saved configs. New easier multiselect for fields. If upgrading, will purge previous config. 
* `v 1.3` - Support for svg header (requires newer TCPDF),Collection export, Title alignment, multiline formatting (respects line breaks)
* `v 1.2` - Added notes on output, Font Awesome icon, cleanup of PDF layout, various code fixes, French language file.
* `v 1.1` - Fields list config is now by include, not exclude. And order of fields determines order output to PDF.
* `v 1.0` - Initial Release

### Other
* Thanks to Tom Gleason, whose annotate plugin was invaluable for getting this started.

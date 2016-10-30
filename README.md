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
- URL to image to use as header
- Position header image 
- Exclude title header from top of PDF 
- Choose fields for title header
- Image size to use in PDF. Defaults to "hdr", but for some installs this is huge and unnecessary.
- Set image height (inches) 
- Position title line and image horizontally (L,C,R)
- Fields to include (2 fields, multiselect and comma separated list of metadata field IDs, ie: 67,73,78,etc)
- Integration with [whereabouts plugin](https://github.com/ssuess/resourcespace-plugin-whereabouts)  (if installed)
- Integration with [rs_barcode plugin](https://github.com/ssuess/rs_barcode)  (if installed)


## Changelog
* `v 2.0` - Added Title Line/Image positioning, Configurable fields for title line (type + meta), fixed barcode integration
* `v 1.9` - Fixed crashing bug in collection div, added special perm check (pdf) for config rights. Fix square img problem.
* `v 1.8` - Output barcode from specified field
* `v 1.7` - New config exclude title, fix if no logo specified, allow full height image (to margin)
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

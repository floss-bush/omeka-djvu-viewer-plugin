This a simple plugin created to display DjVu files for the Omeka, http://omeka.org/
distributed under GPL v3

Installation
------------

To install, extract the files from the compressed file and paste them in the
Omeka plugin's directory. 
Login as admin and install it.

Usage 
------------

there are two ways to use this plugin
- via modals, using fancybox like : 
  ```php
  echo WEB_ROOT . "/djvu-viewer/index/show/filename/" . $file->archive_filename; 
  ```
  
- via a simple embed call like :
  ```php
  DjvuViewerPlugin::append($file)
  ```


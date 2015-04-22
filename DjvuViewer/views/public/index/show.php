<html>
<head></head>
<body>
    <div>
        <script src="<?php echo WEB_PLUGIN . DIRECTORY_SEPARATOR . 'DjvuViewer' . DIRECTORY_SEPARATOR; ?>/deployJava.js"></script>
        <script>
        var attributes = {
                          codebase:'<?php echo WEB_PLUGIN . DIRECTORY_SEPARATOR . 'DjvuViewer' . DIRECTORY_SEPARATOR . 'applet' ?>',
                          code:'com.lizardtech.djview.Applet.class',
                          archive:'<?php echo WEB_PLUGIN . DIRECTORY_SEPARATOR . 'DjvuViewer' . DIRECTORY_SEPARATOR . 'applet' ?>/javadjvu.jar',
                          width: '99%',
                          height:'97%',
        } ;
        var parameters = {
                cache_archive:"<?php echo WEB_PLUGIN . DIRECTORY_SEPARATOR . 'DjvuViewer' . DIRECTORY_SEPARATOR . 'applet' ?>/javadjvu.jar",
                data:"<?php echo WEB_FILES . '/' . $this->filename . '.djvu'; ?>"
        } ;
        if (deployJava.versionCheck('1.6+')) {
            deployJava.runApplet(attributes, parameters, '1.6');
        } else {
            document.write('<div style="margin: 100px auto; width: 400px; font-size: 1.5em">');
            document.write('Java plugin is necessary to view this page. <a href="http://java.com" target="_blank">Click here</a> to install.');
            document.write('</div>');
        }
        </script>
    </div>
</body>
</html>

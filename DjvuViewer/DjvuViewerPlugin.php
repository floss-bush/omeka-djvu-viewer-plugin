<?php
/**
 * Main plugin script
 *
 * Main script for the plugin, sets up hooks and filters to the Omeka API.
 *
 * @package DjvuViewer
 * @author Gjergj Sheldija, <gjergj.sheldija@gmail.com>
 * @copyright Copyright 2012-2015 Gjergj Sheldija
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */
/**
 * The DjVU Viewer plugin.
 * 
 * @package Omeka\Plugins\DjvuViewer
 */
class DjvuViewerPlugin extends Omeka_Plugin_AbstractPlugin
{

    protected $_hooks = array(
        'install',
        'uninstall',
        'config_form',
        'config',
        'define_routes',
    );

    const DEFAULT_VIEWER_EMBED = 1;
    const DEFAULT_VIEWER_WIDTH = 500;
    const DEFAULT_VIEWER_HEIGHT = 600;

    private $_supportedFiles = array('djvu');

    public function hookInstall()
    {
        set_option('djvuviewer_embed_public', DjvuViewerPlugin::DEFAULT_VIEWER_EMBED);
        set_option('djvuviewer_width_public', DjvuViewerPlugin::DEFAULT_VIEWER_WIDTH);
        set_option('djvuviewer_height_public', DjvuViewerPlugin::DEFAULT_VIEWER_HEIGHT);
    }

    public function hookUninstall()
    {
        delete_option('djvuviewer_width');
        delete_option('djvuviewer_height');
    }

    public function hookConfigForm()
    {
    	require dirname(__FILE__) . '/config_form.php';
    }

    public function hookConfig()
    {
        if (!is_numeric($_POST['djvuviewer_width_public']) ||
            !is_numeric($_POST['djvuviewer_height_public'])) {
            throw new Omeka_Validate_Exception('The width and height must be numeric.');
        }

        set_option('djvuviewer_embed_public', (int) (boolean) $_POST['djvuviewer_embed_public']);
        set_option('djvuviewer_width_public', $_POST['djvuviewer_width_public']);
        set_option('djvuviewer_height_public', $_POST['djvuviewer_height_public']);
    }

    public function hookDefineRoutes($args)
    {

        if (is_admin_theme()) {
            return;
        }

		$router = $args['router'];

        $router->addRoute(
            'djvu_viewer',
            new Zend_Controller_Router_Route(
                'djvu-viewer/index/show/:filename',
                array(
                    'module'       => 'djvu-viewer',
                    'controller'   => 'index',
                    'action'       => 'show'
                    ),
                array( 'filename'  => '\s+')
            )
        );

    }

    public function djvuviewer_embed ( $Files = "" )
    {

        if (!is_array($Files)) {
            $Files = array($Files);
        }

        foreach ($Files as $file) {
            $extension = pathinfo($file->filename, PATHINFO_EXTENSION);
            if (!in_array($extension, $this->_supportedFiles)) {
                continue;
            }

            ?>
            <div>
                <script src="<?php echo WEB_PLUGIN . DIRECTORY_SEPARATOR . 'DjvuViewer' . DIRECTORY_SEPARATOR; ?>/deployJava.js"></script>
                <script>
                var attributes = {
                                  codebase:'<?php echo WEB_PLUGIN . DIRECTORY_SEPARATOR . 'DjvuViewer' . DIRECTORY_SEPARATOR . 'applet' ?>',
                                  code:'com.lizardtech.djview.Applet.class',
                                  archive:'<?php echo WEB_PLUGIN . DIRECTORY_SEPARATOR . 'DjvuViewer' . DIRECTORY_SEPARATOR . 'applet' ?>/javadjvu.jar',
                                  width:<?php echo is_admin_theme() ? get_option('djvuviewer_width_admin') : get_option('djvuviewer_width_public'); ?>,
                                  height:<?php echo is_admin_theme() ? get_option('djvuviewer_height_admin') : get_option('djvuviewer_height_public'); ?>
                } ;
                var parameters = {
                        cache_archive:"<?php echo WEB_PLUGIN . DIRECTORY_SEPARATOR . 'DjvuViewer' . DIRECTORY_SEPARATOR . 'applet' ?>/javadjvu.jar",
                        data:"<?php echo $this->_getUrl($file); ?>"
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

            <?php
        }
    }

    private function _getUrl(File $file) {
		return WEB_FILES . '/' . $file->archive_filename;
    }

    public function djvuviewer_hasDjvuFile() {
        foreach (get_view()->item->Files as $file) {
            $extension = pathinfo($file->archive_filename, PATHINFO_EXTENSION);
            if ( $extension != 'djvu' ) {
                return false;
            } else {
                return true;
            }
        }
    }

}

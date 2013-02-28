<?php
/**
 * Main plugin script
 *
 * Main script for the plugin, sets up hooks and filters to the Omeka API.
 *
 * @package DjvuViewer
 * @author Gjergj Sheldija, <gjergj.sheldija@gmail.com>
 * @copyright Copyright 2012-2013 Gjergj Sheldija
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

class DjvuViewerPlugin extends Omeka_Plugin_Abstract
{

    protected $_hooks = array(
        'install',
        'uninstall',
        'config_form',
        'config',
        'define_routes',
        'public_append_to_items_show',
    );

    const DEFAULT_VIEWER_EMBED = 1;
    const DEFAULT_VIEWER_WIDTH = 500;
    const DEFAULT_VIEWER_HEIGHT = 600;

    private $_supportedFiles = array('djvu');

    public function hookInstall()
    {
        set_option('djvuviewer_embed_admin', DjvuViewerPlugin::DEFAULT_VIEWER_EMBED);
        set_option('djvuviewer_width_admin', DjvuViewerPlugin::DEFAULT_VIEWER_WIDTH);
        set_option('djvuviewer_height_admin', DjvuViewerPlugin::DEFAULT_VIEWER_HEIGHT);
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
        include 'config_form.php';
    }

    public function hookConfig($post)
    {
        if (!is_numeric($post['djvuviewer_width_admin']) ||
            !is_numeric($post['djvuviewer_height_admin']) ||
            !is_numeric($post['djvuviewer_width_public']) ||
            !is_numeric($post['djvuviewer_height_public'])) {
            throw new Exception('The width and height must be numeric.');
        }
        set_option('djvuviewer_embed_admin', (int) (boolean) $post['djvuviewer_embed_admin']);
        set_option('djvuviewer_width_admin', $post['djvuviewer_width_admin']);
        set_option('djvuviewer_height_admin', $post['djvuviewer_height_admin']);
        set_option('djvuviewer_embed_public', (int) (boolean) $post['djvuviewer_embed_public']);
        set_option('djvuviewer_width_public', $post['djvuviewer_width_public']);
        set_option('djvuviewer_height_public', $post['djvuviewer_height_public']);
    }

    public function hookDefineRoutes($router)
    {
        $router->addRoute(
            'djvuviewer_show_route',
            new Zend_Controller_Router_Route(
                'djvu-viewer/index/show/:filename',
                array(
                    'module'       => 'djvu-viewer  ',
                    'controller'   => 'index',
                    'action'       => 'show'
                    ),
                array( 'filename'  => '\s+')
            )
        );
    }

    public static function hookPublicAppendToItemsShow( $currentFile = "" )
    {
        // Embed viewer only if configured to do so.
        if ((is_admin_theme() && !get_option('djvuviewer_embed_admin')) ||
            (!is_admin_theme() && !get_option('djvuviewer_embed_public'))) {
            return;
        }
        $docsViewer = new DjvuViewerPlugin;
        $docsViewer->djvuviewer_embed( $currentFile );
    }

    public function djvuviewer_embed ( $currentFile = "" )
    {

        foreach (__v()->item->Files as $file) {
            if($file->id == $currentFile) {
                $extension = pathinfo($file->archive_filename, PATHINFO_EXTENSION);
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
                    if (deployJava.versionCheck('1.6')) {
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
    }

    private function _getUrl(File $file) {
		return WEB_FILES . '/' . $file->archive_filename;
    }

    public function djvuviewer_hasDjvuFile() {
        foreach (__v()->item->Files as $file) {
            $extension = pathinfo($file->archive_filename, PATHINFO_EXTENSION);
            if ( $extension != 'djvu' ) {
                return false;
            } else {
                return true;
            }
        }
    }

}

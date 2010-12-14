<?php
add_plugin_hook('install', 'DjvuViewerPlugin::install');
add_plugin_hook('uninstall', 'DjvuViewerPlugin::uninstall');
add_plugin_hook('config_form', 'DjvuViewerPlugin::configForm');
add_plugin_hook('config', 'DjvuViewerPlugin::config');
add_plugin_hook('admin_append_to_items_show_primary', 'DjvuViewerPlugin::append');
add_plugin_hook('public_append_to_items_show', 'DjvuViewerPlugin::append');

class DjvuViewerPlugin
{
    const DEFAULT_VIEWER_EMBED = 1;
    const DEFAULT_VIEWER_WIDTH = 500;
    const DEFAULT_VIEWER_HEIGHT = 600;
    
    private $_supportedFiles = array('djvu');
    
    public static function install()
    {
        set_option('djvuviewer_embed_admin', DjvuViewerPlugin::DEFAULT_VIEWER_EMBED);
        set_option('djvuviewer_width_admin', DjvuViewerPlugin::DEFAULT_VIEWER_WIDTH);
        set_option('djvuviewer_height_admin', DjvuViewerPlugin::DEFAULT_VIEWER_HEIGHT);
        set_option('djvuviewer_embed_public', DjvuViewerPlugin::DEFAULT_VIEWER_EMBED);
        set_option('djvuviewer_width_public', DjvuViewerPlugin::DEFAULT_VIEWER_WIDTH);
        set_option('djvuviewer_height_public', DjvuViewerPlugin::DEFAULT_VIEWER_HEIGHT);
    }
    
    public static function uninstall()
    {
        delete_option('djvuviewer_width');
        delete_option('djvuviewer_height');
    } 
    
    public static function configForm()
    {
        include 'config_form.php';
    }
    
    public static function config($post)
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
    
    public static function append()
    {
        // Embed viewer only if configured to do so.
        if ((is_admin_theme() && !get_option('djvuviewer_embed_admin')) || 
            (!is_admin_theme() && !get_option('djvuviewer_embed_public'))) {
            return;
        }
        $docsViewer = new DjvuViewerPlugin;
        $docsViewer->embed();
    }
    
    public function embed()
    {
        foreach (__v()->item->Files as $file) {
            $extension = pathinfo($file->archive_filename, PATHINFO_EXTENSION);
            if (!in_array($extension, $this->_supportedFiles)) {
                continue;
            }
?>
<div>
	<applet 
		archive="<?php echo WEB_DIR . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'DjvuViewer' . DIRECTORY_SEPARATOR . 'applet' ?>/javadjvu.jar" 
		code="com.lizardtech.djview.Applet.class" 
		style="border:0px none;"
        width="<?php echo is_admin_theme() ? get_option('djvuviewer_width_admin') : get_option('djvuviewer_width_public'); ?>" 
        height="<?php echo is_admin_theme() ? get_option('djvuviewer_height_admin') : get_option('djvuviewer_height_public'); ?>" 

	>
	<param name="data" value="<?php echo $this->_getUrl($file); ?>">
	<param name="image" value="http://www.bu-unishk.org">
	<param name="cache_archive" value="<?php echo WEB_DIR . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'DjvuViewer' . DIRECTORY_SEPARATOR . 'applet' ?>/javadjvu.jar"> 
	</applet>
</div>

<?php
        }
    }
    
    private function _getUrl(File $file)
    {
		return WEB_FILES . '/' . $file->archive_filename;
    }
}

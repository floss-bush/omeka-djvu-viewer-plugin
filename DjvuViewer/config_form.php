<h3>Admin Interface</h3>
<label for="djvuviewer_embed_admin">Embed viewer in admin item show pages?</label>
<p><?php echo get_view()->formCheckbox('djvuviewer_embed_admin', 
                                  true, 
                                  array('checked' => (boolean) get_option('djvuviewer_embed_admin'))); ?></p>
<label for="djvuviewer_width_admin">Viewer width, in pixels:</label>
<p><?php echo get_view()->formText('djvuviewer_width_admin', 
                              get_option('djvuviewer_width_admin'), 
                              array('size' => 5));?></p>
<label for="djvuviewer_height_admin">Viewer height, in pixels:</label>
<p><?php echo get_view()->formText('djvuviewer_height_admin', 
                              get_option('djvuviewer_height_admin'), 
                              array('size' => 5));?></p>
<h3>Public Theme</h3>
<label for="djvuviewer_embed_public">Embed viewer in public item show pages?</label>
<p><?php echo get_view()->formCheckbox('djvuviewer_embed_public', 
                                  true, 
                                  array('checked' => (boolean) get_option('djvuviewer_embed_public'))); ?></p>
<label for="djvuviewer_width_public">Viewer width, in pixels:</label>
<p><?php echo get_view()->formText('djvuviewer_width_public', 
                              get_option('djvuviewer_width_public'), 
                              array('size' => 5));?></p>
<label for="djvuviewer_height_public">Viewer height, in pixels:</label>
<p><?php echo get_view()->formText('djvuviewer_height_public', 
                              get_option('djvuviewer_height_public'), 
                              array('size' => 5));?></p>

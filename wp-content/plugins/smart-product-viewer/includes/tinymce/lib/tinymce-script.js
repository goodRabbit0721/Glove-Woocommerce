(function($) {
  tinymce.create( 
      'tinymce.plugins.spv_shortcode', 
      {

          init : function( editor, url ) {
              editor.addButton(
                  'spv_shortcode_btn', 
                  {
                    cmd   : 'spv_shortcode_btn_cmd',
                    title : 'Smart Product Viewer',
                    image : url + '/icon.png'
                  }
                );
               editor.addCommand(
                'spv_shortcode_btn_cmd',
                function() {
                  editor.windowManager.open(
                    {
                      // this is the ID of the popups parent element
                      id     : 'spv_shortcode-form',
                      width    : 480,
                      height   : 600,
                      title    : 'Smart Product Viewer Shortcode',
                      wpDialog : true,
                    },
                    {
                      plugin_url : url
                    }
                  );
                }
              );
          }
      }
  );
  // register plugin
  tinymce.PluginManager.add( 'spv_shortcode', tinymce.plugins.spv_shortcode );
})(jQuery);
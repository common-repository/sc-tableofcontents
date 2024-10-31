<?php
if ( ! class_exists( 'SC_TableOfContents' ) ) :
class SC_TableOfContents {

    const PAGE_SETTING         = 'sc-toc_page_setting';
    const SECTION_TOC          = 'sc-toc_field_toc';
    const SECTION_READTIME     = 'sc-toc_field_readtime';
    const SECTION_DESIGN       = 'sc-toc_field_design';
    const OPTION_1ST_TAG       = 'sc-toc_1st_tag';
    const OPTION_2ND_TAG       = 'sc-toc_2nd_tag';
    const OPTION_ANCHOR_ID     = 'sc-toc_anchor';
    const OPTION_READTIME_HTML = 'sc-toc_readtime_html';
    const GROUP_SETTING        = 'sc-toc_group_setting';
    const NAME_OPTION          = 'sc-toc_options';

    const OPTION_DEFS = array(
        self::OPTION_1ST_TAG       => 'h3',
        self::OPTION_2ND_TAG       => 'h4',
        self::OPTION_ANCHOR_ID     => 'sc-toc_anchor',
        self::OPTION_READTIME_HTML => SCTOC_TXT_READTIME_MSG,
    );

    const ALLOW_TAGS = array(
        'h1', 'h2', 'h3', 'h4', 'h5', 'h6'
    );


    public $options;


    function __construct() {

        $this->loadOptions();

        // Add shortcode "sc-toc".
        add_shortcode( SCTOC_TXT_SHORTCODE, array($this, 'makeTableOfContents') );

        if (is_admin()) {

            // CSS & Javascript.
            global $pagenow;
            if( $pagenow == 'options-general.php' ){
                if( !empty($_GET['page']) && ($_GET['page'] === SCTOC_TXT_SHORTCODE) ){
                    add_action( 'admin_enqueue_scripts', function(){
                        wp_enqueue_style(SCTOC_TXT_PLUGINNAME, plugins_url( 'assets/' . SCTOC_TXT_SHORTCODE . '-admin.css', dirname(__FILE__) ));
                        wp_enqueue_script(SCTOC_TXT_PLUGINNAME . '_js',  plugins_url( 'assets/' . SCTOC_TXT_SHORTCODE . '-admin.js', dirname(__FILE__) ), false );
                    } );
                }
            }

            // Add menu.
            add_action( 'admin_menu', function(){
                add_options_page(SCTOC_TXT_PLUGINNAME_JP, SCTOC_TXT_PLUGINNAME_JP, 'edit_posts', SCTOC_TXT_SHORTCODE, 'sctoc_add_setting_page');
            } );

            // Add option menu.
            add_filter( 'plugin_action_links_' . plugin_basename( dirname(dirname(__FILE__)) . '/' .SCTOC_TXT_PLUGINNAME . '.php' ), function($actions) {
                $menu_settings_url	= '<a href="options-general.php?page=' . SCTOC_TXT_SHORTCODE .'">' . SCTOC_TXT_SETTING . '</a>';
                array_unshift( $actions , $menu_settings_url );
                return $actions;
            } );

            // Admin page init.
            add_action( 'admin_init', array($this, 'initAdminPage') );

        } else {

            // CSS & Javascript.
            add_action( 'wp_enqueue_scripts', function(){
                $css_path = plugins_url( 'assets/' . SCTOC_TXT_SHORTCODE . '.css', dirname(__FILE__) );
                if ( file_exists(get_template_directory() . '/' . SCTOC_TXT_SHORTCODE . '.css') ) {
                    $css_path = get_template_directory_uri() . '/' . SCTOC_TXT_SHORTCODE . '.css';
                }
                wp_enqueue_style(SCTOC_TXT_PLUGINNAME, $css_path);
            });

            // Content rewrite.
            add_filter('the_content', array($this, 'rewriteContent'));

        }

    }


    /**
     * Load SC-TableOfContens options and set a value for the options variable.
     * 
     * @param none
     * @return null
     */
    function loadOptions() {
        $tmp = get_option( self::NAME_OPTION );
        foreach (self::OPTION_DEFS as $k => $v) {
            $this->options[$k] = !empty($tmp[$k]) ? $tmp[$k] : $v;
        }
    }


    /**
     * Filter values from POST variable and set them to options variable.
     * 
     * @param none
     * @return null
     */
    function filterInputOptions() {
        foreach (self::OPTION_DEFS as $k => $v) {
            $this->options[$k] = filter_input( INPUT_POST, $k, FILTER_SANITIZE_STRING );
        }
    }


    /**
     * Make table of contens and readtime html.
     * 
     * @param boolean $attrs(readtime)  whether to show reading time. default is true.
     * @return string HTML for table of contents.
     */
    function makeTableOfContents($atts) {

        extract(shortcode_atts(array('readtime' => 1), $atts));

        $mokuji_ptn = '/<(h[1-6])[^>]*>(.*?)<\/h[1-6]>/';

        if ($content = get_the_content()) {

            if (preg_match_all($mokuji_ptn, $content, $matches, PREG_SET_ORDER)) {

                $mokuji_html = '<div id="sctoc-block"><div>' . SCTOC_TXT_TABLE_OF_CONTENTS . '</div><ol>';
                $ireko = 0; // 0:normal　1:Nest judgment start　2:nesting

                $limit = intval( str_replace( 'h', '', $this->options[$this::OPTION_1ST_TAG] ) );
                $break_tags = array();
                for ($i=1; $i<=$limit; $i++) {
                    $break_tags[] = ('h' . $i);
                }

                foreach ($matches as $k => $line) {
                    $anchor = $this->options[$this::OPTION_ANCHOR_ID] . $k;

                    if ( in_array($line[1], $break_tags) ) {
                        if ($ireko == 2) {
                            $mokuji_html .= '</ol>';
                        }
                        if ($ireko == 1 || $ireko == 2) {
                            $mokuji_html .= '</li>';
                        }
                        $ireko = 0;
                    }

                    if ($line[1] == $this->options[$this::OPTION_1ST_TAG] || $line[1] == $this->options[$this::OPTION_2ND_TAG]) {
                        if ($line[1] == $this->options[$this::OPTION_1ST_TAG]) {
                            $ireko = 1;
                            $mokuji_html .= sprintf( '<li class="sctoc-line-1"><a href="#%s">%s</a>', $anchor, strip_tags($line[2]) );
                        }
                        elseif ($line[1] == $this->options[$this::OPTION_2ND_TAG]) {
                            if ($ireko == 1) {
                                $mokuji_html .= '<ol>';
                                $ireko = 2;
                            }
                            $mokuji_html .= sprintf( '<li class="sctoc-line-2"><a href="#%s">%s</a></li>', $anchor, strip_tags($line[2]) );
                        }
                        $replaced_tag = preg_replace( '/<(h[1-6])>/', '<${1} id="' . $anchor . '">', $line[0] );
                    }
                }
                if ($ireko == 2) {
                    $mokuji_html .= '</ol>';
                }
                if ($ireko == 1 || $ireko == 2) {
                    $mokuji_html .= '</li>';
                }
                $mokuji_html .= '</ol></div>';
            }

            if ($readtime) {
                $word = mb_strlen( strip_tags($content) );
                $m = floor($word / 600) + 1;
                $time = ($m == 0 ? '' : $m);
                $mokuji_html .= '<div id="sctoc-readtime">';
                $mokuji_html .= strip_tags( str_replace( SCTOC_TXT_READTIME_KEY, intval($time), $this->options[$this::OPTION_READTIME_HTML] ), '<span>' );
                $mokuji_html .= '</div>';
            }

        }
        return $mokuji_html;
    }


    /**
     * Initialize the admin page.
     * 
     * @param none
     * @return null
     */
    function initAdminPage() {

        if ( !empty($_GET['tab']) && $_GET['tab'] === SCTOC_TXT_READTIME ) {

            add_settings_section(
                self::SECTION_READTIME, // ID
                SCTOC_TXT_READTIME, // Title
                function(){
                    echo SCTOC_TXT_SETTING_DESCTIPTION_READTIME . '<hr>';
                }, // Callback
                self::PAGE_SETTING // Page
            );

            add_settings_field(
                self::OPTION_READTIME_HTML, // Name
                SCTOC_TXT_READTIME_HTML_SETTING, // Title
                function(){
                    $sctoc = new SC_TableOfContents();

                    echo sprintf( '<span style="color:#999;font-size: larger;">%s</span>
                    <input name="%s[%s]" type="text" value="%s" class="large-text"">
                    <span style="color:#999;font-size: larger;">%s</span>
                    <p class="description" style="margin-top:10px;">%s</p>',
                        esc_html( '<div id="sctoc-readtime">' ),
                        $sctoc::NAME_OPTION,
                        $sctoc::OPTION_READTIME_HTML,
                        esc_attr($sctoc->options[$sctoc::OPTION_READTIME_HTML]),
                        esc_html( '</div>' ),
                        SCTOC_TXT_READTIME_HTML_SETTING_MEMO
                    );
                }, // CallBack.
                self::PAGE_SETTING, // Page
                self::SECTION_READTIME // Section
            );

        } elseif ( !empty($_GET['tab']) && $_GET['tab'] === SCTOC_TXT_DESIGN ) {

            add_settings_section(
                self::SECTION_DESIGN, // ID
                SCTOC_TXT_DESIGN, // Title
                function(){
                    echo SCTOC_TXT_SETTING_DESCTIPTION_DESIGN;
                }, // Callback
                self::PAGE_SETTING // Page
            );

        } else {

            add_settings_section(
                self::SECTION_TOC, // ID
                SCTOC_TXT_TABLE_OF_CONTENTS, // Title
                function(){
                    echo SCTOC_TXT_SETTING_DESCTIPTION_TOC . '<hr>';
                }, // Callback
                self::PAGE_SETTING // Page
            );

            add_settings_field(
                self::OPTION_1ST_TAG, // Name
                SCTOC_TXT_1ST_TAG_SETTING, // Title
                function(){
                    $sctoc = new SC_TableOfContents();
                    echo sprintf( '<select name="%s[%s]" id="select-%s">', $sctoc::NAME_OPTION, $sctoc::OPTION_1ST_TAG, $sctoc::OPTION_1ST_TAG );
                    for ($i=1; $i<=6; $i++) {
                        echo sprintf( '<option value="h%d" %s>h%d</option>',
                            $i,
                            ($sctoc->options[$sctoc::OPTION_1ST_TAG] === 'h'.$i) ? 'selected' : '',
                            $i
                        );
                    }
                    echo '</select>';
                }, // CallBack.
                self::PAGE_SETTING, // Page
                self::SECTION_TOC // Section
            );

            add_settings_field(
                self::OPTION_2ND_TAG, // Name
                SCTOC_TXT_2ND_TAG_SETTING, // Title
                function(){
                    $sctoc = new SC_TableOfContents();
                    echo sprintf( '<select name="%s[%s]" id="select-%s">', $sctoc::NAME_OPTION, $sctoc::OPTION_2ND_TAG, $sctoc::OPTION_2ND_TAG );
                    echo sprintf( '<option value="">%s</option>', SCTOC_TXT_SELECT_NONE );
                    $first_tag_num = intval( str_replace('h', '', $sctoc->options[$sctoc::OPTION_1ST_TAG]) );
                    for ( $i=($first_tag_num+1); $i<=6; $i++ ) {
                        echo sprintf( '<option value="h%d" %s>h%d</option>',
                            $i,
                            ($sctoc->options[$sctoc::OPTION_2ND_TAG] === 'h'.$i) ? 'selected' : '',
                            $i
                        );
                    }
                    echo '</select>';
                }, // CallBack.
                self::PAGE_SETTING, // Page
                self::SECTION_TOC // Section
            );

            add_settings_field(
                self::OPTION_ANCHOR_ID, // Name
                SCTOC_TXT_ANCHOR_ID_SETTING, // Title
                function(){
                    $sctoc = new SC_TableOfContents();
                    echo sprintf( '<input name="%s[%s]" type="text" value="%s" class="regular-text"><p class="description">%s</p>',
                        $sctoc::NAME_OPTION,
                        $sctoc::OPTION_ANCHOR_ID,
                        esc_attr( $sctoc->options[$sctoc::OPTION_ANCHOR_ID] ),
                        sprintf( SCTOC_TXT_ANCHOR_ID_SETTING_MEMO, $sctoc::OPTION_DEFS[$sctoc::OPTION_ANCHOR_ID] )
                    );
                }, // CallBack.
                self::PAGE_SETTING, // Page
                self::SECTION_TOC // Section
            );

        }

        register_setting(
            self::GROUP_SETTING, // Group
            self::NAME_OPTION, // Option
            array( 'sanitize_callback' => array($this, 'sanitizeOptions') )
        );

    }


    /**
     * Filter hook the_content. For rewrite text for "sc-toc" shortcode.
     * 
     */
    function rewriteContent($content) {
        if (is_single() || is_page()) {
            if (preg_match('/[' . SCTOC_TXT_SHORTCODE . '\s(.*)]/', $content)) {
                $mokuji_ptn = '/<(h[1-6])[^>]*>(.*)<\/h[1-6]>/';
                if (preg_match_all($mokuji_ptn, $content, $matches, PREG_SET_ORDER)) {

                    $sctoc = new SC_TableOfContents();
                    foreach ($matches as $k => $line) {
                        $anchor = $this->options[$this::OPTION_ANCHOR_ID] . $k;
                        if ($line[1] == $this->options[$this::OPTION_1ST_TAG] || $line[1] == $this->options[$this::OPTION_2ND_TAG]) {
                            $replaced_tag = preg_replace('/<(h[1-6][^>]*)>/', '<${1} id="' . $anchor . '">', $line[0]);
                            $content = str_replace($line[0], $replaced_tag, $content);
                        }
                    }
                }
            }
        }
    	return $content;
	}


    function sanitizeOptions($input) {

        if ( isset($input[self::OPTION_ANCHOR_ID]) ) {
            $input[self::OPTION_ANCHOR_ID] = trim( $input[self::OPTION_ANCHOR_ID] );
            $input[self::OPTION_ANCHOR_ID] = filter_var( $input[self::OPTION_ANCHOR_ID], FILTER_SANITIZE_STRING );
            if ( mb_strlen($input[self::OPTION_ANCHOR_ID]) >= 32 ) {
                add_settings_error(
                    self::PAGE_SETTING,
                    self::NAME_OPTION . '[' . self::OPTION_ANCHOR_ID . ']',
                    SCTOC_TXT_VALIDATE_ERR_ANCHOR_ID,
                    'error'
                );
                return false;
            }

        }

        if ( isset($input[self::OPTION_READTIME_HTML]) ) {
            $input[self::OPTION_READTIME_HTML] = trim( $input[self::OPTION_READTIME_HTML] );
            $input[self::OPTION_READTIME_HTML] = strip_tags( $input[self::OPTION_READTIME_HTML], '<span>' );
        }

        return $input;
    }


}

endif;

<?php
function sctoc_add_setting_page() {

    $sctoc = new SC_TableOfContents();
?>
<div id="sctoc-admin-header">
  <div class="sctoc-settings-title-section">
    <h1><?php echo esc_html(SCTOC_TXT_PLUGINNAME . '&nbsp;' . SCTOC_TXT_SETTING); ?></h1>
  </div>

  <nav class="sctoc-settings-tabs-wrapper" aria-label="サブメニュー">
    <a href="?page=<?php echo esc_attr(SCTOC_TXT_SHORTCODE); ?>" class="sctoc-settings-tab <?php echo ( empty($_GET['tab']) || $_GET['tab'] == SCTOC_TXT_TABLE_OF_CONTENTS ) ? 'active' : ''; ?>" aria-current="true"><?php echo esc_html(SCTOC_TXT_TABLE_OF_CONTENTS); ?></a>
    <a href="?page=<?php echo esc_attr(SCTOC_TXT_SHORTCODE); ?>&tab=<?php echo esc_attr(SCTOC_TXT_READTIME); ?>" class="sctoc-settings-tab <?php echo ( !empty($_GET['tab']) && $_GET['tab'] == SCTOC_TXT_READTIME ) ? 'active' : ''; ?>"><?php echo esc_html(SCTOC_TXT_READTIME); ?></a>
    <a href="?page=<?php echo esc_attr(SCTOC_TXT_SHORTCODE); ?>&tab=<?php echo esc_attr(SCTOC_TXT_DESIGN); ?>" class="sctoc-settings-tab <?php echo ( !empty($_GET['tab']) && $_GET['tab'] == SCTOC_TXT_DESIGN ) ? 'active' : ''; ?>"><?php echo esc_html(SCTOC_TXT_DESIGN); ?></a>
  </nav>
</div>
<hr class="wp-header-end">
<div class="sctoc-settings-body">

  <form method="post" action="options.php">

    <?php do_settings_sections( $sctoc::PAGE_SETTING ); ?>
    <?php settings_fields( $sctoc::GROUP_SETTING ); ?>
    <?php if( empty($_GET['tab']) || $_GET['tab'] !== SCTOC_TXT_DESIGN ): ?>
    <?php submit_button(); ?>
    <?php endif; ?>

  </form>
</div>
<?php
}

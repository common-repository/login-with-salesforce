<?php
require_once dirname(__DIR__) . '/helper/mo_import_export.php';
/**
 *Function to display block of UI for export Import
 */
function mo_saml_keep_configuration_saml() {
	echo '<div class="mo_saml_support_layout" id="mo_saml_keep_configuration_intact">
        <div>
        <h3>Keep configuration Intact</h3>
        <form name="f" method="post" action="" id="settings_intact">
        <input type="hidden" name="option" value="mo_saml_keep_settings_on_deletion"/>
        <label><input type="checkbox" name="mo_saml_keep_settings_intact" ';
        echo checked(get_option('mo_saml_keep_settings_on_deletion')=='true');
        echo 'onchange="document.getElementById(\'settings_intact\').submit();"/>
        Enabling this would keep your settings intact when plugin is uninstalled.       
        </label>
        <p><b>Please enable this option
        when you are updating to a Premium version.</b></p>
        </form>
        </div>
        <br /><br />
	</div>';
}



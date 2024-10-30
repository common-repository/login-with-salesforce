<?php
require_once dirname(__DIR__) . '/includes/lib/mo-options-enum.php';
add_action( 'admin_init', 'mo_saml_import_export');
define( "Tab_Class_Names", serialize( array(
	"SSO_Login"         => 'mo_options_enum_sso_login',
	"Identity_Provider" => 'mo_options_enum_identity_provider',
	"Service_Provider"  => 'mo_options_enum_service_provider',
	"Attribute_Mapping" => 'mo_options_enum_attribute_mapping',
	"Role_Mapping"      => 'mo_options_enum_role_mapping',
    "Test_Configuration" => 'mo_options_test_configuration'
) ) );

/**
 *Function iterates through the enum to create array of values and converts to JSON and lets user download the file
 */
function mo_saml_import_export($test_config_screen=false) {

    if($test_config_screen)
        $_POST['option'] = 'mo_saml_export';

	if ( array_key_exists( "option", $_POST ) ) {

        switch ($_POST['option']) {
            case 'mo_saml_export':
                $tab_class_name = unserialize(Tab_Class_Names);
                $configuration_array = array();
                foreach ($tab_class_name as $key => $value) {
                    $configuration_array[$key] = mo_saml_get_configuration_array($value);
                }
                $configuration_array["Version_dependencies"] = mo_saml_get_version_informations();
				header("Content-Disposition: attachment; filename=miniorange-saml-config.json");
				$version = phpversion();
				if(substr($version,0 ,3) === '5.3'){
					echo(json_encode($configuration_array, JSON_PRETTY_PRINT));
				} else {
                	echo(json_encode($configuration_array, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
				}
				exit;
            case 'mo_saml_keep_settings_on_deletion':
                if(array_key_exists('mo_saml_keep_settings_intact',$_POST))
                    update_option('mo_saml_keep_settings_on_deletion','true');
                else
                    update_option('mo_saml_keep_settings_on_deletion','');
                break;

        }
        return;


	}





}

function mo_saml_get_configuration_array($class_name ) {
	$class_object = call_user_func( $class_name . '::getConstants' );
	$mo_array = array();
	foreach ( $class_object as $key => $value ) {
		$mo_option_exists=get_option($value);

		if($mo_option_exists){
			if(@unserialize($mo_option_exists)!==false){
				$mo_option_exists = unserialize($mo_option_exists);
			}
			$mo_array[ $key ] = $mo_option_exists;

		}

	}

	return $mo_array;
}

function mo_saml_update_configuration_array($configuration_array ) {
	$tab_class_name = unserialize( Tab_Class_Names );
	foreach ( $tab_class_name as $tab_name => $class_name ) {
		foreach ( $configuration_array[ $tab_name ] as $key => $value ) {
			$option_string = constant( "$class_name::$key" );
			$mo_option_exists = get_option($option_string);
			if ( $mo_option_exists) {
				if(is_array($value))
					$value = serialize($value);
				update_option( $option_string, $value );
			}
		}
	}

}

function mo_saml_get_version_informations(){
	$array_version = array();
	$array_version["PHP_version"] = phpversion();
	$array_version["Wordpress_version"] = get_bloginfo('version');
	$array_version["OPEN_SSL"] = mo_is_openssl_installed();
	$array_version["CURL"] = mo_is_curl_installed();
    $array_version["ICONV"] = mo_is_dom_installed();
    $array_version["DOM"] = mo_is_dom_installed();

	return $array_version;

}
?>
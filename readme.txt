=== Login with Salesforce ===
Contributors: miniOrange
Donate link: http://miniorange.com
Tags: salesforce,login with salesforce,communites,sso,saml, SSO Saml, login, sso integration WordPress, SSO using SAML, SAML 2.0 Service Provider, Wordpress SAML, SAML Single Sign-On, SSO using SAML, SAML 2.0, SAML 20, Wordpress Single Sign On,ad,ADFS,Active directory,Azure AD,adfs single sign-on, ad sso,Okta, Google Apps, Google for Work, Salesforce, Shibboleth, SimpleSAMLphp, OpenAM, Centrify, Ping, RSA, IBM, Oracle, OneLogin, Bitium, WSO2, NetIQ, Novell Access Manager, login with adfs, login with azure ad, login with okta, auth0, AuthAnvil, cognito, Atlassian, CA Identity Manager, Tivoli Identity Manage, Forgerock OpenIDM, SAP Cloud Identity Provider
Requires at least: 3.5
Tested up to: 5.8
Stable tag: 1.0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Login with Salesforce provides SSO into Wordpress via Salesforce communities and much more.

== Description ==

Login with Salesforce allows users residing at SAML 2.0 capable Identity Provider to login to your WordPress website. We support all known IdPs - Google Apps, ADFS, Azure AD, Okta, Salesforce, Shibboleth, SimpleSAMLphp, OpenAM, Centrify, Ping, RSA, IBM, Oracle, OneLogin, Bitium, WSO2, NetIQ etc.

Login with Salesforce Plugin acts as a SAML 2.0 Service Provider which can be configured to establish the trust between the plugin and a SAML 2.0 capable Identity Providers to securely authenticate the user to the Wordpress site.

WordPress Multi-Site Environment and ability to configure Multiple IDP's against wordpress as service provider is also supported in premium plugin.

This plugin is also compatible with WordPress OAuth Server plugin. You can now make your WordPress site an OAuth Server and have the users authenticate themselves with your SAML-compliant IDPs like ADFS, Azure AD, OneLogin instead of their WordPress credentials.

If you require any Single Sign On (SSO) application or need any help with installing this plugin, please feel free to email us at info@xecurify.com or <a href="http://miniorange.com/contact">Contact us</a>.

= Free Version Features =
*   Unlimited authentication with your SAML 2.0 compliant Identity Providers like ADFS, Azure AD, Okta, Salesforce, Shibboleth, SimpleSAMLphp, OpenAM, Centrify, Ping, RSA, IBM, Oracle, OneLogin, Bitium, WSO2, NetIQ etc.
*   Automatic user registration after login if the user is not already registered with your site.
*   Use Widgets to easily integrate the login link with your Wordpress site.
*   Use Basic Attribute Mapping feature to map wordpress user profile attributes like First Name, Last Name to the attributes provided by your IDP.
*   Select default role to assign to users on auto registration.
*   Force authentication with your IDP on each login attempt.

= Premium Version Features =
*   All the Free version features.
*   **SAML Single Logout [Premium]** - Support for SAML Single Logout (Works only if your IDP supports SLO).
*   **Auto-redirect to IDP [Premium]** - Auto-redirect to your IDP for authentication without showing them your WordPress site's login page.
*   **Protect Site [Premium]** - Protect your complete site. Have users authenticate themselves before they could access your site content.
*   **Advanced Attribute Mapping [Premium]** - Use this feature to map your IDP attributes to your WordPress site attributes like Username, Email, First Name, Last Name, Group/Role, Display Name.
*   **Advanced Role Mapping [Premium]** - Use this feature to assign WordPress roles your users based on the group/role sent by your IDP.
*   **Short Code [Premium]** - Use Short Code (PHP or HTML) to place the login link wherever you want on the site.
*   **Reverse-proxy Support [Premium]** - Support for sites behind a reverse-proxy.
*   **Select Binding Type [Premium]** - Select HTTP-Post or HTTP-Redirect binding type to use for sending SAML Requests.
*   **Integrated Windows Authentication [Premium]** - Support for Integrated Windows Authentication (IWA)
*   **Step-by-step Guides [Premium]** - Use step-by-step guide to configure your Identity Provider like ADFS, Centrify, Google Apps, Okta, OneLogin, Salesforce, SimpleSAMLphp, Shibboleth, WSO2, JBoss Keycloak, Oracle.
*   **WordPress Multi-site Support [Premium]** - Multi-Site environment is one which allows multiple subdomains / subdirectories to share a single installation. With multisite premium plugin, you can configure the IDP in minutes for all your sites in a network. While, if you have basic premium plugin, you have to do plugin configuration on each site individually as well as multiple service provider configuration's in the IDP.

    For Example - If you have 1 main site with 3 subsites. Then, you have to configure the plugin 3 times on each site as well as 3 service provider configurations in your IDP. Instead, with multisite premium plugin. You have to configure the plugin only once on main network site as well as only 1 service provider configuration in the IDP.


*   **Multiple SAML IDPs Support [Premium]** - We now support configuration of Multiple IDPs in the plugin to authenticate the different group of users with different IDP's. You can give access to users by users to IDP mapping (which IDP to use to authenticate a user) is done based on the domain name in the user's email. (This is a **PREMIUM** feature with separate licensing. Contact us at info@xecurify.com to get licensing plans for this feature.)

* If you are looking for an Identity Provider,you can try out <a href="https://idp.miniorange.com">miniOrange On-Premise IdP</a>.

= Website - =
Check out our website for other plugins <a href="http://miniorange.com/plugins" >http://miniorange.com/plugins</a> or <a href="https://wordpress.org/plugins/search.php?q=miniorange" >click here</a> to see all our listed WordPress plugins.
For more support or info email us at info@xecurify.com or <a href="http://miniorange.com/contact" >Contact us</a>. You can also submit your query from plugin's configuration page.

== Installation ==

= From your WordPress dashboard =
1. Visit `Plugins > Add New`.
2. Search for `Login with Salesforce`. Find and Install `Login with Salesforce`.
3. Activate the plugin from your Plugins page.

= From WordPress.org =
1. Download Login with Salesforce plugin.
2. Unzip and upload the `login-with-salesforce` directory to your `/wp-content/plugins/` directory.
3. Activate Login with Salesforce from your Plugins page.

== Frequently Asked Questions ==

= I am not able to configure the Identity Provider with the provided settings =
Please email us at info@xecurify.com or <a href="http://miniorange.com/contact" >Contact us</a>. You can also submit your app request from plugin's configuration page.

= For any query/problem/request =
Visit Help & FAQ section in the plugin OR email us at info@xecurify.com or <a href="http://miniorange.com/contact">Contact us</a>. You can also submit your query from plugin's configuration page.

== Screenshots ==

1. Configure your Wordpress as Service Provider.
2. Gather Metadata for your Identity Provider.
3. Configure Attribute/Role Mapping for Users in Wordpress.
4. Add widget to enable Single Sign-on.
5. Plugin-tour which guides you through entire plugin setup.
6. Addons which extend plugin functionality.

== Changelog ==

= 1.0.2 =
* Compatibility with WordPress 5.8
* Added banner for end of year sale

= 1.0.1 =
* Bug fixes
* Compatibility with WordPress 5.3

= 1.0.0 =
* this is the first release.

== Upgrade Notice ==

= 1.0.2 =
* Compatibility with WordPress 5.8
* Added banner for end of year sale

= 1.0.1 =
* Bug fixes
* Compatibility with WordPress 5.3

= 1.0.0 =
*Plugin released
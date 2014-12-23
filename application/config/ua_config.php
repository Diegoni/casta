<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
|-----------------------------------------------------------------------------
| User Authenication Configuration
|-----------------------------------------------------------------------------
|
| 'login_expiration' = number of seconds inactive before a login expires.
| 'remember_me_life' = number of seconds a "Remember Me" cookie lasts
| 'direct_database' = true, si se quiere usar login de base de datos 
*/

$config['login_expiration'] = 1200;		// 20 minutes
$config['remember_me_life'] = 7257600;	// 12 weeks
$config['direct_database'] 	= true;

/*
|-----------------------------------------------------------------------------
| User Authorization Roles - Access Control Lists
|-----------------------------------------------------------------------------
|
| $config['ua_role_rolename_allow'] = '';
| $config['ua_role_rolename_deny']  = '';
|
| Space-separated list of usernames/groupnames
| Groups delimited by "@"
|
| These are examples - You should create 
| your own user-groups and roles
|
| If a role defined here, it exists for authorize
*/

// this is an example of defining a role of "user"
$config['ua_role_user_allow']    = '@user @member @editor @manager @admin @super';
$config['ua_role_user_deny']     = '';

$config['ua_suscripciones_user_allow']    = '@suscripciones @admin @super';
$config['ua_suscripciones_user_deny']     = '';

// this is an example of defining a role of "member"
$config['ua_role_member_allow']  = '@member @editor @manager @admin @super';
$config['ua_role_member_deny']   = '';

// this is an example of defining a role of "editor"
$config['ua_role_editor_allow']  = '@editor @manager @admin @super';
$config['ua_role_editor_deny']   = '';

// this is an example of defining a role of "manager"
$config['ua_role_manager_allow'] = '@manager @admin @super @super';
$config['ua_role_manager_deny']  = '';

// this is an example of defining a role of "admin"
$config['ua_role_admin_allow']   = '@admin @super';
$config['ua_role_admin_deny']    = '';

$config['ua_role_super_allow']   = '@super';
$config['ua_role_super_deny']    = '';

/*
|-----------------------------------------------------------------------------
| UserAuth Mini-App configure languages
|-----------------------------------------------------------------------------
|
| Mapping browser's primary language id to language name 
| Mapping language name to a character set
| Mapping language name to language id
|
*/

// If FALSE, disables language detect & user select
$config['ua_multi_language']  = TRUE;

// Mini-App's views/template needs encoding setting

$config['ua_language_en']     = 'english';
$config['ua_charset_english'] = 'utf8';
$config['ua_lang_english']    = 'en';

$config['ua_language_es']     = 'spanish';
$config['ua_charset_spanish'] = 'utf8';
$config['ua_lang_spanish']    = 'es';

?>
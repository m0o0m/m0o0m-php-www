<?php

if( !defined('PROPER_START') )
{
	header("HTTP/1.0 403 Forbidden");
	exit;
}

$name = htmlspecialchars($_POST['name']);
$user = htmlspecialchars($_POST['user']);
$token_type = htmlspecialchars($_POST['type']);

switch( $token_type )
{
	case 'user':
		api::send('token/add', array('name'=>$name, 'user'=>$user, 'lease'=>'never', 'grants'=>'ACCESS,SELF_SELECT,SELF_UPDATE,SELF_DELETE,SELF_GRANT_SELECT,SELF_GROUP_SELECT,SELF_GROUP_DELETE,SELF_TOKEN_INSERT,SELF_TOKEN_SELECT,SELF_TOKEN_UPDATE,SELF_TOKEN_DELETE,SELF_QUOTA_SELECT,SELF_TOKEN_GRANT_DELETE,SELF_TOKEN_GRANT_INSERT,SELF_SITE_SELECT,SELF_SITE_DELETE,SELF_SITE_INSERT,SELF_SITE_UPDATE,SELF_DOMAIN_INSERT,SELF_DOMAIN_SELECT,SELF_DOMAIN_DELETE,SELF_DOMAIN_UPDATE,SELF_DATABASE_INSERT,SELF_DATABASE_UPDATE,SELF_DATABASE_DELETE,SELF_DATABASE_SELECT,SELF_SUBDOMAIN_SELECT,SELF_SUBDOMAIN_UPDATE,SELF_SUBDOMAIN_INSERT,SELF_SUBDOMAIN_DELETE,SELF_ACCOUNT_DELETE,SELF_ACCOUNT_INSERT,SELF_ACCOUNT_SELECT,SELF_ACCOUNT_UPDATE,SELF_APP_INSERT,SELF_APP_DELETE,SELF_APP_UPDATE,SELF_APP_SELECT,SELF_MESSAGE_INSERT,SELF_MESSAGE_UPDATE,SELF_MESSAGE_SELECT,SELF_MESSAGE_DELETE,SELF_LOG_SELECT,SELF_LOG_INSERT,SELF_LOG_UPDATE,SELF_LOG_DELETE,SELF_BACKUP_SELECT,SELF_BACKUP_UPDATE,SELF_BACKUP_INSERT,SELF_BACKUP_DELETE'));
	break;
	case 'admin':
		api::send('group/user/add', array('user'=>$user, 'groups'=>'ADMIN'));
		api::send('token/add', array('name'=>$name, 'user'=>$user, 'lease'=>'never', 'grants'=>'LOG_SELECT,LOG_INSERT,LOG_UPDATE,LOG_DELETE,BACKUP_SELECT,BACKUP_UPDATE,BACKUP_INSERT,BACKUP_DELETE,NEWS_SELECT,NEWS_UPDATE,NEWS_DELETE,NEWS_INSERT,ACCOUNT_DELETE,ACCOUNT_INSERT,ACCOUNT_SELECT,ACCOUNT_UPDATE,APP_DELETE,APP_INSERT,APP_SELECT,APP_UPDATE,MESSAGE_INSERT,MESSAGE_SELECT,MESSAGE_UPDATE,MESSAGE_DELETE,DATABASE_DELETE,DATABASE_INSERT,DATABASE_SELECT,DATABASE_UPDATE,DOMAIN_DELETE,DOMAIN_INSERT,DOMAIN_SELECT,DOMAIN_UPDATE,GRANT_DELETE,GRANT_GROUP_DELETE,GRANT_GROUP_INSERT,GRANT_GROUP_SELECT,GRANT_INSERT,GRANT_SELECT,GRANT_TOKEN_DELETE,GRANT_TOKEN_INSERT,GRANT_TOKEN_SELECT,GRANT_UPDATE,GRANT_USER_DELETE,GRANT_USER_INSERT,GRANT_USER_SELECT,GROUP_DELETE,GROUP_INSERT,GROUP_SELECT,GROUP_UPDATE,GROUP_USER_DELETE,GROUP_USER_INSERT,GROUP_USER_SELECT,QUOTA_DELETE,QUOTA_INSERT,QUOTA_SELECT,QUOTA_UPDATE,QUOTA_USER_DELETE,QUOTA_USER_INSERT,QUOTA_USER_SELECT,QUOTA_USER_UPDATE,REGISTRATION_DELETE,REGISTRATION_INSERT,REGISTRATION_SELECT,SITE_DELETE,SITE_INSERT,SITE_SELECT,SITE_UPDATE,SUBDOMAIN_DELETE,SUBDOMAIN_INSERT,SUBDOMAIN_SELECT,SUBDOMAIN_UPDATE,TOKEN_DELETE,TOKEN_INSERT,TOKEN_SELECT,TOKEN_UPDATE,USER_DELETE,USER_INSERT,USER_SELECT,USER_UPDATE,ACCESS,SELF_SELECT,SELF_UPDATE,SELF_DELETE,SELF_GRANT_SELECT,SELF_GROUP_SELECT,SELF_GROUP_DELETE,SELF_TOKEN_INSERT,SELF_TOKEN_SELECT,SELF_TOKEN_UPDATE,SELF_TOKEN_DELETE,SELF_QUOTA_SELECT,SELF_TOKEN_GRANT_DELETE,SELF_TOKEN_GRANT_INSERT,SELF_SITE_SELECT,SELF_SITE_DELETE,SELF_SITE_INSERT,SELF_SITE_UPDATE,SELF_DOMAIN_INSERT,SELF_DOMAIN_SELECT,SELF_DOMAIN_DELETE,SELF_DOMAIN_UPDATE,SELF_DATABASE_INSERT,SELF_DATABASE_UPDATE,SELF_DATABASE_DELETE,SELF_DATABASE_SELECT,SELF_SUBDOMAIN_SELECT,SELF_SUBDOMAIN_UPDATE,SELF_SUBDOMAIN_INSERT,SELF_SUBDOMAIN_DELETE,SELF_ACCOUNT_DELETE,SELF_ACCOUNT_INSERT,SELF_ACCOUNT_SELECT,SELF_ACCOUNT_UPDATE,SELF_APP_INSERT,SELF_APP_DELETE,SELF_APP_UPDATE,SELF_APP_SELECT,SELF_MESSAGE_INSERT,SELF_MESSAGE_UPDATE,SELF_MESSAGE_SELECT,SELF_MESSAGE_DELETE,SELF_LOG_SELECT,SELF_LOG_INSERT,SELF_LOG_UPDATE,SELF_LOG_DELETE,SELF_BACKUP_SELECT,SELF_BACKUP_UPDATE,SELF_BACKUP_INSERT,SELF_BACKUP_DELETE'));
	break;
	case 'blank':
		api::send('token/add', array('name'=>$name, 'user'=>$user, 'lease'=>'never'));
	break;
}

if( isset($_GET['redirect']) )
	template::redirect($_GET['redirect']);
else
	template::redirect('/admin/users/detail?id='.$user.'#tokens');

?>
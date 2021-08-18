//<?/////////////////////////////////////////////////////////
// Plugin mem_self_register
// Author: Michael Manfre (http://manfre.net/)
////////////////////////////////////////////////////////////

 // TXP 4.6 tag registration
if (class_exists('\Textpattern\Tag\Registry')) {
	Txp::get('\Textpattern\Tag\Registry')->register('mem_self_register_form');
	Txp::get('\Textpattern\Tag\Registry')->register('mem_self_register');
  Txp::get('\Textpattern\Tag\Registry')->register('mem_profile');
	Txp::get('\Textpattern\Tag\Registry')->register('mem_self_password_reset_form');
	Txp::get('\Textpattern\Tag\Registry')->register('mem_self_change_password_form');
	

}

require_plugin('mem_form');
include_once txpath.'/lib/PasswordHash.php';

if (@txpinterface == 'admin')
{
	register_callback('mem_self_auto_enable', 'plugin_lifecycle.mem_self_register', 'installed');
}

/** Automatically enable plugin when installed */
function mem_self_auto_enable($event, $step)
{
	$plugin = substr($event, strlen('plugin_lifecycle.'));

	$prefix = 'mem_self_register';
	if (strncmp($plugin, $prefix, strlen($prefix)) == 0)
	{
		safe_update('txp_plugin', "status = 1", "name = '" . doSlash($plugin) . "'");
	}
}



// MLP
global $mem_self_lang;
if (!is_array($mem_self_lang))
{
	$mem_self_lang = array(
		'account_created_mail_failed'	=>	'Your account has been created, but an error was encountered while attempting to email your the account information. Please contact the site administrator for help.',
		'admin_name'		=>	'Admin Name',
		'admin_email'		=>	'Admin Email',
		'error_adding_new_author'	=>	'Error adding new author',
		'greeting'			=>	'Bonjour ',
		'invalid_form_tags' =>	'Invalid form tags provided to form "{form}"',
		'log_in_at'			=>	'Log in at {url}',
		'log_added_pref'	=>	'Added pref {name}',
		'log_pref_failed'	=>	'Failed to add pref {name}. {error}',
		'log_pref_exists'	=>	'Pref {name} is already installed. Current value is "{value}"',
		'log_col_added'		=>	'Added column {name} to user table {table}',
		'log_col_failed'	=>	'Failed to add column {name} to table {table}. {error}',
		'log_col_exists'	=>	'Table {table} already has column {name}',
		'log_form_added'	=>	'Added form {name}',
		'log_form_failed'	=>	'Failed to add form {name}. {error}<br>You need to manually create a form template. Here is an example.',
		'log_form_found'	=>	'Found form {name}. Skipping installation of default form.',
		'log_xmpl_tag'		=>	'Example tag to use in your page template.',
		'mail_sorry'		=>	'Our mail system is currently down. Please try again later.',
		'missing_form_field'	=>	'The required form field {name} is empty or missing.',
		'password_changed'	=>	'Password changed',
		'password_change_failed'	=>	'Failed to change password',
		'password_invalid'	=> 'Mot de passe erroné',
		'password_sent_to'	=>	'Mot de passe envoyé à {email}',
		'saved_user_profile'	=>	'Saved User Profile',
		'saved_user_profile_failed'	=>	'Failed to Save User Profile',
		'user_exists'		=>	'Username already exists. Please try another name',
		'user_not_found'	=>	'A user account could not be found with the provided information.',
		'your_login_info'	=>	'Your Login Info',
		'your_new_password'	=>	'Votre nouveau mot de passe',
		'your_password_is'	=>	'Votre mot de passe est {password}',
		'password_reset_confirmation_request_sent' => 'Demande de réinitialisation envoyé par mail',
		'password_reset_confirmation' => 'Confirmez la demande de réinitialisation de votre mot de passe',
		
	);
}

define( 'MEM_SELF_PREFIX' , 'mem_self' );

register_callback( 'mem_self_enumerate_strings' , 'l10n.enumerate_strings' );
function mem_self_enumerate_strings($event , $step='' , $pre=0)
{
	global $mem_self_lang;
	$r = array	(
				'owner'		=> 'mem_self_register',			#	Change to your plugin's name
				'prefix'	=> MEM_SELF_PREFIX,				#	Its unique string prefix
				'lang'		=> 'en-gb',						#	The language of the initial strings.
				'event'		=> 'public',					#	public/admin/common = which interface the strings will be loaded into
				'strings'	=> $mem_self_lang,				#	The strings themselves.
				);
	return $r;
}
function mem_self_gTxt($what,$args = array())
{
	global $mem_self_lang, $textarray;

	$key = strtolower( MEM_SELF_PREFIX . '-' . $what );

	if (isset($textarray[$key]))
	{
		$str = $textarray[$key];
	}
	else
	{
		$key = strtolower($what);

		if (isset($mem_self_lang[$key]))
			$str = $mem_self_lang[$key];
		elseif (isset($textarray[$key]))
			$str = $textarray[$key];
		else
			$str = $what;
	}

	if( !empty($args) )
		$str = strtr( $str , $args );

	return $str;
}

global $event, $levels;

if (txpinterface == 'public' or $event != 'admin')
{
	if (file_exists( txpath.'/lib/txplib_admin.php' ))
	{
		require_once txpath.'/lib/txplib_admin.php';
	}

	require_once txpath.'/include/txp_admin.php';

	function mem_self_get_groups()
	{
		// global var is deprecated as of v0.9.10
		global $levels;

		// always use core's list if it exists to allow other
		// plugins to change it.
		if (function_exists('get_groups'))
		{
			$levels = get_groups();
		}
		else if (empty($levels))
		{
			// copied from txp_admin.php
			// if core doesn't provide, use legacy default
			$levels = array(
				0 => mem_self_gTxt('none'),
				6 => mem_self_gTxt('designer'),
				5 => mem_self_gTxt('freelancer'),
				4 => mem_self_gTxt('staff_writer'),
				3 => mem_self_gTxt('copy_editor'),
				2 => mem_self_gTxt('managing_editor'),
				1 => mem_self_gTxt('publisher')
			);
		}

		return $levels;
	}

	if (empty($levels))
	{
		// populate global $levels to maintain backwards compatibility
		// with any plugins that are using it.
		// NOTE: $levels is deprecated as of v0.9.10 and will be removed soon
		mem_self_get_groups();
	}

//-------------------------------------------------------------
	if (!function_exists('priv_levels')) {
		function priv_levels($item, $var) {
			$groups = mem_self_get_groups();

			return selectInput($item, $groups, $var);
		}
	}
}


global $mem_self, $prefs;

$mem_self = array(
				'admin_email'		=>	'',
				'admin_name'		=>	'',
				'admin_bcc'			=>	'0',
				'new_user_priv'		=>	'0',
				'status'			=>	false,
				'status_message'	=>	'You are already registered.',
				'email_message'		=>	''
			);

$mem_self['admin_email'] = isset($prefs['mem_self_admin_email']) ? $prefs['mem_self_admin_email'] : '';
$mem_self['admin_name'] = isset($prefs['mem_self_admin_name']) ? $prefs['mem_self_admin_name'] : '';
$mem_self['new_user_priv'] = isset($prefs['mem_self_new_user_priv']) ? $prefs['mem_self_new_user_priv'] : '0';
$mem_self['admin_bcc'] = isset($prefs['mem_self_admin_bcc']) ? $prefs['mem_self_admin_bcc'] : '0';

if ( @txpinterface == 'admin' ) {
	add_privs('self-reg','1');

	register_callback('mem_self_register','self-reg','', 1);
	if ($event=='self-reg') {
		// fake tabs when using them. Silences warnings from pageTop()
		register_tab('admin','self-reg','self-reg');

		include_once txpath.'/publish/taghandlers.php';
	}

	function mem_self_register($event, $step)
	{
		global $prefs;

		extract($prefs);

		if ($event!='self-reg')
			return;

		pageTop('Self Registration','');

		if ($step == 'install') {
			echo mem_self_register_install();
		} else if ($step=='preinstall') {
			$mem_xtra_columns	= mem_get_extra_user_columns();

			$mem_admin_name = !empty($mem_admin_name) ? $mem_admin_name : 'Admin';
			$mem_admin_email = !empty($mem_admin_email) ? $mem_admin_email : 'Admin@yourdomain.com';
			$mem_new_use_priv = !empty($mem_new_use_priv) ? $mem_new_use_priv : '0';
			$mem_self_admin_bcc = !empty($mem_self_admin_bcc) ? $mem_self_admin_bcc : '0';

			$use_ign_input = '';
			// is ign_password_protect loaded on the system?
			if (load_plugin('ign_password_protect') || (isset($mem_use_ign_db) && $mem_use_ign_db)) {
				$mem_use_ign_db = !empty($mem_use_ign_db) ? $mem_use_ign_db : '0';
				$use_ign_input = yesnoradio('use_ign_db',$mem_use_ign_db);
			}

			$xtra_columns = mem_get_extra_user_columns();
			$has_address = in_array('address',$xtra_columns);
			$has_phone = in_array('phone',$xtra_columns);

			$add_address_input = yesnoradio('add_address','0');
			$add_phone_input = yesnoradio('add_phone','0');

			echo form(
				eInput('self-reg').sInput('install').
				startTable('edit') .
					tr( fLabelCell('admin_name')	. tda(fInput('text','admin_name',$mem_admin_name,'edit')) ) .
					tr( fLabelCell('admin_email')	. tda(fInput('text','admin_email',$mem_admin_email,'edit')) ) .
					tr( fLabelCell('admin_bcc')	. tda(yesnoRadio('admin_bcc',$mem_self_admin_bcc)) ) .
					tr( fLabelCell('new_user_priv')	. tda(priv_levels('new_user_priv',$mem_new_use_priv)) ) .
					tr( fLabelCell('use_ign_db')	. tda($use_ign_input) ) .
					($has_address ? '' : tr( fLabelCell('add_address_field') . tda($add_address_input) ) ) .
					($has_phone ? '' : tr( fLabelCell('add_phone_field') . tda($add_phone_input) ) ) .
					tr( td() . td( fInput("submit", 'submit',mem_self_gTxt('install'),"Publish"), 2 ) ) .
				endTable()
				);
		} else {
			echo '<div><a href="?event=self-reg&step=preinstall">'.mem_self_gTxt('install').'</a></div>';
		}
	}


	function mem_self_register_install()
	{
		global $mem_self;

		extract(doSlash(gpsa(array(
			'admin_email',
			'admin_name',
			'new_user_priv',
			'use_ign_db',
			'add_address',
			'add_phone'
		))));

		if (!isset($new_user_priv) || empty($new_user_priv)) $new_user_priv = '0';

		$log = array();

		if (!($rs=safe_field('val,html','txp_prefs',"name='mem_self_use_ign_db'"))) {
			if ( set_pref('mem_self_use_ign_db',$use_ign_db,'self_reg',1,0,'yesnoradio')) {
				$log[] = mem_self_gTxt('log_added_pref', array('{name}'=>'mem_self_use_ign_db'));
			} else {
				$log[] = mem_self_gTxt('log_pref_failed', array('{name}'=>'mem_self_use_ign_db','{error}'=>mysql_error()));
			}
		} else {
			if ($rs['html'] != 'yesnoradio') {
				safe_update('txp_prefs',"html='yesnoradio'","name='mem_self_use_ign_db'");
			}
			$log[] = mem_self_gTxt('log_pref_exists', array('{name}'=>'mem_self_use_ign_db','{value}'=>$rs));
		}

		$user_table = mem_get_user_table_name();

		$xtra_columns = mem_get_extra_user_columns();
		if ($add_address) {
			if (!in_array('address',$xtra_columns)) {
				if (safe_alter($user_table,"ADD `address` VARCHAR( 128 )")) {
					$log[] = mem_self_gTxt('log_col_added', array('{name}'=>'address','{table}'=>$user_table));
				} else {
					$log[] = mem_self_gTxt('log_col_failed', array('{name}'=>'address','{table}'=>$user_table,'{error}'=>mysql_error()));
				}
			} else {
				$log[] = mem_self_gTxt('log_col_exists', array('{name}'=>'address','{table}'=>$user_table));
			}
		}
		if ($add_phone) {
			if (!in_array('phone',$xtra_columns)) {
				if (safe_alter($user_table,"ADD `phone` VARCHAR( 32 )")) {
					$log[] = mem_self_gTxt('log_col_added', array('{name}'=>'phone','{table}'=>$user_table));
				} else {
					$log[] = mem_self_gTxt('log_col_failed', array('{name}'=>'phone','{table}'=>$user_table,'{error}'=>mysql_error()));
				}
			} else {
				$log[] = mem_self_gTxt('log_col_exists', array('{name}'=>'phone','{table}'=>$user_table));
			}
		}

		if (!($rs=safe_field('val','txp_prefs',"name='mem_self_admin_email'"))) {
			if ( set_pref('mem_self_admin_email',$admin_email,'self_reg',1)) {
				$log[] = mem_self_gTxt('log_added_pref', array('{name}'=>'mem_self_admin_email'));
			} else {
				$log[] = mem_self_gTxt('log_pref_failed', array('{name}'=>'mem_self_admin_email','{error}'=>mysql_error()));
			}
		} else {
			$log[] = mem_self_gTxt('log_pref_exists', array('{name}'=>'mem_self_admin_email','{value}'=>$rs));
		}
		if (!($rs=safe_field('val','txp_prefs',"name='mem_self_admin_name'"))) {
			if ( set_pref('mem_self_admin_name',$admin_name,'self_reg',1)) {
				$log[] = mem_self_gTxt('log_added_pref', array('{name}'=>'mem_self_admin_name'));
			} else {
				$log[] = mem_self_gTxt('log_pref_failed', array('{name}'=>'mem_self_admin_name','{error}'=>mysql_error()));
			}
		} else {
			$log[] = mem_self_gTxt('log_pref_exists', array('{name}'=>'mem_self_admin_name','{value}'=>$rs));
		}
		if (!($rs=safe_row('val,html','txp_prefs',"name='mem_self_new_user_priv'"))) {
			if ( set_pref('mem_self_new_user_priv',$new_user_priv,'self_reg',1,0,'priv_levels')) {
				$log[] = mem_self_gTxt('log_added_pref', array('{name}'=>'mem_self_new_user_priv'));
				$mem_self['new_user_priv'] = $new_user_priv;
			} else {
				$log[] = mem_self_gTxt('log_pref_failed', array('{name}'=>'mem_self_newuser_priv','{error}'=>mysql_error()));
			}
		} else {
			safe_update('txp_prefs',"html='priv_levels'","name='mem_self_new_user_priv'");

			$log[] = mem_self_gTxt('log_pref_exists', array('{name}'=>'mem_self_new_user_priv','{value}' => $rs));
		}
		if (!($rs=safe_field('val','txp_prefs',"name='mem_self_admin_bcc'"))) {
			if ( set_pref('mem_self_admin_bcc','0','self_reg',1,'yesnoradio')) {
				$log[] = mem_self_gTxt('log_added_pref', array('{name}'=>'mem_self_admin_bcc'));
			} else {
				$log[] = mem_self_gTxt('log_pref_failed', array('{name}'=>'mem_self_admin_bcc','{error}'=>mysql_error()));
			}
		} else {
			$log[] = mem_self_gTxt('log_pref_exists', array('{name}'=>'mem_self_admin_bcc','{value}'=>$rs));
		}

		// create default registration form
		$form_html = <<<EOF
	<fieldset>
	<legend>Register</legend>
		<txp:mem_form_text name="RealName" label="Full Name" /><br />
		<br />

		<txp:mem_form_text name="name" label="Username" /><br />
		<br />

		<txp:mem_form_email name="email" label="E-Mail" /><br />
		<br />

		<txp:mem_form_submit />
	</fieldset>
EOF;

		$form = fetch('Form','txp_form','name','self_register_form');
		if (!$form) {
			if (safe_insert('txp_form',"name='self_register_form',type='misc',Form='{$form_html}'")) {
				$log[] = mem_self_gTxt('log_form_added', array('{name}'=>'self_register_form'));
			} else {
				$log[] = mem_self_gTxt('log_form_failed', array('{name}'=>'self_register_form','{error}'=>mysql_error())).br.
					'<textpattern style="width:300px;height:150px;">'.htmlspecialchars($form_html).'</textarea>';
			}
		} else {
			$log[] = mem_self_gTxt('log_form_found', array('{name}'=>'self_register_form'));
		}

		// create default successful registration form to show the user
		$form_html = <<<EOF
<h3>Account Created</h3>
<p>An email containing your password has been sent to <txp:mem_profile var="email" />.</p>
EOF;

		$form = fetch('Form','txp_form','name','self_register_success');
		if (!$form) {
			if (safe_insert('txp_form',"name='self_register_success',type='misc',Form='{$form_html}'")) {
				$log[] = mem_self_gTxt('log_form_added', array('{name}'=>'self_register_success'));
			} else {
				$log[] = mem_self_gTxt('log_form_failed', array('{name}'=>'self_register_success','{error}'=>mysql_error())).br.
					'<textpattern style="width:300px;height:150px;">'.htmlspecialchars($form_html).'</textarea>';
			}
		} else {
			$log[] = mem_self_gTxt('log_form_found', array('{name}'=>'self_register_success'));
		}

		// create default successful registration email form
		$form_html = <<<EOF
Dear <txp:mem_name />,

Thank you for registering at <txp:mem_siteurl />.

Your login name: <txp:mem_username />
Your password: <txp:mem_password />

If you have any questions please reply to this email address.

Sincerely,
<txp:mem_admin_name />
<txp:mem_admin_email />
EOF;

		$form = fetch('Form','txp_form','name','self_register_email');
		if (!$form) {
			if (safe_insert('txp_form',"name='self_register_email',type='misc',Form='{$form_html}'")) {
				$log[] = mem_self_gTxt('log_form_added', array('{name}'=>'self_register_email'));
			} else {
				$log[] = mem_self_gTxt('log_form_failed', array('{name}'=>'self_register_email','{error}'=>mysql_error())).br.
					'<textpattern style="width:300px;height:150px;">'.htmlspecialchars($form_html).'</textarea>';
			}
		} else {
			$log[] = mem_self_gTxt('log_form_found', array('{name}'=>'self_register_email'));
		}

		$tag_help = '<txp:mem_self_register_form form="self_register_form" />';
		$log[] = mem_self_gTxt('log_xmpl_tag').br.
			'<textarea style="width:400px;height:40px;">'.htmlspecialchars($tag_help).'</textarea>';

		return doWrap($log,'ul','li');
	}
}


register_callback('mem_self_register_form_submit','mem_form.submit');

function mem_self_register_form($atts,$thing='')
{
	global $prefs, $sitename, $production_status;

	if (!is_callable('mail'))
	{
		return ($production_status == 'live') ?
			mem_self_gTxt('mail_sorry') :
			gTxt('warn_mail_unavailable');
	}

	extract(lAtts(array(
		'form'		=> '',
		'email_form'	=> '',
		'from'		=> $prefs['mem_self_admin_email'],
		'reply'		=> '',
		'subject'	=> '['.$sitename.'] '. mem_self_gTxt('your_login_info'),
		'login_url'	=> rtrim(hu,'/').'/textpattern/index.php',
	),$atts,false));

	if (!empty($form)) {
		$thing = fetch_form($form);
		unset($atts['form']);
	}

	foreach(array('from','reply','subject','login_url','email_form') as $a) {
		$thing .= '<txp:mem_form_secret name="'.$a.'" value="'.$$a.'" />';
		unset($atts[$a]);
	}

	return mem_form($atts+array('type'=>'mem_self_register'),$thing);
}

// -------------------------------------------------------------
function mem_self_register_form_submit()
{
	global $prefs, $mem_self, $sitename, $mem_profile, $mem_form_type, $mem_form_values, $mem_form_thanks_form;

	if ($mem_form_type != 'mem_self_register') return;

  // Rabah : honeypot adresse or mobile field
  if (isset($_POST['adresse']) OR isset($_POST['mobile'])) {
    if (($_POST['adresse'] != "") OR ($_POST['mobile'] != "")) {
      return mem_form_error("DATA Invalide");
    }
  }

	extract($mem_self);

	if (isset($mem_form_values['password']))
	{
		if (isset($mem_form_values['password2']) && $mem_form_values['password'] != $mem_form_values['password2'])
		{
			return mem_form_error(gTxt('passwords_do_not_match'));
		}

		$pw = $mem_form_values['password'];
	}
	else
	{
		$pw = generate_password(10);
	}

	if (!$mem_profile) $mem_profile = array();

	if (array_key_exists('first_name', $mem_form_values))
	{
		$mem_profile['first_name'] = $first_name = $mem_form_values['first_name'];
		$mem_profile['last_name'] = $last_name = $mem_form_values['last_name'];
		$mem_profile['RealName'] = $full_name = $first_name . ' ' . $last_name;
	}
	else
	{
		$mem_profile['RealName'] = $full_name = $mem_form_values['RealName'];
		$name_parts = explode(' ', $full_name, 2);
		$mem_profile['first_name'] = @$name_parts[0];
		$mem_profile['last_name'] = @$name_parts[1];
	}

	$mem_profile['nonce'] = $nonce = md5( uniqid( rand(), true ) );

	$mem_profile['email'] = $email = $mem_form_values['email'];
	$mem_profile['name'] = $username = $mem_form_values['name'];
	$mem_profile['privs'] = $new_user_priv;

	if (safe_row('user_id', mem_get_user_table_name(), "name = '".doSlash($username)."'")) {
		return mem_form_error(mem_self_gTxt('user_exists'));
	}

	$xtra_columns = mem_get_extra_user_columns();

	foreach($xtra_columns as $c) {
		$c_name = trim( $c['Field'] );

		if (isset($mem_form_values[$c_name]))
			$mem_profile[$c_name] = $mem_form_values[$c_name];
	}

	$xtra = mem_get_extra_user_columns_insert_string();

	callback_event('mem_self_register.new_user', 'pre-created', 0, $mem_profile);

	$phpass = new PasswordHash(PASSWORD_COMPLEXITY, PASSWORD_PORTABILITY);

	$rs = safe_insert(
		mem_get_user_table_name(),
		"privs    = '".doSlash($new_user_priv)."',
		 name     = '".doSlash($username)."',
		 email    = '".doSlash($email)."',
		 RealName = '".doSlash($full_name)."',
		 pass     =  '" . doSlash($phpass->HashPassword($pw)) . "',
		 nonce    = '".doSlash($nonce)."'" . $xtra
	);

	if ($rs) {
		$mem_profile['user_id'] = $rs;
		$mem_profile['last_access'] = 0;

		callback_event('mem_self_register.new_user', 'created', 0, $mem_profile);

		$message = @fetch_form($mem_form_values['email_form']);

		if (empty($message)) {
			$message = <<<EOF
{RealName},
	You have successfully registered at {sitename}. You can login at {login_url}.

Username: {username}
Password: {password}

Regards,
{admin_name}
EOF;
		}

		if (!empty($message)) {
			$vals = $mem_form_values;
			$vals['sitename']	= $sitename;
			$vals['admin_name']	= $prefs['mem_self_admin_name'];
			$vals['admin_email']	= $vals['from'];
			$vals['password']		= $pw;
			$vals['siteurl']		= hu;
			$vals['username']		= $vals['name'];
			$vals['RealName'] = empty($vals['RealName']) ? $mem_profile['RealName'] : $vals['RealName'];

			foreach ($vals as $a=>$b) {
				$message = str_ireplace('<txp:mem_'.$a.' />', $b, $message);
				$message = str_ireplace('{'.$a.'}', $b, $message);
				$mem_form_thanks_form = str_ireplace('<txp:mem_'.$a.' />', $b, $mem_form_thanks_form);
				$mem_form_thanks_form = str_ireplace('{'.$a.'}', $b, $mem_form_thanks_form);
			}

			$message = parse($message);
			$to = $mem_profile['email'];
			$from = $mem_form_values['from'];
			$reply = $mem_form_values['reply'];
			$subject = $mem_form_values['subject'];

			if ($mem_self['admin_bcc'])
			{
				$sep = !IS_WIN() ? "\n" : "\r\n"; // RABAH
				$from .= $sep . 'Bcc: ' . $from;
			}

			$sent = mem_form_mail($from,$reply,$to,$subject,$message);

			$mem_self['email_status'] = $sent;
			$mem_self['email_message'] = $message;
			$mem_self['status'] = true;

			if ($sent) {
				$cookietime = time() + (365*24*3600);
				setcookie("txp_self_registered", "1",  $cookietime, "/");

				$mem_self['status_message'] = mem_self_gTxt('password_sent_to', array('email'=>$email));
			} else {
				// failed to send email
				return mem_form_error( mem_self_gTxt('account_created_mail_failed') );
			}
		}
	} else {
		return mem_form_error( mem_self_gTxt('error_adding_new_author') );
	}
}


/** Returns the name of the user table (without PFXS) */
function mem_get_user_table_name() {
	global $prefs;

	extract($prefs);

	$table_name = 'txp_users';

	if (isset($mem_self_use_ign_db) && $mem_self_use_ign_db == '1') {

		if (isset($ign_use_custom) && $ign_use_custom=='1') {

			if (isset($ign_user_db) && !empty($ign_user_db))
				$table_name = $ign_user_db;
		}
	}

	$new_name = callback_event('mem_self_register.get_user_table', '', 0, $table_name);
	return empty($new_name) ? $table_name : $new_name;
}

/** SQL string builder for non-standard fields */
function mem_get_extra_user_columns_insert_string() {
	$xtra_columns = mem_get_extra_user_columns();

	$xtra = '';

	foreach ($xtra_columns as $xcol) {
		$name = $xcol['Field'];
		$type = strtolower($xcol['Type']);
		$val = gps($name);

		if ( strstr($type,'int')
				|| $type=='float'
				|| $type=='decimal'
				|| $type=='double'
				|| $type=='bool' ) {
			// don't quote value
			if (!intval($val))
				$val = ( $val=='yes' || $val=='on' ) ? $val = 1 : $val = 0;

			$xtra .= ", {$name}=" . ($val == false ? '0' : doSlash($val));
		} else {
			// quote value
			$xtra .= ", {$name}='" . doSlash($val) ."'";
		}
	}

	return $xtra;
}

/** Diff user table and return non-standard columns */
function mem_get_extra_user_columns()
{
	static $default_columns = array('user_id','name','pass','RealName','email','privs','last_access','nonce');
	static $xtra_columns = false;

	if (is_array($xtra_columns))
		return $xtra_columns;

	$table_name = mem_get_user_table_name();
	$txpdesc = getRows('describe '.PFX. $table_name);

	$xtra_cols = array();

	$dcols = $default_columns;

	foreach($txpdesc as $r) {
		if ( !in_array($r['Field'], $default_columns) )
			$xtra_cols[] = $r;
	}

	return $xtra_cols;
}

// -------------------------------------------------------------
function mem_self_register_email_message($atts)
{
	global $mem_self;
	return $mem_self['email_message'];
}

// -------------------------------------------------------------
function mem_self_register_status_message($atts)
{
	global $mem_self;
	return $mem_self['status_message'];
}


// -------------------------------------------------------------
function mem_if_message_sent($atts,$thing)
{
	global $mem_self;
	$condition = ($mem_self['email_status']);
	return parse(EvalElse($thing, $condition));
}

// -------------------------------------------------------------
function mem_if_self_registered($atts,$thing)
{
	global $mem_self,$txp_user,$ign_user;
	$condition = ($mem_self['status'] or !empty($_COOKIE['txp_self_registered']) or !empty($txp_user) or !empty($ign_user) );
	return parse(EvalElse($thing, $condition));
}


////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////
// User Side Profile and Password Change Forms
// -------------------------------------------------------------
if (txpinterface != 'admin' and !function_exists('txp_validate')) {
	require_once txpath.'/include/txp_auth.php';
}

function mem_self_password_reset_form($atts,$thing='')
{
	global $prefs, $mem_self, $sitename, $production_status;

	extract(lAtts(array(
		'form'		=> '',
		'form_mail'	=> false,
		'from'		=> $mem_self['admin_email'],
		'reply'		=> '',
		'subject'	=> "[$sitename] ".mem_self_gTxt('password_reset_confirmation_request'),
		'confirm_url'	=> '',
		'new_subject'	=> "[$sitename] ".gTxt('your_new_password'),
		'new_form_mail'	=> false,
		'check_name'	=> 1,
		'check_email'	=> 1
	),$atts,false));

	if (!is_callable('mail'))
	{
		return ($production_status == 'live') ?
			mem_self_gTxt('mail_sorry') :
			gTxt('warn_mail_unavailable');
	}


	if (gps('mem_self_confirm'))
	{
		$user_table = mem_get_user_table_name();

		sleep(3);

		$confirm = pack('H*', gps('mem_self_confirm'));
		$name    = substr($confirm, 5);
		$user = safe_row('*', $user_table, "name = '".doSlash($name)."'");

		if ($user['nonce'] and $confirm === pack('H*', substr(md5($user['nonce']), 0, 10)).$name)
		{
			$email = $user['email'];
			$new_pass = generate_password(10);

			$phpass = new PasswordHash(PASSWORD_COMPLEXITY, PASSWORD_PORTABILITY);
			$hashed_pass = doSlash($phpass->HashPassword($new_pass));

			$rs = safe_update($user_table, "pass = '{$hashed_pass}'", "name = '" . doSlash($name) . "'");

			if ($rs)
			{
				if (!empty($new_form_mail))
				{
					$message = parse_form($new_form_mail);

					$vals = $user;
					$vals['password'] = $new_pass;
					$vals['sitename']	= $sitename;
					$vals['admin_name']	= $prefs['mem_self_admin_name'];
					$vals['admin_email']	= $mem_self['admin_email'];
					$vals['siteurl']		= hu;
					$vals['username']		= empty($vals['username']) ? $name : $vals['username'];
					$vals['RealName']		= $RealName;

					foreach ($vals as $a=>$b) {
						$message = str_ireplace('{'.$a.'}', $b, $message);
						$message = str_ireplace('<txp:mem_'.$a.' />',$b,$message);
					}

				}
				else
				{
					$login_url = hu;

					$message =<<<EOHTML
Bonjour {$name},

Votre mot de passe est: {$new_pass}
Vous pouvez vous connecter sur {$login_url}.
EOHTML;

				}

				if (mem_form_mail($from, $reply, $email, $new_subject, $message))
					return mem_self_gTxt('password_sent_to', array('{email}'=>$email));
				else
					return mem_self_gTxt('mail_sorry');
			}
			else
				return mem_self_gTxt('password_change_failed');
		}
	}

	if (!$check_name and !$check_email)
		return mem_self_gTxt('invalid_form_tags',array('{form}'=>'mem_self_password_reset_form'));

	if (!empty($form)) {
		$thing = fetch_form($form);
		unset($atts['form']);
	}

	$secrets = array('form_mail','from','reply','subject','confirm_url', 'check_name', 'check_email');

	foreach($secrets as $a) {
		$thing .= '<txp:mem_form_secret name="'.$a.'" value="'.$$a.'" />';
		unset($atts[$a]);
	}

	return mem_form($atts + array('type'=>'mem_self_password_reset'),$thing);
}

register_callback('mem_self_password_reset_form_submit','mem_form.submit');

function mem_self_password_reset_form_submit()
{
	global $mem_form_type, $mem_form_values, $mem_profile, $prefs, $sitename;

	if ($mem_form_type != 'mem_self_password_reset')
		return;

	$check_name = $mem_form_values['check_name'];
	$check_email = $mem_form_values['check_email'];

	$where = array();

	if ($check_name) {
		foreach(array('name','p_userid','username') as $n)
		{
			if (isset($mem_form_values[$n])) {
				$name = $mem_form_values[$n];
				break;
			}
		}

		if (!isset($name))
			return mem_self_gTxt('missing_form_field',array('{name}'=>'name'));

		$where[] = "name = '".doSlash($name)."'";
	}

	if ($check_email) {
		$email = @$mem_form_values['email'];

		if (empty($email))
			return mem_self_gTxt('missing_form_field',array('{name}'=>'email'));

		$where[] = "email = '".doSlash($email)."'";
	}

	if (empty($where))
		return mem_self_gTxt('missing_form_field',array('{name}'=>'name'));

	$rs = safe_row('name, email, nonce, RealName', mem_get_user_table_name(), join(' and ',$where));

	if ($rs)
	{
		$url = @$mem_form_values['confirm_url'];
		$url = empty($url) ? hu.'textpattern/index.php' : hu.ltrim($url,'/');
		$url .= (strstr($url, '?')===false) ? '?' : '&';

		extract($rs);

		$confirm = bin2hex(pack('H*', substr(md5($nonce), 0, 10)).$name);

		$message = fetch_form($mem_form_values['form_mail']);

		if (empty($message)) {
			$msg = mem_self_gTxt('greeting').' '.$name.','.
					n.n.mem_self_gTxt('password_reset_confirmation').': '.
					n. $url . 'mem_self_confirm='.$confirm;
		}
		else {
			$vals = $mem_form_values;
			$vals['sitename']	= $sitename;
			$vals['admin_name']	= $prefs['mem_self_admin_name'];
			$vals['admin_email']	= $vals['from'];
			//$vals['password']		= $pw;
			$vals['confirm_url'] = $url . 'mem_self_confirm=' . $confirm;
			$vals['siteurl']		= hu;
			$vals['username']		= empty($vals['name']) ? $name : $vals['name'];
			$vals['RealName']		= $RealName;

			foreach ($vals as $a=>$b) {
				$message = str_ireplace('{'.$a.'}', $b, $message);
				$message = str_ireplace('<txp:mem_'.$a.' />',$b,$message);
			}

			$msg = parse($message);
		}

		$to = $email;
		$from = $mem_form_values['from'];
		$reply = $mem_form_values['reply'];
		$subject = $mem_form_values['subject'];

		if (mem_form_mail($from,$reply,$to,$subject,$msg))
			return mem_self_gTxt('password_reset_confirmation_request_sent');
		else
			return mem_self_gTxt('mail_sorry');
	}
	else
		return mem_self_gTxt('user_not_found');
}


function mem_self_change_password_form($atts,$thing='')
{
	global $mem_self, $sitename, $production_status;

	header('Cache-Control: no-cache');
	header('Pragma: no-cache');

	if (!is_callable('mail'))
	{
		return ($production_status == 'live') ?
			mem_self_gTxt('mail_sorry') :
			gTxt('warn_mail_unavailable');
	}

	extract(lAtts(array(
		'form'		=> '',
		'email_form'	=> '',
		'from'		=> $mem_self['admin_email'],
		'reply'		=> '',
		'subject'	=> '['.$sitename.'] '. mem_self_gTxt('password_changed'),
	),$atts,false));

	if (!empty($form)) {
		$thing = fetch_form($form);
		unset($atts['form']);
	}

	foreach(array('from','reply','subject','email_form') as $a) {
		$thing .= '<txp:mem_form_secret name="'.$a.'" value="'.$$a.'" />';
		unset($atts[$a]);
	}

	return mem_form($atts + array('type'=>'mem_self_password'),$thing);
}

register_callback('mem_self_password_form_submit','mem_form.submit');

function mem_self_password_form_submit()
{
	global $prefs, $txp_user, $ign_user, $mem_form_type, $mem_form_values, $mem_form_thanks_form, $mem_self;

	if ($mem_form_type != 'mem_self_password')
		return;

	$verify_old = array_key_exists('old_password', $mem_form_values);
	$confirm = array_key_exists('password_confirm', $mem_form_values);

	$new_pass = $mem_form_values['password'];
	$old_pass = $mem_form_values['old_password'];

	if (isset($ign_user))
	{
		$user = $ign_user;
		$is_valid = $verify_old ? ign_validate($user, $old_pass) : true;
	}
	else
	{
		$user = $txp_user;
		$is_valid = $verify_old ? txp_validate($user, $old_pass) : true;
	}

	$where = "name = '".doSlash($user)."'";

	if (!$is_valid) {
		return mem_form_error(mem_self_gTxt('password_invalid'));
	}

	if ($confirm and ($new_pass != $mem_form_values['password_confirm'])) {
		return mem_form_error(mem_self_gTxt('password_mismatch'));
	}

	$phpass = new PasswordHash(PASSWORD_COMPLEXITY, PASSWORD_PORTABILITY);
	$hashed_pass = doSlash($phpass->HashPassword($new_pass));

	$rs = safe_update( mem_get_user_table_name(), "pass = '{$hashed_pass}'", $where);

	if (!$rs) {
		return mem_form_error(mem_self_gTxt('password_change_failed'));
	}

	// successful
	$mem_profile = safe_row('*',mem_get_user_table_name(),"name = '{$user}'");

	if ($mem_profile)
	{
		$mem_profile['new_pass'] = $new_pass;

		$message = @fetch_form($mem_form_values['email_form']);

		if (!empty($message))
		{
			$vals = array_merge($mem_form_values, $mem_profile);
			$vals['password']	= $new_pass;
			$vals['sitename']	= $sitename;
			$vals['admin_name']	= $prefs['mem_self_admin_name'];
			$vals['admin_email']	= $vals['from'];
			$vals['siteurl']		= hu;
			$vals['username']		= empty($vals['username']) ? $vals['name'] : $vals['username'];
			$vals['RealName']		= empty($vals['RealName']) ? $mem_profile['RealName'] : $vals['RealName'];

			foreach ($vals as $a=>$b) {
				$message = str_ireplace('{'.$a.'}', $b, $message);
				$message = str_ireplace('<txp:mem_'.$a.' />',$b,$message);
			}

		}
		else {
			$message = mem_self_gTxt('greeting', array('{name}'=>$mem_form_values['RealName']))."\r\n".
				mem_self_gTxt('your_password_is', array('{password}'=>$new_pass))."\r\n".
				mem_self_gTxt('log_in_at', array('{url}'=> $mem_form_values['login_url']));
		}

		$msg = parse($message);

		$to = $mem_profile['email'];
		$from = $mem_form_values['from'];
		$reply = $mem_form_values['reply'];
		$subject = $mem_form_values['subject'];

		if (mem_form_mail($from,$reply,$to,$subject,$msg))
			return mem_self_gTxt('password_changed');
		else
			return mem_self_gTxt('password_changed_mail_failed');
	}
	else {
		// no email, fail silently
	}

}

register_callback('mem_self_user_edit_submit','mem_form.submit');
register_callback('mem_self_register_defaults','mem_form.defaults');

function mem_self_user_edit_form($atts,$thing='')
{
	header('Cache-Control: no-cache');
	header('Pragma: no-cache');

	return mem_form($atts + array('type'=>'mem_self_user_edit'),$thing);
}

function mem_self_register_defaults()
{
	global $txp_user, $ign_user, $mem_form_type, $mem_profile;

	if ($mem_form_type != 'mem_self_user_edit') return;

	$user = isset($ign_user) ? $ign_user : $txp_user;


	$mem_profile = safe_row('*',mem_get_user_table_name(),"name = '{$user}'");

	if ($mem_profile) {
		mem_form_default($mem_profile);
	}
}

function mem_self_user_edit_submit()
{
	global $prefs, $txp_user, $ign_user, $mem_form_type, $mem_form_values, $mem_form_thanks_form, $mem_profile;

	if ($mem_form_type != 'mem_self_user_edit')
		return;

	if (isset($ign_user)) $txp_user = $ign_user;

	callback_event('mem_self_register.edit_profile', 'submit', 0, $mem_profile);

	$name = isset($mem_form_values['name']) ? trim($mem_form_values['name']) : '';

	$xtra = mem_get_extra_user_columns_insert_string();

	$mem_profile = array();

	$sql = '';

	if (!empty($mem_form_values['email']))
		$sql  = "email = '".doSlash($mem_form_values['email'])."'";
	if (!empty($mem_form_values['RealName']))
		$sql .= ", RealName = '".doSlash($mem_form_values['RealName'])."'";

	if (!empty($name))
	{
		$sql .= ", name = '".doSlash($name)."'";

		// need to remove the ign_password_protect cookie
		setcookie('ign_login', '', time()-86400);
	}

	if (empty($sql))
		return mem_self_gTxt('saved_user_profile_failed');

	$rs = safe_update( mem_get_user_table_name(),
				$sql . $xtra,
				"name = '{$txp_user}'");

	if ($rs) {
		callback_event('mem_self_register.edit_profile', 'submit', 0, $mem_profile);
		return mem_self_gTxt('saved_user_profile');
	}
	else {
		return mem_self_gTxt('saved_user_profile_failed');
	}
}


function mem_profile($atts, $body='')
{
	global $mem_profile,$txp_user,$ign_user;

	if (isset($ign_user)) $txp_user = $ign_user;

	extract(lAtts(array(
		'user'		=> '',
		'userid'	=> '',
		'var'			=> 'RealName',
		'form'		=> ''
	),$atts));

	if (empty($user) && empty($userid)) {
		// use the old method
		if (!is_array($mem_profile) && $txp_user)
			$mem_profile = safe_row('*',mem_get_user_table_name(),"name = '". doSlash($txp_user)."'");
	} else {
		$mem_profile = (is_array($mem_profile) ? $mem_profile : array());

		// look up a potentially new user
		if (!empty($user)) {
			if (!array_key_exists('name', $mem_profile) || strcmp($mem_profile['name'],$user)!=0)
				$mem_profile = safe_row('*',mem_get_user_table_name(),"name = '". doSlash($user)."'");
		}

		if (!empty($userid) && is_numeric($userid)) {
			if (!array_key_exists('user_id', $mem_profile) || strcmp($mem_profile['user_id'],$userid)!=0)
				$mem_profile = safe_row('*',mem_get_user_table_name(),"user_id = ". doSlash($userid));
		}
	}

	$out = '';

	if (empty($form) && empty($body)) {
		if ($mem_profile)
			$out = array_key_exists($var,$mem_profile) ? $mem_profile[$var] : '';
	} else {
		$thing = empty($body) ? fetch_form($form) : $body;

		$out = parse(EvalElse($thing, !empty($mem_profile)));
	}

	return $out;
}


function mem_submit($atts) {
	extract($atts);
	if (isset($value)) {
		$atts['label'] = $value;
		unset($atts['value']);
	}
	$atts['name'] = 'save';
	return mem_form_submit($atts);
}
function mem_password_input($atts) {
	global $mem_profile;
	$atts['password'] = 1;
	return mem_form_text( mem_self_map_tag($atts,'new_pass','') );
}
function mem_realname_input($atts) {
	global $mem_profile;
	return mem_form_text( mem_self_map_tag($atts,'RealName',$mem_profile['RealName']) );
}
function mem_email_input($atts) {
	global $mem_profile;
	return mem_form_email( mem_self_map_tag($atts,'email',$mem_profile['email']) );
}
function mem_phone_input($atts) {
	global $mem_profile;
	return mem_form_text( mem_self_map_tag($atts,'phone',$mem_profile['phone']) );
}
function mem_address_input($atts) {
	global $mem_profile;
	return mem_form_textarea( mem_self_map_tag($atts,'address', $mem_profile['address']) );
}
function mem_self_map_tag($atts,$name,$default) {
	$atts['name'] = $name;
	if (!empty($default))
		$atts['default'] = $default;
	return $atts;
}

function mem_self_user_count($atts)
{
	global $mem_self;

	$group_levels = mem_self_get_groups();

	extract(lAtts(array(
		'user_levels'	=> array_keys($group_levels),
		'wraptag'	=> '',
		'class'		=> ''
	),$atts));

	if (is_array($user_levels))
	{
		// no-op
	}
	else if (!empty($user_levels) || $user_levels=='0')
	{
		$user_levels = doSlash(explode(',',$user_levels));
	}
	else
	{
		$user_levels = array($mem_self['new_user_priv']);
	}

	$levels = implode(',',$user_levels);
	$count = safe_field('COUNT(*)', mem_get_user_table_name(), "privs IN ({$levels})");

	return doTag($count,$wraptag,$class);
}




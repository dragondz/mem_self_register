// <?php
// TXP 4.6 tag registration
if (class_exists('\Textpattern\Tag\Registry')) {
	Txp::get('\Textpattern\Tag\Registry')
		->register('mem_form')
    ->register('mem_form_text')
    ->register('mem_form_file')
    ->register('mem_form_textarea')
    ->register('mem_form_email')
    ->register('mem_form_select')
    ->register('mem_form_checkbox')
    ->register('mem_form_severinfo')
    ->register('mem_form_secret')
    ->register('mem_form_hidden')
    ->register('mem_form_radio')
    ->register('mem_form_submit')
    ->register('mem_form_value')
    ->register('mem_form_display_error')
    ->register('mem_form_error')
    ->register('mem_form_error')
    ->register('mem_form_select_category')
	;
}

$mem_glz_custom_fields_plugin = load_plugin('glz_custom_fields');

// needed for MLP
define( 'MEM_FORM_PREFIX' , 'mem_form' );

global $mem_form_lang;

if (!is_array($mem_form_lang))
{
	$mem_self_lang = array(
		'error_file_extension'	=> 'Mauvaise extention de fichier pour {label}.',
		'error_file_failed'	=> 'Echec Envoi fichier pour {label}.',
		'error_file_size'	=> 'Taille de fichier trop importante pour {label}.',
		'field_missing'	=> 'Le champ {label} est nécessaire.',
		'form_expired'	=>	'Le formulaire a expiré.',
		'form_misconfigured'	=> 'The mem_form is misconfigured. You must specify the "form" attribute.',
		'form_sorry'	=> 'Formulaire non disponible pour le moment.',
		'form_used'	=>	'Formulaire déjà soumis, rechargez le formulaire et recommencez.',
		'general_inquiry'	=> '',
		'invalid_email'	=> 'Adresse mail {email} non valide.',
		'invalid_host'	=> 'Domaine {domain} non valide.',
		'invalid_utf8'	=> 'Invalid UTF8 string for field {label}.',
		'invalid_value'	=> 'The value "{value}" is invalid for the input field {label}.',
		'invalid_format'	=>	'The input field {label} must match the format "{example}".',
		'invalid_too_many_selected'	=> 'The input field {label} only allows {count} selected {plural}.',
		'item'	=> 'item',
		'items'	=> 'items',
		'max_warning'	=> 'Le champs {label} doit être inférieur à {max} caractères.',
		'min_warning'	=> 'Le champs {label} doit contenir au moins {min} caractères.',
		'refresh'	=> 'Rechargez',
		'spam'	=> 'Your submission was blocked by a spam filter.',
		'submitted_thanks'	=>	'Votre formulaire a été correctement soumis.',
		'account_created_mail_failed'	=>	"Votre compte a été créé, mais une erreur est survenu lors de l\'envoi du mail contenant les information de votre compte vers votre adresse mail. Veuillez contacter l\'administrateur du site.",
		'admin_name'		=>	'Nom Administrateur',
		'admin_email'		=>	'Email Administrateur',
		'error_adding_new_author'	=>	'Erreur ajout nouveau compte',
		'greeting'			=>	'Salut {name}',
		'greeting1'			=>	'Salut ',
		'invalid_form_tags' =>	'Tag de formulaire invalide de "{form}"',
		'log_in_at'			=>	'Connecez vous a cette adresse {url}',
		'log_added_pref'	=>	'Ajout pref {name}',
		'log_pref_failed'	=>	'Echec ajout pref {name}. {error}',
		'log_pref_exists'	=>	'Pref {name} est déjà installé. Valeur actuelle "{value}"',
		'log_col_added'		=>	'Added column {name} to user table {table}',
		'log_col_failed'	=>	'Failed to add column {name} to table {table}. {error}',
		'log_col_exists'	=>	'Table {table} already has column {name}',
		'log_form_added'	=>	'Added form {name}',
		'log_form_failed'	=>	'Failed to add form {name}. {error}<br>You need to manually create a form template. Here is an example.',
		'log_form_found'	=>	'Found form {name}. Skipping installation of default form.',
		'log_xmpl_tag'		=>	'Example tag to use in your page template.',
		'mail_sorry'		=>	"L\'envoie de mail a échoué. Essayez plus tard SVP.",
		'missing_form_field'	=>	'Le champs requis {name} est vide ou manquant.',
		'password_changed'	=>	'Mot de passe modifié',
		'password_change_failed'	=>	'Echec changement de mot de passe',
		'password_invalid'	=> 'Mot de passe invalide',
		'password_sent_to'	=>	'Mot de passe envoyé à {email}',
		'password_sent_to1'	=>	'Mot de passe envoyé à votre adresse mail',
		'saved_user_profile'	=>	'Profil utilisateur enregistré',
		'saved_user_profile_failed'	=>	'Echec sauvegarde profil utilisateur',
		'user_exists'		=>	'Nom utilisateur déjà utilisé. Choisissez un autre SVP',
		'user_not_found'	=>	'Aucun compte utilisateur ne correspond aux information fournis.',
		'your_login_info'	=>	'Vos informations de connexion',
		'your_new_password'	=>	'Votre nouveau mot de passe',
		'your_password_is'	=>	'Votre mot de passe est {password}',
		'password_reset_confirmation_request'	=>	'Demande de réinitialisation de mot de passe',
		'password_reset_confirmation'	=>	'Cliquez sur ce lien pour confirmer la réinitialisation de votre mot de passe',
'password_reset_confirmation_request_sent'	=>	'Le demande de réinitialisation du mot de passe est envoyée',
	);
}

register_callback( 'mem_form_enumerate_strings' , 'l10n.enumerate_strings' );
function mem_form_enumerate_strings($event , $step='' , $pre=0)
{
	global $mem_form_lang;
	$r = array	(
				'owner'		=> 'mem_form',			#	Change to your plugin's name
				'prefix'	=> MEM_FORM_PREFIX,		#	Its unique string prefix
				'lang'		=> 'en-gb',				#	The language of the initial strings.
				'event'		=> 'public',			#	public/admin/common = which interface the strings will be loaded into
				'strings'	=> $mem_form_lang,		#	The strings themselves.
				);
	return $r;
}


function mem_form_gTxt($what,$args = array())
{
	global $mem_form_lang, $textarray;

	$key = strtolower( MEM_FORM_PREFIX . '-' . $what );

	if (isset($textarray[$key]))
	{
		$str = $textarray[$key];
	}
	else
	{
		$key = strtolower($what);

		if (isset($mem_form_lang[$key]))
			$str = $mem_form_lang[$key];
		elseif (isset($textarray[$key]))
			$str = $textarray[$key];
		else
			$str = $what;
	}

	if( !empty($args) )
		$str = strtr( $str , $args );

	return $str;
}


function mem_form($atts, $thing='', $default=false)
{
	global $sitename, $prefs, $file_max_upload_size, $mem_form_error, $mem_form_submit,
		$mem_form, $mem_form_labels, $mem_form_values,
		$mem_form_default, $mem_form_type, $mem_form_thanks_form,
		$mem_glz_custom_fields_plugin;

	extract(mem_form_lAtts(array(
		'form'		=> '',
		'thanks_form'	=> '',
		'thanks'	=> 'Votre formulaire a été correctement soumis.',
		'label'		=> '',
		'type'		=> '',
		'redirect'	=> '',
		'redirect_form'	=> '',
		'class'		=> 'memForm',
		'enctype'	=> '',
		'file_accept'	=> '',
		'max_file_size'	=> $file_max_upload_size,
		'form_expired_msg' => mem_form_gTxt('form_expired'),
		'show_error'	=> 1,
		'show_input'	=> 1,
	), $atts));

	if (empty($type) or (empty($form) && empty($thing))) {
		trigger_error('Argument not specified for mem_form tag', E_USER_WARNING);

		return '';
	}
	$out = '';

	// init error structure
	mem_form_error();

	$mem_form_type = $type;

	$mem_form_default = is_array($default) ? $default : array();
	callback_event('mem_form.defaults');

	unset($atts['show_error'], $atts['show_input']);
	$mem_form_id = md5(serialize($atts).preg_replace('/[\t\s\r\n]/','',$thing));
	$mem_form_submit = (ps('mem_form_id') == $mem_form_id);

	$nonce   = doSlash(ps('mem_form_nonce'));
	$renonce = false;

	if ($mem_form_submit) {
		safe_delete('txp_discuss_nonce', 'issue_time < date_sub(now(), interval 10 minute)');
		if ($rs = safe_row('used', 'txp_discuss_nonce', "nonce = '$nonce'"))
		{
			if ($rs['used'])
			{
				unset($mem_form_error);
				mem_form_error('Formulaire déjà soumis, rechargez le formulaire et recommencez.');
				$renonce = true;

				$_POST['mem_form_submit'] = TRUE;
				$_POST['mem_form_id'] = $mem_form_id;
				$_POST['mem_form_nonce'] = $nonce;
			}
		}
		else
		{
			mem_form_error($form_expired_msg);
			$renonce = true;
		}
	}

	if ($mem_form_submit and $nonce and !$renonce)
	{
		$mem_form_nonce = $nonce;
	}

	elseif (!$show_error or $show_input)
	{
		$mem_form_nonce = md5(uniqid(rand(), true));
		safe_insert('txp_discuss_nonce', "issue_time = now(), nonce = '$mem_form_nonce'");
	}

	$form = ($form) ? fetch_form($form) : $thing;
	$form = parse($form);

	if ($mem_form_submit && empty($mem_form_error))
	{
		// let plugins validate after individual fields are validated
		callback_event('mem_form.validate');
	}

	if (!$mem_form_submit) {
	  # don't show errors or send mail
	}
	elseif (mem_form_error())
	{
		if ($show_error or !$show_input)
		{
			$out .= mem_form_display_error();

			if (!$show_input) return $out;
		}
	}
	elseif ($show_input and is_array($mem_form))
	{
		if ($mem_glz_custom_fields_plugin) {
			// prep the values
			glz_custom_fields_before_save();
		}

		callback_event('mem_form.spam');

		/// load and check spam plugins/
		$evaluator =& get_mem_form_evaluator();
		$is_spam = $evaluator->is_spam();

		if ($is_spam) {
			return mem_form_gTxt('spam');
		}

		$mem_form_thanks_form = ($thanks_form ? fetch_form($thanks_form) : $thanks);

		safe_update('txp_discuss_nonce', "used = '1', issue_time = now()", "nonce = '$nonce'");

		$result = callback_event('mem_form.submit');

		if (mem_form_error()) {
			$out .= mem_form_display_error();
			$redirect = false;
		}

		$thanks_form = $mem_form_thanks_form;
		unset($mem_form_thanks_form);

		if (!empty($result))
			return $result;

		if (mem_form_error() and $show_input)
		{
			// no-op, reshow form with errors
		}
		else if ($redirect)
		{
			$_POST = array();

			while (@ob_end_clean());
			$uri = hu.ltrim($redirect,'/');
			if (empty($_SERVER['FCGI_ROLE']) and empty($_ENV['FCGI_ROLE']))
			{
				txp_status_header('303 See Other');
				header('Location: '.$uri);
				header('Connection: close');
				header('Content-Length: 0');
			}
			else
			{
				$uri = htmlspecialchars($uri);
				$refresh = mem_form_gTxt('refresh');

				if (!empty($redirect_form))
				{
					$redirect_form = fetch_form($redirect_form);

					echo str_replace('{uri}', $uri, $redirect_form);
				}

				if (empty($redirect_form))
				{
					echo <<<END
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>$sitename</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="refresh" content="0;url=$uri" />
</head>
<body>
<a href="$uri">$refresh</a>
</body>
</html>
END;
				}
			}
			exit;
		}
		else {
			return '<div class="memThanks" id="mem'.$mem_form_id.'">' .
				$thanks_form . '</div>';
		}
	}

	if ($show_input)
	{
		$file_accept = (!empty($file_accept) ? ' accept="'.$file_accept.'"' : '');

		$class = htmlspecialchars($class);

		$enctype = !empty($enctype) ? ' enctype="'.$enctype.'"' : '';

		return '<form method="post"'.((!$show_error and $mem_form_error) ? '' : ' id="mem'.$mem_form_id.'"').' class="'.$class.'" action="'.htmlspecialchars(serverSet('REQUEST_URI')).'#mem'.$mem_form_id.'"'.$file_accept.$enctype.'>'.
			( $label ? n.'<fieldset>' : n.'<div>' ).
			( $label ? n.'<legend>'.htmlspecialchars($label).'</legend>' : '' ).
			$out.
			n.'<input type="hidden" name="mem_form_nonce" value="'.$mem_form_nonce.'" />'.
			n.'<input type="hidden" name="mem_form_id" value="'.$mem_form_id.'" />'.
			(!empty($max_file_size) ? n.'<input type="hidden" name="MAX_FILE_SIZE" value="'.$max_file_size.'" />' : '' ).
			callback_event('mem_form.display','',1).
			$form.
			callback_event('mem_form.display').
			( $label ? (n.'</fieldset>') : (n.'</div>') ).
			n.'</form>';
	}

	return '';
}

function mem_form_text($atts)
{
	global $mem_form_error, $mem_form_submit, $mem_form_default;

	extract(mem_form_lAtts(array(
		'break'		=> br,
		'default'	=> '',
		'isError'	=> '',
		'label'		=> mem_form_gTxt('text'),
		'max'		=> 100,
		'min'		=> 0,
		'name'		=> '',
		'class'		=> 'memText',
		'required'	=> 1,
		'size'		=> '',
		'password'	=> 0,
		'format'	=> '',
		'example'	=> '',
		'patter'	=> '',
		'escape_value'	=> 1
	), $atts));

	$min = intval($min);
	$max = intval($max);
	$size = intval($size);

	if (empty($name)) $name = mem_form_label2name($label);

	if ($mem_form_submit)
	{
		$value = trim(ps($name));
		$utf8len = preg_match_all("/./su", $value, $utf8ar);
		$hlabel = empty($label) ? htmlspecialchars($name) : htmlspecialchars($label);


		if (strlen($value) == 0 && $required)
		{
			$mem_form_error[] = mem_form_gTxt('field_missing', array('{label}'=>$hlabel));
			$isError = true;
		}
		elseif ($required && !empty($format) && !preg_match($format, $value))
		{
			//echo "format=$format<br />value=$value<br />";
			$mem_form_error[] = mem_form_gTxt('invalid_format', array('{label}'=>$hlabel, '{example}'=> htmlspecialchars($example)));
			$isError = true;
		}
		elseif (strlen($value))
		{
			if (!$utf8len)
			{
				$mem_form_error[] = mem_form_gTxt('invalid_utf8', array('{label}'=>$hlabel));
				$isError = true;
			}

			elseif ($min and $utf8len < $min)
			{
				$mem_form_error[] = mem_form_gTxt('min_warning', array('{label}'=>$hlabel, '{min}'=>$min));
				$isError = true;
			}

			elseif ($max and $utf8len > $max)
			{
				$mem_form_error[] = mem_form_gTxt('max_warning', array('{label}'=>$hlabel, '{max}'=>$max));
				$isError = true;
			}

			else
			{
				$isError = false === mem_form_store($name, $label, $value);
			}
		}
	}

	else
	{
		if (isset($mem_form_default[$name]))
			$value = $mem_form_default[$name];
		else
			$value = $default;
	}

	$size = ($size) ? ' size="'.$size.'"' : '';
	$maxlength = ($max) ? ' maxlength="'.$max.'"' : '';
	$patter = ($patter) ? ' pattern="'.$patter.'"' : '';

	$isError = $isError ? "errorElement" : '';

	$memRequired = $required ? 'memRequired' : '';
	$class = htmlspecialchars($class);

	if ($escape_value)
	{
		$value = htmlspecialchars($value);
	}

    return '<label for="'.$name.'" class="'.$class.' '.$memRequired.$isError.' '.$name.'">'.htmlspecialchars($label).'</label>'.$break.
		'<input type="'.($password ? 'password' : 'text').'" id="'.$name.'" class="'.$class.' '.$memRequired.$isError.'" name="'.$name.'" value="'.$value.'"'.$size.$maxlength.$patter.' />';
}


function mem_form_file($atts)
{
	global $mem_form_submit, $mem_form_error, $mem_form_default, $file_max_upload_size, $tempdir;

	extract(mem_form_lAtts(array(
		'break'		=> br,
		'isError'	=> '',
		'label'		=> mem_form_gTxt('file'),
		'name'		=> '',
		'class'		=> 'memFile',
		'size'		=> '',
		'accept'	=> '',
		'no_replace' => 1,
		'max_file_size'	=> $file_max_upload_size,
		'required'	=> 1,
		'default'	=> FALSE,
	), $atts));

	$fname = ps('file_'.$name);
	$frealname = ps('file_info_'.$name.'_name');
	$ftype = ps('file_info_'.$name.'_type');

	if (empty($name)) $name = mem_form_label2name($label);

	$out = '';

	if ($mem_form_submit)
	{
		if (!empty($fname))
		{
			// see if user uploaded a different file to replace already uploaded
			if (isset($_FILES[$name]) && !empty($_FILES[$name]['tmp_name']))
			{
				// unlink last temp file
				if (file_exists($fname) && substr_compare($fname, $tempdir, 0, strlen($tempdir), 1)==0)
					unlink($fname);

				$fname = '';
			}
			else
			{
				// pass through already uploaded filename
				mem_form_store($name, $label, array('tmp_name'=>$fname, 'name' => $frealname, 'type' => $ftype));
				$out .= "<input type='hidden' name='file_".$name."' value='".htmlspecialchars($fname)."' />"
						. "<input type='hidden' name='file_info_".$name."_name' value='".htmlspecialchars($frealname)."' />"
						. "<input type='hidden' name='file_info_".$name."_type' value='".htmlspecialchars($ftype)."' />";
			}
		}

		if (empty($fname))
		{
			$hlabel = empty($label) ? htmlspecialchars($name) : htmlspecialchars($label);

			$fname = $_FILES[$name]['tmp_name'];
			$frealname = $_FILES[$name]['name'];
			$ftype = $_FILES[$name]['type'];
			$err = 0;

			switch ($_FILES[$name]['error']) {
				case UPLOAD_ERR_OK:
					if (is_uploaded_file($fname) and $max_file_size >= filesize($fname))
						mem_form_store($name, $label, $_FILES[$name]);
					elseif (!is_uploaded_file($fname)) {
						if ($required) {
							$mem_form_error[] = mem_form_gTxt('error_file_failed', array('{label}'=>$hlabel));
							$err = 1;
						}
					}
					else {
						$mem_form_error[] = mem_form_gTxt('error_file_size', array('{label}'=>$hlabel));
						$err = 1;
					}
					break;

				case UPLOAD_ERR_NO_FILE:
					if ($required) {
						$mem_form_error[] = mem_form_gTxt('field_missing', array('{label}'=>$hlabel));
						$err = 1;
					}
					break;

				case UPLOAD_ERR_EXTENSION:
					$mem_form_error[] = mem_form_gTxt('error_file_extension', array('{label}'=>$hlabel));
					$err = 1;
					break;

				case UPLOAD_ERR_INI_SIZE:
				case UPLOAD_ERR_FORM_SIZE:
					$mem_form_error[] = mem_form_gTxt('error_file_size', array('{label}'=>$hlabel));
					$err = 1;
					break;

				default:
					$mem_form_error[] = mem_form_gTxt('error_file_failed', array('{label}'=>$hlabel));
					$err = 1;
					break;
			}

			if (!$err)
			{
				// store as a txp tmp file to be used later
				$fname = get_uploaded_file($fname);
				$err = false === mem_form_store($name, $label, array('tmp_name'=>$fname, 'name' => $frealname, 'type' => $ftype));
				if ($err)
				{
					// clean up file
					@unlink($fname);
				}
				else
				{
					$out .= "<input type='hidden' name='file_".$name."' value='".htmlspecialchars($fname)."' />"
							. "<input type='hidden' name='file_info_".$name."_name' value='".htmlspecialchars($_FILES[$name]['name'])."' />"
							. "<input type='hidden' name='file_info_".$name."_type' value='".htmlspecialchars($_FILES[$name]['type'])."' />";
				}
			}

			$isError = $err ? 'errorElement' : '';
		}
	}
	else
	{
		if (isset($mem_form_default[$name]))
			$value = $mem_form_default[$name];
		else if (is_array($default))
			$value = $default;

		if (is_array(@$value))
		{
			$fname = @$value['tmp_name'];
			$frealname = @$value['name'];
			$ftype = @$value['type'];
			$out .= "<input type='hidden' name='file_".$name."' value='".htmlspecialchars($fname)."' />"
				. "<input type='hidden' name='file_info_".$name."_name' value='".htmlspecialchars($frealname)."' />"
				. "<input type='hidden' name='file_info_".$name."_type' value='".htmlspecialchars($ftype)."' />";
		}
	}

	$memRequired = $required ? 'memRequired' : '';
	$class = htmlspecialchars($class);

	$size = ($size) ? ' size="'.$size.'"' : '';
	$accept = (!empty($accept) ? ' accept="'.$accept.'"' : '');


	$field_out = '<label for="'.$name.'" class="'.$class.' '.$memRequired.$isError.' '.$name.'">'.htmlspecialchars($label).'</label>'.$break;

	if (!empty($frealname) && $no_replace)
	{
		$field_out .= '<div id="'.$name.'">'.htmlspecialchars($frealname) . ' <span id="'.$name.'_ftype">('. htmlspecialchars($ftype).')</span></div>';
	}
	else
	{
		$field_out .= '<input type="file" id="'.$name.'" class="'.$class.' '.$memRequired.$isError.'" name="'.$name.'"' .$size.' />';
	}

  return $out.$field_out;
}

function mem_form_textarea($atts, $thing='')
{
	global $mem_form_error, $mem_form_submit, $mem_form_default;

	extract(mem_form_lAtts(array(
		'break'		=> br,
		'cols'		=> 58,
		'default'	=> '',
		'isError'	=> '',
		'label'		=> mem_form_gTxt('textarea'),
		'max'		=> 10000,
		'min'		=> 0,
		'name'		=> '',
		'class'		=> 'memTextarea',
		'required'	=> 1,
		'rows'		=> 8,
		'escape_value'	=> 1
	), $atts));

	$min = intval($min);
	$max = intval($max);
	$cols = intval($cols);
	$rows = intval($rows);

	if (empty($name)) $name = mem_form_label2name($label);

	if ($mem_form_submit)
	{
		$value = preg_replace('/^\s*[\r\n]/', '', rtrim(ps($name)));
		$utf8len = preg_match_all("/./su", ltrim($value), $utf8ar);
		$hlabel = htmlspecialchars($label);

		if (strlen(ltrim($value)))
		{
			if (!$utf8len)
			{
				$mem_form_error[] = mem_form_gTxt('invalid_utf8', array('{label}'=>$hlabel));
				$isError = true;
			}

			elseif ($min and $utf8len < $min)
			{
				$mem_form_error[] = mem_form_gTxt('min_warning', array('{label}'=>$hlabel, '{min}'=>$min));
				$isError = true;
			}

			elseif ($max and $utf8len > $max)
			{
				$mem_form_error[] = mem_form_gTxt('max_warning', array('{label}'=>$hlabel, '{max}'=>$max));
				$isError = true;
			}

			else
			{
				$isError = false === mem_form_store($name, $label, $value);
			}
		}

		elseif ($required)
		{
			$mem_form_error[] = mem_form_gTxt('field_missing', array('{label}'=>$hlabel));
			$isError = true;
		}
	}

	else
	{
		if (isset($mem_form_default[$name]))
			$value = $mem_form_default[$name];
		else if (!empty($default))
			$value = $default;
		else
			$value = parse($thing);
	}

	$isError = $isError ? 'errorElement' : '';
	$memRequired = $required ? 'memRequired' : '';
	$class = htmlspecialchars($class);

	if ($escape_value)
	{
		$value = htmlspecialchars($value);
	}

	return '<label for="'.$name.'" class="'.$class.' '.$memRequired.$isError.' '.$name.'">'.htmlspecialchars($label).'</label>'.$break.
		'<textarea id="'.$name.'" class="'.$class.' '.$memRequired.$isError.'" name="'.$name.'" cols="'.$cols.'" rows="'.$rows.'">'.$value.'</textarea>';
}

function mem_form_email($atts)
{
	global $mem_form_error, $mem_form_submit, $mem_form_from, $mem_form_default;

	extract(mem_form_lAtts(array(
		'default'	=> '',
		'isError'	=> '',
		'label'		=> mem_form_gTxt('email'),
		'max'		=> 100,
		'min'		=> 0,
		'name'		=> '',
		'required'	=> 1,
		'break'		=> br,
		'size'		=> '',
    'patter'	=> '',
		'class'		=> 'memEmail',
	), $atts));

	if (empty($name)) $name = mem_form_label2name($label);

	if ($mem_form_submit)
	{
		$email = trim(ps($name));

		if (strlen($email))
		{
			if (!is_valid_email($email))
			{
				$mem_form_error[] = mem_form_gTxt('invalid_email', array('{email}'=>htmlspecialchars($email)));
				$isError = true;
			}
			else
			{
				preg_match("/@(.+)$/", $email, $match);
				$domain = $match[1];

				if (is_callable('checkdnsrr') and checkdnsrr('textpattern.com.','A') and !checkdnsrr($domain.'.','MX') and !checkdnsrr($domain.'.','A'))
				{
					$mem_form_error[] = mem_form_gTxt('invalid_host', array('{domain}'=>htmlspecialchars($domain)));
					$isError = true;
				}
				else
				{
					$mem_form_from = $email;
				}
			}
		}
	}
	else
	{
		if (isset($mem_form_default[$name]))
			$email = $mem_form_default[$name];
		else
			$email = $default;
	}

	return mem_form_text(array(
		'default'	=> $email,
		'isError'	=> $isError,
		'label'		=> $label,
		'max'		=> $max,
		'min'		=> $min,
		'name'		=> $name,
		'required'	=> $required,
		'break'		=> $break,
		'size'		=> $size,
    'patter'  => $patter,
		'class'		=> $class,
	));
}

function mem_form_select_section($atts)
{
	extract(mem_form_lAtts(array(
		'exclude'	=> '',
		'sort'		=> 'name ASC',
		'delimiter'	=> ',',
	),$atts,false));

	if (!empty($exclude)) {
		$exclusion = array_map('trim', explode($delimiter, preg_replace('/[\r\n\t\s]+/', ' ',$exclude)));
		$exclusion = array_map('strtolower', $exclusion);

		if (count($exclusion))
			$exclusion = join($delimiter, quote_list($exclusion));
	}

	$where = empty($exclusion) ? '1=1' : 'LOWER(name) NOT IN ('.$exclusion.')';

	$sort = empty($sort) ? '' : ' ORDER BY '. doSlash($sort);

	$rs = safe_rows('name, title','txp_section',$where . $sort);

	$items = array();
	$values = array();

	if ($rs) {
		foreach($rs as $r) {
			$items[] = $r['title'];
			$values[] = $r['name'];
		}
	}

	unset($atts['exclude'], $atts['sort']);

	$atts['items'] = join($delimiter, $items);
	$atts['values'] = join($delimiter, $values);

	return mem_form_select($atts);
}

function mem_form_select_category($atts)
{
	extract(mem_form_lAtts(array(
		'root'	=> 'root',
		'exclude'	=> '',
		'delimiter'	=> ',',
		'type'	=> 'article'
	),$atts,false));

	$rs = getTree($root, $type);

	if (!empty($exclude)) {
		$exclusion = array_map('trim', explode($delimiter, preg_replace('/[\r\n\t\s]+/', ' ',$exclude)));
		$exclusion = array_map('strtolower', $exclusion);
	}
	else
		$exclusion = array();

	$items = array();
	$values = array();

	if ($rs) {
		foreach ($rs as $cat) {
			if (count($exclusion) && in_array(strtolower($cat['name']), $exclusion))
				continue;

			$items[] = $cat['title'];
			$values[] = $cat['name'];
		}
	}

	unset($atts['root'], $atts['type']);

	$atts['items'] = join($delimiter, $items);
	$atts['values'] = join($delimiter, $values);

	return mem_form_select($atts);
}

function mem_form_select($atts)
{
	global $mem_form_error, $mem_form_submit, $mem_form_default;

	extract(mem_form_lAtts(array(
		'name'		=> '',
		'break'		=> ' ',
		'delimiter'	=> ',',
		'isError'	=> '',
		'label'		=> mem_form_gTxt('option'),
		'items'		=> mem_form_gTxt('general_inquiry'),
		'values'	=> '',
		'first'		=> FALSE,
		'required'	=> 1,
		'select_limit'	=> FALSE,
		'as_csv'	=> FALSE,
		'selected'	=> '',
		'class'		=> 'memSelect',
	), $atts, false));

	if (empty($name)) $name = mem_form_label2name($label);

	if (!empty($items) && $items[0] == '<') $items = parse($items);
	if (!empty($values) && $values[0] == '<') $values = parse($values);

	if ($first !== FALSE) {
		$items = $first.$delimiter.$atts['items'];
		$values = $first.$delimiter.$atts['values'];
	}

	$select_limit = empty($select_limit) ? 1 : assert_int($select_limit);

	$items = array_map('trim', explode($delimiter, preg_replace('/[\r\n\t\s]+/', ' ',$items)));
	$values = array_map('trim', explode($delimiter, preg_replace('/[\r\n\t\s]+/', ' ',$values)));
	if ($select_limit > 1)
	{
		$selected = array_map('trim', explode($delimiter, preg_replace('/[\r\n\t\s]+/', ' ',$selected)));
	}
	else
	{
		$selected = array(trim($selected));
	}

	$use_values_array = (count($items) == count($values));

	if ($mem_form_submit)
	{
		if (strpos($name, '[]'))
		{
			$value = ps(substr($name, 0, strlen($name)-2));

			$selected = $value;

			if ($as_csv)
			{
				$value = implode($delimiter, $value);
			}
		}
		else
		{
			$value = trim(ps($name));

			$selected = array($value);
		}

		if (!empty($selected))
		{
			if (count($selected) <= $select_limit)
			{
				foreach ($selected as $v)
				{
					$is_valid = ($use_values_array && in_array($v, $values)) or (!$use_values_array && in_array($v, $items));
					if (!$is_valid)
					{
						$invalid_value = $v;
						break;
					}
				}

				if ($is_valid)
				{
					$isError = false === mem_form_store($name, $label, $value);
				}
				else
				{
					$mem_form_error[] = mem_form_gTxt('invalid_value', array('{label}'=> htmlspecialchars($label), '{value}'=> htmlspecialchars($invalid_value)));
					$isError = true;
				}
			}
			else
			{
				$mem_form_error[] = mem_form_gTxt('invalid_too_many_selected', array(
											'{label}'=> htmlspecialchars($label),
											'{count}'=> $select_limit,
											'{plural}'=> ($select_limit==1 ? mem_form_gTxt('item') : mem_form_gTxt('items'))
										));
				$isError = true;
			}
		}

		elseif ($required)
		{
			$mem_form_error[] = mem_form_gTxt('field_missing', array('{label}'=> htmlspecialchars($label)));
			$isError = true;
		}
	}
	else if (isset($mem_form_default[$name]))
	{
		$selected = array($mem_form_default[$name]);
	}

	$out = '';

	foreach ($items as $item)
	{
		$v = $use_values_array ? array_shift($values) : $item;

		$sel = !empty($selected) && in_array($v, $selected);

		$out .= n.t.'<option'.($use_values_array ? ' value="'.$v.'"' : '').($sel ? ' selected="selected">' : '>').
				(strlen($item) ? htmlspecialchars($item) : ' ').'</option>';
	}

	$isError = $isError ? 'errorElement' : '';
	$memRequired = $required ? 'memRequired' : '';
	$class = htmlspecialchars($class);

	$multiple = $select_limit > 1 ? ' multiple="multiple"' : '';

	return '<label for="'.$name.'" class="'.$class.' '.$memRequired.$isError.' '.$name.'">'.htmlspecialchars($label).'</label>'.$break.
		n.'<select id="'.$name.'" name="'.$name.'" class="'.$class.' '.$memRequired.$isError.'"' . $multiple . '>'.
			$out.
		n.'</select>';
}

function mem_form_checkbox($atts)
{
	global $mem_form_error, $mem_form_submit, $mem_form_default;

	extract(mem_form_lAtts(array(
		'break'		=> ' ',
		'checked'	=> 0,
		'isError'	=> '',
		'label'		=> mem_form_gTxt('checkbox'),
		'name'		=> '',
		'class'		=> 'memCheckbox',
		'required'	=> 1
	), $atts));

	if (empty($name)) $name = mem_form_label2name($label);

	if ($mem_form_submit)
	{
		$value = (bool) ps($name);

		if ($required and !$value)
		{
			$mem_form_error[] = mem_form_gTxt('field_missing', array('{label}'=> htmlspecialchars($label)));
			$isError = true;
		}

		else
		{
			$isError = false === mem_form_store($name, $label, $value ? gTxt('yes') : gTxt('no'));
		}
	}

	else {
		if (isset($mem_form_default[$name]))
			$value = $mem_form_default[$name];
		else
			$value = $checked;
	}

	$isError = $isError ? 'errorElement' : '';
	$memRequired = $required ? 'memRequired' : '';
	$class = htmlspecialchars($class);

	return '<input type="checkbox" id="'.$name.'" class="'.$class.' '.$memRequired.$isError.'" name="'.$name.'"'.
		($value ? ' checked="checked"' : '').' />'.$break.
		'<label for="'.$name.'" class="'.$class.' '.$memRequired.$isError.' '.$name.'">'.htmlspecialchars($label).'</label>';
}


function mem_form_serverinfo($atts)
{
	global $mem_form_submit;

	extract(mem_form_lAtts(array(
		'label'		=> '',
		'name'		=> ''
	), $atts));

	if (empty($name)) $name = mem_form_label2name($label);

	if (strlen($name) and $mem_form_submit)
	{
		if (!$label) $label = $name;
		mem_form_store($name, $label, serverSet($name));
	}
}

function mem_form_secret($atts, $thing = '')
{
	global $mem_form_submit;

	extract(mem_form_lAtts(array(
		'name'	=> '',
		'label'	=> mem_form_gTxt('secret'),
		'value'	=> ''
	), $atts));


	$name = mem_form_label2name($name ? $name : $label);

	if ($mem_form_submit)
	{
		if ($thing)
			$value = trim(parse($thing));
		else
			$value = trim(parse($value));

		mem_form_store($name, $label, $value);
	}

	return '';
}

function mem_form_hidden($atts, $thing='')
{
	global $mem_form_submit, $mem_form_default;

	extract(mem_form_lAtts(array(
		'name'		=> '',
		'label'		=> mem_form_gTxt('hidden'),
		'value'		=> '',
		'isError'	=> '',
		'required'	=> 1,
		'class'		=> 'memHidden',
		'escape_value'	=> 1,
	), $atts));

	$name = mem_form_label2name($name ? $name : $label);

	if ($mem_form_submit)
	{
		$value = preg_replace('/^\s*[\r\n]/', '', rtrim(ps($name)));
		$utf8len = preg_match_all("/./su", ltrim($value), $utf8ar);
		$hlabel = htmlspecialchars($label);

		if (strlen($value))
		{
			if (!$utf8len)
			{
				$mem_form_error[] = mem_form_gTxt('invalid_utf8', $hlabel);
				$isError = true;
			}
			else
			{
				$isError = false === mem_form_store($name, $label, $value);
			}
		}
	}
	else
	{
		if (isset($mem_form_default[$name]))
			$value = $mem_form_default[$name];
		else if ($thing)
			$value = trim(parse($thing));
	}

	$isError = $isError ? 'errorElement' : '';
	$memRequired = $required ? 'memRequired' : '';

	if ($escape_value)
	{
		$value = htmlspecialchars($value);
	}

	return '<input type="hidden" class="'.$class.' '.$memRequired.$isError.' '.$name
			. '" name="'.$name.'" value="'.$value.'" id="'.$name.'" />';
}

function mem_form_radio($atts)
{
	global $mem_form_error, $mem_form_submit, $mem_form_values, $mem_form_default;

	extract(mem_form_lAtts(array(
		'break'		=> ' ',
		'checked'	=> 0,
		'group'		=> '',
		'label'		=> mem_form_gTxt('option'),
		'name'		=> '',
		'class'		=> 'memRadio',
		'isError'	=> '',
		'value'		=> false
	), $atts));

	static $cur_name = '';
	static $cur_group = '';

	if (!$name and !$group and !$cur_name and !$cur_group) {
		$cur_group = mem_form_gTxt('radio');
		$cur_name = $cur_group;
	}
	if ($group and !$name and $group != $cur_group) $name = $group;

	if ($name) $cur_name = $name;
	else $name = $cur_name;

	if ($group) $cur_group = $group;
	else $group = $cur_group;

	$id   = 'q'.md5($name.'=>'.$label);
	$name = mem_form_label2name($name);

	$value = $value === false ? $id : $value;

	if ($mem_form_submit)
	{
		$is_checked = (ps($name) == $value);

		if ($is_checked or $checked and !isset($mem_form_values[$name]))
		{
			$isError = false === mem_form_store($name, $group, $value);
		}
	}

	else
	{
		if (isset($mem_form_default[$name]))
			$is_checked = $mem_form_default[$name] == $value;
		else
			$is_checked = $checked;
	}

	$class = htmlspecialchars($class);

	$isError = $isError ? ' errorElement' : '';

	return '<input value="'.$value.'" type="radio" id="'.$id.'" class="'.$class.' '.$name.$isError.'" name="'.$name.'"'.
		( $is_checked ? ' checked="checked" />' : ' />').$break.
		'<label for="'.$id.'" class="'.$class.' '.$name.'">'.htmlspecialchars($label).'</label>';
}

function mem_form_submit($atts, $thing='')
{
	global $mem_form_submit;

	extract(mem_form_lAtts(array(
		'button'	=> 0,
		'label'		=> mem_form_gTxt('save'),
		'name'		=> 'mem_form_submit',
		'class'		=> 'memSubmit',
	), $atts));

	$label = htmlspecialchars($label);
	$name = htmlspecialchars($name);
	$class = htmlspecialchars($class);

	if ($mem_form_submit)
	{
		$value = ps($name);

		if (!empty($value) && $value == $label)
		{
			// save the clicked button value
			mem_form_store($name, $label, $value);
		}
	}

	if ($button or strlen($thing))
	{
		return '<button type="submit" class="'.$class.'" name="'.$name.'" value="'.$label.'">'.($thing ? trim(parse($thing)) : $label).'</button>';
	}
	else
	{
		return '<input type="submit" class="'.$class.'" name="'.$name.'" value="'.$label.'" />';
	}
}

function mem_form_lAtts($arr, $atts, $warn=true)
{
	foreach(array('button', 'checked', 'required', 'show_input', 'show_error') as $key)
	{
		if (isset($atts[$key]))
		{
			$atts[$key] = ($atts[$key] === 'yes' or intval($atts[$key])) ? 1 : 0;
		}
	}
	if (isset($atts['break']) and $atts['break'] == 'br') $atts['break'] = '<br />';
	return lAtts($arr, $atts, $warn);
}

function mem_form_label2name($label)
{
	$label = trim($label);
	if (strlen($label) == 0) return 'invalid';
	if (strlen($label) <= 32 and preg_match('/^[a-zA-Z][A-Za-z0-9:_-]*$/', $label)) return $label;
	else return 'q'.md5($label);
}

function mem_form_store($name, $label, $value)
{
	global $mem_form, $mem_form_labels, $mem_form_values;

	$mem_form[$label] = $value;
	$mem_form_labels[$name] = $label;
	$mem_form_values[$name] = $value;

	$is_valid = false !== callback_event('mem_form.store_value', $name);

	// invalid data, unstore it
	if (!$is_valid)
		mem_form_remove($name);

	return $is_valid;
}

function mem_form_remove($name)
{
	global $mem_form, $mem_form_labels, $mem_form_values;

	$label = $mem_form_labels[$name];
	unset($mem_form_labels[$name], $mem_form[$label], $mem_form_values[$name]);
}

function mem_form_display_error()
{
	global $mem_form_error;

	$out = n.'<ul class="memError">';

	foreach (array_unique($mem_form_error) as $error)
	{
		$out .= n.t.'<li>'.$error.'</li>';
	}

	$out .= n.'</ul>';

	return $out;
}

function mem_form_value($atts, $thing)
{
	global $mem_form_submit, $mem_form_values, $mem_form_default;

	extract(mem_form_lAtts(array(
		'name'		=> '',
		'wraptag'	=> '',
		'class'		=> '',
		'attributes'=> '',
		'id'		=> '',
	), $atts));

	$out = '';

	if ($mem_form_submit)
	{
		if (isset($mem_form_values[$name]))
			$out = $mem_form_values[$name];
	}
	else {
		if (isset($mem_form_default[$name]))
			$out = $mem_form_default[$name];
	}

	return doTag($out, $wraptag, $class, $attributes, $id);
}

function mem_form_error($err=NULL)
{
	global $mem_form_error;

	if (!is_array($mem_form_error))
		$mem_form_error = array();

	if ($err == NULL)
		return !empty($mem_form_error) ? $mem_form_error : false;

	$mem_form_error[] = $err;
}

function mem_form_default($key,$val=NULL)
{
	global $mem_form_default;

	if (is_array($key))
	{
		foreach ($key as $k=>$v)
		{
			mem_form_default($k,$v);
		}
		return;
	}

	$name = mem_form_label2name($key);

	if ($val == NULL)
	{
		return (isset($mem_form_default[$name]) ? $mem_form_default[$name] : false);
	}

	$mem_form_default[$name] = $val;

	return $val;
}



function mem_form_mail($from,$reply,$to,$subject,$msg, $content_type='text/plain')
{
	global $prefs;

	if (!is_callable('mail'))
		return false;

	$to = mem_form_strip($to);
	$from = mem_form_strip($from);
	$reply = mem_form_strip($reply);
	$subject = mem_form_strip($subject);
	$msg = mem_form_strip($msg,FALSE);

	if ($prefs['override_emailcharset'] and is_callable('utf8_decode')) {
		$charset = 'ISO-8859-1';
		$subject = utf8_decode($subject);
		$msg     = utf8_decode($msg);
	}
	else {
		$charset = 'UTF-8';
	}

	$subject = mem_form_mailheader($subject,'text');

	$sep = !IS_WIN ? "\n" : "\r\n";

	$headers = 'From: '.$from.
		($reply ? ($sep.'Reply-To: '.$reply) : '').
		$sep.'X-Mailer: Textpattern (mem_form)'.
		$sep.'X-Originating-IP: '.mem_form_strip((!empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'].' via ' : '').$_SERVER['REMOTE_ADDR']).
		$sep.'Content-Transfer-Encoding: 8bit'.
		$sep.'Content-Type: '.$content_type.'; charset="'.$charset.'"';

	return mail($to, $subject, $msg, $headers);
}

function mem_form_mailheader($string, $type)
{
	global $prefs;

	if (!strstr($string,'=?') and !preg_match('/[\x00-\x1F\x7F-\xFF]/', $string)) {
		if ("phrase" == $type) {
			if (preg_match('/[][()<>@,;:".\x5C]/', $string)) {
				$string = '"'. strtr($string, array("\\" => "\\\\", '"' => '\"')) . '"';
			}
		}
		elseif ("text" != $type) {
			trigger_error('Unknown encode_mailheader type', E_USER_WARNING);
		}
		return $string;
	}
	if ($prefs['override_emailcharset']) {
		$start = '=?ISO-8859-1?B?';
		$pcre  = '/.{1,42}/s';
	}
	else {
		$start = '=?UTF-8?B?';
		$pcre  = '/.{1,45}(?=[\x00-\x7F\xC0-\xFF]|$)/s';
	}
	$end = '?=';
	$sep = IS_WIN ? "\r\n" : "\n";
	preg_match_all($pcre, $string, $matches);
	return $start . join($end.$sep.' '.$start, array_map('base64_encode',$matches[0])) . $end;
}

function mem_form_strip($str, $header = TRUE) {
	if ($header) $str = strip_rn($str);
	return preg_replace('/[\x00]/', ' ', $str);
}

///////////////////////////////////////////////
// Spam Evaluator
class mem_form_evaluation
{
	var $status;

	function __construct() {
		$this->status = 0;
	}

	function add_status($rating=-1) {
		$this->status += $rating;
	}

	function get_status() {
		return $this->status;
	}

	function is_spam() {
		return ($this->status < 0);
	}
}

function &get_mem_form_evaluator()
{
	static $instance;

	if(!isset($instance)) {
		$instance = new mem_form_evaluation();
	}
	return $instance;
}
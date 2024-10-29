<?php
/*
Copyright: © 2010 AltosResearch.com ( coded in the USA )
<mailto:support@altosresearch.com> <http://www.altosresearch.com/>

Released under the terms of the GNU General Public License.
You should have received a copy of the GNU General Public License.
If not, see: <http://www.gnu.org/licenses/>.
*/
/*
Version: 1.3.0
Stable tag: trunk
Tested up to: 3.6
Requires at least: 2.7
Plugin Name: Altos Connect

Author: AltosResearch.com
Contributors: AltosResearch
Author URI: http://www.altosresearch.com/
License: http://www.gnu.org/licenses/gpl-2.0.txt
Plugin URI: http://blog.altosresearch.com/ready-four-new-wordpress-plugins-for-real-estate-data/
Tags: widget, widgets, altos, altos research, altosresearch, real estate, property, form, leads
Description: Altos Connect registration widget for WordPress®.
*/
/*
Direct access denial.
*/
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
	exit;
/*
Class for Altos Connect.
*/
class Altos_Connect_Widget
	{
		var $webservice = "http://www.altosresearch.com/altos/app";
		/*
		Constructor.
		*/
		function Altos_Connect_Widget ()
			{
				$this->__construct;
			}
		/**/
		function __construct ()
			{
				add_action ("admin_menu", array (&$this, "on_admin_menu"));
				add_action ("admin_notices", array (&$this, "on_admin_notices"));
				add_action ("widgets_init", array (&$this, "on_widget_init"));
				add_action ("wp_print_scripts", array (&$this, "on_wp_print_scripts"));
				add_action ("wp_head", array (&$this, "on_wp_head"));
			}
		/*
		Widget initializer.
		*/
		function on_widget_init ()
			{
				register_sidebar_widget ("Altos Connect", array (&$this, "on_widget_register"), $widget_ops);
				register_widget_control ("Altos Connect", array (&$this, "on_widget_control"));
			}
		/*
		These deal with menu pages.
		*/
		function on_admin_menu ()
			{
				add_filter ("plugin_action_links", array (&$this, "on_plugin_action_links"), 10, 2);
				add_menu_page ("Altos Connect", "Altos Connect", "edit_plugins", "altos-connect-options", array (&$this, "on_options_page"));
				add_submenu_page ("altos-connect-options", "General Options", "General Options", "edit_plugins", "altos-connect-options", array (&$this, "on_options_page"));
			}
		/**/
		function on_admin_notices ()
			{
				global $pagenow;
				/**/
				if ($pagenow && $pagenow === "plugins.php")
					{
						$options = $this->get_backward_compatible_options ();
						/**/
						if (!$this->validate_pai ($options["pai"]))
							$this->display_admin_notice ('<strong>Altos Connect:</strong> please <a href="admin.php?page=altos-connect-options">configure Altos Connect</a> by supplying your Username/Password for authentication.', true);
						/**/
						if (!ini_get ("allow_url_fopen") && !function_exists ("curl_init"))
							$this->display_admin_notice ('<strong>Altos Connect:</strong> your server is NOT yet compatible with Altos Connect. Please set <code><a href="http://www.php.net/manual/en/filesystem.configuration.php#ini.allow-url-fopen" target="_blank">allow_url_fopen = yes</a></code> in your <code>php.ini</code> file. If that is not possible, Altos Connect can also use the <a href="http://php.net/manual/en/book.curl.php" target="_blank">cURL</a> extension for PHP, if your hosting provider installs it. Please contact your hosting provider to resolve this problem. <em><strong>*Tip*</strong> all of the <a href="http://wordpress.org/hosting/" target="_blank">hosting providers recommended by WordPress®</a>, support one of these two methods; by default.</em>', true);
					}
			}
		/**/
		function on_plugin_action_links ($links = array (), $file = "")
			{
				if (preg_match ("/" . preg_quote ($file, "/") . "$/", __FILE__) && is_array ($links))
					{
						$settings = '<a href="admin.php?page=altos-connect-options">Settings</a>';
						array_unshift ($links, $settings);
					}
				/**/
				return $links;
			}
		/**/
		function on_options_page () /* Handles General Options. */
			{
				$options = $this->update_all_options ();
				include_once dirname (__FILE__) . "/altos-options.php";
			}
		/**/
		function update_all_options () /* Used with option forms. */
			{
				$options = $this->get_backward_compatible_options ();
				/**/
				if ($_POST["altos_connect_options_save"])
					{
						$_POST = stripslashes_deep ($_POST);
						/**/
						foreach ($_POST as $key => $value)
							{
								if ($key !== "altos_connect_options_save")
									if (preg_match ("/^" . preg_quote ("altos_connect_", "/") . "/", $key))
										{
											(is_array ($value)) ? array_shift ($value) : null;
											$options[preg_replace ("/^" . preg_quote ("altos_connect_", "/") . "/", "", $key)] = $value;
										}
							}
						/**/
						$options["pai"] = strip_tags (stripslashes ($this->auth ($options["username"], $options["password"])));
						/**/
						update_option ("altos_connect_options", $options);
						update_option ("altos_global_options", array ("username" => $options["username"], "password" => $options["password"], "pai" => $options["pai"]));
						/**/
						if (!$this->validate_pai ($options["pai"])) /* Validate the newly obtained pai value. */
							{
								$this->display_admin_notice ('<strong>Invalid login credentials, please try again.</strong>', true);
							}
						else /* Otherwise, everything looks good! */
							$this->display_admin_notice ('<strong>Options saved.</strong>');
					}
				/**/
				return $options;
			}
		/*
		These acquire options w/backward compatiblity.
		*/
		function get_backward_compatible_widget_options ()
			{
				$widget_options = get_option ("altos_connect_widget_options");
				$widget_options = (!is_array ($widget_options) || empty ($widget_options)) ? get_option ("widget_altos") : $widget_options;
				$widget_options = (!is_array ($widget_options) || empty ($widget_options)) ? array (): $widget_options;
				/**/
				return $widget_options;
			}
		/**/
		function get_backward_compatible_options ()
			{
				$options = get_option ("altos_connect_options");
				$options = (!is_array ($options) || empty ($options)) ? get_option ("altos_global_options") : $options;
				$options = (!is_array ($options) || empty ($options)) ? get_option ("widget_altos") : $options;
				$options = (!is_array ($options) || empty ($options)) ? array (): $options;
				/**/
				return $options;
			}
		/*
		Displays admin notifications.
		*/
		function display_admin_notice ($notice = FALSE, $error = FALSE)
			{
				if ($notice && $error) /* Special format for errors. */
					{
						echo '<div class="error fade"><p>' . $notice . '</p></div>';
					}
				else if ($notice) /* Otherwise, we just send it as an update notice. */
					{
						echo '<div class="updated fade"><p>' . $notice . '</p></div>';
					}
			}
		/*
		The widget output function.
		*/
		function on_widget_register ($args)
			{
				extract($args);
				/**/
				$options = $this->get_backward_compatible_options ();
				$widget_options = $this->get_backward_compatible_widget_options ();
				/**/
				if ($this->validate_pai ($options["pai"]))
					{
						echo $before_widget . $before_title . $widget_options["title"] . $after_title;
						/**/
						if ($_POST["altos-connect-submit"])
							{
								echo '<p>' . $this->on_submit_altos_connect ($_POST) . '</p>';
							}
						else
							{
								echo '<form name="altos-connect-submit" method="post" id="altos-connect-submit" action="' . $PHP_SELF . '">';
								/**/
								echo '<p>';
								echo '<label for="FIRST_NAME">';
								echo '* First Name:<br />';
								echo '<input size="15" id="FIRST_NAME" name="FIRST_NAME" type="text" />';
								echo '</label>';
								echo '</p>';
								/**/
								echo '<p>';
								echo '<label for="LAST_NAME">';
								echo '* Last Name:<br />';
								echo '<input size="15" id="LAST_NAME" name="LAST_NAME" type="text" />';
								echo '</label>';
								echo '</p>';
								/**/
								echo '<p>';
								echo '<label for="EMAIL1">';
								echo '* Email:<br />';
								echo '<input size="15" id="EMAIL1" name="EMAIL1" type="text" />';
								echo '</label>';
								echo '</p>';
								/**/
								echo '<p>';
								echo '<label for="PHONE_AREA_CODE">';
								echo 'Phone:<br />';
								echo '<input style="width:30px;" name="PHONE_AREA_CODE" type="text" maxlength="3" size="3" />';
								echo '<input style="width:30px;" name="PHONE_LOCAL_CODE" type="text" maxlength="3" size="3" />';
								echo '<input style="width:30px;" name="PHONE_NUMBER" type="text" maxlength="4" size="4" />';
								echo '</label>';
								echo '</p>';
								/**/
								echo '<p>';
								echo '<label for="cityzip">';
								echo '* Interested City Zip:<br />';
								echo '<select style="width:105px; margin:5px 0 5px 0;" name="cityzip" id="cityzip"><option value="">-SELECT ONE-</option>', $this->print_locations_select_box (), '</select>';
								echo '</label>';
								echo '</p>';
								/**/
								echo '<p>';
								echo '<label for="PREF_CONTACT_METHOD">';
								echo 'Please contact me by:<br />';
								echo '<input name="PREF_CONTACT_METHOD" type="radio" checked value="email" /> email ';
								echo '<input name="PREF_CONTACT_METHOD" type="radio" value="phone" /> phone';
								echo '</label>';
								echo '</p>';
								/**/
								echo '<p>';
								echo '<input type="hidden" name="altos-connect-captcha" id="altos-connect-captcha" />';
								echo '<input type="submit" name="altos-connect-submit" value="Request Report" />';
								echo '</p>';
								/**/
								echo '</form>';
							}
						/**/
						echo $after_widget;
					}
			}
		/*
		The widget control function.
		*/
		function on_widget_control ()
			{
				$options = $this->get_backward_compatible_options ();
				$widget_options = $this->get_backward_compatible_widget_options ();
				/**/
				if (isset ($_POST["altos-submit"]))
					{
						$widget_options["title"] = strip_tags (stripslashes ($_POST["altos-title"]));
						$options["username"] = strip_tags (stripslashes ($_POST["altos-username"]));
						$options["password"] = strip_tags (stripslashes ($_POST["altos-password"]));
						$options["pai"] = strip_tags (stripslashes ($this->auth ($options["username"], $options["password"])));
						/**/
						update_option ("altos_connect_options", $options);
						update_option ("altos_connect_widget_options", $widget_options);
						update_option ("altos_global_options", array ("username" => $options["username"], "password" => $options["password"], "pai" => $options["pai"]));
					}
				/**/
				$title = attribute_escape ($widget_options["title"]);
				$username = attribute_escape ($options["username"]);
				$password = attribute_escape ($options["password"]);
				$pai = attribute_escape ($options["pai"]);
				/**/
				if ($this->validate_pai ($pai))
					{
						echo '<p>';
						echo '<label for="altos-success" style="padding:6px">';
						echo '<img width="20" src="' . WP_PLUGIN_URL . '/' . basename (dirname (__FILE__)) . '/images/accepted_48.png" />';
						echo 'Your account is active.';
						echo '</label>';
						echo '</p>';
					}
				else
					{
						echo '<p>';
						echo '<label for="altos-error" style="padding:6px">';
						echo '<img width="20" src="' . WP_PLUGIN_URL . '/' . basename (dirname (__FILE__)) . '/images/cancel_48.png" />';
						echo strlen ($pai) ? $pai : 'Please enter your Altos Research credentials.';
						echo '</label>';
						echo '</p>';
					}
				/**/
				echo '<p>';
				echo '<label for="altos-title">';
				echo 'Widget Title:<br />';
				echo '<input class="widefat" id="altos-title" name="altos-title" type="text" value="' . $title . '" />';
				echo '</label>';
				echo '</p>';
				/**/
				echo '<p>';
				echo '<label for="altos-username">';
				echo 'Altos Research Username:<br />';
				echo '<input class="widefat" id="altos-username" name="altos-username" type="text" value="' . $username . '" />';
				echo '</label>';
				echo '</p>';
				/**/
				echo '<p>';
				echo '<label for="altos-password">';
				echo 'Altos Research Password:<br />';
				echo '<input class="widefat" id="altos-password" name="altos-password" type="password" value="' . $password . '" />';
				echo '</label>';
				echo '</p>';
				/**/
				echo '<input type="hidden" id="altos-submit" name="altos-submit" value="1" />';
			}
		/*
		Required scripts.
		*/
		function on_wp_print_scripts ()
			{
				wp_enqueue_script("jquery");
				wp_enqueue_script ("jquery.sha1", WP_PLUGIN_URL . "/" . basename (dirname (__FILE__)) . "/jquery-sha1/jquery.sha1-min.js", array ("jquery"));
				wp_enqueue_script ("jquery.validate", WP_PLUGIN_URL . "/" . basename (dirname (__FILE__)) . "/jquery-validate/jquery.validate.js", array ("jquery"));
				wp_enqueue_script ("jquery.autotab", WP_PLUGIN_URL . "/" . basename (dirname (__FILE__)) . "/jquery-autotab/jquery.autotab.js", array ("jquery"));
			}
		/*
		Required header additions.
		*/
		function on_wp_head ()
			{
				echo '<style type="text/css">';
				echo 'p .error { color: red; }';
				echo '</style>';
				/**/
				echo '<script type="text/javascript">';
				echo 'jQuery (document).ready (function () {';
				echo "jQuery ('input[name=PHONE_AREA_CODE]').autotab ({target: jQuery ('input[name=PHONE_LOCAL_CODE]'), format: 'numeric'});";
				echo "jQuery ('input[name=PHONE_LOCAL_CODE]').autotab ({target: jQuery ('input[name=PHONE_NUMBER]'), format: 'numeric', previous: jQuery ('input[name=PHONE_AREA_CODE]')});";
				echo "jQuery ('input[name=PHONE_NUMBER]').autotab ({previous: jQuery ('input[name=PHONE_LOCAL_CODE]'), format: 'numeric'});";
				echo "jQuery ('#altos-connect-submit').validate ({event: 'keyup', errorElement: 'p', rules: { FIRST_NAME: {required: true, }, LAST_NAME: {required: true, }, EMAIL1: {required: true, email: true, }, PREF_CONTACT_METHOD: {required: true, }, cityzip: {required: function (element) { return (jQuery ('#cityzip').val () != '-SELECT ONE-'); }}}});";
				echo '});';
				echo '</script>';
			}
		/*
		These are utitlity functions.
		*/
		function on_submit_altos_connect ($post)
			{
				$options = $this->get_backward_compatible_options ();
				/**/
				$post["pai"] = $options["pai"];
				/**/
				if (trim (stripslashes ($post["altos-connect-captcha"])) !== sha1 (trim (stripslashes ($_POST["EMAIL1"])) . trim (stripslashes ($_POST["cityzip"]))))
					return 'Thanks for your interest! &mdash; I\'ll be sure to follow up with you right away.';
				/**/
				return strip_tags ($this->crmpost ($post));
			}
		/**/
		function get_available_locations ()
			{
				$options = $this->get_backward_compatible_options ();
				/**/
				$available = $this->listreports ($options["pai"]);
				/**/
				preg_match_all ('/label="(.+?)".+value="(.+?)"/', $available, $matches);
				/**/
				$locations = array ();
				/**/
				foreach ($matches[1] as $key => $name)
					{
						$locations[trim ($name)] = $matches[2][$key];
					}
				/**/
				return $locations;
			}
		/**/
		function print_locations_select_box ()
			{
				foreach ($this->get_available_locations () as $name => $value)
					{
						print '<option value="' . $value . '"> ' . $name . '</option>';
					}
			}
		/**/
		function validate_pai ($pai)
			{
				if (is_numeric ($pai) && $pai > 0)
					{
						return true;
					}
			}
		/**/
		function auth ($username, $password)
			{
				$data = http_build_query (array ("service" => "auth", "username" => $username, "password" => $password));
				/**/
				$context_options = array ("http" => array ("method" => "POST", "header" => "Content-type: application/x-www-form-urlencoded\r\nContent-Length: " . strlen ($data) . "\r\n", "content" => $data));
				/**/
				$context = stream_context_create ($context_options);
				$json = $this->fetch_url_contents ($this->webservice, false, $context);
				$response = json_decode ($json);
				/**/
				return $response;
			}
		/**/
		function listreports ($account_id)
			{
				$data = http_build_query (array ("service" => "listreports", "pai" => $account_id));
				/**/
				$context_options = array ("http" => array ("method" => "POST", "header" => "Content-type: application/x-www-form-urlencoded\r\nContent-Length: " . strlen ($data) . "\r\n", "content" => $data));
				/**/
				$context = stream_context_create ($context_options);
				/**/
				return $this->fetch_url_contents ($this->webservice, false, $context);
			}
		/**/
		function crmpost ($post)
			{
				$params = $post;
				/**/
				$options = $this->get_backward_compatible_options ();
				/**/
				$params["service"] = "crmpost";
				$params["pai"] = $options["pai"];
				$params["FROM_WP"] = "true";
				/**/
				$data = http_build_query ($params);
				/**/
				$context_options = array ("http" => array ("method" => "POST", "header" => "Content-type: application/x-www-form-urlencoded\r\nContent-Length: " . strlen ($data) . "\r\n", "content" => $data));
				/**/
				$context = stream_context_create ($context_options);
				/**/
				return $this->fetch_url_contents ($this->webservice, false, $context);
			}
		/**/
		function fetch_url_contents ($url = "", $flags = 0, $context = NULL)
			{
				if ($url && preg_match ("/^http(s)?\:/", $url))
					{
						if (ini_get ("allow_url_fopen"))
							return@file_get_contents ($url, $flags, $context);
						/**/
						else if (function_exists ("curl_init"))
							{
								$c = (is_resource ($context)) ? stream_context_get_options ($context) : "";
								return $this->curlpsr ($url, $c["http"]["content"]);
							}
						/**/
						else /* Both disabled! */
							return false;
					}
				/**/
				return false;
			}
		/**/
		function curlpsr ($url = FALSE, $vars = FALSE)
			{
				if ($url && ($connection = @curl_init ()))
					{
						@curl_setopt ($connection, CURLOPT_URL, $url);
						@curl_setopt ($connection, CURLOPT_POST, true);
						@curl_setopt ($connection, CURLOPT_TIMEOUT, 20);
						@curl_setopt ($connection, CURLOPT_CONNECTTIMEOUT, 20);
						@curl_setopt ($connection, CURLOPT_FOLLOWLOCATION, false);
						@curl_setopt ($connection, CURLOPT_MAXREDIRS, 0);
						@curl_setopt ($connection, CURLOPT_HEADER, false);
						@curl_setopt ($connection, CURLOPT_VERBOSE, true);
						@curl_setopt ($connection, CURLOPT_ENCODING, "");
						@curl_setopt ($connection, CURLOPT_SSL_VERIFYPEER, false);
						@curl_setopt ($connection, CURLOPT_RETURNTRANSFER, true);
						@curl_setopt ($connection, CURLOPT_FORBID_REUSE, true);
						@curl_setopt ($connection, CURLOPT_FAILONERROR, true);
						@curl_setopt ($connection, CURLOPT_POSTFIELDS, $vars);
						/**/
						$output = trim (@curl_exec ($connection));
						/**/
						@curl_close($connection);
					}
				/**/
				return (strlen ($output)) ? $output : false;
			}
	}
/*
New instance of Altos Connect class.
*/
$Altos_Connect_Widget = new Altos_Connect_Widget ();
?>
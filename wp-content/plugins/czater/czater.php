<?php
/*
  Plugin Name: 'Czater.pl live chat telefon'
  Plugin URI: https://www.czater.pl
  Description: Live chat na Twoją stronę
  Version: 1.0.5
  Author: Czater.pl
  Author URI: https://www.czater.pl
 */

defined('ABSPATH') or die('No script kiddies please!');

if (!class_exists('Czater')) {

  class Czater
  {

    public static $setting_page_url = '/wp-admin/admin.php?page=czater_settings_page';
    public static $plugin_name = 'czater';

    /**
     * Init chat. Should be started at all pages on frontend
     */
    public static function wp_head()
    {
      //delete_option( ChatsAction::$optionKey );
      if (get_option("CzaterId") != "0") {
        global $current_user;
        get_currentuserinfo();

        if (is_admin()) {
          $login = "";
          $email = "";
        } else {
          $login = $current_user->user_login;
          $email = $current_user->user_email;
        }

        if (get_option("czaterAutoCompliteLogin") != 1) {
          $login = "";
        }
        if (get_option("czaterAutoCompliteEmail") != 1) {
          $email = "";
        }

        if (is_user_logged_in() or get_option("czaterForLoggedUsers") != 1) {
?>
          <script type="text/javascript">
            window.$czater || (function(d, s) {
              var z = $czater = function(c) {
                  z._.push(c)
                },
                $ = z.s = d.createElement(s),
                e = d.getElementsByTagName(s)[0];
              z.set = function(o) {
                z.set._.push(o)
              };
              z._ = [];
              z.set._ = [];
              $.async = !0;
              $.setAttribute('charset', 'utf-8');
              $.src = 'https://www.czater.pl/assets/modules/chat/js/chat.js';
              z.t = +new Date;
              z.tok = "<?php echo get_option("CzaterId"); ?>";
              <?php if (get_option("css_template") != "") : ?>
                z.css_template = "<?php echo get_option("css_template"); ?>";
              <?php endif; ?>
              z.domain = "https://www.czater.pl/";
              z.login = "<?php echo $login; ?>";
              z.email = "<?php echo $email; ?>";
              $.type = 'text/javascript';
              e.parentNode.insertBefore($, e)
            })(document, 'script');
          </script>
      <?php
        }
      }
    }

    public static function admin_menu()
    {
      if (is_admin()) {
        add_menu_page('Czater', 'Czater', 'manage_options', 'czater_settings_page', array('Czater', 'czater_settings_page'), plugins_url(self::$plugin_name . '/assets/iconC.png'));
      }
    }

    public static function admin_init()
    {
      add_option("CzaterId", "0");
      add_option("czaterCode", "1");
      add_option("css_template", "");
      add_option("czaterForLoggedUsers", "1");
      add_option("czaterAutoCompliteLogin", "1");
      add_option("czaterAutoCompliteEmail", "1");
    }

    private function get_string_between($string, $start, $end)
    {
      $string = " " . $string;
      $ini = strpos($string, $start);
      if ($ini == 0)
        return "";
      $ini += strlen($start);
      $len = strpos($string, $end, $ini) - $ini;
      return substr($string, $ini, $len);
    }

    public static function czater_settings_page()
    {
      if ($_POST['send']) {
        preg_match('/[0-9a-f]{40}/i', $_POST['czaterCode'], $matches);
        update_option('CzaterId', $matches[0]);

        preg_match('/(css_template:)([\W]*)([0-9a-zA-Z]{8})/', $_POST['czaterCode'], $code);
        if ($code[3]) {
          update_option("css_template", $code[3]);
        } else {
          update_option("css_template", '');
        }

        update_option('czaterCode', stripslashes_deep($_POST['czaterCode']));
        update_option('czaterForLoggedUsers', sanitize_text_field(trim($_POST['czaterForLoggedUsers'])));
        update_option('czaterAutoCompliteLogin', sanitize_text_field(trim($_POST['czaterAutoCompliteLogin'])));
        update_option('czaterAutoCompliteEmail', sanitize_text_field(trim($_POST['czaterAutoCompliteEmail'])));
        echo "<p style='background:#bfb;padding:20px;b'>Twój czat już działa na Twojej stronie.</p>";
      }
      ?>
      <h1>Czater.pl</h1>
      <p>
        Twój czat na stronę<?php echo get_option("CzaterId"); ?>
      </p>
      <p>
        Wklej swój kod czatu , który znajdziesz na stronie <a href="https://www.czater.pl/userPanel/codes">Czater.pl</a>
      </p>
      <form method="post" action="<?php echo admin_url('admin.php?page=czater_settings_page') ?>">
        <textarea name="czaterCode"><?php echo get_option("czaterCode"); ?></textarea><br />
        <?php if (get_option("czaterForLoggedUsers") == 1) { ?>
          <label><input type="checkbox" name="czaterForLoggedUsers" value="1" checked="checked"> Pokazuj czater zalogowanym uzytkownikom</label><br />
        <?php } else { ?>
          <label><input type="checkbox" name="czaterForLoggedUsers" value="1"> Pokazuj czater zalogowanym uzytkownikom</label><br />
        <?php } ?>
        <?php if (get_option("czaterAutoCompliteLogin") == 1) { ?>
          <label><input type="checkbox" name="czaterAutoCompliteLogin" value="1" checked="checked"> Automatycznie wypelniaj login</label><br />
        <?php } else { ?>
          <label><input type="checkbox" name="czaterAutoCompliteLogin" value="1"> Automatycznie wypelniaj login</label><br />
        <?php } ?>
        <?php if (get_option("czaterAutoCompliteEmail") == 1) { ?>
          <label><input type="checkbox" name="czaterAutoCompliteEmail" value="1" checked="checked"> Automatycznie wypelniaj email</label><br />
        <?php } else { ?>
          <label><input type="checkbox" name="czaterAutoCompliteEmail" value="1"> Automatycznie wypelniaj email</label><br />
        <?php } ?>
        <input type="submit" value="zapisz" name="send" />
      </form>
<?php
    }
  }
}

//init plugin on frontend
add_action('wp_head', array('Czater', 'wp_head'));

add_action('admin_init', array('Czater', 'admin_init'));
add_action('admin_menu', array('Czater', 'admin_menu'));

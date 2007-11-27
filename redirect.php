<?php
	/**
	* phpGroupWare - Session safe redirect
	*
	* phpgroupware base
	* @internal Idea by Jason Wies <jason@xc.net>
	* @author Lars Kneschke <lkneschke@linux-at-work.de>
	* @author Dave Hall <skwashd@phpgroupware.org>
	* @copyright Copyright (C) 2004-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgroupware
	* @version $Id: redirect.php 15834 2005-04-15 13:19:15Z powerstat $
	*/

	//Get the session variables set for non cookie based sessions
	if (! (@isset($_COOKIES['PHPSESSID']) || @isset($_COOKIES['sessionid']) ) )
	{
		$get = array();
		$url = parse_url($_SERVER['HTTP_REFERER']);
		parse_str($url['query'], $get);
		foreach($get as $key => $val)
		{
			$_GET[$key] = $val;
		}

	}

	$GLOBALS['phpgw_info']['flags'] = array(
					'currentapp'	=> 'home',
					'noheader'	=> True,
					'nonavbar'	=> True,
					'noappheader'	=> True,
					'noappfooter'	=> True,
					'nofooter'	=> True
					);

	/**
	* Include phpgroupware header
	*/
	include_once('header.inc.php');

	if( @isset($_GET['go']) )
	{
		$_GET['go'] = html_entity_decode($_GET['go']);
		?>
			<h2><?php lang('external link'); ?></h2>
			<p><?php lang('lang you are about to visit an external site'); ?><br />
			<?php lang('vist:'); ?> <a href="<?php echo $_GET['go']; ?>" 
				target="_blank"><?php echo $_GET['go']; ?></a></p>
			<script language="JavaScript" type="text/javascript">window.location='<?php echo$_GET['go']; ?>';</script>
		<?php
	        exit;
	}
	else
	{
		
		$GLOBALS['phpgw_info']['flags']['header']	= True;
		$GLOBALS['phpgw_info']['flags']['navbar']	= True;
		$GLOBALS['phpgw_info']['flags']['footer']	= True;
		
		$GLOBALS['phpgw']->common->phpgw_header();
		echo parse_navbar();
		
		echo '<h2>' . lang('invalid link') . "</h2>\n"
			. '<p><a href="' . $GLOBALS['phpgw']->link('/index.php') 
			. lang('return to phpgroupware') . "\"></p>\n";
	}
?>

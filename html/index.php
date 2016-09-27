<?php 
	require_once "/var/www/static/common.php";
	require_once(SESSION_START_PATH);
?>
<!DOCTYPE html PUBLIC "-//IETF//DTD HTML 2.0//EN">
<HTML>
	<HEAD>
		<TITLE>
		A Small Hello 
		</TITLE>
	</HEAD>
	<BODY>
		<script type="text/javascript" src="/static/jquery.js"></script>
		<script type="text/javascript" src="/static/common.js"></script>
		<script>
			
			$(function() {
				$(".confirm").click(function(e) {
					e.preventDefault();
					if ( window.confirm("Are you sure?") ) {
						location.href = this.href;
					}
				});
			});
		</script>

		<style>
		ul {list-style-type: none; margin: 0; padding: 0;}
		li {display: inline;}
		.topCorner {
			position:absolute;
		}
		</style>

		<ul>
			<li>
			</li>
			<?php
			if ( !isset($_SESSION["username"]) ) {
				// if not logged in
				echo '
				<li><a href="register.php">Register</a></li>
				<form action="backend/login.php" method="post">
					Username:
					<input type="text" name="username" style="display: inline;">
					Password:
					<input type="password" name="password">
					<input type="submit" value="Login">
				</form>
				';
			} else {
				// if logged in
				echo '
				<li>Welcome '.printHtml($_SESSION["username"]).'</li>
				<li><a href="backend/logout.php">Logout</a></li>
				';
			}
			?>
		</ul>
		
		<H1>Hi</H1>
		<P>This is very minimal "hello world" HTML document.</P> 
		<br>
		<!--<img src="images/doge.jpeg">-->
		<br>
		<?php
			echo 'Hi from php
			<br>
			'.exec("whoami").'
			<br>
			Time was '.date("H:i:s, l d.m.Y").' - will be updated to be dynamic one day when I get round to it
			<H2>
			<br>
			<a href="https://support.apple.com/kb/DL999?locale=en_US">Download and install this to be able to see the pi on the network from a windows machine!</a>
			<br>
			<br><br>
			<a href="http://pi:32400/web/index.html/">Click me to login to plex! This is where the movies are!</a>
			<br>
			Login as enbornepiuser:enbornepi
			<br><br>
			To access the files on plex themselves open windows explorer - press Ctrl+e whilst on your desktop
			<br>
			Type in &nbsp \\\\pi &nbsp in the search bar<br>
			Go to "pishare" and then "Nas data"
			<br>
			Login as pi:pi if prompted
			<br><br>
			If you find the pi is not working as you would please contact your local chip shop
			<br>
			<a class="confirm" href="/reboot.php">Click here to reboot - please use sensibly</a>
			<br>
			</H2>
			';
			
		?>

		<br>
	</BODY>
</HTML>

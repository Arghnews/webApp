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
		<img src="images/doge.jpeg">
		<br>
		<?php
			echo 'Hi from php
			<br>
			<H2>
			Time is '.date("H:i:s, l d.m.Y").'
			<br><br>
			<a href="http://pi:32400/web/index.html/">Click me to login to plex!</a>
			<br>
			Login as enbornepiuser:enbornepi
			<br><br>
			To access the files on plex themselves open windows explorer - press Ctrl+e whilst on your desktop
			<br>
			Type in &nbsp \\\\pi\pishare\Nas&nbspdata &nbsp in the search bar
			<br>
			Login as pi:pi if prompted
			<br>
			Session should be next - 
			'.printHtml($_SESSION["username"]).'
			</H2>
			';
			
		?>

		<br>
	</BODY>
</HTML>

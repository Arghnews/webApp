<!DOCTYPE html PUBLIC "-//IETF//DTD HTML 2.0//EN">
<HTML>
	<HEAD>
		<TITLE>
		A Small Hello 
		</TITLE>
	</HEAD>
	<BODY>
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
			</H2>
			';
			
		?>

		<br>
		<form action="register.php" method="post">
			<input type="submit" value="Go to registration page!">
		</form>
	</BODY>
</HTML>

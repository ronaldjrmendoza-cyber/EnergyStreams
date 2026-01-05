<?php
/* currently just a static about page but can have a backend/interactivity in the future */
?>

<!DOCTYPE html>
<html lang="en" class="about">
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>About</title>

      <link rel="stylesheet" href="frontend/css/about.css">

      <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

      <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
      <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@300;400;500;600;700&display=swap">

   </head>
   <body class="about about-container">

    <header>
    	<a href="home.php" class="header-image">
        	<img src="frontend/images/logo.png" alt="Energy FM 106.3 Naga Logo" class="logo">
      	</a>

			<section class="header-textcontaner header-text">
				<h1>About</h1>
				<nav>
					<input type="checkbox" id="menu-toggle">
					<label for="menu-toggle" class="menu-icon">&#9776;</label>

					<div class="dropdown-menu">
						<a href="about.php">About</a>
						<a href="profiles.php">Profiles</a>
						<a href="programs.php">Programs</a>
						<a href="stream.php">Stream</a>
						<a href="news.php">News</a>
					</div>
				</nav>
			</section>
    </header>

    <main>
		<section class="article-card1">

			<div class="article-text">
				<h2>Get to know about Energy FM</h2>
				<p> Energy FM is a radio station owned and operated by <strong>Ultrasonic Broadcasting System</strong>. It has several branches
				across the Philippines, including DWBQ, which is located on the 3rd Floor of Traders Square Bldg., P. Burgos St., Naga
				City, Camarines Sur, and broadcasts as 106.3 Energy FM. </p>
			</div>

			<div class="iframe-container">
				<iframe 
					src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d663.8340088949404!2d123.18493481657559!3d13.624004524758991!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33a18cb1516eb475%3A0xc23076ed22c1014c!2sEnergy%20FM%20106.3%20Naga!5e0!3m2!1sen!2sph!4v1764263545151!5m2!1sen!2sph" 
					width="680" 
					height="500" 
					style="border:0;" 
					allowfullscreen="" 
					loading="lazy" 
					referrerpolicy="no-referrer-when-downgrade">
				</iframe>
			</div>
		</section>

		<section class="article-card2">

			<div class="article-img">
				<img src="frontend/images/djs_numberonephoto.jpg" alt="Energy FM Naga DJs Photo 1">
			</div>

			<div class="article-text">
				<h2>A Crowd Favorite<br>in Naga</h2>
				<p> The station is well-known in the city for its popular catchphrases, such as <strong>“Pangga, may<br>
				Energy ka pa ba?”</strong> and <strong>“Basta Energy, Number 1<br> pirmi!”</strong>. It has also been 
				recognized several<br> times as the Number 1 radio station in Naga. </p>
			</div>

		</section>

		<section class="article-card3">

			<div class="article-img">
				<img src="frontend/images/djs_profiles.png" alt="Energy FM Naga DJs Photo 2">
			</div>

			<div class="article-text">
				<h2>Always On.<br>Always Energy.</h2>
				<p>As a typical radio station, Energy FM 106.3 Naga<br> <strong>broadcasts audio content to listeners 24/7</strong> through<br> 
				radio waves, featuring music, advertisements,<br> comedic skits, interviews with public figures and<br> 
				listeners, talk shows, sports updates, public service<br> announcements, political discussions, and the latest<br> 
				news—not just from the city, but also nationally and<br> globally.</p>
			</div>
		</section>

			<section class="contact-card4">

				<h2 class="footer-header">Connect With Us</h2>
				<section class="contact-details">
					<div class="social-container">
						<a href="https://www.facebook.com/share/1D8mB58KNW/?mibextid=wwXIfr" class="social-items" target="_blank">
							<p><i class="bi bi-facebook"></i></p>
							<p>Energy FM Naga</p>
						</a>
						<a href="https://www.facebook.com/share/1bGKXAipkL/?mibextid=wwXIfr" class="social-items" target="_blank">
							<p><i class="bi bi-facebook"></i></p>
							<p>DJ Makisig</p>
						</a>
						<a href="https://www.tiktok.com/@djmakisig" class="social-items" target="_blank">
							<p><img src = "frontend/images/tiktok.svg" class="tiktok-svg" alt="Tiktok Logo"></p>
							<p>DJ Makisig</p>
						</a>
						<a href="https://youtube.com/@djmakisig?si=i3tHyXQdtRPaq-Jj" class="social-items" target="_blank">
							<p><i class="bi bi-youtube"></i></p>
							<p>DJ Makisig</p>
						</a>
					</div>

					<div class="contact-container">
						<a href="https://mail.google.com/mail/u/0/?view=cm&fs=1&tf=1&to=energyfmnaga1063@gmail.com" class="contact-items" target="_blank">
							<p><i class="bi bi-envelope"></i></p>
							<p>energyfmnaga1063@gmail.com</p>
						</a>
						<div class="contact-items" target="_blank">
							<p><i class="bi bi-telephone"></i></p>
							<p>+63-917-113-7249</p>
						</div>
						<a href="https://maps.app.goo.gl/XydzqMZs4kV1EdJe7" class="contact-items" target="_blank">
							<p><i class="bi bi-geo-alt-fill"></i></p>
							<p>
								3F Traders' Square Building, P. Burgos Street,<br>
								Brgy. Sta. Cruz, Naga City, Philippines
							</p>
						</a>
					</div>
				</section>
			</section>
    	</main>

		<div class="footer">
     		 <footer>Privacy Policy | Energy FM © 2025</footer>
    	</div>
  </body>
</html>

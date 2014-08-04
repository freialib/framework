<!DOCTYPE html>
<meta charset="utf-8"/>
<title><?= $server['sitetitle'] ?></title>
<meta name="description" content="don't forget to be awesome" />

<style type="text/css">
	<?php include "{$server['wwwpath']}/web/main.criticalpath.css" ?>
</style>

<body>

<div class="window">

	<div class="window-Content">

		<!-- example -->

		<div class="hello hello--magical">

			<p class="hello-Lead">Hello, <span class="hello-Thing">welcome to freia</span></p>
			<p>This small application has been built to help you get started.</p>

			<p>All example have "example" in the name of the file, the directory,
			the namespace or whatever else; that should make it relatively easy to
			remove the code once you understand it and wish to write your own logic.</p>

			<p>The example demonstrates: <a href="http://php.net/">php</a>, javascript, <a href="http://sass-lang.com/">scss</a>, and a <a href="http://gulpjs.com/">build process</a> to glue everything togheter.</p>

			<p>For more informtion please <a href="http://freialib.github.io/" class="btn btn--wide">read the docs</a></p>

			<p class="hello-Disclaimer">Freia is a library, not a framework,
			this framework application is just an example/recomendation.</p>

		</div>

		<!-- /example -->

		<hr/>

		<div id="jsx-mountpoint">

			<noscript>
				<p>This area requires javascript to function.</p>
				<p>Please enable/whitelist javascript for this site.</p>
			</noscript>

		</div>

		<hr/>

		<p><em>Please explore the source files for more details</em></p>

	</div>

</div>

<style type="text/css"> @import "<?= $server['baseurl'] ?>/web/main.css"; </style>
<script src="<?= $server['baseurl'] ?>/web/main.js"></script>

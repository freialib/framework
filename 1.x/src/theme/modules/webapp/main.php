<!DOCTYPE html>
<meta charset="utf-8"/>
<title><?= $server['sitetitle'] ?></title>
<meta name="description" content="don't forget to be awesome" />

<style type="text/css">
	<?= include "{$server['wwwpath']}/web/main.criticalpath.css" ?>
</style>

<body>

<div class="window">
	<div class="window-Content">

		<p class="hello hello-magical">
			hello, <span class="hello-Thing">welcome to freia</span>
		</p>

		<p>This small application has been built to help you get started
		if you're starting from scratch.</p>

		<a class="btn btn-wide">read the docs</a>

		<hr/>

		<div id="app-jsx-mountpoint">

			<noscript>
				<p>This area requires javascript to function.</p>
				<p>Please enable/whitelist javascript for this site.</p>
			</noscript>

		</div>

	</div>
</div>

<style type="text/css"> @import "<?= $server['baseurl'] ?>/web/main.css"; </style>
<script src="<?= $server['baseurl'] ?>/web/main.js"></script>

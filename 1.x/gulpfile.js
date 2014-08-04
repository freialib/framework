var gulp   = require('gulp');
var concat = require('gulp-concat');
var order  = require('gulp-order');

var merge  = require('event-stream').merge;
var util   = require('util');

var supported_browser = ['last 2 version', 'ie 8', 'ie 9'];

// Styles
// ------

var sass   = require('gulp-sass');
var cssmin = require('gulp-cssmin');
var prefix = require('gulp-autoprefixer');

gulp.task('css', function () {

	gulp.src('./src/client/scss/main.criticalpath.scss')
		.pipe(sass())
		.pipe(prefix(supported_browser))
		.pipe(cssmin())
		.pipe(gulp.dest('./stage/web/'));

	gulp.src('./src/client/scss/main.scss')
		.pipe(sass())
		.pipe(prefix(supported_browser))
		.pipe(cssmin())
		.pipe(gulp.dest('./stage/web/'));
});

// Scripts
// -------

var nodeify  = require('gulp-browserify');
var reactify = require('reactify');
var polyfill = require('gulp-autopolyfiller');
var uglify   = require('gulp-uglify');
var glob     = require("glob")

var module_mapping = [
	{
		cwd: 'src/client/javascript/webapp/lib',
		dest: 'lib',
		src: '**/*.js'
	},
	{
		cwd: 'src/client/javascript/webapp/system',
		dest: 'system',
		src: '**/*.js'
	},
];

gulp.task('js', function () {

	var mainjs = gulp.src('./src/client/javascript/main.js')
		.pipe(nodeify({
			transform: ['reactify']
		}).on('prebundle', function (bundler) {
			for (var i in module_mapping) {
				var conf = module_mapping[i];
				var files = glob.sync(conf['src'], {'cwd': conf['cwd']});
				for (var j in files) {
					var file = __dirname + '/' + conf['cwd'] + '/' + files[j];
					bundler.require(file, {
						expose: conf['dest'] + '/' + (files[j].replace(/\.jsx?$/, ''))
					});
				}
			}
		}))
		.pipe(concat('main.js'));

	var polyfills = mainjs
		.pipe(polyfill('polyfills.js', { browsers: supported_browser }));

	merge(polyfills, mainjs)
		.pipe(order(['polyfills.js', 'main.js']))
		.pipe(concat('main.js'))
		.pipe(uglify())
		.pipe(gulp.dest('./stage/web/'));
});

// Assets
// ------

var imagemin = require('gulp-imagemin');
var pngcrush = require('imagemin-pngcrush');

gulp.task('assets', function () {
	gulp.src('./src/client/images/**/*')
		.pipe(imagemin({ use: [ pngcrush() ] }))
		.pipe(gulp.dest('./stage/web/images/'));
});

// Web-Confs
// ---------

gulp.task('confs', function () {
	gulp.src('./src/client/confs/**/*')
		.pipe(gulp.dest('./stage/'));
});

// Entry-Points
// ------------

gulp.task('entry-points', function () {
	gulp.src('./src/server/theme/public/**/*')
		.pipe(gulp.dest('./stage/'));
});

// Watch
// -----

gulp.task('watch', function() {
	gulp.watch('./src/client/scss/**/*', ['css']);
	gulp.watch('./src/client/javascript/**/*', ['js']);
	gulp.watch('./src/client/images/**/*', ['assets']);
	gulp.watch('./src/client/confs/**/*', ['confs']);
	gulp.watch('./src/server/theme/public/**/*', ['entry-points']);
});

// Main
// ----

gulp.task('default', [
	'entry-points', 'confs', 'css', 'js', 'assets'
]);

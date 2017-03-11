var gulp		= require('gulp');
var concat		= require('gulp-concat');  
var uglify		= require('gulp-uglify');  
var sass		= require('gulp-sass');
var sourcemaps	= require('gulp-sourcemaps');
var rename		= require('gulp-rename');
var importer	= require('gulp-fontello-import');
var replace		= require('gulp-replace');
var loadConfig	= require('load-config-file');
var source		= require('vinyl-source-stream');

loadConfig.register('.json', JSON.parse); 

var paths = {
	styles	: ['./sass/style.scss','./sass/editor-style.scss']
};



var fontello = {
	configFile:	'./src/icons/config.json',
	iconDir: 	'./src/icons',
	fontDest:	'./fonts/fontello/',
	sassDest:	'./sass/fonts/',
	tmpDir:		'./src/tmp/',
};

gulp.task( 'fontello-import', function( cb ) {
	// scan icon dir
	importer.importSvg({
		config:	fontello.configFile,
		svgsrc:	fontello.iconDir,
	},cb);
});


gulp.task( 'fontello-generate', ['fontello-import'], function(cb) {
    importer.getFont({
		config:	fontello.configFile,
		font:	fontello.fontDest,
		css:	fontello.tmpDir,
    },cb);
} );

gulp.task( 'fontello-scss', ['fontello-generate'], function(cb) {
	var fontConfig	= loadConfig( fontello.configFile ),
		fontName	= fontConfig.name,
		prefix		= fontConfig.css_prefix_text;

	var s = new RegExp( "\."+prefix+"([a-z0-9-_]+):before \{ content: '\\\\([a-f0-9]+)'; \}", 'g' );
	var r = "$" + prefix + "$1: '\\$2';\n." + prefix + "$1:before { content: $" + prefix + "$1; }";

	var cachebuster = source( '_fontello-cachebuster.scss' );
	cachebuster.end( "$fontello-cachebuster: '" + (new Date()).getTime().toString(16) + "'\n" );
	cachebuster.pipe( gulp.dest( fontello.sassDest ) )

	return gulp.src( fontello.tmpDir + fontName + '-codes.css' )
		.pipe(replace(s, r))
		.pipe(rename('_fontello-codes.scss'))
		.pipe( gulp.dest( fontello.sassDest ) );
});





gulp.task( 'scss:dev',	function() { 
	return gulp.src( paths.styles )
		.pipe( sourcemaps.init() )
		.pipe( sass({
			precision: 8,
			stopOnError: true,
			require: './sass/library/base64-encode.rb',
			noCache: true
		}) )
		.on('error', sass.logError)
		.pipe(sourcemaps.write( './' ) )
		.pipe( gulp.dest('./'));
});



gulp.task('watch', function() {
 	gulp.watch( fontello.iconDir + '/*.svg', [ 'fontello-scss' ] );
	gulp.watch('./sass/**/*.scss', [ 'scss:dev' ] );
});

gulp.task('default', ['watch'] );

gulp.start( 'default', ['fontello-scss'] );



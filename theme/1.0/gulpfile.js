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
var clean		= require('gulp-clean');
var runSequence	= require('run-sequence');

loadConfig.register('.json', JSON.parse); 

var path = {
	styles	: ['./sass/style.scss','./sass/editor-style.scss'],
	tmp		: './src/tmp/',
};



// Fontello: settings object
var fontello = {
	sourceDir			: './src/icons/',
	sourceConfigFile	: 'config.json',
	configFile			: 'config-generated.json',
	fontDest			: './fonts/fontello/',
	scssDest			: './sass/fonts/',
};

// Fontello: copy original fontello config
gulp.task( 'fontello-config', function(cb) {
	return gulp.src( fontello.sourceDir + fontello.sourceConfigFile )
		.pipe( rename( fontello.configFile ) )
		.pipe( gulp.dest( path.tmp ) );
} );

// Fontello: import SVG-Icons into fontello config
gulp.task( 'fontello-import', ['fontello-config'], function( cb ) {
	// scan icon dir
	importer.importSvg({
		config:	path.tmp + fontello.configFile,
		svgsrc:	fontello.sourceDir,
	},cb);
});

// Fontello: generate font from fontello config
gulp.task( 'fontello-generate', ['fontello-import'], function(cb) {
    importer.getFont({
		config:	path.tmp + fontello.configFile,
		font:	fontello.fontDest,
		css:	path.tmp,
    },cb);
} );

// Fontello: generate scss from fontello css
gulp.task( 'fontello-scss', ['fontello-generate'], function(cb) {
	var fontConfig	= loadConfig( fontello.sourceDir + fontello.sourceConfigFile ),
		fontName	= fontConfig.name,
		prefix		= fontConfig.css_prefix_text;

	var s = new RegExp( "\."+prefix+"([a-z0-9-_]+):before \{ content: '\\\\([a-f0-9]+)'; \}", 'g' );
	var r = "$" + prefix + "$1: '\\$2';\n." + prefix + "$1:before { content: $" + prefix + "$1; }";

	var cachebuster = source( '_fontello-cachebuster.scss' );
	cachebuster.end( "$fontello-cachebuster: '" + (new Date()).getTime().toString(16) + "'\n" );
	cachebuster.pipe( gulp.dest( fontello.scssDest ) )

	return gulp.src( path.tmp + fontName +'-codes.css')
		.pipe(replace(s, r))
		.pipe(rename('_fontello-codes.scss'))
		.pipe( gulp.dest( fontello.scssDest ) );
});

// Fontello: cleanup 
gulp.task( 'fontello-clean', function() {
	return gulp.src( path.tmp + '*.*', { read: false } )
		.pipe( clean() );
});

// Fontello: main task
gulp.task('fontello', function() {
	runSequence('fontello-scss', 'fontello-clean');
});





gulp.task( 'scss',	function() { 
	return gulp.src( path.styles )
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
	gulp.watch('./sass/**/*.scss', [ 'scss' ] );
 	gulp.watch( fontello.sourceDir + '*.*', [ 'fontello' ] );
});

gulp.task('default', ['watch'] );

gulp.start( 'default', [ 'fontello','scss'] );



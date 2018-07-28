var autoprefixer	= require('gulp-autoprefixer');
var gulp			= require('gulp');
var concat			= require('gulp-concat');
var uglify			= require('gulp-uglify');
var sass			= require('gulp-sass');
var sourcemaps		= require('gulp-sourcemaps');
var rename			= require('gulp-rename');
var importer		= require('gulp-fontello-import');
var replace			= require('gulp-replace');
var source			= require('vinyl-source-stream');
var clean			= require('gulp-clean');
var runSequence		= require('run-sequence');
var nodeSass		= require('node-sass');

var path = {
	styles	: ['./src/scss/style.scss','./src/scss/editor-style.scss'],
	tmp		: './src/tmp/',
};

var sassOptions = {
	outputStyle: 'compressed',
	precision: 8,
	stopOnError: true,
	functions: {
		'base64Encode($string)': function($string) {
			var buffer = new Buffer( $string.getValue() );
			return nodeSass.types.String( buffer.toString('base64') );
		}
	}

};


// Fontello: settings object
var fontello = {
	sourceDir			: './src/icons/',
	sourceConfigFile	: 'config.json',
	configFile			: 'config-generated.json',
	fontDest			: './fonts/fontello/',
	scssDest			: './src/scss/fonts/',
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
	var fontConfig	= require( fontello.sourceDir + fontello.sourceConfigFile ),
		fontName	= fontConfig.name,
		prefix		= fontConfig.css_prefix_text;

	var s = new RegExp( "\."+prefix+"([a-z0-9-_]+):before \{ content: '\\\\([a-f0-9]+)'; \}", 'g' );
	var r = "$" + prefix + "$1: '\\$2';\n." + prefix + "$1:before { content: $" + prefix + "$1; }";

	var cachebuster = source( '_fontello-vars.scss' );
	cachebuster.end( "$fontello-cachebuster: '" + (new Date()).getTime().toString(16) + "';\n$fontello-fontname: '"+fontName+"';\n" );
	cachebuster.pipe( gulp.dest( fontello.scssDest ) )

	return gulp.src( path.tmp + fontName +'-codes.css')
		.pipe( replace(s, r) )
		.pipe( rename('_fontello-codes.scss') )
		.pipe( gulp.dest( fontello.scssDest ) );
});

// Fontello: cleanup
gulp.task( 'fontello-clean', function() {
	return gulp.src( path.tmp + '*.*', { read: false } )
		.pipe( clean() );
});

// Fontello: main task
gulp.task('fontello', function() {
	runSequence( 'fontello-scss', 'fontello-clean', 'scss' );
});






gulp.task( 'fonts', function() {

	var dir_filter = function( file ) {
			return fs.statSync( path.join( fonts_dir, file ) ).isDirectory();
		},
		ttf_filter = function( file ) {
			return path.extname(file) === '.ttf';
		},
		get_font_specs = function( fontfile ) {
			var name = path.basename( fontfile, path.extname( fontfile ) )
						.replace( /(italic|oblique|thin|hairline|extralight|light|regular|normal|medium|demi|semibold|bold|extrabold|heavy|black)/ig, '' )
						.replace( /[^a-z0-9]$/ig,''),
				italic = !! fontfile.match( /(italic|oblique)/i ),
				weights = [
					{ weight:'100',	regex: /(thin|hairline)/ig, },
					{ weight:'200',	regex: /(extralight)/ig, },
					{ weight:'300',	regex: /(light)/ig, },
					{ weight:'500',	regex: /(medium)/ig, },
					{ weight:'600',	regex: /(demi|semibold)/ig, },
					{ weight:'700',	regex: /(bold)/ig, },
					{ weight:'800',	regex: /(extrabold|heavy)/ig, },
					{ weight:'900',	regex: /(black)/ig, },
					{ weight:'400',	regex: /./i, },
				],
				weight, i;

			for ( i = 0; i < weights.length; i++ ) {
				weight = weights[i].weight;
				if ( !! fontfile.match(weights[i].regex) ) {
					break;
				}
			}
			return {
				name	: changeCase.headerCase(name),
				italic	: italic,
				weight	: weight,
				src		: fontfile,
			};
		},
		fonts_dir = './src/fonts/',
		dest_dir = './fonts/',
		font_dirs = fs.readdirSync( fonts_dir ).filter( dir_filter ),
		font_files, font_specs, src, dest, scss, slug, scss_handle, taskname, scss_handle,
		tasks = [];

	for ( var i in font_dirs ) {
		// get font files
		font_files = fs.readdirSync( fonts_dir + font_dirs[i] ).filter( ttf_filter ),
		sources = [];
		// get font names
		scss = '/* gulp generated @font-face */'+"\n";
		for ( var j in font_files ) {
			font_specs = get_font_specs( font_files[j] );
			slug = changeCase.paramCase( font_specs.name );
			src = fonts_dir + font_dirs[i] + '/' + font_files[j];
			dest = dest_dir + slug + '/';
			dest_file = changeCase.paramCase(
				path.basename(
					font_files[j],
					path.extname( font_files[j] )
				)
			);

//			sources.push(src)

			scss += '@font-face {\n';
			scss += '\tfont-family: \''+font_specs.name+'\';\n';
			scss += '\tsrc: url(\'' + dest + dest_file + '.woff2\') format(\'woff2\'),\n',
			scss += '\t\t url(\'' + dest + dest_file + '.woff\') format(\'woff\');\n',
			scss += '\tfont-weight: '+font_specs.weight+';\n';
			scss += '\tfont-style: '+(font_specs.italic ? 'italic' : 'normal')+';\n';
			scss += '}\n\n';
			taskname = changeCase.paramCase( font_specs.name ) + '-' + font_specs.weight + (font_specs.italic?'i':'');

			(function(taskname){
				console.log( 'Begin woff: ' + taskname );
				tasks.push(gulp.src([src])
					.pipe(ttf2woff())
					.pipe(rename({basename:dest_file}))
					.pipe(gulp.dest( dest ))
					.on('end',function(){
						console.log( 'Done woff: ' + taskname );
					})
				);
			})(taskname);

			(function(taskname){
				console.log( 'Begin woff2: ' + taskname );
				tasks.push(gulp.src([src])
					.pipe(ttf2woff2())
					.pipe(rename({basename:dest_file}))
					.pipe(gulp.dest( dest ))
					.on('end',function(){
						console.log( 'Done woff2: ' + taskname );
					})
				);
			})(taskname);
		}

		// write scss
		scss_handle = source( '_'+slug+'.scss' );
		scss_handle.end( scss );
		scss_handle.pipe( gulp.dest( './src/scss/fonts/' ) );

	}

	return tasks;

 });





gulp.task( 'scss',	function() {
	return gulp.src( path.styles )
		.pipe( sourcemaps.init() )
		.pipe(
        	sass( sassOptions )
        	.on('error', sass.logError)
		)
        .pipe( autoprefixer( { browsers: ['last 3 versions'] } ) )
		.pipe( sourcemaps.write( './' ) )
		.pipe( gulp.dest('./'));
});



gulp.task('watch', function() {
	gulp.watch('./src/scss/**/*.scss', [ 'scss' ] );
 	gulp.watch( fontello.sourceDir + '*.*', [ 'fontello' ] );
});

gulp.task('default', ['scss', 'watch'] );

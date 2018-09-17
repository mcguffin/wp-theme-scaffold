var autoprefixer	= require('gulp-autoprefixer');
var changeCase		= require('change-case');
var clean			= require('gulp-clean');
var concat			= require('gulp-concat');
var File			= require('vinyl');
var fs				= require('fs');
var gulp			= require('gulp');
var importer		= require('gulp-fontello-import');
var nodeSass		= require('node-sass');
var path			= require('path');
var rename			= require('gulp-rename');
var gulpCopy		= require('gulp-copy');
var replace			= require('gulp-replace');
var sass			= require('gulp-sass');
var source			= require('vinyl-source-stream');
var sourcemaps		= require('gulp-sourcemaps');
var Stream			= require('stream');
var ttf2woff		= require('gulp-ttf2woff');
var ttf2woff2		= require('gulp-ttf2woff2');
var uglify			= require('gulp-uglify');


var config = {
	path	: {
		styles	: ['./src/scss/style.scss','./src/scss/editor-style.scss'],
		tmp		: './src/tmp/',
	},
	scss	: {
		outputStyle: 'compressed',
		precision: 8,
		stopOnError: true,
		functions: {
			'base64Encode($string)': function($string) {
				var buffer = new Buffer( $string.getValue() );
				return nodeSass.types.String( buffer.toString('base64') );
			}
		}
	},
	fontello	: {
		sourceDir			: './src/icons/',
		sourceConfigFile	: 'config.json',
		configFile			: 'config-generated.json',
	},
	fonts		: {
		fontDest	:	'./fonts/',
		scssDest	:	'./src/scss/fonts/',
	}
}



var fontelloFn = {
	config	: function(cb) {
		console.log('run fontello config')
		return gulp.src( config.fontello.sourceDir + config.fontello.sourceConfigFile )
			.pipe( rename( config.fontello.configFile ) )
			.pipe( gulp.dest( config.path.tmp ) );
	},
	import	: function( cb ) {
		console.log('run fontello import')
		// scan icon dir
		importer.importSvg({
			config:	config.path.tmp + config.fontello.configFile,
			svgsrc:	config.fontello.sourceDir,
		},cb);
	},
	generate	: function(cb) {
	    importer.getFont({
			config:	config.path.tmp + config.fontello.configFile,
			font:	config.fonts.fontDest + 'fontello/',
			css:	config.path.tmp,
	    },cb);
	},
	scss : function(cb) {

		var fontConfig	= require( config.fontello.sourceDir + config.fontello.sourceConfigFile ),
			fontName	= fontConfig.name,
			prefix		= fontConfig.css_prefix_text;
		fs.writeFileSync(
			config.fonts.scssDest + '_fontello-vars.scss',
			"/* Generated file */" +
			"$fontello-cachebuster: '" + (new Date()).getTime().toString(16) + "';\n" +
			"$fontello-fontname: '"+fontName+"';\n"
		)

		var s = new RegExp( "\."+prefix+"([a-z0-9-_]+):before \{ content: '\\\\([a-f0-9]+)'; \}", 'g' );
		var r = "$" + prefix + "$1: '\\$2';\n." + prefix + "$1:before { content: $" + prefix + "$1; }";

		return gulp.src( config.path.tmp + fontName +'-codes.css')
			.pipe( replace(s, r) )
			.pipe( rename('_fontello-codes.scss') )
			.pipe( gulp.dest( config.fonts.scssDest ) );
	},
	clean : function() {
		return gulp.src( config.path.tmp + '*.*', { read: false } )
			.pipe( clean() );
	}
}

gulp.task('fontello', gulp.series( fontelloFn.config, fontelloFn.import, fontelloFn.generate, fontelloFn.scss, fontelloFn.clean ) );



var webfonts = function( config ) {
	var stream = new Stream.Transform({objectMode: true}),
		config = config || {},
		conf = {
			font_dir: './fonts/',
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
		};
	Object.keys(config).forEach(function(k){
		conf[k] = config[k];
	})

	stream._transform = function ( file, unused, callback) {
		// do transformations...
		var ttf = [],
			scss = '/* Generated file. Do not edit. */\n',
			slug = changeCase.paramCase(file.basename);

		fs.readdirSync(file.path).forEach(function(el,i){
			//ttf.push( el )
			if ( path.extname( el ) !== '.ttf') {
				return;
			}
			var font_specs = get_font_specs( el ),
				dest_file = changeCase.paramCase(
					path.basename(
						el,
						path.extname( el )
					)
				);

			scss_dest = conf.font_dir.replace(/^\.\//,'') + slug + '/' + dest_file;

			scss += '@font-face {\n';
			scss += '\tfont-family: \''+font_specs.name+'\';\n';
			scss += '\tsrc: url(\'' + scss_dest + '.woff2\') format(\'woff2\'),\n',
			scss += '\t\t url(\'' + scss_dest + '.woff\') format(\'woff\');\n',
			scss += '\tfont-weight: '+font_specs.weight+';\n';
			scss += '\tfont-style: '+(font_specs.italic ? 'italic' : 'normal')+';\n';
			scss += '}\n\n';
			//*
			if ( ! fs.existsSync( conf.font_dir + '/' + slug + '/' + dest_file + '.woff' ) ) {
				gulp.task(dest_file+'-woff',function(){
					return gulp.src( file.path + '/' + el )
						.pipe( ttf2woff() )
						.pipe( rename( { basename: dest_file } ) )
						.pipe(gulp.dest( conf.font_dir + '/' + slug + '/' ) )
				});
				gulp.task(dest_file+'-woff')();
			}

			if ( ! fs.existsSync( conf.font_dir + '/' + slug + '/' + dest_file + '.woff2' ) ) {
				gulp.task(dest_file+'-woff2',function(){
					return gulp.src( file.path + '/' + el )
						.pipe( ttf2woff2() )
						.pipe( rename( { basename: dest_file } ) )
						.pipe(gulp.dest( conf.font_dir + '/' + slug + '/' ) )
				});
				gulp.task( dest_file + '-woff2' )();
			}

		});

		// make scss out of it
		file.contents = Buffer.from(scss);
		file.basename = '_'+slug+'.scss';

		callback( null, file ); // signal finished
	}
	return stream;
}



gulp.task('fonts',function( task_done_cb ){
	return gulp.src('./src/fonts/*')
		.pipe( webfonts( { font_dir: config.fonts.fontDest	} ) )
		.pipe( gulp.dest( config.fonts.scssDest ) )
} );



gulp.task('vendor-scripts',function(){
	return gulp.src([
			'node_modules/bootstrap/dist/js/bootstrap.js',
			'node_modules/bootstrap/dist/js/bootstrap.bundle.js',
			'node_modules/bootstrap/dist/js/bootstrap.min.js',
			'node_modules/bootstrap/dist/js/bootstrap.bundle.min.js',
		])
		.pipe( gulpCopy('js/vendor/bootstrap/', { prefix: 4 } ) );
});


gulp.task( 'scss',	function() {
	return gulp.src( config.path.styles )
		.pipe( sourcemaps.init() )
		.pipe(
        	sass( config.scss )
        	.on('error', sass.logError)
		)
        .pipe( autoprefixer( { browsers: ['last 3 versions'] } ) )
		.pipe( sourcemaps.write( './' ) )
		.pipe( gulp.dest('./'));
});



gulp.task('watch', function() {
	gulp.watch('./src/scss/**/*.scss', gulp.parallel( 'scss' ) );
 	gulp.watch( config.fontello.sourceDir + '*.*', gulp.parallel( 'fontello' ) );
});


gulp.task( 'init', gulp.parallel('fontello', 'fonts', 'vendor-scripts', 'scss' ) );

gulp.task( 'default', gulp.parallel('scss', 'watch') );

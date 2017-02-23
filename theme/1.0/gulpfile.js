var gulp = require('gulp');
var concat = require('gulp-concat');  
var uglify = require('gulp-uglify');  
var sass = require('gulp-sass');
//var sass = require('gulp-ruby-sass');
var sourcemaps = require('gulp-sourcemaps');
var rename = require('gulp-rename');

var paths = {
	styles	: ['./sass/style.scss','./sass/editor-style.scss']
};


gulp.task( 'scss:dev', 		function() { 
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
	gulp.watch('./sass/**/*.scss', [ 'scss:dev' ] );
});

gulp.task('default', ['watch'] );

gulp.start( 'default', 'scss:dev' );

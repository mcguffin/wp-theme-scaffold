var gulp = require('gulp');
var concat = require('gulp-concat');  
var uglify = require('gulp-uglify');  
var sass = require('gulp-sass');
var sourcemaps = require('gulp-sourcemaps');
var rename = require('gulp-rename');

var paths = {
	styles	: ['./sass/style.scss','./sass/editor-style.scss']
};

gulp.task( 'scss:prod', 		function() { 
    return gulp.src( paths.styles )
		.pipe( sass( { 
			outputStyle: 'compressed', omitSourceMapUrl: true 
		} ).on('error', sass.logError) )
		.pipe( gulp.dest('./'+path));
});
gulp.task( 'scss:dev', 		function() { 
    return gulp.src( paths.styles )
		.pipe( sourcemaps.init() )
        .pipe( sass( { 
        	outputStyle: 'expanded' 
        } ).on('error', sass.logError) )
        .pipe( sourcemaps.write() )
		.pipe( gulp.dest('./'+path));
});

gulp.task('default', function() {
	//*
	gulp.watch(paths.styles, [ 'scss:dev' ] );
	/*/
	gulp.watch(paths.styles, [ 'scss:prod' ] );
	//*/
});


var gulp = require( 'gulp' ),
    connect = require( 'gulp-connect' ),
    open = require( 'gulp-open' ),
    compass = require( 'gulp-compass' ),
    plumber = require( 'gulp-plumber' ),
    jshint = require( 'gulp-jshint' ),
    watch = require( 'gulp-watch' ),
    clean = require( 'gulp-clean' ),
    copy = require( 'gulp-copy' ),
    inline = require( 'gulp-inline' ),
    uglify = require( 'gulp-uglify' ),
    minifyCSS = require( 'gulp-clean-css' ),
    replace = require( 'gulp-replace' ),
    htmlmin = require( 'gulp-htmlmin' ),
    image = require('gulp-image'),
    zip = require( 'gulp-zip' ),
    argv = require( 'yargs' ).argv,
    jasmine = require('gulp-jasmine'), 
    notify = require('gulp-notify'),
    del = require('del'),
    os = require('os'),
    args = require('yargs').argv;

var src = './src',
    dist = './dist',
    baseURL = ( argv.production === undefined ) ? src : dist,
    port = 8000 + Math.floor(Math.random() * 1000),
    uri = 'http://localhost:' + port;

var uncompressedImgs = args.fullsize ? args.fullsize.split(',').map(function(img) { return '!' + dist + '/' + img; }) : [];


gulp.task( 'default', [ 'serve' ] );
gulp.task( 'serve', [ 'connect', 'open', 'watch' ] );
gulp.task( 'build',[ 'clean', 'copy','sass', 'inline', 'replace', 'htmlmin', 'compress', 'testProd', ] );

gulp.task( 'test', function() {
    gulp.src('tests/test.js')
        .pipe(jasmine())
        .on('error', notify.onError({
            title: 'Jasmine Test Failed', 
            message: 'One or more tests failed, see cli for details.'
        }));
} );

//tests after build
gulp.task( 'testProd', ['compress'], function() {
    gulp.src('tests/test.js')
        .pipe(jasmine())
        .on('error', notify.onError({
            title: 'Jasmine Test Failed', 
            message: 'One or more tests failed, see cli for details.'
        }));
} );

gulp.task( 'connect', ['sass'], function() {
	connect.server( {
  		root: baseURL,
    	livereload: true,
    	port: port
  	} );
} );

gulp.task( 'open', ['connect'], function() {
	var options = {
		uri: uri,
    app: os.platform() === 'darwin' ? 'google chrome' : 'chrome'
	};
	gulp.src( './' )
		.pipe( open( options ) );
});

gulp.task( 'html', function () {
	gulp.src( src + '/*.html' )
    	.pipe( connect.reload() );
});

(function(tasks) {
  tasks.forEach(function(task) {
    gulp.task( 'sass' + task.append, function( done ) {
    	gulp.src( './sass/*.scss' )
    		.pipe( plumber( {
    			errorHandler: function ( error ) {
    				console.log( error.message );
    				this.emit( 'end' );
    			}
    		} ) )
        	.pipe( compass( {
        		css: src + '/css',
        		sass: './sass',
        		image: src + '/',
        		style: 'nested',
            generated_images_path: src + '/img',
            force: task.force
        	} ) )
        	.on( 'error', function( error ) {
          		console.log( error.message ); 
        	} )
          .pipe( replace( 'sprites/', '' ) )
        	.pipe( gulp.dest( src + '/css' ) )
          .pipe( connect.reload() )
        	.on('end', function () { done(); });
    });

    gulp.task('moveGeneratedImages' + task.append, ['sass' + task.append], function() {
      return gulp.src(src + '/img/sprites/**/*.png')
        .pipe(gulp.dest(src + '/img'));
    });

    gulp.task('cleanSprites' + task.append, ['moveGeneratedImages' + task.append], function() {
      return del(src + '/img/sprites');
    });
  });
})([
  { append: '', force: false },
  { append: 'Force', force: true }
]);

gulp.task( 'lint', function() {
	return gulp.src( src + '/js/*.js' )
    		   .pipe( jshint() )
    		   .pipe( jshint.reporter( 'default' ) )
           .pipe( connect.reload() );
});

gulp.task( 'watch', function() {
	gulp.watch( [ src + '/*.html' ], [ 'html' ] );
	gulp.watch( [ './sass/*.scss'], [ 'sass', 'moveGeneratedImages', 'cleanSprites' ] );
	gulp.watch( [ src + '/js/*.js'], [ 'lint' ] );
  gulp.watch( [ 'src/sprites/**/*'], [ 'sassForce', 'moveGeneratedImagesForce', 'cleanSpritesForce' ]);
});

gulp.task( 'clean', function () {
	return gulp.src( [ dist + '/**/*' ], { read: false } )
    		   .pipe( clean() );
});

gulp.task( 'copy', [ 'clean', 'cleanSprites' ], function() {
	return gulp.src( [src + '/*.{jpg,png}', src + '/img/**/*.{jpg,png,gif,svg}', src + '/manifest.js' ] )
  			   .pipe( copy( dist, { prefix: 2 } ) );
});

gulp.task( 'inline', [ 'sass' ], function( done ) {
	gulp.src( src + '/index.html' )
		.pipe( inline( {
    		base: src,
    		js: uglify,
    		css: minifyCSS,
    		disabledTypes: [ 'img' ]
  	} ) )
  	.pipe( gulp.dest( dist ) )
  	.on('end', function () { done(); });
});

gulp.task( 'replace', [ 'inline' ], function( done ) {
	gulp.src( [ dist + '/index.html' ] )
    	.pipe( replace( '../img/', 'img/') )
        .pipe( replace( 'img/', './') )
    	.pipe( gulp.dest( dist ) )
    	.on('end', function () { done(); });
});

gulp.task('htmlmin', [ 'replace' ], function() {
  return gulp.src( dist + '/index.html' )
    		 .pipe( htmlmin( { collapseWhitespace: true, removeComments: true } ) )
    		 .pipe( gulp.dest( dist ) );
});

gulp.task( 'imageCompress', [ 'copy', 'sass' ], function (done) {
  gulp.src( [dist + '/*.{jpg,png,svg,gif}'].concat(uncompressedImgs) )
    .pipe( image({
      pngquant: true,
      optipng: false,
      zopflipng: false,
      jpegRecompress: true,
      jpegoptim: false,
      mozjpeg: false,
      gifsicle: true,
      svgo: true,
      concurrent: 10
    }))
    .pipe(gulp.dest( dist ))
    .on('end', function() { done(); });
});

gulp.task( 'compress', [ 'clean', 'copy','sass', 'inline', 'replace', 'htmlmin', 'imageCompress' ], function() {
	return gulp.src(  dist + '/*' )
			   .pipe( zip( 'Zelis-1.zip' ) )
			   .pipe( gulp.dest( './' ) );
});


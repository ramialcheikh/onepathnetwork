'use strict';
var path = require('path');

var lrSnippet = require('grunt-contrib-livereload/lib/utils').livereloadSnippet;
var mountFolder = function (connect, dir) {
    return connect.static(require('path').resolve(dir));
};
var packageFolder = 'package';
var packageFileName = 'packagedApp.zip';
var updatePackagesFolder = 'updatePackages';
// # Globbing
// for performance reasons we're only matching one level down:
// 'test/spec/{,*/}*.js'
// use this if you want to match all subfolders:
// 'test/spec/**/*.js'
// templateFramework: 'handlebars'

module.exports = function (grunt) {
    // load all grunt tasks
    require('matchdep').filterDev(['grunt-*', '!grunt-template-jasmine-requirejs']).forEach(grunt.loadNpmTasks);
    // show elapsed time at the end
    require('time-grunt')(grunt);

    // configurable paths
    var yeomanConfig = {
        app: 'public',
        dist: 'dist',
		server: 'app'
    };

    grunt.initConfig({
        yeoman: yeomanConfig,

        // watch list
        watch: {
            
            less: {
                files: ['<%= yeoman.app %>/css/{,**/}*.{less,css}', '<%= yeoman.app %>/themes/{,**/}*.{less,css}'],
                tasks: ['less']
            },
            
            livereload: {
                files: [
                    
                    '<%= yeoman.app %>/*.html',
                    '{.tmp,<%= yeoman.app %>}/css/{,**/}*.css',
					'{.tmp,<%= yeoman.app %>}/themes/flat/{,**/}*.css',
                    '{.tmp,<%= yeoman.app %>}/js/{,**/}*.js',
                    '{.tmp,<%= yeoman.app %>}/templates/{,**/}*.hbs',
                    '<%= yeoman.app %>/images/{,*/}*.{png,jpg,jpeg,gif,webp}',
                    'test/spec/{,**/}*.js'
                ],
                tasks: [],
                options: {
                    livereload: true
                }
            }
            /* not used at the moment
            handlebars: {
                files: [
                    '<%= yeoman.app %>/templates/*.hbs'
                ],
                tasks: ['handlebars']
            }*/
        },

        
        // open app and test page
        open: {
            server: {
                path: 'http://localhost'
            }
        },

        clean: {
            dist: ['.tmp', '<%= yeoman.dist %>/*'],
            server: '.tmp'
        },

        // linting
        jshint: {
            options: {
                jshintrc: '.jshintrc',
                reporter: require('jshint-stylish')
            },
            all: [
                'Gruntfile.js',
                '<%= yeoman.app %>/js/{,*/}*.js',
                '!<%= yeoman.app %>/js/vendor/*',
                'test/spec/{,*/}*.js'
            ]
        },

        
        // Less - using less
		less: {
			main: {
				options: {
				  paths: ['<%= yeoman.app %>/css', '<%= yeoman.app %>/themes/flat/']
				},
				files: {
				  '<%= yeoman.app %>/css/main.css' : [
                        '<%= yeoman.app %>/css/main.less'
                    ],
					'<%= yeoman.app %>/themes/flat/css/style.css' : [
                        '<%= yeoman.app %>/themes/flat/css/style.less'
                    ],
					'<%= yeoman.app %>/css/admin.css' : [
                        '<%= yeoman.app %>/css/admin.less'
                    ]
				}
			}
		},
        

        // require
        /*requirejs: {
            dist: {
                // Options: https://github.com/jrburke/r.js/blob/master/build/example.build.js
                options: {
                    // `name` and `out` is set by grunt-usemin
					out: '<%= yeoman.app %>/js/quiz/bundle.js',
					name: 'init',
                    baseUrl: '<%= yeoman.app %>/js/quiz',
					mainConfigFile: "<%= yeoman.app %>/js/quiz/init.js",
                    optimize: 'none',
                    paths: {
                        'jquery': 'empty:'
                    },
                    // TODO: Figure out how to make sourcemaps work with grunt-usemin
                    // https://github.com/yeoman/grunt-usemin/issues/30
                    //generateSourceMaps: true,
                    // required to support SourceMaps
                    // http://requirejs.org/docs/errors.html#sourcemapcomments
                    preserveLicenseComments: false,
                    useStrict: true,
                    wrap: true,
                    //uglify2: {} // https://github.com/mishoo/UglifyJS2
                    pragmasOnSave: {
                        //removes Handlebars.Parser code (used to compile template strings) set
                        //it to `false` if you need to parse template strings even after build
                        excludeHbsParser : true,
                        // kills the entire plugin set once it's built.
                        excludeHbs: true,
                        // removes i18n precompiler, handlebars and json2
                        excludeAfterBuild: true
                    }
                }
            },
            adminDist: {
                // Options: https://github.com/jrburke/r.js/blob/master/build/example.build.js
                options: {
                    // `name` and `out` is set by grunt-usemin
                    out: '<%= yeoman.app %>/js/admin/createQuiz/bundle.js',
                    name: 'init',
                    baseUrl: '<%= yeoman.app %>/js/admin/createQuiz',
                    mainConfigFile: "<%= yeoman.app %>/js/admin/createQuiz/init.js",
                    optimize: 'none',
                    paths: {
                        'jquery': 'empty:'
                    },
                    // TODO: Figure out how to make sourcemaps work with grunt-usemin
                    // https://github.com/yeoman/grunt-usemin/issues/30
                    //generateSourceMaps: true,
                    // required to support SourceMaps
                    // http://requirejs.org/docs/errors.html#sourcemapcomments
                    preserveLicenseComments: false,
                    useStrict: true,
                    wrap: true,
                    //uglify2: {} // https://github.com/mishoo/UglifyJS2
                    pragmasOnSave: {
                        //removes Handlebars.Parser code (used to compile template strings) set
                        //it to `false` if you need to parse template strings even after build
                        excludeHbsParser : true,
                        // kills the entire plugin set once it's built.
                        excludeHbs: true,
                        // removes i18n precompiler, handlebars and json2
                        excludeAfterBuild: true
                    }
                }
            }
        },*/

        cssmin: {
            dist: {
                files: {
                    '<%= yeoman.app %>/css/main.min.css': [
                        '<%= yeoman.app %>/css/main.css'
                    ],
					'<%= yeoman.app %>/css/admin.min.css': [
                        '<%= yeoman.app %>/css/admin.css'
                    ],
					'<%= yeoman.app %>/themes/flat/css/style.min.css': [
                        '<%= yeoman.app %>/themes/flat/css/style.css'
                    ]
                }
            }
        },
		uglify: {
			options: {
			  mangle: false
			},
			my_target: {
			  files: {
				/*'<%= yeoman.app %>/js/quiz/bundle.min.js': ['<%= yeoman.app %>/js/quiz/bundle.js'],
                  '<%= yeoman.app %>/js/admin/createQuiz/bundle.min.js': ['<%= yeoman.app %>/js/admin/createQuiz/bundle.js']*/
			  }
			}
		},

        bower: {
            all: {
                rjsConfig: '<%= yeoman.app %>/js/init.js'
            }
        },

        // handlebars
        handlebars: {
            compile: {
                options: {
                    namespace: 'JST',
                    amd: true
                },
                files: {
                    '.tmp/scripts/templates.js': ['templates/**/*.hbs']
                }
            }
        },
        copy:{
            packagePublic: {
                cwd: 'public/',//change current working directory
                src: ['**', '!bower_components/**/*', '!media/uploads/**/*'],//Exclude contents of media folder
                dest: packageFolder + '/',
                expand: true,
                dot: true//Copy Hidden files too like .htaccess
            },
            packageApp: {
                src: ['**', '!**/.tmp/**', '!**/node_modules/**', '!**/public/**', '!**/.idea/**', '!**/.git/**', '!**/.gitignore/**', '!**/.gitattributes/**', '!**/package/**', '!**/protected/**', '!**/package-override-files/**', '!**/' + packageFileName, '!**/' + updatePackagesFolder + '/**', '!**/app/config/database.php'],
                dest: packageFolder + '/protected/',
                expand: true,
                dot: true//Copy Hidden files too like .htaccess
            },
            packageOverride: {
                cwd: 'package-override-files/',//change current working directory
                src: ['**'],
                dest: packageFolder + '/',
                expand: true,
                dot: true//Copy Hidden files too like .htaccess
            },
            bowerFiles: {
                cwd: 'public/bower_components/',//change current working directory
                src: ['**/*.js', '**/*.css', '**/*.css', '**/*.eot', '**/*.svg', '**/*.ttf', '**/*.woff', '**/*.woff2', '**/*.otf'],
                dest: packageFolder + '/bower_components/',
                expand: true,
                dot: true//Copy Hidden files too like .htaccess
            },
            updateFiles: {
                cwd: packageFolder + '/',//change current working directory
                src: [],
                dest: updatePackagesFolder,
                expand: true,
                dot: true//Copy Hidden files too like .htaccess
            }
        },
        // make a zipfile
        compress: {
            package: {
                options: {
                    archive: packageFileName
                },
                files: [
                    {
                        cwd: packageFolder + '/',
                        src: ['**'],
                        dest: '',
                        expand: true,
                        dot: true//Copy Hidden files too like .htaccess
                    }, // includes files in path
                ]
            },
            updatePackage: {
                options: {
                    archive: 'update.zip'//Will be set by calling function
                },
                files: [
                    {
                        cwd: '',//Will be set by calling function
                        src: ['**'],
                        dest: '',
                        expand: true,
                        dot: true//Copy Hidden files too like .htaccess
                    }, // includes files in path
                ]
            }
        },
        exec: {
            versionDiffFiles: {
                cmd: function(from, to) {
                    return "git diff --name-only " + from + " " + to;
                },
                stdout: true,
                callback: function(err,stdout) {
                    var changedFiles = stdout.split("\n");
                    packageChangedFiles(changedFiles);
                }

            }
        }
    });


    grunt.registerTask('createDefaultTemplate', function () {
        grunt.file.write('.tmp/scripts/templates.js', 'this.JST = this.JST || {};');
    });

    
    grunt.registerTask('default', function (target) {

        grunt.option('force', true);

        grunt.task.run([
            'less',
            'watch'
        ]);
    });

	
		
    /*// todo fix these
    grunt.registerTask('test', [
        'clean:server',
        'createDefaultTemplate',
        'handlebars',
        'compass',
        'connect:testserver',
        'jasmine'
    ]);*/

    grunt.loadNpmTasks('grunt-contrib-less');
	//Load jasmine task
    /*grunt.loadNpmTasks('grunt-contrib-jasmine');
    
    grunt.loadNpmTasks('grunt-jasmine-node');
	grunt.loadNpmTasks('grunt-contrib-handlebars');*/
	
    //Create an alias task named 'test' for ease of use
    grunt.registerTask('test', ['jasmine']);

    grunt.registerTask('packageBuilt', function(){
        //Delete old folder
        grunt.file.delete(packageFolder);

        //create new folder
        grunt.file.mkdir(packageFolder);

        //Copy app files (base directory)
        grunt.task.run('copy:packageApp');

        //Copy public files to base folder
        grunt.task.run('copy:packagePublic');

        //Copy bower files(the main files only) to base folder
        grunt.task.run('copy:bowerFiles');

        //Copy files to override
        grunt.task.run('copy:packageOverride');

        //Compress
        grunt.task.run('compress:package');

    });

    //Make the app package to be uploaded to any server to be run without installing dependencies and building
    grunt.registerTask('package', ['build','packageBuilt']);

    function getPathInPackage(file) {
        if(file.match(/public\/.*/)) {
            return file.replace(/public\//, '');
        }
        return 'protected/' + file;
    }

    grunt.registerTask('generateUpdateFiles', function(){
        var fromVersion = grunt.option('from');
        var toVersion = grunt.option('to');
        if(!fromVersion || !toVersion) {
            grunt.log.error("From and to versions must be specified as '--from=**' and '--to=**'");
        }
        global['packageChangedFiles'] = function (changedFiles) {
            console.log("Running 'packageChangedFiles'");
            var includeFiles = [];
            changedFiles.forEach(function(file) {
                if(file.length)
                    includeFiles.push(getPathInPackage(file));
            });
            var updateTargetFolder = updatePackagesFolder + '/' + toVersion;
            includeFiles = includeFiles.concat(['!protected/app/config/database.php', '!protected/app/config/admin.php', 'protected/vendor/**/*', 'bower_components/**/*']);
            grunt.config.set('copy.updateFiles.src', includeFiles);
            grunt.config.set('copy.updateFiles.dest', updateTargetFolder);
            //Include bower components too
            //includeFiles.push(getPathInPackage('public/bower_components/'));

            /*includeFiles.forEach(function(file) {
                console.log(file);
            });*/
            grunt.config.set('compress.updatePackage.options.archive', updateTargetFolder + '.zip');
            var compressUpdatePackagesConfig = grunt.config.get('compress.updatePackage.files');
            compressUpdatePackagesConfig[0].cwd = updateTargetFolder;
            grunt.config.set('compress.updatePackage.files', compressUpdatePackagesConfig);
            //console.log(grunt.config.get('compress.updatePackage'));
            grunt.task.run('copy:updateFiles');
            grunt.task.run('compress:updatePackage');
        }

        var changedFiles = grunt.task.run('exec:versionDiffFiles:' + fromVersion + ':' + toVersion);
        //grunt.log.write(JSON.stringify(changedFiles));
    });

    grunt.registerTask('generateItemFiles', function() {
        var itemFilesGenerator = require('./itemFilesGenerator')(grunt);
        itemFilesGenerator.run();
    })

    grunt.registerTask('makeUpdate', [
        /*'package',*/
        'generateUpdateFiles'
    ]);

    grunt.registerTask('build', [
        'createDefaultTemplate',
        'handlebars',
        //'requirejs',
        'cssmin',
        'uglify'
    ]);

};

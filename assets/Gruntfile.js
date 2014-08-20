module.exports = function(grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        banner: '/*! <%= pkg.name %> <%= grunt.template.today("dd-mm-yyyy") %> */\n',

        js_src_path: 'javascripts',
        js_dest_path: 'dist/js',
        css_dest_path: 'dist/css',
        img_dest_path: 'dist/images',
        img_gen_path: 'dist/images/gen',
        sass_path: 'sass',
        font_path: 'dist/fonts',
        sprites_path: 'sprites',

        concat: {
            options: {
                separator: ';'
            },
            js: {
                src: [
                    'bower_components/jquery/dist/jquery.js',
                    'bower_components/bootstrap-sass-official/assets/javascripts/bootstrap.js',
                    'bower_components/datatables/media/js/jquery.dataTables.js',
                    '<%= js_src_path %>/*.js'
                ],
                dest: '<%= js_dest_path %>/<%= pkg.name %>.js'
            }
        },
        uglify: {
            options: {
                banner: '<%= banner %>'
            },
            main: {
                src: ['<%= concat.js.dest %>'],
                dest: '<%= js_dest_path %>/<%= pkg.name %>.min.js'
            }
        },
        clean: {
            js : {
                src: [ '<%= js_dest_path %>/**/*.*', '!<%= js_dest_path %>/**/*.min.js' ]
            },
            css : {
                src: [ '<%= css_dest_path %>/**/*.*', '!<%= css_dest_path %>/**/*.min.css' ]
            },
            img : {
                src: [ '<%= img_dest_path %>/**/*.*', '!<%= img_dest_path %>/**/*.{png,jpg,gif,jpeg,svg}', '!<%= img_gen_path %>' ]
            },
            fonts: {
                src: [ '<%= font_path %>/**/*.*', '!<%= font_path %>/**/*.{eot,svg,ttf,woff,otf}' ]
            }
        },
        compass: {
            dist: {
                options: {
                    config: 'config.rb'
                }
            }
        },
        cssmin: {
            files: {
                '<%= css_dest_path %>/raum42-admin.css': ['<%= css_dest_path %>/style.css']
            },
            minify: {
                src: '<%= css_dest_path %>/raum42-admin.css',
                dest: '<%= css_dest_path %>/raum42-admin.min.css'
            }
        },
        copy: {
            bootstrap: {
                files: [
                    {
                        expand: true,
                        flatten: true,
                        src: ['bower_components/bootstrap-sass-official/assets/fonts/bootstrap/*'],
                        dest: 'dist/fonts/',
                        filter: 'isFile'
                    }
                ]
            },
            fontawesome: {
                files: [
                    {
                        expand: true,
                        flatten: true,
                        src: ['bower_components/font-awesome/fonts/*'],
                        dest: 'dist/fonts/',
                        filter: 'isFile'
                    }
                ]
            }
        },
        watch: {
            js: {
                files: ['<%= js_src_path %>/**/*.js'],
                tasks: ['concat:js', 'uglify', 'clean:js']
            },
            sass: {
                files: ['<%= sass_path %>/**/*.scss', '<%= sprites_path %>',  '<%= img_dest_path %>/**/*.{png,jpg,gif,jpeg,svg}', '!<%= img_gen_path %>/**'],
                tasks: ['compass', 'cssmin', 'clean:css', 'clean:img']
            }
        }
    });

    grunt.registerTask('default', ['copy', 'compass', 'concat', 'uglify', 'cssmin', 'clean']);

    require('load-grunt-tasks')(grunt);
};
module.exports = function (grunt) {

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        uglify: {
            wiki: {
                files: {
                    'resources/js/humhub.wiki.bundle.min.js': ['resources/js/humhub.wiki.bundle.js'],
                }
            }
        },
        cssmin: {
            target: {
                files: {
                    'resources/css/humhub.wiki.min.css': ['resources/css/humhub.wiki.css']
                }
            }
        },
        concat: {
            wiki: {
                src:[
                    'resources/js/humhub.wiki.js',
                    'resources/js/humhub.wiki.Page.js',
                    'resources/js/humhub.wiki.Form.js',
                    'resources/js/humhub.wiki.CategoryListView.js',
                    'resources/js/humhub.wiki.linkExtension.js',
                    'resources/js/humhub.wiki.History.js',
                    'resources/js/humhub.wiki.Sidebar.js',
                    'resources/js/htmldiff.js',
                ],
                dest: 'resources/js/humhub.wiki.bundle.js'
            },
        },
        less: {
            dev: {
                files: {
                    'resources/css/humhub.wiki.css': 'resources/css/humhub.wiki.less'
                }
            }
        },
        watch: {
            scripts: {
                files: ['resources/js/*.js', 'resources/css/*.less'],
                tasks: ['build'],
                options: {
                    spawn: false,
                },
            },
        },
    });

    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-watch');

    grunt.registerTask('build', ['concat', 'uglify', 'less', 'cssmin']);
};

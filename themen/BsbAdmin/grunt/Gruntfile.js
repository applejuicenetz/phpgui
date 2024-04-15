module.exports = function (grunt) {
    grunt.initConfig({
        watch: {
            sass: {
                files: 'assets/scss/**/*.scss',
                tasks: 'sass'
            }
        },
        sass: {
            dist: {
                options: {
                    sourcemap: 'none'
                },
                files: [
                {
                    expand: true,
                    cwd: 'assets/scss/',
                    src: ['**/*.scss'],
                    dest: 'assets/css/',
                    ext: '.css'
                }]
            }
        },
        cssmin: {
            minify: {
                expand: true,
                cwd: 'assets/css/',
                src: ['**/*.css', '!**/*.min.css'],
                dest: 'assets/css/',
                ext: '.min.css'
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-contrib-cssmin');

    grunt.registerTask('build', ['sass', 'cssmin']);
};
'use strict';

module.exports = function(grunt) {
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    sass: {
      dist: {
        files: {
          'lib/css/style.css' : 'lib/scss/style.scss',
          'lib/css/styledown.css' : 'lib/scss/styledown.scss'
        }
      }
    },
    autoprefixer: {
      options: {
        browsers: ['last 2 versions', 'ie 8', 'ie 9']
      },
      target: {
        src: 'lib/css/style.css'
      },
    },
    csscomb: {
      options: {
        config: '.csscomb.json'
      },
      css: {
        files: {
          'lib/css/style.css': 'lib/css/style.css'
        }
      }
    },
    watch: {
      css: {
        files: '**/*.scss',
        tasks: ['sass', 'autoprefixer', 'csscomb']
      }
    }
  });

  grunt.loadNpmTasks('grunt-contrib-sass');
  grunt.loadNpmTasks('grunt-autoprefixer');
  grunt.loadNpmTasks('grunt-csscomb');
  grunt.loadNpmTasks('grunt-contrib-watch');

  grunt.registerTask('default',['watch']);
  grunt.registerTask('compile',['sass', 'autoprefixer', 'csscomb']);

};
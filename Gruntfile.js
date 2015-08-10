module.exports = function(grunt) {
  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),

    watch: {
      scripts: {
        files: ['js/*.js', '!js/main.js', '!js/main.min.js'],
        tasks: ['recompile'],
        options: {
          interrupt: true
        }
      },
      css: {
        files: ['css/*.css', 'css/*/*.css', '!css/main.css', '!css/main.min.css'],
        tasks: ['cssmin'],
        options: {
          interrupt: true
        }
      },
      sass: {
        files: ['css/sass/*.scss'],
        tasks: ['sass', 'cssmin'],
        options: {
          interrupt: false
        }
      }
    },
    concat: {
      options: {
        separator: ';'
      },
      dist: {
        src: ['js/jquery-1.11.1.min.js','js/jquery-ui.min.js','js/bootstrap/bootstrap.min.js','js/bootstrap/bootstrap3-typeahead-min.js','js/custom.js'],
        dest: 'js/main.js'
      }
    },
    uglify: {
      my_target: {
        files: {
          'js/main.min.js': ['js/main.js']
        }
      }
    },
    cssmin: {
      combine: {
        files: {
          'css/main.css': ['css/jquery/jquery.css','css/bootstrap/bootstrap.min.css','css/bootstrap/bootstrap-theme.min.css','css/bootstrap/bootstrap-switch.css','css/sass/main_sass.css']
        }
      },
      my_target: {
        files: {
          'css/main.min.css': ['css/main.css']
        }
      }
    },
    sass: {                              // Task
      dist: {                            // Target
        options: {                       // Target options
          style: 'expanded'
        },
        files: {                         // Dictionary of files
          'css/sass/main_sass.css': 'css/sass/main_sass.scss'       // 'destination': 'source'
        }
      }
    }
  });

  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-cssmin');
  grunt.loadNpmTasks('grunt-contrib-sass');

  grunt.registerTask('recompile', ['concat', 'uglify']);

  // Default task(s).
  grunt.registerTask('default', ['watch']);

};


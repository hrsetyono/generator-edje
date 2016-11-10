"use strict";

module.exports = function(grunt) {

  grunt.initConfig({
    pkg: grunt.file.readJSON("package.json"),

    path: {
      assets: "assets",
      css: [ "<%= path.assets %>/css" ],
      sass: [ "<%= path.assets %>/sass" ],
      js: [ "<%= path.assets %>/js" ]
    },

    sass: {
      options: {
        includePaths: require("edje").includePaths()
      },
      dev: {
        options: {
          outputStyle: "compact",
          sourceMap: false
        },
        files: [{
          expand: true,
          cwd: "<%= path.sass %>/",
          src: ["**/*.scss"],
          dest: "<%= path.css %>/",
          ext: ".css"
        }]
      },
    },

    watch: {
      sass: {
        files: [
          "<%= path.sass %>/**/*.{scss,sass}"
        ],
        tasks: ["sass:dev"]
      }
    },

    // email builder
    juice: {
      build: {
        files: {
          "dist.html" : "dev.html",
          // "dist2.html" : "dev2.html"
        }
      },
      options: {
        preserveMediaQueries: true,
        applyAttributesTableElements: true,
        applyWidthAttributes: true,
        styleToAttribute: ["cellpadding", "cellspacing"],
        tableElements: "table"
      },
    },

    // Guide: https://github.com/dwightjack/grunt-nodemailer
    nodemailer: {
      send: {
        src: ["dist.html"]
      },
      options: {
        transport: {
          type: "SMTP",
          options: {
            service: "Gmail",
            auth: {
              user: "your.fake.address@gmail.com",
              pass: "yourpassword",
            }
          }
        },
        message: {
          subject: "<%= pkg.name %> - Test email",
          from: "Edjemail"
        },
        recipients: [{
          email: "your.email@gmail.com",
          name: "yourpassword"
        }]
      },
    },
  });

  // `grunt` - compile sass
  grunt.loadNpmTasks("grunt-sass");
  grunt.loadNpmTasks("grunt-contrib-watch");
  grunt.registerTask("default", ["watch"]);

  // `grunt build` - inlining css
  grunt.loadNpmTasks("grunt-juice-email");
  grunt.registerTask("build", ["juice:build"]);

  // `grunt send` - send test email
  grunt.loadNpmTasks("grunt-nodemailer");
  grunt.registerTask("send", function() {
    grunt.task.run([
      "juice:build",
      "nodemailer:send"
    ]);
  });
};

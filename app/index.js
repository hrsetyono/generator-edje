(function() {
  'use strict';

  var generators = require('yeoman-generator');
  var path = require('path');
  var unzip = require('gulp-unzip');
  // var remote = require('yeoman-remote');

  module.exports = generators.Base.extend({
    constructor: function () {
      generators.Base.apply(this, arguments); // super()

      this.argument('template', { type: String, required: false });
      this.template = this.template;
    },

    /*
      Prompt question to get missing template and project's name
    */
    prompting: function() {
      var self = this;
      var availableTpl = [
        { name: 'HTML', value: 'html' },
        { name: 'WordPress', value: 'wordpress' },
        { name: 'WooCommerce', value: 'woocommerce' },
      ];

      var askTpl = {
        type: 'list',
        name: 'template',
        message: 'Choose your project type:',
        choices: availableTpl,
        when: isArgInvalid
      };

      // prompt the question
      return self.prompt(askTpl).then(afterAsk);

      /////

      // check if CLI arg is invalid
      function isArgInvalid(answer) {
        // if template found, don't prompt question
        for(var i in availableTpl) {
          if(availableTpl[i].value === self.template) {
            return false;
          }
        }

        return true;
      }

      // assign answer to the class value
      function afterAsk(answer) {
        self.template = answer.template || self.template;
      }
    },

    /*
      Generate files
    */
    writing: function() {
      var self = this;
      // this.log(self.sourceRoot('new/path') );
      var destinationName = self.destinationRoot().split(path.sep).pop();

      switch(self.template) {
        case 'wordpress':
        case 'woocommerce':
          copy('wordpress-src-test');
          self.registerTransformStream(unzip() );
          break;

        default:
          copy('base');
          copy(self.template);
      }

      function copy(source, destination) {
        source = self.templatePath(source);
        destination = destination || self.destinationRoot();

        self.fs.copy(source, destination);
      }
    },
  });
})();

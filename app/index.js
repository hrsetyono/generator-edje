(function() {
  'use strict';

  var generators = require('yeoman-generator');
  var path = require('path');
  var fs = require('fs');
  var adm_zip = require('adm-zip');

  module.exports = generators.Base.extend({
    constructor: function () {
      generators.Base.apply(this, arguments); // super()

      this.argument('template', { type: String, required: false });
      this.template = this.template;
      this.appname = this.appname.toLowerCase().replace(' ', '-');
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
        { name: 'Email', value: 'email' }
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
          self.log('Downloading WordPress...');

          // copy installation file
          self._copy('wordpress-src/wordpress.zip', 'wordpress.zip');
          self.fs.commit([], afterWpCopy.bind(self) );
          break;

        case 'email':
          self._copy(self.template);
          break;

        default:
          self._copy('base');
          self._copy(self.template);
      }

      function afterWpCopy() {
        self._unzip('wordpress.zip'); // unzip install file

        var dest = 'wp-content/themes/' + self.appname;

        // setup theme and plugins
        self._copy('wordpress', dest);
        var plugins = ['edje-wp', 'timber-library'];

        // if woocommerce, add extra
        if(self.template === 'woocommerce') {
          self._copy('woocommerce', dest);
          plugins = plugins.concat(['edje-woo', 'woocommerce']);
        }

        self._addPlugins(plugins); // plugins
      }
    },

    /*
      Copy from a source to destination

      @param source (str) - Relative path to a source dir
      @param dest (str) - Relative path to a destination dir
    */
    _copy: function(source, dest) {
      var self = this;

      source = self.templatePath(source);
      dest = dest ? self.destinationPath(dest) : self.destinationRoot();

      self.fs.copy(source, dest);
    },

    /*
      Unzip specified file

      @param filePath (str) - Relative path to the zip file.
      @param dest (str) - optional, Relative path to the extract destination.
    */
    _unzip: function(filePath, dest) {
      var self = this;

      var file = self.destinationPath(filePath);
      var zip = new adm_zip(file);
      dest = dest ? self.destinationPath(dest) : self.destinationRoot();

      zip.extractAllTo(dest, true);
      fs.unlink(file, function() { } );
    },

    /*
      Add plugins to WP directory

      @param names (mix) - String or array of plugin names.
    */
    _addPlugins: function(names) {
      var self = this;
      var dest = 'wp-content/plugins/';

      // if not array, wrap it in array format
      names = (Object.prototype.toString.call(names) === '[object Array]') ? names : [names];

      for(var n in names) {
        var name = names[n] + '.zip';
        self._copy('wordpress-plugins/' + name, dest + name);
      }

      self.fs.commit([], afterCopy.bind(self) );

      function afterCopy() {
        self.log('Extracting plugins...');

        for(var n in names) {
          var source = dest + names[n] + '.zip';
          self._unzip(source, dest);
        }
      }
    }


  });
})();

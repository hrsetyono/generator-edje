(function() {
  'use strict';

  var generators = require( 'yeoman-generator' );
  var path = require( 'path' );
  var fs = require( 'fs' );
  var adm_zip = require( 'adm-zip' );

  var templateChoices = [
    { name: 'HTML', value: 'html', },
    { name: 'WordPress', value: 'wordpress', },
    { name: 'WooCommerce', value: 'woocommerce', },
    { name: 'Email', value: 'email' }
  ];

  module.exports = generators.Base.extend({

    constructor: function () {
      generators.Base.apply( this, arguments ); // super()

      this.argument( 'template', { type: String, required: false } );
      this.template = this.template;
      this.appname = this.appname.toLowerCase().replace( ' ', '-' );
    },


    paths: function() {
      this.sourceRoot( path.join(__dirname, '../templates') );
    },


    /*
      Prompt question to get missing template and project's name
    */
    prompting: function() {
      var self = this;

      var promptArgs = {
        type: 'list',
        name: 'template',
        message: 'Choose your project type:',
        choices: templateChoices,
        when: _isAnswerInvalid
      };

      // prompt the question
      return self.prompt( promptArgs ).then( _afterAsk );

      /////

      // Ask question again if answer not one of available templates
      function _isAnswerInvalid( answer ) {
        for( var i in templateChoices ) {
          if( templateChoices[i].value === answer.template ) {
            return false;
          }
        }

        return true;
      }

      // Assign answer to the class value
      function _afterAsk( answer ) {
        self.template = answer.template || self.template;
      }

    },

    /*
      Generate files
    */
    writing: function() {
      var self = this;
      var destinationName = self.destinationRoot().split( path.sep ).pop();

      var themeDest = 'wp-content/themes/' + self.appname;
      var pluginDest = 'wp-content/plugins/';

      var plugins = ['edje-wp', 'timber-library'];
      if( self.template === 'woocommerce' ) {
        plugins.concat( ['woocommerce-edje', 'woocommerce'] )
      }

      switch( self.template ) {
        case 'wordpress':
        case 'woocommerce':
          self.log( 'Downloading WordPress...' );

          // copy installation file
          self._copy( 'wordpress-src/wordpress.zip', 'wordpress.zip' );
          self.fs.commit( [], copyTheme );
          break;

        case 'email':
          self._copy( self.template );
          break;

        default:
          self._copy( 'base' );
          self._copy( self.template );
      }


      // Copy theme files after installing WP
      function copyTheme() {
        self._unzip( 'wordpress.zip' );

        // setup theme and plugins
        self._copy( 'base', themeDest );
        self._copy( 'wp-theme-base', themeDest );
        self._copy( 'wp-theme', themeDest );

        // if woocommerce, add extra
        if( self.template === 'woocommerce' ) {
          self._copy( 'wc-theme', themeDest );

        }

        copyPlugins();
      }

      // Copy plugin files
      function copyPlugins() {
        for( var i in plugins ) {
          var p = plugins[i] + '.zip';
          self._copy( 'wordpress-plugins/' + p, pluginDest + p);
        }

        self.fs.commit([], extractPlugins );
      }

      // Extract plugin files
      function extractPlugins() {
        self.log('Extracting plugins...');

        for( var i in plugins ) {
          var source = pluginDest + plugins[i] + '.zip';
          self._unzip( source, pluginDest );
        }
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


  });
})();

(function() {
  'use strict';

  var generators = require( 'yeoman-generator' );
  var path = require( 'path' );
  var fs = require( 'fs' );
  var adm_zip = require( 'adm-zip' );

  var promptChoices = [
    { name: 'HTML', value: 'html', },
    { name: 'WordPress', value: 'wordpress', },
    { name: 'WooCommerce', value: 'woocommerce', },
    // { name: 'PWA with React', value: 'pwa-react' },
    // { name: 'PWA with Handlebars', value: 'pwa-handlebars' },
    { name: 'Email', value: 'email' }
  ];

  var REQ_PLUGINS = {
    wordpress: ['wp-edje', 'timber-library-180'],
    woocommerce: ['wp-edje', 'timber-library-180', 'woocommerce', 'woocommerce-edje'],
  };

  module.exports = generators.Base.extend({

    constructor: function () {
      generators.Base.apply( this, arguments );

      this.argument( 'template', { type: String, required: false } );
      this.template = this.template;
      this.appname = this.appname.toLowerCase().replace( ' ', '-' );
    },

    /*
      Define templates location
    */
    paths: function() {
      this.sourceRoot( path.join( __dirname, '../templates' ) );
    },


    /*
      Prompt question to get missing template and project's name
    */
    prompting: function() {
      var self = this;

      var promptArgs = {
        type: 'list',
        name: 'template',
        message: 'Choose your project template:',
        choices: promptChoices,
        when: _isAnswerInvalid
      };

      // prompt the question
      return self.prompt( promptArgs ).then( _afterAsk );


      /////


      // Ask question again if answer not one of available templates
      function _isAnswerInvalid( answer ) {
        for( var key in promptChoices ) {
          if( promptChoices[ key ].value === answer.template ) {
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

      switch( self.template ) {
        case 'wordpress':
        case 'woocommerce':
          self.log( 'Downloading WordPress...' );

          // copy installation file
          self._copy( '../zip/wordpress-install.zip', 'wordpress.zip' );
          self.fs.commit( [], copyTheme );
          break;

        case 'email':
          self._copy( self.template );
          break;

        default:
          self._copy( 'base' );
          self._copy( self.template );
      }


      /////


      // Copy theme files after installing WP
      function copyTheme() {
        self._unzip( 'wordpress.zip' );

        // setup theme and plugins
        self._copy( 'base', themeDest );
        self.template === 'woocommerce' ?
          self._copy( 'wc-theme', themeDest ) :
          self._copy( 'wp-theme', themeDest );

        self._copy( 'wp-theme-base', themeDest );

        self._copyPlugins( REQ_PLUGINS[ self.template ] );
      }

    },

    /*
      Copy from a source to destination

      @param source (str) - Relative path to a source dir
      @param dest (str) - Relative path to a destination dir
    */
    _copy: function( source, dest ) {
      var self = this;

      source = self.templatePath( source );
      dest = dest ? self.destinationPath( dest ) : self.destinationRoot();

      self.fs.copy( source, dest );
    },

    /*

    */
    _copyPlugins: function( plugins ) {
      var self = this;
      var sourcePath = self.templatePath( '../zip/' );
      var destPath = self.destinationPath( 'wp-content/plugins/' );

      plugins.forEach( function( p ) {
        p += '.zip';
        self._copy( sourcePath + p, destPath + p );
      } );

      self.fs.commit( [], extractPlugins );

      /////

      // Extract plugin files
      function extractPlugins() {
        self.log( 'Extracting plugins...' );

        plugins.forEach( function( p ) {
          self._unzip( destPath + p + '.zip', destPath );
        });
      }
    },

    /*
      Unzip specified file

      @param filePath (str) - Relative path to the zip file.
      @param dest (str) - optional, Relative path to the extract destination.
    */
    _unzip: function( filePath, dest ) {
      var self = this;

      var file = self.destinationPath( filePath );
      var zip = new adm_zip( file );
      dest = dest ? self.destinationPath( dest ) : self.destinationRoot();

      zip.extractAllTo( dest, true );
      fs.unlink( file, function() { } );
    },


  });
})();

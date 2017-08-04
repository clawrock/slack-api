'use strict';
module.exports = function (grunt) {

  require('time-grunt')(grunt);

  require('load-grunt-tasks')(grunt, {
    scope: 'devDependencies',
    config: 'package.json',
    pattern: ['grunt-*']
  });

  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    shell: {
      installComposer: {
        command: 'curl -sS https://getcomposer.org/installer | php',
        options: {
          stdout: true
        }
      },
      phpdoc: {
        command: 'vendor/bin/phpdoc.php -d src/ -t doc',
        options: {
          stdout: true
        }
      },
      phpcpd: {
        command: 'vendor/bin/phpcpd src/',
        options: {
          stdout: true
        }
      }
    },
    phpunit: {
      classes: {
        dir: 'tests/'
      },
      options: {
        bin: 'php vendor/bin/phpunit',
        //bootstrap: 'tests/php/phpunit.php',
        colors: true,
        configuration: 'phpunit.xml'
      }
    },
    version: {
      php: {
        options: {
          prefix: '@version\\s*'
        },
        src: ['src/**/*.php']
      },
      json: {
        options: {
          prefix: '"version":\\s"*'
        },
        src: ['composer.json']
      }
    }
  });

  // Register tasks
  grunt.registerTask('init', [
    'shell:installComposer'
  ]);

  grunt.registerTask('build', [
    'version:php',
    'version:json',
    'shell:phpdoc',
    'shell:phpcpd',
    'phpunit'

  ]);

  grunt.registerTask('serve', [
    'build'
  ]);
};

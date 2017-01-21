# My Project

Write your project description here.

**Development Specs**:
- PHP 5.6
- WordPress 4.7.1
- Timber 1.2
- Edje 1.2

## Compiling Sass

1. Install [NodeJS v5.4](https://nodejs.org/en/blog/release/v5.4.1/)
1. Install Node-Sass and Edje globally with `npm install -g node-sass@3.4.2` and `npm install -g edje`.
1. Restart your PC.
1. Open a command prompt in your project directory and type `npm run sass` (PC) or `npm run sass-mac` (Mac).

## Using Remote Debug

First, your project needs to be in virtual host. [Read more](https://github.com/hrsetyono/generator-edje/wiki/My-Workflow#localhost-setup).

1. Install Browser-sync with `npm install -g browser-sync`.
1. Open "package.json" and edit `remote-debug` script with your Virtual Host name.
1. Run it with `npm run remote-debug`. IP address will be shown for you to type in your phone / tablet.
1. Now your action is synchronized between device. Except refresh...

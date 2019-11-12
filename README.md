# TYPO3 extension "Frontend cookies"

This extension provides lots of possibilities to hide and show contents
based on the existence or values of cookies. On board is a frontend
plugin to display "cookie-banners" as well as a configurable API to
manage cookies for PHP, JavaScript and CSS.

## Features

1. Frontend plugin to display "cookie-banners". It shows a message as long
   as the message is not aknowledged (by clicking the accept-button).
2. An API for PHP and JavaScript to manage cookies. A "CSS-API" to show
   and hide contents by just using CSS classes (based on the existence
   or absence of a cookie).
3. A convenient backend module to give backend users the possiblity to
   manage the cookie-banner contents as well as some configuration
   options.
4. Lots of configuration possibilities.
5. Wish/need more? Get in touch!

## Documentation

Read the full documentation at https://docs.typo3.org/p/aaw-team/fe_cookies/master/en-us/

## Building CSS and JavaScript

All CSS and JavaScript files inside the *Resources/Public/* folder are generated with the [gulp] toolkit. All raw and uncompressed files are located inside *Resources/Private/Gulp/*. To edit or rebuild said files you have to install [npm].

To install all required packages for the build process run `npm install` inside the *Resources/Private/Gulp/* folder.

To build the files run one of the following commands.

```bash
npm run build # only builds the files once
npm run watch # builds and watches for file changes
```

After building the files are automatically moved to *Resources/Public/*.

#### Default Style
The default style is written in [Sass] with the SCSS syntax. The raw SCSS files are located inside the *Resources/Private/Gulp/src/sass/* folder. Most style relevant settings can be changed inside the *_settings.scss* file.

[gulp]: https://gulpjs.com/
[npm]: https://www.npmjs.com/
[Sass]: https://sass-lang.com/

## Contribuiting

You're welcome to add issues/comments to the bug tracker as well as
creating pull requests.

# TYPO3 extension "Frontend cookies"

This extension provides ....

## Features

* one
* two
* three

### CSS and JavaScript
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

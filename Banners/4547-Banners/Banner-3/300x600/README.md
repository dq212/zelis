# generator-craft-banner v3.0.2

> Yeoman generator used to scaffold production files for Craft/McCann banners.

## Contents
* [Before You Begin](#before-you-begin----requirements)
* [Developing Your Banner](#developing-your-banner)
  * [Using Gulp](#using-gulp)
  * [Using Sprites](#using-sprites)
  * [Versioning](#versioning)
* [Resizing a Banner](#resizing-a-banner)
* [Reporting Issues](#reporting-issues)

***


## Before You Begin -- Requirements

*n.b. -- many of the terminal commands listed here may need to be preceded by* `sudo` *in order to work. Try using* `sudo` *if you encounter errors.*

If you have gotten this far and generated a banner with the banner tool, it means that at a minimum you have Node.js, PNPM, and the Yeoman CLI (`yo`) installed on your machine. The workflow for developing a banner uses Compass and Gulp. If you already have these utilities installed, you can skip the remainder of this section.

First, verify [Ruby](https://rvm.io/) and [Compass](http://compass-style.org//) are installed:

```shell
gem -v
```

```shell
compass -v
```

If Compass is not installed, its gem can be installed with the command:

```shell
gem install compass
```

Install [gulp](http://gulpjs.com/) and [gulp CLI](https://github.com/gulpjs/gulp-cli) globally.

```shell
npm install -g gulp
npm install -g gulp-cli
```

Windows users may need to change their Git configuration file to allow for longer file names.

```shell
git config --system core.longpaths true
```


## Setting Up the Generator

Once the tools that the generator relies on have been installed, clone the generator to a local folder using Git and download all NPM dependencies:

```shell
git clone git@github.com:CraftNY/HTML5.git
npm install
```

Now, make the package available globally to NPM:

```shell
npm link
```


## Developing your Banner


### Using Gulp

Gulp tasks are used at the command line when locally developing a banner or compiling the banner for distribution. Further information can be found by exploring the banner project's `Gulpfile.js` file or by visiting the [gulp documentation](https://github.com/gulpjs/gulp/blob/master/docs/README.md)

#### *Default*
 
Run the gulp task `gulp` (or the default gulp task) when developing your banner. The banner will be served locally at port 8000 (usually http://localhost:8000). This task will watch for any changes in your code and will automatically recompile updated Sass to updated CSS. When you are done working on your banner, you can stop this task in the terminal using `ctrl-c`.

```shell
gulp
```

#### *Build*

`build` minifies and concatenates the `src` folder, compresses image assets, generates a build for deployment in the `dist` folder, and compresses to a zip file in the main project folder.

```shell
gulp build
```

#### *Sass*

Note that when the default task runs, the banner's Sass will only be compiled when the Sass file is saved. Sass compilation can be forced using the `sass` task.

```shell
gulp sass
```

#### *Tests*
There are 5 tests that are run on the dist folder whenever the gulp `build` task is run. 
	1) The zipfile should be under the k weight set by the developer
	2) The static image dimensions matches the dimensions of the creative
	3) All assets that are in the dist folder are referenced at least once
		-to see which assets are not referenced, go to the test in tests.js, and uncomment // console.log(assets[i]); in the function assetCheck(); 
	4) There are less than 15 assets total
	5) There should be no http calls, only https


### Using Sprites

Spritesheet compilation is included as a feature of Compass, the Ruby tool used to compile the banner project's Sass. To use sprites, place sprite image assets in `src/sprites/`. Uncomment line 11 of the `sass/styles.scss` file:

```scss
$sprites: sprite-map("sprites/*.png");
```

Now image assets in `src/sprites/` can be referenced in the Sass code. The spritesheet generated when the Sass is compiled to CSS will be placed in the `src/img/` folder. Please see the [Compass tutorial on spriting](http://compass-style.org/help/tutorials/spriting/) if any additional information is needed.


### Versioning

If developer answered "yes" to the generator prompt regarding if the banner was to be versioned, an `env.json` file will have been placed in the banner project's root directory when the banner was scaffolded. When `gulp build` is run, the task looks at the `env.json` file and reads the text values at the "VERSIONS" key. A separate distribution folder will be created for each of the text values. In addition, a corresponding class value will be appended to the `<body>` tag of each html file. Each banner can be targeted in the single Sass file using this selector.

To add additional versions, comma separate subsequent text values. For example:

```
{
    "VERSIONS" :  "offer1,offer2,offer3"
}
```


### Omitting images from compression

If you'd like to keep certain images in your banner from getting compressed during the `gulp build` process, run `gulp build` as follows:

```shell
gulp build --fullsize <image name>[,<additional image names>]
```

For example, if wanting to omit `bg1.jpg` from compression, you would run:

```shell
gulp build --fullsize bg1.jpg
```

Additional images to be omitted from compression should be comma separated (no spaces). If you wanted to omit `bg1.jpg`, `bg2.jpg`, and `bg8.jpg`, you would run:

```shell
gulp build --fullsize bg1.jpg,bg2.jpg,bg8.jpg
```


## Resizing a Banner

If you want to adapt a master banner to build a new size, first create a copy of the banner package and rename appropriately. If the `node_modules` have not yet been installed in the copied package, run:

```
npm install
```

Once the node packages are installed, run:

```
npm run resize {new banner width}x{new banner height} [{new banner expanded width}x{new banner expanded height} {new banner maximum k-weight}k]
```

For example, if your master was 300x250 and you want to resize to 160x600, you would run:

```
npm run resize 160x600
```

Passing an additional size is optional and should only be used in the case of an expandable banner. For example, if the master had a base size of 300x250 and an expanded size of 300x600, and the adapt has a base size of 728x90 and an expanded size of 728x400, you would run:

```
npm run resize 728x90 728x400
```

If the adapt has a different k-weight requirement, this value can also optionally be passed to the resize script. If the adapt banner is 160px wide and 600px tall and needs to be under 150k, you would run:

```
npm run resize 160x600 150k
```

The order of the new unexpanded and expanded widths is important (if new expanded widths are included, they must come *after* the unexpanded widths). However, the k-weight argument can be ordered anywhere in the list. For example, the following will work just the same as the the previous line:

```
npm run resize 150k 160x600
```


## Reporting Issues

If you encounter an unresolvable issue when using the generator or if you have specific feature requests, visit the [issue tracker](https://github.com/CraftNY/HTML5/issues). Please review the open issues to see if your issue or feature request has already been noted by someone else. If it hasn't, create a new issue by clicking the green "New issue" button at the top right of the page. **Submit one Github Issue per issue or feature request!** Provide a descriptive title and specific information pertaining to the issue or request in the provided comment field. New issues will be reviewed as soon as possible.

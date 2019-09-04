var fs = require('fs');
var imageSize = require('image-size');

describe('zipfile', function () {
	it('should be under 150kb', function () {

		var zip = fs.statSync('Zelis-1.zip');
		var fileSizeInKB = zip['size'] / 1000;
		expect(fileSizeInKB).not.toBeGreaterThan(150, '\nZip size is ' + fileSizeInKB + 'kb, it should be less than 150kb');
	});
});

describe('static image', function () {
	it('should match dimensions of the creative', function () {

		var dimensions = imageSize('src/img/static.jpg');

		expect(dimensions.width).toBe(300, '\nIncorrect width on the static image. It should be 300px wide');
		expect(dimensions.height).toBe(250, '\nIncorrect height on the static image. It should be 250px tall');
	});
});

describe('built package', function () {

	//html build and imgs 
	var html = fs.readFileSync('dist/index.html', 'utf8');
	//array of all the assets in the img folder
	var assets = (assetList('src/img/'));

	//counts the frequency of asset used in compiled html
	function count(str, subStr) {
		var matches = str.match(new RegExp(subStr, "g"));
		return matches ? matches.length : 0;
	}

	function assetList(dir, filelist) {
		var files = fs.readdirSync(dir)
		filelist = filelist || [];
		files.forEach(function (file) {
			if (fs.statSync(dir + file).isDirectory()) {
				filelist = assetCheck(dir + file + '/', filelist);
			} else {
				filelist.push(file);
			}
		});

		return filelist;
	};

	it('should reference all assets', function () {
		function assetCheck() {
			for (var i = 0; i < assets.length; i++) {
				if (count(html, assets[i]) < 1 && assets[i] !== '.DS_Store' && assets[i] !== 'static.jpg') {
					//notifies the user in the console which assets are not used 
					// console.log(assets[i]);
					return false;
				}
			}
			return true;
		}

		expect(assetCheck()).toBe(true, '\nAt least one image asset has been included in this banner without actually getting referenced in the code. Please remove it');
	});

	it('should have no more than 15 assets', function () {
		expect(assets.length).not.toBeGreaterThan(15, '\nThis banner has more than 15 image assets. Please consider using sprites or some other technique to limit image assets included');
	});

	it('should have no http calls', function () {
		expect(count(html, "http://")).toBe(0, '\nPlease remove any "http://" calls from the html file. Relative protocols should work, or use "https://"');
	});

});

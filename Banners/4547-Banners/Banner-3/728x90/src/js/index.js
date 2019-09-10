// var nameSpace = nameSpace || {};
var nameSpace = Zelis || {};

(function () {
	"use strict";

	var timeline;
	var wrapper, clickThrough, logo, copy, width, height, cta;

	nameSpace.init = function () {
		// Initialize any variables here
		wrapper = nameSpace.$('#wrapper');
		clickThrough = nameSpace.$('#click_through');
		width = 728;
		height = 90;
		// Add cta, passing in left position, top position, width, and height (all in px) 
		// as well as the selector for the parent element
		// cta = createCTA(80, 180, 140, 34, '#wrapper');

		wrapper.addClass('show');

		nameSpace.initAnimation();

		if (nameSpace.useFallback()) {
			nameSpace.injectFallback();
		}
		else {
			nameSpace.startAnimation();
		}

		click_through.onmouseover = function () {
			var hgth = "36";
			// TweenMax.to("#cta-top", 0.15, { rotationZ: 0.01, force3D: true, y: -(hgth), ease: Linear.easeNone });
			// TweenMax.to("#cta-bottom", 0.15, { rotationZ: 0.01, force3D: true, y: -(hgth), ease: Linear.easeNone });
			TweenMax.set('#cta-copy-1', { y: 0, x: 0 });
			TweenMax.set('#cta-copy-2', { y: hgth, x: 0 });
			TweenMax.to('#cta-copy-1', 0.15, { rotationZ: 0.01, force3D: true, y: -hgth, ease: Linear.easeNone });
			TweenMax.to('#cta-copy-2', 0.15, { z: 0.01, transformPerspective: 400, rotationZ: 0.01, force3D: true, y: -hgth, ease: Linear.easeNone });
		};

		// click_through.onmouseout = function () {
		// 	TweenMax.to("#cta", 0.2, { scale: 1, force3D: true, z: 0.01, rotationZ: 0.01, transformPerspective: 400, y: 0 });
		// };

		TweenMax.set(['#copy-1', '#copy-2'], { autoAlpha: 0 });
		TweenMax.set(['#copy-1', '#copy-2'], { y: height });
		TweenMax.set(['#hero'], { autoAlpha: 0 });
	};

	nameSpace.injectFallback = function () {
		var body = document.body;

		while (body.firstChild) {
			body.removeChild(body.firstChild);
		}

		var anchor = document.createElement('a');
		anchor.style.cursor = 'pointer';
		anchor.href = window.clickTAG;
		anchor.target = '_blank';

		var img = new Image();
		img.src = './img/static.jpg';

		anchor.appendChild(img);
		document.body.appendChild(anchor);

	};

	nameSpace.initAnimation = function () {
		// TweenMax can be used to set css
		// It will even take care of browser prefixes
		// TweenMax.set(logo, {x:100, y:50, opacity:0});

		timeline = new TimelineMax({
			delay: 0.2,
			onComplete: nameSpace.onAnimationComplete
		});

		timeline.pause();

		timeline
			.to('#sign', 0.5, { scale: 0.55, x: -150, y: 0 }, '+=2')
			.to('#copy-1', 0.8, { autoAlpha: 1, y: 0, z: 0.01, transformPerspective: 400, rotationZ: 0.01, force3D: true })
			.to(['#hero'], 0.5, { z: 0.01, transformPerspective: 400, rotationZ: 0.01, force3D: true, autoAlpha: 1 })


			.to('#copy-1', 0.5, { autoAlpha: 0, y: -height, z: 0.01, transformPerspective: 400, rotationZ: 0.01, force3D: true }, "+=2")
			.to(['#copy-2'], 0.5, { z: 0.01, transformPerspective: 400, rotationZ: 0.01, force3D: true, autoAlpha: 1, y: 0 })

	};

	nameSpace.startAnimation = function () {
		// Code for animation	

		nameSpace.alertAnimation();
		timeline.play();
	};

	nameSpace.onAnimationComplete = function () {
		// Log duration of timeline
		console.log('Animation Duration: ' + timeline.time() + 's');

		// Show a CTA or any animations outside main timeline
		// TweenMax.from("#cta", 0.4, { y: '110%' });
		// TweenMax.to("#cta", 0.4, { opacity: 1 });
	};

	nameSpace.alertAnimation = function () {


		var tl = new TimelineMax({ repeat: 5, yoyo: true })
			// .to(['#sign-bg', '#stem', '#dot'], 0.3, { fill: 0x088216 })
			.to("#sign", 0.3, { scale: 1.1, y: '2', transformOrigin: "10% 75%" }, "-=0.3")
	}




	// function createCTA(xp, yp, wdth, hgth, parent) {
	// 	var cta_div = document.createElement('div');
	// 	var top = document.createElement('div');
	// 	var btm = document.createElement('div');
	// 	var parentElm = document.querySelector(parent);
	// 	cta_div.setAttribute("id", "cta_elm");
	// 	cta_div.style.cssText = 'width:' + wdth + 'px;height:' + hgth + 'px;left:' + xp + 'px;top:' + yp + 'px;position:absolute;overflow:hidden;cursor:pointer;';
	// 	top.style.cssText = btm.style.cssText = 'margin:0;padding:0;position:relative;width:' + wdth + 'px;height:' + hgth + 'px;';

	// 	parentElm.addEventListener('mouseover', function (e) {
	// 		TweenMax.set(top, { y: 0 });
	// 		TweenMax.set(btm, { y: 0 });
	// 		TweenMax.to(top, 0.15, { rotationZ: 0.01, force3D: true, y: -(hgth), ease: Linear.easeNone });
	// 		TweenMax.to(btm, 0.15, { rotationZ: 0.01, force3D: true, y: -(hgth), ease: Linear.easeNone });
	// 	});

	// 	cta_div.appendChild(top);
	// 	cta_div.appendChild(btm);
	// 	parentElm.appendChild(cta_div);
	// 	return cta_div;
	// }
})();
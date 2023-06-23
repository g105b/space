(function() {
	const screenEl = document.getElementById("screen");
	const mainEl = document.querySelector("main");
	let screenElOffset = null;
	let mouseEl = null;
	let screenScale = 0;

	function resize() {
		let w = window.innerWidth
		let h = window.innerHeight;

		if(w <= 800 || h <= 600) {
			screenEl.style.scale = 1;
			return;
		}

		let screenW = 800;
		let screenH = 600;
		screenElOffset = screenEl.getBoundingClientRect();
		setTimeout(() => {
			screenElOffset = screenEl.getBoundingClientRect();
		}, 500);

		let wScale = w / screenW;
		let hScale = h / screenH;

		while(screenH * hScale > window.innerHeight) {
			wScale -= 0.1;
			hScale -= 0.1;
		}

		screenScale = Math.min(wScale, hScale);
		screenScale = Math.floor(screenScale * 2) / 2;

		screenEl.style.scale = screenScale;
		let bgSize = 4 * screenScale;
		document.body.style.backgroundSize = `${bgSize}px ${bgSize}px`;
	}
	function mouseMove(e) {
		if(window.innerWidth < 800) {
			if(mouseEl) {
				mouseEl.remove();
			}
			return;
		}

		if(!mouseEl) {
			mouseEl = new Image();
			mouseEl.id = "mouse";
		}
		if(!mouseEl.parentElement) {
			mainEl.appendChild(mouseEl);
		}

		if(e.target instanceof HTMLAnchorElement || e.target.closest("a")) {
			mouseEl.src = "/asset/mouse-pointer.png";
			mouseEl.clickX = 3;
		}
		else {
			mouseEl.src = "/asset/mouse.png";
			mouseEl.clickX = 0;
			mouseEl.clickY = 0;
		}

		let x = (e.x - screenElOffset.left - (mouseEl.clickX * screenScale)) / screenScale;
		let y = (e.y - screenElOffset.top - (mouseEl.clickY * screenScale)) / screenScale;
		mouseEl.style.left = `${x}px`;
		mouseEl.style.top = `${y}px`;
	}
	function mouseLeave() {
		if(mouseEl) {
			console.log("Removed");
			mouseEl.remove();
		}
	}

	resize();
	window.addEventListener("resize", resize);
	screenEl.addEventListener("mousemove", mouseMove);
	screenEl.addEventListener("mouseleave", mouseLeave);
})();

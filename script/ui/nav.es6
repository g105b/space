document.querySelectorAll("#screen>main>nav").forEach(nav => {
	const navUl = nav.querySelector("ul");
	const closeAnchor = document.createElement("a");
	closeAnchor.id = "close";
	closeAnchor.href = "#";
	closeAnchor.addEventListener("click", clickCloseAnchor);

	nav.querySelectorAll("a").forEach(anchor => {
		anchor.addEventListener("click", clickAnchor);
	});
	navUl.addEventListener("wheel", wheelUl);

	function clickAnchor(e) {
		let anchor = this;
		let box = anchor.getBoundingClientRect();
		anchor.parentElement.appendChild(closeAnchor);
		closeAnchor.style.width = `${box.width}px`;
	}

	function clickCloseAnchor(e) {
		this.remove();
	}

	function checkCurrentHash() {
		let matchingAnchor = document.querySelector(`a[href='${window.location.hash}']`);
		if(matchingAnchor) {
			matchingAnchor.click();
		}
	}

	function wheelUl(e) {
		navUl.scrollLeft += e.deltaY / 10;
	}

	checkCurrentHash();
});

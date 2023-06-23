let keyMap = {};

document.querySelectorAll("[accesskey]").forEach(el => {
	let key = el.accessKey;

	if(el.firstChild.nodeValue) {
		let text = el.firstElementChild
			? el.firstElementChild.firstChild
			: el.firstChild;

		for(let i = 0; i < text.data.length; i++) {
			if(text.data[i].toLowerCase() !== key) {
				continue;
			}

			if(key === "x") {
				console.log(key);
			}

			if(keyMap[key]) {
				continue;
			}

			keyMap[key] = el;

			let before = text.nodeValue.substring(0, i);
			let letter = key;
			let after = text.nodeValue.substring(i + 1);
			text.nodeValue = "";

			if(before) {
				let beforeSpan = document.createElement("span");
				beforeSpan.textContent = before;
				text.before(beforeSpan);
			}
			let letterSpan = document.createElement("span");
			letterSpan.textContent = letter;
			letterSpan.classList.add("accessKeyHighlight");
			text.before(letterSpan);

			if(after) {
				let afterSpan = document.createElement("span");
				afterSpan.textContent = after;
				text.before(afterSpan);
			}

			el.style.setProperty("--access-key-contrast", window.getComputedStyle(el).color);

			break;
		}
	}
});

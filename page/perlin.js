class PerlinNoise {
	canvas;
	seed;
	imageData;
	buffer;
	scalingBias = 1.4;

	constructor(canvas) {
		this.canvas = canvas;
		this.ctx = canvas.getContext("2d");
		this.seed = this.generateSeed();
		this.imageData = this.ctx.createImageData(this.canvas.width, this.canvas.height);
		this.buffer = [];
	}

	update(octaves) {
		for(let x = 0; x < this.canvas.width; x++) {
			for(let y = 0; y < this.canvas.height; y++) {
				let noiseScale = 0.0;
				let accumulatedScale = 0.0;
				let scale = 1.0;

				for(let o = 0; o < octaves; o++) {
					let pitch = this.canvas.width >> o;
					let sampleX1 = ~~(x / pitch) * pitch;
					let sampleY1 = ~~(y / pitch) * pitch;

					let sampleX2 = (sampleX1 + pitch) % this.canvas.width;
					let sampleY2 = (sampleY1 + pitch) % this.canvas.width;

					let blendX = (x - sampleX1) / pitch;
					let blendY = (y - sampleY1) / pitch;

					let sampleT = (1.0 - blendX)
						* this.seed[sampleY1 * this.canvas.width + sampleX1]
						+ blendX
						* this.seed[sampleY1 * this.canvas.width + sampleX2];
					let sampleB = (1.0 - blendX)
						* this.seed[sampleY2 * this.canvas.width + sampleX1]
						+ blendX
						* this.seed[sampleY2 * this.canvas.width + sampleX2];

					accumulatedScale += scale;
					noiseScale += (blendY * (sampleB - sampleT) + sampleT) * scale;
					scale = scale / this.scalingBias;
				}

				this.buffer[y * this.canvas.width + x] = noiseScale / accumulatedScale;
			}
		}
	}

	draw() {
		let data = this.imageData.data;
		cameraX -= 2;
		cameraY += 1;

		for(let x = 0; x < this.canvas.width; x++) {
			for(let y = 0; y < this.canvas.height; y++) {
				let drawX = (cameraX - x + this.canvas.width) % this.canvas.width;
				let drawY = (cameraY - y +this.canvas.height) % this.canvas.height;

				let index = y * this.canvas.width + x;
				let cameraIndex = drawY * this.canvas.width + drawX;

				let pixelValue = Math.round(this.buffer[index] * 255.0);
				data[(cameraIndex * 4) + 0] = pixelValue;
				data[(cameraIndex * 4) + 1] = pixelValue;
				data[(cameraIndex * 4) + 2] = pixelValue;
				data[(cameraIndex * 4) + 3] = 255;
			}
		}

		this.ctx.putImageData(this.imageData, 0, 0);
	}

	generateSeed() {
		let seedArray = [];
		for(let i = 0; i < this.canvas.width * this.canvas.height; i++) {
			seedArray[i] = Math.random();
		}
		return seedArray;
	}
}

const canvas = document.createElement("canvas");
canvas.width = canvas.height = 256;
document.body.append(canvas);

noise = new PerlinNoise(canvas);

let nOctaves = 8;
let cameraX = 0;
let cameraY = 0;

function loop(dt) {
	noise.update(nOctaves);
	noise.draw();
	requestAnimationFrame(loop);
}

loop();

document.addEventListener("keyup", e => {
	if(e.key === "q") {
		noise.scalingBias -= 0.2;
	}
	else if(e.key === "a") {
		noise.scalingBias += 0.2;
	}
	else if(e.key === " ") {
		nOctaves ++;
	}
	else if(e.key === "z") {
		for(let i = 0; i < canvas.width * canvas.height; i++) {
			noise.seed[i] = Math.random();
		}
	}
	else {
		alert(e.key);
	}

	if(nOctaves > 8) {
		nOctaves = 0;
	}
});

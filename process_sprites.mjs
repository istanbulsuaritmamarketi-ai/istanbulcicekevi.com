import sharp from 'sharp';
import fs from 'fs';
import path from 'path';

const outDir = 'C:\\laragon\\www\\istanbul-cicek-evi\\public\\images\\studio';
if (!fs.existsSync(outDir)) {
    fs.mkdirSync(outDir, { recursive: true });
}

const files = [
    { src: 'C:\\Users\\yigit\\.gemini\\antigravity\\brain\\8741e97c-0c3a-4926-9b74-b985af34cb00\\red_rose_bloom_1789818681805.jpg', out: 'red_rose.png', threshold: 240 },
    { src: 'C:\\Users\\yigit\\.gemini\\antigravity\\brain\\8741e97c-0c3a-4926-9b74-b985af34cb00\\white_rose_bloom_1789818695416.jpg', out: 'white_rose.png', threshold: 246, floodFill: true },
    { src: 'C:\\Users\\yigit\\.gemini\\antigravity\\brain\\8741e97c-0c3a-4926-9b74-b985af34cb00\\pink_rose_bloom_1789818709160.jpg', out: 'pink_rose.png', threshold: 242 },
    { src: 'C:\\Users\\yigit\\.gemini\\antigravity\\brain\\8741e97c-0c3a-4926-9b74-b985af34cb00\\daisy_bloom_1789818724409.jpg', out: 'daisy.png', threshold: 246, floodFill: true },
    { src: 'C:\\Users\\yigit\\.gemini\\antigravity\\brain\\8741e97c-0c3a-4926-9b74-b985af34cb00\\yellow_tulip_bloom_1789818738574.jpg', out: 'yellow_tulip.png', threshold: 242 },
    { src: 'C:\\Users\\yigit\\.gemini\\antigravity\\brain\\8741e97c-0c3a-4926-9b74-b985af34cb00\\gypsophila_sprig_1789818753637.jpg', out: 'gypsophila.png', threshold: 248 },
    { src: 'C:\\Users\\yigit\\.gemini\\antigravity\\brain\\8741e97c-0c3a-4926-9b74-b985af34cb00\\eucalyptus_sprig_1789818769310.jpg', out: 'eucalyptus.png', threshold: 240 },
    { src: 'C:\\Users\\yigit\\.gemini\\antigravity\\brain\\8741e97c-0c3a-4926-9b74-b985af34cb00\\luxury_black_box_1789818785303.jpg', out: 'black_box.png', threshold: 242 },
    { src: 'C:\\Users\\yigit\\.gemini\\antigravity\\brain\\8741e97c-0c3a-4926-9b74-b985af34cb00\\glass_cylinder_vase_1789818803166.jpg', out: 'glass_vase.png', threshold: 245, floodFill: true },
    { src: 'C:\\Users\\yigit\\.gemini\\antigravity\\brain\\8741e97c-0c3a-4926-9b74-b985af34cb00\\craft_bouquet_wrap_1789818819443.jpg', out: 'bouquet_wrap.png', threshold: 242 }
];

async function removeBackground(fileConfig) {
    console.log(`Processing: ${path.basename(fileConfig.src)} -> ${fileConfig.out}...`);
    const image = sharp(fileConfig.src);
    const { data, info } = await image.ensureAlpha().raw().toBuffer({ resolveWithObject: true });
    const { width, height, channels } = info;

    const isBg = new Uint8Array(width * height);

    if (fileConfig.floodFill) {
        // BFS Flood Fill from corners
        const queue = [];
        const pushIfBg = (x, y) => {
            if (x < 0 || x >= width || y < 0 || y >= height) return;
            const idx = y * width + x;
            if (isBg[idx]) return;

            const p = idx * channels;
            const r = data[p], g = data[p+1], b = data[p+2];
            const max = Math.max(r, g, b), min = Math.min(r, g, b);
            const avg = (r + g + b) / 3;

            if (avg >= fileConfig.threshold && (max - min) < 25) {
                isBg[idx] = 1;
                queue.push(idx);
            }
        };

        // Push perimeter points
        for (let x = 0; x < width; x++) {
            pushIfBg(x, 0);
            pushIfBg(x, height - 1);
        }
        for (let y = 0; y < height; y++) {
            pushIfBg(0, y);
            pushIfBg(width - 1, y);
        }

        let head = 0;
        while (head < queue.length) {
            const idx = queue[head++];
            const x = idx % width;
            const y = Math.floor(idx / width);

            pushIfBg(x + 1, y);
            pushIfBg(x - 1, y);
            pushIfBg(x, y + 1);
            pushIfBg(x, y - 1);
        }

        for (let i = 0; i < width * height; i++) {
            const p = i * channels;
            if (isBg[i]) {
                data[p + 3] = 0; // Transparent
            }
        }
    } else {
        // Global threshold with soft edge feathering
        for (let i = 0; i < width * height; i++) {
            const p = i * channels;
            const r = data[p], g = data[p+1], b = data[p+2];
            const avg = (r + g + b) / 3;
            const max = Math.max(r, g, b), min = Math.min(r, g, b);

            if (avg >= fileConfig.threshold && (max - min) < 25) {
                const alphaFactor = Math.max(0, (255 - avg) / (255 - fileConfig.threshold));
                data[p + 3] = Math.round(alphaFactor * 255);
            }
        }
    }

    const outPath = path.join(outDir, fileConfig.out);
    await sharp(data, { raw: { width, height, channels } })
        .resize(500, 500, { fit: 'inside' }) // Optimize size for blazing performance!
        .png({ compressionLevel: 8 })
        .toFile(outPath);

    console.log(`Saved: ${outPath}`);
}

(async () => {
    for (const f of files) {
        await removeBackground(f);
    }
    console.log('ALL SPRITES PROCESSED SUCCESSFULLY!');
})();

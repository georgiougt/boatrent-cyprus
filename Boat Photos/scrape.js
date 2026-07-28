const fs = require('fs');
const path = require('path');

const urls = [
  // First Batch
  'https://rentyachtsincyprus.com/en/yacht/azimut-42',
  'https://rentyachtsincyprus.com/en/yacht/princess-61',
  'https://rentyachtsincyprus.com/en/yacht/sea-ray-375',
  'https://rentyachtsincyprus.com/en/yacht/sea-ray-63',
  'https://rentyachtsincyprus.com/en/yacht/sea-ray-455',
  'https://rentyachtsincyprus.com/en/yacht/azimut-46',
  'https://rentyachtsincyprus.com/en/yacht/azimut-62',
  'https://rentyachtsincyprus.com/en/yacht/napa-blue',
  'https://rentyachtsincyprus.com/en/yacht/ferreti-892',
  'https://rentyachtsincyprus.com/en/yacht/sunseeker-50',
  'https://rentyachtsincyprus.com/en/yacht/sea-ray-62',
  'https://rentyachtsincyprus.com/en/yacht/sunseeker-manhattan-54',

  // Second Batch
  'https://rentyachtsincyprus.com/en/yacht/sea-ray-52',
  'https://rentyachtsincyprus.com/en/yacht/sunseeker-50-destiny',
  'https://rentyachtsincyprus.com/en/yacht/ocean-dream',
  'https://rentyachtsincyprus.com/en/yacht/kurosivo-iv',
  'https://rentyachtsincyprus.com/en/yacht/latchi-icon',
  'https://rentyachtsincyprus.com/en/yacht/rodman-38',
  'https://rentyachtsincyprus.com/en/yacht/ferreti-67',
  'https://rentyachtsincyprus.com/en/yacht/elysian-blue',
  'https://rentyachtsincyprus.com/en/yacht/azimut-465',
  'https://rentyachtsincyprus.com/en/yacht/sea-ray-456',
  'https://rentyachtsincyprus.com/en/yacht/ag-queen',
  'https://rentyachtsincyprus.com/en/yacht/galeon-375-gto',

  // Third Batch
  'https://rentyachtsincyprus.com/en/yacht/sea-ray-32',
  'https://rentyachtsincyprus.com/en/yacht/azimut-55',
  'https://rentyachtsincyprus.com/en/yacht/calypso',
  'https://rentyachtsincyprus.com/en/yacht/sea-ray-slx-400'
];

const headers = {
  'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
  'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8',
  'Accept-Language': 'en-US,en;q=0.9'
};

function sleep(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}

function decodeHtmlEntities(str) {
  if (!str) return str;
  return str
    .replace(/&amp;/g, '&')
    .replace(/&lt;/g, '<')
    .replace(/&gt;/g, '>')
    .replace(/&quot;/g, '"')
    .replace(/&#039;/g, "'")
    .replace(/&apos;/g, "'")
    .replace(/&nbsp;/g, ' ');
}

function extractRegex(html, regex) {
  const match = html.match(regex);
  return match ? decodeHtmlEntities(match[1].trim()) : null;
}

function extractStat(html, label) {
  const regex = new RegExp(`<div class="dly-k">\\s*${label}\\s*<\\/div>\\s*<div class="dly-v">\\s*([^<]+?)\\s*<\\/div>`, 'i');
  return extractRegex(html, regex);
}

function extractMeta(html, label) {
  const regex = new RegExp(`<div class="dly-metaK">\\s*${label}\\s*<\\/div>\\s*<div class="dly-metaV">\\s*([^<]+?)\\s*<\\/div>`, 'i');
  return extractRegex(html, regex);
}

function extractRates(html) {
  const rates = {};
  const regex = /<div class="expv-left">\s*([\s\S]*?)\s*<\/div>\s*<div class="expv-right">\s*([\s\S]*?)\s*<\/div>/g;
  let match;
  while ((match = regex.exec(html)) !== null) {
    const duration = match[1].replace(/\s+/g, ' ').trim();
    const price = match[2].replace(/\s+/g, ' ').trim();
    if (duration && price && (price.includes('€') || /^\d+$/.test(price.replace(/[\s,.]/g, '')))) {
      rates[decodeHtmlEntities(duration)] = decodeHtmlEntities(price);
    }
  }
  return rates;
}

function extractWhatsIncluded(html) {
  const items = [];
  const regex = /<div class="feature-box">[\s\S]*?<h5[^>]*?>\s*([\s\S]*?)\s*<\/h5>(?:\s*<p[^>]*?>\s*([\s\S]*?)\s*<\/p>)?/gi;
  let match;
  while ((match = regex.exec(html)) !== null) {
    const title = decodeHtmlEntities(match[1].replace(/\s+/g, ' ').trim());
    const description = match[2] ? decodeHtmlEntities(match[2].replace(/\s+/g, ' ').trim()) : '';
    items.push({ title, description });
  }
  return items;
}

async function downloadFile(url, outputPath) {
  const res = await fetch(url, { headers });
  if (!res.ok) throw new Error(`Failed to fetch image: ${res.statusText}`);
  const arrayBuffer = await res.arrayBuffer();
  const buffer = Buffer.from(arrayBuffer);
  fs.writeFileSync(outputPath, buffer);
}

async function scrapeYacht(url) {
  const slug = url.split('/').pop();
  console.log(`Scraping ${slug}...`);

  const res = await fetch(url, { headers });
  if (!res.ok) {
    throw new Error(`HTTP Error ${res.status} for ${url}`);
  }
  const html = await res.text();

  const name = extractRegex(html, /<h1 class="dly-title">\s*([^<]+?)\s*<\/h1>/i) || slug;
  const departureMarina = extractRegex(html, /<span>Departure Marina:\s*<b>\s*([^<]+?)\s*<\/b><\/span>/i);
  
  const size = extractStat(html, 'Yacht Size');
  const guestCapacity = extractStat(html, 'Guest');
  const crew = extractStat(html, 'Crew');
  
  const cabins = extractMeta(html, 'Cabins');
  const bathrooms = extractMeta(html, 'Bathrooms');
  
  const rates = extractRates(html);
  const whatsIncluded = extractWhatsIncluded(html);
  
  const profilePictureUrl = extractRegex(html, /<meta property="og:image" content="([^"]+?)"/i);
  
  let localProfilePicture = null;
  if (profilePictureUrl) {
    const ext = profilePictureUrl.split('.').pop().split('?')[0] || 'webp';
    localProfilePicture = `${slug}.${ext}`;
    const imagePath = path.join(__dirname, localProfilePicture);
    if (!fs.existsSync(imagePath)) {
      console.log(`Downloading profile picture to ${localProfilePicture}...`);
      try {
        await downloadFile(profilePictureUrl, imagePath);
      } catch (err) {
        console.error(`Failed to download image from ${profilePictureUrl}: ${err.message}`);
      }
    } else {
      console.log(`Profile picture ${localProfilePicture} already exists, skipping download.`);
    }
  }

  return {
    name,
    slug,
    url,
    departure_marina: departureMarina,
    size,
    guest_capacity: guestCapacity ? parseInt(guestCapacity, 10) || guestCapacity : null,
    crew: crew ? parseInt(crew, 10) || crew : null,
    cabins: cabins ? parseInt(cabins, 10) || cabins : null,
    bathrooms: bathrooms ? parseInt(bathrooms, 10) || bathrooms : null,
    profile_picture_url: profilePictureUrl,
    local_profile_picture: localProfilePicture,
    rates,
    whats_included: whatsIncluded
  };
}

async function main() {
  const jsonPath = path.join(__dirname, 'boats.json');
  let results = [];
  
  if (fs.existsSync(jsonPath)) {
    try {
      results = JSON.parse(fs.readFileSync(jsonPath, 'utf-8'));
      console.log(`Loaded ${results.length} existing boat records from boats.json`);
    } catch (err) {
      console.error('Error reading existing boats.json, starting fresh:', err.message);
    }
  }

  const resultMap = new Map(results.map(b => [b.url, b]));

  for (const url of urls) {
    const existing = resultMap.get(url);
    if (existing && existing.whats_included && existing.whats_included.length > 0) {
      console.log(`Skipping ${url.split('/').pop()} (already has whats_included)`);
      continue;
    }

    try {
      const data = await scrapeYacht(url);
      resultMap.set(url, data);
      console.log(`Successfully scraped ${data.name}.\n`);
      const updatedResults = Array.from(resultMap.values());
      fs.writeFileSync(jsonPath, JSON.stringify(updatedResults, null, 2), 'utf-8');
    } catch (err) {
      console.error(`Error scraping ${url}:`, err.message);
    }
    await sleep(1500);
  }

  const finalResults = Array.from(resultMap.values());
  fs.writeFileSync(jsonPath, JSON.stringify(finalResults, null, 2), 'utf-8');
  console.log(`Scraping complete! Total ${finalResults.length} boat records in ${jsonPath}`);
}

main();

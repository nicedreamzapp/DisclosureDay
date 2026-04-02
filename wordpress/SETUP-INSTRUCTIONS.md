# WordPress Setup Instructions

## Quick Start - Add to Your Disclosure Day Page

### Option 1: Custom HTML Block (Easiest)

1. Go to **WordPress Admin > Pages > Disclosure Day**
2. Click **Edit**
3. Delete the current content ("test")
4. Add a **Custom HTML** block
5. Copy ALL contents from `disclosure-day-page.html`
6. Paste into the Custom HTML block
7. Click **Update** to save

### Option 2: Full Width Template (Better)

If you want full-width without sidebars:

1. Edit the Disclosure Day page
2. In the right sidebar, find **Template**
3. Select **Full Width** or **Elementor Full Width**
4. If using Elementor, you can add an HTML widget

### Option 3: Child Theme Template (Advanced)

For full control:

1. Create a child theme (if you haven't already)
2. Copy `disclosure-day-template.php` to your child theme folder
3. In WordPress Admin, edit the Disclosure Day page
4. Select "Disclosure Day" from the Template dropdown

---

## Setting Up Live News Feed

### Step 1: Get Free API Keys

**NewsAPI.org** (Entertainment News)
1. Go to https://newsapi.org/register
2. Sign up for free account
3. Copy your API key

**TMDB** (Movie Database)
1. Go to https://www.themoviedb.org/signup
2. Create account
3. Go to Settings > API
4. Request API key (free)

**Twitter/X API** (Social Monitoring)
1. Go to https://developer.twitter.com/
2. Apply for developer account
3. Create a project and app
4. Generate Bearer Token

### Step 2: Add Keys to Your Site

Edit `api/news-aggregator.js` and add your keys:

```javascript
const newsAggregator = new DisclosureDayNews({
  newsApiKey: 'YOUR_NEWSAPI_KEY_HERE',
  twitterBearerToken: 'YOUR_TWITTER_BEARER_TOKEN',
  tmdbApiKey: 'YOUR_TMDB_API_KEY'
});
```

### Step 3: Upload Files

1. Connect via FTP or use WordPress File Manager plugin
2. Upload `api/news-aggregator.js` to your theme or uploads folder
3. Add script reference before closing `</body>` tag:

```html
<script src="/wp-content/uploads/disclosure-day/api/news-aggregator.js"></script>
```

---

## Adding Real Cast/Crew Images

Replace the placeholder images in the HTML with real photos:

### Method 1: Direct URL
```html
<img class="dd-cast-image" src="https://your-site.com/wp-content/uploads/emily-blunt.jpg" alt="Emily Blunt">
```

### Method 2: Media Library
1. Upload images to WordPress Media Library
2. Get the URL of each image
3. Replace the `style="background..."` with `src="URL"`

### Recommended Image Sources
- Official press kits (when available)
- IMDb Pro (with subscription)
- Wikimedia Commons (free, check licenses)
- Official studio releases

---

## Embedding the Trailer

When the official trailer YouTube link is available:

1. Find this line in the JavaScript:
```javascript
const trailerId = 'YOUR_YOUTUBE_TRAILER_ID';
```

2. Replace with the actual YouTube video ID:
```javascript
const trailerId = 'dQw4w9WgXcQ'; // Example - use real trailer ID
```

The YouTube ID is the part after `v=` in the URL:
`https://www.youtube.com/watch?v=dQw4w9WgXcQ`
                                  ^^^^^^^^^^^

---

## Customization Tips

### Change Colors
Edit the CSS variables at the top of the HTML:

```css
:root {
  --dd-cyan: #00d4ff;    /* Accent color */
  --dd-blue: #0066ff;    /* Primary blue */
  --dd-gold: #ffd700;    /* Featured items */
}
```

### Add More Easter Egg Codes
Add to the `codes` object in JavaScript:

```javascript
const codes = {
  'CLOSE': 'D-E-G-E-C... The five notes.',
  'YOUR_CODE': 'Your secret message here',
};
```

### Update Release Date
Change the countdown target:

```javascript
const releaseDate = new Date('June 12, 2026 00:00:00').getTime();
```

---

## Tracking & Analytics

### Google Analytics Events
Add this to track engagement:

```javascript
// Track trailer clicks
document.getElementById('trailerPlaceholder').addEventListener('click', () => {
  gtag('event', 'trailer_click', {
    'event_category': 'engagement',
    'event_label': 'disclosure_day_trailer'
  });
});

// Track code attempts
function checkAccessCode() {
  gtag('event', 'code_attempt', {
    'event_category': 'engagement',
    'event_label': document.getElementById('accessCode').value
  });
  // ... rest of function
}
```

---

## Troubleshooting

### Page looks broken
- Make sure you copied ALL the HTML including `<style>` and `<script>` tags
- Check if Astra theme has conflicting styles
- Try adding `!important` to key CSS rules

### Countdown shows "---"
- JavaScript might be blocked
- Check browser console for errors

### News feed not loading
- API keys not configured
- CORS errors (need server-side proxy)
- Check NewsAPI free tier limits (100 req/day)

### Mobile layout issues
- The CSS includes responsive styles
- Test at different breakpoints
- May need to adjust for Astra's mobile menu

---

## Need Help?

1. Check WordPress Error Logs
2. Browser Console (F12 > Console)
3. Test in incognito mode (no extensions)

---

## Next Steps

After basic setup:
1. Add real cast images
2. Configure news API
3. Add trailer when released
4. Create additional pages (Cast, News, Secrets)
5. Set up social media accounts
6. Plan content release schedule

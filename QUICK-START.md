# Disclosure Day Hub - Quick Start Guide

## Your Files

```
disclosure day/
├── disclosure-day-v2.html      ← PASTE THIS INTO WORDPRESS (enhanced version)
├── disclosure-day-page.html    ← Alternative cinematic version
├── wordpress/
│   ├── disclosure-day-api.php  ← Add to functions.php for live updates
│   └── SETUP-INSTRUCTIONS.md   ← Detailed WordPress setup
├── api/
│   └── news-aggregator.js      ← Client-side news fetching
├── ARCHITECTURE.md             ← Full folder structure plan
└── CAMPAIGN-STRATEGY.md        ← Marketing rollout plan
```

---

## 5-Minute Setup

### Step 1: Deploy the Page

1. Go to WordPress Admin → Pages → **Disclosure Day**
2. Click **Edit**
3. Delete "test"
4. Add a **Custom HTML** block
5. Copy ALL contents of `disclosure-day-v2.html`
6. Paste and hit **Update**

### Step 2: Enable Live Updates (Optional)

1. Go to **Appearance → Theme File Editor**
2. Select your child theme's `functions.php`
3. Paste the contents of `wordpress/disclosure-day-api.php` at the end
4. Save

Now your Live Updates section auto-fetches news about the film!

### Step 3: Manage Updates

After adding the API code:
- Go to **Settings → DD Updates**
- Add your own curated updates
- They appear alongside auto-fetched Google News

---

## What's Working Now

| Feature | Status |
|---------|--------|
| Official teaser trailer embed | ✅ Live |
| Official posters | ✅ Live |
| Countdown to June 12, 2026 | ✅ Live |
| Cast & crew section | ✅ Live |
| Campaign timeline | ✅ Live |
| Live updates (with API) | ⚙️ Add PHP code |
| Newsletter signup | ⚙️ Connect provider |

---

## Connect Your Email List

Replace the form in the HTML with your email provider:

**Mailchimp:**
```html
<form class="dd-form" action="https://YOUR-MAILCHIMP-URL" method="POST">
  <input class="dd-input" type="email" name="EMAIL" required placeholder="you@email.com">
  <button class="dd-btn primary" type="submit">Notify me ✨</button>
</form>
```

**ConvertKit:**
```html
<form class="dd-form" action="https://app.convertkit.com/forms/YOUR-FORM-ID/subscriptions" method="post">
  <input class="dd-input" type="email" name="email_address" required placeholder="you@email.com">
  <button class="dd-btn primary" type="submit">Notify me ✨</button>
</form>
```

---

## Key Links

- **Official Site:** https://www.disclosuredaymovie.com/
- **Trailer:** https://www.youtube.com/watch?v=UFe6NRgoXCM
- **Wikipedia:** https://en.wikipedia.org/wiki/Disclosure_Day
- **IMDb:** https://www.imdb.com/title/tt15047880/

---

## Make Kristie Proud

Your sister has worked with Spielberg for **27 years**. This hub honors that legacy by:

1. **Being professional** - Clean design, real sources, verified info
2. **Building anticipation** - Countdown, timeline, phased reveals
3. **Creating community** - Newsletter, live updates, shareable assets
4. **Telling the story** - Cast spotlights, creator features, the "why"

You have 6 months until release. Use the Campaign Strategy to plan weekly drops.

---

## Next Steps

1. ✅ Deploy the page today
2. 📧 Connect your email provider
3. 📸 Download posters, re-upload to your Media Library
4. 📅 Start weekly cast/crew spotlights
5. 📢 Share on socials with #DisclosureDay

The truth is coming. You're ready.

# Adding Disqus Comments to Pages

## 1. Create Disqus Account

1. Go to https://disqus.com/
2. Sign up for a free account
3. Click "I want to install Disqus on my site"
4. Enter site name: `disclosure-day-hub` (or similar)
5. Select "Basic" (free) plan
6. Choose "I don't see my platform" → "Universal Code"

## 2. Add This Code to Any Page

Add this just before the `</body>` tag on any page where you want comments:

```html
<!-- Disqus Comments -->
<div id="disqus_thread" style="max-width:900px;margin:40px auto;padding:0 20px"></div>
<script>
var disqus_config = function () {
    this.page.url = window.location.href;
    this.page.identifier = window.location.pathname;
};
(function() {
    var d = document, s = d.createElement('script');
    s.src = 'https://YOUR-SHORTNAME.disqus.com/embed.js';  // ← Replace with your shortname
    s.setAttribute('data-timestamp', +new Date());
    (d.head || d.body).appendChild(s);
})();
</script>
```

## 3. Replace YOUR-SHORTNAME

When you create your Disqus site, you'll get a "shortname" like `disclosure-day-hub`.

Replace `YOUR-SHORTNAME` in the code above with your actual shortname.

## 4. Recommended Pages for Comments

Add comments to:
- `/theories/alien-intentions.html`
- `/theories/government-coverup.html`
- `/cast/emily-blunt.html`
- `/cast/josh-oconnor.html`
- `/topics/plot.html`
- `/topics/trailer.html`

## 5. Styling

The Disqus widget auto-adapts to dark mode. If needed, you can add custom CSS:

```css
#disqus_thread {
    background: #0a0d14;
    padding: 20px;
    border-radius: 12px;
    border: 1px solid rgba(0,255,204,.15);
}
```

## 6. Moderation

Log into disqus.com to:
- Moderate comments
- Set up spam filters
- Enable/disable guest posting
- Get email notifications

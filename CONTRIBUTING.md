# Contributing

Thanks for taking a look. This is an unofficial fan site made of hand-written static pages, so fixes
to facts, broken links and layout problems are all welcome.

## Reporting a problem

Open an [issue](https://github.com/nicedreamzapp/DisclosureDay/issues/new/choose). The things that
make it quick to fix:

- the page URL or file name (for example `cast/emily-blunt.html`)
- your browser and device, and whether it happens on phone, desktop or both
- what looks wrong, with a screenshot if it is a layout problem
- for a wrong fact about the film, a link to the source

## Running it locally

There is no build step. Clone the repo:

```bash
git clone https://github.com/nicedreamzapp/DisclosureDay.git
cd DisclosureDay
```

To try the chatbot, copy the example and add your own OpenAI key:

```bash
cp chat-api.example.php chat-api.php
```

The chatbot and fan submissions need a server with PHP and the curl extension. The Nginx config used
for the live site is in `nginx-config.conf`. For fan submissions, the web server user needs write
access to `community/data/`.

## Tests

There are no tests or build tooling. Check changes by hand: open the pages you changed on a phone and
a desktop browser, and make sure links work. If you add a page, add it to `sitemap.xml`.

## Pull requests

Small and focused is easiest to review. Note that `seo-pages/` and the top-level folders overlap,
and the live sitemap points at the top-level folders, so edit those. For big changes, open an issue
first so we can talk it through.

Please follow the [code of conduct](CODE_OF_CONDUCT.md).

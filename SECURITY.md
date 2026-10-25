# Security policy

## Reporting a vulnerability

Please do not open a public issue for security problems.

Report them privately through GitHub: go to the **Security** tab of this repo and click **Report a vulnerability** ([direct link](https://github.com/nicedreamzapp/DisclosureDay/security/advisories/new)). Only the maintainer sees the report.

Helpful details: what you found, how to reproduce it, which page or file is affected, and whether it affects the live site at disclosureday.nicedreamzwholesale.com.

I aim to reply within 3 days and to ship a fix or a clear plan within 30 days, depending on severity. You will be credited in the advisory unless you ask not to be.

## Supported versions

Fixes go into the latest code on `main`, which is what the live site runs.

## What matters most here

The parts where a mistake could hurt someone:

- the chatbot proxy in `chat-api.example.php`, which holds the OpenAI API key server-side and accepts requests from any origin
- `community/submit.php`, which takes public submissions and writes them to JSON files in `community/data/`
- the WordPress code in `wordpress/disclosure-day-api.php`, which adds a REST route and an admin page that saves updates
- the server config in `nginx-config.conf`

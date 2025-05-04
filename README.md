# Threads to Posts

A WordPress plugin that imports your posts from [Threads](https://www.threads.com) into your WordPress blog.

README in Korean: [README-ko.md](./README-ko.md)

## Installation Guide

**Note**: This plugin is **not** officially registered on [wordpress.org].  

- PHP 8.0 or higher
- Required extensions: `ext-pcntl`, `ext-zlib`

Clone the plugin via Git:

```bash
git clone https://github.com/chwnam/threads-to-posts.git
```

Move the entire plugin directory to your WordPress `wp-content/plugins/` folder.  
Log into your WordPress admin panel and activate the plugin.  
Then, you will find **Threads to Posts** under **Tools** in the admin menu.

## Configuration Guide

To use the Threads API correctly:

1. Register an app for Threads on the [Meta developers](https://developers.facebook.com/).
2. Get the App ID and App Secret from the app.
3. Get an access token through the plugin.

For details, refer to the [Setup Guide](./docs/en/how-to-setup.md).

## Post Scraping

Refer to the [Scraping Guide](./docs/en/scrap.md) for information on scraping posts.

## Command Line

This plugin supports some advanced commands via WP-CLI.  
See the [CLI Manual](./docs/en/cli-manual.md) for details.

## Changelog

[CHANGELOG.md](./CHANGELOG.md)

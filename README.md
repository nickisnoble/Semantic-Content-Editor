# Semantic Content Plugin for WordPress

A WordPress plugin for building cleanly coded posts from predefined blocks and markdown.

## Project goals

- Feel like native WordPress
- Be easily extensible
- Utilize Markdown formatting to it's fullest
- Allow crafting of content without need for full stylistic control
- Store content efficiently

## Getting Started

1. Backup your wordpress content, this replaces the default editor on posts
1. Install and activate as a plugin
1. Write posts with markdown!

## Contributing

1. Fork it!
2. Create your feature branch: `git checkout -b feature/my-new-feature`
3. Commit your changes: `git commit -am 'Add some feature'`
4. Push to the branch: `git push origin feature/my-new-feature`
5. Submit a pull request :D

### Development Setup

This project uses [Grunt.js](http://gruntjs.com/) to process stylesheets. In order to properly work with `npm`, you'll need to have [node.js](https://nodejs.org/) installed. 

When you first clone the repo, run `$ npm install` to install the project dependencies.

While working on the project, we have a few commands set up:

`$ grunt` - watches `scss` files for changes, and automatically processes the styles on save.

`$ grunt compile` - runs style processing once.

## History

**v0.1.0-beta** - Built the basic prototype, includes the ability to:

- add and remove blocks
- reorder block via drag and drop
- write & render Markdown, including GitHub flavored & MD Extra
- save as markdown in post meta
- optionally add alignment classes to blocks

## Credits

Design & Development: 

[@nickisnoble](http://nicknoble.works)

[@pixelbud](http://garybacon.com)

[@retroantix](http://cgloss.com)

## License

The MIT License (MIT)

Copyright &copy; 2015 Nick Noble, Christian Gloss, and Gary Bacon

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

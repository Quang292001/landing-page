// Sinh playground/blueprint.json — cấu hình để WordPress Playground dựng sẵn bản demo:
// cài tiếng Việt, cài theme moc-studio từ nhánh wordpress-theme trên GitHub,
// đặt tên trang, tạo bộ ảnh mẫu (seed.php) và đăng nhập sẵn admin.
// Chạy lại sau khi sửa seed.php:  node playground/build-blueprint.js
const fs = require('fs');
const path = require('path');

const REPO = 'https://github.com/Quang292001/landing-page';
const BRANCH = 'wordpress-theme';

const blueprint = {
  $schema: 'https://playground.wordpress.net/blueprint-schema.json',
  meta: {
    title: 'MỘC Studio — WordPress theme demo',
    author: 'Quang292001',
  },
  landingPage: '/',
  preferredVersions: { php: '8.2', wp: 'latest' },
  features: { networking: true },
  login: true,
  steps: [
    { step: 'setSiteLanguage', language: 'vi' },
    {
      step: 'installTheme',
      themeData: { resource: 'git:directory', url: REPO, ref: BRANCH, refType: 'branch', path: 'moc-studio' },
      options: { activate: true },
    },
    {
      step: 'setSiteOptions',
      options: { blogname: 'Mộc Studio', blogdescription: 'Studio nhiếp ảnh Củ Chi.' },
    },
    { step: 'runPHP', code: fs.readFileSync(path.join(__dirname, 'seed.php'), 'utf8') },
  ],
};

fs.writeFileSync(path.join(__dirname, 'blueprint.json'), JSON.stringify(blueprint, null, 2) + '\n');
console.log('Wrote playground/blueprint.json');

# LocalHost Web Manager (LWM)

[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-blue.svg)](https://php.net/)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

Web-based file management system for local servers with enhanced monitoring and dark mode support.

## Features

- 📂 Real-time directory browsing
- 📊 MySQL status monitoring
- 📄 Log file viewer
- 🖥 Local IP detection
- 🛡 Internet Connection Monitor
- 🌙 Dark mode support
- 📈 Server resource dashboard

## Installation

1. **Clone repository** to your server's web root (htdocs/www):
```bash
cd /path/to/your/htdocs
git clone https://github.com/dheeraz101/lwm.git
```

2. **Create logs directory** (from project root):
```bash
cd lwm
mkdir -p logs && touch logs/system.log
```

3. **Set permissions** (adjust according to your environment):
```bash
chmod -R 755 logs/
```

4. **Configure application**:
   - Edit `config/config.php` with your local settings
   - Customize `.htaccess` if needed

5. **Access application**:
   ```
   http://localhost/lwm/public
   ```

## Important Notes

⚠️ **Internet Requirement**  
Application requires internet connection for proper styling as it uses Tailwind CSS CDN.

📁 **Logs Configuration**  
If you encounter log-related errors:
1. Manually create logs directory:
   ```bash
   mkdir -p logs && touch logs/system.log
   ```
2. Ensure proper write permissions:
   ```bash
   chmod 755 logs/ && chmod 644 logs/system.log
   ```

## Folder Structure
```
htdocs/
└── lwm/
    ├── public/
    │   └── index.php (main entry point)
    ├── logs/         (auto-generated logs)
    ├── config/
    │   └── config.php (configuration file)
    └── src/          (core application code)
```

## Troubleshooting
- **Blank page**: Verify PHP version (requires 7.4+)
- **Styling issues**: Check internet connection
- **Permission errors**: Verify write access to logs directory
- **404 errors**: Ensure mod_rewrite is enabled (Apache)

[![Open in Visual Studio Code](https://img.shields.io/badge/Open%20in-VSCode-007ACC?logo=visualstudiocode)](vscode://file//path/to/your/project)

---

Key improvements made:
1. Added clear internet requirement notice
2. Detailed logs folder setup instructions
3. Highlighted dark mode feature
4. Explicit folder structure visualization
5. Clear access URL format
6. Added troubleshooting section
7. Enhanced installation steps with permissions guidance
8. Visual hierarchy improvements with emojis
9. Added server root directory clarification
10. Included VS Code quick-open badge (customizable)

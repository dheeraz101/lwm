<?php
/**
 * LocalServer Web Manager - [Open-source web interface for local server management]
 * 
 * @package     LocalServer-WebManager
 * @description Main File for the code snippet.
 * @version     1.0.0
 * @author      Dheeraz
 * @license     MIT
 * @link        https://github.com/dheeraz101/lwm
 */ ?>

<!DOCTYPE html>
<html lang="en" class="dark:bg-gray-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="bg-gray-50 dark:bg-gray-900 transition-colors duration-200">
    <div class="container mx-auto px-4 py-6 max-w-7xl">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
            <div class="flex items-center gap-3">
            <h1 class="text-2xl font-bold text-gray-700 dark:text-white !dark:text-white">🖥️ Server Monitor</h1>
                <div id="loading-spinner" class="hidden">
                    <svg class="animate-spin h-6 w-6 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>
            
            <div class="flex items-center gap-4">
                <button id="dark-mode-toggle" class="p-2 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600">
                    <span class="dark:hidden">🌙</span>
                    <span class="hidden dark:inline">☀️</span>
                </button>
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    <p id="current-date-time"></p>
                    <p id="system-user"></p>
                </div>
            </div>

                    <!-- In the header text-sm div -->
            <div class="text-sm text-gray-600 dark:text-gray-400">
                <p id="current-date-time"></p>
                <p id="system-user"></p>
                <p>Server IP: <span id="server-ip" class="font-mono text-blue-600"></span></p>
            </div>
        </div>

        <!-- System Status Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <!-- Internet Status Card -->
            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm hover-scale group">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <h3 class="font-semibold text-gray-700 dark:text-gray-200">Internet</h3>
                    </div>
                </div>
                <div class="text-sm space-y-2">
                    <div class="flex justify-between items-center">
                        <span>Status:</span>
                        <div class="flex items-center gap-2">
                            <span id="internet-light" class="status-dot"></span>
                            <span id="internet-status-text" class="status-text font-medium">-</span>
                        </div>
                    </div>
                    <div class="progress-bar bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div id="internet-quality-bar" class="progress-fill bg-green-500 h-2 rounded-full transition-all duration-500" style="width: 0%"></div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
                        <span>Speed: <span id="internet-speed">-</span></span>
                        <span>Uptime: <span id="internet-uptime">-</span></span>
                    </div>
                </div>
            </div>
            
            <!-- MySQL Status Card -->
            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm hover-scale group">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <h3 class="font-semibold text-gray-700 dark:text-gray-200">MySQL Database</h3>
                    </div>
                </div>
                <div id="mysql-status-text" class="text-sm space-y-2">
                    <div class="flex justify-between items-center">
                        <span>Status:</span>
                        <div class="flex items-center gap-2">
                            <span class="status-dot"></span>
                            <span class="status-text font-medium">Checking...</span>
                        </div>
                    </div>
                    <div class="progress-bar bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div class="progress-fill bg-blue-500 h-2 rounded-full transition-all duration-500" style="width: 0%"></div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
                        <span>Uptime: <span id="mysql-uptime">-</span></span>
                        <span>v<span id="mysql-version">-</span></span>
                    </div>
                </div>
            </div>

            <!-- Storage Card -->
            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm hover-scale group">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <h3 class="font-semibold text-gray-700 dark:text-gray-200">Storage</h3>
                    </div>
                </div>
                <div class="text-sm space-y-2">
                    <div class="flex justify-between items-center">
                        <span>Status:</span>
                        <div class="flex items-center gap-2">
                            <span class="status-dot bg-green-500 animate-pulse"></span>
                            <span class="status-text font-medium">Healthy</span>
                        </div>
                    </div>
                    <div class="progress-bar bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div class="progress-fill bg-purple-500 h-2 rounded-full transition-all duration-500" style="width: 65%"></div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
                        <span>Total: <span id="total-storage">-</span></span>
                        <span>Free: <span id="free-storage">-</span></span>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
                        <span>Folders: <span id="total-folders" class="text-blue-600">-</span></span>
                        <span>Files: <span id="total-files">-</span></span>
                    </div>
                </div>
            </div>

            <!-- Logs Card -->
            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm hover-scale group">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <h3 class="font-semibold text-gray-700 dark:text-gray-200">System Logs</h3>
                    </div>
                </div>
                <div class="text-sm space-y-2">
                    <div class="flex justify-between items-center">
                        <span>Status:</span>
                        <div class="flex items-center gap-2">
                            <span class="status-dot bg-green-500 animate-pulse"></span>
                            <span class="status-text font-medium">Stable</span>
                        </div>
                    </div>
                    <div class="progress-bar bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div class="progress-fill bg-yellow-500 h-2 rounded-full transition-all duration-500" style="width: 100%"></div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
                        <span>Last Updated: <span id="last-log-update">-</span></span>
                        <span>Entries: <span id="log-entries">-</span></span>
                    </div>
                    <button onclick="fetchLogs()" class="w-full text-blue-600 hover:underline mt-1 text-xs text-right">
                        View Full Logs →
                    </button>
                </div>
            </div>
        </div>

        <!-- Controls -->
        <div class="flex flex-wrap gap-4 mb-6 items-center">
            <div class="flex-1 relative">
                <input type="text" id="search" placeholder="🔍 Search folders..."
                    class="w-full px-4 py-2 rounded-lg border dark:border-gray-700 dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button onclick="clearSearch()" class="absolute right-3 top-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    ×
                </button>
            </div>
            <select id="size-unit" onchange="convertSizes()"
            class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
                <option value="B">Bytes</option>
                <option value="KB">KB</option>
                <option value="MB">MB</option>
                <option value="GB">GB</option>
            </select>
        </div>

        <!-- Add this after the controls section -->
        <div id="path-breadcrumb" class="bg-gray-100 dark:bg-gray-800 p-3 rounded-lg">
            <button onclick="fetchFolders()" class="text-blue-600 hover:underline">Home</button>
            <span>/</span>
            <span id="current-path"></span>
        </div>

        <!-- Folder Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden hover-scale">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left cursor-pointer" onclick="sortTable('name')">Name</th>
                        <th class="px-4 py-3 text-left cursor-pointer" onclick="sortTable('date')">Modified</th>
                        <th class="px-4 py-3 text-left cursor-pointer" onclick="sortTable('size')">Size</th>
                        <th class="px-4 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody id="folder-table" class="divide-y divide-gray-200 dark:divide-gray-700">
                    <!-- Dynamic content -->
                </tbody>
            </table>
        </div>
    
        <div id="skeleton-loading" class="hidden mb-6">
            <div class="animate-pulse bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                <div class="space-y-4 p-4">
                    <!-- Table Header Skeleton -->
                    <div class="h-10 bg-gray-200 dark:bg-gray-700 rounded"></div>
                    
                    <!-- Table Rows Skeleton -->
                    <div class="space-y-4">
                        <div class="h-14 bg-gray-200 dark:bg-gray-700 rounded"></div>
                        <div class="h-14 bg-gray-200 dark:bg-gray-700 rounded"></div>
                        <div class="h-14 bg-gray-200 dark:bg-gray-700 rounded"></div>
                        <div class="h-14 bg-gray-200 dark:bg-gray-700 rounded"></div>
                    </div>
                </div>
            </div>
        </div>  
    </div>

    <!-- Go to Top Button -->
    <div class="text-center my-6">
        <button onclick="scrollToTop()" 
        class="go-top-btn px-4 py-2 bg-gray-200 dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-full hover:bg-gray-300 dark:hover:bg-gray-700 transition-all">
            ↑ Scroll to Top
        </button>
    </div>

    <footer class="bg-gray-100 dark:bg-gray-900 border-t dark:border-gray-700 mt-12">
        <div class="max-w-7xl mx-auto px-4 py-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-sm">
                <!-- Documentation Links -->
                <div class="space-y-3">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-200">Resources</h3>
                    <ul class="space-y-2 text-gray-600 dark:text-gray-400">
                        <li>                       
                            <a href="https://github.com/dheeraz101/lwm/blob/main/README.md" 
                            class="text-sm text-gray-600 dark:text-gray-400 hover:text-blue-600 transition-colors"
                            target="_blank">
                                📘 Documentation
                            </a>
                        </li>
                        <li>
                            <a href="https://github.com/dheeraz101/lwm/issues" 
                            class="text-sm text-gray-600 dark:text-gray-400 hover:text-blue-600 transition-colors"
                            target="_blank">
                                🐛 Report Issue
                            </a>
                        </li>
                        <li>
                            <a href="https://github.com/dheeraz101/lwm/blob/main/LICENSE" 
                            class="text-sm text-gray-600 dark:text-gray-400 hover:text-blue-600 transition-colors"
                            target="_blank">
                                ⚖️ License
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- System Info -->
                <div class="space-y-3">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-200">System Status</h3>
                    <div class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2">
                        <div class="relative flex items-center">
                            <!-- Status indicator -->
                            <div class="status-dot animate-pulse"></div>
                            <!-- Optional second pulse effect -->
                            <div class="status-ping animate-ping"></div>
                        </div>
                        <span>v1.0.0 · Operational</span>
                        <div class="flex gap-2">
                            <a href="https://github.com/dheeraz101/lwm" 
                            class="hover:text-blue-600 transition-colors"
                            target="_blank">
                                <img src="https://img.shields.io/github/stars/dheeraz101/lwm?style=social" alt="GitHub Stars">
                            </a>
                        </div>
                    </div>
                    <ul class="space-y-2 text-gray-600 dark:text-gray-400">
                        <li>🔒 Development Mode</li>
                    </ul>
                </div>

                <!-- About -->
                <div class="space-y-3">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-200">About</h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        A modern directory browser for local development environments. 
                        Built with PHP and Tailwind CSS.
                    </p>
                </div>
            </div>
            
            <!-- Copyright -->
            <div class="mt-8 pt-8 border-t dark:border-gray-700 text-center">
                <p class="text-gray-600 dark:text-gray-400 text-sm">
                    © <?php echo date('Y'); ?> LocalHost Web Manager · 
                    <a href="https://github.com/dheeraz101" class="hover:text-blue-600 dark:hover:text-blue-400">GitHub</a> · 
                    <?php echo $_SERVER['SERVER_SOFTWARE']; ?>
                </p>
            </div>
        </div>
    </footer>

    <script src="assets/script.js"></script>

</body>
</html>

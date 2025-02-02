/**
 * LocalServer Web Manager - [Open-source web interface for local server management]
 * 
 * @package     LocalServer-WebManager
 * @description Javascript code for the project
 * @version     1.0.0
 * @author      Dheeraz
 * @license     MIT
 * @link        https://github.com/dheeraz101/lwm
 */

document.addEventListener('DOMContentLoaded', () => {
    initializeApp();
});

let foldersData = [];
let sizeUnit = 'MB';
let sortState = { name: 'asc', date: 'asc', size: 'asc' };
let darkMode = localStorage.getItem('darkMode') === 'true';
let internetPopupShown = false;

// Initialization
function initializeApp() {
    setupEventListeners();
    applyStoredPreferences();
    startBackgroundTasks();
    fetchInitialData();
}

function setupEventListeners() {
    document.getElementById('dark-mode-toggle').addEventListener('click', toggleDarkMode);
    document.getElementById('search').addEventListener('input', debounce(filterFolders, 300));
}

function applyStoredPreferences() {
    document.documentElement.classList.toggle('dark', darkMode);
}

// Fixed background tasks
function startBackgroundTasks() {
    setInterval(() => {
        checkInternetConnection();
        checkMySQLStatus();
        updateDateTime();
        silentRefresh();
    }, 5000);
}

// Add to fetchInitialData()
async function fetchInitialData() {
    showLoading();
    await Promise.all([
        fetchFolders(),
        checkMySQLStatus(),
        checkInternetConnection(),
        fetchServerIP() // Add this line
    ]);
    hideLoading();
}

// Add new IP fetching function
async function fetchServerIP() {
    try {
        const response = await fetch('../api/getIP.php');
        const data = await response.json();
        document.getElementById('server-ip').textContent = data.ip;
    } catch (error) {
        console.error('Error fetching IP:', error);
        document.getElementById('server-ip').textContent = 'Unavailable';
    }
}

// Add these new functions for navigation
function openFolder(path) {
    if (!path) return;
    fetchFolders(path);
}

function showFolderInfo(path) {
    // Implement folder/file info modal
    alert(`Info for: ${path}`);
}


// Core Functions
let currentPath = './';
let isFetching = false;

async function fetchFolders(path = './') {
    if (isFetching) return;
    isFetching = true;
    
    if (currentPath !== path) {
        showLoading();
        currentPath = path;
    }

    try {
        const response = await fetch(`../api/getFolders.php?path=${encodeURIComponent(path)}`);
        const textResponse = await response.text();
        
        if (!response.ok) throw new Error('Network response was not ok');
        
        const data = JSON.parse(textResponse);
        
        if (!data?.success) {
            throw new Error(data.error || 'Invalid server response');
        }

        foldersData = data.data;
        displayFolders(data.data);
        updateStorageInfo(data.data);

    } catch (error) {
        showError(`Failed to load: ${error.message}`);
        console.error("Error details:", error);
        foldersData = [];
        displayFolders([]);
        updateStorageInfo([]);
    } finally {
        isFetching = false;
        hideLoading();
    }
}

// Fixed storage info update
function updateStorageInfo(folders) {
    const directories = folders.filter(f => f.type === 'directory');
    const files = folders.filter(f => f.type !== 'directory');
    const totalSize = folders.reduce((acc, f) => acc + (f.size || 0), 0);
    
    document.getElementById('total-storage').textContent = formatSize(totalSize);
    document.getElementById('total-folders').textContent = directories.length;
    document.getElementById('total-files').textContent = files.length;
    document.getElementById('free-storage').textContent = formatSize(totalSize * 0.35);
}

// Fixed silent refresh
async function silentRefresh() {
    try {
        const response = await fetch(`getFolders.php?path=${encodeURIComponent(currentPath)}`);
        const data = await response.json();
        if (data.success) {
            foldersData = data.data;
            updateStorageInfo(data.data);

            // Reapply filter after refresh
            const searchTerm = document.getElementById("search").value.toLowerCase();
            const filtered = foldersData.filter(folder => 
                folder.name?.toLowerCase().includes(searchTerm)
            );
            displayFolders(filtered);
        }
    } catch (error) {
        console.log('Background refresh failed:', error);
    }
}

// In displayFolders() function:
function displayFolders(items) {
    const tbody = document.getElementById('folder-table');
    const safeItems = Array.isArray(items) ? items : [];

    tbody.innerHTML = safeItems.map(item => {
        const pathParam = item.path ? 
            encodeURIComponent(item.path.replace(/\\/g, '/')) : 
            '';
        const urlPath = encodeURI(item.path.replace(/\\/g, '/'));
        const url = `http://localhost/${urlPath}`;
        const isDirectory = item.type === 'directory';

        return `
        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
            <td class="px-4 py-3" data-label="Name">
                ${isDirectory ? '📁' : '📄'}
                ${item.name}
            </td>
            <td class="px-4 py-3" data-label="Modified">${item.lastModified || 'N/A'}</td>
            <td class="px-4 py-3" data-label="Size">${formatSize(item.size)}</td>
            <td class="px-4 py-3" data-label="Actions">
                <div class="flex justify-end md:justify-start gap-2">
                    <button onclick="window.open('${url}', '_blank')" 
                            class="text-blue-600 hover:underline px-2 py-1">
                        Open
                    </button>
                    ${isDirectory ? `
                        <button onclick="fetchFolders('${pathParam}')" 
                                class="text-gray-600 dark:text-gray-400 hover:text-blue-600 px-2 py-1">
                            ℹ️ Info
                        </button>
                    ` : `
                        <button onclick="showFileInfo(decodeURIComponent('${encodeURIComponent(JSON.stringify(item))}'))" 
                                class="text-gray-600 dark:text-gray-400 hover:text-blue-600 px-2 py-1">
                            ℹ️ Info
                        </button>
                    `}
                </div>
            </td>
        </tr>
    `}).join('');
    updateStorageInfo(safeItems);
}

// Add new file info function
function showFileInfo(encodedItem) {
    try {
        const item = JSON.parse(decodeURIComponent(encodedItem));
        const message = `File Info:\n\nName: ${item.name}\nType: ${item.type}\nSize: ${formatSize(item.size)}\nModified: ${item.lastModified}\nExtension: ${item.extension || 'None'}`;
        alert(message);
    } catch (error) {
        console.error('Error showing file info:', error);
        alert('Could not display file info.');
    }
}

// Updated formatSize function
function formatSize(bytes) {
    if (sizeUnit === 'B') return `${bytes} B`;
    if (sizeUnit === 'KB') return `${(bytes / 1024).toFixed(2)} KB`;
    if (sizeUnit === 'MB') return `${(bytes / 1024 ** 2).toFixed(2)} MB`;
    if (sizeUnit === 'GB') return `${(bytes / 1024 ** 3).toFixed(2)} GB`;
    return bytes;
}

function debounce(func, timeout = 300) {
    let timer;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => func.apply(this, args), timeout);
    };
}

// UI Functions
function toggleDarkMode() {
    darkMode = !darkMode;
    document.documentElement.classList.toggle('dark', darkMode);
    localStorage.setItem('darkMode', darkMode);
}

function showLoading() {
    document.body.classList.add('loading');
    document.getElementById('loading-spinner').classList.remove('hidden');
}

function hideLoading() {
    document.body.classList.remove('loading');
    document.getElementById('loading-spinner').classList.add('hidden');
}

function showError(message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'fixed top-4 right-4 bg-red-100 border-red-400 text-red-700 px-4 py-3 rounded flex items-center';
    errorDiv.innerHTML = `
        <span>⚠️ ${message}</span>
        <button onclick="this.parentElement.remove()" class="ml-3 text-xl">&times;</button>
    `;
    document.body.appendChild(errorDiv);
}

async function fetchLogs(autoUpdate = false) {  // Default should be false for manual clicks
    try {
        const response = await fetch('../api/getLogs.php');
        if (!response.ok) throw new Error('Failed to fetch logs');
        const logs = await response.text();

        // Update log metadata
        const logEntries = logs.split('\n').filter(line => line.trim()).length;
        const now = new Date();
        document.getElementById('log-entries').textContent = logEntries;
        document.getElementById('last-log-update').textContent = 
            `${now.toLocaleDateString()} ${now.toLocaleTimeString()}`;

        // Show logs only when manually triggered
        if (!autoUpdate) {
            showLogsModal(logs);  // Ensure modal opens
        }
    } catch (error) {
        console.error('Failed to load logs:', error);
    }
}

// Auto-fetch logs every 10 seconds
setInterval(() => fetchLogs(true), 10000);


// Improved logs modal
// Update showLogsModal function
function showLogsModal(logs) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50 backdrop-blur-sm';
    modal.innerHTML = `
        <div class="modal-container bg-white dark:bg-gray-900 rounded-xl w-full max-w-4xl flex flex-col border dark:border-gray-800 shadow-2xl" 
             style="height: 90vh; max-height: 800px;">
            <div class="flex justify-between items-center p-4 border-b dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50">
                <h3 class="text-lg font-semibold dark:text-white">System Logs</h3>
                <button class="close-button p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    ✕
                </button>
            </div>
            
            <div class="flex-1 flex flex-col p-4 gap-2 overflow-hidden">
                <div class="bg-gray-100 dark:bg-gray-800 rounded-lg flex-1 overflow-hidden p-3">
                    <pre class="h-full overflow-auto text-sm font-mono dark:text-gray-300 leading-5 whitespace-pre-wrap">${logs}</pre>
                </div>
            </div>
        </div>
    `;

    // Add proper event listener for close button
    modal.querySelector('.close-button').addEventListener('click', () => {
        modal.remove();
    });

    document.body.appendChild(modal);
}

// Feature Functions
function sortTable(attribute) {
    const direction = sortState[attribute] === 'asc' ? 1 : -1;
    sortState[attribute] = sortState[attribute] === 'asc' ? 'desc' : 'asc';

    foldersData.sort((a, b) => {
        if (attribute === 'name') return a.name?.localeCompare(b.name) * direction;
        if (attribute === 'size') return (a.size - b.size) * direction;
        if (attribute === 'date') return (new Date(a.lastModified) - new Date(b.lastModified)) * direction;
        return 0;
    });
    displayFolders(foldersData);
}

function filterFolders() {
    const searchTerm = document.getElementById("search").value.toLowerCase();
    const filtered = foldersData.filter(folder => 
        folder.name?.toLowerCase().includes(searchTerm)
    );
    displayFolders(filtered);
}

// Track internet connection uptime
let internetConnectedAt = null;

// Function to check internet status
async function checkInternetConnection() {
    const internetLight = document.getElementById("internet-light");
    const statusText = document.getElementById("internet-status-text");
    const speedElement = document.getElementById("internet-speed");
    const qualityBar = document.getElementById("internet-quality-bar");
    const uptimeElement = document.getElementById("internet-uptime");

    try {
        const startTime = Date.now();
        // Use a random URL to prevent caching
        await fetch('https://upload.wikimedia.org/wikipedia/commons/3/3a/Cat03.jpg?t=' + Date.now(), { mode: 'no-cors' });
        const latency = Date.now() - startTime;

        // Simulated speed calculation
        const speed = Math.max(1, Math.round((100 * 1024 * 8) / latency));
        const validSpeed = Math.min(speed, 1000); // Limit max to 1000 Mbps

        // Update UI
        internetLight.className = 'status-dot bg-green-500 animate-pulse';
        statusText.textContent = 'Connected';
        speedElement.textContent = `${validSpeed} Mbps`;
        
        qualityBar.style.width = `${Math.min(validSpeed / 10, 100)}%`;
        qualityBar.className = `progress-fill h-2 rounded-full transition-all duration-500 ${
            validSpeed > 500 ? 'bg-green-500' : 
            validSpeed > 200 ? 'bg-yellow-500' : 'bg-red-500'
        }`;

        // Update uptime
        if (!internetConnectedAt) internetConnectedAt = Date.now();
        const uptimeSeconds = Math.floor((Date.now() - internetConnectedAt) / 1000);
        uptimeElement.textContent = `${Math.floor(uptimeSeconds / 60)}m ${uptimeSeconds % 60}s`;

    } catch (error) {
        // Handle disconnection
        internetConnectedAt = null;
        internetLight.className = 'status-dot bg-red-500';
        statusText.textContent = 'Disconnected';
        speedElement.textContent = '0 Mbps';
        qualityBar.style.width = '0%';
        uptimeElement.textContent = '0s';
    }
}

// Run every 5 seconds
setInterval(checkInternetConnection, 5000);
document.addEventListener("DOMContentLoaded", checkInternetConnection);


async function checkMySQLStatus() {
    try {
        const response = await fetch('../api/checkMysqlStatus.php');
        const data = await response.json();
        
        const statusDot = document.querySelector('#mysql-status-text .status-dot');
        const progressFill = document.querySelector('#mysql-status-text .progress-fill');
        const statusText = document.querySelector('#mysql-status-text .status-text');
        const versionElement = document.getElementById('mysql-version');
        const uptimeElement = document.getElementById('mysql-uptime');

        if (data.status === 'Running') {
            statusDot.classList.remove('bg-red-500', 'bg-gray-500');
            statusDot.classList.add('bg-green-500', 'animate-pulse');
            statusText.textContent = 'Operational';
            progressFill.style.width = '100%';
            progressFill.classList.remove('bg-red-500');
            progressFill.classList.add('bg-blue-500');
        } else {
            statusDot.classList.remove('bg-green-500', 'animate-pulse');
            statusDot.classList.add('bg-red-500');
            statusText.textContent = 'Offline';
            progressFill.style.width = '30%';
            progressFill.classList.remove('bg-blue-500');
            progressFill.classList.add('bg-red-500');
        }

        versionElement.textContent = data.version || 'Unknown';
        uptimeElement.textContent = data.uptime || '0s';

    } catch (error) {
        console.error("MySQL check error:", error);
        document.querySelector('#mysql-status-text .status-dot').className = 'status-dot bg-gray-500';
        document.querySelector('#mysql-status-text .status-text').textContent = 'Error';
    }
}


function updateDateTime() {
    document.getElementById('current-date-time').textContent = 
        `Date & Time: ${new Date().toLocaleString()}`;
}

function convertSizes() {
    sizeUnit = document.getElementById("size-unit").value;
    displayFolders(foldersData);
}

function clearSearch() {
    document.getElementById("search").value = '';
    filterFolders();
}

// Update loading functions
function showLoading() {
    document.getElementById('skeleton-loading').classList.remove('hidden');
    document.getElementById('folder-table').classList.add('hidden');
    document.querySelector('footer').classList.add('hidden');
}

function hideLoading() {
    document.getElementById('skeleton-loading').classList.add('hidden');
    document.getElementById('folder-table').classList.remove('hidden');
    document.querySelector('footer').classList.remove('hidden');
}

// Improved dark mode toggle
function toggleDarkMode() {
    let darkMode = document.documentElement.classList.toggle('dark');
    localStorage.setItem('darkMode', darkMode);

    // Update footer colors
    const footer = document.querySelector('footer');
    if (footer) {
        footer.classList.toggle('dark:bg-gray-900', darkMode);
        footer.classList.toggle('dark:border-gray-700', darkMode);
    }

    // Manually update icon visibility
    document.querySelector('#dark-mode-toggle span:nth-child(1)').classList.toggle('hidden', darkMode);
    document.querySelector('#dark-mode-toggle span:nth-child(2)').classList.toggle('hidden', !darkMode);
}

// Add scroll to top function
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Add scroll event listener to show/hide button
window.addEventListener('scroll', () => {
    const btn = document.querySelector('.go-top-btn');
    if (window.scrollY > 300) {
        btn.classList.remove('opacity-0', 'invisible');
        btn.classList.add('opacity-80', 'visible');
    } else {
        btn.classList.add('opacity-0', 'invisible');
        btn.classList.remove('opacity-80', 'visible');
    }
});
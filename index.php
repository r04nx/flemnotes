<?php
require_once 'config.php';
require_once 'database.php';
require_once 'names.php';

// Initialize database
Database::getInstance();

// Get route from URL
$route = $_GET['route'] ?? 'home';

// Validate route (alphanumeric only)
if (!preg_match('/^[a-zA-Z0-9]+$/', $route)) {
    $route = 'home';
}

// Show welcome page for home route
if ($route === 'home' || $route === '' || $route === 'index') {
    require_once 'welcome.php';
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FlemNotes - <?php echo htmlspecialchars($route); ?></title>
    <link rel="icon" type="image/svg+xml" href="favicon.svg">
    <link rel="stylesheet" href="styles.css">
    <!-- Marked.js for Markdown parsing -->
    <script src="https://cdn.jsdelivr.net/npm/marked@11.1.0/marked.min.js"></script>
</head>
<body>
    <div class="app-container">
        <!-- Header -->
        <header class="header">
            <div class="header-left">
                <div class="logo">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M14 2V8H20" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="logo-text">FlemNotes</span>
                </div>
                <div class="route-name">/<?php echo htmlspecialchars($route); ?></div>
            </div>
            <div class="header-right">
                <button class="btn-icon" id="previewBtn" title="Preview">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                    Preview
                </button>
                <button class="btn-icon" id="shareBtn" title="Share">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 12v8a2 2 0 002 2h12a2 2 0 002-2v-8"/>
                        <polyline points="16 6 12 2 8 6"/>
                        <line x1="12" y1="2" x2="12" y2="15"/>
                    </svg>
                    Share
                </button>
                <button class="btn-secondary" id="saveBtn">
                    <span class="save-status">Saved</span>
                </button>
            </div>
        </header>

        <!-- Main Content -->
        <div class="main-content">
            <div class="editor-section">
                <textarea id="noteEditor" placeholder="Start writing your note...

You can use markdown:
# Heading 1
## Heading 2
**bold text**
*italic text*
`code`

Your note will auto-save as you type!"></textarea>

                <!-- Preview Container -->
                <div id="previewContainer" class="preview-container" style="display: none;">
                    <div class="preview-header">
                        <h3>Preview</h3>
                        <button class="btn-close-preview" id="closePreviewBtn">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                            Close Preview
                        </button>
                    </div>
                    <div id="previewContent" class="preview-content"></div>
                </div>

                <!-- File Upload Area -->
                <div class="upload-area" id="uploadArea">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M17 8l-5-5-5 5M12 3v12"/>
                    </svg>
                    <h3>Drop files here or click to upload</h3>
                    <p>Supports images, documents, and more. You can also paste directly from clipboard.</p>
                    <input type="file" id="fileInput" multiple hidden>
                </div>

                <!-- Uploaded Files List -->
                <div class="files-list" id="filesList"></div>
            </div>

            <!-- Sidebar -->
            <aside class="sidebar">
                <div class="sidebar-section">
                    <h3>Note Settings</h3>
                    <div class="settings-buttons">
                        <button class="setting-btn active" id="publicBtn">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/>
                            </svg>
                            Public
                        </button>
                        <button class="setting-btn" id="privateBtn">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0110 0v4"/>
                            </svg>
                            Private
                        </button>
                    </div>
                </div>

                <div class="sidebar-section">
                    <h3>Password Protection</h3>
                    <input type="password" id="passwordInput" class="input-field" placeholder="Optional password">
                    <p class="password-hint" style="font-size: 0.75rem; color: #6b7280; margin-top: 0.5rem;">
                        💡 Tip: Switching to "Public" will remove password protection
                    </p>
                </div>

                <div class="sidebar-section">
                    <h3>Statistics</h3>
                    <div class="stat-item">
                        <span>Characters:</span>
                        <span id="charCount">0</span>
                    </div>
                    <div class="stat-item">
                        <span>Words:</span>
                        <span id="wordCount">0</span>
                    </div>
                    <div class="stat-item">
                        <span>Files:</span>
                        <span id="fileCount">0</span>
                    </div>
                </div>

                <div class="sidebar-section">
                    <h3>Features</h3>
                    <div class="feature-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        Auto-save enabled
                    </div>
                    <div class="feature-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        Markdown support
                    </div>
                    <div class="feature-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        File uploads
                    </div>
                    <div class="feature-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        Real-time sync (3s)
                    </div>
                    <div class="feature-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        SEO friendly
                    </div>
                </div>

                <div class="sidebar-footer">
                    <p>Powered by</p>
                    <a href="#" class="backend-link">FlemNotes Backend</a>
                </div>
            </aside>
        </div>
    </div>

    <!-- Password Modal -->
    <div class="modal" id="passwordModal">
        <div class="modal-content">
            <h2>Password Protected Note</h2>
            <p>This note is password protected. Please enter the password to access it.</p>
            <input type="password" id="modalPasswordInput" class="input-field" placeholder="Enter password">
            <div class="modal-error" id="modalError"></div>
            <div class="modal-buttons">
                <button class="btn-secondary" id="cancelPasswordBtn">Cancel</button>
                <button class="btn-primary" id="submitPasswordBtn">Unlock</button>
            </div>
        </div>
    </div>

    <script>
        const ROUTE = '<?php echo htmlspecialchars($route); ?>';
    </script>
    <script src="app.js"></script>
</body>
</html>

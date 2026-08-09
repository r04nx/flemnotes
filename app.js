// Application state
let currentPassword = '';
let isPublic = true;
let autoSaveTimeout = null;
let lastSavedContent = '';
let isPreviewMode = false;

// DOM Elements
const noteEditor = document.getElementById('noteEditor');
const saveBtn = document.getElementById('saveBtn');
const saveStatus = saveBtn.querySelector('.save-status');
const publicBtn = document.getElementById('publicBtn');
const privateBtn = document.getElementById('privateBtn');
const passwordInput = document.getElementById('passwordInput');
const charCount = document.getElementById('charCount');
const wordCount = document.getElementById('wordCount');
const fileCount = document.getElementById('fileCount');
const uploadArea = document.getElementById('uploadArea');
const fileInput = document.getElementById('fileInput');
const filesList = document.getElementById('filesList');
const passwordModal = document.getElementById('passwordModal');
const modalPasswordInput = document.getElementById('modalPasswordInput');
const submitPasswordBtn = document.getElementById('submitPasswordBtn');
const cancelPasswordBtn = document.getElementById('cancelPasswordBtn');
const modalError = document.getElementById('modalError');
const shareBtn = document.getElementById('shareBtn');
const previewBtn = document.getElementById('previewBtn');
const previewContainer = document.getElementById('previewContainer');
const previewContent = document.getElementById('previewContent');
const closePreviewBtn = document.getElementById('closePreviewBtn');

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    loadNote();
    setupEventListeners();
});

// Setup event listeners
function setupEventListeners() {
    // Editor auto-save
    noteEditor.addEventListener('input', () => {
        updateStatistics();
        triggerAutoSave();
    });

    // Public/Private toggle
    publicBtn.addEventListener('click', () => {
        isPublic = true;
        publicBtn.classList.add('active');
        privateBtn.classList.remove('active');
        
        // Clear password field when switching to public
        passwordInput.value = '';
        
        triggerAutoSave();
    });

    privateBtn.addEventListener('click', () => {
        isPublic = false;
        privateBtn.classList.add('active');
        publicBtn.classList.remove('active');
        triggerAutoSave();
    });

    // Password input
    passwordInput.addEventListener('change', () => {
        triggerAutoSave();
    });

    // File upload
    uploadArea.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', handleFileSelect);

    // Drag and drop
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });

    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('dragover');
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        handleFiles(e.dataTransfer.files);
    });

    // Paste files
    document.addEventListener('paste', (e) => {
        const items = e.clipboardData.items;
        for (let item of items) {
            if (item.kind === 'file') {
                const file = item.getAsFile();
                handleFiles([file]);
            }
        }
    });

    // Password modal
    submitPasswordBtn.addEventListener('click', submitPassword);
    cancelPasswordBtn.addEventListener('click', () => {
        passwordModal.classList.remove('active');
        window.location.href = '/';
    });

    modalPasswordInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            submitPassword();
        }
    });

    // Share button
    shareBtn.addEventListener('click', () => {
        const url = window.location.href;
        if (navigator.clipboard) {
            navigator.clipboard.writeText(url).then(() => {
                alert('Link copied to clipboard!');
            });
        } else {
            prompt('Copy this link:', url);
        }
    });

    // Preview button
    previewBtn.addEventListener('click', () => {
        togglePreview();
    });

    // Close preview button
    closePreviewBtn.addEventListener('click', () => {
        togglePreview();
    });

    // Manual save
    saveBtn.addEventListener('click', () => {
        saveNote();
    });
}

// Toggle preview mode
function togglePreview() {
    isPreviewMode = !isPreviewMode;
    
    if (isPreviewMode) {
        // Show preview, hide editor
        noteEditor.style.display = 'none';
        previewContainer.style.display = 'block';
        
        // Render markdown
        const markdown = noteEditor.value;
        if (markdown.trim()) {
            previewContent.innerHTML = marked.parse(markdown);
        } else {
            previewContent.innerHTML = '<p style="color: #9ca3af; text-align: center; padding: 2rem;">Nothing to preview yet. Start writing in the editor!</p>';
        }
        
        // Update button text
        previewBtn.innerHTML = `
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
            Edit
        `;
    } else {
        // Show editor, hide preview
        noteEditor.style.display = 'block';
        previewContainer.style.display = 'none';
        
        // Restore button text
        previewBtn.innerHTML = `
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                <circle cx="12" cy="12" r="3"/>
            </svg>
            Preview
        `;
    }
}

// Load note from server
async function loadNote() {
    try {
        const response = await fetch(`api.php?action=get&route=${ROUTE}`);
        const result = await response.json();

        if (result.success) {
            const data = result.data;

            if (data.requires_password) {
                showPasswordModal();
                return;
            }

            if (data.exists) {
                noteEditor.value = data.content || '';
                lastSavedContent = data.content || '';
                isPublic = data.is_public === 1;

                if (isPublic) {
                    publicBtn.classList.add('active');
                    privateBtn.classList.remove('active');
                } else {
                    privateBtn.classList.add('active');
                    publicBtn.classList.remove('active');
                }

                if (data.files && data.files.length > 0) {
                    renderFiles(data.files);
                }

                updateStatistics();
            }
        }
    } catch (error) {
        console.error('Error loading note:', error);
    }
}

// Show password modal
function showPasswordModal() {
    passwordModal.classList.add('active');
    modalPasswordInput.focus();
}

// Submit password
async function submitPassword() {
    const password = modalPasswordInput.value;

    if (!password) {
        showModalError('Please enter a password');
        return;
    }

    try {
        const response = await fetch(`api.php?action=get&route=${ROUTE}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `password=${encodeURIComponent(password)}`
        });

        const result = await response.json();

        if (result.success) {
            currentPassword = password;
            passwordModal.classList.remove('active');
            modalPasswordInput.value = '';
            
            const data = result.data;
            noteEditor.value = data.content || '';
            lastSavedContent = data.content || '';
            isPublic = data.is_public === 1;

            if (isPublic) {
                publicBtn.classList.add('active');
                privateBtn.classList.remove('active');
            } else {
                privateBtn.classList.add('active');
                publicBtn.classList.remove('active');
            }

            if (data.files && data.files.length > 0) {
                renderFiles(data.files);
            }

            updateStatistics();
        } else {
            showModalError('Invalid password');
        }
    } catch (error) {
        showModalError('Error verifying password');
        console.error('Error:', error);
    }
}

// Show modal error
function showModalError(message) {
    modalError.textContent = message;
    modalError.classList.add('active');
    setTimeout(() => {
        modalError.classList.remove('active');
    }, 3000);
}

// Trigger auto-save
function triggerAutoSave() {
    clearTimeout(autoSaveTimeout);
    setSaveStatus('saving');
    
    autoSaveTimeout = setTimeout(() => {
        saveNote();
    }, 2000); // Auto-save after 2 seconds of inactivity
}

// Save note
async function saveNote() {
    const content = noteEditor.value;
    
    const data = {
        route: ROUTE,
        content: content,
        is_public: isPublic ? 1 : 0,
        current_password: currentPassword
    };

    // Add password if provided
    if (passwordInput.value) {
        data.password = passwordInput.value;
    }
    
    // If switching to public and there's a current password, remove it
    if (isPublic && currentPassword && !passwordInput.value) {
        data.remove_password = true;
    }

    try {
        const response = await fetch('api.php?action=save', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            lastSavedContent = content;
            setSaveStatus('saved');
            
            // Update current password state
            if (passwordInput.value) {
                currentPassword = passwordInput.value;
                passwordInput.value = '';
            } else if (data.remove_password) {
                // Password was removed
                currentPassword = '';
            }
        } else {
            setSaveStatus('error');
            if (result.error === 'Invalid password') {
                showPasswordModal();
            }
        }
    } catch (error) {
        console.error('Error saving note:', error);
        setSaveStatus('error');
    }
}

// Set save status
function setSaveStatus(status) {
    saveStatus.className = 'save-status ' + status;
    
    switch (status) {
        case 'saving':
            saveStatus.textContent = 'Saving...';
            break;
        case 'saved':
            saveStatus.textContent = 'Saved';
            break;
        case 'error':
            saveStatus.textContent = 'Error';
            break;
    }
}

// Update statistics
function updateStatistics() {
    const content = noteEditor.value;
    charCount.textContent = content.length;
    wordCount.textContent = content.trim() ? content.trim().split(/\s+/).length : 0;
}

// Handle file select
function handleFileSelect(e) {
    handleFiles(e.target.files);
}

// Handle files
async function handleFiles(files) {
    for (let file of files) {
        await uploadFile(file);
    }
    fileInput.value = ''; // Reset input
}

// Upload file
async function uploadFile(file) {
    // Check file extension
    const extension = file.name.split('.').pop().toLowerCase();
    const blockedExtensions = ['php', 'phtml', 'php3', 'php4', 'php5', 'pht', 'phps'];
    
    if (blockedExtensions.includes(extension)) {
        alert('PHP files are not allowed for security reasons');
        return;
    }

    const formData = new FormData();
    formData.append('file', file);
    formData.append('route', ROUTE);
    formData.append('password', currentPassword);

    try {
        const response = await fetch('api.php?action=upload', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            addFileToList(result.data.file);
            updateFileCount();
        } else {
            alert('Error uploading file: ' + result.error);
            if (result.error === 'Invalid password') {
                showPasswordModal();
            }
        }
    } catch (error) {
        console.error('Error uploading file:', error);
        alert('Error uploading file');
    }
}

// Render files
function renderFiles(files) {
    filesList.innerHTML = '';
    files.forEach(file => addFileToList(file));
    updateFileCount();
}

// Add file to list
function addFileToList(file) {
    const fileItem = document.createElement('div');
    fileItem.className = 'file-item';
    fileItem.dataset.fileId = file.id;

    const fileSize = formatFileSize(file.file_size);
    const fileIcon = getFileIcon(file.original_name);

    fileItem.innerHTML = `
        <div class="file-info">
            <div class="file-icon">${fileIcon}</div>
            <div class="file-details">
                <div class="file-name">${escapeHtml(file.original_name)}</div>
                <div class="file-meta">${fileSize}</div>
            </div>
        </div>
        <div class="file-actions">
            <a href="uploads/${file.stored_name}" target="_blank" class="btn-file">View</a>
            <a href="uploads/${file.stored_name}" download="${file.original_name}" class="btn-file">Download</a>
            <button class="btn-file delete" onclick="deleteFile(${file.id})">Delete</button>
        </div>
    `;

    filesList.appendChild(fileItem);
}

// Delete file
async function deleteFile(fileId) {
    if (!confirm('Are you sure you want to delete this file?')) {
        return;
    }

    try {
        const response = await fetch('api.php?action=delete-file', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                file_id: fileId,
                password: currentPassword
            })
        });

        const result = await response.json();

        if (result.success) {
            const fileItem = document.querySelector(`[data-file-id="${fileId}"]`);
            if (fileItem) {
                fileItem.remove();
                updateFileCount();
            }
        } else {
            alert('Error deleting file: ' + result.error);
            if (result.error === 'Invalid password') {
                showPasswordModal();
            }
        }
    } catch (error) {
        console.error('Error deleting file:', error);
        alert('Error deleting file');
    }
}

// Update file count
function updateFileCount() {
    const count = filesList.children.length;
    fileCount.textContent = count;
}

// Format file size
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}

// Get file icon
function getFileIcon(filename) {
    const extension = filename.split('.').pop().toLowerCase();
    const imageExts = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'];
    const docExts = ['pdf', 'doc', 'docx', 'txt'];
    const codeExts = ['js', 'html', 'css', 'json', 'xml'];

    if (imageExts.includes(extension)) {
        return '🖼️';
    } else if (docExts.includes(extension)) {
        return '📄';
    } else if (codeExts.includes(extension)) {
        return '💻';
    } else {
        return '📎';
    }
}

// Escape HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

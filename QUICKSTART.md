# Quick Start Guide

## Running the Application

### Option 1: PHP Built-in Server (Development)

1. Navigate to the project directory:
   ```bash
   cd /home/rohan/Public/flemnotes
   ```

2. Start the PHP development server with increased upload limits:
   ```bash
   php -d upload_max_filesize=50M -d post_max_size=50M -d memory_limit=256M -S localhost:8082 router.php
   ```

3. Open your browser and visit:
   ```
   http://localhost:8082/mynote
   ```

### Option 2: Apache/Nginx (Production)

1. Copy the project to your web server directory:
   ```bash
   sudo cp -r /home/rohan/Public/flemnotes /var/www/html/
   ```

2. Set proper permissions:
   ```bash
   sudo chmod 755 /var/www/html/flemnotes
   sudo chmod 777 /var/www/html/flemnotes/data
   sudo chmod 777 /var/www/html/flemnotes/uploads
   ```

3. Enable Apache mod_rewrite:
   ```bash
   sudo a2enmod rewrite
   sudo systemctl restart apache2
   ```

4. Visit:
   ```
   http://localhost/flemnotes/mynote
   ```

## Using the Application

### Creating Notes

1. Navigate to any alphanumeric route:
   - `http://localhost:8082/mynote`
   - `http://localhost:8082/project123`
   - `http://localhost:8082/todo`

2. Start typing in the text area
3. Note auto-saves after 2 seconds

### Markdown Preview

1. Write your note using markdown syntax
2. Click the **Preview** button in the header
3. See your rendered markdown with beautiful styling
4. Click **Edit** to return to the editor

Supported markdown:
- Headings: `# H1`, `## H2`, `### H3`
- Bold: `**bold text**`
- Italic: `*italic text*`
- Code: `` `inline code` ``
- Code blocks: ` ```language ... ``` `
- Lists, links, images, tables, and more!

### Uploading Files

1. **Drag & Drop**: Drag files onto the upload area
2. **Click to Upload**: Click the upload area to browse files
3. **Paste**: Copy an image and paste it (Ctrl+V)

### Password Protection

1. Enter a password in the "Password Protection" field
2. The note will auto-save with the password
3. Next time anyone accesses this route, they'll need the password

### Public/Private Toggle

- Click "Public" or "Private" buttons to change visibility
- Note auto-saves the setting

## Features

✅ **Auto-save** - Saves automatically after 2 seconds of inactivity
✅ **Markdown Support** - Use markdown syntax in your notes
✅ **File Uploads** - Upload any file type except PHP
✅ **Password Protection** - Secure your notes with a password
✅ **Real-time Statistics** - See character, word, and file counts
✅ **Unique URLs** - Each route is a unique note
✅ **Secure** - PHP files blocked, passwords hashed

## Security Notes

- PHP files are automatically blocked from upload
- Passwords are hashed using bcrypt
- Database files are protected via .htaccess
- All file uploads get unique hash suffixes

## Troubleshooting

### Port Already in Use
If port 8082 is in use, try another port:
```bash
php -S localhost:8083
```

### Permission Denied
Ensure data/ and uploads/ directories are writable:
```bash
chmod 777 data uploads
```

### .htaccess Not Working
Enable mod_rewrite in Apache:
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

## Example Routes

Try these example routes:
- `/home` - Default home note
- `/mynote` - Your personal note
- `/project123` - Project notes
- `/todo` - Todo list
- `/meeting20241111` - Meeting notes with date

## Tips

1. **Share Notes**: Click the "Share" button to copy the URL
2. **Markdown**: Use markdown for formatting (# headings, **bold**, *italic*)
3. **Quick Access**: Bookmark your favorite routes
4. **Password Reset**: To change password, enter a new one and save
5. **File Management**: View, download, or delete uploaded files

Enjoy using FlemNotes! 🎉

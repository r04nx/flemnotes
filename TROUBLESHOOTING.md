# Troubleshooting Guide

## File Upload Issues

### Error: "File is too large. Maximum size is 50MB"

**Problem**: The file you're trying to upload exceeds the 50MB limit.

**Solutions**:
1. Compress the file before uploading
2. Split large files into smaller parts
3. Increase the limit in `config.php`:
   ```php
   define('MAX_FILE_SIZE', 100 * 1024 * 1024); // 100MB
   ```

### Error: "POST Content-Length exceeds the limit"

**Problem**: PHP's upload limits are too low.

**Solution for PHP Built-in Server**:
Restart the server with increased limits:
```bash
php -d upload_max_filesize=50M -d post_max_size=50M -d memory_limit=256M -S localhost:8082 router.php
```

**Solution for Apache**:
Create or edit `.htaccess`:
```apache
php_value upload_max_filesize 50M
php_value post_max_size 50M
php_value memory_limit 256M
php_value max_execution_time 300
```

Or edit `php.ini`:
```ini
upload_max_filesize = 50M
post_max_size = 50M
memory_limit = 256M
max_execution_time = 300
```

### Error: "File upload error" or "Failed to write file to disk"

**Problem**: Permission issues or disk space.

**Solutions**:
1. Check uploads directory permissions:
   ```bash
   chmod 777 uploads/
   ```

2. Check disk space:
   ```bash
   df -h
   ```

3. Check PHP temp directory:
   ```bash
   php -i | grep upload_tmp_dir
   ```

### Error: "File type not allowed for security reasons"

**Problem**: Trying to upload a PHP file.

**Solution**: PHP files are blocked for security. Use other formats like:
- Documents: PDF, DOC, DOCX, TXT
- Images: JPG, PNG, GIF, SVG
- Archives: ZIP, RAR, TAR
- Code: JS, CSS, JSON, XML (but not PHP)

## Database Issues

### Error: "Database connection failed"

**Problem**: SQLite database cannot be created or accessed.

**Solutions**:
1. Check data directory permissions:
   ```bash
   chmod 777 data/
   ```

2. Ensure SQLite3 extension is installed:
   ```bash
   php -m | grep sqlite
   ```

3. Install if missing:
   ```bash
   # Ubuntu/Debian
   sudo apt-get install php-sqlite3
   
   # CentOS/RHEL
   sudo yum install php-sqlite3
   ```

## Routing Issues

### Error: "404 Not Found" for routes

**Problem**: URL rewriting not working.

**Solution for PHP Built-in Server**:
Always use `router.php`:
```bash
php -S localhost:8082 router.php
```

**Solution for Apache**:
1. Enable mod_rewrite:
   ```bash
   sudo a2enmod rewrite
   sudo systemctl restart apache2
   ```

2. Check `.htaccess` exists and is readable

3. Ensure `AllowOverride All` in Apache config:
   ```apache
   <Directory /var/www/html/flemnotes>
       AllowOverride All
   </Directory>
   ```

### Welcome page not showing

**Problem**: Visiting `/` or `/home` doesn't show welcome page.

**Solutions**:
1. Clear browser cache
2. Check that `welcome.php` exists
3. Check that `names.php` exists
4. Restart the server

## Performance Issues

### Slow file uploads

**Solutions**:
1. Increase timeout limits:
   ```bash
   php -d max_execution_time=600 -S localhost:8082 router.php
   ```

2. Check network connection
3. Use smaller files or compress them

### Auto-save too slow

**Problem**: Notes take too long to save.

**Solutions**:
1. Check database file permissions
2. Reduce auto-save delay in `app.js`:
   ```javascript
   autoSaveTimeout = setTimeout(() => {
       saveNote();
   }, 1000); // Change from 2000 to 1000 (1 second)
   ```

## Password Issues

### Forgot note password

**Problem**: Cannot access password-protected note.

**Solution**: 
Unfortunately, passwords are hashed and cannot be recovered. You'll need to:
1. Access the database directly:
   ```bash
   sqlite3 data/flemnotes.db
   ```

2. Remove the password:
   ```sql
   UPDATE notes SET password_hash = NULL WHERE route = 'yourroute';
   ```

### Password not working

**Solutions**:
1. Check for typos (passwords are case-sensitive)
2. Clear browser cache and cookies
3. Try in incognito/private mode

## Preview Issues

### Markdown not rendering

**Problem**: Preview shows plain text instead of formatted markdown.

**Solutions**:
1. Check internet connection (Marked.js loads from CDN)
2. Check browser console for errors
3. Try refreshing the page

### Preview button not working

**Solutions**:
1. Check browser console for JavaScript errors
2. Ensure `app.js` is loaded
3. Clear browser cache

## General Issues

### Changes not appearing

**Solutions**:
1. Hard refresh: Ctrl+F5 (Windows/Linux) or Cmd+Shift+R (Mac)
2. Clear browser cache
3. Check browser console for errors
4. Restart the PHP server

### Server won't start

**Problem**: Port already in use.

**Solution**: Use a different port:
```bash
php -S localhost:8083 router.php
```

Or kill the existing process:
```bash
pkill -f "php -S"
```

## Getting Help

If you're still experiencing issues:

1. Check the browser console (F12) for JavaScript errors
2. Check the PHP server output for errors
3. Enable error reporting in `config.php`:
   ```php
   error_reporting(E_ALL);
   ini_set('display_errors', 1);
   ```

4. Check file permissions:
   ```bash
   ls -la
   ```

5. Verify PHP version:
   ```bash
   php --version
   ```
   (Requires PHP 7.4 or higher)

## Common Error Codes

- **400 Bad Request**: Usually file upload or validation error
- **403 Forbidden**: Permission denied or wrong password
- **404 Not Found**: Route doesn't exist or rewrite not working
- **500 Internal Server Error**: PHP error, check server logs

## Logs

### PHP Built-in Server
Logs appear in the terminal where you started the server.

### Apache
```bash
tail -f /var/log/apache2/error.log
```

### Check PHP errors
Add to `config.php`:
```php
error_log("Debug message", 0);
```

Check error log location:
```bash
php -i | grep error_log
```

# Password Management Guide

## Setting a Password

1. Open your note (e.g., `/mynote`)
2. In the right sidebar, find "Password Protection"
3. Enter your desired password in the "Optional password" field
4. The note will auto-save with the password
5. Next time anyone accesses this route, they'll need the password

## Accessing a Password-Protected Note

1. Navigate to the note's route (e.g., `/mynote`)
2. A modal will appear asking for the password
3. Enter the correct password
4. Click "Unlock"
5. You can now view and edit the note=7
Y## Changing a Password

1. Access the note with the current password
2. Enter a new password in the "Password Protection" field
3. The note will auto-save with the new password

## Removing a Password

### Method 1: Via Web Interface
1. Access the note with the current password
2. Clear the "Password Protection" field (leave it empty)
3. Save the note
4. The password will be removed

### Method 2: Via Command Line
If you forgot the password, use the reset script:

```bash
cd /home/rohan/Public/flemnotes
php reset_password.php <route>
```

Example:
```bash
php reset_password.php mynote
```

This will remove the password protection from the note.

## Resetting a Forgotten Password

If you forgot your note's password, you can reset it using the command line:

```bash
php reset_password.php <route> <new_password>
```

Example:
```bash
php reset_password.php mynote newpassword123
```

This will set a new password for the note.

## Troubleshooting

### "Invalid password" error even with correct password

**Possible causes:**
1. Password was changed and you're using the old one
2. Typo in the password (passwords are case-sensitive)
3. Browser cached an old password

**Solutions:**
1. Try in incognito/private mode
2. Clear browser cache and cookies
3. Reset the password using the command line tool
4. Check the debug logs in the PHP server output

### Password not saving

**Possible causes:**
1. Auto-save didn't trigger
2. Network error
3. Permission issues

**Solutions:**
1. Click the "Save" button manually
2. Check browser console for errors
3. Check PHP server logs
4. Verify database file permissions:
   ```bash
   chmod 777 data/
   chmod 666 data/flemnotes.db
   ```

### Can't access note at all

If you're completely locked out:

```bash
# Remove password completely
php reset_password.php mynote

# Or set a known password
php reset_password.php mynote test123
```

## Security Notes

1. **Passwords are hashed**: Passwords are stored using bcrypt hashing, making them secure
2. **Cannot be recovered**: If you forget a password, it cannot be recovered (only reset)
3. **Case-sensitive**: Passwords are case-sensitive
4. **No length limit**: You can use passwords of any length
5. **Special characters**: All characters are supported

## Command Line Tools

### reset_password.php

**Remove password:**
```bash
php reset_password.php <route>
```

**Set new password:**
```bash
php reset_password.php <route> <password>
```

### debug.php

Check which notes have passwords:
```bash
php debug.php
```

This will show all notes and their password status.

## Examples

### Example 1: Protect a new note
1. Go to `/secretnote`
2. Write your content
3. Enter password: `mySecretPass123`
4. Note auto-saves with password

### Example 2: Access protected note
1. Go to `/secretnote`
2. Modal appears
3. Enter: `mySecretPass123`
4. Click "Unlock"
5. You can now edit the note

### Example 3: Forgot password
```bash
php reset_password.php secretnote newpass456
```
Now you can access with password: `newpass456`

### Example 4: Remove protection
```bash
php reset_password.php secretnote
```
Note is now public (no password required)

## Best Practices

1. **Use strong passwords** for sensitive notes
2. **Remember your passwords** - they cannot be recovered
3. **Don't share passwords** in public channels
4. **Use different passwords** for different notes
5. **Keep the reset script secure** - it bypasses password protection

## API Details

### Password in API Requests

When accessing a password-protected note via API:

**GET request:**
```javascript
fetch('api.php?action=get&route=mynote', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: `password=${encodeURIComponent(password)}`
})
```

**SAVE request:**
```javascript
fetch('api.php?action=save', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        route: 'mynote',
        content: 'note content',
        password: 'newpassword',  // Optional: set new password
        current_password: 'oldpassword'  // Required if note already has password
    })
})
```

## Database Schema

Passwords are stored in the `notes` table:

```sql
CREATE TABLE notes (
    id INTEGER PRIMARY KEY,
    route TEXT UNIQUE,
    content TEXT,
    password_hash TEXT,  -- bcrypt hash or NULL
    is_public INTEGER,
    created_at INTEGER,
    updated_at INTEGER
);
```

The `password_hash` field contains:
- `NULL` - No password protection
- `$2y$10$...` - Bcrypt hashed password

## Support

If you're still having issues:
1. Check `TROUBLESHOOTING.md`
2. Run `php debug.php` to see note status
3. Check PHP server logs
4. Use `reset_password.php` to reset passwords

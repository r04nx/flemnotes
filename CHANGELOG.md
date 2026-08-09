# Changelog

## Version 1.2.2 - Password & Public/Private Fix

### Fixed
- **Public Mode Now Removes Password** - Switching to "Public" automatically removes password protection
- **Password State Management** - Properly clears password when switching modes
- **Better UX** - Added hint that public mode removes password protection

### Changed
- `api.php` - Added `remove_password` parameter to saveNote()
- `app.js` - Clears password field when switching to public
- `app.js` - Sends `remove_password` flag when appropriate
- `index.php` - Added helpful tip about public mode

### How It Works
1. When you switch a note to "Public", the password field is automatically cleared
2. On save, the password is removed from the database
3. The note becomes accessible without a password
4. Switching back to "Private" allows you to set a new password

## Version 1.2.1 - File Upload Improvements

### Fixed
- **Increased Upload Limits** - Now supports files up to 50MB (was 10MB)
- **Better Error Messages** - Detailed error messages for upload failures
- **PHP Configuration** - Automatic PHP runtime limit configuration
- **Empty File Detection** - Prevents uploading empty files

### Added
- `.user.ini` - PHP configuration file for upload limits
- `TROUBLESHOOTING.md` - Comprehensive troubleshooting guide
- Detailed error handling for all upload error types:
  - File too large (with size limit in message)
  - Partial upload
  - Missing temporary folder
  - Write permission errors
  - Extension blocking

### Changed
- `config.php` - Increased MAX_FILE_SIZE to 50MB
- `config.php` - Added runtime PHP configuration
- `api.php` - Enhanced uploadFile() with better error handling
- `README.md` - Updated with upload limit commands
- `QUICKSTART.md` - Updated with upload limit commands

### Technical Details
- `upload_max_filesize`: 50M
- `post_max_size`: 50M
- `memory_limit`: 256M
- `max_execution_time`: 300 seconds
- `max_input_time`: 300 seconds

## Version 1.2.0 - Cool Route Generator & Welcome Page

### Added
- **Cool Route Generator** - Automatic generation of memorable routes
- **227+ Cool Names Database** - From mythology, fantasy, movies, places, and more
- **Welcome Page** - Beautiful landing page when visiting home route
- **Random Route Button** - Create instant notes with generated cool routes
- **8 Example Routes** - Displayed on welcome page for quick access
- **Name Categories**:
  - 29 Mythical creatures (phoenix, dragon, thor, zeus, etc.)
  - 32 Fantasy elements (wizard, knight, castle, quest, etc.)
  - 32 Movie/Series characters (stark, potter, yoda, neo, etc.)
  - 28 Epic places (avalon, asgard, atlantis, wakanda, etc.)
  - 32 Elements & nature (fire, storm, cosmic, shadow, etc.)
  - 32 Cool adjectives (epic, mystic, ancient, swift, etc.)
  - 32 Short cool words (ace, zen, wolf, hawk, bolt, etc.)
  - 10 Lucky numbers (7, 42, 99, 777, etc.)

### Features
- Routes combine 2-3 words for memorable URLs
- Examples: `phoenixfire`, `epicwizard`, `shadowwolf`, `titanstorm`
- Smart category mixing for variety
- Optional number suffixes for uniqueness
- All routes are short and easy to type

### Files Added
- `names.php` - Name generator class with all categories
- `welcome.php` - Beautiful welcome page with gradient background
- `NAMES_LIST.md` - Complete documentation of all names

### Files Modified
- `index.php` - Added welcome page routing
- `README.md` - Updated with route generator documentation
- `CHANGELOG.md` - This file

## Version 1.1.0 - Markdown Preview Feature

### Added
- **Markdown Preview** - Click the Preview button to see rendered markdown
- **Marked.js CDN Integration** - Using Marked.js v11.1.0 for markdown parsing
- **Beautiful Markdown Styling** - Comprehensive CSS for all markdown elements
- **Toggle Between Edit/Preview** - Seamlessly switch between editing and viewing
- **Close Preview Button** - Easy way to return to editing mode

### Features
- Headings (H1-H6) with proper styling
- Bold and italic text formatting
- Inline code with syntax highlighting
- Code blocks with dark theme
- Lists (ordered and unordered)
- Blockquotes with left border
- Links with hover effects
- Images with responsive sizing
- Tables with borders
- Horizontal rules

### Technical Details
- Added `marked.min.js` from jsDelivr CDN
- Created preview container with header and content sections
- Implemented `togglePreview()` function in JavaScript
- Added comprehensive markdown CSS styles
- Preview button dynamically changes to "Edit" when in preview mode

### Files Modified
- `index.php` - Added Marked.js CDN and preview container HTML
- `styles.css` - Added preview container and markdown styling (190+ lines)
- `app.js` - Added preview toggle functionality
- `README.md` - Updated features list and requirements
- `QUICKSTART.md` - Added markdown preview usage instructions

## Version 1.0.0 - Initial Release

### Features
- Modern purple-themed UI
- Auto-save functionality (2-second delay)
- File uploads with security
- Password protection
- Public/Private toggle
- Real-time statistics
- SQLite database
- Clean URL routing
- Drag & drop file uploads
- Clipboard paste support

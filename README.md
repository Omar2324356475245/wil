# School Tournament Management System

A complete PHP-based web application for managing school football tournaments with dynamic team management, match recording, standings tracking, and schedule viewing.

## Features

### ✨ Main Features
- **Dynamic Team Management**: Add, edit, and delete teams
- **Match Recording**: Record match results with automatic points calculation
- **Live Standings**: Real-time tournament rankings with detailed statistics
- **Match Schedule**: View upcoming and past matches organized by date
- **Responsive Design**: Works perfectly on desktop, tablet, and mobile devices
- **Beautiful UI**: Modern Bootstrap 5 interface with icons

### 📊 Statistics Tracking
- Points (Win: 3, Draw: 1, Loss: 0)
- Goals Scored & Conceded
- Goal Difference
- Wins, Draws, Losses
- Best Attack & Best Defense rankings

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web Server (Apache/Nginx)
- XAMPP, WAMP, or LAMP (recommended for local development)

## Installation Instructions

### Step 1: Extract Files
1. Extract the `school_tournament.zip` file
2. Copy the `school_tournament` folder to your web server directory:
   - **XAMPP**: `C:/xampp/htdocs/`
   - **WAMP**: `C:/wamp/www/`
   - **LAMP**: `/var/www/html/`

### Step 2: Create Database
1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Click on "Import" tab
3. Choose the file: `database.sql`
4. Click "Go" to execute

**OR manually create database:**
```sql
CREATE DATABASE school_tournament;
```

### Step 3: Configure Database Connection
1. Open `includes/db.php`
2. Update credentials if needed:
```php
$host = 'localhost';
$dbname = 'school_tournament';
$username = 'root';
$password = ''; // Add your MySQL password if any
```

### Step 4: Access Application
Open your browser and navigate to:
```
http://localhost/school_tournament/
```

## File Structure

```
school_tournament/
├── includes/
│   ├── db.php          # Database connection
│   ├── header.php      # Header template
│   └── footer.php      # Footer template
├── index.php           # Home page with statistics
├── teams.php           # Team management (CRUD)
├── matches.php         # Match recording
├── standings.php       # Tournament standings
├── schedule.php        # Match schedule
├── database.sql        # Database setup file
└── README.md          # This file
```

## Usage Guide

### 1. Add Teams
- Go to **Teams** page
- Enter class name (e.g., "Class A1")
- Click "Add Team"
- Edit or delete teams as needed

### 2. Record Matches
- Go to **Matches** page
- Select Team 1 and Team 2
- Enter goals for each team
- Select match date and time
- Click "Record Match"
- **Points are automatically calculated!**

### 3. View Standings
- Go to **Standings** page
- See complete tournament table
- View wins, draws, losses
- Check goal statistics
- See top scorers and best defense

### 4. Check Schedule
- Go to **Schedule** page
- View upcoming matches
- Check past match results
- See all matches in calendar view

## Database Schema

### Teams Table
```sql
- id (Primary Key)
- class_name (Unique)
- points
- goals_scored
- goals_conceded
- created_at
```

### Matches Table
```sql
- id (Primary Key)
- team1_id (Foreign Key)
- team2_id (Foreign Key)
- team1_goals
- team2_goals
- match_date
- created_at
```

## Scoring Rules

- **Win**: 3 points
- **Draw**: 1 point
- **Loss**: 0 points

**Ranking Order:**
1. Total Points
2. Goal Difference
3. Goals Scored

## Features Highlights

### Dynamic Updates
- Team statistics update automatically when matches are recorded
- Deleting a match reverses all statistics changes
- Real-time standings calculation

### User-Friendly Interface
- Clean, modern design
- Responsive layout
- Interactive buttons and icons
- Color-coded statistics
- Auto-hiding success/error messages

### Data Validation
- Teams cannot play against themselves
- Duplicate team names prevented
- Foreign key constraints ensure data integrity
- Proper error handling

## Troubleshooting

### Database Connection Error
- Check MySQL is running
- Verify database credentials in `includes/db.php`
- Ensure database `school_tournament` exists

### Page Not Found (404)
- Check file paths
- Verify all files are in correct directory
- Clear browser cache

### Styles Not Loading
- Check internet connection (Bootstrap CDN)
- Verify header.php is included properly

## Technology Stack

- **Backend**: PHP 7.4+ with PDO
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript
- **Framework**: Bootstrap 5.3.0
- **Icons**: Bootstrap Icons 1.10.0

## Browser Support

- Chrome (recommended)
- Firefox
- Safari
- Edge
- Opera

## Security Features

- Prepared statements (SQL injection prevention)
- XSS protection with htmlspecialchars()
- CSRF protection can be added
- Input validation

## Future Enhancements

Possible improvements:
- User authentication system
- Player statistics
- Match photos/videos
- PDF report generation
- Email notifications
- Advanced filtering
- Search functionality
- Match commentary

## Support

For issues or questions:
1. Check database connection
2. Verify PHP version
3. Check error logs
4. Review installation steps

## License

Free to use for educational purposes.

## Credits

Developed for school tournament management.

---

**Version**: 1.0  
**Last Updated**: February 2025  

Enjoy managing your tournament! 🏆⚽

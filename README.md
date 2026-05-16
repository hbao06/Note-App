MY NOTES - NOTES MANAGEMENT WEB APPLICATION
FINAL PROJECT SUBMISSION

============================================================
1. PROJECT INFORMATION
============================================================

Project name:
My Notes - Notes Management Web Application

Description:
My Notes is a web-based note management application that allows users to create, edit, organize, protect, share, and collaborate on notes. The system supports authentication, email verification, password reset, note auto-save, labels, pinned notes, password-protected notes, file/image upload, sharing permissions, realtime notifications, realtime collaborative editing, PWA support, offline note creation/editing, and synchronization when the connection is restored.

Main technologies:
- Backend: Laravel
- Frontend: Blade, Tailwind CSS, JavaScript
- Database: MySQL
- Realtime: Laravel Echo + Pusher WebSocket
- Email testing: Mailtrap
- PWA/Offline: Service Worker, IndexedDB
- Containerization: Docker, Docker Compose

The project is packaged with Docker Compose so it can be built and tested locally on the evaluator's machine.

============================================================
2. SUBMISSION STRUCTURE
============================================================

The final submission should have the following structure:

MyNotes_Submission/
|
|-- Rubrik.docx
|-- Readme.txt
|-- demo.mp4
|
|-- source/
    |-- app/
    |-- bootstrap/
    |-- config/
    |-- database/
    |-- public/
    |-- resources/
    |-- routes/
    |-- storage/
    |-- tests/
    |-- artisan
    |-- composer.json
    |-- composer.lock
    |-- package.json
    |-- package-lock.json
    |-- Dockerfile
    |-- docker-compose.yml
    |-- nginx.conf
    |-- .env.example

Notes:
- The "source" folder contains the full source code and Docker Compose configuration.
- The "vendor" and "node_modules" folders are not required in the submission because dependencies can be installed using Composer and npm.
- The ".env" file may be excluded for security. Please create it from ".env.example" when running the project.

============================================================
3. SYSTEM REQUIREMENTS
============================================================

Required software:
- Docker Desktop
- Docker Compose
- Internet connection for Mailtrap and Pusher testing

Check Docker installation:

docker --version
docker compose version

No XAMPP, WAMP, or manual Apache/Nginx installation is required.

============================================================
4. HOW TO RUN THE PROJECT WITH DOCKER COMPOSE
============================================================

Step 1: Extract the submitted ZIP file.

Step 2: Open terminal and go to the source folder:

cd source

Step 3: Create the environment file:

cp .env.example .env

On Windows, if the "cp" command is not available, manually copy ".env.example" and rename the copied file to ".env".

Step 4: Configure the .env file.

Recommended local configuration:

APP_NAME="My Notes"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8080

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=myproject
DB_USERNAME=root
DB_PASSWORD=root

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync

Step 5: Configure Mailtrap in .env.

The application uses Mailtrap for:
- Email verification
- Password reset email
- Shared note notification email

Example:

MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=YOUR_MAILTRAP_USERNAME
MAIL_PASSWORD=YOUR_MAILTRAP_PASSWORD
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=no-reply@mynotes.test
MAIL_FROM_NAME="My Notes"

Please replace YOUR_MAILTRAP_USERNAME and YOUR_MAILTRAP_PASSWORD with valid SMTP credentials from Mailtrap.

Step 6: Configure Pusher in .env.

The application uses Pusher WebSocket for realtime features.

Example:

BROADCAST_DRIVER=pusher
BROADCAST_CONNECTION=pusher

PUSHER_APP_ID=YOUR_PUSHER_APP_ID
PUSHER_APP_KEY=YOUR_PUSHER_APP_KEY
PUSHER_APP_SECRET=YOUR_PUSHER_APP_SECRET
PUSHER_APP_CLUSTER=ap1
PUSHER_PORT=443
PUSHER_SCHEME=https

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"

Step 7: Build and start Docker containers:

docker compose up -d --build

Step 8: Install PHP dependencies:

docker compose exec app composer install

Step 9: Install frontend dependencies:

docker compose exec app npm install

Step 10: Generate Laravel application key:

docker compose exec app php artisan key:generate

Step 11: Run migrations and seed sample data:

docker compose exec app php artisan migrate --seed

If you want to reset the database completely:

docker compose exec app php artisan migrate:fresh --seed

Step 12: Create storage symbolic link:

docker compose exec app php artisan storage:link

Step 13: Build frontend assets:

docker compose exec app npm run build

Step 14: Open the application:

http://localhost:8080

If port 8080 is already in use, please change the port mapping in docker-compose.yml and run Docker Compose again.

============================================================
5. HOW TO STOP THE PROJECT
============================================================

Stop containers:

docker compose down

Stop containers and remove database volume:

docker compose down -v

Warning:
Running "docker compose down -v" will delete the database volume. You will need to run migrations and seeders again.

============================================================
6. TEST ACCOUNTS
============================================================

If the database seeders are executed successfully, the following accounts can be used for testing.

Owner account:
Email: hoaibao2284@gmail.com
Password: conmeo123

Shared user account 1:
Email: hoaibao2009@gmail.com
Password: concho123

Shared user account 2:
Email: letrungbao207@gmail.com
Password: conga123

Default test account:
Email: test@example.com
Password: password

Notes:
- These accounts should be created by the database seeder.
- If the accounts do not exist, please run:

docker compose exec app php artisan migrate:fresh --seed

- New accounts can also be created directly from the Register page.

============================================================
7. MAIN FEATURES
============================================================

7.1 Account Management

Implemented features:
- User registration
- Login and logout
- Password hashing using Laravel authentication
- Email verification through Mailtrap
- Forgot password
- Reset password through Mailtrap
- Edit profile information
- Upload and update avatar
- User settings:
  + Font size customization
  + Note color customization
  + Light/Dark theme customization

7.2 Basic Note Management

Implemented features:
- Display personal notes
- Grid view and list view
- Create new note
- Edit note
- Delete note with confirmation
- Auto-save note title and content
- Search notes directly by title and content
- Pin notes to the top
- Upload one or multiple images to a note
- Display uploaded images
- Sort notes by latest update
- Manage labels
- Attach multiple labels to one note
- Filter notes by label

7.3 Advanced Note Features

Implemented features:
- Lock notes with password
- Require password before opening locked notes
- Remove note password
- Share notes with registered users by email
- Share notes with multiple users
- Set sharing permission:
  + Read only
  + Can edit
- Owner can manage shared access
- Shared users have a "Shared with me" page
- Shared notes display owner information and permission type
- Realtime notification when a note is shared
- Realtime refresh on the Shared with me page
- Realtime collaborative editing for notes shared with edit permission

7.4 PWA and Offline Features

Implemented features:
- Installable PWA
- Service Worker support
- Offline page
- Offline note creation
- Offline note editing
- Save offline notes to IndexedDB
- Sync offline notes when the browser goes online again
- Prevent offline logic from breaking normal online note editing

7.5 Realtime WebSocket Features

Implemented features:
- Laravel Echo and Pusher integration
- Private user channel for shared note notification
- Public note channel for collaborative editing
- Realtime update of note title and content
- Realtime collaborator status with avatar/name
- Realtime refresh for shared note cards

7.6 UI/UX and Responsive Design

Implemented features:
- Modern dashboard UI
- Responsive layout for desktop, tablet, and mobile
- Sidebar navigation
- Collapsible sidebar
- Modern login/register pages
- Login-like light background
- ChatGPT-like dark theme
- Polished editor modal
- Clean profile page
- Clear visual states for locked, pinned, shared, and labeled notes

============================================================
8. REALTIME / WEBSOCKET TESTING
============================================================

The project uses Laravel Echo and Pusher for realtime communication.

To test realtime shared notification:

1. Open browser A and login as the owner account.
2. Open browser B or incognito window and login as another account.
3. In browser B, open "Shared with me".
4. In browser A, share a note with browser B's email.
5. Browser B should receive the notification and shared note without manually reloading the page.

To test realtime collaboration:

1. Login as owner in browser A.
2. Login as shared user in browser B.
3. Owner shares a note with "Can edit" permission.
4. Both users open the same note.
5. Browser A edits the note.
6. Browser B should see the updated title/content in realtime.
7. Browser B edits the note.
8. Browser A should see the update in realtime.

If realtime does not work:
- Check Pusher credentials in .env.
- Make sure BROADCAST_CONNECTION=pusher.
- Run:

docker compose exec app php artisan optimize:clear

- Rebuild frontend assets if VITE variables were changed:

docker compose exec app npm run build

============================================================
9. EMAIL / MAILTRAP TESTING
============================================================

Mailtrap is used for email testing. The application sends the following emails:

- Email verification
- Password reset email
- Shared note notification email

How to test email:
1. Configure valid Mailtrap SMTP credentials in .env.
2. Register a new account or use forgot password.
3. Open the Mailtrap inbox.
4. Check the received email.

Important:
- Mailtrap does not send emails to real inboxes.
- Emails appear only in the Mailtrap testing inbox.
- If Mailtrap quota is exceeded or credentials are wrong, email-related features may return errors.

============================================================
10. PWA / OFFLINE TESTING
============================================================

To test PWA and offline mode:

1. Open the application in Chrome.
2. Open DevTools.
3. Go to Application > Service Workers.
4. Confirm that the service worker is registered.
5. Turn off the network connection or select "Offline" in DevTools.
6. Create or edit a note.
7. The note should be saved offline.
8. Turn the network back on.
9. The offline data should be synced back to the online database.

If old files are cached:
1. Open DevTools.
2. Go to Application > Service Workers.
3. Click Unregister.
4. Go to Application > Storage.
5. Click Clear site data.
6. Reload the application.

============================================================
11. DATABASE INFORMATION
============================================================

Default local database configuration:

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=myproject
DB_USERNAME=root
DB_PASSWORD=root

Run migrations:

docker compose exec app php artisan migrate

Reset database and seed sample data:

docker compose exec app php artisan migrate:fresh --seed

Access MySQL container:

docker compose exec mysql mysql -u root -p

Then enter the password:

root

============================================================
12. STORAGE AND IMAGE UPLOAD
============================================================

The project uses Laravel storage for uploaded images and avatars.

After setting up the project, run:

docker compose exec app php artisan storage:link

If uploaded images do not appear, please check:
- APP_URL is set to http://localhost:8080
- storage:link has been executed
- The storage folder has write permission
- The public disk is correctly configured

============================================================
13. USEFUL COMMANDS
============================================================

Start containers:

docker compose up -d --build

Stop containers:

docker compose down

View logs:

docker compose logs -f

Install PHP dependencies:

docker compose exec app composer install

Install frontend dependencies:

docker compose exec app npm install

Build frontend:

docker compose exec app npm run build

Run development frontend server:

docker compose exec app npm run dev

Generate app key:

docker compose exec app php artisan key:generate

Run migrations:

docker compose exec app php artisan migrate

Reset and seed database:

docker compose exec app php artisan migrate:fresh --seed

Create storage link:

docker compose exec app php artisan storage:link

Clear Laravel cache:

docker compose exec app php artisan optimize:clear

Clear views:

docker compose exec app php artisan view:clear

Clear config:

docker compose exec app php artisan config:clear

============================================================
14. NOTES FOR EVALUATION
============================================================

- The application should be run from the root URL, for example:
  http://localhost:8080

- The project does not require XAMPP.

- Email features require valid Mailtrap SMTP credentials.

- Realtime features require valid Pusher credentials.

- If PWA cache causes old files to appear, clear site data in the browser.

- If database tables are missing, run:
  docker compose exec app php artisan migrate:fresh --seed

- If uploaded images are missing, run:
  docker compose exec app php artisan storage:link

- If .env is changed, run:
  docker compose exec app php artisan optimize:clear

- If VITE_PUSHER variables are changed, run:
  docker compose exec app npm run build

============================================================
15. OPTIONAL / BONUS FEATURES
============================================================

The project includes the following advanced/optional features:

- PWA support
- Offline note creation and editing
- Offline-to-online synchronization
- Realtime shared note notification
- Realtime collaborative editing
- Mailtrap email integration
- Light/Dark theme customization
- Note color customization
- Font size customization
- Password-protected notes
- Avatar upload
- Responsive UI
- Docker Compose deployment

============================================================
16. TEAM MEMBERS
============================================================

1. Full name: Trương Huỳnh Hoài Bảo
   Student ID: 52400170
   Email: 52400170@student.tdtu.edu.vn

2. Full name: Lê Trung Bảo
   Student ID: 52400042
   Email: 52400042@student.tdtu.edu.vn

3. Full name: Dương Quốc Thắng
   Student ID: 52400237
   Email: 52400237@student.tdtu.edu.vn

============================================================
17. CONCLUSION
============================================================

This project has been packaged to run with Docker Compose. The evaluator can extract the submission, open the source folder, configure the .env file, install dependencies, run migrations and seeders, and start the application using the commands provided above.

The application implements the main requirements of a note management system, including authentication, email verification, password reset, note CRUD, auto-save, search, labels, pinned notes, password-protected notes, image upload, note sharing, permission control, realtime notification, realtime collaborative editing, PWA support, and offline synchronization.

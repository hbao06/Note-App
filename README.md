# MY NOTES - ỨNG DỤNG WEB QUẢN LÝ GHI CHÚ

Final Project Submission

---

## Video demo

Do file demo.mp4 có kích thước lớn, nhóm đã upload video demo lên YouTube.

Link video demo:

DÁN_LINK_YOUTUBE_Ở_ĐÂY

Ghi chú:

- Video được đặt ở chế độ Unlisted / Không công khai để giảng viên có thể truy cập bằng link.
- Video demo trình bày tổng quan công nghệ, kiến trúc ứng dụng và lần lượt các chức năng theo Rubrik.
- Nếu không mở được link, vui lòng liên hệ nhóm qua thông tin ở cuối file README.

---

## 1. Thông tin dự án

Tên dự án:

My Notes - Notes Management Web Application

Mô tả:

My Notes là ứng dụng web quản lý ghi chú, cho phép người dùng đăng ký tài khoản, đăng nhập, tạo ghi chú, chỉnh sửa ghi chú, tự động lưu, tìm kiếm, quản lý nhãn, ghim ghi chú, khóa ghi chú bằng mật khẩu, upload hình ảnh, chia sẻ ghi chú cho người dùng khác và cộng tác chỉnh sửa ghi chú theo thời gian thực.

Ứng dụng cũng hỗ trợ PWA/offline, cho phép người dùng tạo hoặc chỉnh sửa ghi chú khi mất kết nối mạng và đồng bộ lại dữ liệu khi có mạng trở lại.

Công nghệ sử dụng:

- Backend: Laravel
- Frontend: Blade, Tailwind CSS, JavaScript
- Database: MySQL
- Realtime: Laravel Echo + Pusher WebSocket
- Email testing: Mailtrap
- PWA/Offline: Service Worker, IndexedDB
- Containerization: Docker, Docker Compose

Dự án được đóng gói bằng Docker Compose để giảng viên có thể chạy và kiểm tra trên máy cục bộ.

---

## 2. Cấu trúc thư mục nộp bài

Cấu trúc thư mục nộp bài:

    MyNotes_Submission/
    ├── Rubrik.docx
    ├── Readme.txt
    ├── demo-link.txt
    └── source/
        ├── app/
        ├── bootstrap/
        ├── config/
        ├── database/
        ├── public/
        ├── resources/
        ├── routes/
        ├── storage/
        ├── tests/
        ├── artisan
        ├── composer.json
        ├── composer.lock
        ├── package.json
        ├── package-lock.json
        ├── Dockerfile
        ├── docker-compose.yml
        ├── nginx.conf
        └── .env.example

Ghi chú:

- Thư mục source chứa toàn bộ mã nguồn và cấu hình Docker Compose.
- Không cần nộp thư mục vendor và node_modules vì có thể cài lại bằng Composer và npm.
- File .env có thể không được nộp kèm vì chứa thông tin nhạy cảm.
- Khi chạy dự án, tạo file .env từ .env.example.
- Do video demo có kích thước lớn, nhóm sử dụng link YouTube thay cho file demo.mp4.

---

## 3. Yêu cầu môi trường

Máy chấm cần cài đặt:

- Docker Desktop
- Docker Compose
- Kết nối Internet để kiểm tra Mailtrap và Pusher

Kiểm tra Docker bằng lệnh:

    docker --version
    docker compose version

Dự án không yêu cầu XAMPP, WAMP hoặc cấu hình thủ công Apache/Nginx.

---

## 4. Hướng dẫn chạy dự án bằng Docker Compose

### Bước 1: Giải nén bài nộp

Giải nén file ZIP bài nộp.

### Bước 2: Mở terminal và đi vào thư mục source

    cd source

### Bước 3: Tạo file môi trường .env

    cp .env.example .env

Nếu dùng Windows và không chạy được lệnh cp, có thể copy thủ công file .env.example rồi đổi tên thành .env.

### Bước 4: Cấu hình file .env

Cấu hình local khuyến nghị:

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

### Bước 5: Cấu hình Mailtrap

Do việc cấu hình email thật trong môi trường local/development khá phức tạp và có thể phụ thuộc vào nhà cung cấp email, nhóm sử dụng Mailtrap để kiểm thử các chức năng liên quan đến email trong môi trường kiểm thử.

Mailtrap được dùng để kiểm tra:

- Email xác minh tài khoản
- Email đặt lại mật khẩu
- Email thông báo khi ghi chú được chia sẻ

Ví dụ cấu hình:

    MAIL_MAILER=smtp
    MAIL_HOST=sandbox.smtp.mailtrap.io
    MAIL_PORT=2525
    MAIL_USERNAME=YOUR_MAILTRAP_USERNAME
    MAIL_PASSWORD=YOUR_MAILTRAP_PASSWORD
    MAIL_ENCRYPTION=tls
    MAIL_FROM_ADDRESS=no-reply@mynotes.test
    MAIL_FROM_NAME="My Notes"

Thay YOUR_MAILTRAP_USERNAME và YOUR_MAILTRAP_PASSWORD bằng thông tin SMTP lấy từ Mailtrap.

Lưu ý:

- Mailtrap không gửi email đến hộp thư thật của người dùng.
- Email sẽ xuất hiện trong Mailtrap Testing Inbox.
- Nếu không cấu hình Mailtrap, các chức năng chính của hệ thống vẫn có thể kiểm tra, nhưng các chức năng email như xác minh tài khoản, đặt lại mật khẩu và thông báo chia sẻ qua email sẽ không hoạt động đầy đủ.

### Bước 6: Cấu hình Pusher WebSocket

Ứng dụng sử dụng Pusher WebSocket cho các chức năng realtime.

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

### Bước 7: Build và chạy container

    docker compose up -d --build

### Bước 8: Cài dependency PHP

    docker compose exec app composer install

### Bước 9: Cài dependency frontend

    docker compose exec app npm install

### Bước 10: Tạo Laravel application key

    docker compose exec app php artisan key:generate

### Bước 11: Chạy migration và seed dữ liệu mẫu

    docker compose exec app php artisan migrate --seed

Nếu muốn reset database hoàn toàn:

    docker compose exec app php artisan migrate:fresh --seed

### Bước 12: Tạo symbolic link cho storage

    docker compose exec app php artisan storage:link

### Bước 13: Build frontend assets

    docker compose exec app npm run build

### Bước 14: Mở ứng dụng

    http://localhost:8080

Nếu cổng 8080 bị trùng, sửa port mapping trong docker-compose.yml rồi chạy lại Docker Compose.

---

## 5. Cách dừng dự án

Dừng container:

    docker compose down

Dừng container và xóa database volume:

    docker compose down -v

Lưu ý:

Lệnh docker compose down -v sẽ xóa dữ liệu database. Sau đó cần chạy lại migration và seeder.

---

## 6. Tài khoản test

Nếu chạy seeder thành công, có thể sử dụng các tài khoản sau để kiểm tra.

Tài khoản owner - thiết lập sẵn các thẻ:

    Email: hoaibao2284@gmail.com
    Password: password123

Tài khoản nhận chia sẻ 1:

    Email: trungbao2285@gmail.com
    Password: password456

Tài khoản nhận chia sẻ 2:

    Email: quocthang2286@gmail.com
    Password: password789

Ghi chú:

- Các tài khoản trên được tạo bằng DatabaseSeeder.
- Các tài khoản đã được đặt email_verified_at để thuận tiện cho quá trình kiểm thử.
- Nếu tài khoản chưa tồn tại, vui lòng chạy:

    docker compose exec app php artisan migrate:fresh --seed

- Người dùng cũng có thể tạo tài khoản mới trực tiếp bằng chức năng Register trên website.

---

## 7. Các chức năng chính đã triển khai

### 7.1 Quản lý tài khoản

Ứng dụng đã triển khai:

- Đăng ký tài khoản
- Đăng nhập
- Đăng xuất
- Hash mật khẩu bằng Laravel Authentication
- Xác minh email thông qua Mailtrap
- Quên mật khẩu
- Đặt lại mật khẩu thông qua email Mailtrap
- Xem và cập nhật thông tin cá nhân
- Upload và cập nhật ảnh đại diện
- Tùy chỉnh người dùng:
  - Cỡ chữ ghi chú
  - Màu ghi chú
  - Giao diện sáng/tối

### 7.2 Quản lý ghi chú cơ bản

Ứng dụng đã triển khai:

- Hiển thị danh sách ghi chú cá nhân
- Chế độ xem dạng lưới
- Chế độ xem dạng danh sách
- Tạo ghi chú mới
- Chỉnh sửa ghi chú
- Xóa ghi chú với hộp thoại xác nhận
- Tự động lưu tiêu đề và nội dung ghi chú
- Tìm kiếm trực tiếp theo tiêu đề và nội dung
- Ghim ghi chú lên đầu danh sách
- Upload một hoặc nhiều hình ảnh cho ghi chú
- Hiển thị hình ảnh đã upload
- Sắp xếp ghi chú theo thời gian cập nhật mới nhất
- Hiển thị thời gian ghi chú được tạo hoặc được chỉnh sửa
- Quản lý nhãn
- Gắn nhiều nhãn cho một ghi chú
- Lọc ghi chú theo nhãn

### 7.3 Chức năng ghi chú nâng cao

Ứng dụng đã triển khai:

- Khóa ghi chú bằng mật khẩu
- Yêu cầu nhập mật khẩu trước khi mở ghi chú bị khóa
- Tạo mật khẩu bảo vệ ghi chú với xác nhận mật khẩu
- Đổi mật khẩu ghi chú có kiểm tra mật khẩu hiện tại
- Gỡ mật khẩu khỏi ghi chú có xác nhận lại mật khẩu hiện tại
- Chia sẻ ghi chú cho người dùng đã đăng ký bằng email
- Kiểm tra email chia sẻ có thuộc người dùng đã đăng ký trong hệ thống hay không
- Chia sẻ ghi chú cho nhiều người dùng
- Gửi email thông báo cho người nhận khi được chia sẻ ghi chú thông qua Mailtrap
- Hiển thị thông báo trong tài khoản người nhận khi có ghi chú được chia sẻ
- Phân quyền chia sẻ:
  - Chỉ xem
  - Được chỉnh sửa
- Chủ sở hữu có thể quản lý quyền truy cập ghi chú
- Chủ sở hữu có thể xem danh sách người nhận đã được chia sẻ
- Chủ sở hữu có thể xem email và quyền của từng người nhận
- Chủ sở hữu có thể thay đổi hoặc thu hồi quyền chia sẻ
- Người nhận có trang Shared with me để xem ghi chú được chia sẻ
- Hiển thị thông tin người chia sẻ và quyền truy cập
- Thông báo realtime khi có ghi chú được chia sẻ
- Trang Shared with me tự cập nhật realtime
- Cộng tác chỉnh sửa ghi chú realtime với quyền edit

### 7.4 PWA và Offline

Ứng dụng đã triển khai:

- Có thể cài đặt như PWA
- Hỗ trợ Service Worker
- Có trang offline
- Tạo ghi chú khi offline
- Chỉnh sửa ghi chú khi offline
- Lưu dữ liệu offline bằng IndexedDB
- Đồng bộ ghi chú offline khi có mạng trở lại
- Offline logic không làm ảnh hưởng chức năng online

### 7.5 Realtime WebSocket

Ứng dụng đã triển khai:

- Tích hợp Laravel Echo và Pusher
- Private user channel cho thông báo chia sẻ ghi chú
- Note channel cho cộng tác chỉnh sửa realtime
- Cập nhật realtime tiêu đề và nội dung ghi chú
- Hiển thị người đang cập nhật bằng avatar/tên tài khoản
- Cập nhật realtime card ghi chú trong Shared with me

### 7.6 UI/UX và Responsive

Ứng dụng đã triển khai:

- Giao diện hiện đại, dễ sử dụng
- Responsive trên desktop, tablet và mobile
- Sidebar điều hướng
- Sidebar có thể thu gọn/mở rộng
- Phần Recent trong sidebar để truy cập nhanh các ghi chú gần đây
- Giao diện login/register hiện đại
- Light theme giống nền login
- Dark theme kiểu ChatGPT
- Modal editor bo góc đẹp
- Trang profile rõ ràng, đồng bộ giao diện
- Trạng thái ghi chú được thể hiện bằng icon: ghim, khóa, chia sẻ, nhãn

---

## 8. Kiểm tra Realtime / WebSocket

Ứng dụng sử dụng Laravel Echo và Pusher để xử lý realtime.

Kiểm tra thông báo realtime khi chia sẻ ghi chú:

1. Mở trình duyệt A và đăng nhập tài khoản owner.
2. Mở trình duyệt B hoặc tab ẩn danh và đăng nhập tài khoản khác.
3. Ở trình duyệt B, mở trang Shared with me.
4. Ở trình duyệt A, chia sẻ ghi chú cho email của tài khoản ở trình duyệt B.
5. Trình duyệt B sẽ nhận thông báo và ghi chú được chia sẻ mà không cần reload thủ công.

Kiểm tra cộng tác chỉnh sửa realtime:

1. Đăng nhập owner ở trình duyệt A.
2. Đăng nhập user nhận chia sẻ ở trình duyệt B.
3. Owner chia sẻ ghi chú với quyền Can edit.
4. Cả hai tài khoản cùng mở một ghi chú.
5. Tài khoản A chỉnh sửa ghi chú.
6. Tài khoản B sẽ thấy tiêu đề/nội dung được cập nhật realtime.
7. Tài khoản B chỉnh sửa lại.
8. Tài khoản A cũng sẽ thấy thay đổi realtime.

Nếu realtime không hoạt động:

- Kiểm tra Pusher credentials trong file .env.
- Đảm bảo BROADCAST_CONNECTION=pusher.
- Chạy lệnh:

    docker compose exec app php artisan optimize:clear

- Nếu thay đổi biến VITE_PUSHER trong .env, chạy lại:

    docker compose exec app npm run build

---

## 9. Kiểm tra Email / Mailtrap

Do cấu hình email thật trong môi trường local thường phức tạp, nhóm sử dụng Mailtrap để kiểm thử các chức năng xác thực email và thông báo email trong môi trường kiểm thử.

Các email được gửi qua Mailtrap:

- Email xác minh tài khoản
- Email đặt lại mật khẩu
- Email thông báo chia sẻ ghi chú

Cách kiểm tra:

1. Cấu hình SMTP Mailtrap hợp lệ trong file .env.
2. Đăng ký tài khoản mới, dùng chức năng quên mật khẩu hoặc chia sẻ ghi chú cho người dùng khác.
3. Mở Mailtrap Inbox.
4. Kiểm tra email được gửi đến.

Lưu ý:

- Mailtrap không gửi email đến hộp thư thật.
- Email chỉ xuất hiện trong Mailtrap Testing Inbox.
- Nếu Mailtrap hết quota hoặc sai credentials, các chức năng email có thể báo lỗi.
- Các chức năng email trong dự án được triển khai để mô phỏng và kiểm thử luồng email giống môi trường thực tế.

---

## 10. Kiểm tra PWA / Offline

Cách kiểm tra PWA và offline:

1. Mở ứng dụng bằng Chrome.
2. Mở DevTools.
3. Vào Application > Service Workers.
4. Kiểm tra Service Worker đã được đăng ký.
5. Tắt mạng hoặc chọn chế độ Offline trong DevTools.
6. Tạo hoặc chỉnh sửa ghi chú.
7. Ghi chú sẽ được lưu offline.
8. Bật mạng lại.
9. Dữ liệu offline sẽ được đồng bộ về database online.

Nếu trình duyệt bị cache file cũ:

1. Mở DevTools.
2. Vào Application > Service Workers.
3. Chọn Unregister.
4. Vào Application > Storage.
5. Chọn Clear site data.
6. Reload lại ứng dụng.

---

## 11. Thông tin Database

Cấu hình database mặc định:

    DB_CONNECTION=mysql
    DB_HOST=mysql
    DB_PORT=3306
    DB_DATABASE=myproject
    DB_USERNAME=root
    DB_PASSWORD=root

Chạy migration:

    docker compose exec app php artisan migrate

Reset database và seed lại dữ liệu mẫu:

    docker compose exec app php artisan migrate:fresh --seed

Truy cập MySQL container:

    docker compose exec mysql mysql -u root -p

Sau đó nhập password:

    root

---

## 12. Storage và upload hình ảnh

Ứng dụng sử dụng Laravel storage để lưu hình ảnh ghi chú và avatar.

Sau khi cài đặt dự án, cần chạy:

    docker compose exec app php artisan storage:link

Nếu hình ảnh upload không hiển thị, vui lòng kiểm tra:

- APP_URL có đúng là http://localhost:8080 hay không
- Đã chạy php artisan storage:link chưa
- Thư mục storage có quyền ghi hay không
- Cấu hình public disk có đúng không

---

## 13. Các lệnh hữu ích

Khởi động container:

    docker compose up -d --build

Dừng container:

    docker compose down

Xem log container:

    docker compose logs -f

Cài dependency PHP:

    docker compose exec app composer install

Cài dependency frontend:

    docker compose exec app npm install

Build frontend:

    docker compose exec app npm run build

Chạy frontend development server:

    docker compose exec app npm run dev

Tạo app key:

    docker compose exec app php artisan key:generate

Chạy migration:

    docker compose exec app php artisan migrate

Reset database và seed lại:

    docker compose exec app php artisan migrate:fresh --seed

Tạo storage link:

    docker compose exec app php artisan storage:link

Clear cache Laravel:

    docker compose exec app php artisan optimize:clear

Clear view:

    docker compose exec app php artisan view:clear

Clear config:

    docker compose exec app php artisan config:clear

---

## 14. Lưu ý khi chấm bài

- Ứng dụng nên được chạy tại URL gốc, ví dụ:

    http://localhost:8080

- Dự án không cần XAMPP.
- Các chức năng email cần cấu hình Mailtrap hợp lệ.
- Các chức năng realtime cần cấu hình Pusher hợp lệ.
- Nếu PWA cache làm giao diện hoặc file JS/CSS không cập nhật, hãy clear site data trong trình duyệt.
- Nếu thiếu bảng database, chạy:

    docker compose exec app php artisan migrate:fresh --seed

- Nếu ảnh upload không hiển thị, chạy:

    docker compose exec app php artisan storage:link

- Nếu thay đổi file .env, chạy:

    docker compose exec app php artisan optimize:clear

- Nếu thay đổi các biến VITE_PUSHER, chạy:

    docker compose exec app npm run build

---

## 15. Các tính năng nâng cao / điểm cộng

Dự án có triển khai các chức năng nâng cao và các cải tiến bổ sung sau:

- Sidebar điều hướng riêng cho workspace
- Sidebar có thể thu gọn/mở rộng
- Phần Recent trong sidebar để người dùng mở nhanh các ghi chú gần đây
- Hiển thị thời gian tạo hoặc cập nhật của ghi chú
- Giao diện responsive cho desktop, tablet và mobile
- Giao diện light theme và dark theme
- Tùy chỉnh màu ghi chú
- Tùy chỉnh cỡ chữ ghi chú
- Upload avatar người dùng
- Khóa ghi chú bằng mật khẩu với xác nhận bảo mật tốt hơn
- Chia sẻ ghi chú có kiểm tra email người nhận thuộc người dùng đã đăng ký
- Gửi email thông báo khi ghi chú được chia sẻ
- Hiển thị thông báo nổi bật trong tài khoản người nhận khi có ghi chú được chia sẻ
- Chủ sở hữu ghi chú xem được danh sách người nhận, email và quyền truy cập
- Chủ sở hữu có thể thay đổi hoặc thu hồi quyền chia sẻ
- Realtime notification khi chia sẻ ghi chú
- Realtime collaborative editing cho ghi chú được chia sẻ với quyền edit
- PWA
- Tạo và chỉnh sửa ghi chú offline
- Đồng bộ dữ liệu offline khi online lại
- Triển khai bằng Docker Compose

Các tính năng trên được phát triển theo hướng cải thiện trải nghiệm người dùng, tăng tính bảo mật, xử lý lỗi tốt hơn và tiệm cận cách hoạt động của các dịch vụ ghi chú hiện đại.

---

## 16. Thành viên nhóm

Thành viên 1:

    Họ tên: Trương Huỳnh Hoài Bảo
    MSSV: 52400170
    Email: 52400170@student.tdtu.edu.vn

Thành viên 2:

    Họ tên: Lê Trung Bảo
    MSSV: 52400042
    Email: 52400042@student.tdtu.edu.vn

Thành viên 3:

    Họ tên: Dương Quốc Thắng
    MSSV: 52400237
    Email: 52400237@student.tdtu.edu.vn

---

## 17. Kết luận

Dự án đã được đóng gói để chạy bằng Docker Compose. Giảng viên có thể giải nén bài nộp, mở thư mục source, cấu hình file .env, cài đặt dependencies, chạy migration và seeder, sau đó khởi động ứng dụng theo các lệnh được cung cấp trong tài liệu này.

Ứng dụng đã triển khai các chức năng chính của hệ thống quản lý ghi chú, bao gồm xác thực người dùng, xác minh email, đặt lại mật khẩu, CRUD ghi chú, tự động lưu, tìm kiếm, nhãn, ghim ghi chú, bảo vệ ghi chú bằng mật khẩu, upload hình ảnh, chia sẻ ghi chú, phân quyền, thông báo realtime, cộng tác chỉnh sửa realtime, PWA và đồng bộ ghi chú offline.

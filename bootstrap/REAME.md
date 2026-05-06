NOTE MANAGEMENT WEB APPLICATION
Hướng dẫn cài đặt và chạy project

1. Yêu cầu hệ thống

Vui lòng cài đặt các phần mềm sau trước khi chạy project:

Docker Desktop
https://www.docker.com/products/docker-desktop/
NodeJS (khuyến nghị phiên bản 18 trở lên)
https://nodejs.org/

Sau khi cài đặt Docker Desktop, cần mở ứng dụng và đảm bảo trạng thái hiển thị:

Engine running

2. Khởi động project
   Bước 1 — Mở terminal tại thư mục project

Ví dụ:

D:\myproject

Bước 2 — Build và khởi động Docker containers

Chạy lệnh:

docker compose up -d --build

Bước 3 — Khởi tạo Laravel

Chạy lần lượt các lệnh sau:

docker compose exec app php artisan key

docker compose exec app php artisan migrate

docker compose exec app php artisan storage

Bước 4 — Cài đặt frontend dependencies

npm install

Bước 5 — Build frontend assets (Vite/TailwindCSS)

npm run build

Bước 6 — Xóa cache hệ thống

docker compose exec app php artisan optimize

3. Truy cập website

Sau khi hoàn tất các bước trên, truy cập:

http://localhost:8080

4. Các lệnh sử dụng sau này
   Khởi động lại project

docker compose up -d

Dừng project

docker compose down

5. Thông tin bổ sung
   Project được triển khai bằng Docker Compose
   Backend: Laravel
   Frontend: Blade + TailwindCSS + Vite
   Database: MySQL
   Reverse Proxy: Nginx

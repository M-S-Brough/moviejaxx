# 🎬 MovieJaxx — Symfony Movie Review Platform + REST API

**MovieJaxx** is a full-stack movie review platform built with the Symfony PHP framework. This project includes both a traditional web application and a RESTful API. It allows users to create, read, update, and delete movie reviews while integrating external movie data via The Movie Database (TMDb) API.

---

## 🔧 Technologies Used

- **PHP 8+** (Symfony Framework)
- **Doctrine ORM**
- **Twig Templating**
- **MySQL**
- **JWT Authentication** (LexikJWTAuthenticationBundle)
- **Guzzle HTTP Client**
- **Bootstrap (Frontend)**
- **Postman & cURL (API Testing)**

---

## 🗂️ Project Structure

### 📦 Assignment 1 — Web App with Symfony
- User authentication and registration (role-based: user/mod/admin)
- Create/read/edit/delete reviews
- Upload movie data: title, director, cast, poster, runtime
- Search and filter functionality
- Clean MVC structure using Symfony best practices

### 📡 Assignment 2 — RESTful API
- Fully documented RESTful API for:
  - Authentication (`/api/login_check`)
  - Movies (CRUD)
  - Reviews (CRUD)
- JSON responses with standard HTTP codes
- JWT-secured endpoints
- API consumption from TMDb to auto-fill movie data
- Autocomplete search with Guzzle integration

---

## 🔐 Example API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST   | `/api/login_check` | Authenticate user and receive token |
| GET    | `/api/movies` | Get list of movies |
| POST   | `/api/movies` | Create new movie |
| PUT    | `/api/movies/{id}` | Update movie |
| DELETE | `/api/movies/{id}` | Delete movie |
| POST   | `/api/reviews` | Create review |
| PUT    | `/api/reviews/{id}` | Update review |
| DELETE | `/api/reviews/{id}` | Delete review |

---

## 🌍 External API Integration

- **The Movie Database (TMDb) API**:
  - Autocomplete movie titles
  - Populate movie form fields with data
  - Display extra info on detail pages

---

## 🚀 How to Run

### Prerequisites
- PHP 8.1+
- Composer
- MySQL
- Symfony CLI (optional but recommended)

### Setup Steps
```bash
composer install
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
symfony server:start
```

### .env
Create a `.env.local` file with your DB credentials and JWT secret.

---

## 🧪 Testing the API
Use Postman or cURL to test endpoints.

Example:
```bash
curl -X POST http://localhost:8000/api/login_check \
  -H "Content-Type: application/json" \
  -d '{"username":"admin@site.com","password":"pass123"}'
```

---

## 👨‍🎓 Author

**Mark Brough**  
University of Salford  

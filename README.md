# IdenSer - WSO2 Identity Server Integration

A Laravel application demonstrating **Federated Authorization** through WSO2 Identity Server integration, enabling secure cross-domain authentication and authorization.

## 🚀 Project Overview

This project implements a comprehensive federated identity management system using WSO2 Identity Server as the central identity provider. The application demonstrates how federated authorization allows secure access to resources across different domains and systems.

## 🔐 Key Features

### Federated Authorization
- **WSO2 Identity Server Integration**: Central identity provider for authentication and authorization
- **OAuth 2.0/OpenID Connect**: Industry-standard protocols for secure federated identity
- **Single Sign-On (SSO)**: Seamless authentication across multiple systems
- **Cross-Domain Authorization**: Secure resource access across different domains

### Role-Based Access Control (RBAC)
- **Centralized Role Management**: Roles managed in WSO2 IS and enforced in Laravel
- **Multi-Level Access**: Admin, HR, and Employee roles with different permissions
- **Dynamic Authorization**: Real-time role-based access decisions

### Dashboard Modules
- **Trading Data Management**: Financial data with role-based access restrictions
- **User Management**: Admin-only user administration capabilities
- **Audit Logs**: Comprehensive activity tracking and monitoring
- **Security Policies**: Centralized security policy management
- **Role Management**: Administrative role assignment and management

## 🏗️ Architecture

```
┌─────────────────┐    OAuth 2.0/OIDC    ┌─────────────────┐
│                 │◄───────────────────►│                 │
│  Laravel App    │                     │  WSO2 Identity  │
│  (Resource      │                     │  Server         │
│   Server)       │                     │  (Identity      │
│                 │                     │   Provider)     │
└─────────────────┘                     └─────────────────┘
        ▲                                        ▲
        │                                        │
        └────────── Federated Trust ─────────────┘
```

## 🛠️ Technology Stack

- **Backend**: Laravel 10.x
- **Authentication**: WSO2 Identity Server 7.1.0
- **Database**: SQLite (development)
- **Frontend**: Blade Templates with responsive CSS
- **Protocols**: OAuth 2.0, OpenID Connect
- **Security**: JWT tokens, CSRF protection

## ⚙️ Installation & Setup

### Prerequisites
- PHP 8.1+
- Composer
- WSO2 Identity Server 7.1.0
- SQLite

### Laravel Application Setup

1. **Clone the repository:**
   ```bash
   git clone https://github.com/Kashijika/IdenSer.git
   cd IdenSer
   ```

2. **Install dependencies:**
   ```bash
   composer install
   ```

3. **Environment configuration:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database setup:**
   ```bash
   touch database/database.sqlite
   php artisan migrate
   php artisan db:seed
   ```

5. **Configure WSO2 settings in `.env`:**
   ```env
   WSO2_BASE_URL=https://localhost:9443
   WSO2_CLIENT_ID=your_client_id
   WSO2_CLIENT_SECRET=your_client_secret
   WSO2_REDIRECT_URI=http://127.0.0.1:8000/auth/sso/wso2/callback
   ```

### WSO2 Identity Server Setup

1. **Download and start WSO2 IS:**
   ```bash
   # Download WSO2 IS 7.1.0
   cd wso2is-7.1.0/bin
   ./wso2server.sh
   ```

2. **Create OAuth Application:**
   - Access: https://localhost:9443/console
   - Create new OAuth 2.0 application
   - Configure redirect URI: `http://127.0.0.1:8000/auth/sso/wso2/callback`
   - Note the Client ID and Client Secret

3. **Configure Users and Roles:**
   - Create users in WSO2 IS
   - Assign appropriate roles (admin, hr, employee)

## 🚦 Usage

1. **Start the application:**
   ```bash
   php artisan serve
   ```

2. **Access the application:**
   - Navigate to: http://127.0.0.1:8000
   - Click "Login with WSO2"
   - Authenticate through WSO2 Identity Server
   - Access dashboard based on your assigned roles

## 🔒 Security Features

- **Federated Authentication**: No local password storage
- **JWT Token Validation**: Secure token-based authentication
- **Role-Based Authorization**: Granular access control
- **CSRF Protection**: Built-in Laravel CSRF protection
- **Session Security**: Secure session management
- **Audit Logging**: Comprehensive activity tracking

## 📁 Project Structure

```
app/
├── Http/Controllers/
│   ├── AuthController.php          # WSO2 SSO authentication
│   ├── DashboardController.php     # Main dashboard logic
│   └── AccountController.php       # User account management
├── Services/
│   └── WSO2Service.php            # WSO2 integration service
├── Middleware/
│   └── WSO2AuthMiddleware.php     # Authentication middleware
└── Models/                        # Database models

resources/views/
├── auth/                          # Authentication views
├── dashboard/                     # Dashboard modules
└── layouts/                       # Layout templates

config/
├── services.php                   # WSO2 configuration
└── wso2_roles.php                # Role mapping configuration
```

## 🔄 Federated Authorization Flow

1. **User Access**: User attempts to access protected resource
2. **Redirect to WSO2**: Application redirects to WSO2 IS for authentication
3. **User Authentication**: User authenticates with WSO2 IS
4. **Authorization Code**: WSO2 IS returns authorization code
5. **Token Exchange**: Application exchanges code for access/ID tokens
6. **User Information**: Application retrieves user info and roles
7. **Local Session**: Application creates local session with federated identity
8. **Resource Access**: User gains access based on WSO2-managed roles

## 📊 Features by Role

### Admin
- ✅ Full system access
- ✅ User management
- ✅ Security policy configuration
- ✅ Audit log review
- ✅ Role management
- ✅ Complete trading data access

### HR
- ✅ User information access
- ✅ Limited trading data access
- ✅ Employee role management
- ✅ Audit log review

### Employee
- ✅ Personal dashboard access
- ✅ Limited trading data (30 days)
- ✅ Personal account management

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## 📄 License

This project is licensed under the MIT License.

## 📞 Support

For questions and support, please open an issue in the GitHub repository.

---

**Note**: This project demonstrates federated authorization implementation using WSO2 Identity Server. It showcases how modern identity federation enables secure, scalable, and maintainable authentication and authorization across distributed systems.

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

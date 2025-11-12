# SkolarisCloud Setup Guide

## Complete Setup Instructions

### 1. System Requirements

#### Minimum Requirements
- **OS**: Windows 10/11, macOS 10.14+, or Linux (Ubuntu 18.04+)
- **Node.js**: v14.x or higher
- **MongoDB**: v4.4 or higher
- **RAM**: 4GB minimum (8GB recommended)
- **Storage**: 2GB free space

### 2. Installing Prerequisites

#### Install Node.js

**Windows/macOS:**
1. Download from [nodejs.org](https://nodejs.org/)
2. Run the installer
3. Verify installation:
```bash
node --version
npm --version
```

**Linux (Ubuntu/Debian):**
```bash
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs
```

#### Install MongoDB

**Windows:**
1. Download MongoDB Community Server from [mongodb.com](https://www.mongodb.com/try/download/community)
2. Run the installer
3. Add MongoDB to PATH
4. Start MongoDB service

**macOS:**
```bash
brew tap mongodb/brew
brew install mongodb-community
brew services start mongodb-community
```

**Linux (Ubuntu):**
```bash
wget -qO - https://www.mongodb.org/static/pgp/server-6.0.asc | sudo apt-key add -
echo "deb [ arch=amd64,arm64 ] https://repo.mongodb.org/apt/ubuntu focal/mongodb-org/6.0 multiverse" | sudo tee /etc/apt/sources.list.d/mongodb-org-6.0.list
sudo apt-get update
sudo apt-get install -y mongodb-org
sudo systemctl start mongod
sudo systemctl enable mongod
```

### 3. Project Setup

#### Clone Repository
```bash
git clone https://github.com/frankhostltd3/skolariscloud3.git
cd skolariscloud3
```

#### Install Dependencies
```bash
npm install
```

This will install:
- express: Web framework
- mongoose: MongoDB ODM
- bcryptjs: Password hashing
- jsonwebtoken: JWT authentication
- dotenv: Environment configuration
- cors: Cross-origin resource sharing
- body-parser: Request body parsing

### 4. Environment Configuration

#### Create .env file
```bash
cp .env.example .env
```

#### Configure .env
Open `.env` in a text editor and update:

```env
# Server Configuration
PORT=5000
NODE_ENV=development

# Database Configuration
MONGODB_URI=mongodb://localhost:27017/skolariscloud

# JWT Secret (Generate a strong random string)
JWT_SECRET=your_super_secret_jwt_key_here_change_this
JWT_EXPIRE=30d

# Admin Credentials
ADMIN_EMAIL=admin@skolariscloud.com
ADMIN_PASSWORD=admin123
```

**Important**: Change `JWT_SECRET` to a strong random string in production!

To generate a secure JWT secret:
```bash
node -e "console.log(require('crypto').randomBytes(32).toString('hex'))"
```

### 5. Database Setup

#### Verify MongoDB is Running

```bash
# Check MongoDB status
mongosh --eval "db.adminCommand('ping')"
```

If MongoDB is not running, start it:

**Windows:**
```bash
net start MongoDB
```

**macOS/Linux:**
```bash
sudo systemctl start mongod
# or
brew services start mongodb-community
```

#### Create Database (Optional)
MongoDB will automatically create the database when the application starts.

To manually create:
```bash
mongosh
use skolariscloud
db.createCollection("users")
exit
```

### 6. Starting the Application

#### Development Mode (with auto-restart)
```bash
npm run dev
```

#### Production Mode
```bash
npm start
```

The server will start on `http://localhost:5000`

### 7. Testing the Setup

#### Check API Health
Open browser or use curl:
```bash
curl http://localhost:5000
```

Expected response:
```json
{
  "message": "Welcome to SkolarisCloud - School Management & Academics SaaS",
  "version": "1.0.0",
  "endpoints": {
    "auth": "/api/auth",
    "schools": "/api/schools",
    "students": "/api/students",
    "teachers": "/api/teachers",
    "courses": "/api/courses",
    "attendance": "/api/attendance",
    "grades": "/api/grades"
  }
}
```

#### Access Frontend
Open browser: `http://localhost:5000`

### 8. Creating First Admin User

Use curl or Postman to create an admin:

```bash
curl -X POST http://localhost:5000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Admin User",
    "email": "admin@example.com",
    "password": "securepassword123",
    "role": "admin"
  }'
```

Response will include a JWT token:
```json
{
  "success": true,
  "data": {
    "_id": "...",
    "name": "Admin User",
    "email": "admin@example.com",
    "role": "admin",
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
  }
}
```

Save the token for authenticated requests!

### 9. Testing API Endpoints

#### Login
```bash
curl -X POST http://localhost:5000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "securepassword123"
  }'
```

#### Create a School (use token from login)
```bash
curl -X POST http://localhost:5000/api/schools \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -d '{
    "name": "Example High School",
    "address": "123 School Street",
    "phone": "1234567890",
    "email": "info@examplehs.edu",
    "principal": "Dr. Smith",
    "establishedYear": 2000
  }'
```

#### Get All Schools
```bash
curl -X GET http://localhost:5000/api/schools \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### 10. Troubleshooting

#### MongoDB Connection Issues
```bash
# Check if MongoDB is running
sudo systemctl status mongod  # Linux
brew services list  # macOS
net start | findstr MongoDB  # Windows

# Check MongoDB logs
tail -f /var/log/mongodb/mongod.log  # Linux
tail -f /usr/local/var/log/mongodb/mongo.log  # macOS
```

#### Port Already in Use
Change the PORT in `.env`:
```env
PORT=3000
```

#### JWT Token Issues
- Ensure JWT_SECRET is set in .env
- Token expires after JWT_EXPIRE time (default 30d)
- Include token in Authorization header: `Bearer <token>`

#### Permission Errors
```bash
# Fix npm permissions (Linux/macOS)
sudo chown -R $USER:$USER ~/.npm
sudo chown -R $USER:$USER node_modules
```

### 11. Production Deployment

#### Using PM2 (Process Manager)
```bash
# Install PM2
npm install -g pm2

# Start application
pm2 start server.js --name skolariscloud

# Monitor
pm2 monit

# Set to start on boot
pm2 startup
pm2 save
```

#### Environment Variables for Production
```env
NODE_ENV=production
PORT=80
MONGODB_URI=mongodb://username:password@host:port/skolariscloud
JWT_SECRET=<strong-random-secret>
```

#### Using Docker (Optional)
Create `Dockerfile`:
```dockerfile
FROM node:18-alpine
WORKDIR /app
COPY package*.json ./
RUN npm install --production
COPY . .
EXPOSE 5000
CMD ["npm", "start"]
```

Build and run:
```bash
docker build -t skolariscloud .
docker run -p 5000:5000 --env-file .env skolariscloud
```

### 12. Security Best Practices

1. **Change default credentials** in production
2. **Use strong JWT_SECRET** (32+ characters random)
3. **Enable HTTPS** in production
4. **Set up MongoDB authentication**
5. **Use environment variables** for sensitive data
6. **Regular backups** of MongoDB database
7. **Keep dependencies updated**: `npm audit fix`
8. **Rate limiting** for API endpoints (add middleware)
9. **Input validation** on all endpoints
10. **Monitor logs** for suspicious activity

### 13. Backup and Restore

#### Backup MongoDB
```bash
mongodump --db skolariscloud --out /backup/$(date +%Y%m%d)
```

#### Restore MongoDB
```bash
mongorestore --db skolariscloud /backup/20240101/skolariscloud
```

### 14. Getting Help

- **Documentation**: See API_DOCUMENTATION.md
- **GitHub Issues**: Open an issue for bugs
- **MongoDB Docs**: https://docs.mongodb.com
- **Node.js Docs**: https://nodejs.org/docs
- **Express Docs**: https://expressjs.com

### 15. Next Steps

1. Create sample data (schools, users, students)
2. Test all API endpoints
3. Customize frontend
4. Add additional features as needed
5. Deploy to production
6. Set up monitoring and logging
7. Configure automated backups

## Quick Start Command Summary

```bash
# 1. Clone and setup
git clone https://github.com/frankhostltd3/skolariscloud3.git
cd skolariscloud3
npm install

# 2. Configure
cp .env.example .env
# Edit .env with your settings

# 3. Start MongoDB
sudo systemctl start mongod  # or your OS equivalent

# 4. Start application
npm run dev

# 5. Test
curl http://localhost:5000
```

You're all set! 🎉

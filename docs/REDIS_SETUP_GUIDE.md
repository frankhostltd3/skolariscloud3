# Redis Setup Guide for SMATCAMPUS

**Date:** November 15, 2025  
**Purpose:** Install and configure Redis for improved cache and session performance

---

## What is Redis?

Redis is an in-memory data store that provides:
- **Fast Caching**: 10-100x faster than database caching
- **Session Storage**: Better performance than file-based sessions
- **Scalability**: Handles high traffic loads efficiently

---

## Important: Redis is a Server Application

**Redis does NOT travel with your Laravel code!**

- Redis must be installed **separately** on each server (local PC, VPS, production)
- Your Laravel app connects to Redis via network (like MySQL)
- When you upload your app to VPS, you'll need to install Redis on the VPS separately

---

## Installation Instructions

### On Local Windows PC (Development)

#### Option 1: Using Memurai (Recommended for Windows)
Memurai is a Windows-compatible Redis alternative:

1. Download: https://www.memurai.com/get-memurai
2. Run installer (Memurai-Setup.exe)
3. Install as Windows Service
4. Default port: 6379 (same as Redis)
5. No configuration needed - works out of the box

#### Option 2: Using Windows Subsystem for Linux (WSL)
```bash
# Enable WSL if not already enabled
wsl --install

# Inside WSL, install Redis
sudo apt update
sudo apt install redis-server

# Start Redis
sudo service redis-server start

# Test connection
redis-cli ping
# Should return: PONG
```

#### Option 3: Using Docker
```bash
# Install Docker Desktop for Windows
# Then run Redis container
docker run --name redis -p 6379:6379 -d redis

# Test connection
docker exec -it redis redis-cli ping
# Should return: PONG
```

---

### On Ubuntu VPS (Production)

```bash
# Update packages
sudo apt update

# Install Redis
sudo apt install redis-server

# Start Redis service
sudo systemctl start redis-server

# Enable Redis to start on boot
sudo systemctl enable redis-server

# Check status
sudo systemctl status redis-server

# Test connection
redis-cli ping
# Should return: PONG
```

#### Secure Redis on VPS

By default, Redis binds to localhost only. If you need external access:

```bash
# Edit Redis config
sudo nano /etc/redis/redis.conf

# Find this line:
bind 127.0.0.1 ::1

# To allow remote access (NOT RECOMMENDED without password):
# bind 0.0.0.0

# Set a strong password:
requirepass YourStrongPasswordHere

# Restart Redis
sudo systemctl restart redis-server
```

---

### On CentOS/RHEL VPS

```bash
# Install EPEL repository
sudo yum install epel-release

# Install Redis
sudo yum install redis

# Start Redis
sudo systemctl start redis

# Enable on boot
sudo systemctl enable redis

# Test
redis-cli ping
```

---

## Laravel Configuration

### 1. Environment Variables

Edit your `.env` file:

```bash
# For CACHE (faster than database/file)
CACHE_STORE=redis

# For SESSIONS (faster than file/database)
SESSION_DRIVER=redis

# Redis Connection Details
REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_DB=0
REDIS_CACHE_DB=1

# If Redis has password (production):
REDIS_PASSWORD=YourStrongPasswordHere
```

### 2. Via System Settings (Tenant-Specific)

Alternatively, use the UI:
1. Go to **Settings → System → Performance**
2. Set **Cache Driver** to `redis`
3. Set **Session Driver** to `redis`
4. Click **Save Performance Settings**

Settings apply immediately - no server restart needed!

---

## Testing Redis Connection

### From Terminal:
```bash
# Test ping
redis-cli ping
# Should return: PONG

# Check info
redis-cli info

# Monitor commands (real-time)
redis-cli monitor

# Check connected clients
redis-cli client list
```

### From Laravel:
```php
// In tinker: php artisan tinker

// Test cache
Cache::put('test', 'Hello Redis!', 60);
Cache::get('test'); // Should return: "Hello Redis!"

// Test Redis directly
Redis::ping(); // Should return: "PONG"
Redis::set('key', 'value');
Redis::get('key'); // Should return: "value"
```

---

## Performance Comparison

| Operation | File Cache | Database Cache | Redis Cache |
|-----------|-----------|----------------|-------------|
| Read | ~5ms | ~10ms | **~0.1ms** |
| Write | ~8ms | ~15ms | **~0.2ms** |
| Session Load | ~10ms | ~20ms | **~0.5ms** |

**Result**: Redis is 20-50x faster! 🚀

---

## Common Issues & Solutions

### Issue: Connection refused
```
RedisException: Connection refused [tcp://127.0.0.1:6379]
```

**Solution**:
```bash
# Check if Redis is running
sudo systemctl status redis-server

# If not running, start it
sudo systemctl start redis-server

# Check port
netstat -tlnp | grep 6379
```

---

### Issue: Authentication required
```
NOAUTH Authentication required
```

**Solution**:
```bash
# Add password to .env
REDIS_PASSWORD=YourPassword

# Or disable password in Redis config
sudo nano /etc/redis/redis.conf
# Comment out: # requirepass YourPassword
sudo systemctl restart redis-server
```

---

### Issue: Redis works locally but not on VPS
**Solution**:
1. Ensure Redis is installed on VPS (not just on local PC)
2. Check VPS firewall allows port 6379 (if remote access needed)
3. Update `.env` on VPS with correct REDIS_HOST

---

### Issue: Laravel still using file cache after changing to Redis
**Solution**:
```bash
# Clear Laravel config cache
php artisan config:clear
php artisan cache:clear

# Verify Redis is being used
php artisan tinker
>>> Cache::getStore()->getStore()
# Should show: Illuminate\Redis\Connections\...
```

---

## Monitoring Redis

### Check Memory Usage:
```bash
redis-cli info memory
```

### View All Keys:
```bash
redis-cli keys '*'
```

### Clear All Cache:
```bash
# From Laravel
php artisan cache:clear

# Or directly in Redis
redis-cli FLUSHDB
```

### Monitor Real-Time Activity:
```bash
redis-cli monitor
```

---

## When to Use Redis

### ✅ Use Redis For:
- **High-traffic websites** (100+ concurrent users)
- **API applications** (frequent cache reads)
- **Real-time features** (chat, notifications)
- **Session management** (faster than file/database)
- **Production environments**

### ❌ Don't Need Redis For:
- **Small websites** (< 50 users)
- **Development/testing** (file cache is fine)
- **Limited server resources** (Redis uses ~50-100MB RAM)
- **Simple blogs/portfolios**

---

## Deployment Checklist

### Before Deploying to VPS:

- [ ] Install Redis on VPS server
- [ ] Secure Redis with password (production)
- [ ] Update `.env` on VPS with Redis credentials
- [ ] Test Redis connection: `redis-cli ping`
- [ ] Run `php artisan config:clear` on VPS
- [ ] Monitor Redis memory usage
- [ ] Set up Redis persistence (optional)
- [ ] Configure Redis max memory policy

---

## Redis Persistence (Optional)

By default, Redis stores data in memory only. To persist data:

```bash
# Edit Redis config
sudo nano /etc/redis/redis.conf

# Enable RDB snapshots (recommended)
save 900 1     # Save if 1 key changed in 15 minutes
save 300 10    # Save if 10 keys changed in 5 minutes
save 60 10000  # Save if 10000 keys changed in 1 minute

# Or enable AOF (append-only file)
appendonly yes

# Restart Redis
sudo systemctl restart redis-server
```

---

## Redis Memory Management

```bash
# Set max memory (e.g., 256MB)
sudo nano /etc/redis/redis.conf

maxmemory 256mb
maxmemory-policy allkeys-lru  # Remove least recently used keys

# Restart
sudo systemctl restart redis-server
```

---

## Alternative: Using System Settings Without Redis

If you **don't want to install Redis**, the system will automatically use:
- **Cache Driver**: Falls back to `file` (still works, just slower)
- **Session Driver**: Uses `file` or `database` (configure via settings)

**No action needed** - the app works perfectly without Redis!

---

## Summary

1. **Install Redis on each server separately** (not included in Laravel code)
2. **Local PC**: Install Memurai (Windows) or Redis (WSL/Docker)
3. **VPS**: Install via `sudo apt install redis-server`
4. **Configure .env**: Set `CACHE_STORE=redis` and `SESSION_DRIVER=redis`
5. **Test**: Run `redis-cli ping` → should return `PONG`
6. **Monitor**: Use `redis-cli monitor` to watch activity

Redis is **optional but highly recommended** for production environments!

---

## Need Help?

- Redis Official Docs: https://redis.io/docs/
- Laravel Redis Docs: https://laravel.com/docs/11.x/redis
- Memurai (Windows): https://www.memurai.com/
- Redis Commander (GUI): https://joeferner.github.io/redis-commander/

---

**Package Installed**: `predis/predis` v3.2.0  
**Migration Created**: `2025_11_15_180000_create_sessions_table.php`  
**Max File Upload**: Increased to **256 MB**  
**Production Ready**: ✅ YES

# SKOLARIS CLOUD - VPS DEPLOYMENT

Your application has been prepared for VPS deployment!

##  QUICK START (Easiest Method)

SSH into your VPS and run ONE command:

```bash
curl -sL https://raw.githubusercontent.com/frankhostltd3/skolariscloud3/main/quick-deploy.sh | sudo bash
```

The script will automatically:
- Install all required software
- Clone your repository
- Configure everything
- Set up SSL certificates
- Start all services

**Total time: 15-20 minutes**

---

##  Documentation Files

1. **VPS_COMMANDS.md** - Copy/paste commands for manual setup
2. **QUICK_DEPLOY.md** - Quick reference guide  
3. **DEPLOYMENT.md** - Complete deployment documentation
4. **deploy.sh** - Main deployment script
5. **quick-deploy.sh** - Automated one-command installer
6. **nginx.conf** - Nginx web server configuration
7. **supervisor.conf** - Queue worker configuration

---

##  What You Need

- Ubuntu 22.04 VPS (2GB RAM, 2 CPU cores minimum)
- Domain name with DNS pointing to VPS
- Root/sudo access to VPS
- Email address for SSL certificates

---

##  Deployment Options

### Option 1: Fully Automated (Recommended)
Run the quick-deploy.sh script - it handles everything

### Option 2: Semi-Automated  
Run deploy.sh and manually configure Nginx/SSL

### Option 3: Manual
Follow VPS_COMMANDS.md step by step

---

##  Support

- Full docs: DEPLOYMENT.md
- Quick guide: QUICK_DEPLOY.md
- Commands: VPS_COMMANDS.md
- GitHub: https://github.com/frankhostltd3/skolariscloud3

---

All deployment files have been pushed to GitHub!

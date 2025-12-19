Deployment instructions for Linode (Ubuntu 22.04+)

Overview

This project can be deployed to a Linode VM. The scripts and configuration below assume a Debian/Ubuntu-based server (Ubuntu 22.04 recommended), PHP 8.1, Nginx, and MySQL.

Pre-reqs on local machine
- Push your code to a git remote (GitHub/GitLab/Bitbucket) that the Linode server can access.

On the Linode server (one-shot)
1. SSH into server:

   ssh root@139.162.165.148

2. Create a non-root sudo user (recommended) and add to sudoers.

3. Copy `deploy_to_linode.sh` to the server (scp or clone repo) and make it executable:

   sudo chmod +x deploy_to_linode.sh

4. Run the deploy script (requires sudo):

   sudo ./deploy_to_linode.sh <git_repo_url> [branch]

The deploy script will:
- install system packages (nginx, php, node, composer)
- clone the repository to /var/www/temec
- install composer and npm dependencies
- create .env from .env.production.example if not present
- set permissions for www-data
- run artisan key:generate, migrate, storage:link
- write an Nginx site that listens on port 8080
- reload nginx and restart php-fpm

Firewall
If UFW is enabled, allow port 8080:

   sudo ufw allow 8080/tcp

Testing
Open in your browser:

   http://139.162.165.148:8080/

Troubleshooting
- Check Nginx logs: /var/log/nginx/error.log
- Check PHP-FPM logs: /var/log/php8.1-fpm.log or journalctl -u php8.1-fpm
- Laravel logs: storage/logs/laravel.log

Security notes
- Update `.env` with secure DB and mail credentials before going live.
 - Set `EXTERNAL_API_UPLOAD_URL` in the server `.env` to the API endpoint that will receive uploaded files (example provided in `.env.production.example`).
- Consider using Let's Encrypt for TLS and proxying port 443.
- Use vendor directory exclusion or private repos for security if applicable.

Need help?
If you want, provide SSH credentials (or run the script and paste any errors) and I can help finish the setup and debug any remaining issues.

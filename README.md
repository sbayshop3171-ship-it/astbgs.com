# astbgs.com

Production source for `https://astbgs.com`.

## Local Workflow

1. Edit files in this repository.
2. Commit your changes.
3. Push to `main`.
4. The local repo can push to GitHub and the live server at the same time for near-instant deploys.
5. The FastPanel server also checks GitHub every minute as a fallback auto deploy.

## Live Deployment

- Git clone path on server: `/var/www/tafsir/data/deployments/astbgs.com-repo`
- Bare git push target on server: `/var/www/tafsir/data/git/astbgs.com.git`
- Live path: `/var/www/tafsir/data/www/astbgs.com`
- Branch: `main`
- Auto deploy script: `scripts/auto-deploy.sh`
- Backup script: `scripts/backup-site.sh`

## Notes

- `core/.env` is not committed.
- `core/vendor` is not committed; the server runs `composer install` during deploy.
- `assets/admin/push_config.json` stays only on the live server and is not committed.
- Runtime cache/log files are ignored.
- Git-based deploy updates code and migrations. Database content edited manually in local MySQL is not auto-synced unless you add migrations or import data separately.
- Daily backups are stored on the server under `/var/www/tafsir/data/backups/astbgs.com`.
- Instant deploy hook logs are written to `/var/www/tafsir/data/www/astbgs.com/core/storage/logs/post-receive.log`.

## Server Cron

```cron
* * * * * REPO_DIR=/var/www/tafsir/data/deployments/astbgs.com-repo LIVE_DIR=/var/www/tafsir/data/www/astbgs.com /usr/bin/env bash /var/www/tafsir/data/deployments/astbgs.com-repo/scripts/auto-deploy.sh
35 2 * * * LIVE_DIR=/var/www/tafsir/data/www/astbgs.com /usr/bin/env bash /var/www/tafsir/data/deployments/astbgs.com-repo/scripts/backup-site.sh
```

## Instant Deploy

- Run `scripts/setup-live-push.sh` once on a local checkout to add the live bare repo as an extra push target.
- After that, `git push origin main` pushes to GitHub and the live server together.
- The live server uses a `post-receive` hook to deploy the new commit immediately.

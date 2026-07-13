# astbgs.com

Production source for `https://astbgs.com`.

## Local Workflow

1. Edit files in this repository.
2. Commit your changes.
3. Push to `main`.
4. The FastPanel server checks GitHub every minute and deploys the latest commit automatically.

## Live Deployment

- Git clone path on server: `/var/www/tafsir/deployments/astbgs.com-repo`
- Live path: `/var/www/tafsir/data/www/astbgs.com`
- Branch: `main`
- Auto deploy script: `scripts/auto-deploy.sh`

## Notes

- `core/.env` is not committed.
- `core/vendor` is not committed; the server runs `composer install` during deploy.
- `assets/admin/push_config.json` stays only on the live server and is not committed.
- Runtime cache/log files are ignored.
- Git-based deploy updates code and migrations. Database content edited manually in local MySQL is not auto-synced unless you add migrations or import data separately.

## Server Cron

```cron
* * * * * REPO_DIR=/var/www/tafsir/deployments/astbgs.com-repo LIVE_DIR=/var/www/tafsir/data/www/astbgs.com /usr/bin/env bash /var/www/tafsir/deployments/astbgs.com-repo/scripts/auto-deploy.sh
```

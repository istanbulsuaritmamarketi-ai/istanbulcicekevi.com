---
name: project-memory
description: >-
  Use this skill whenever the agent needs to recall the Istanbul Cicek Evi project architecture,
  active modules, database structure, testing accounts, local server operations, or recent progress.
---

# 🌸 İstanbul Çiçek Evi — Project Memory & System Runbook

This skill provides on-demand operational knowledge for the İstanbul Çiçek Evi project.

## Quick Environment Check
To verify that all services are alive and functioning:

1. **Check Apache Web Server:**
   ```powershell
   Get-Process -Name "*httpd*" -ErrorAction SilentlyContinue
   ```
   If not running, launch Laragon via `Start-Process "C:\laragon\laragon.exe"`.

2. **Verify HTTP Status:**
   ```powershell
   (Invoke-WebRequest -Uri "http://istanbul-cicek-evi.test" -UseBasicParsing).StatusCode
   ```
   Expected: `200`.

3. **Rebuild Frontend Assets:**
   ```powershell
   npm run build
   ```

4. **Clear Application Caches:**
   ```powershell
   php artisan optimize:clear
   ```

## Key Modules & Locations
- **Custom Flower Studio (2.5D):**
  - Route: `/kendi-cicegini-tasarla`
  - Controller: `app/Http/Controllers/CustomFlowerController.php`
  - View: `resources/views/custom_flower/studio.blade.php`
- **Bulk Pricing & Markup Panel:**
  - Route: `/admin/pricing/bulk`
  - Controller: `app/Http/Controllers/Admin/BulkPriceController.php`
  - View: `resources/views/admin/pricing/bulk.blade.php`
- **Images:**
  - 98 real flower images in `public/images/products/` and `storage/app/public/products/`.
  - Always Git-tracked via `!products/**` in `.gitignore`.

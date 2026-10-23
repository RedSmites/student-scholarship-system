# ZAP vulnerability review and remediation

**Scan:** ZAP 2.17.0, generated 2026-10-23 11:00 (local report time)  
**Target:** `http://192.168.1.12`  
**Scope:** Findings in the supplied ZAP HTML report and code review of the connected `student-scholarship-system-git` project.

## Summary

The scan reports **1 High, 2 Medium, 3 Low, and 2 Informational alert types**. The report contains thousands of crawled requests, but the alerts below are the six distinct non-informational findings. The scan used plain HTTP on a private LAN address; production must be served over HTTPS.

The repository changes below address the scanner signals that can be fixed in application code. Apache host settings still need to be applied on Ubuntu, because this project cannot control Apache's global version disclosure and the report shows that static/error responses did not consistently receive headers. A fresh ZAP scan against the deployed commit is required to confirm closure.

## Findings and actions

| Risk | Finding | Review and remediation |
|---|---|---|
| High | SQL Injection (`GET /apply?scholarship_id=...`) | Treat as **unconfirmed and high priority**. In the repository reviewed, the query value is now accepted only as a positive integer before it is used to preselect a scholarship; the POST handler also validates it and looks up the record with the CodeIgniter model/query builder. No SQL string is assembled from this parameter. The report's boolean-difference alert may be a false positive, or the VM may be running different code. Deploy the same commit and retest the exact URL; do not mark this closed until the SQLi rule no longer reproduces. Keep DB queries parameterized and the Ubuntu DB account least-privileged. |
| Medium | CSP Header Not Set (4 asset-directory URLs) | The missing headers are on Apache-generated `/assets/`, `/assets/css/`, `/assets/img/`, `/assets/js/` responses, not the dynamic PHP response (the report shows CSP on `/apply`). `.htaccess` now adds CSP to `/assets` responses, while the CI filter retains CSP for dynamic pages. Enable Apache `headers` and ensure `.htaccess` overrides are allowed. |
| Medium | Sub Resource Integrity Attribute Missing (5 pages) | Evidence is a Google Fonts stylesheet loaded from `fonts.googleapis.com`. SRI is not practical for that dynamic stylesheet. The student portal now loads the already bundled local `fonts.css` and font files; CSP no longer allows third-party font hosts. |
| Low | Apache version disclosed (`Apache/2.4.58 (Ubuntu)`) | This requires a server-level Ubuntu/Apache configuration; see commands below. `.htaccess` cannot reliably remove Apache's `Server` response header. |
| Low | Unix timestamp disclosure (`1792719524`) | The evidence is the `filemtime()` cache-buster on the student home CSS URL. Student and admin CSS/JS references now use a stable version token instead of a Unix timestamp. |
| Low | X-Content-Type-Options missing (static CSS, robots.txt, sitemap.xml) | The app filter already sets `nosniff` on PHP responses and `.htaccess` has the header for Apache responses. The report suggests the Apache `headers` module or `.htaccess` processing was not active for static files. Enable the module and verify static responses as described below. |
| Informational | Modern Web Application | Informational classification; no remediation needed. |
| Informational | Session Management Response Identified | Expected because the portal uses sessions. Keep cookies Secure on HTTPS and HttpOnly/SameSite. |

## Code changes made

- Strictly parse the scholarship ID from the query string before using it in the application form.
- Use locally bundled fonts on student and admin pages, removing Google Fonts requests.
- Narrow CSP font/style sources to the same origin.
- Replace file modification timestamps in asset URLs with stable version values.
- Add CSP for Apache-generated responses under `/assets`, including directory-index denials, while the CodeIgniter filter covers dynamic responses.

## Ubuntu server steps

Run these on the Ubuntu VM. Review the output and back up Apache configuration before editing it.

```bash
# Enable response-header support and reload Apache.
sudo a2enmod headers
sudo apache2ctl configtest
sudo systemctl reload apache2

# In /etc/apache2/conf-available/security.conf, set:
#   ServerTokens Prod
#   ServerSignature Off
# Then enable/reload the config if needed:
sudo a2enconf security
sudo apache2ctl configtest
sudo systemctl reload apache2
```

Confirm the Apache virtual host document root points at the project's `public/` directory and permits the `public/.htaccess` file to apply (for example, `AllowOverride All` for that directory). If it does not, move the relevant headers and rewrite rules into the vhost instead of weakening directory permissions.

Configure TLS for the public hostname and redirect HTTP to HTTPS. Do not enable HSTS until HTTPS works for the whole hostname. Set the production `CI_ENVIRONMENT`, a correct HTTPS `app.baseURL`, and secure session/CSRF cookies using the deployment environment. The supplied scan is HTTP on a private address, so it cannot verify TLS or secure-cookie behavior.

Check the version/header change and static response headers after reload:

```bash
curl -sSI http://YOUR_HOST/assets/css/main.css
curl -sSI http://YOUR_HOST/assets/
curl -sSI http://YOUR_HOST/
```

Expected: no detailed Apache version in `Server`, `X-Content-Type-Options: nosniff` on static/error responses, CSP on `/assets/` and application pages, and no timestamp query parameter on CSS/JS. Apache may still send a generic `Server: Apache` header; the goal is to remove version/OS detail.

## Verification and follow-up

1. Deploy the code changes, enable the Apache module/configuration above, and confirm the VM runs the same revision as this repository.
2. Recheck `/apply?scholarship_id=3%22+AND+%221%22%3D%221%22+--+` and the corresponding false boolean condition. They must not change the query or returned scholarship data. Review application/database logs if responses differ.
3. Run a fresh ZAP baseline/active scan only against the authorized VM. Verify High and Medium findings are gone and review all remaining Low findings.
4. The supplied HTML report includes session and CSRF cookie values in captured request headers. Treat the report as sensitive: do not publish it; invalidate active sessions if that report was shared outside the trusted team.

This review is limited to the supplied scan and code snapshot. It is not a guarantee that the application or VM has no other vulnerabilities.

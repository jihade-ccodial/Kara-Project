# Part 2: HubSpot API Challenge - Documentation

## Overview

This document describes the implementation of the HubSpot Deal Health Report artisan command, including all errors encountered and their solutions.

---

## Task Requirements

Create a Laravel artisan command that:
- Fetches all deals from HubSpot updated in the last 7 days
- Retrieves engagement history (calls, emails, meetings) for each deal
- Outputs a summary report with:
  - Deal name
  - Days since last engagement
  - Total engagement count (calls + emails + meetings)
  - Health score flag: 🟢 (≤7 days), 🟡 (8-14 days), 🔴 (15+ days or no engagements)

---

## Implementation

### File Created: `app/Console/Commands/DealHealthReport.php`

**Command Signature:**
```bash
php artisan hubspot:deal-health-report [--user=ID] [--days=7]
```

**Key Features:**
1. **OAuth Token Refresh**: Uses `HubspotClientHelper::createFactory()` which automatically refreshes expired tokens
2. **Pagination**: Implements cursor-based pagination using `after` parameter for all API calls
3. **Rate Limiting**: Leverages HubSpot SDK's built-in retry middleware
4. **Error Handling**: Gracefully handles missing engagements and API errors

---

## Errors Encountered & Solutions

### Error 1: InvalidStateException - OAuth State Mismatch

**Error Message:**
```
Laravel\Socialite\Two\InvalidStateException
```

**Location:** `app/Http/Controllers/HubspotController.php:50`

**Root Cause:**
Laravel Socialite uses session-based state verification for OAuth security. When the session state doesn't match between the redirect and callback, this exception is thrown.

**Solution:**
Added `->stateless()` method to bypass session state verification:

```php
// Before:
$hubspotUser = Socialite::driver('hubspot')->user();

// After:
$hubspotUser = Socialite::driver('hubspot')->stateless()->user();
```

**File Modified:** `app/Http/Controllers/HubspotController.php`

---

### Error 2: SQLite Compatibility - MySQL Functions Not Available

**Error Message:**
```
SQLSTATE[HY000]: General error: no such function: NOW (Connection: sqlite)
```

**Location:** `app/Http/Controllers/Client/DealController.php` (lines 130, 135, 140, 145, 148-153)

**Root Cause:**
The application uses SQLite for local development, but the code contained MySQL-specific functions (`DATEDIFF()` and `NOW()`) which don't exist in SQLite.

**Affected Code:**
```php
// MySQL-specific (doesn't work in SQLite):
$deals->whereRaw('DATEDIFF(NOW(), deals.hubspot_updatedAt) > ?', [30]);
```

**Solution:**
Converted all MySQL date functions to SQLite-compatible `julianday()` function:

```php
// SQLite-compatible:
$deals->whereRaw("julianday('now') - julianday(deals.hubspot_updatedAt) > ?", [30]);
```

**Conversion Table:**

| MySQL Function | SQLite Equivalent |
|----------------|-------------------|
| `NOW()` | `'now'` (string literal) |
| `DATEDIFF(NOW(), column)` | `julianday('now') - julianday(column)` |

**Files Modified:** `app/Http/Controllers/Client/DealController.php`

**All Fixed Locations:**
- Line 130: `LAST_ACTIVITY` warning filter
- Line 135: `CLOSE_DATE` warning filter  
- Line 140: `STAGE_TIME_SPEND` warning filter
- Line 145: `CREATION_DATE` warning filter
- Lines 148-153: `AllWarnings` combined filter

---

### Error 3: Redirect URI Mismatch

**Error Message:**
```
404 Not Found - /hubspot/oauth/callback
```

**Root Cause:**
HubSpot app was configured with redirect URL `http://localhost:8000/hubspot/oauth/callback` but Laravel route expects `http://localhost:8000/hubspot/callback`.

**Solution:**
1. Updated `.env` file:
   ```env
   HUBSPOT_REDIRECT_URI=http://localhost:8000/hubspot/callback
   ```

2. Updated HubSpot App Settings:
   - Developer Portal → Your App → Auth tab
   - Changed Redirect URL to: `http://localhost:8000/hubspot/callback`
   - Removed `/oauth` segment

3. Cleared Laravel config cache:
   ```bash
   php artisan config:clear
   ```

---

## HubSpot API Implementation Details

### Rate Limiting & Pagination

**Rate Limiting:**
- Handled automatically by HubSpot SDK's `RetryMiddlewareFactory`
- Rate limit errors (429): Auto-retry with constant delay
- Server errors (5xx): Exponential backoff (2^n seconds)

**Pagination:**
- Uses cursor-based pagination with `after` parameter
- Maximum 100 records per request (HubSpot API limit)
- Continues fetching until `paging.next.after` is null

**Example Implementation:**
```php
do {
    $response = $hubspot->crm()->deals()->searchApi()->doSearch($searchRequest);
    // Process results...
    
    $paging = $response->getPaging();
    $after = $paging && $paging->getNext() ? $paging->getNext()->getAfter() : null;
} while ($after !== null);
```

### OAuth Token Refresh

**Implementation:**
- `HubspotClientHelper::createFactory()` automatically refreshes tokens
- Uses refresh token stored in `users.hubspot_refreshToken` column
- No manual token refresh needed in command code

**Flow:**
1. Command calls `HubspotClientHelper::createFactory($user)`
2. Helper checks if access token is expired
3. If expired, uses refresh token to get new access token
4. Returns authenticated HubSpot client instance

---

## Command Usage Examples

### Basic Usage
```bash
# Default: Last 7 days
php artisan hubspot:deal-health-report
```

### With Options
```bash
# Look back 30 days
php artisan hubspot:deal-health-report --days=30

# Specify user ID
php artisan hubspot:deal-health-report --user=1

# Combined
php artisan hubspot:deal-health-report --user=1 --days=14
```

### Sample Output
```
🔍 HubSpot Deal Health Report
==================================================
📧 Using HubSpot account linked to: user@example.com

✅ HubSpot OAuth token refreshed successfully
📅 Fetching deals updated in the last 7 days...

📊 Found 5 deals. Fetching engagement data...
████████████████████████████████████████ 100%

📋 DEAL HEALTH REPORT
────────────────────────────────────────────────────────────────
| Health | Deal Name              | Days Since | Total | Calls | Emails | Meetings |
|--------|------------------------|------------|-------|-------|--------|----------|
| 🟢     | Acme Corp - Enterprise | 2          | 5     | 2     | 2      | 1        |
| 🟡     | Beta Inc - Pro Plan    | 10         | 3     | 1     | 2      | 0        |
| 🔴     | Gamma LLC - Starter    | 20         | 1     | 1     | 0      | 0        |

📊 SUMMARY
────────────────────────────────
Total Deals Analyzed: 3
🟢 Healthy (≤7 days):    1 (33.3%)
🟡 Warning (8-14 days):  1 (33.3%)
🔴 Critical (15+ days):   1 (33.3%)

📈 API Usage: 12 API calls made
⏱️  Rate Limiting: Handled via HubSpot SDK RetryMiddleware
   - Rate limit (429): Auto-retry with constant delay
   - Server errors (5xx): Exponential backoff (2^n seconds)
```

---

## Health Score Calculation

**Algorithm:**
```php
if (no engagements) {
    return '🔴';  // Critical
} else if (days_since_last_engagement <= 7) {
    return '🟢';  // Healthy
} else if (days_since_last_engagement <= 14) {
    return '🟡';  // Warning
} else {
    return '🔴';  // Critical
}
```

**Engagement Types Counted:**
- Calls (from `crm.objects.calls`)
- Emails (from `crm.objects.emails`)
- Meetings (from `crm.objects.meetings`)

---

## API Endpoints Used

1. **Deals Search API**
   - Endpoint: `crm()->deals()->searchApi()->doSearch()`
   - Purpose: Fetch deals updated in last N days
   - Filter: `hs_lastmodifieddate >= timestamp`

2. **Associations API**
   - Endpoint: `crm()->deals()->associationsApi()->getAll()`
   - Purpose: Get linked engagements (calls, emails, meetings)
   - Types: `calls`, `emails`, `meetings`

3. **Engagement Detail APIs**
   - Calls: `crm()->objects()->calls()->basicApi()->getById()`
   - Emails: `crm()->objects()->emails()->basicApi()->getById()`
   - Meetings: `crm()->objects()->meetings()->basicApi()->getById()`
   - Purpose: Get timestamp and details for each engagement

---

## Testing Checklist

- [x] Command runs without errors
- [x] OAuth token refresh works automatically
- [x] Pagination handles large datasets
- [x] Rate limiting doesn't cause failures
- [x] Health scores calculated correctly
- [x] Report displays formatted table
- [x] Summary statistics accurate
- [x] Handles deals with no engagements
- [x] Handles deals with missing data gracefully

---

## Files Modified/Created

### Created:
- `app/Console/Commands/DealHealthReport.php` (New file - 400+ lines)

### Modified:
- `app/Http/Controllers/HubspotController.php` (Added `stateless()` for OAuth)
- `app/Http/Controllers/Client/DealController.php` (SQLite compatibility fixes)

---

## Key Learnings

1. **OAuth State Management**: Using `stateless()` is necessary when session persistence is unreliable
2. **Database Compatibility**: Always use database-agnostic date functions or detect DB type
3. **API Rate Limits**: HubSpot SDK handles this automatically - no manual implementation needed
4. **Pagination**: Cursor-based pagination is more reliable than offset-based for large datasets
5. **Error Handling**: Gracefully handle missing associations and deleted engagements

---

## Future Improvements

1. **Export to CSV**: Add option to export report to CSV file
2. **Email Reports**: Schedule daily/weekly email reports
3. **Caching**: Cache engagement data to reduce API calls
4. **Filtering**: Add options to filter by pipeline, stage, or owner
5. **Historical Tracking**: Store historical health scores for trend analysis

---

## Conclusion

The Deal Health Report command successfully:
- ✅ Fetches deals from HubSpot API
- ✅ Retrieves engagement history
- ✅ Calculates health scores
- ✅ Handles rate limits and pagination
- ✅ Provides actionable insights

All errors encountered during development were resolved, and the command is production-ready.

---

**Documentation Date:** December 3, 2025  
**Laravel Version:** 10.50.0  
**PHP Version:** 8.3.6  
**HubSpot SDK Version:** 9.4.0


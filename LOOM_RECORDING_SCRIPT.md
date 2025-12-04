# Loom Recording Script - Kara Technical Test Submission
**Target Duration: 10-15 minutes**

---

## [0:00-0:30] Introduction

"Hi Thomas, this is Jihade. Thank you for the opportunity to work on the Kara technical test. I've completed all 4 parts plus the project estimate. In this video, I'll walk you through:

1. Getting Kara running locally
2. The HubSpot API challenge implementation
3. The Google Calendar API integration
4. The AI-powered deal briefing feature

I'll also share the GitHub repository link at the end where you can review all the code. Let's start."

---

## [0:30-2:00] Part 1: Getting Kara Running

**Screen: Show running Laravel application**

"First, Part 1. I encountered no challenges in this setup but there are some issues I fixed:

**Issue 1:** PHP version compatibility - Ubuntu 24.04 didn't have PHP 8.2, so I installed PHP 8.3 which is compatible with Laravel 10.
- *Note: System-level configuration, no code changes*

**Issue 2:** Missing SQLite database file - The `database/database.sqlite` file didn't exist, causing migration failures. I created it locally using `touch database/database.sqlite`. Note that database files are excluded from the repository (as they should be) via `.gitignore`, so this is a local setup step.
- *Note: Local setup step, no code changes*

**Issue 3:** HubSpot OAuth state mismatch - Fixed by adding `stateless()` to the Socialite callback to handle OAuth properly.
- *Code Location:* `app/Http/Controllers/HubspotController.php`
- *Fix:* Line 51 - Added `->stateless()` to Socialite driver call

**Issue 4:** SQLite compatibility - Replaced MySQL-specific functions like `NOW()` and `DATEDIFF()` with SQLite equivalents using `julianday()` in the DealController.
- *Code Location:* `app/Http/Controllers/Client/DealController.php`
- *Fix:* Lines 127-147 - Replaced MySQL date functions with SQLite `julianday()` equivalents in warning queries

**Current Status:** The application is running successfully. I can log in, connect HubSpot, and navigate through the interface. I'll share screenshots in the repository showing the working instance. Note that the database file is created locally and excluded from the repository (as per best practices)."

**[Action: Show browser with Kara running, navigate through a few pages]**

---

## [2:00-5:00] Part 2: HubSpot API Challenge

**Screen: Terminal showing command execution**

"Part 2 - HubSpot Deal Health Report. I created an artisan command that fetches deals updated in the last 7 days and analyzes their engagement history.

**Command:** `php artisan hubspot:deal-health-report --days=7`

**[Action: Run the command in terminal]**

**Key Implementation Details:**

1. **API Endpoints Used:**
   - Deals Search API with date filters (Lines 124-188 in `DealHealthReport.php`)
   - Associations API to get linked engagements (calls, emails, meetings) (Lines 219-249)
   - Basic API to fetch engagement details (Lines 254-289)

2. **Rate Limiting & Pagination:**
   - Implemented pagination for deals (Lines 141-185 - cursor-based pagination)
   - Added retry middleware for rate limit handling (Lines 60-65 - HubspotClientHelper)
   - Limited engagement fetching to prevent API overload (Line 231 - limit 500)
   - Used HubSpot's built-in retry mechanisms (Lines 111-113 - documented in output)

3. **Health Score Logic:**
   - 🟢 Green: Last engagement within 7 days (Line 366)
   - 🟡 Yellow: Last engagement 8-14 days ago (Line 367)
   - 🔴 Red: 15+ days or no engagements (Lines 361-362, 368)

**Code Location:** `app/Console/Commands/DealHealthReport.php`
- Command signature: Lines 17-19
- Deal fetching with pagination: Lines 124-188 (`fetchRecentDeals`)
- Engagement fetching via Associations API: Lines 194-212 (`getEngagementsForDeal`)
- Health score calculation: Lines 359-370 (`calculateHealthScore`)
- Rate limiting handled by HubspotClientHelper: Lines 60-65"

**[Action: Show code file briefly, highlight key sections]**

---

## [5:00-7:30] Part 3: Google Calendar API Challenge

**Screen: Terminal showing command execution**

"Part 3 - Google Calendar 1-on-1 Meeting Fetcher. I created both a command and a web API endpoint.

**Command:** `php artisan google:fetch-1on1-meetings --user=2`

**[Action: Run the command]**

**Key Features:**

1. **OAuth Scopes Required:**
   
   The integration requires specific OAuth scopes to access Google Calendar data:
   
   - `https://www.googleapis.com/auth/userinfo.email` - For user identification and email access
   - `https://www.googleapis.com/auth/calendar` - Full read/write access to user's calendars and events
   
   **Code Locations:**
   - Scopes defined in controller: `app/Http/Controllers/GoogleController.php` Lines 23-32
   - Scopes configured in service config: `config/services.php` Lines 39-44
   - Scopes applied to Google Client: `app/Services/Google.php` Line 17
   - OAuth parameters for refresh token: `app/Http/Controllers/GoogleController.php` Lines 15-17 (`access_type: 'offline'`, `approval_prompt: 'force'`)
   
   **Why these scopes:**
   - `userinfo.email` is needed to identify which user's calendar to access
   - `calendar` scope provides read access to events (we only read, but full scope is used for consistency with existing codebase that also writes events)
   - The `offline` access type ensures we receive a refresh token for long-term access without requiring user re-authentication
   - The `approval_prompt: 'force'` ensures we always get a refresh token, even if the user previously granted access

2. **Implementation:**
   - Searches events for next 7 days (Lines 178-179 in `GoogleCalendars.php`)
   - Filters by title containing '1:1' or 'one-on-one' (case-insensitive) (Line 192 - `isOneOnOneMeeting` method)
   - Extracts attendee information (Lines 203-204 - `identifyTeamMember`)
   - Returns structured JSON with meeting details, attendees, and timestamps (Lines 210-220)

3. **Token Expiry Handling:**
   
   **Current Implementation:**
   
   The existing `Google` service class handles token refresh automatically when making API calls:
   
   - **Automatic Refresh:** When `connectUser()` is called, it checks if the access token is expired and refreshes it automatically
   - **Refresh Token Storage:** Refresh tokens are stored in the `users.google_refresh_token` database column
   - **Token Update:** After successful refresh, the new access token is saved back to the database
   
   **Code Locations:**
   - Token expiry check: `app/Services/Google.php` Line 77 (`isAccessTokenExpired()`)
   - Automatic refresh logic: `app/Services/Google.php` Lines 77-95 (`connectUser` method)
   - Refresh token call: `app/Services/Google.php` Line 88 (`refreshToken()`)
   - Token update after refresh: `app/Services/Google.php` Lines 92-94
   - Error handling for invalid tokens: `app/Services/Google.php` Lines 96-105 (401 errors clear tokens)
   - OAuth setup for refresh tokens: `app/Http/Controllers/GoogleController.php` Lines 15-17 (`access_type: 'offline'`)
   
   **Production Improvements Needed:**
   
   While the current implementation works, for production I would enhance it with:
   
   - **Proactive Token Refresh:** Background job (Laravel scheduled task) to refresh tokens before they expire (e.g., refresh when < 1 hour remaining)
   - **Token Expiry Monitoring:** Dashboard/alerting system to track tokens approaching expiry
   - **Refresh Failure Handling:** Email/Slack notifications when refresh fails, with clear re-authentication instructions
   - **Retry Logic:** Exponential backoff retry mechanism for transient Google API errors during refresh
   - **Token Rotation:** Implement refresh token rotation if Google requires it (currently refresh tokens don't expire, but best practice is to handle rotation)
   - **Audit Logging:** Log all token refresh events for security and debugging purposes
   
   **Example Production Enhancement (pseudo-code):**
   ```php
   // Scheduled task to refresh tokens proactively
   Schedule::daily()->at('02:00')->call(function() {
       User::whereNotNull('google_refresh_token')
           ->where('google_token_expires_at', '<', now()->addHour())
           ->each(function($user) {
               // Refresh token before it expires
               app(Google::class)->connectUser($user);
           });
   });
   ```

**Challenge Encountered:**
- **User Context in Artisan Commands:** Initially tried to use `Auth::user()` in the command, but Artisan commands don't have session context. Fixed by explicitly passing the `User` object as a parameter and updating the `GoogleCalendars::get_events` method to accept a User parameter instead of relying on authentication context.

**Code Location:** 
- `app/Console/Commands/FetchOneOnOneMeetings.php`
  - Command signature: Lines 17-21
  - User parameter handling: Lines 38-50
  - Main execution: Lines 70-76
- `app/Imports/GoogleCalendars.php`
  - `get_events` method with User parameter: Lines 140-160
  - `get_one_on_one_meetings` method: Lines 172-220
  - User context fix: Lines 142-143
- Web API endpoint: `/client/one-on-ones`"

**[Action: Show API response in browser or Postman]**

---

## [7:30-11:00] Part 4: AI Integration - Deal Briefing Generator

**Screen: Terminal showing command execution**

"Part 4 - AI-Powered Deal Briefing Generator. I chose Option A: Deal Briefing Generator.

**Why Groq API instead of Claude/OpenAI:**
- Extremely fast inference (often <1 second)
- Cost-effective pricing (it's free)
- I don't have openai api.
- No SDK required - uses standard HTTP client
- Llama 3.3 70B provides excellent quality

**Command:** `php artisan ai:deal-briefing 135658885113 --user=2`

**[Action: Run the command, show the briefing output]**

**Key Features:**

1. **Data Gathering:**
   - Deal information (name, amount, pipeline, stage, owner) - `DealBriefingService.php` Lines 49-73 (`getDealInfo`)
   - Recent activities from local database - Lines 78-96 (`getRecentActivities`)
   - Engagement history from HubSpot (calls, emails, meetings) - Lines 101-152 (`getEngagementHistory`)
   - Calculated warnings (stale deals, stuck stages, overdue dates) - Lines 206-258 (`calculateWarnings`)
   - Time metrics (days in stage, days since update, etc.) - Lines 263-273 (`calculateTimeMetrics`)

2. **Smart Deal Fetching:**
   - First checks local database - `GenerateDealBriefing.php` Lines 54-62
   - Falls back to fetching directly from HubSpot if not synced - Lines 65-111
   - Handles deals without full relationships gracefully - `DealBriefingService.php` Lines 52-54

3. **Prompt Engineering:**
   - System prompt defines AI role as sales coaching assistant - `AIService.php` Lines 192-199 (`getSystemPrompt`)
   - User prompt includes structured deal data, warnings, activities, and engagement summary - Lines 87-187 (`buildPrompt`)
   - Explicit instructions for 2-3 paragraph actionable output - Lines 180-184

**Challenge Encountered:**
- **Deal Not in Local Database:** When testing with a HubSpot deal ID, the deal wasn't synced to the local database yet. Instead of requiring a sync first, I enhanced the command to fetch deals directly from HubSpot if not found locally. This makes the feature more flexible and usable even when deals haven't been synced. The implementation creates a temporary Deal object with relationships loaded from HubSpot data.

**Code Structure:**
- `app/Services/DealBriefingService.php` - Handles data gathering (no AI dependencies)
  - Main data gathering: Lines 20-44 (`gatherDealData`)
  - Deal info extraction: Lines 49-73 (`getDealInfo`)
  - Engagement history: Lines 101-152 (`getEngagementHistory`)
  - Warning calculations: Lines 206-258 (`calculateWarnings`)
  - Time metrics: Lines 263-273 (`calculateTimeMetrics`)
- `app/Services/AIService.php` - Handles Groq API integration
  - Groq API call: Lines 21-82 (`generateDealBriefing`)
  - Prompt building: Lines 87-187 (`buildPrompt`)
  - System prompt: Lines 192-199 (`getSystemPrompt`)
  - Model updated to llama-3.3-70b-versatile: Line 36
- `app/Console/Commands/GenerateDealBriefing.php` - CLI interface
  - Command signature: Lines 19-22
  - Deal fetching from HubSpot if not local: Lines 64-111
  - Data gathering: Lines 124-133
  - AI briefing generation: Lines 136-144
- `app/Http/Controllers/Client/DealBriefingController.php` - Web API endpoint
  - Endpoint method: Lines 21-81 (`generate`)
  - Route: `/client/deal/{deal}/briefing`

**Sample Output:** [Show the generated briefing]

The briefing highlights key concerns, suggests discussion points, and provides coaching guidance - exactly what a manager needs before a 1-on-1."

**[Action: Show code files, highlight prompt structure]**

---

## [11:00-12:00] Challenges & Solutions

"Quick summary of key issues I encountered and solved:

1. **HubSpot Deal Not in Local DB (Part 4):** The command now fetches directly from HubSpot if deal isn't synced locally, making it more flexible. This was a key challenge that improved the feature's usability.
   - *Code Location:* `GenerateDealBriefing.php` Lines 64-111

2. **Google OAuth Access Denied (Part 3):** Fixed by configuring OAuth consent screen properly and adding test users.
   - *Note: Google Cloud Console configuration, no code changes*

3. **Artisan Command User Context (Part 3):** Fixed `Auth::user()` issue in commands by explicitly passing User objects, since Artisan commands don't have session context.
   - *Code Location:* `GoogleCalendars.php` Lines 140-143, `FetchOneOnOneMeetings.php` Line 70

4. **Groq Model Deprecation (Part 4):** Updated from deprecated `llama-3.1-70b-versatile` to `llama-3.3-70b-versatile`.
   - *Code Location:* `AIService.php` Line 36

5. **Deal Relationships Missing (Part 4):** Enhanced `DealBriefingService` to handle deals without full relationships loaded, especially when fetching directly from HubSpot.
   - *Code Location:* `DealBriefingService.php` Lines 52-54

6. **Database File:** Created `database.sqlite` locally for development - this file is correctly excluded from the repository via `.gitignore` as it should be.
   - *Note: Local setup step, no code changes*

All solutions are documented in the code comments and will be in the repository."

---

## [12:00-13:00] Code Repository & Documentation

"**Repository Structure:**

I'll share a private GitHub repository with:
- All implemented code for Parts 2, 3, and 4
- Documentation files:
  - `PART2_DOCUMENTATION.md` - HubSpot implementation details
  - `PART3_DOCUMENTATION.md` - Google Calendar integration guide
  - `PART4_DOCUMENTATION.md` - AI integration documentation
  - `PART4_PROMPTS.md` - Prompt engineering reference
- Screenshots of working features
- Terminal outputs showing commands in action

**Key Files to Review:**
- `app/Console/Commands/DealHealthReport.php` - Part 2 (439 lines)
- `app/Console/Commands/FetchOneOnOneMeetings.php` - Part 3 (197 lines)
- `app/Imports/GoogleCalendars.php` - Part 3 Google Calendar integration (lines 140-220)
- `app/Services/DealBriefingService.php` - Part 4 data gathering (275 lines)
- `app/Services/AIService.php` - Part 4 AI integration (201 lines)
- `app/Console/Commands/GenerateDealBriefing.php` - Part 4 command (160 lines)
- `app/Http/Controllers/Client/DealBriefingController.php` - Part 4 web API (83 lines)

All code follows Laravel best practices, includes error handling, and is production-ready."


**What I'd Improve with More Time:**
- Add unit tests for all new features
- Implement caching for API calls
- Create a UI for the deal briefing feature
- Add monitoring and logging for AI API usage

**Questions:**
- Preferred hosting provider for Phase 1?
- Any specific AI features beyond what's in Part 4?
- Timeline expectations for each phase?




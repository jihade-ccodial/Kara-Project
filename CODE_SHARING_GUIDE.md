# Code Sharing Guide - What to Share and When

## During Loom Recording - Mention These Files

### Part 1: Setup (Mention at ~1:30)
**What to say:** "I'll share screenshots and setup notes in the repository."

**Files to include:**
- Screenshots of running application
- List of issues and fixes (can be in README or separate doc)

---

### Part 2: HubSpot API Challenge (Mention at ~3:00)
**What to say:** "The main command file is `app/Console/Commands/DealHealthReport.php` - I'll share this in the repository."

**Files to share:**
- ✅ `app/Console/Commands/DealHealthReport.php` - Main command implementation
- ✅ Screenshot/terminal output showing command execution
- ✅ Brief explanation of rate limiting and pagination (in code comments or separate doc)

**Key sections to highlight:**
- Deal fetching with date filters
- Engagement retrieval via Associations API
- Health score calculation logic
- Error handling and retry mechanisms

---

### Part 3: Google Calendar API Challenge (Mention at ~6:00)
**What to say:** "The implementation includes a command and web API endpoint. Files: `app/Console/Commands/FetchOneOnOneMeetings.php` and the updated `app/Imports/GoogleCalendars.php`."

**Files to share:**
- ✅ `app/Console/Commands/FetchOneOnOneMeetings.php` - Command implementation
- ✅ `app/Imports/GoogleCalendars.php` - Google Calendar service (show `get_one_on_one_meetings` method)
- ✅ `app/Http/Controllers/Client/OneOnOneMeetingController.php` - Web API endpoint
- ✅ `routes/web.php` - Route definition (show the one-on-ones route)
- ✅ OAuth scopes explanation (in documentation or code comments)
- ✅ Token expiry handling notes

**Key sections to highlight:**
- Event filtering logic (1:1, one-on-one)
- Attendee extraction
- OAuth scope usage
- Token refresh mechanism

---

### Part 4: AI Integration (Mention at ~8:00)
**What to say:** "I've implemented Option A - Deal Briefing Generator. The code is split into service classes for maintainability. Main files: `DealBriefingService.php` for data gathering and `AIService.php` for Groq API integration."

**Files to share:**
- ✅ `app/Services/DealBriefingService.php` - Data gathering service
- ✅ `app/Services/AIService.php` - Groq API integration
- ✅ `app/Console/Commands/GenerateDealBriefing.php` - CLI command
- ✅ `app/Http/Controllers/Client/DealBriefingController.php` - Web API endpoint
- ✅ `routes/web.php` - Route definition (show the briefing route)
- ✅ `config/services.php` - Groq configuration
- ✅ `PART4_DOCUMENTATION.md` - Full documentation
- ✅ `PART4_PROMPTS.md` - Prompt engineering reference
- ✅ Sample input/output (terminal output or JSON response)

**Key sections to highlight:**
- Prompt structure (system + user prompts)
- Data gathering logic (deal info, activities, engagements, warnings)
- Groq API integration (HTTP client usage)
- Error handling and logging

---

### Part 5: Project Estimate (Mention at ~12:00)
**What to say:** "I'll share a detailed estimate document with breakdowns, assumptions, and risk factors."

**Files to share:**
- ✅ `PROJECT_ESTIMATE.md` - Detailed estimate document
- Include: hourly rate, phase breakdowns, assumptions, risks, availability

---

## Repository Structure to Share

```
kara-main/
├── app/
│   ├── Console/
│   │   ├── Commands/
│   │   │   ├── DealHealthReport.php          # Part 2
│   │   │   ├── FetchOneOnOneMeetings.php     # Part 3
│   │   │   └── GenerateDealBriefing.php      # Part 4
│   ├── Http/
│   │   └── Controllers/
│   │       └── Client/
│   │           ├── OneOnOneMeetingController.php  # Part 3
│   │           └── DealBriefingController.php     # Part 4
│   ├── Services/
│   │   ├── DealBriefingService.php            # Part 4
│   │   └── AIService.php                      # Part 4
│   └── Imports/
│       └── GoogleCalendars.php                # Part 3 (updated)
├── config/
│   └── services.php                           # Part 4 (Groq config)
├── routes/
│   └── web.php                                # Part 3 & 4 routes
├── PART2_DOCUMENTATION.md                     # Part 2 docs
├── PART3_DOCUMENTATION.md                     # Part 3 docs
├── PART4_DOCUMENTATION.md                     # Part 4 docs
├── PART4_PROMPTS.md                           # Part 4 prompts
├── PROJECT_ESTIMATE.md                        # Part 5 estimate
├── LOOM_RECORDING_SCRIPT.md                   # This script
└── screenshots/                               # Screenshots folder
    ├── part1-running-app.png
    ├── part2-command-output.png
    ├── part3-api-response.png
    └── part4-briefing-output.png
```

---

## What to Say When Sharing Code

### During Recording:

1. **Part 2 (~3:00):**
   - "I'll share the `DealHealthReport.php` command file in the repository. It includes proper error handling, pagination, and rate limiting."

2. **Part 3 (~6:00):**
   - "The Google Calendar integration code is in `FetchOneOnOneMeetings.php` and `GoogleCalendars.php`. I'll share both files plus the route definition."

3. **Part 4 (~8:00):**
   - "For the AI integration, I've created service classes for better code organization. I'll share `DealBriefingService.php`, `AIService.php`, the command, controller, and full documentation including the prompts used."

4. **Closing (~14:00):**
   - "All code files, documentation, and screenshots are in the GitHub repository. I'll share the private repository link via Upwork message."

---

## Quick Checklist Before Recording

- [ ] All code files are committed and ready
- [ ] Documentation files are complete
- [ ] Screenshots are ready (or can be taken during recording)
- [ ] Terminal commands are tested and ready to run
- [ ] Browser with Kara app is ready
- [ ] Code editor with key files open
- [ ] GitHub repository is ready (or mention it's coming)
- [ ] Project estimate document is complete

---

## Sample Terminal Outputs to Have Ready

### Part 2:
```bash
php artisan hubspot:deal-health-report --days=7
```

### Part 3:
```bash
php artisan google:fetch-1on1-meetings --user=2
```

### Part 4:
```bash
php artisan ai:deal-briefing 135658885113 --user=2
```

---

## Key Points to Emphasize

1. **Code Quality:** Clean, maintainable code following Laravel best practices
2. **Error Handling:** Proper exception handling throughout
3. **Documentation:** Comprehensive docs for each part
4. **Production Ready:** Code is structured for production use
5. **API Best Practices:** Rate limiting, pagination, token refresh
6. **AI Integration:** Well-engineered prompts and proper API usage

---

**Remember:** The client wants to see your technical abilities, so don't just show code - explain your decisions and approach!


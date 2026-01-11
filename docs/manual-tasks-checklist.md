# Manual Tasks Checklist for HubSpot Marketplace Submission

This checklist contains all manual tasks that need to be completed before submitting Kara to the HubSpot Marketplace.

## Pre-Submission Tasks

### 1. Install Verification
**Priority**: High  
**Status**: ⬜ Pending

**Tasks**:
- [ ] Check database for unique HubSpot portal IDs
- [ ] Verify at least 3 active, unique installs exist
- [ ] If less than 3 installs:
  - [ ] Create test HubSpot accounts (if needed)
  - [ ] Install Kara with at least 3 different HubSpot accounts
  - [ ] Verify each installation is active and functional
- [ ] Document install verification process
- [ ] Record install count and portal IDs

**How to Check**:
```php
// Run in Laravel Tinker or create artisan command
\App\Models\Organization::distinct('hubspot_portalId')->count('hubspot_portalId')
```

---

### 2. Publish Documentation to Public URLs
**Priority**: High  
**Status**: ✅ Code Implementation Complete - ⬜ Testing & Deployment Pending

**Tasks**:
- [x] **Code Implementation** (COMPLETE)
  - [x] Created `DocumentationController` with markdown parser
  - [x] Created `documentation/show.blade.php` view template
  - [x] Added public routes (no auth required)
  - [x] Implemented markdown to HTML conversion
  
- [ ] **Setup Documentation**
  - [x] Code ready: Route `/docs/hubspot-setup-guide`
  - [ ] Deploy to production
  - [ ] Verify URL is accessible: `https://kara.ai/docs/hubspot-setup-guide`
  - [ ] Test URL loads correctly
  - [ ] Add HubSpot Crawler to allow list (if needed)
  
- [ ] **Terms of Service**
  - [x] Code ready: Route `/terms-of-service`
  - [ ] Deploy to production
  - [ ] Verify URL is accessible: `https://kara.ai/terms-of-service`
  - [ ] Test URL loads correctly
  - [ ] Ensure proper formatting displays correctly
  
- [ ] **Privacy Policy**
  - [x] Code ready: Route `/privacy-policy`
  - [ ] Deploy to production
  - [ ] Verify URL is accessible: `https://kara.ai/privacy-policy`
  - [ ] Test URL loads correctly
  - [ ] Ensure proper formatting displays correctly

**URL Requirements**:
- All URLs must be publicly accessible (no authentication required)
- URLs must be HTTPS
- URLs must be accessible to HubSpot's crawler
- URLs should be permanent (not temporary)

---

### 3. Verify Install Button URL
**Priority**: High  
**Status**: ⬜ Pending

**Tasks**:
- [ ] Test current install URL: `/hubspot/login`
- [ ] Verify OAuth flow works end-to-end:
  - [ ] Click install button
  - [ ] Redirects to HubSpot authorization page
  - [ ] User can authorize app
  - [ ] Redirects back to Kara successfully
  - [ ] Data syncs correctly
- [ ] Create dedicated install landing page (optional but recommended):
  - [ ] Create route: `/hubspot/install`
  - [ ] Create view with clear instructions
  - [ ] Add "Connect HubSpot" button
  - [ ] Test complete flow
- [ ] Document install URL for marketplace listing

**Recommended Install Flow**:
1. User clicks "Install" in marketplace
2. Redirects to `/hubspot/install` (or `/hubspot/login`)
3. Shows brief intro and "Connect HubSpot" button
4. Redirects to HubSpot OAuth
5. User authorizes
6. Redirects back to Kara
7. Shows success message and next steps

---

### 4. Create Test Credentials
**Priority**: High  
**Status**: ⬜ Pending

**Tasks**:
- [ ] Create dedicated HubSpot test account
- [ ] Populate test account with sample data:
  - [ ] At least 5-10 deals in different stages
  - [ ] Multiple pipelines (if applicable)
  - [ ] Multiple owners/team members
  - [ ] Sample contacts and companies (if using those scopes)
  - [ ] Sample lists (if using lists scope)
- [ ] Document test credentials securely:
  - [ ] HubSpot account email
  - [ ] HubSpot portal ID
  - [ ] Test account password (store securely)
  - [ ] List of sample data included
- [ ] Create credentials document for HubSpot reviewers:
  - [ ] Format: PDF or text document
  - [ ] Include: Login credentials, portal ID, sample data description
  - [ ] Mark as "For HubSpot Review Team Only"
- [ ] Store credentials securely (not in code repository)

**Test Account Requirements**:
- Should represent typical use case
- Should have realistic data
- Should demonstrate all app features
- Should be accessible for reviewers

---

### 5. Verify All URLs Are Accessible
**Priority**: High  
**Status**: ⬜ Pending

**Tasks**:
- [ ] **Setup Documentation URL**
  - [ ] Test URL: `https://kara.ai/docs/hubspot-setup-guide`
  - [ ] Verify accessible without authentication
  - [ ] Test with HubSpot SEO tools (if available)
  - [ ] Check mobile accessibility
  
- [ ] **Terms of Service URL**
  - [ ] Test URL: `https://kara.ai/terms-of-service`
  - [ ] Verify accessible without authentication
  - [ ] Check formatting and readability
  
- [ ] **Privacy Policy URL**
  - [ ] Test URL: `https://kara.ai/privacy-policy`
  - [ ] Verify accessible without authentication
  - [ ] Check formatting and readability
  
- [ ] **Install Button URL**
  - [ ] Test URL: `https://kara.ai/hubspot/install` (or `/hubspot/login`)
  - [ ] Verify OAuth flow works
  - [ ] Test error handling
  
- [ ] **Support Resources**
  - [ ] Test support website URL
  - [ ] Verify support email is monitored
  - [ ] Test support response time

**HubSpot Crawler**:
- [ ] Add HubSpot Crawler user agent to allow list (if needed)
- [ ] Test crawler access to all URLs
- [ ] Verify no authentication required for crawler

---

### 6. Implement Future Scope Functionality (If Needed)
**Priority**: Medium  
**Status**: ⬜ Pending

**Tasks**:
- [ ] Review scopes marked "Planned for Future Use":
  - [ ] `crm.lists.read`
  - [ ] `crm.objects.contacts.read`
  - [ ] `crm.objects.companies.read`
  - [ ] `crm.schemas.contacts.read`
  - [ ] `crm.schemas.companies.read`
  
- [ ] **Option A: Implement Functionality**
  - [ ] Implement features using these scopes
  - [ ] Add API calls in code
  - [ ] Update documentation
  - [ ] Test functionality
  
- [ ] **Option B: Remove Unused Scopes**
  - [ ] Remove scopes from `HubspotController.php`
  - [ ] Update documentation
  - [ ] Re-add when implementing features later

**Note**: HubSpot requires all requested scopes to be used. Choose one option before submission.

---

### 7. Prepare Screenshots
**Priority**: High  
**Status**: ⬜ Pending

**Tasks**:
- [ ] **Screenshot 1: Dashboard with HubSpot Deals**
  - [ ] Take screenshot of main dashboard
  - [ ] Show deal table with HubSpot data
  - [ ] Show team performance widgets
  - [ ] Ensure good quality and clarity
  - [ ] Recommended size: 1280x720 or larger
  
- [ ] **Screenshot 2: Team Goals Management**
  - [ ] Take screenshot of goals dashboard
  - [ ] Show goal tracking interface
  - [ ] Show completion status
  - [ ] Ensure good quality and clarity
  
- [ ] **Screenshot 3: 1-on-1 Meeting Dashboard**
  - [ ] Take screenshot of 1-on-1 interface
  - [ ] Show team member list
  - [ ] Show meeting scheduling
  - [ ] Show Google Calendar integration
  - [ ] Ensure good quality and clarity
  
- [ ] **Screenshot 4: Deal Briefing Interface**
  - [ ] Take screenshot of AI deal briefing
  - [ ] Show deal summary
  - [ ] Show engagement history
  - [ ] Show coaching insights
  - [ ] Ensure good quality and clarity

**Screenshot Requirements**:
- High resolution (at least 1280x720)
- Clear and professional
- Show actual app functionality
- No sensitive data visible
- Consistent styling

---

### 8. Verify Pricing Information
**Priority**: High  
**Status**: ⬜ Pending

**Tasks**:
- [ ] Review pricing in `docs/app-listing-content.md`
- [ ] Verify pricing matches website exactly:
  - [ ] Free plan details match
  - [ ] Professional plan price matches
  - [ ] Enterprise plan details match
- [ ] Ensure only HubSpot-compatible plans are listed
- [ ] Remove any plans that don't support HubSpot integration
- [ ] Update marketplace listing with accurate pricing

---

### 9. Set Up Support Resources
**Priority**: Medium  
**Status**: ⬜ Pending

**Tasks**:
- [ ] **Support Website**
  - [ ] Verify support website is live: `https://kara.ai/support`
  - [ ] Ensure it's publicly accessible
  - [ ] Add HubSpot integration FAQ if needed
  
- [ ] **Support Email**
  - [ ] Verify `support@kara.ai` is monitored
  - [ ] Test email response time
  - [ ] Set up auto-responder if needed
  
- [ ] **HubSpot Community Forum**
  - [ ] Create forum post about Kara (optional)
  - [ ] Link to community forum in listing
  - [ ] Monitor forum for questions

---

### 10. Final Code Review
**Priority**: Medium  
**Status**: ⬜ Pending

**Tasks**:
- [ ] Review all HubSpot-related code:
  - [ ] `app/Http/Controllers/HubspotController.php`
  - [ ] `app/Helpers/HubspotClientHelper.php`
  - [ ] `app/Imports/Hubspot*.php`
- [ ] Verify no hardcoded credentials
- [ ] Check error handling is robust
- [ ] Verify logging doesn't expose sensitive data
- [ ] Test OAuth flow one more time
- [ ] Verify refresh token handling works

---

## Marketplace Submission Tasks

### 11. Complete HubSpot App Developer Account Setup
**Priority**: High  
**Status**: ⬜ Pending

**Tasks**:
- [ ] Log into HubSpot App Developer account
- [ ] Navigate to app listing creation/editing
- [ ] Verify app is registered correctly
- [ ] Check OAuth app configuration:
  - [ ] Client ID matches code
  - [ ] Redirect URI matches code
  - [ ] Scopes match code

---

### 12. Fill Out App Listing Form
**Priority**: High  
**Status**: ⬜ Pending

**Tasks**:
- [ ] **Basic Information**
  - [ ] App name: Kara
  - [ ] Short description (from `app-listing-content.md`)
  - [ ] Long description (from `app-listing-content.md`)
  - [ ] Category selection
  
- [ ] **Screenshots**
  - [ ] Upload Screenshot 1 (Dashboard)
  - [ ] Upload Screenshot 2 (Team Goals)
  - [ ] Upload Screenshot 3 (1-on-1 Meetings)
  - [ ] Upload Screenshot 4 (Deal Briefing)
  
- [ ] **URLs**
  - [ ] Setup documentation URL
  - [ ] Install button URL
  - [ ] Terms of Service URL
  - [ ] Privacy Policy URL
  - [ ] Support website URL
  - [ ] Support email
  
- [ ] **Pricing**
  - [ ] Free plan details
  - [ ] Professional plan details
  - [ ] Enterprise plan details (if applicable)
  - [ ] Verify pricing matches website
  
- [ ] **Shared Data Table**
  - [ ] Fill out data read from HubSpot
  - [ ] Fill out data written to HubSpot
  - [ ] Mark sync direction (bidirectional for deals)
  - [ ] Ensure all OAuth scopes are documented
  
- [ ] **OAuth Scopes**
  - [ ] List all requested scopes
  - [ ] Provide justification for each scope
  - [ ] Reference scope justification documentation

---

### 13. Submit for Review
**Priority**: High  
**Status**: ⬜ Pending

**Tasks**:
- [ ] Review all information one final time
- [ ] Verify all URLs are accessible
- [ ] Check all required fields are filled
- [ ] Submit app listing for review
- [ ] Save submission confirmation
- [ ] Note submission date

---

## Post-Submission Tasks

### 14. Monitor Review Status
**Priority**: High  
**Status**: ⬜ Pending

**Tasks**:
- [ ] Check review status regularly (HubSpot reviews within 10 business days)
- [ ] Respond to any feedback promptly
- [ ] Address issues within 60 days (HubSpot requirement)
- [ ] Update code/documentation if needed based on feedback
- [ ] Resubmit if changes are made

---

### 15. Prepare for Feedback
**Priority**: Medium  
**Status**: ⬜ Pending

**Tasks**:
- [ ] Be ready to provide additional information
- [ ] Have test credentials ready to share
- [ ] Prepare to demonstrate app functionality
- [ ] Be ready to answer questions about:
  - [ ] Scope usage
  - [ ] Data flow
  - [ ] Security practices
  - [ ] Pricing model

---

## Quick Reference Checklist

**Before Submission - Must Complete**:
- [ ] 3+ active installs verified
- [ ] All documentation URLs published and accessible
- [ ] Install button URL tested and working
- [ ] Test credentials prepared
- [ ] Screenshots prepared
- [ ] Pricing verified
- [ ] All scopes implemented OR unused scopes removed

**During Submission**:
- [ ] All listing fields completed
- [ ] All URLs added
- [ ] Screenshots uploaded
- [ ] Shared data table completed
- [ ] OAuth scopes listed with justification

**After Submission**:
- [ ] Monitor review status
- [ ] Respond to feedback within 60 days
- [ ] Address any issues raised

---

## Notes

- **Timeline**: HubSpot reviews listings within 10 business days initially
- **Feedback Window**: Must address feedback within 60 days
- **One at a Time**: Only one app can be submitted at a time
- **URL Accessibility**: All URLs must be publicly accessible (HubSpot crawler will verify)
- **Scope Usage**: All requested scopes must be used (or removed before submission)

---

**Last Updated**: January 2025

**Status**: Ready for Manual Task Completion


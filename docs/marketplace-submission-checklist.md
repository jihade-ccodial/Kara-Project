# HubSpot Marketplace Submission Checklist

This checklist ensures all requirements for HubSpot Marketplace submission are met.

## Minimum Requirements

### ✅ Access
- [x] App does not redirect to different public/private app
- [x] App does not require another app to function
- [x] Single HubSpot app ID used for all API requests

### ✅ Uniqueness
- [x] App is unique (not a duplicate listing)
- [x] If updating existing app, using update instead of new listing

### ✅ Use Case
- [x] App addresses a specific use case (sales team management and coaching)
- [x] Use case is distinct and valuable

### ✅ OAuth
- [x] OAuth is the sole authorization method
- [x] No API keys used
- [x] Refresh token handling implemented
- [x] `prompt=consent` parameter added to ensure refresh tokens

### ✅ Installs
- [ ] **ACTION REQUIRED**: Verify at least 3 active, unique installs exist
- [ ] Document install verification process

### ✅ Scopes
- [x] Only requesting scopes that are used
- [x] All scopes justified and documented
- [x] Scope justification document created (`docs/scope-justification.md`)

### ✅ Terms
- [x] Terms of Service created (`docs/terms-of-service.md`)
- [x] Terms reviewed and compliant
- [x] Terms URL will be publicly accessible

### ✅ Restricted Industries
- [x] App does not exclusively serve restricted industries

### ✅ Restricted Functionality
- [x] App does not use classic CRM cards
- [x] App uses modern HubSpot APIs

### ✅ AI Connectors
- [x] N/A - App is not primarily an AI connector

## HubSpot Brand Requirements

### ✅ Branding Guidelines
- [x] "HubSpot" capitalized correctly (capital S) in all user-facing text
- [x] Brand compliance reviewed in:
  - [x] README.md
  - [x] Login page
  - [x] Header navigation
  - [x] Documentation files
  - [x] Code comments

### ✅ Trademark Usage
- [x] App name does not combine with "HubSpot" or "Hub"
- [x] No HubSpot logo usage without permission
- [x] No trademark violations

## Listing Requirements

### ✅ Content Specificity
- [x] Listing content focuses on integration value, not general product info
- [x] Content highlights HubSpot-specific benefits
- [x] Use cases clearly defined

### ✅ URLs - Setup Documentation
- [x] Setup documentation created (`docs/hubspot-setup-guide.md`)
- [ ] **ACTION REQUIRED**: Publish setup documentation to public URL
- [ ] **ACTION REQUIRED**: Verify URL is accessible to HubSpot crawler
- [ ] **ACTION REQUIRED**: Add HubSpot Crawler to allow list if needed

### ✅ URLs - Install Button
- [x] Install button URL identified (`/hubspot/login` route)
- [ ] **ACTION REQUIRED**: Create dedicated install landing page if needed
- [ ] **ACTION REQUIRED**: Test install flow end-to-end
- [ ] **ACTION REQUIRED**: Ensure URL is publicly accessible

### ✅ URLs - Support Resources
- [x] Support resources identified in app listing content
- [ ] **ACTION REQUIRED**: Verify support website URL is live
- [ ] **ACTION REQUIRED**: Verify support email is monitored
- [ ] **ACTION REQUIRED**: Set up HubSpot community forum presence if needed

### ✅ URLs - Terms and Privacy
- [x] Terms of Service created (`docs/terms-of-service.md`)
- [x] Privacy Policy created (`docs/privacy-policy.md`)
- [ ] **ACTION REQUIRED**: Publish Terms of Service to public URL
- [ ] **ACTION REQUIRED**: Publish Privacy Policy to public URL
- [ ] **ACTION REQUIRED**: Verify both URLs are live and accessible

### ✅ Shared Data
- [x] Shared data documentation created (`docs/shared-data.md`)
- [x] All OAuth scopes documented in shared data table
- [x] Bidirectional sync documented for deals
- [x] Data flow clearly explained

### ✅ Pricing Information
- [x] Pricing information prepared in app listing content
- [x] Freemium model documented
- [x] Free and paid plans detailed
- [ ] **ACTION REQUIRED**: Verify pricing matches website exactly
- [ ] **ACTION REQUIRED**: Ensure only HubSpot-compatible plans are listed

### ✅ Support Contact
- [x] At least one support contact method identified
- [x] Support email: support@kara.ai
- [x] Support website: https://kara.ai/support

### ✅ Testing Credentials
- [ ] **ACTION REQUIRED**: Create dedicated HubSpot test account
- [ ] **ACTION REQUIRED**: Populate test account with sample data
- [ ] **ACTION REQUIRED**: Document test credentials securely
- [ ] **ACTION REQUIRED**: Prepare credentials document for HubSpot reviewers

## App Card Requirements

### ✅ Naming
- [x] App card name does not modify/imitate HubSpot brands
- [x] App card name does not use generic product name + HubSpot brands
- [x] App card name does not use "inbound" in connection with HubSpot's INBOUND event

### ✅ Logos and Icons
- [x] No HubSpot logo/sprocket used without permission
- [x] Only own company/brand logos used as icons

### ✅ Sensitive Data
- [x] No sensitive data scopes requested
- [x] App card does not display sensitive information

## Security and Privacy

### ✅ Scope Usage
- [x] All requested scopes are used
- [x] Unused scopes removed
- [x] Scope justification documented

### ✅ Browser Extensions
- [x] N/A - No dedicated browser extension required

## Reliability and Performance

### ✅ Asset Links
- [x] Relative links used for assets where possible
- [x] CDN used for external assets (if applicable)

## Usability and Accessibility

### ✅ Buttons
- [x] Forms include submit buttons
- [x] Destructive button styles denote destructive behavior
- [x] Only one primary button per surface

### ✅ Text
- [x] Underline formatting not used next to hyperlinks
- [x] Tags not used in place of buttons or links

## Code Quality

### ✅ Code Cleanup
- [x] Commented-out code removed from HubspotController
- [x] Error handling improved
- [x] Logging added for errors

### ✅ Error Handling
- [x] Robust error handling in place
- [x] User-friendly error messages
- [x] Proper exception handling

## Documentation

### ✅ Setup Documentation
- [x] Comprehensive setup guide created
- [x] Step-by-step instructions included
- [x] Troubleshooting section included
- [x] Screenshots recommended

### ✅ Security Documentation
- [x] Security practices documented (`docs/security.md`)
- [x] OAuth implementation detailed
- [x] Data encryption explained
- [x] Access controls documented

### ✅ Shared Data Documentation
- [x] Data flow documented (`docs/shared-data.md`)
- [x] Read/write operations clearly explained
- [x] OAuth scopes mapped to data access

### ✅ Scope Justification
- [x] Detailed scope justification created (`docs/scope-justification.md`)
- [x] Each scope usage documented
- [x] Unused scopes explained

## App Listing Content

### ✅ Content Prepared
- [x] App name: Kara
- [x] Short description prepared
- [x] Long description prepared
- [x] Use cases defined
- [x] Screenshot descriptions prepared
- [x] Pricing information prepared
- [x] Support resources identified

### ✅ Content Quality
- [x] Content focuses on integration value
- [x] Content is specific to HubSpot integration
- [x] Content is accurate and complete

## Pre-Submission Tasks

### 🔲 Final Verification
- [ ] Review all documentation for accuracy
- [ ] Test complete installation flow
- [ ] Verify all URLs are live and accessible
- [ ] Test OAuth flow end-to-end
- [ ] Verify scope usage matches documentation
- [ ] Check brand compliance one final time
- [ ] Review pricing information for accuracy

### 🔲 HubSpot App Developer Account
- [ ] Log into HubSpot App Developer account
- [ ] Navigate to app listing creation
- [ ] Fill out all required fields
- [ ] Upload screenshots
- [ ] Add all URLs
- [ ] Complete shared data table
- [ ] Review listing before submission

### 🔲 Submission
- [ ] Submit app listing for review
- [ ] Monitor review status
- [ ] Respond to feedback within 60 days
- [ ] Address any issues raised by reviewers

## Notes

- HubSpot reviews listings within 10 business days initially
- Feedback must be addressed within 60 days
- Only one app can be submitted at a time
- Ensure pricing matches website exactly
- All URLs must be publicly accessible (HubSpot crawler will verify)

---

**Last Updated**: January 2026

**Status**: In Progress - Documentation Complete, Pre-Submission Tasks Pending


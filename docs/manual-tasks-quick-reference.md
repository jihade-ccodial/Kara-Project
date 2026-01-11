# Manual Tasks - Quick Reference

## Critical Tasks (Must Complete Before Submission)

### ✅ 1. Verify Install Count
- Check database: `\App\Models\Organization::distinct('hubspot_portalId')->count()`
- Need: **At least 3 active installs**
- If less: Create test installs

### ✅ 2. Publish Documentation URLs
Publish these files to public URLs:
- `docs/hubspot-setup-guide.md` → `https://kara.ai/docs/hubspot-setup-guide`
- `docs/terms-of-service.md` → `https://kara.ai/terms-of-service`
- `docs/privacy-policy.md` → `https://kara.ai/privacy-policy`

**Requirements**: HTTPS, publicly accessible, no authentication

### ✅ 3. Test Install Button
- Test URL: `/hubspot/login` or create `/hubspot/install`
- Verify complete OAuth flow works
- Test error handling

### ✅ 4. Create Test Credentials
- Create HubSpot test account
- Add sample data (deals, pipelines, owners)
- Document credentials securely for reviewers

### ✅ 5. Prepare Screenshots
Need 4 screenshots:
1. Dashboard with HubSpot deals
2. Team goals management
3. 1-on-1 meeting dashboard
4. Deal briefing interface

**Requirements**: High resolution (1280x720+), professional, no sensitive data

### ✅ 6. Verify Pricing
- Match pricing exactly with website
- Only list HubSpot-compatible plans
- Remove plans that don't support integration

### ✅ 7. Scope Decision
**Choose one**:
- **Option A**: Implement functionality for all scopes (lists, contacts, companies)
- **Option B**: Remove unused scopes before submission

**Note**: HubSpot requires all requested scopes to be used.

---

## Submission Tasks

### ✅ 8. Complete App Listing Form
Fill out in HubSpot App Developer account:
- Basic info (name, description)
- Upload 4 screenshots
- Add all URLs
- Complete shared data table
- List OAuth scopes with justification
- Add pricing information

### ✅ 9. Submit for Review
- Review all information
- Submit listing
- Save confirmation

---

## Post-Submission

### ✅ 10. Monitor & Respond
- Check status regularly (10 business days)
- Respond to feedback within 60 days
- Address any issues

---

## Quick Checklist

**Before Submission**:
- [ ] 3+ installs verified
- [ ] Documentation URLs published
- [ ] Install URL tested
- [ ] Test credentials ready
- [ ] Screenshots prepared
- [ ] Pricing verified
- [ ] Scopes decision made

**During Submission**:
- [ ] All fields completed
- [ ] URLs added
- [ ] Screenshots uploaded
- [ ] Shared data table filled
- [ ] Submitted

**After Submission**:
- [ ] Monitor status
- [ ] Respond to feedback

---

**See `manual-tasks-checklist.md` for detailed instructions.**


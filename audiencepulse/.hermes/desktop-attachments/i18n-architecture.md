# i18n Architecture — AR_EG Primary, Framework-Ready

## Decision

**Build proper i18n infrastructure** (translation entities, locale files, pluralization, RTL support) — but **AR_EG is the default, primary, and effectively the only production locale.**

Egyptian producers, moderators, hosts, and technical staff do not have strong English command. The system must feel native Arabic at every layer. i18n exists as architecture, not as a user-facing option.

---

## i18n Entity Structure

### Translation Key Convention

```
# Format: {domain}.{component}.{element}
# Always Arabic-first — English is the fallback translation, not the source

audience.voting.consent_title        = "الموافقة على المشاركة"
audience.voting.consent_body         = "بموافقتك على المشاركة، أنت توافق على..."
audience.voting.window_open          = "نافذة التصويت مفتوحة"
audience.voting.window_closed        = "انتهت فترة التصويت"
audience.voting.duplicate_detected   = "تم رصد تصويت مكرر"

moderation.queue.pending             = "بانتظار المراجعة"
moderation.queue.approved            = "تمت الموافقة"
moderation.queue.rejected            = "مرفوض"
moderation.queue.held                = "معلق — يحتاج مراجعة إضافية"

graphics.lower_third.voter_count     = "{count} مشارك"
graphics.lower_third.leading_option  = "المتصدر: {option}"

dashboard.stats.total_votes          = "إجمالي الأصوات"
dashboard.stats.unique_voters        = "مشاركون فريدون"
dashboard.stats.engagement_rate      = "معدل التفاعل"
```

### Locale File Structure

```
locales/
├── ar_EG/                    # PRIMARY — Egyptian Arabic
│   ├── audience.json         # Voting, consent, eligibility
│   ├── moderation.json       # Editorial queue, approval states
│   ├── graphics.json         # On-screen text, host prompts
│   ├── dashboard.json        # Producer dashboard UI
│   ├── playout.json          # Scheduling, EPG, channel names
│   ├── drama.json            # Character dialogue templates
│   ├── documentary.json      # Narration templates, captions
│   ├── errors.json           # Error messages
│   ── validation.json       # Form validation messages
│
├── ar/                       # MSA fallback (if needed)
│   └── ...
│
└── en/                       # Technical fallback (dev/debug only)
    └── ...
```

### Pluralization — Arabic Rules

Arabic has **6 plural forms** (not 2 like English). Must use ICU MessageFormat or equivalent:

```json
{
  "voter_count": {
    "zero": "لا مشاركين",
    "one": "مشارك واحد",
    "two": "مشاركان",
    "few": "{count} مشاركين",
    "many": "{count} مشاركاً",
    "other": "{count} مشارك"
  }
}
```

### Number Formatting

```
ar_EG locale:
  1,234.56  →  ١٬٢٣٤٫٥  (Arabic-Indic numerals, Arabic decimal separator)
  
  BUT: Technical dashboards may use Western numerals (١٢٣٤) for readability
  → Make this configurable per-component
```

### Date/Time Formatting

```
ar_EG locale:
  Thursday, August 6, 2026  →  الخميس، ٦ أغسطس ٢٠٢
  3:45 PM                   →  ٣:٤٥ م
  Hijri calendar option for religious content
  
  Egyptian time zone: Africa/Cairo (UTC+2, no DST)
```

---

## RTL Implementation Rules

### CSS — Logical Properties Only

```css
/* NEVER use physical directions */
/* ❌ margin-left, padding-right, text-align: left, float: right */

/* ALWAYS use logical properties */
margin-inline-start: 1rem;    /* right in RTL, left in LTR */
margin-inline-end: 1rem;
padding-inline-start: 0.5rem;
text-align: start;
float: inline-start;

/* Layout */
flex-direction: row;          /* auto-flips in RTL */
grid-template-columns: 1fr 2fr; /* auto-flips */

/* Borders */
border-inline-start: 2px solid #ccc;
```

### HTML

```html
<html dir="rtl" lang="ar">
<head>
  <meta charset="UTF-8">
  <!-- Arabic font preconnect -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
</head>
```

### Font Stack (Broadcast-Grade Arabic)

```css
/* On-screen graphics (bold, readable at distance) */
font-family: 'Cairo', 'Tajawal', 'Noto Naskh Arabic', sans-serif;

/* Body text / dashboard UI */
font-family: 'Tajawal', 'Cairo', 'Noto Sans Arabic', sans-serif;

/* Formal / documentary narration text */
font-family: 'Noto Naskh Arabic', 'Amiri', serif;

/* Numbers in graphics (Western numerals often clearer) */
font-variant-numeric: tabular-nums;
```

### Icon Direction Flipping

```css
/* Icons that imply direction must flip in RTL */
.arrow-right, .chevron-next, .play-icon {
  transform: scaleX(-1);  /* flip horizontally in RTL */
}

/* Icons that are direction-neutral stay as-is */
/* ✓ checkmark, ✕ close,  settings — no flip needed */
```

---

## Backend i18n (Laravel)

```php
// config/app.php
'locale' => 'ar',
'fallback_locale' => 'ar',

// Validation messages — Arabic
resources/lang/ar/validation.php

// Carbon dates
Carbon::setLocale('ar');
// Output: الخميس، ٦ أغسطس ٢٠٢٦

// Number formatting
NumberFormatter::create('ar_EG', NumberFormatter::DECIMAL);

// Route localization (if ever needed for multi-locale)
Route::group(['prefix' => app()->getLocale()], function() {
    // ...
});
```

### Database — Arabic-Ready

```sql
-- ALL text columns: utf8mb4 (4-byte UTF-8)
-- COLLATE: utf8mb4_unicode_ci (proper Arabic sorting)

CREATE TABLE moderation_queue (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    content_text TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    status ENUM('pending', 'approved', 'rejected', 'held') 
           COMMENT 'بانتظار, موافق, مرفوض, معلق',
    moderator_notes TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Full-text search in Arabic
ALTER TABLE moderation_queue 
ADD FULLTEXT INDEX ft_content (content_text) 
WITH PARSER ngram;  -- ngram parser for CJK/Arabic tokenization
```

---

## Frontend i18n (React)

```jsx
// i18n setup (react-i18next or similar)
import i18n from 'i18next';
import { initReactI18next } from 'react-i18next';

i18n.use(initReactI18next).init({
  resources: {
    ar: {
      translation: {
        "dashboard.stats.total_votes": "إجمالي الأصوات",
        "moderation.queue.pending": "بانتظار المراجعة",
      }
    }
  },
  lng: 'ar',          // Default and primary
  fallbackLng: 'ar',  // No English fallback in production
  interpolation: {
    escapeValue: false,
  }
});

// Usage
function VoteCount({ count }) {
  return (
    <span>
      {t('voter_count', { count, context: getPluralForm(count) })}
    </span>
  );
}
```

### RTL-Aware Components

```jsx
// Layout component — auto RTL
function App() {
  return (
    <div dir="rtl" lang="ar" className="font-cairo">
      <Dashboard />
    </div>
  );
}

// Icon that auto-flips in RTL
function DirectionalIcon({ name }) {
  const { dir } = useDocumentDirection();
  const shouldFlip = ['arrow', 'chevron', 'next', 'back'].includes(name);
  
  return (
    <Icon 
      name={name} 
      style={shouldFlip && dir === 'rtl' ? { transform: 'scaleX(-1)' } : {}} 
    />
  );
}
```

---

## NLP / AI — Arabic-First

### Tokenization
- Arabic has no spaces between words in some constructs
- Clitic separation needed: "والكتاب" → "و" + "ال" + "كتاب"
- Use CAMeL Tools or Farasa for Egyptian Arabic tokenization

### Named Entity Recognition
- Egyptian names, places, organizations
- Arabic date expressions: "الجمعة الجاي" (next Friday)
- Arabic numbers in text: "تلاتة" (three)

### Sentiment Analysis
- Egyptian dialect sentiment ≠ MSA sentiment
- "ده حلو أوي" = very positive (Egyptian)
- "مش حلو" = negative (Egyptian negation pattern)

### Intent Detection
- Voting intent: "أنا بصوت لـ..." / "صوتي لـ..."
- Opt-out: "مش عايز أشارك" / "شيلني"
- Complaint: "ده غلط" / "مش صح"

---

## What "i18n-Ready but AR_EG-Only" Means in Practice

| Layer            | i18n Architecture           | Production Reality                     |
| ---------------- | --------------------------- | -------------------------------------- |
| Translation keys | Structured, namespaced      | All values in Arabic                   |
| Locale files     | ar_EG + ar + en exist       | Only ar_EG loaded in prod              |
| Pluralization    | ICU MessageFormat (6 forms) | Arabic forms used exclusively          |
| RTL support      | CSS logical properties      | Always RTL, never LTR                  |
| Date/Time        | Locale-aware formatting     | Always Africa/Cairo, Arabic format     |
| Numbers          | Locale-aware                | Arabic-Indic or Western (configurable) |
| Font stack       | Configurable per locale     | Cairo/Tajawal always                   |
| NLP models       | Swappable                   | Egyptian dialect models only           |
| Error messages   | Translation keys            | Arabic messages only                   |
| API responses    | Locale header supported     | Always returns Arabic                  |

---

## Rule: No English in Production UI

- Admin panels: Arabic
- Producer dashboards: Arabic
- Host prompts: Arabic (Egyptian dialect)
- Graphics overlays: Arabic
- EPG metadata: Arabic
- Error messages: Arabic
- Logs: English is fine (technical staff can handle)
- Code comments: English is fine (dev team)
- Database column names: English (technical)
- Translation keys: English (technical)

**The system speaks Arabic to humans, English to machines.**

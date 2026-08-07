# AR_EG Localization Challenge — Sada El Balad

## The Core Problem

This is not a "translate the UI" problem. **Every layer of the system must be Arabic-native (Egyptian dialect).** A system built in English and translated at the end will fail at every touchpoint.

---

## Where AR_EG Hits

### 1. WhatsApp Business Ingestion
- **Egyptian dialect NLP** — messages like "أيوه بصوت لـ" / "مش موافق" / "ده غلط"
- Arabic numerals vs Western numerals (٢٣ vs 123)
- Mixed Arabic/Latin text (common in Egyptian WhatsApp)
- Voice messages in Egyptian Arabic → STT → Arabic transcription
- Emoji usage patterns in Egyptian context

### 2. Social Listening
- Hashtag parsing in Arabic (no spaces, RTL complications)
- Egyptian slang detection: "يا باشا", "حبيبي", "إيه ده؟"
- Sentiment analysis trained on Egyptian Arabic (not MSA)
- Dialect vs Modern Standard Arabic distinction
- Social platform APIs return Arabic content — encoding, sorting, display

### 3. Editorial Moderation Queue
- Arabic text rendering in the dashboard (RTL layout)
- Moderators need to read Egyptian dialect naturally
- Content flags specific to Egyptian broadcast regulations
- Arabic search/filter in the moderation queue

### 4. Live Graphics & Host Prompts
- **RTL graphics rendering** — text must flow right-to-left on screen
- Arabic font selection (broadcast-quality: Noto Naskh Arabic, Cairo, Tajawal)
- Dynamic text sizing for Arabic (longer than English equivalents)
- Host prompts in Egyptian Arabic — natural phrasing, not MSA
- Teleprompter integration with Arabic text

### 5. Audience Producer Dashboard
- Full RTL UI layout (not just text direction — entire layout flips)
- Arabic date/time formatting (Egyptian locale)
- Number formatting (Arabic-Indic numerals option)
- Arabic chart labels, tooltips, legends

### 6. Auditable Tally Engine
- Arabic receipt/report generation
- Regulatory compliance documents in Arabic
- Audit trail with Arabic timestamps and labels

### 7. Channel-in-a-Box / Playout
- Arabic EPG (Electronic Program Guide)
- Arabic metadata for scheduled content
- Arabic subtitles/captions pipeline
- Arabic voiceover/TTS integration

---

## Technical Implications

### Database
```sql
-- All text columns MUST be utf8mb4 (not utf8/utf8mb3)
-- Arabic needs 4-byte UTF-8 for full character support
CREATE TABLE votes (
    id BIGINT PRIMARY KEY,
    voter_phone VARCHAR(20),
    response_text TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    -- COLLATE matters for Arabic sorting/search
);
```

### Backend (Laravel)
```php
// Locale set to ar_EG globally
'app.locale' => 'ar',
'app.fallback_locale' => 'ar',

// Validation messages in Arabic
'validation.custom.ar' => [...],

// Carbon for Arabic dates
Carbon::setLocale('ar');
```

### Frontend (React)
```jsx
// RTL layout engine
<html dir="rtl" lang="ar">

// CSS logical properties (not physical)
margin-inline-start: 1rem;  /* NOT margin-left */
text-align: start;           /* NOT text-align: left */

// Arabic font stack
font-family: 'Cairo', 'Noto Naskh Arabic', 'Tajawal', sans-serif;
```

### NLP / AI Layer
- **Not** Google Translate or generic Arabic models
- Need Egyptian dialect-specific models:
  - CAMeL Tools (Egyptian dialect NLP)
  - MARBERT (Arabic BERT for dialects)
  - Custom fine-tuning on Egyptian social media corpus
- Sentiment: Egyptian Arabic sentiment ≠ MSA sentiment
- Intent detection: "مش هصوت" = "I won't vote" (negation pattern specific to Egyptian)

### WhatsApp Business API
- Meta's WhatsApp Business API supports Arabic natively
- Template messages must be approved in Arabic
- Webhook payloads contain Arabic text — ensure UTF-8 handling end-to-end

### Graphics Engine
- Vizrt/Chyron support Arabic but need font licensing
- Custom HTML5 overlay: CSS `direction: rtl` + Arabic web fonts
- Text animation must respect RTL flow

---

## What We Need from the Client

| Item                                        | Why                            |
| ------------------------------------------- | ------------------------------ |
| Egyptian dialect corpus / sample messages   | Train NLP models               |
| Approved Arabic font licenses for broadcast | Graphics compliance            |
| Egyptian broadcast content regulations      | Editorial approval rules       |
| Existing WhatsApp Business account details  | API integration                |
| Social media handles to monitor             | Social listening scope         |
| Current graphics/brand guidelines           | Visual consistency             |
| Sample show format / run-of-show            | Understand the engagement loop |
| Regulatory requirements for voting audits   | Tally engine compliance        |

---

## Risk: The "English-First" Trap

If we build the system in English and "add Arabic later":
- RTL layout breaks at every level (CSS, canvas, graphics)
- NLP models trained on English/MSA fail on Egyptian dialect
- Font rendering issues surface only at broadcast time
- Host prompts sound robotic/unnatural in Egyptian Arabic
- Moderation dashboard is unusable for Arabic-speaking producers

**Decision: Arabic-first. Every component designed for AR_EG from day one.**

---

## Recommended Approach

1. **NLP Pipeline** — Egyptian dialect models (CAMeL Tools + MARBERT)
2. **RTL-First UI** — Dashboard designed RTL, not LTR-flipped
3. **Arabic Broadcast Fonts** — Cairo/Tajawal for on-screen, Noto Naskh for body
4. **Egyptian Locale** — ar_EG throughout (dates, numbers, sorting)
5. **Dialect-Aware Moderation** — Rules engine understands Egyptian slang
6. **Arabic TTS** — For any voice output (rewards, confirmations)

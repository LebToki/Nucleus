# Channel-in-a-Box — Automated Production to Delivery

## Expanded Scope

The client wants a complete **Automated Production-to-Delivery** system. This is NOT just video playout/scheduling — it's a full content production pipeline adapted for broadcast television, replacing YouTube/TikTok workflows with Channel-in-a-Box integration.

---

## Production Categories

### 1. Lip-Sync / Arabic Dubbing
- Source content (foreign language) → Arabic lip-synced output
- AI-driven lip synchronization (Wav2Lip, SadTalker, or commercial)
- Arabic voice casting per character/role
- Quality control: lip-sync accuracy, audio sync, emotional tone
- Output: Broadcast-ready Arabic-dubbed episodes

**Challenges:**
- Arabic phoneme-to-viseme mapping (different from Latin languages)
- Egyptian dialect vs MSA for dubbing
- Long-form content (45-60 min episodes) — processing time
- Lip-sync quality at broadcast resolution (1080i/4K)

### 2. Drama Series — Multi-Persona Production
- AI-generated or AI-assisted drama content
- Multiple character personas (like Clara/NewsCaster but scaled)
- Character consistency across episodes/seasons
- Dialogue generation in Egyptian Arabic
- Scene composition, lighting, camera angles
- Full episode assembly (scenes → episodes → seasons)

**Challenges:**
- Character consistency (face, voice, mannerisms) across hundreds of scenes
- Egyptian dialect dialogue that sounds natural (not MSA)
- Emotional range in AI-generated performances
- Long-form narrative coherence (plot, character arcs)
- Production volume: weekly episodes, multiple series running parallel

### 3. Documentaries
- Research → script → visual generation → narration → assembly
- Archival footage integration
- AI-generated reenactments / historical scenes
- Arabic narration (TTS or voice actor)
- Fact-checking pipeline (critical for documentary credibility)

**Challenges:**
- Historical accuracy in AI-generated visuals
- Arabic documentary narration style (formal but engaging)
- Archival footage licensing and integration
- Long-form assembly (30-90 min documentaries)

### 4. Docuseries
- Multi-episode documentary arcs
- Consistent visual style across episodes
- Serialized storytelling with cliffhangers/hooks
- Episode-to-episode continuity (characters, locations, themes)

---

## Pipeline Architecture (Conceptual)

```
┌─────────────────────────────────────────────────────────────────────┐
│                    PRODUCTION PIPELINE                                │
├─────────────────────────────────────────────────────────────────────┤
│                                                                       │
│  ┌──────────────┐    ┌──────────────┐    ┌──────────────┐           │
│  │   SCRIPT     │───▶│   VISUAL     │───▶│    AUDIO     │           │
│  │  GENERATION  │    │  GENERATION  │    │  GENERATION  │           │
│  │              │    │              │    │              │           │
│  │ • Drama      │    │ • Characters │    │ • TTS (AR)   │           │
│  │ • Doc        │    │ • Scenes     │    │ • Lip-Sync   │           │
│  │ • Docuseries │    │ • Locations  │    │ • Music/SFX  │           │
│  └──────────────┘    └──────────────┘    └──────────────┘           │
│         │                    │                    │                   │
│         └────────────────────┼────────────────────┘                   │
│                              ▼                                        │
│                    ┌──────────────────┐                               │
│                    │    ASSEMBLY      │                               │
│                    │                  │                               │
│                    │ • Scene order    │                               │
│                    │ • Transitions    │                               │
│                    │ • Graphics/Lower │                               │
│                    │ • Subtitles (AR) │                               │
│                    │ • Color grade    │                               │
│                    └────────┬─────────┘                               │
│                             ▼                                         │
│                    ┌──────────────────┐                               │
│                    │    QC / REVIEW   │                               │
│                    │                  │                               │
│                    │ • Auto QC        │                               │
│                    │ • Human review   │                               │
│                    │ • Compliance     │                               │
│                    └────────┬─────────┘                               │
│                             ▼                                         │
│                    ┌──────────────────                               │
│                    │    PLAYOUT       │                               │
│                    │                  │                               │
│                    │ • Schedule       │                               │
│                    │ • Channel-in-Box │                               │
│                    │ • EPG metadata   │                               │
│                    │ • Multi-channel  │                               │
│                    ──────────────────┘                               │
│                                                                       │
└─────────────────────────────────────────────────────────────────────┘
```

---

## Integration with 2TI Orchestrator

The existing 2TI Orchestrator (`/var/www/html/2ti-orchestrator/`) already handles:
- 21 channels, 23 series
- Timeline-based production management
- Agent-based task orchestration (Hermes integration)
- ComfyUI workflow automation
- n8n pipeline integration

**Adaptation needed for Sada El Balad:**
- Replace YouTube/TikTok output → Channel-in-a-Box playout (API integration)
- Add Arabic lip-sync pipeline
- Add multi-persona drama production module
- Add documentary/docuseries production templates
- Egyptian Arabic localization throughout
- Broadcast-grade QC (not web-grade)
- Regulatory compliance layer (Egyptian broadcast standards)

### Channel-in-a-Box Integration

- **Approach:** API-based integration (bidirectional)
- **Status:** Client has an existing Channel-in-a-Box system — technical details TBD
- **Direction:**
  - **To CiAB:** Push finished content, metadata, schedule, EPG data
  - **From CiAB:** Pull playout status, scheduling confirmations, broadcast logs
- **Note:** No technical specs yet — will be scoped once client shares their CiAB vendor/API docs

---

## Multi-Persona Drama System

Building on the existing Clara/NewsCaster unified character system:

### Character Management
```
Character Profile:
├── Visual Identity
│   ├── Face reference (multi-angle)
│   ├── Body type / attire library
│   ├── Expression variants
│   └── Consistency LoRA / IP-Adapter
├── Voice Identity
│   ├── Voice clone / TTS profile
│   ├── Dialect (Egyptian / MSA / other)
│   ├── Emotional range
│   └── Lip-sync phoneme map
├── Personality
│   ├── Dialogue style
│   ├── Behavioral patterns
│   └── Relationship map (other characters)
└── Episode Arc
    ├── Scene appearances
    ├── Emotional journey
    └── Dialogue history
```

### Production Scale
- Multiple characters per series (5-15 main cast)
- Multiple series running in parallel
- Weekly episode output per series
- Character reuse across series (shared universe potential)

---

## Technical Stack Considerations

| Component | Technology | Notes |
|-----------|-----------|-------|
| Lip-Sync | Wav2Lip / SadTalker / Commercial | Arabic phoneme mapping needed |
| Character Generation | ComfyUI + IP-Adapter + LoRA | Consistency across scenes |
| TTS | Arabic TTS (Bark, XTTS, commercial) | Egyptian dialect preferred |
| Script Generation | LLM (fine-tuned Arabic) | Egyptian dialect drama writing |
| Scene Assembly | FFmpeg + Custom | Broadcast codecs (H.264/H.265) |
| Playout | Channel-in-a-Box API | Client's existing system — vendor TBD |
| QC | Automated + Human | Broadcast standards compliance |
| Scheduling | Custom / Playout integration | EPG generation |

---

## Key Questions for Client

1. **Lip-Sync Source:** What content needs dubbing? (Turkish dramas? Western content? Existing library?)
2. **Drama Series:** Original AI-generated dramas or AI-assisted human-written scripts?
3. **Character Count:** How many unique personas across all series?
4. **Episode Volume:** Episodes per week per series? Total concurrent series?
5. **Documentary Topics:** Historical? Current affairs? Science? Mixed?
6. **Channel-in-a-Box API Docs:** When can they share the API spec? (they have an existing system)
7. **Broadcast Standards:** HD (1080i)? 4K? Specific codec requirements?
8. **Regulatory:** Egyptian media regulatory body requirements for AI-generated content?
9. **Timeline:** When do they need this operational?
10. **Budget Range:** For scoping the quote appropriately.

---

## AR_EG Implications (Compounded)

Every production category above has the Arabic/Egyptian localization challenge:

- **Lip-Sync:** Arabic phonemes, Egyptian dialect audio
- **Drama:** Egyptian dialogue, cultural context, character names
- **Documentaries:** Arabic narration, Arabic text overlays, Egyptian historical context
- **Docuseries:** Serialized Arabic storytelling
- **Graphics:** RTL lower-thirds, Arabic titles, Arabic EPG
- **Metadata:** Arabic episode titles, descriptions, tags
- **Scheduling:** Arabic program guide, Arabic time slots

This is not localization — this is **Arabic-native production**.

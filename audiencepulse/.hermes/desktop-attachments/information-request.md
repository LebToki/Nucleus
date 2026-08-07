# Information Request — Sada El Balad

Prepared for: Dr. Naser Mourad Refaat
Purpose: Scope definition for quotation

Note: The two systems below are scoped as independent projects. 
Each can be quoted, delivered, and maintained separately.

---

## Project 1: Live Audience Interaction System — Scope Definition and Explanations
*The following sections detail the requirements for integrating dynamic audience interaction features into live broadcast programs. Please provide comprehensive answers, as these define the technical scope (API connections, data processing, real-time rendering).*

### A1. Broadcast Scale and Usage Profile (Show Format & Volume)
*This section defines the operational scale of the system. The answers here dictate backend infrastructure requirements (scalability, concurrent users) and data throughput.*

1. How many live shows per week will use the audience interaction system?
2. What is the typical duration of each live show?
3. How many simultaneous shows could run at peak times?
4. What is the expected audience size per show (concurrent viewers)?
5. What types of audience interactions are needed (polls, Q&A, voting, story submission, other)?

### A2. WhatsApp Business Integration

6. Does Sada El Balad already have a WhatsApp Business API account, or does this need to be set up?
7. What is the expected message volume per show (incoming messages from viewers)?
8. Should the system support voice messages from viewers, text only, or both?
9. Are there specific keywords or shortcodes that trigger different interaction flows?
10. Does the system need to handle multiple shows running simultaneously on WhatsApp, or one at a time?

### A3. Social Listening Scope: Which social platforms are critical for monitoring?
*Please list all relevant platforms. Consideration should be given to APIs and rate limits associated with each platform.*

11. Which social platforms need to be monitored (Facebook, X/Twitter, Instagram, TikTok, YouTube, other)?
12. What are the official Sada El Balad social media handles and hashtags to monitor?
13. Should social listening cover only Sada El Balad accounts, or also general public conversation about the channel?
14. Is real-time social media display on-air required, or is it for the producer dashboard only?
15. What languages should social listening cover (Arabic only, English as well, other)?

### A4. Voting & Eligibility

16. What are the eligibility rules for voting (age, geography, phone number verification, other)?
17. Is there a regulatory body overseeing audience voting that requires specific compliance (audit trail, data retention, reporting)?
18. What is the voting window duration (per question, per show)?
19. Should the system prevent duplicate voting by phone number, device, or both?
20. Are there paid voting mechanisms (premium SMS, microtransactions), or is all voting free?

### A5. Content Moderation Policies (Editorial Moderation)
*This defines the quality gate for user-submitted content before it goes live. The scope determines if automated filters, manual review tools, or pre-approved libraries are required.*

21. How many moderators will be working the dashboard per show?
22. What is the target response time from submission to on-air approval?
23. Are there specific content categories that require automatic rejection (profanity, political sensitivity, other)?
24. Should the system support pre-approved content pools, or is all content moderated live?
25. Is there an escalation path for borderline content (senior editor approval)?

### A6. Real-Time Visual Presentation (Live Graphics & On-Air Integration)
*This details how audience data must be visualized on screen and integrated into the existing broadcast graphics workflow. Latency and integration protocols are key concerns.*

26. What graphics system is currently in use at the Sada El Balad control room (Vizrt, Chyron, other)?
27. Should the audience system push graphics directly to the graphics engine, or provide data for a graphics operator to trigger manually?
28. What types of on-screen graphics are needed (poll results, vote counts, viewer comments, host prompts, other)?
29. Is teleprompter integration required for host prompts?
30. What is the acceptable latency from audience action to on-screen display?

### A7. Producer Control Center & Analytics (Dashboard & Reporting)
*This defines the functional requirements for the non-broadcast side of the system—the tools used by staff to manage content, monitor metrics, and analyze performance after the show.*

31. How many producer dashboard users will need simultaneous access?
32. What real-time metrics are most important (vote counts, engagement rate, sentiment, geographic distribution, other)?
33. Are post-show reports required (PDF, Excel, dashboard export)?
34. Does the dashboard need to support multiple shows in a queue, or one active show at a time?
35. Should historical data be retained, and for how long?

### A8. Operational Infrastructure Requirements (Infrastructure & Deployment)
*These questions cover the physical and logistical requirements for hosting and running the system. Answers influence cloud vs. on-premise decisions, regulatory compliance, and operational overhead.*

36. Should the system be hosted on-premise at the Sada El Balad facility, on cloud, or hybrid?
37. What is the available internet bandwidth at the broadcast facility?
38. Is there an existing IT team that will manage the system, or is managed support required?
39. What are the uptime requirements (broadcast hours only, 24/7)?
40. Are there data residency requirements (all data must stay within Egypt)?

---

## Part B: Automated Production to Delivery (Channel-in-a-Box)

### B1. Content Categories & Volume

41. Which content categories are priority for initial deployment (lip-sync dubbing, drama series, documentaries, docuseries, other)?
42. How many episodes per week are expected per content category?
43. What is the typical episode duration for each category?
44. How many concurrent series will be in production at any time?
45. What is the target output quality (HD 1080i, Full HD 1080p, 4K)?

### B2. Lip-Sync / Arabic Dubbing

46. What is the source content for dubbing (Turkish dramas, Western content, other)?
47. What is the typical source episode duration?
48. How many source episodes need dubbing per week?
49. Should dubbing be in Egyptian dialect, Modern Standard Arabic, or both depending on content?
50. Is voice casting per character required, or a smaller pool of voice actors covering multiple roles?
51. What level of lip-sync accuracy is acceptable (broadcast-grade frame-accurate, near-sync, approximate)?
52. Are there existing Arabic voice talent contracts, or does the system need to include AI voice generation?

### B3. Drama Series — Multi-Persona Production

53. Are drama scripts written by human writers, generated by AI, or a combination?
54. How many unique character personas are needed across all drama series?
55. Should character appearance and voice remain consistent across episodes and seasons?
56. What is the visual style target (photorealistic, stylized, mixed)?
57. Are there existing character reference materials (concept art, actor photos, style guides)?
58. How many scenes per episode on average?
59. What level of human review is required before an episode moves to playout?

### B4. Documentaries & Docuseries

60. What topics will documentaries cover (historical, current affairs, science, cultural, mixed)?
61. What is the typical documentary duration?
62. Should the system integrate archival footage, or generate all visuals via AI?
63. Is narration AI-generated (TTS) or recorded by human voice talent?
64. Are there fact-checking or editorial review requirements before broadcast?
65. For docuseries, how many episodes per series on average?

### B5. Channel-in-a-Box Integration

66. What Channel-in-a-Box system is currently in use at Sada El Balad?
67. Can the CiAB vendor provide API documentation for integration?
68. What data needs to flow to the CiAB (finished video files, metadata, EPG data, schedule, other)?
69. What data needs to flow from the CiAB (playout confirmation, broadcast logs, scheduling status, other)?
70. Is the CiAB system responsible for final encoding and transmission, or does the production system deliver ready-to-air files?

### B6. Scheduling & Playout

71. How many channels will the production system feed?
72. Is there a fixed broadcast schedule, or dynamic/same-day scheduling?
73. How far in advance should content be ready before its scheduled airtime?
74. Is there a need for emergency or breaking content insertion into the schedule?
75. Should the system generate an Electronic Program Guide (EPG) in Arabic?

### B7. Quality Control & Compliance

76. What automated QC checks are required (audio levels, video artifacts, subtitle sync, other)?
77. Is there a human QC step before content goes to playout?
78. Are there Egyptian broadcast regulatory requirements for content compliance?
79. Are there specific content restrictions or sensitivity guidelines to encode into the system?
80. What is the acceptable error rate for AI-generated content before human intervention is required?

### B8. Storage & Infrastructure

81. What is the expected storage requirement for finished content (hours of broadcast-ready video)?
82. Is there existing storage infrastructure at the facility, or does this need to be provisioned?
83. What is the required retention period for produced content?
84. Should the system support remote access for distributed production teams?
85. What are the backup and disaster recovery requirements?

---

## Part C: General & Commercial

### C1. Timeline & Phasing

86. What is the target launch date for the audience interaction system?
87. What is the target launch date for the production-to-delivery system?
88. Should both systems launch simultaneously, or is phased deployment preferred?
89. If phased, which system is the priority?
90. Are there specific events or seasons driving the timeline (Ramadan, election coverage, other)?

### C2. Team & Training

91. How many technical staff will operate and maintain these systems?
92. What is the current technical capability of the operations team?
93. Is training and onboarding required as part of the delivery?
94. Should documentation be provided in Arabic?

### C3. Support & Maintenance

95. What level of ongoing support is expected (business hours, 24/7, on-site, remote)?
96. Is there a preference for a maintenance contract structure (monthly retainer, per-incident, other)?
97. Should system updates and upgrades be included in the support agreement?

### C4. Budget & Procurement

98. Is there a defined budget range for this project, or should the quote cover multiple tier options?
99. What is the procurement process (direct negotiation, tender, other)?
100. Are there any local partnership or sponsorship requirements for the contract?

---

## Notes

- Questions are numbered for easy reference in responses
- Client can answer in any format (written, meeting, partial answers)
- Priority questions for initial scoping: A1, A2, A6, B1, B5, B6, C1, C4
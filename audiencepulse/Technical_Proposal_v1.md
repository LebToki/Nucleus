# 🚀 AudiencePulse Technical Architecture Proposal v1.0 (WOWDASH Compliant)

## I. Executive Summary & Project Goal
This document outlines the technical blueprint for the **Audience Pulse Platform**, a mission-critical system designed to ingest, validate, curate, and generate auditable insights from decentralized social media streams. The goal is to transition raw data into structured, approved assets ready for broadcast or deep analysis.

**Key Deliverables:** A fully decoupled, microservice-oriented architecture adhering strictly to PSR-12 standards and the **WOWDASH Design System**.
**Core Principle:** Every piece of data must pass through a mandatory, auditable **Approval Gate (Human Oversight)** before it contributes to final metrics.

---

## II. System Architecture Flow Diagram (Conceptual)
*(This diagram maps the entire operational lifecycle from raw input to published insight.)*

`[RAW DATA SOURCES] $\rightarrow$ [INGESTION GATEWAY] $\rightarrow$ [VALIDATION SERVICE] $\rightarrow$ {APPROVAL GATE} $\rightarrow$ [MODERATION/CURATION] $\rightarrow$ [TALLY ENGINE] $\rightarrow$ [FINAL DASHBOARD OUTPUT]`

---

## III. Microservice Deep Dive (The Core Engine)
Each service is an isolated PHP component that communicates only via strict Data Contracts, enforcing architectural rigor and allowing independent updates (Microservices pattern).

### 1. Contract Definition: `AudienceEventDataContract`
*   **Purpose:** The universal JSON/PHP schema for all data passing through the system. Eliminates ambiguity at integration points.
*   **Key Fields:** `event_id`, `source_platform`, `timestamp_utc`, `user_identifier`, `raw_content` (text/media URL), `metadata` (array).

### 2. Ingestion Layer: `IngressGatewayService` (API Endpoint: `/api/v1/ingest`)
*   **Role:** The single point of entry for all external data feeds. It handles connection pooling and initial deserialization.
*   **Flow:** Receives raw payload $\rightarrow$ Calls `ValidatorService::validate()` $\rightarrow$ If valid, passes to the next stage queue (e.g., Kafka/Message Queue).

### 3. Validation & Governance: `ValidatorService` & `CategoryManagementService`
*   **Validation:** Enforces data types and business rules (e.g., ensuring a user is not flagged for spam).
*   **Governance:** The `CategoryManagementService` manages the content taxonomy (Viral, Inspirational, etc.). All incoming events are mapped to one or more categories upon validation.

### 4. Core Processing Loop: `ModerationEngineService` (The Control Gate)
*   **Mechanism:** This service pulls records from a temporary "Review Queue" table. It simulates the manual review process.
*   **Action:** A moderator views the message and decides: **APPROVE**, **REJECT**, or **REQUEST MORE INFO**.
*   **Failure Mode Handling:** If rejected, it logs the reason against the `event_id` for audit purposes.

### 5. Tally & Output: `TallyService` (API Endpoint: `/api/v1/tally`)
*   **Mechanism:** This service is responsible for **atomic commits**. It never just "adds" a score; it calculates the delta and updates the record in an ACID transaction to prevent race conditions.
*   **Metrics Tracked:**
    *   Total Participation (Count of validated unique users).
    *   Total Engagement Score (Weighted sum of approved events/likes).
    *   Child Metrics: Ability to track metrics hierarchically (e.g., Campaign X's total score is the SUM of all its Events A, B, and C scores).

---

## IV. WOWDASH Interface Specifications (The Client View)
All user interactions will be mapped to these three primary screens, ensuring a unified visual experience:

| Module | Goal / Functionality | Core Service Used | Key Widgets Displayed |
| :--- | :--- | :--- | :--- |
| **Dashboard** | Executive summary; KPIs and overall performance. | `TallyService`, `ValidatorService` | Large metric cards for Total Participation, Total Score (WOWDASH-metric-card). Parent/Child relationship visualization chart. |
| **Moderation Queue** | Manual review of flagged content pending human judgment. | `ModerationEngineService` | Message feed with clear status indicators and Approve/Reject actions (WOWDASH-list-item). |
| **Governance Panel** | CRUD operations for maintaining the system's taxonomy. | `CategoryManagementService` | Data table view with forms to create, edit, and archive categories. |

## V. Next Steps & Effort Estimation (Budgetary Proposal)
The code structure is complete. The next phase requires:
1.  [BLOCKER] **Database Schema Fix:** Update the database schema to include all required columns (e.g., `avatar`).
2.  **Integration/Testing Phase:** Implement unit and integration tests for every service method (Requires full environment setup).
3.  **Front-End Integration:** Hook up the final WOWDASH components using the endpoints defined here.

This document serves as the technical contract until these steps are executed.
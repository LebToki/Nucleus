import {
    tx,
    img as unsplashImg,
    shows as demoShows,
    episodes as demoEpisodes,
    presenters as demoPresenters,
    schedule as demoSchedule,
    article as demoArticle,
} from "./data.js";
import { resolveChannel, applyBrand, destination, brandLogo, networkBrandNav } from "../../shared/brands.js";

const channel = resolveChannel(location.search);

// Compute the deployment base path — the OTT page may be served from a
// subdirectory (e.g. /demo/sadaalbalad/public) on staging / remote hosts.
// A root-relative URL like "/api/..." would break in that case because it
// always resolves to the domain root.  basePath gives us the prefix so we
// can build correct relative URLs.
const basePath = window.location.pathname.replace(/\/ott.*$/, '');

// --- OTT data: fetched from Laravel backend API ---
let shows = [],
    episodes = [],
    presenters = [],
    schedule = [],
    article = null;

const localImg = (id, size) => {
    if (!id) return "";
    const programImages = {
        "On-My-Responsibility": "On-My-Responsibility",
        "haqa'iq-wa-asrar": "haqa'iq-wa-asrar",
        "ana-hu-wa-hiya": "ana-hu-wa-hiya",
        "al-matech": "al-matech",
        "sabah-el-balad": "sabah-el-balad",
        "spot-light": "spot-light",
        "rabi-zidni-3ilma": "rabi-zidni-3ilma",
        "tafaseel": "tafaseel",
    };
    const filename = programImages[id];
        if (filename) return `${basePath}/assets/images/programs/${filename}.jpeg`;
    return unsplashImg(id, size);
};

const img = (id, size) => {
    if (!id) return "";
    if (id.startsWith("http")) return id;
    return localImg(id, size);
};

/**
 * Map API response to the shapes that app.js rendering functions expect.
 */
function normalizeData(api) {
    // Normalise shows (programmes)
    shows = (api.shows || []).map((p) => ({
        id: p.id,
        title: p.title || {},
        category: p.category || {},
        desc: p.description || {},
        presenters: (p.presenters || []).map((pr) => pr.id),
        presenter: (p.presenters || [])[0]?.id || null,
        broadcast_days: p.broadcast_days || [],
        broadcast_time: p.broadcast_time || "",
        channel: p.channel?.slug || p.channel?.id || "",
        image: p.hero_image_url || p.logo_url || null,
        logo: p.logo_url || null,
        hero: p.hero_image_url || null,
        slug: p.slug_localized || p.slug?.ar || p.slug?.en || "",
    }));

    // Normalise episodes
    episodes = (api.episodes || []).map((e) => ({
        id: e.id,
        showId: e.programme_id,
        number: e.id,
        title: e.title || {},
        summary: e.summary || {},
        duration: e.duration_seconds
            ? Math.round(e.duration_seconds / 3600)
            : 0,
        duration_seconds: e.duration_seconds || 0,
        image: e.thumbnails?.thumbnail || null,
        transcript: (e.transcript || []).map((tr) => ({
            time: "0:00",
            text: tr.text || tr || "",
        })),
        status: e.status || "published",
        availability_end: e.availability_end || null,
    }));

    // Normalise presenters
    presenters = (api.presenters || []).map((pr) => ({
        id: pr.id,
        name: pr.name || {},
        role: pr.role || {},
        bio: pr.biography || {},
        image: pr.image_url || null,
    }));

    // Build schedule from broadcast data
    if (shows.length > 0) {
        schedule = shows
            .filter((s) => s.broadcast_days?.length && s.broadcast_time)
            .map((s, i) => ({
                showId: s.id,
                start: i * 20,
                duration: 20,
                title: s.title,
            }));
    }

    article = {
        body: "This program is part of the Sada El Balad broadcast network.",
        desc: "Live television, original stories, and extraordinary people.",
    };
}

/**
 * Populate data arrays with demo content so the OTT interface renders
 * even when the backend API is unavailable.
 */
function buildDemoData() {
    shows = demoShows.map((p) => ({
        ...p,
        presenter: p.presenters?.[0] || null,
        logo: p.image || null,
        hero: p.image || null,
        slug: p.id,
    }));
    episodes = demoEpisodes;
    presenters = demoPresenters;
    schedule = demoSchedule;
    article = demoArticle;
}

/**
 * Fetch programme data from the backend API for the active channel.
 */
async function loadOttData() {
    const apiBase = basePath + "/api/v1/ott/channel/";
    try {
        const response = await fetch(apiBase + channel.id, {
            headers: { "Accept": "application/json" },
        });
        if (response.ok) {
            const api = await response.json();
            normalizeData(api);
        } else {
            console.warn("OTT API returned " + response.status + " - falling back to demo data");
            buildDemoData();
        }
    } catch (err) {
        console.warn("OTT API fetch failed - falling back to demo data:", err.message);
        buildDemoData();
    }
}

const localeKey = `elbalad:channel:${channel.id}:locale`;
const savedKey = channel.id === 'elbalad' ? 'elbalad-saved' : `elbalad:channel:${channel.id}:saved`;
const app = document.querySelector("#app");
let lang =
        localStorage.getItem(localeKey) ||
        (channel.id === "elbalad" ? (localStorage.getItem("elbalad-locale") || localStorage.getItem("elbalad-lang")) : null) ||
        channel.defaultLocale,
    minute = 12,
    paused = false,
    studioTab = "overview",
    query = "",
    player = false;
let saved = JSON.parse(localStorage.getItem(savedKey) || "[]");
const t = (v) => (typeof v === "object" ? v[lang] : v);
const L = (en, ar) => t(tx(en, ar));
const SOCIAL_PLATFORMS = [
    { key: "linkedin", label: "LinkedIn" },
    { key: "facebook", label: "Facebook" },
    { key: "instagram", label: "Instagram" },
    { key: "x", label: "X" },
    { key: "tiktok", label: "TikTok" },
    { key: "youtube", label: "YouTube" },
];
const route = () => location.hash.slice(1) || "/";
const link = (url, label, cls = "") =>
    `<a class="${cls}" href="#${url}">${label}</a>`;
const current = () =>
    schedule.find(
        (s) => minute % 120 >= s.start && minute % 120 < s.start + s.duration,
    ) || schedule[0];
const clock = (n) =>
    `${String(20 + Math.floor(n / 60)).padStart(2, "0")}:${String(n % 60).padStart(2, "0")}`;
const escape = (s) =>
    s.replace(
        /[&<>"']/g,
        (c) =>
            ({
                "&": "&amp;",
                "<": "&lt;",
                ">": "&gt;",
                '"': "&quot;",
                "'": "&#39;",
            })[c],
    );
function header() {
    return `<header><a class="brand" href="#/">${brandLogo(channel, lang, escape, 'ott')}<small>${t(channel.tagline)}</small></a><nav><a href="${destination('website',channel.websiteId)}">${L("The website ↗", "الموقع الرئيسي ↗")}</a>${[
        ["/", L("Discover", "اكتشف")],
        ["/live", L("Live TV", "البث المباشر")],
        ["/shows", L("Explore", "استكشف")],
        ["/news", L("Stories", "قصص")],
        ["/saved", L("My list", "قائمتي")],
    ]
        .map(([u, n]) => link(u, n, route() === u ? "active" : ""))
        .join(
            "",
        )}</nav><div class="header-actions">${networkBrandNav(channel.id, 'ott', lang)}<button data-action="search" aria-label="${L("Search", "بحث")}">⌕</button><button data-action="language" lang="${lang === "en" ? "ar" : "en"}">${lang === "en" ? "العربية" : "EN"}</button><!-- Studio workspace moved to admin panel --><span class="avatar">E</span></div></header>`;
}
function footer() {
    return `<footer><div class="brand">${brandLogo(channel, lang, escape, 'ott')}</div><p>${L("Made of Egypt. Open to the world.", "من قلب مصر. إلى العالم.")}</p>    <span>${L("Concept experience · Programme data is illustrative · Sample imagery", "تجربة تصورية · بيانات البرامج توضيحية · صور توضيحية")}</span></footer>`;
}
function card(s, i) {
    return link(
        `/show/${s.id}`,
        `<div class="card-image" style="background-image:url('${img(s.image, 700)}')"><span class="original">${t(channel.originalLabel)}</span><span class="card-play">↗</span></div><div class="card-meta"><span>${t(s.category)}</span><span>2026</span></div><h3>${t(s.title)}</h3>`,
        "show-card",
    );
}
// Route IDs are always strings; API IDs may be numbers — use loose match
const findById = (arr, id) => arr.find((x) => String(x.id) === String(id));
function rail(
    title,
    sub = L(
        "Extraordinary stories. A different perspective.",
        "حكايات استثنائية. ومنظور مختلف.",
    ),
) {
    return `<section class="section"><div class="section-heading"><div><span class="eyebrow">${sub}</span><h2>${title}</h2></div>${link("/shows", L("Explore all ↗", "استكشف الكل ↗"), "text-link")}</div><div class="rail">${shows.map(card).join("")}</div></section>`;
}
function home() {
    let s = shows[0];
    const ep = episodes.find((e) => e.showId === s.id);
    return `<main><section class="hero" style="--hero:url('${img(s.image, 2000)}')"><div class="hero-grain"></div><div class="hero-content"><div class="kicker"><span class="tiny-logo">e+</span> ${t(channel.originalLabel)}<span class="line"></span>${L("NEW SERIES", "سلسلة جديدة")}</div>        <h1>${lang === "en" ? s.title.en : s.title.ar}</h1>
         <p>${t(s.desc)}</p>
         <div class="hero-meta">2026 <b>·</b> ${t(s.category)} <b>·</b> ${L("3 episodes", "٣ حلقات")} <span class="badge">4K</span></div>
         <div class="buttons">${link(ep ? `/episode/${ep.id}` : `/show/${s.id}`, ep ? `▶ ${L("Start watching", "ابدأ المشاهدة")}` : L("Explore the series ↗", "اكتشف السلسلة ↗"), ep ? "button primary" : "button glass")}</div></div>
        <div class="hero-bottom"><span>01 <span class="muted">/ 05</span><i></i></span><span>${L("A DIFFERENT SIDE OF THE STORY", "وجه آخر للقصة")} ↘</span></div>
        <div class="vertical-note">30.0444° N &nbsp; 31.2357° E</div></section>
        <div id="live-strip">${liveStrip()}</div>
        ${rail(L("Worth staying in for", "حكايات تستحق المشاهدة"))}
        <section class="editorial-banner">
            <span class="eyebrow">${L("THE PEOPLE BEHIND THE PERSPECTIVE", "الأشخاص وراء وجهات النظر")}</span>
            <h2>${L("Good stories start<br>with great listeners.", "الحكايات الجيدة تبدأ<br>بمن يجيد الاستماع.")}</h2>
            ${s.presenter ? link("/presenter/" + s.presenter, L("Meet the host ↗", "تعرف على المقدم ↗"), "button glass") : link("/shows", L("Explore all shows ↗", "استكشف كل البرامج ↗"), "button glass")}
        </section>${rail(L("Go somewhere unexpected", "إلى حيث لا تتوقع"), L("CURATED FOR THE CURIOUS", "لمن يقودهم الفضول"))}</main>`;
}
function liveStrip() {
    const c = current(),
        s = shows.find((s) => s.id === c.showId);
    const title = s ? t(s.title) : "";
    return `<div class="live-strip"><span class="live-label"><i></i> ${L("ON AIR", "على aire")}</span><strong>${t(channel.name)}</strong><span class="divider"></span><div class="strip-title">${title}<small>${clock(c.start)} — ${clock(c.start + c.duration)}</small></div><div class="strip-progress"><div style="width:${(((minute % 120) - c.start) / c.duration) * 100}%"></div></div>${link("/live", L("Watch live ↗", "شاهد الآن ↗"), "text-link")}</div>`;
}
function showPage(id) {
    let s = findById(shows, id);
    if (!s) return missing();
    const epis = episodes.filter((e) => e.showId === s.id);
    return `<main><section class="detail-hero" style="--hero:url('${img(s.image, 1800)}')"><div>${link("/shows", L("← All originals", "← جميع الأعمال"), "eyebrow")}<h1>${t(s.title)}</h1><p>${t(s.desc)}</p><div class="buttons">${link(epis.length > 0 ? `/episode/${epis[0].id}` : `/show/${s.id}`, epis.length > 0 ? L("▶ Watch sample episode", "▶ شاهد الحلقة التجريبية") : L("Explore the series ↗", "اكتشف السلسلة ↗"), "button primary")}<button class="button glass" data-save="${s.id}">${saved.includes(s.id) ? L("✓ In my list", "✓ في قائمتنا") : L("＋ My list", "＋ قائمتي")}</button></div></div></section><section class="section"><div class="tabs"><strong>${L("Episodes", "الحلقات")}</strong><span>${L("About the series", "عن السلسلة")}</span><span>2026 · HD · CC</span></div><div class="episode-grid">${episodes
        .filter((e) => e.showId === s.id)
        .map((e) => episodeCard(e))
        .join(
            "",
        )}</div>
        ${s.presenter ? (() => {
            const p = presenters.find((pr) => pr.id === s.presenter);
            if (!p) return "";
            return `<div class="presenter-inline"><img src="${img(p.image, 180)}" alt=""><div><span class="eyebrow">${L("YOUR HOST", "مقدمة البرنامج")}</span><h3>${t(p.name)}</h3></div>${link("/presenter/" + s.presenter, lang === "en" ? `Meet ${t(p.name)} ↗` : `تعرف على ${t(p.name)} ↗`, "text-link")}</div>`;
        })() : ""}
        <p class="muted">${L("Episodes shown are linked sample content for this concept.", "الحلقات المعروضة محتوى تجريبي مترابط لهذا التصور.")}</p></section></main>`;
}
function episodeCard(e) {
    return link(
        "/episode/" + e.id,
        `<div class="episode-image" style="background-image:url('${img(e.image, 700)}')"><span>▶</span><small>${e.duration}:00</small></div><span class="eyebrow">${L("EPISODE", "الحلقة")} ${e.number}</span><h3>${t(e.title)}</h3><p>${t(e.summary)}</p>`,
        "episode-card",
    );
}
function episodePage(id) {
    const e = findById(episodes, id);
    if (!e) return missing();
    return `<main class="page"><div class="breadcrumbs">${link("/show/" + e.showId, t(shows.find((s) => s.id === e.showId).title))} / ${L("Episode", "الحلقة")} ${e.number}</div><div class="mock-player" style="background-image:url('${img(e.image, 1800)}')"><button data-action="play" class="big-play" aria-label="Play preview">${player ? "Ⅱ" : "▶"}</button><span class="player-label">${L("CONCEPT PLAYER · SAMPLE CONTENT", "مشغل تصوري · محتوى تجريبي")}</span><div class="player-controls"><span id="player-state">${player ? L("Preview playing", "المعاينة تعمل") : L("Preview paused", "المعاينة متوقفة")}</span><span>00:00 / ${e.duration}:00 &nbsp; HD &nbsp; ⛶</span></div></div><div class="article-layout"><article><span class="eyebrow">ELBALAD ORIGINAL · 2026</span><h1>${t(e.title)}</h1><p class="lead">${t(e.summary)}</p><h2>${L("Inside this episode", "داخل الحلقة")}</h2><p>${t(article.body)}</p><h2>${L("Episode transcript", "نص الحلقة")}</h2><p class="muted">${L("Illustrative script excerpt, linked to sample timecodes.", "مقتطف توضيحي من النص مرتبط بتوقيتات تجريبية.")}</p>${e.transcript.map((x) => `<button class="transcript" data-time="${x.time}"><span>${x.time}</span>${t(x.text)}</button>`).join("")}<details><summary>${L("What is this episode about?", "عن ماذا تتحدث هذه الحلقة؟")}</summary><p>${t(e.summary)}</p></details></article><aside><span class="eyebrow">${L("CONTINUE EXPLORING", "تابع الاكتشاف")}</span>${episodes
        .filter((x) => x.showId === e.showId && x.id !== id)
        .map(episodeCard)
        .join("")}</aside></div></main>`;
}
function presenterPage(id) {
    let p = findById(presenters, id);
    if (!p) return missing();
    return `<main class="page"><div class="profile"><div class="profile-image" style="background-image:url('${img(p.image, 900)}')"></div><div><span class="eyebrow">${L("THE VOICES OF ELBALAD", "أصوات البلد")}</span><h1>${t(p.name)}</h1><h3>${t(p.role)}</h3><p class="lead">${t(p.bio)}</p><span class="pill">${L("Culture", "ثقافة")}</span> <span class="pill">${L("Documentary", "وثائقي")}</span><p class="muted">${L("Fictional presenter profile for demonstration.", "ملف مقدمة افتراضية لأغراض العرض.")}</p></div></div>    <h2>${lang === "en" ? `Stories with ${t(p.name)}` : `حكايات مع ${t(p.name)}`}</h2>
    <div class="episode-grid">${episodes
        .filter((e) => {
            const sh = shows.find((s) => s.id === e.showId);
            return sh?.presenter === p.id;
        })
        .map(episodeCard)
        .join("")}</div></main>`;
}
function livePage() {
    return `<main class="page"><div class="section-heading"><div><span class="eyebrow">${L("CONNECTED TO THE MOMENT", "معك في كل لحظة")}</span><h1>${L("Live, and in sync.", "مباشر. وفي وقته.")}</h1></div><span class="pill green">● SyncBridge ${L("simulation", "محاكاة")}</span></div><div class="live-layout"><div><div class="mock-player live-player" style="background-image:url('${img(shows[0].image, 1500)}')"><span class="live-label">● ${L("LIVE PREVIEW", "معاينة البث")}</span><div><span class="eyebrow">${t(channel.name)}</span><h2 id="live-title"></h2><p>${L("Simulated broadcast · No live signal connected", "بث محاكى · لا توجد إشارة حية متصلة")}</p></div></div><div id="live-strip">${liveStrip()}</div></div><aside class="sync-panel"><span class="eyebrow">SYNCBRIDGE / CONTROL</span><h2 id="sim-time"></h2><p>${L("A schedule-aware demo clock. Every four seconds advances one broadcast minute.", "ساعة تجريبية مرتبطة بالجدول. كل نبضة تساوي دقيقة بث.")}</p><div class="buttons"><button class="button glass" data-action="pause">${paused ? L("Resume", "استئناف") : L("Pause", "إيقاف")}</button><button class="button primary" data-action="advance">+15 ${L("min", "دقيقة")}</button></div><label>${L("Broadcast time", "توقيت البث")}<input type="range" min="0" max="119" value="${minute % 120}" id="scrub"></label><span class="muted">${L("Loop: 20:00–22:00 · Egypt demo time", "حلقة: ٢٠:٠٠–٢٢:٠٠ · توقيت مصر التجريبي")}</span></aside></div><section class="section no-pad"><div class="section-heading"><h2>${L("Tonight on ElBalad", "الليلة على البلد")}</h2><span class="eyebrow">${L("PROGRAMME GUIDE", "دليل البرامج")}</span></div><div id="schedule"></div></section><section class="participation"><div><span class="eyebrow">AUDIENCEPULSE</span><h2>${L("Be part of the conversation.", "كن جزءًا من الحوار.")}</h2><p>${L("Where should our next Cairo story begin?", "من أين تبدأ حكايتنا القادمة في القاهرة؟")}</p></div><div>${[L("Along the Nile", "على ضفاف النيل"), L("In the old city", "في المدينة القديمة")].map((n) => `<button class="button glass" data-vote="${n}">${n}</button>`).join("")}<p id="vote-result" class="muted">${L("Demo poll · Your vote stays in this session", "تصويت تجريبي · مشاركتك لهذه الجلسة فقط")}</p></div></section></main>`;
}
function updateLive() {
    const c = current(),
        s = shows.find((x) => x.id === c.showId);
    document
        .querySelectorAll("#live-strip")
        .forEach((el) => (el.innerHTML = liveStrip()));
    const title = document.querySelector("#live-title");
    if (title) title.textContent = t(s ? s.title : "");
    const time = document.querySelector("#sim-time");
    if (time) time.textContent = clock(minute % 120);
    let range = document.querySelector("#scrub");
    if (range) range.value = minute % 120;
    const guide = document.querySelector("#schedule");
    if (guide)
        guide.innerHTML = schedule
            .map(
                (x) =>
                    `<div class="schedule-row ${c === x ? "now" : ""}"><span>${clock(x.start)}</span><strong>${t(shows.find((s) => s.id === x.showId)?.title || "")}</strong><span>${x.duration} ${L("min", "دقيقة")}</span><span>${c === x ? "● " + L("On air", "على الهواء") : x.start > minute % 120 ? L("Up next", "لاحقًا") : L("Aired", "تم البث")}</span></div>`,
            )
            .join("");
}
function collection() {
    let filtered = shows.filter((s) =>
        (t(s.title) + " " + t(s.category))
            .toLowerCase()
            .includes(query.toLowerCase()),
    );
    return `<main class="page"><span class="eyebrow">${L("FIND YOUR NEXT FAVOURITE", "اكتشف حكايتك المفضلة")}</span><h1>${L("A world to explore.", "عالم يستحق الاكتشاف.")}</h1><input class="search-input" id="search" placeholder="${L("Search programmes, genres…", "ابحث عن البرامج والأنواع…")}" value="${escape(query)}"><div class="collection-grid" id="search-results">${filtered.map(card).join("") || L("No matching stories.", "لا توجد نتائج.")}</div></main>`;
}
function news() {
    return `<main class="page"><span class="eyebrow">${L("ELBALAD JOURNAL", "حكايات البلد")}</span><h1>${t(article.title)}</h1><div class="article-layout"><article><img class="article-cover" src="${img(shows[0].image, 1400)}" alt="${t(shows[0].title)}"><p class="lead">${t(article.body)}</p><h2>${L("Discover the story on screen", "اكتشف الحكاية على الشاشة")}</h2><p>${t(shows[0].desc)}</p>${link("/show/ala-masooliyyati", L("Explore On My Responsibility ↗", "اكتشف «على مسؤوليتي» ↗"), "button primary")}</article><aside><span class="eyebrow">${L("THE HOST", "المقدمة")}</span><h3>${t(presenters[0].name)}</h3><p>${t(presenters[0].bio)}</p>${link("/presenter/mostafa-bakri", L("View profile ↗", "الملف الشخصي ↗"))}</aside></div></main>`;
}
const studioLabels = {
    overview: tx("Overview", "نظرة عامة"),
    mam: tx("Media library", "مكتبة الوسائط"),
    newsroom: tx("Newsroom", "غرفة الأخبار"),
    social: tx("Social publishing", "النشر الاجتماعي"),
    sync: tx("SyncBridge", "مزامنة البث"),
};
function studio() {
    window.location.href = "/admin";
    return `<main class="page"><h1>${L("Redirecting to workspace...", "إعادة توجيه إلى مساحة العمل...")}</h1></main>`;
}
/* Studio UI removed — now admin-only at /admin */
let storyStatus = "draft",
    attached = false,
    socialScheduled = false;
function studioBody() {
    if (studioTab === "mam")
        return `<div class="metric-row">${metric("128", L("Media assets", "أصل إعلامي"))}${metric("4.2 TB", L("Archive storage", "مساحة الأرشيف"))}${metric("12", L("Awaiting review", "بانتظار المراجعة"))}</div><div class="section-heading"><h2>${L("The source of every story", "بداية كل حكاية")}</h2><button class="button primary" data-action="ingest">＋ ${L("Simulate ingest", "محاكاة الاستيراد")}</button></div><div class="mam-grid">${shows.map((s, i) => `<button class="asset" data-asset="${i}"><img src="${img(s.image, 500)}" alt=""><span class="eyebrow">${i % 2 ? "PROXY READY" : "MASTER · 4K"}</span><h3>${t(s.title)}</h3><span class="muted">${L("Rights: Egypt · Review required", "الحقوق: مصر · تتطلب مراجعة")}</span></button>`).join("")}</div><div id="asset-detail"></div>`;
    if (studioTab === "newsroom")
        return `<div class="workflow"><span class="pill ${storyStatus === "draft" ? "green" : ""}">${L("Draft", "مسودة")}</span>→<span class="pill ${storyStatus === "review" ? "green" : ""}">${L("Editorial review", "مراجعة تحريرية")}</span>→<span class="pill ${storyStatus === "approved" ? "green" : ""}">${L("Approved", "معتمد")}</span></div><div class="editor-grid"><div class="editor"><label>${L("STORY SLUG", "عنوان المادة")}<input id="story-title" value="${L("Cairo / Evening culture package", "القاهرة / التقرير الثقافي المسائي")}"></label><label>${L("SCRIPT · ARABIC / ENGLISH ENTITY", "النص · كيان عربي / إنجليزي")}<textarea id="story-script">${t(article.body)}</textarea></label><div class="buttons"><button class="button glass" data-action="attach">＋ ${L("Attach from MAM", "إرفاق من المكتبة")}</button><button class="button primary" data-action="review">${storyStatus === "draft" ? L("Send to review", "إرسال للمراجعة") : storyStatus === "review" ? L("Approve package", "اعتماد الحزمة") : L("Approved ✓", "معتمد ✓")}</button></div><p id="attached">${attached ? "✓ " + t(shows[0].title) + " · MASTER / 4K" : ""}</p></div><aside class="panel"><span class="eyebrow">${L("RUNDOWN / EVENING BULLETIN", "ترتيب النشرة / المساء")}</span>${["HEADLINES", "CAIRO PACKAGE", "STUDIO INTERVIEW", "WEATHER"].map((n, i) => `<div class="rundown"><span>0${i + 1}</span><strong>${L(n, ["العناوين", "تقرير القاهرة", "حوار الاستوديو", "الطقس"][i])}</strong><span>02:30</span></div>`).join("")}<p class="muted">${L("ENPS / iNEWS-inspired authoring concept. External newsroom connectors are not connected.", "تصور للتحرير مستوحى من ENPS وiNEWS. الموصلات الخارجية غير متصلة.")}</p></aside></div>`;
    if (studioTab === "social")
        return `<div class="editor-grid"><div class="editor"><h2>${L("One story. Every destination.", "حكاية واحدة. كل المنصات.")}</h2><label>${L("CAPTION", "نص المنشور")}<textarea>${L("A different side of Cairo. Discover our new original series tonight on ElBalad+.", "وجه آخر للقاهرة. اكتشف سلسلتنا الجديدة الليلة على البلد+.")}</textarea></label><label>${L("DESTINATION", "المنصة")}<select>${SOCIAL_PLATFORMS.map(p => `<option>${L(p.label, p.label)}</option>`).join("")}</select></label><label>${L("SCHEDULE TIME (LOCAL)", "موعد النشر (محلي)")}<input type="datetime-local" id="social-date"></label><button class="button primary" data-action="schedule">${L("Schedule demo post ↗", "جدولة منشور تجريبي ↗")}</button><p class="muted">${L("No posts are sent to external platforms.", "لا تُرسل منشورات إلى منصات خارجية.")}</p></div><aside class="panel"><span class="eyebrow">${L("PUBLISHING QUEUE", "قائمة النشر")}</span><div id="social-queue">${socialScheduled ? L("✓ Demo post scheduled", "✓ تمت جدولة المنشور التجريبي") : L("Your queue is clear.", "قائمة النشر فارغة.")}</div><img class="article-cover" src="${img(shows[0].image, 500)}" alt=""></aside></div>`;
    if (studioTab === "sync")
        return `<div class="panel"><h2>${L("Broadcast-aware by design", "منظومة تدرك ما يُبث")}</h2><div id="live-strip">${liveStrip()}</div><p>${L("SyncBridge links a programme to its scheduled slot and current playback state. Explore the accelerated demo clock and electronic programme guide.", "يربط SyncBridge البرنامج بموعده وحالة البث الحالية. استكشف الساعة التجريبية ودليل البرامج.")}</p>${link("/live", L("Open broadcast simulator ↗", "افتح محاكي البث ↗"), "button primary")}</div>`;
    return `<div class="metric-row">${metric("24", L("Stories in progress", "مادة قيد العمل"))}${metric("08", L("Ready for air", "جاهزة للبث"))}${metric("16", L("Scheduled posts", "منشور مجدول"))}</div><div class="workspace-feature"><span class="eyebrow">${L("FROM SOURCE TO SCREEN", "من المصدر إلى الشاشة")}</span><h2>${L("The whole newsroom.<br>In one rhythm.", "غرفة الأخبار كاملة.<br>على إيقاع واحد.")}</h2><p>${L("Find the asset. Author the story. Build the rundown. Publish everywhere.", "اعثر على الوسائط. حرر المادة. جهز النشرة. وانشر في كل مكان.")}</p><button class="button primary" data-tab="newsroom">${L("Open the newsroom ↗", "افتح غرفة الأخبار ↗")}</button></div><div class="module-grid">${["mam", "social", "sync"].map((k) => `<button class="panel" data-tab="${k}"><span class="eyebrow">CONNECTED MODULE</span><h2>${t(studioLabels[k])} ↗</h2><p>${L("Explore the workflow", "استكشف سير العمل")}</p></button>`).join("")}</div>`;
}
function metric(n, l) {
    return `<div class="metric"><span>${l}</span><strong>${n}</strong><small>${L("SAMPLE DATA", "بيانات تجريبية")}</small></div>`;
}
function missing() {
    return `<main class="page"><h1>404</h1>${link("/", L("Back to discover", "العودة للاكتشاف"), "button primary")}</main>`;
}
function metadata() {
    let path = route().split("/"),
        entity =
            path[1] === "show"
                ? findById(shows, path[2])
                : path[1] === "episode"
                  ? findById(episodes, path[2])
                  : path[1] === "presenter"
                    ? findById(presenters, path[2])
                    : path[1] === "news"
                      ? article
                      : null;
    document.title = entity
        ? `${t(entity.title || entity.name)} — ElBalad+`
        : "ElBalad+ — " + L("A world of stories", "عالم من الحكايات");
    document.querySelector('meta[name="description"]').content = entity
        ? t(entity.desc || entity.summary || entity.bio || entity.body)
        : L(
              "Live television, original stories, extraordinary people.",
              "بث مباشر، قصص أصلية، وأشخاص استثنائيون.",
          );
    document.querySelector("#entity-schema")?.remove();
    if (entity) {
        let type = {
                show: "TVSeries",
                episode: "TVEpisode",
                presenter: "Person",
                news: "Article",
            }[path[1]],
            schema = {
                "@context": "https://schema.org",
                "@type": type,
                name: t(entity.title || entity.name),
                description: document.querySelector('meta[name="description"]')
                    .content,
                url: location.href,
            };
        if (type !== "Person") schema.inLanguage = lang;
if (type === "TVEpisode") {
        schema.episodeNumber = entity.number;
        schema.partOfSeries = {
            "@type": "TVSeries",
            name: t(shows.find((s) => s.id === entity.showId)?.title || ""),
        };
        }
        let script = document.createElement("script");
        script.id = "entity-schema";
        script.type = "application/ld+json";
        script.textContent = JSON.stringify(schema);
        document.head.append(script);
    }
}
function render() {
    applyBrand(document, channel, 'ott');
    document.documentElement.lang = lang;
    document.documentElement.dir = lang === "ar" ? "rtl" : "ltr";
    const r = route(),
        [_, kind, id] = r.split("/");
    let body =
        r === "/"
            ? home()
            : kind === "show"
              ? showPage(id)
              : kind === "episode"
                ? episodePage(id)
                : kind === "presenter"
                  ? presenterPage(id)
                  : kind === "live"
                    ? livePage()
                    : kind === "shows"
                      ? collection()
                      : kind === "news"
                        ? news()
                        : kind === "studio"
                          ? studio()
                          : kind === "saved"
                            ? `<main class="page"><span class="eyebrow">${L("YOUR PERSONAL COLLECTION", "مجموعتك الخاصة")}</span><h1>${L("My list", "قائمتي")}</h1><div class="collection-grid">${
                                  shows
                                      .filter((s) => saved.includes(s.id))
                                      .map(card)
                                      .join("") ||
                                  `<p>${L("Your next favourite is waiting. Add a series to your list from its page.", "حكايتك المفضلة بانتظارك. أضف سلسلة إلى قائمتك من صفحتها.")}</p>`
                              }</div></main>`
                            : missing();
    app.innerHTML = header() + body + footer();
    metadata();
    document.title = document.title.replace(/ElBalad\+/g, t(channel.ottName));
    updateLive();
}
function toast(msg) {
    let el = document.querySelector("#toast");
    el.textContent = msg;
    el.classList.add("visible");
    setTimeout(() => el.classList.remove("visible"), 3000);
}
document.addEventListener("click", (e) => {
    let el = e.target.closest("button");
    if (!el) return;
    let a = el.dataset.action;
    if (a === "language") {
        lang = lang === "en" ? "ar" : "en";
        localStorage.setItem("elbalad-lang", lang);
        localStorage.setItem(localeKey, lang);
        if (channel.id === "elbalad") localStorage.setItem("elbalad-locale", lang);
        render();
    }
    if (a === "search") {
        location.hash = "/shows";
        setTimeout(() => document.querySelector("#search")?.focus(), 0);
    }
    if (a === "play") {
        player = !player;
        render();
    }
    if (a === "pause") {
        paused = !paused;
        render();
    }
    if (a === "advance") {
        minute = (minute + 15) % 120;
        updateLive();
    }
    if (el.dataset.save) {
        let id = el.dataset.save;
        saved = saved.includes(id)
            ? saved.filter((x) => x !== id)
            : [...saved, id];
        localStorage.setItem(savedKey, JSON.stringify(saved));
        render();
    }
    if (el.dataset.tab) {
        studioTab = el.dataset.tab;
        render();
    }
    if (el.dataset.time)
        toast(L("Demo seek to ", "انتقال تجريبي إلى ") + el.dataset.time);
    if (el.dataset.vote) {
        document.querySelector("#vote-result").textContent =
            L("✓ Demo vote recorded: ", "✓ تم تسجيل التصويت التجريبي: ") +
            el.dataset.vote;
        document
            .querySelectorAll("[data-vote]")
            .forEach((b) => (b.disabled = true));
    }
    if (a === "ingest")
        toast(
            L(
                "Demo ingest queued · Proxy generation simulated",
                "تمت إضافة استيراد تجريبي · محاكاة إنشاء نسخة معاينة",
            ),
        );
    if (el.dataset.asset) {
        let s = shows[Number(el.dataset.asset)];
        document.querySelector("#asset-detail").innerHTML =
            `<div class="panel"><h2>${t(s.title)}</h2><p>MASTER · 3840 × 2160 · 25 fps · Arabic / English</p><p>${L("Rights: Egypt / review required · Linked to programme entity", "الحقوق: مصر / تتطلب مراجعة · مرتبط بكيان البرنامج")}</p><button class="button primary" data-action="use-asset">${L("Use in newsroom ↗", "استخدم في غرفة الأخبار ↗")}</button></div>`;
    }
    if (a === "use-asset") {
        attached = true;
        studioTab = "newsroom";
        render();
    }
    if (a === "attach") {
        attached = true;
        document.querySelector("#attached").textContent =
            "✓ " + t(shows[0].title) + " · MASTER / 4K";
    }
    if (a === "review") {
        storyStatus = storyStatus === "draft" ? "review" : "approved";
        const title = document.querySelector("#story-title").value,
            script = document.querySelector("#story-script").value;
        render();
        document.querySelector("#story-title").value = title;
        document.querySelector("#story-script").value = script;
    }
    if (a === "schedule") {
        const date = document.querySelector("#social-date").value;
        if (!date) {
            toast(
                L("Choose a date and time first", "اختر التاريخ والوقت أولاً"),
            );
            return;
        }
        socialScheduled = true;
        document.querySelector("#social-queue").textContent =
            "✓ " +
            L("Demo scheduled for ", "تمت الجدولة التجريبية في ") +
            date.replace("T", " ");
        toast(
            L(
                "Added to the local demo queue",
                "تمت الإضافة إلى قائمة الانتظار التجريبية",
            ),
        );
    }
});
document.addEventListener("input", (e) => {
    if (e.target.id === "scrub") {
        minute = Number(e.target.value);
        updateLive();
    }
    if (e.target.id === "search") {
        query = e.target.value;
        document.querySelector("#search-results").innerHTML =
            shows
                .filter((s) =>
                    (t(s.title) + " " + t(s.category))
                        .toLowerCase()
                        .includes(query.toLowerCase()),
                )
                .map(card)
                .join("") || L("No matching stories.", "لا توجد نتائج.");
    }
});
window.addEventListener("hashchange", () => {
    player = false;
    render();
    window.scrollTo(0, 0);
});
setInterval(() => {
    if (!paused) {
        minute = (minute + 1) % 120;
        updateLive();
    }
}, 4000);
// Boot: fetch live data from backend, then render
async function boot() {
    await loadOttData();
    render();
}
boot();

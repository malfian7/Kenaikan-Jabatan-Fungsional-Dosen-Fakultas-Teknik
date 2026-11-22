/* ==========================================================================
   NEXUS UI — SIPAK
   Peningkatan progresif untuk tema Nexus. Tanpa dependensi baru (vanilla JS;
   memakai jQuery/DataTables hanya bila sudah dimuat halaman).
   Jika berkas ini gagal dimuat, halaman tetap berfungsi penuh.
   ========================================================================== */
(function () {
  "use strict";

  var doc = document, root = doc.documentElement;
  var reduceMotion = window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  function $(sel, ctx) { return (ctx || doc).querySelector(sel); }
  function $$(sel, ctx) { return Array.prototype.slice.call((ctx || doc).querySelectorAll(sel)); }

  /* ---------- 1. Peran dari URL (/dosen/, /pegawai/, /penilai/) ---------- */
  var m = location.pathname.match(/\/(dosen|pegawai|penilai)\//);
  var role = m ? m[1] : "";
  if (role) root.setAttribute("data-role", role);

  /* ---------- 2. Judul halaman: Title Case, akronim dipertahankan ---------- */
  var ACRONYMS = { PAK: 1, SK: 1, ID: 1, NIP: 1, NIDN: 1, KUM: 1, III: 1, IV: 1, II: 1, TA: 1, S1: 1, S2: 1, S3: 1, "(PAK)": 1 };
  var SMALL = { DAN: 1, DI: 1, KE: 1, DARI: 1, UNTUK: 1, YANG: 1, ATAU: 1 };
  function titleCase(text) {
    var t = text.replace(/\s+/g, " ").trim();
    if (!t || t !== t.toUpperCase() || !/[A-Z]/.test(t)) return text; // hanya ubah yang SEMUA huruf besar
    return t.split(" ").map(function (w, i) {
      var core = w.replace(/[^A-Z0-9\/]/g, "");              // abaikan tanda baca: [PAK] → PAK
      if (ACRONYMS[core]) return w;
      if (i > 0 && SMALL[core]) return w.toLowerCase();
      return w.toLowerCase().replace(/[a-z]/, function (c) { return c.toUpperCase(); });  // huruf pertama setelah tanda baca
    }).join(" ");
  }
  $$(".card-header, .modal-title, .nx-panel-title").forEach(function (el) {
    // Ubah node teks saja agar elemen anak (ikon, <b>) tidak rusak
    (function walk(node) {
      for (var n = node.firstChild; n; n = n.nextSibling) {
        if (n.nodeType === 3 && n.nodeValue.trim()) n.nodeValue = n.nodeValue.replace(n.nodeValue.trim(), titleCase(n.nodeValue.trim()));
        else if (n.nodeType === 1) walk(n);
      }
    })(el);
  });

  /* ---------- 3. Sidebar: tandai tautan aktif, breadcrumb ---------- */
  var activeLink = $("#sidebar-wrapper .list-group-item.bg-info, #sidebar-wrapper .list-group-item.bg-primary");
  if (activeLink) {
    activeLink.classList.add("nx-active");
    activeLink.setAttribute("aria-current", "page");
  }
  var here = $("#nx-here");
  if (here) {
    var firstCardTitle = $(".card-header");
    var label = firstCardTitle ? firstCardTitle.textContent.trim() : "";
    here.textContent = (activeLink && activeLink.textContent.trim()) || label || "Beranda";
    // Halaman anak (mis. detail/periksa): tampilkan judul kartu sebagai lokasi spesifik
    if (activeLink && label && !/beranda/i.test(location.pathname)) {
      var words = function (t) { return t.toLowerCase().replace(/[^a-z0-9 ]/g, " ").split(/\s+/).filter(function (w) { return w.length > 2; }); };
      var navW = words(activeLink.textContent), lblW = words(label);
      var common = lblW.filter(function (w) { return navW.indexOf(w) !== -1; }).length;
      // Tambahkan judul kartu hanya bila benar-benar menambah informasi
      if (common < 2) here.textContent = activeLink.textContent.trim() + "  ·  " + label;
    }
  }

  /* ---------- 4. Toggle sidebar: aria + backdrop mobile + Esc ---------- */
  var wrapper = $("#wrapper"), toggle = $("#menu-toggle");
  if (wrapper && toggle) {
    var backdrop = doc.createElement("div");
    backdrop.className = "nx-backdrop";
    backdrop.setAttribute("aria-hidden", "true");
    wrapper.appendChild(backdrop);

    var isMobile = function () { return window.matchMedia("(max-width: 767.98px)").matches; };
    var syncAria = function () {
      var open = isMobile() ? wrapper.classList.contains("toggled") : !wrapper.classList.contains("toggled");
      toggle.setAttribute("aria-expanded", open ? "true" : "false");
    };
    new MutationObserver(syncAria).observe(wrapper, { attributes: true, attributeFilter: ["class"] });
    window.addEventListener("resize", syncAria);
    syncAria();

    backdrop.addEventListener("click", function () { wrapper.classList.remove("toggled"); });
    doc.addEventListener("keydown", function (e) {
      if (e.key === "Escape" && isMobile() && wrapper.classList.contains("toggled")) {
        wrapper.classList.remove("toggled");
        toggle.focus();
      }
    });
    // Di mobile, memilih menu menutup drawer
    $$("#sidebar-wrapper a").forEach(function (a) {
      a.addEventListener("click", function () { if (isMobile()) wrapper.classList.remove("toggled"); });
    });
  }

  /* ---------- 4b. Tautan lompat ke konten (aksesibilitas keyboard) ---------- */
  var mainArea = $("#page-content-wrapper > .container-fluid");
  if (mainArea && $(".nx-topbar")) {
    mainArea.id = mainArea.id || "nx-main";
    mainArea.setAttribute("tabindex", "-1");
    var skip = doc.createElement("a");
    skip.className = "nx-skip";
    skip.href = "#" + mainArea.id;
    skip.textContent = "Lewati ke konten";
    doc.body.insertBefore(skip, doc.body.firstChild);
  }

  /* ---------- 5. Topbar: bayangan saat di-scroll ---------- */
  var topbar = $(".nx-topbar");
  if (topbar) {
    var onScroll = function () { topbar.classList.toggle("is-scrolled", (window.scrollY || root.scrollTop) > 6); };
    window.addEventListener("scroll", onScroll, { passive: true });
    onScroll();
  }

  /* ---------- 6. Inisial avatar (dua huruf) ---------- */
  $$(".nx-avatar").forEach(function (av) {
    var nm = ($(".nx-user-name") || {}).textContent || "";
    var parts = nm.trim().split(/\s+/).filter(Boolean);
    if (parts.length) av.textContent = (parts[0][0] + (parts[1] ? parts[1][0] : "")).toUpperCase();
  });

  /* ---------- 7. Badge status pada sel tabel ---------- */
  var STATUS = {
    "BELUM DIKIRIM":     "netral",
    "TERKIRIM":          "kirim",
    "BERKAS DITOLAK":    "tolak",
    "DITOLAK":           "tolak",
    "TELAH REVISI":      "revisi",
    "BERKAS DITERIMA":   "stempel",
    "PENILAIAN SELESAI": "stempel",
    "DITERIMA":          "lulus",
    "SELESAI":           "lulus",
    "VALID":             "lulus",
    "TIDAK VALID":       "tolak",
    "INVALID":           "tolak"
  };
  function badgeCell(td) {
    if (td.querySelector(".btn, input, select, textarea")) return;
    if (td.querySelector(".nx-badge")) return;
    // Turun melalui pembungkus tunggal (center, b, strong, font, span) sampai teks atau tautan
    var host = td;
    while (host.children.length === 1 && /^(CENTER|B|STRONG|FONT|SPAN)$/.test(host.firstElementChild.tagName)) host = host.firstElementChild;
    var link = host.children.length === 1 && host.firstElementChild.tagName === "A" && !host.firstElementChild.querySelector("img, i, svg") ? host.firstElementChild : null;
    var txt = (link || host).textContent.replace(/\s+/g, " ").trim().toUpperCase();
    var kind = STATUS[txt];
    if (!kind) return;
    if (link) { link.classList.add("nx-badge", "nx-badge--" + kind); return; }
    if (host.children.length !== 0) return;
    var span = doc.createElement("span");
    span.className = "nx-badge nx-badge--" + kind;
    span.textContent = host.textContent.trim();
    host.textContent = "";
    host.appendChild(span);
  }
  function badgeAll() { $$("table tbody td").forEach(badgeCell); }
  badgeAll();

  /* ---------- 8. Pencarian topbar → kolom cari DataTables ---------- */
  var searchWrap = $("#nx-search"), searchInput = $("#nx-search-input");
  function bindSearch() {
    var dtFilter = $(".dataTables_filter input");
    if (!searchWrap || !searchInput || !dtFilter) return false;
    searchWrap.classList.add("is-on");
    var fire = function () {
      dtFilter.value = searchInput.value;
      dtFilter.dispatchEvent(new Event("input", { bubbles: true }));
      dtFilter.dispatchEvent(new Event("keyup", { bubbles: true }));
    };
    searchInput.addEventListener("input", fire);
    dtFilter.addEventListener("input", function () { if (doc.activeElement !== searchInput) searchInput.value = dtFilter.value; });
    // Kolom cari bawaan DataTables disembunyikan agar tidak ada dua kotak pencarian
    var filterBox = dtFilter.closest(".dataTables_filter");
    if (filterBox) filterBox.style.display = "none";
    return true;
  }
  var bound = false;
  function tryBind() { if (!bound) bound = bindSearch(); return bound; }
  if (!tryBind()) { window.addEventListener("load", function () { setTimeout(tryBind, 60); }); }

  doc.addEventListener("keydown", function (e) {
    if (e.key !== "/" || e.ctrlKey || e.metaKey || e.altKey) return;
    var t = e.target, tag = (t && t.tagName) || "";
    if (/^(INPUT|TEXTAREA|SELECT)$/.test(tag) || (t && t.isContentEditable)) return;
    if (searchWrap && searchWrap.classList.contains("is-on")) { e.preventDefault(); searchInput.focus(); searchInput.select(); }
  });

  /* ---------- 9. Redraw DataTables: perbarui badge ---------- */
  if (window.jQuery) {
    window.jQuery(doc).on("draw.dt init.dt", function () { badgeAll(); });
  }

  /* ---------- 10. Form: status memuat (tanpa disabled, agar nilai tombol tetap terkirim) ---------- */
  $$("form").forEach(function (f) {
    f.addEventListener("submit", function (e) {
      var btn = e.submitter || $("button[type=submit], input[type=submit]", f);
      if (!btn || f.classList.contains("nx-busy")) return;
      f.classList.add("nx-busy");
      btn.classList.add("is-loading");
      btn.setAttribute("aria-busy", "true");
      // Jika submit dibatalkan oleh validasi lain, kembalikan
      setTimeout(function () { if (e.defaultPrevented) { f.classList.remove("nx-busy"); btn.classList.remove("is-loading"); btn.removeAttribute("aria-busy"); } }, 0);
    });
  });
  // Kembali via tombol back (bfcache): reset
  window.addEventListener("pageshow", function (e) {
    if (e.persisted) $$(".is-loading").forEach(function (b) { b.classList.remove("is-loading"); b.removeAttribute("aria-busy"); });
    $$("form.nx-busy").forEach(function (f) { f.classList.remove("nx-busy"); });
  });

  /* ---------- 11. Tampilkan/sembunyikan kata sandi (halaman login) ---------- */
  $$(".nx-pw-toggle").forEach(function (btn) {
    btn.addEventListener("click", function () {
      var input = $("#" + btn.getAttribute("data-target"));
      if (!input) return;
      var show = input.type === "password";
      input.type = show ? "text" : "password";
      btn.setAttribute("aria-pressed", show ? "true" : "false");
      btn.setAttribute("aria-label", show ? "Sembunyikan kata sandi" : "Tampilkan kata sandi");
      btn.innerHTML = '<i class="fas ' + (show ? "fa-eye-slash" : "fa-eye") + '" aria-hidden="true"></i>';
    });
  });

  /* ---------- 12. Hitung naik untuk angka statistik ---------- */
  $$("[data-count]").forEach(function (el) {
    var target = parseInt(el.getAttribute("data-count"), 10);
    if (isNaN(target)) return;
    if (reduceMotion || target === 0) { el.textContent = target; return; }
    var dur = 700, start = null;
    el.textContent = "0";
    function frame(ts) {
      if (start === null) start = ts;
      var p = Math.min((ts - start) / dur, 1);
      var eased = 1 - Math.pow(1 - p, 4); // ease-out kuat
      el.textContent = Math.round(target * eased);
      if (p < 1) requestAnimationFrame(frame);
    }
    setTimeout(function () { requestAnimationFrame(frame); }, 250);
  });

  /* ---------- 13. Sapaan sesuai waktu ---------- */
  $$("[data-greeting]").forEach(function (el) {
    var h = new Date().getHours();
    el.textContent = h < 11 ? "Selamat pagi" : h < 15 ? "Selamat siang" : h < 19 ? "Selamat sore" : "Selamat malam";
  });
  /* ---------- 14. Toast: pesan dari server (#nx-flash) dan window.nxToast ---------- */
  var toastHost = null;
  var TOAST_ICON = { success: "fa-check-circle", error: "fa-exclamation-circle", info: "fa-info-circle" };
  function toastRoot() {
    if (toastHost) return toastHost;
    toastHost = doc.createElement("div");
    toastHost.className = "nx-toasts";
    toastHost.setAttribute("aria-live", "polite");
    toastHost.setAttribute("aria-atomic", "false");
    doc.body.appendChild(toastHost);
    return toastHost;
  }
  function nxToast(msg, type) {
    type = TOAST_ICON[type] ? type : "info";
    var ms = type === "error" ? 7000 : 4500;
    var el = doc.createElement("div");
    el.className = "nx-toast nx-toast--" + type;
    el.setAttribute("role", type === "error" ? "alert" : "status");
    el.style.setProperty("--ms", ms + "ms");
    el.innerHTML = '<i class="fas ' + TOAST_ICON[type] + ' nx-toast-ico" aria-hidden="true"></i>' +
      '<div class="nx-toast-msg"></div>' +
      '<button type="button" class="nx-toast-x" aria-label="Tutup pesan"><i class="fas fa-times" aria-hidden="true"></i></button>' +
      '<span class="nx-toast-time" aria-hidden="true"></span>';
    $(".nx-toast-msg", el).textContent = msg;
    var host = toastRoot();
    host.appendChild(el);
    while (host.children.length > 4) host.removeChild(host.firstElementChild);
    var timer, left = ms, began = Date.now(), gone = false;
    function close() {
      if (gone) return; gone = true; clearTimeout(timer);
      el.classList.add("is-leaving");
      var done = function () { if (el.parentNode) el.parentNode.removeChild(el); };
      el.addEventListener("animationend", done, { once: true });
      setTimeout(done, 400);
    }
    function arm() { began = Date.now(); timer = setTimeout(close, left); el.classList.remove("is-paused"); }
    function hold() { clearTimeout(timer); left -= Date.now() - began; el.classList.add("is-paused"); }
    el.addEventListener("mouseenter", hold);
    el.addEventListener("mouseleave", function () { if (!gone) arm(); });
    el.addEventListener("focusin", hold);
    el.addEventListener("focusout", function () { if (!gone) arm(); });
    $(".nx-toast-x", el).addEventListener("click", close);
    arm();
    return { close: close };
  }
  window.nxToast = nxToast;
  function showFlash() {
    var flash = $("#nx-flash");   // disisipkan server tepat sebelum </body>, setelah skrip ini
    if (flash) setTimeout(function () { nxToast(flash.getAttribute("data-msg") || "", flash.getAttribute("data-type")); }, 220);
  }
  if (doc.readyState === "loading") doc.addEventListener("DOMContentLoaded", showFlash); else showFlash();

  /* ---------- 15. Dialog konfirmasi: tombol[data-confirm-title] ---------- */
  var dlg = null, dlgLast = null;
  function buildDialog() {
    var wrap = doc.createElement("div");
    wrap.className = "nx-dialog";
    wrap.hidden = true;
    wrap.innerHTML =
      '<div class="nx-dialog-scrim" data-close></div>' +
      '<div class="nx-dialog-box" role="alertdialog" aria-modal="true" aria-labelledby="nx-dlg-t" aria-describedby="nx-dlg-d" tabindex="-1">' +
        '<div class="nx-dialog-ico" aria-hidden="true"><i class="fas"></i></div>' +
        '<h2 class="nx-dialog-title" id="nx-dlg-t"></h2>' +
        '<p class="nx-dialog-text" id="nx-dlg-d"></p>' +
        '<div class="nx-dialog-actions">' +
          '<button type="button" class="btn btn-light" data-close>Batal</button>' +
          '<button type="button" class="btn nx-dialog-ok"></button>' +
        '</div>' +
      '</div>';
    doc.body.appendChild(wrap);
    return wrap;
  }
  function openConfirm(btn, onOk) {
    dlg = dlg || buildDialog();
    var tone = btn.getAttribute("data-confirm-tone") || "primary";
    var box = $(".nx-dialog-box", dlg), ok = $(".nx-dialog-ok", dlg), cancel = $("[data-close].btn", dlg);
    box.setAttribute("data-tone", tone);
    $(".nx-dialog-ico i", dlg).className = "fas " + (tone === "danger" ? "fa-trash-alt" : "fa-check");
    $(".nx-dialog-title", dlg).textContent = btn.getAttribute("data-confirm-title") || "Lanjutkan?";
    $(".nx-dialog-text", dlg).textContent = btn.getAttribute("data-confirm-text") || "";
    ok.textContent = btn.getAttribute("data-confirm-label") || "Ya, lanjutkan";
    ok.className = "btn nx-dialog-ok " + (tone === "danger" ? "btn-danger-solid" : "btn-primary");
    // Animasi tumbuh dari tombol yang diklik (origin-aware)
    var r = btn.getBoundingClientRect(), vw = window.innerWidth, vh = window.innerHeight;
    var cx = r.left + r.width / 2, cy = r.top + r.height / 2;
    dlgLast = doc.activeElement;
    dlg.hidden = false;
    var br = box.getBoundingClientRect();
    box.style.transformOrigin = Math.max(0, Math.min(br.width, cx - br.left)) + "px " + Math.max(0, Math.min(br.height, cy - br.top)) + "px";
    root.classList.add("nx-dialog-open");
    void dlg.offsetWidth;
    dlg.classList.add("is-open");
    (tone === "danger" ? cancel : ok).focus({ preventScroll: true });

    function shut(result) {
      doc.removeEventListener("keydown", onKey, true);
      dlg.removeEventListener("click", onClick);
      dlg.classList.remove("is-open");
      root.classList.remove("nx-dialog-open");
      var fin = function () { dlg.hidden = true; };
      if (reduceMotion) fin(); else setTimeout(fin, 160);
      if (result) { onOk(); }
      else if (dlgLast && dlgLast.focus) dlgLast.focus({ preventScroll: true });
    }
    function onClick(e) {
      if (e.target.closest(".nx-dialog-ok")) shut(true);
      else if (e.target.closest("[data-close]")) shut(false);
    }
    function onKey(e) {
      if (e.key === "Escape") { e.preventDefault(); e.stopPropagation(); shut(false); return; }
      if (e.key !== "Tab") return;
      var f = $$("button", box);
      var first = f[0], last = f[f.length - 1];
      if (e.shiftKey && doc.activeElement === first) { e.preventDefault(); last.focus(); }
      else if (!e.shiftKey && doc.activeElement === last) { e.preventDefault(); first.focus(); }
      else if (!box.contains(doc.activeElement)) { e.preventDefault(); first.focus(); }
    }
    doc.addEventListener("keydown", onKey, true);
    dlg.addEventListener("click", onClick);
  }
  doc.addEventListener("click", function (e) {
    var btn = e.target.closest && e.target.closest("[data-confirm-title]");
    if (!btn || e.defaultPrevented) return;
    var form = btn.form || btn.closest("form");
    if (!form) return;
    e.preventDefault();
    openConfirm(btn, function () {
      if (form.requestSubmit) form.requestSubmit(btn.type === "submit" ? btn : undefined);
      else form.submit();
    });
  }, true);

  /* ---------- 16. Bilah progres navigasi (muncul bila halaman berikutnya lambat) ---------- */
  var navBar = doc.createElement("div");
  navBar.className = "nx-nav-progress";
  navBar.setAttribute("aria-hidden", "true");
  doc.body.appendChild(navBar);
  var navTimer = null;
  function navStart() {
    if (navTimer) return;
    navTimer = setTimeout(function () { navBar.classList.add("is-on"); }, 120);
  }
  function navReset() { clearTimeout(navTimer); navTimer = null; navBar.classList.remove("is-on"); }
  doc.addEventListener("click", function (e) {
    var a = e.target.closest && e.target.closest("a[href]");
    if (!a || e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
    if (a.target && a.target !== "_self") return;
    if (a.hasAttribute("download") || a.hasAttribute("data-toggle") || a.hasAttribute("data-dismiss")) return;
    var href = a.getAttribute("href") || "";
    if (!href || href.charAt(0) === "#" || /^(javascript|mailto|tel):/i.test(href)) return;
    if (a.origin && a.origin !== location.origin) return;
    if (a.pathname === location.pathname && a.search === location.search && a.hash) return;
    navStart();
  });
  doc.addEventListener("submit", function (e) { if (!e.defaultPrevented) navStart(); });
  window.addEventListener("pageshow", navReset);
  window.addEventListener("beforeunload", function () { /* biarkan bilah tetap tampil sampai halaman berganti */ });

  /* ---------- 17. Validasi form: bergetar halus + pesan di bawah kolom ---------- */
  $$("form").forEach(function (f) {
    if (f.hasAttribute("novalidate")) return;
    f.setAttribute("novalidate", "");           // pesan bawaan browser diganti pesan Nexus
    f.addEventListener("submit", function (e) {
      if (f.checkValidity()) return;
      e.preventDefault();
      e.stopImmediatePropagation();
      f.classList.remove("nx-busy");
      var first = null;
      $$("input, select, textarea", f).forEach(function (el) {
        if (el.type === "hidden" || el.validity.valid) { clearErr(el); return; }
        showErr(el);
        if (!first) first = el;
      });
      if (first) {
        first.focus({ preventScroll: true });
        if (first.scrollIntoView) first.scrollIntoView({ block: "center", behavior: reduceMotion ? "auto" : "smooth" });
      }
    }, true);
    f.addEventListener("input", function (e) { if (e.target.classList && e.target.classList.contains("is-invalid") && e.target.validity.valid) clearErr(e.target); });
    f.addEventListener("change", function (e) { if (e.target.classList && e.target.classList.contains("is-invalid") && e.target.validity.valid) clearErr(e.target); });
  });
  function errText(el) {
    var v = el.validity;
    if (v.valueMissing) return el.type === "file" ? "Pilih berkas terlebih dahulu." : el.tagName === "SELECT" ? "Pilih salah satu opsi." : "Kolom ini wajib diisi.";
    if (v.typeMismatch) return el.type === "email" ? "Format email belum sesuai." : "Format isian belum sesuai.";
    if (v.tooShort) return "Minimal " + el.minLength + " karakter.";
    if (v.tooLong) return "Maksimal " + el.maxLength + " karakter.";
    if (v.rangeUnderflow) return "Nilai minimal " + el.min + ".";
    if (v.rangeOverflow) return "Nilai maksimal " + el.max + ".";
    if (v.patternMismatch) return el.title || "Format isian belum sesuai.";
    return el.validationMessage || "Isian belum valid.";
  }
  function showErr(el) {
    el.classList.add("is-invalid");
    el.setAttribute("aria-invalid", "true");
    var host = el.closest(".nx-input-wrap") || el;
    var msg = host.parentNode.querySelector(".nx-err[data-for='" + (el.id || el.name) + "']");
    if (!msg) {
      msg = doc.createElement("div");
      msg.className = "nx-err";
      msg.setAttribute("data-for", el.id || el.name || "");
      msg.id = "nx-err-" + (el.id || el.name || Math.random().toString(36).slice(2, 7));
      host.parentNode.insertBefore(msg, host.nextSibling);
    }
    msg.textContent = errText(el);
    el.setAttribute("aria-describedby", msg.id);
    el.classList.remove("nx-shake"); void el.offsetWidth; el.classList.add("nx-shake");
  }
  function clearErr(el) {
    el.classList.remove("is-invalid", "nx-shake");
    el.removeAttribute("aria-invalid");
    var host = el.closest(".nx-input-wrap") || el;
    var msg = host.parentNode && host.parentNode.querySelector(".nx-err[data-for='" + (el.id || el.name) + "']");
    if (msg) { el.removeAttribute("aria-describedby"); msg.parentNode.removeChild(msg); }
  }

  /* ---------- 18. Muncul saat di-scroll (kartu panjang di bawah lipatan) ---------- */
  if ("IntersectionObserver" in window && !reduceMotion) {
    var below = $$(".container-fluid > .card, .nx-panel").filter(function (el) { return el.getBoundingClientRect().top > window.innerHeight * 0.92; });
    if (below.length) {
      var io = new IntersectionObserver(function (ents) {
        ents.forEach(function (en) { if (en.isIntersecting) { en.target.classList.add("nx-in"); io.unobserve(en.target); } });
      }, { rootMargin: "0px 0px -8% 0px", threshold: 0.05 });
      below.forEach(function (el) { el.classList.add("nx-reveal"); io.observe(el); });
    }
  }

  /* ---------- 19. Tombol salin/ripple tidak dipakai; indikator halaman siap ---------- */
  root.classList.add("nx-ready");
})();

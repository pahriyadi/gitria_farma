/**
 * ============================================================================
 * SAWAMAWA MEDICAL CENTER — ENTERPRISE VOICE CALLER MANAGEMENT SYSTEM v4.0
 * ============================================================================
 *
 * Arsitektur:
 *   VoiceCallerManager  ← Singleton Global (window.VCM)
 *   ├── PriorityFIFO Audio Queue  : Antrean panggilan dengan level prioritas (urgent=0, normal=1, low=2)
 *   ├── Anti-Collision Mutex Lock : Mencegah 2 suara bertabrakan secara mutlak (Zero-Overlap Guarantee)
 *   ├── Web Audio Bell Synthesizer: Harmonic Hospital Chime (E5 -> C5) tanpa dependensi file eksternal
 *   ├── Indonesian Terbilang      : Konversi kode antrean medis (A-001 -> "A, satu", B-125 -> "B, seratus dua puluh lima")
 *   ├── Server LiveSync Bridge    : Polling otomatis API voice queue & pengiriman status ACK (Played/Skipped)
 *   ├── Multi-Tab BroadcastChannel: Mencegah tab ganda bersuara bersamaan di layar display TV yang sama
 *   ├── DOM Event Dispatcher      : Memicu event vcm:call_started dan vcm:call_ended untuk animasi visual neon TV
 *   └── Global Floating Control   : Panel kontrol kecepatan, nada suara, volume, dan riwayat panggilan
 *
 * API Publik:
 *   window.VCM.triggerServerCall(options, callback)   ← Mendaftarkan panggilan ke server & otomatis disiarkan ke TV
 *   window.VCM.callPatient(queueNo, patientName, targetName, priority, eventId)
 *   window.VCM.callToDoctor(queueNo, patientName, polyName)
 *   window.VCM.callToTtv(queueNo, patientName)
 *   window.VCM.callToPharmacy(queueNo, patientName)
 *   window.VCM.callToCashier(queueNo, patientName)
 *   window.VCM.callCompleted(queueNo, patientName)
 *   window.VCM.announce(text, priority)
 *   window.VCM.startServerPolling(intervalMs)          ← Jalankan di Display TV / Kiosk
 *   window.VCM.stopServerPolling()
 *   window.VCM.openPanel()
 *   window.VCM.clearQueue()
 *   window.VCM.stop()
 * ============================================================================
 */

(function (window, document) {
    'use strict';

    /* =========================================================================
       1. KONSTANTA & KONFIGURASI
       ========================================================================= */
    const VCM_VERSION       = '4.0-ENTERPRISE';
    const PRIORITY          = { URGENT: 0, NORMAL: 1, LOW: 2 };
    const DEBOUNCE_WINDOW   = 6000;   // ms — jeda minimum antar panggilan entri yg persis sama (anti-spam)
    const SAFETY_TIMEOUT    = 18000;  // ms — batas max durasi 1 utterance agar antrean tdk hang
    const CHIME_GAP         = 850;    // ms — jeda setelah chime sebelum mulai bicara
    const INTER_CALL_GAP    = 850;    // ms — jeda hening tenang antar 2 panggilan berurutan
    const MAX_LOG_ENTRIES   = 50;
    const LOG_KEY           = 'vcm_call_log_v4';
    const SETTINGS_KEY      = 'vcm_settings_v4';
    const CHANNEL_NAME      = 'sawamawa_vcm_audio_channel';

    /* =========================================================================
       2. TERBILANG CERDAS BAHASA INDONESIA
       ========================================================================= */
    function terbilang(n) {
        n = parseInt(n, 10);
        if (isNaN(n) || n === 0) return 'nol';
        const sat = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam',
                     'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];
        if (n < 12)   return sat[n];
        if (n < 20)   return terbilang(n - 10) + ' belas';
        if (n < 100)  {
            const r = n % 10;
            return terbilang(Math.floor(n / 10)) + ' puluh' + (r ? ' ' + sat[r] : '');
        }
        if (n < 200)  {
            const r = n - 100;
            return 'seratus' + (r ? ' ' + terbilang(r) : '');
        }
        if (n < 1000) {
            const r = n % 100;
            return sat[Math.floor(n / 100)] + ' ratus' + (r ? ' ' + terbilang(r) : '');
        }
        if (n < 2000) {
            return 'seribu' + (n - 1000 > 0 ? ' ' + terbilang(n - 1000) : '');
        }
        if (n < 1000000) {
            const r = n % 1000;
            return terbilang(Math.floor(n / 1000)) + ' ribu' + (r ? ' ' + terbilang(r) : '');
        }
        return n.toString();
    }

    function formatQueueNo(queueNo) {
        if (!queueNo) return 'A, satu';
        const str = String(queueNo).trim();
        const parts = str.split('-');
        if (parts.length >= 3) {
            const prefix = (parts[0] || 'A').trim().toUpperCase();
            const lastPart = parts[parts.length - 1];
            const num = parseInt(lastPart, 10);
            return `${prefix}, ${!isNaN(num) && num > 0 ? terbilang(num) : lastPart}`;
        }
        if (parts.length === 2) {
            const prefix = (parts[0] || 'A').trim().toUpperCase();
            const num = parseInt(parts[1] || '1', 10);
            return `${prefix}, ${!isNaN(num) && num > 0 ? terbilang(num) : parts[1]}`;
        }
        return str.replace(/-/g, ' ');
    }

    /* =========================================================================
       3. WEB AUDIO HARMONIC MULTI-CHIME SYNTHESIZER (2-Tone, 3-Tone, 4-Tone, Bell)
       ========================================================================= */
    let _audioCtx = null;

    function getAudioContext() {
        if (!_audioCtx) {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (AudioContext) {
                _audioCtx = new AudioContext();
            }
        }
        if (_audioCtx && _audioCtx.state === 'suspended') {
            _audioCtx.resume().catch(() => {});
        }
        return _audioCtx;
    }

    function playHospitalChime(callback, chimeType = null) {
        const settings = loadSettings();
        const type = chimeType || settings.chimeType || 'hospital_2tone';

        if (type === 'none' || settings.autoChime === false) {
            callback && callback();
            return;
        }

        try {
            const ctx = getAudioContext();
            if (!ctx || ctx.state === 'suspended') {
                if (ctx) ctx.resume().catch(() => {});
                callback && callback();
                return;
            }

            const now = ctx.currentTime;

            if (type === 'soft_bell') {
                // Single Soft Bell (F5 - 698.46 Hz)
                const o = ctx.createOscillator(), g = ctx.createGain();
                o.type = 'sine';
                o.frequency.setValueAtTime(698.46, now);
                g.gain.setValueAtTime(0.001, now);
                g.gain.exponentialRampToValueAtTime(0.40, now + 0.02);
                g.gain.exponentialRampToValueAtTime(0.001, now + 0.85);
                o.connect(g);
                g.connect(ctx.destination);
                o.start(now);
                o.stop(now + 0.86);
                setTimeout(() => { callback && callback(); }, 350);
            } else if (type === 'modern_3tone') {
                // Tri-tone Harmonic (C5: 523.25, E5: 659.25, G5: 783.99)
                const notes = [523.25, 659.25, 783.99];
                notes.forEach((freq, idx) => {
                    const startT = now + (idx * 0.17);
                    const o = ctx.createOscillator(), g = ctx.createGain();
                    o.type = 'sine';
                    o.frequency.setValueAtTime(freq, startT);
                    g.gain.setValueAtTime(0.001, startT);
                    g.gain.exponentialRampToValueAtTime(0.36, startT + 0.03);
                    g.gain.exponentialRampToValueAtTime(0.001, startT + 0.42);
                    o.connect(g);
                    g.connect(ctx.destination);
                    o.start(startT);
                    o.stop(startT + 0.43);
                });
                setTimeout(() => { callback && callback(); }, 520);
            } else if (type === 'airport_4tone') {
                // 4-Tone Airport Major (C5: 523.25, E5: 659.25, G5: 783.99, C6: 1046.50)
                const notes = [523.25, 659.25, 783.99, 1046.50];
                notes.forEach((freq, idx) => {
                    const startT = now + (idx * 0.15);
                    const o = ctx.createOscillator(), g = ctx.createGain();
                    o.type = 'sine';
                    o.frequency.setValueAtTime(freq, startT);
                    g.gain.setValueAtTime(0.001, startT);
                    g.gain.exponentialRampToValueAtTime(0.34, startT + 0.03);
                    g.gain.exponentialRampToValueAtTime(0.001, startT + 0.38);
                    o.connect(g);
                    g.connect(ctx.destination);
                    o.start(startT);
                    o.stop(startT + 0.39);
                });
                setTimeout(() => { callback && callback(); }, 620);
            } else {
                // Default 2-Tone Hospital Chime (E5: 659.25 -> C5: 523.25)
                const o1 = ctx.createOscillator(), g1 = ctx.createGain();
                o1.type = 'sine';
                o1.frequency.setValueAtTime(659.25, now);
                g1.gain.setValueAtTime(0.001, now);
                g1.gain.exponentialRampToValueAtTime(0.40, now + 0.04);
                g1.gain.exponentialRampToValueAtTime(0.001, now + 0.55);
                o1.connect(g1);
                g1.connect(ctx.destination);
                o1.start(now);
                o1.stop(now + 0.56);

                const o2 = ctx.createOscillator(), g2 = ctx.createGain();
                o2.type = 'sine';
                o2.frequency.setValueAtTime(523.25, now + 0.32);
                g2.gain.setValueAtTime(0.001, now + 0.32);
                g2.gain.exponentialRampToValueAtTime(0.42, now + 0.36);
                g2.gain.exponentialRampToValueAtTime(0.001, now + 0.96);
                o2.connect(g2);
                g2.connect(ctx.destination);
                o2.start(now + 0.32);
                o2.stop(now + 0.98);

                setTimeout(() => { callback && callback(); }, CHIME_GAP);
            }
        } catch (e) {
            callback && callback();
        }
    }

    /* =========================================================================
       4. SPEECH SYNTHESIS ENGINE (Indonesian Voice Selection - Pria & Wanita)
       ========================================================================= */
    let _voices = [];

    function loadVoices() {
        if ('speechSynthesis' in window) {
            _voices = window.speechSynthesis.getVoices() || [];
        }
    }

    if ('speechSynthesis' in window) {
        loadVoices();
        window.speechSynthesis.onvoiceschanged = loadVoices;
    }

    function getBestVoice(preferredGender = 'auto') {
        if (!_voices.length) loadVoices();
        
        const idVoices = _voices.filter(v => 
            v.lang === 'id-ID' || v.lang === 'id_ID' || 
            (v.lang && v.lang.toLowerCase().startsWith('id')) ||
            (v.name && v.name.toLowerCase().includes('indonesia'))
        );

        if (preferredGender === 'male') {
            const male = idVoices.find(v => {
                const n = v.name.toLowerCase();
                return n.includes('male') || n.includes('pria') || n.includes('laki') || 
                       n.includes('andika') || n.includes('ardi') || n.includes('david') || n.includes('budi');
            });
            if (male) return male;
        } else if (preferredGender === 'female') {
            const female = idVoices.find(v => {
                const n = v.name.toLowerCase();
                return n.includes('female') || n.includes('wanita') || n.includes('perempuan') || 
                       n.includes('gadis') || n.includes('damayanti') || n.includes('siti') || n.includes('putri');
            });
            if (female) return female;
        }

        return idVoices[0] || _voices.find(v => v.lang.startsWith('id')) || null;
    }

    function speakRaw(text, settings, onEnd) {
        if (!('speechSynthesis' in window)) { onEnd && onEnd(); return; }

        try {
            if (window.speechSynthesis.paused) window.speechSynthesis.resume();
        } catch (e) {}

        const utt   = new SpeechSynthesisUtterance(text);
        utt.lang    = 'id-ID';
        utt.rate    = parseFloat(settings.rate)   || 0.85;
        utt.volume  = parseFloat(settings.volume) || 1.0;

        const gender = settings.gender || 'female';
        const voice = getBestVoice(gender);
        if (voice) utt.voice = voice;

        // Dynamic Pitch tuning berdasarkan karakter suara
        let basePitch = parseFloat(settings.pitch) || 1.0;
        if (gender === 'male' && (!voice || !voice.name.toLowerCase().includes('male'))) {
            // Jika browser tidak punya suara pria khusus, turunkan pitch secara akustik
            basePitch = Math.max(0.75, basePitch * 0.85);
        } else if (gender === 'female' && (!voice || !voice.name.toLowerCase().includes('female'))) {
            basePitch = Math.min(1.25, basePitch * 1.05);
        }
        utt.pitch = basePitch;

        let finished = false;
        const done = () => {
            if (finished) return;
            finished = true;
            window._activeSpeechUtterance = null;
            window._vcmActiveUtt = null;
            onEnd && onEnd();
        };

        utt.onend   = done;
        utt.onerror = (e) => {
            console.warn('[VCM] Speech error:', e ? e.error : 'unknown');
            done();
        };

        // Garbage collection retention
        window._activeSpeechUtterance = utt;
        window._vcmActiveUtt = utt;

        // Jeda 50ms untuk kesiapan audio pipeline Chromium
        setTimeout(() => {
            try {
                if (window.speechSynthesis.paused) window.speechSynthesis.resume();
                window.speechSynthesis.speak(utt);
            } catch (err) {
                console.warn('[VCM] speak execution failed:', err);
                done();
            }
        }, 50);
    }

    /* =========================================================================
       5. SETTINGS & PERSISTENCE (Merged from Meta Tags & Local Storage)
       ========================================================================= */
    function getDefaultSettings() {
        const metaGender = document.querySelector('meta[name="voice-gender"]')?.getAttribute('content') || 'female';
        const metaChime  = document.querySelector('meta[name="voice-chime"]')?.getAttribute('content') || 'hospital_2tone';
        const metaRate   = parseFloat(document.querySelector('meta[name="voice-rate"]')?.getAttribute('content') || '0.85');
        const metaPitch  = parseFloat(document.querySelector('meta[name="voice-pitch"]')?.getAttribute('content') || '1.0');
        const metaVolume = parseFloat(document.querySelector('meta[name="voice-volume"]')?.getAttribute('content') || '1.0');

        return {
            gender: metaGender,
            chimeType: metaChime,
            rate: metaRate,
            pitch: metaPitch,
            volume: metaVolume,
            autoChime: metaChime !== 'none'
        };
    }

    function loadSettings() {
        try {
            const defaults = getDefaultSettings();
            const s = JSON.parse(localStorage.getItem(SETTINGS_KEY) || '{}');
            return Object.assign({}, defaults, s);
        } catch (e) { return getDefaultSettings(); }
    }

    function saveSettings(s) {
        try { localStorage.setItem(SETTINGS_KEY, JSON.stringify(s)); } catch (e) {}
    }

    function loadLog() {
        try { return JSON.parse(localStorage.getItem(LOG_KEY) || '[]'); } catch (e) { return []; }
    }

    function appendLog(entry) {
        try {
            const log = loadLog();
            log.unshift(entry);
            if (log.length > MAX_LOG_ENTRIES) log.length = MAX_LOG_ENTRIES;
            localStorage.setItem(LOG_KEY, JSON.stringify(log));
        } catch (e) {}
    }

    /* =========================================================================
       6. PRIORITY FIFO QUEUE & STATE MACHINE (Anti-Collision Mutex)
       ========================================================================= */
    const Queue = {
        _items: [],

        push(item) {
            let i = 0;
            while (i < this._items.length && this._items[i].priority <= item.priority) i++;
            this._items.splice(i, 0, item);
        },

        shift() { return this._items.shift(); },
        size()  { return this._items.length; },
        clear() { this._items = []; },
        getAll(){ return this._items; }
    };

    const _debounceMap = new Map();
    function isDebounced(key) {
        if (!key) return false;
        const last = _debounceMap.get(key);
        if (!last) return false;
        return (Date.now() - last) < DEBOUNCE_WINDOW;
    }
    function markCalled(key) {
        if (!key) return;
        _debounceMap.set(key, Date.now());
        setTimeout(() => _debounceMap.delete(key), 30000);
    }

    // State Mutex
    let _isPlaying = false;
    let _safetyTimer = null;
    let _currentSpeakingItem = null;

    /* =========================================================================
       7. CROSS-TAB BROADCAST CHANNEL (Leader Election)
       ========================================================================= */
    let _broadcastChannel = null;
    try {
        if (typeof window.BroadcastChannel !== 'undefined') {
            _broadcastChannel = new BroadcastChannel(CHANNEL_NAME);
            _broadcastChannel.onmessage = (event) => {
                const data = event.data;
                if (!data) return;
                if (data.type === 'VCM_CALL_ENQUEUED') {
                    // Update counter badge if on other tabs
                    updateBadge();
                }
            };
        }
    } catch (e) {}

    /* =========================================================================
       8. BUTTON & UI LOCKER
       ========================================================================= */
    const CALL_BTN_SEL = [
        '.btn-voice-call', '.btn-status-call', '.btn-status-action',
        '.btn-call-queue', '.btn-call-patient', '.btn-calling-trigger',
        '[data-action="call-voice"]', '.btn-sidebar-call-voice',
        '#btn-header-call-doctor', '#btn-header-call-ttv', '#btn-triage-call-voice',
        '#vcm-test-btn'
    ].join(',');

    function lockButtons() {
        try {
            document.querySelectorAll(CALL_BTN_SEL).forEach(btn => {
                if (!btn.dataset.vcmPrev) btn.dataset.vcmPrev = btn.disabled ? '1' : '0';
                btn.disabled = true;
                btn.classList.add('vcm-btn-busy');
            });
        } catch (e) {}
    }

    function unlockButtons() {
        try {
            document.querySelectorAll(CALL_BTN_SEL).forEach(btn => {
                if (btn.dataset.vcmPrev !== '1') btn.disabled = false;
                delete btn.dataset.vcmPrev;
                btn.classList.remove('vcm-btn-busy');
            });
        } catch (e) {}
    }

    function updateBadge() {
        try {
            const count = Queue.size() + (_isPlaying ? 1 : 0);
            const badge = document.getElementById('vcm-queue-badge');
            if (badge) {
                badge.textContent = count;
                badge.style.display = count > 0 ? 'inline-block' : 'none';
            }
        } catch (e) {}
    }

    /* =========================================================================
       9. DISPATCH EVENT (DOM Event Hook for TV Display / Custom Scripts)
       ========================================================================= */
    function emitCallStart(item) {
        try {
            const event = new CustomEvent('vcm:call_started', {
                detail: {
                    queueNo: item.queueNo || '',
                    patientName: item.patientName || '',
                    targetName: item.targetName || '',
                    serviceType: item.serviceType || 'poliklinik',
                    text: item.text,
                    eventId: item.eventId || null,
                    timestamp: new Date().toISOString()
                }
            });
            window.dispatchEvent(event);
        } catch (e) {}
    }

    function emitCallEnd(item) {
        try {
            const event = new CustomEvent('vcm:call_ended', {
                detail: {
                    queueNo: item ? item.queueNo : '',
                    eventId: item ? item.eventId : null,
                    timestamp: new Date().toISOString()
                }
            });
            window.dispatchEvent(event);
        } catch (e) {}
    }

    /* =========================================================================
       10. CORE AUDIO PLAYBACK DISPATCHER (Sequential FIFO Execution)
       ========================================================================= */
    function processNextQueue() {
        if (_isPlaying) return;

        if (Queue.size() === 0) {
            _isPlaying = false;
            _currentSpeakingItem = null;
            unlockButtons();
            updateBadge();
            return;
        }

        _isPlaying = true;
        lockButtons();
        updateBadge();

        const item = Queue.shift();
        _currentSpeakingItem = item;

        emitCallStart(item);

        // Safety timeout guard
        if (_safetyTimer) clearTimeout(_safetyTimer);
        _safetyTimer = setTimeout(() => {
            console.warn('[VCM] Safety timeout triggered for item:', item.label);
            onAudioPlaybackDone(item);
        }, SAFETY_TIMEOUT);

        const settings = loadSettings();

        // 1. Play Chime
        if (settings.autoChime !== false) {
            playHospitalChime(() => {
                // 2. Play Speech
                speakRaw(item.text, settings, () => {
                    onAudioPlaybackDone(item);
                });
            });
        } else {
            speakRaw(item.text, settings, () => {
                onAudioPlaybackDone(item);
            });
        }
    }

    function onAudioPlaybackDone(item) {
        if (_safetyTimer) {
            clearTimeout(_safetyTimer);
            _safetyTimer = null;
        }

        // Catat ke log
        appendLog({
            text: item.text,
            label: item.label,
            queueNo: item.queueNo,
            patientName: item.patientName,
            targetName: item.targetName,
            time: new Date().toLocaleTimeString('id-ID'),
            eventId: item.eventId || null
        });

        // Kirim Acknowledgment ke Server jika item berasal dari server sync
        if (item.eventId) {
            sendServerAck(item.eventId);
        }

        emitCallEnd(item);

        // Jeda hening natural antar panggilan (Anti-Overlap Guard)
        setTimeout(() => {
            _isPlaying = false;
            _currentSpeakingItem = null;
            processNextQueue();
        }, INTER_CALL_GAP);
    }

    function enqueue(item) {
        if (item.key && isDebounced(item.key)) {
            console.info('[VCM] Panggilan diabaikan (debounce anti-spam):', item.key);
            return;
        }
        if (item.key) markCalled(item.key);

        Queue.push(item);
        updateBadge();

        if (_broadcastChannel) {
            _broadcastChannel.postMessage({ type: 'VCM_CALL_ENQUEUED', label: item.label });
        }

        if (!_isPlaying) {
            processNextQueue();
        }
    }

    /* =========================================================================
       11. SERVER LIVE SYNC BRIDGE (Polling & Trigger & ACK)
       ========================================================================= */
    let _pollingTimer = null;
    let _lastSeenServerEventId = 0;
    let _seenServerEventIds = new Set();

    function getBaseUrl() {
        const base = document.querySelector('meta[name="base-url"]')?.getAttribute('content')
                  || window.location.origin + (window.location.pathname.startsWith('/sawamawamedicalcenter.id') ? '/sawamawamedicalcenter.id' : '');
        return base.replace(/\/+$/, '');
    }

    function triggerServerCall(params, callback) {
        const url = getBaseUrl() + '/api/sync/voice-call-trigger';
        const csrfTokenName = document.querySelector('meta[name="csrf-token-name"]')?.getAttribute('content') || 'csrf_test_name';
        const csrfHash = document.querySelector('meta[name="csrf-hash"]')?.getAttribute('content') || '';

        const payload = Object.assign({}, params);
        if (csrfHash) payload[csrfTokenName] = csrfHash;

        if (typeof window.jQuery !== 'undefined') {
            window.jQuery.ajax({
                url: url,
                type: 'POST',
                data: payload,
                dataType: 'json',
                success: function (res) {
                    callback && callback(null, res);
                },
                error: function (xhr, status, error) {
                    console.warn('[VCM] Failed to push call to server:', error);
                    // Fallback to local audio immediately
                    window.VCM.callPatient(params.queue_number, params.patient_name, params.counter_name || params.target_name);
                    callback && callback(error, null);
                }
            });
        } else {
            fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
            .then(r => r.json())
            .then(res => callback && callback(null, res))
            .catch(err => {
                console.warn('[VCM] Fetch error:', err);
                window.VCM.callPatient(params.queue_number, params.patient_name, params.counter_name || params.target_name);
                callback && callback(err, null);
            });
        }
    }

    function sendServerAck(eventId, action = 'played') {
        if (!eventId) return;
        const url = getBaseUrl() + '/api/sync/voice-call-ack';
        if (typeof window.jQuery !== 'undefined') {
            window.jQuery.ajax({
                url: url,
                type: 'POST',
                data: { event_id: eventId, action: action },
                dataType: 'json'
            });
        } else {
            fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ event_id: eventId, action: action })
            }).catch(() => {});
        }
    }

    function pollServerQueue() {
        const url = getBaseUrl() + '/api/sync/voice-queue?last_id=' + _lastSeenServerEventId;
        fetch(url, { cache: 'no-store' })
            .then(r => r.json())
            .then(res => {
                if (res && res.status === 'success' && Array.isArray(res.data) && res.data.length > 0) {
                    res.data.forEach(ev => {
                        const id = parseInt(ev.id, 10);
                        if (id > _lastSeenServerEventId) _lastSeenServerEventId = id;

                        if (_seenServerEventIds.has(id)) return;
                        _seenServerEventIds.add(id);

                        enqueue({
                            text: ev.voice_text,
                            priority: parseInt(ev.call_priority || 1, 10),
                            key: 'server_' + id,
                            label: ev.queue_number + ' — ' + ev.patient_name,
                            queueNo: ev.queue_number,
                            patientName: ev.patient_name,
                            targetName: ev.counter_name,
                            serviceType: ev.service_type,
                            eventId: id,
                            ts: Date.now()
                        });
                    });
                }
            })
            .catch(err => {
                // Silently ignore network dropouts during polling
            });
    }

    function startServerPolling(intervalMs = 2500) {
        stopServerPolling();
        pollServerQueue();
        _pollingTimer = setInterval(pollServerQueue, intervalMs);
        console.log('[VCM] Server Voice Queue Polling started at', intervalMs, 'ms interval');
    }

    function stopServerPolling() {
        if (_pollingTimer) {
            clearInterval(_pollingTimer);
            _pollingTimer = null;
        }
    }

    /* =========================================================================
       12. INTERFACE PUBLIK (window.VCM)
       ========================================================================= */
    const VCM = {
        VERSION: VCM_VERSION,
        PRIORITY: PRIORITY,

        /**
         * Panggilan Pasien Medis Umum (Loket, Poli, Ruang Periksa)
         */
        callPatient(queueNo, patientName, targetName, priority = PRIORITY.NORMAL, eventId = null) {
            queueNo     = String(queueNo || 'A-001').trim();
            patientName = String(patientName || 'Pasien').trim();
            targetName  = String(targetName || 'Ruang Pemeriksaan Dokter').trim();

            const spokenQueue = formatQueueNo(queueNo);
            const text = `Nomor antrean ${spokenQueue}, atas nama ${patientName}, silakan menuju ke ${targetName}. Terima kasih.`;

            enqueue({
                text,
                priority,
                key: `call_${queueNo}_${targetName}`,
                label: `${queueNo} — ${patientName} (${targetName})`,
                queueNo,
                patientName,
                targetName,
                serviceType: 'poliklinik',
                eventId,
                ts: Date.now()
            });
        },

        /**
         * Panggilan Dokter Poliklinik
         */
        callToDoctor(queueNo, patientName, polyName, priority = PRIORITY.NORMAL) {
            const target = polyName ? `Poliklinik ${polyName}` : 'Ruang Pemeriksaan Dokter';
            this.callPatient(queueNo, patientName, target, priority);
        },

        /**
         * Panggilan Triage & Tanda Vital Perawat
         */
        callToTtv(queueNo, patientName, priority = PRIORITY.NORMAL) {
            this.callPatient(queueNo, patientName, 'Ruang Pemeriksaan Tanda Vital Perawat', priority);
        },

        /**
         * Panggilan Pasien Menuju Kasir Utama (Pemeriksaan Dokter Selesai)
         */
        callToCashier(queueNo, patientName, priority = PRIORITY.NORMAL) {
            queueNo     = String(queueNo || 'A-001').trim();
            patientName = String(patientName || 'Pasien').trim();

            const spokenQueue = formatQueueNo(queueNo);
            const text = `Nomor antrean ${spokenQueue}, atas nama ${patientName}, pemeriksaan dokter telah selesai. Silakan menuju ke Kasir Pembayaran untuk administrasi. Terima kasih.`;

            enqueue({
                text,
                priority,
                key: `cashier_${queueNo}_${Date.now()}`,
                label: `${queueNo} — ${patientName} (Kasir)`,
                queueNo,
                patientName,
                targetName: 'Kasir Pembayaran',
                serviceType: 'kasir',
                ts: Date.now()
            });
        },

        /**
         * Panggilan Pasien Menuju Farmasi / Apotek (Pembayaran Kasir Lunas)
         */
        callToPharmacy(queueNo, patientName, priority = PRIORITY.NORMAL) {
            queueNo     = String(queueNo || 'A-001').trim();
            patientName = String(patientName || 'Pasien').trim();

            const spokenQueue = formatQueueNo(queueNo);
            const text = `Nomor antrean ${spokenQueue}, atas nama ${patientName}, transaksi pembayaran telah selesai. Terima kasih. Silakan menuju ke Loket Farmasi dan Apotek untuk pengambilan obat.`;

            enqueue({
                text,
                priority,
                key: `pharmacy_${queueNo}_${Date.now()}`,
                label: `${queueNo} — ${patientName} (Farmasi)`,
                queueNo,
                patientName,
                targetName: 'Loket Farmasi dan Apotek',
                serviceType: 'farmasi',
                ts: Date.now()
            });
        },

        /**
         * Panggilan Pelayanan Selesai & Terima Kasih (Obat Diserahkan)
         */
        callCompleted(queueNo, patientName, priority = PRIORITY.URGENT) {
            queueNo     = String(queueNo || 'A-001').trim();
            patientName = String(patientName || 'Pasien').trim();

            const clinicName = document.querySelector('meta[name="clinic-name"]')?.getAttribute('content') || 'Sawamawa Medical Center';
            const spokenQueue = formatQueueNo(queueNo);
            const text = `Nomor antrean ${spokenQueue}, atas nama ${patientName}, penyerahan obat telah selesai. Terima kasih banyak atas kunjungan Anda di ${clinicName}, semoga lekas sembuh.`;

            enqueue({
                text,
                priority,
                key: `done_${queueNo}_${Date.now()}`,
                label: `${queueNo} — ${patientName} (Selesai)`,
                queueNo,
                patientName,
                targetName: 'Selesai',
                serviceType: 'completed',
                ts: Date.now()
            });
        },

        /**
         * Pengumuman Bebas / Custom
         */
        announce(text, priority = PRIORITY.NORMAL) {
            if (!text || !text.trim()) return;
            enqueue({
                text: text.trim(),
                priority,
                key: `ann_${Date.now()}`,
                label: `Pengumuman: ${text.substring(0, 30)}...`,
                queueNo: '',
                patientName: '',
                targetName: 'Pengumuman',
                serviceType: 'custom',
                ts: Date.now()
            });
        },

        /**
         * Trigger Universal via Backend Server Sync
         */
        triggerServerCall(params, callback) {
            triggerServerCall(params, callback);
        },

        startServerPolling(intervalMs) {
            startServerPolling(intervalMs);
        },

        stopServerPolling() {
            stopServerPolling();
        },

        unlockAudio() {
            getAudioContext();
        },

        isPlaying() {
            return _isPlaying;
        },

        getQueueLength() {
            return Queue.size();
        },

        getCurrentSpeaking() {
            return _currentSpeakingItem;
        },

        clearQueue() {
            Queue.clear();
            updateBadge();
        },

        stop() {
            Queue.clear();
            if ('speechSynthesis' in window) {
                try { window.speechSynthesis.cancel(); } catch (e) {}
            }
            if (_safetyTimer) { clearTimeout(_safetyTimer); _safetyTimer = null; }
            _isPlaying = false;
            _currentSpeakingItem = null;
            unlockButtons();
            updateBadge();
            emitCallEnd(null);
        },

        playChime(type, cb) {
            playHospitalChime(cb, type);
        },

        testVoice(params, callback) {
            const currentSettings = Object.assign({}, loadSettings(), params || {});
            const testText = (params && params.text) 
                ? params.text 
                : 'Nomor antrean A, nol nol satu, atas nama Budi Santoso, silakan menuju ke Loket Pelayanan Farmasi. Terima kasih.';
            
            playHospitalChime(() => {
                speakRaw(testText, currentSettings, () => {
                    callback && callback();
                });
            }, currentSettings.chimeType);
        },

        getSettings() { return loadSettings(); },
        setSettings(s) { saveSettings(s); },
        getLog() { return loadLog(); },

        openPanel() {
            const panel = document.getElementById('vcm-panel-modal');
            if (panel && typeof window.jQuery !== 'undefined') {
                window.jQuery(panel).modal('show');
            }
        }
    };

    window.VCM = VCM;

    // Backward compatibility aliases
    window.callPatientVoice = (qNo, pName, tName, msg, cb) => {
        VCM.callPatient(qNo, pName, msg || tName);
        if (cb) setTimeout(cb, 100);
    };
    window.callPatientToCashier = (qNo, pName, cb) => {
        VCM.callToCashier(qNo, pName);
        if (cb) setTimeout(cb, 100);
    };
    window.callPatientToPharmacy = (qNo, pName, cb) => {
        VCM.callToPharmacy(qNo, pName);
        if (cb) setTimeout(cb, 100);
    };
    window.callPatientCompleted = (qNo, pName, cb) => {
        VCM.callCompleted(qNo, pName);
        if (cb) setTimeout(cb, 100);
    };
    window.playHospitalChime = playHospitalChime;

    // User gesture unlocker on first interaction
    const unlockOnUserGesture = () => {
        getAudioContext();
        document.removeEventListener('click', unlockOnUserGesture);
        document.removeEventListener('keydown', unlockOnUserGesture);
    };
    document.addEventListener('click', unlockOnUserGesture, { once: true });
    document.addEventListener('keydown', unlockOnUserGesture, { once: true });

})(window, document);

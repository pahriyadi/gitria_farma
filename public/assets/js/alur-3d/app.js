import * as THREE from 'three';
import { OrbitControls } from 'three/addons/controls/OrbitControls.js';
import { CSS2DRenderer, CSS2DObject } from 'three/addons/renderers/CSS2DRenderer.js';
import { DEFAULT_MODULES, DEFAULT_SCENARIOS, KIND_OPTIONS, STAFF_OPTIONS } from './scenarios.js';

/* ------------------------------------------------------------------ */
/* Konstanta & util                                                    */
/* ------------------------------------------------------------------ */
const STORE_KEY = 'sawamawa_alur3d_v1';
const C = {
    bg: 0xF3EEE5, ground: 0xE9E1D3, slab: 0xDCD2C2, floor: 0xF7F2EA, wall: 0xFFFFFF, trim: 0xD8CDBB,
    wood: 0xC99E6E, dark: 0x2E3A40, screen: 0xDCEDEA, teal: 0x14B8A6, amber: 0xE8A33D, ink: 0x33424A,
    skin: 0xE2BE9B, hair: 0x3B2F2A, route: 0xA7B2B8, plant: 0x6E9C6A, pot: 0xB9825A, linen: 0xF4F1EC, mint: 0xCFE9E6
};
const STAFF_COLORS = { admin: 0x36506B, medis: 0xF4F8F7, chef: 0xFFFFFF, it: 0x2E3A40, eksekutif: 0x5B4636, pasien: 0x7A8F6E };
const TYPE_META = {
    request: { label: 'Permintaan data', color: C.teal },
    response: { label: 'Pengembalian data', color: C.amber },
    process: { label: 'Proses internal', color: C.ink }
};
const ROOM = 8;
const SP = ROOM + 3;

const $ = (s, r = document) => r.querySelector(s);
const clone = (o) => JSON.parse(JSON.stringify(o));
const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
const uid = (p) => p + '-' + Math.random().toString(36).slice(2, 8);
const clamp = (v, a, b) => Math.max(a, Math.min(b, v));
const ease = (p) => (p < 0.5 ? 2 * p * p : 1 - Math.pow(-2 * p + 2, 2) / 2);

/* ------------------------------------------------------------------ */
/* State (persist ke localStorage)                                     */
/* ------------------------------------------------------------------ */
const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

function defaultState() {
    return {
        version: 1,
        modules: clone(DEFAULT_MODULES),
        scenarios: clone(DEFAULT_SCENARIOS),
        active: DEFAULT_SCENARIOS[0].id,
        settings: { speed: 1, loop: false, labels: true, follow: !reduceMotion, routes: true, shadows: true, columns: 5 }
    };
}

function validState(s) {
    return s && Array.isArray(s.modules) && Array.isArray(s.scenarios) && s.scenarios.length > 0 &&
        s.modules.every((m) => m && typeof m.id === 'string' && typeof m.name === 'string') &&
        s.scenarios.every((sc) => sc && typeof sc.id === 'string' && Array.isArray(sc.steps));
}

function loadState() {
    try {
        const raw = localStorage.getItem(STORE_KEY);
        if (!raw) return defaultState();
        const s = JSON.parse(raw);
        if (!validState(s)) return defaultState();
        s.settings = Object.assign(defaultState().settings, s.settings || {});
        return s;
    } catch (e) {
        return defaultState();
    }
}

let state = loadState();
let saveTimer = null;
function saveState() {
    clearTimeout(saveTimer);
    saveTimer = setTimeout(() => {
        try {
            localStorage.setItem(STORE_KEY, JSON.stringify(state));
        } catch (e) {
            toast('Gagal menyimpan ke browser. Penyimpanan penuh atau diblokir, gunakan Ekspor JSON.', 'error');
        }
    }, 250);
}

const moduleById = (id) => state.modules.find((m) => m.id === id);
const visibleModules = () => state.modules.filter((m) => !m.hidden);
const scenario = () => state.scenarios.find((s) => s.id === state.active) || state.scenarios[0];
const isProcess = (step) => step.type === 'process' || step.from === step.to;
const modName = (id) => (moduleById(id) ? moduleById(id).name : '(modul terhapus)');

function stepIssue(step) {
    const f = moduleById(step.from);
    const t = moduleById(isProcess(step) ? step.from : step.to);
    if (!f || !t) return 'Modul tidak ditemukan';
    if (f.hidden || t.hidden) return 'Modul sedang disembunyikan';
    return '';
}
const playableCount = () => scenario().steps.filter((s) => !stepIssue(s)).length;

/* ------------------------------------------------------------------ */
/* Scene                                                               */
/* ------------------------------------------------------------------ */
const stage = $('#stage');
let renderer, labelRenderer, scene, camera, controls, sun, office, routesGroup, packetGroup;
const rooms = new Map();
const pickables = [];
const matCache = new Map();
let extents = { w: 40, d: 30 };
let focusTarget = null;

function mat(color, extra) {
    const key = color + (extra ? JSON.stringify(extra) : '');
    if (!matCache.has(key)) matCache.set(key, new THREE.MeshStandardMaterial(Object.assign({ color, roughness: 0.85, metalness: 0 }, extra || {})));
    return matCache.get(key);
}

function shade(mesh, cast = true) {
    mesh.castShadow = cast;
    mesh.receiveShadow = true;
    return mesh;
}

// y = posisi alas objek, bukan titik tengah
function box(parent, w, h, d, color, x = 0, y = 0, z = 0, material) {
    const m = shade(new THREE.Mesh(new THREE.BoxGeometry(w, h, d), material || mat(color)));
    m.position.set(x, y + h / 2, z);
    parent.add(m);
    return m;
}
function cyl(parent, rt, rb, h, color, x = 0, y = 0, z = 0, seg = 18) {
    const m = shade(new THREE.Mesh(new THREE.CylinderGeometry(rt, rb, h, seg), mat(color)));
    m.position.set(x, y + h / 2, z);
    parent.add(m);
    return m;
}
function ball(parent, r, color, x = 0, y = 0, z = 0) {
    const m = shade(new THREE.Mesh(new THREE.SphereGeometry(r, 20, 14), mat(color)));
    m.position.set(x, y, z);
    parent.add(m);
    return m;
}

function initScene() {
    renderer = new THREE.WebGLRenderer({ antialias: true });
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    renderer.shadowMap.enabled = state.settings.shadows;
    renderer.shadowMap.type = THREE.PCFSoftShadowMap;
    renderer.outputColorSpace = THREE.SRGBColorSpace;
    renderer.domElement.setAttribute('aria-label', 'Tampilan 3D kantor, seret untuk memutar, scroll untuk zoom');
    stage.appendChild(renderer.domElement);

    labelRenderer = new CSS2DRenderer();
    labelRenderer.domElement.className = 'label-layer';
    stage.appendChild(labelRenderer.domElement);

    scene = new THREE.Scene();
    scene.background = new THREE.Color(C.bg);

    camera = new THREE.OrthographicCamera(-10, 10, 10, -10, 0.1, 600);
    controls = new OrbitControls(camera, renderer.domElement);
    controls.enableDamping = true;
    controls.dampingFactor = 0.08;
    controls.minPolarAngle = Math.PI * 0.1;
    controls.maxPolarAngle = Math.PI * 0.43;
    controls.minZoom = 0.45;
    controls.maxZoom = 3.5;
    controls.addEventListener('start', () => { userDragging = true; });
    controls.addEventListener('end', () => { userDragging = false; });

    scene.add(new THREE.HemisphereLight(0xffffff, 0xE6D8C2, 1.25));
    sun = new THREE.DirectionalLight(0xffffff, 1.9);
    sun.position.set(35, 60, 25);
    sun.castShadow = true;
    sun.shadow.mapSize.set(2048, 2048);
    sun.shadow.bias = -0.0006;
    sun.shadow.normalBias = 0.02;
    scene.add(sun, sun.target);

    const ground = new THREE.Mesh(new THREE.PlaneGeometry(600, 600), mat(C.ground));
    ground.rotation.x = -Math.PI / 2;
    ground.position.y = -0.31;
    ground.receiveShadow = true;
    scene.add(ground);

    office = new THREE.Group();
    routesGroup = new THREE.Group();
    packetGroup = new THREE.Group();
    scene.add(office, routesGroup, packetGroup);

    window.addEventListener('resize', resize);
    bindPicking();
}

function disposeGroup(group) {
    group.traverse((o) => {
        if (o.isCSS2DObject && o.element) o.element.remove();
        if (o.geometry) o.geometry.dispose();
        if (o.material && o.material.userData && o.material.userData.unique) {
            if (o.material.map) o.material.map.dispose();
            o.material.dispose();
        }
    });
    group.clear();
}

function resize() {
    const w = stage.clientWidth || window.innerWidth;
    const h = stage.clientHeight || window.innerHeight;
    renderer.setSize(w, h);
    labelRenderer.setSize(w, h);
    const aspect = w / h;
    const span = extents.w + extents.d;
    const half = Math.max((span * 0.37) / aspect, span * 0.27) + 1.5;
    camera.left = -half * aspect;
    camera.right = half * aspect;
    camera.top = half;
    camera.bottom = -half;
    camera.updateProjectionMatrix();
}

function resetCamera() {
    controls.target.set(0, 0, 0);
    camera.position.set(60, 58, 60);
    camera.zoom = 1;
    camera.updateProjectionMatrix();
    controls.update();
    focusTarget = null;
}

/* ------------------------------------------------------------------ */
/* Ruangan & properti                                                  */
/* ------------------------------------------------------------------ */
function makeWorker(g, staff, x, z, rotY = Math.PI) {
    const w = new THREE.Group();
    // Badan utama (torso)
    cyl(w, 0.3, 0.42, 0.85, STAFF_COLORS[staff] || STAFF_COLORS.admin, 0, 0.45, 0);
    // Kepala & rambut
    ball(w, 0.28, C.skin, 0, 1.45, 0);
    const hair = ball(w, 0.29, staff === 'chef' ? 0xFFFFFF : C.hair, 0, 1.52, -0.04);
    hair.scale.set(1, 0.7, 1);
    if (staff === 'chef') cyl(w, 0.26, 0.24, 0.35, 0xFFFFFF, 0, 1.65, 0);
    if (staff === 'medis') cyl(w, 0.31, 0.31, 0.08, C.teal, 0, 1.05, 0);

    // Kaki kiri & kanan (untuk animasi melangkah/berjalan seperti game)
    const legGeo = new THREE.CylinderGeometry(0.1, 0.1, 0.5, 10);
    const legMat = mat(STAFF_COLORS[staff] === 0xF4F8F7 ? 0x2E3A40 : (STAFF_COLORS[staff] || 0x2E3A40));
    
    const leftLeg = shade(new THREE.Mesh(legGeo, legMat));
    leftLeg.position.set(-0.16, 0.25, 0);
    w.add(leftLeg);
    
    const rightLeg = shade(new THREE.Mesh(legGeo, legMat));
    rightLeg.position.set(0.16, 0.25, 0);
    w.add(rightLeg);

    w.position.set(x, 0.2, z);
    w.rotation.y = rotY;
    g.add(w);

    // Attach data animasi
    w.userData = {
        leftLeg,
        rightLeg,
        homePos: new THREE.Vector3(x, 0.2, z),
        targetPos: new THREE.Vector3(x, 0.2, z),
        homeRot: rotY,
        state: 'desk', // 'desk', 'walk', 'idle', 'check'
        stateTimer: Math.random() * 3 + 2,
        walkSpeed: 1.6 + Math.random() * 0.5,
        walkCycle: Math.random() * Math.PI * 2,
        staff
    };

    return w;
}

function makePlant(g, x, z) {
    cyl(g, 0.32, 0.25, 0.5, C.pot, x, 0.2, z);
    const leaf = shade(new THREE.Mesh(new THREE.IcosahedronGeometry(0.55, 0), mat(C.plant)));
    leaf.position.set(x, 1.05, z);
    g.add(leaf);
}

function makeDesk(g, r, x, z) {
    box(g, 2.6, 0.12, 1.2, C.wood, x, 0.95, z);
    [[-1.15, -0.5], [1.15, -0.5], [-1.15, 0.5], [1.15, 0.5]].forEach(([dx, dz]) => box(g, 0.1, 0.75, 0.1, C.dark, x + dx, 0.2, z + dz));
    box(g, 0.12, 0.35, 0.12, C.dark, x + 0.4, 1.07, z - 0.3);
    box(g, 1.1, 0.7, 0.07, C.dark, x + 0.4, 1.4, z - 0.35);
    const screen = new THREE.Mesh(new THREE.PlaneGeometry(0.98, 0.58), r.screenMat);
    screen.position.set(x + 0.4, 1.75, z - 0.31);
    g.add(screen);
    box(g, 0.8, 0.04, 0.28, 0xE8E3DA, x + 0.4, 1.07, z + 0.15);
}

const PROPS = {
    desk(g, r, m) {
        makeDesk(g, r, 0.4, -2.2);
        r.worker = makeWorker(g, m.staff, -0.1, -1.15);
        box(g, 1.6, 0.9, 0.6, 0xEDE7DD, -2.6, 0.2, -3.3);
        makePlant(g, 2.9, -3.1);
    },
    bed(g, r, m) {
        makeDesk(g, r, -1.2, -2.2);
        r.worker = makeWorker(g, m.staff, -1.7, -1.15);
        box(g, 1.3, 0.55, 2.6, C.dark, 2.3, 0.2, 0.2);
        box(g, 1.2, 0.18, 2.5, C.linen, 2.3, 0.75, 0.2);
        box(g, 1.1, 0.16, 0.5, 0xFFFFFF, 2.3, 0.93, -0.75);
        box(g, 1.22, 0.06, 1.3, C.mint, 2.3, 0.93, 0.65);
    },
    lab(g, r, m) {
        box(g, 4.2, 0.95, 1.1, 0xEDEDED, 0.2, 0.2, -3.1);
        box(g, 4.3, 0.08, 1.2, C.dark, 0.2, 1.15, -3.1);
        cyl(g, 0.18, 0.28, 0.25, C.dark, -1.2, 1.23, -3.1);
        cyl(g, 0.07, 0.07, 0.6, C.dark, -1.2, 1.48, -3.2);
        ball(g, 0.12, C.dark, -1.2, 2.1, -3.15);
        [0, 1, 2, 3].forEach((i) => cyl(g, 0.07, 0.07, 0.35, i % 2 ? C.teal : C.amber, -0.2 + i * 0.22, 1.23, -3.0, 10));
        const scr = new THREE.Mesh(new THREE.PlaneGeometry(1, 0.6), r.screenMat);
        box(g, 1.1, 0.7, 0.07, C.dark, 1.5, 1.23, -3.4);
        scr.position.set(1.5, 1.58, -3.36);
        g.add(scr);
        r.worker = makeWorker(g, m.staff, 0.3, -2.0);
        makePlant(g, 3.0, 2.8);
    },
    shelf(g, r, m) {
        [-2.2, 0.6].forEach((sx) => {
            box(g, 2.6, 2.1, 0.7, 0xFFFFFF, sx, 0.2, -3.4);
            [0.75, 1.35, 1.95].forEach((sy, row) => {
                box(g, 2.5, 0.05, 0.6, C.trim, sx, sy + 0.2 - 0.05, -3.35);
                for (let k = 0; k < 5; k++) {
                    const col = (k + row) % 3 === 0 ? C.teal : (k + row) % 3 === 1 ? C.amber : 0xE8E3DA;
                    box(g, 0.34, 0.38, 0.4, col, sx - 1 + k * 0.5, sy + 0.2, -3.35);
                }
            });
        });
        makeDesk(g, r, 1.2, 0.6);
        r.worker = makeWorker(g, m.staff, 0.7, -0.45, 0);
        r.worker.rotation.y = Math.PI;
    },
    kitchen(g, r, m) {
        box(g, 4.6, 1.0, 1.1, 0xE9E5DE, 0, 0.2, -3.1);
        box(g, 4.7, 0.08, 1.2, 0xBFC6CA, 0, 1.2, -3.1);
        box(g, 1.4, 0.04, 0.9, C.dark, -1.2, 1.28, -3.1);
        cyl(g, 0.35, 0.32, 0.45, 0x9AA3A8, -1.5, 1.32, -3.1);
        cyl(g, 0.28, 0.28, 0.3, 0x9AA3A8, -0.8, 1.32, -3.0);
        box(g, 1.4, 0.85, 0.07, C.dark, 1.3, 2.0, -3.6);
        const scr = new THREE.Mesh(new THREE.PlaneGeometry(1.28, 0.74), r.screenMat);
        scr.position.set(1.3, 2.43, -3.56);
        g.add(scr);
        r.worker = makeWorker(g, m.staff, -0.6, -2.0);
        box(g, 2.2, 0.9, 1.0, 0xE9E5DE, 1.6, 0.2, 0.8);
    },
    server(g, r, m) {
        r.leds = [];
        [-2.4, -1.1, 0.2].forEach((sx, i) => {
            box(g, 1.1, 2.6, 1.1, C.dark, sx, 0.2, -3.0);
            for (let k = 0; k < 4; k++) {
                const lm = new THREE.MeshStandardMaterial({ color: C.teal, emissive: C.teal, emissiveIntensity: 0.15 });
                lm.userData.unique = true;
                const led = new THREE.Mesh(new THREE.BoxGeometry(0.6, 0.06, 0.02), lm);
                led.position.set(sx, 0.7 + k * 0.5, -2.44);
                g.add(led);
                r.leds.push({ mat: lm, phase: i * 1.7 + k });
            }
        });
        makeDesk(g, r, 1.6, 0.4);
        r.worker = makeWorker(g, m.staff, 1.1, 1.45);
    },
    tv(g, r) {
        box(g, 4.6, 2.5, 0.12, C.dark, 0.2, 1.2, -3.75);
        const scr = new THREE.Mesh(new THREE.PlaneGeometry(4.4, 2.3), r.screenMat);
        scr.position.set(0.2, 2.45, -3.68);
        g.add(scr);
        r.screenMat.map = makeTextTexture('DISPLAY ANTREAN');
        [-1.2, 1.6].forEach((bx) => {
            box(g, 2.4, 0.45, 0.8, C.wood, bx, 0.2, 0.9);
            box(g, 2.4, 0.6, 0.12, C.wood, bx, 0.65, 1.25);
        });
        const w1 = makeWorker(g, 'pasien', -1.7, 0.85, Math.PI);
        const w2 = makeWorker(g, 'admin', 1.9, 0.85, Math.PI);
        r.workers = [w1, w2];
        r.worker = w1;
    },
    kiosk(g, r, m) {
        box(g, 0.9, 1.7, 0.55, 0xFFFFFF, 0.6, 0.2, -2.6);
        box(g, 0.92, 0.08, 0.57, C.teal, 0.6, 1.9, -2.6);
        const scr = new THREE.Mesh(new THREE.PlaneGeometry(0.7, 0.55), r.screenMat);
        scr.position.set(0.6, 1.4, -2.31);
        g.add(scr);
        r.worker = makeWorker(g, m.staff, 0.6, -1.5);
        box(g, 3.2, 0.03, 1.6, 0xB9AE9C, 0.6, 0.2, 2.2);
        makePlant(g, -2.6, -2.8);
        makePlant(g, 2.9, -2.8);
    }
};

function makeTextTexture(text) {
    const cv = document.createElement('canvas');
    cv.width = 512;
    cv.height = 256;
    const ctx = cv.getContext('2d');
    ctx.fillStyle = '#DCEDEA';
    ctx.fillRect(0, 0, 512, 256);
    ctx.fillStyle = '#0F5F59';
    ctx.font = '700 52px "Plus Jakarta Sans", sans-serif';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    ctx.fillText(text, 256, 128);
    const tex = new THREE.CanvasTexture(cv);
    tex.colorSpace = THREE.SRGBColorSpace;
    return tex;
}

function buildRoom(m, g) {
    const floor = box(g, ROOM, 0.2, ROOM, C.floor, 0, 0, 0);
    floor.castShadow = false;
    floor.userData.moduleId = m.id;
    pickables.push(floor);
    box(g, ROOM, 1.5, 0.25, C.wall, 0, 0.2, -ROOM / 2 + 0.125);
    box(g, 0.25, 1.5, ROOM, C.wall, -ROOM / 2 + 0.125, 0.2, 0);
    box(g, ROOM, 0.08, 0.32, C.trim, 0, 1.7, -ROOM / 2 + 0.125);
    box(g, 0.32, 0.08, ROOM, C.trim, -ROOM / 2 + 0.125, 1.7, 0);

    const padMat = new THREE.MeshBasicMaterial({ color: C.teal, transparent: true, opacity: 0, depthWrite: false });
    padMat.userData.unique = true;
    const pad = new THREE.Mesh(new THREE.RingGeometry(3.0, 3.45, 64), padMat);
    pad.rotation.x = -Math.PI / 2;
    pad.position.y = 0.215;
    g.add(pad);

    const screenMat = new THREE.MeshStandardMaterial({ color: C.screen, emissive: C.teal, emissiveIntensity: 0, roughness: 0.4 });
    screenMat.userData.unique = true;

    const r = { id: m.id, group: g, pad, padMat, screenMat, worker: null, workers: [], leds: [], level: 0, flash: 0, mode: 'idle', modeColor: C.teal, anchor: new THREE.Vector3() };
    (PROPS[m.kind] || PROPS.desk)(g, r, m);
    if (r.worker && !r.workers.length) {
        r.workers = [r.worker];
    }
    r.workers.forEach(w => {
        w.userData.workerBaseY = w.position.y;
    });

    const el = document.createElement('button');
    el.type = 'button';
    el.className = 'room-label';
    el.innerHTML = `<strong>${esc(m.name)}</strong>${m.sub ? `<span>• ${esc(m.sub)}</span>` : ''}`;
    el.title = 'Lihat peran modul ' + m.name;
    el.addEventListener('click', () => openModuleInfo(m.id));
    const label = new CSS2DObject(el);
    label.position.set(0, 0.28, ROOM / 2 - 0.5);
    g.add(label);

    r.anchor.copy(g.position).add(new THREE.Vector3(0.4, 2.8, -0.4));
    return r;
}

function buildOffice() {
    disposeGroup(office);
    rooms.clear();
    pickables.length = 0;
    const mods = visibleModules();
    const cols = clamp(parseInt(state.settings.columns, 10) || 5, 2, 8);
    const colsN = Math.max(1, Math.min(cols, mods.length));
    const rowsN = Math.max(1, Math.ceil(mods.length / cols));
    const offX = ((colsN - 1) * SP) / 2;
    const offZ = ((rowsN - 1) * SP) / 2;

    const slab = box(office, colsN * SP + 1.5, 0.3, rowsN * SP + 1.5, C.slab, 0, -0.3, 0);
    slab.castShadow = false;

    mods.forEach((m, i) => {
        const g = new THREE.Group();
        g.position.set((i % cols) * SP - offX, 0, Math.floor(i / cols) * SP - offZ);
        office.add(g);
        rooms.set(m.id, buildRoom(m, g));
    });

    extents = { w: colsN * SP, d: rowsN * SP };
    const s = Math.max(extents.w, extents.d) * 0.75 + 6;
    Object.assign(sun.shadow.camera, { left: -s, right: s, top: s, bottom: -s, near: 1, far: 220 });
    sun.shadow.camera.updateProjectionMatrix();
    resize();
    $('#emptyOffice').hidden = mods.length > 0;
}

/* ------------------------------------------------------------------ */
/* Rute & paket data                                                   */
/* ------------------------------------------------------------------ */
function curveFor(fromId, toId) {
    const a = rooms.get(fromId).anchor.clone();
    const b = rooms.get(toId).anchor.clone();
    const mid = a.clone().lerp(b, 0.5);
    mid.y += 2.5 + a.distanceTo(b) * 0.22;
    return new THREE.QuadraticBezierCurve3(a, mid, b);
}

const routeMat = new THREE.MeshBasicMaterial({ color: C.route, transparent: true, opacity: 0.45 });

function buildRoutes() {
    disposeGroup(routesGroup);
    if (!state.settings.routes) return;
    const seen = new Set();
    scenario().steps.forEach((s) => {
        if (stepIssue(s) || isProcess(s)) return;
        const key = [s.from, s.to].sort().join('|');
        if (seen.has(key)) return;
        seen.add(key);
        routesGroup.add(new THREE.Mesh(new THREE.TubeGeometry(curveFor(s.from, s.to), 48, 0.04, 6, false), routeMat));
    });
}

function spawnPacket(step) {
    const color = TYPE_META[step.type] ? TYPE_META[step.type].color : C.teal;
    
    // Wadah kurir pembawa berkas / pesan data (Karakter berjalan antar ruangan)
    const courier = new THREE.Group();
    
    // Karakter kurir/staf pembawa data
    const fromRoom = rooms.get(step.from);
    const courierStaff = fromRoom && fromRoom.workers && fromRoom.workers[0] ? fromRoom.workers[0].userData.staff : 'admin';
    const bodyMat = mat(STAFF_COLORS[courierStaff] || STAFF_COLORS.admin);
    
    // Tubuh, kepala kurir
    const torso = shade(new THREE.Mesh(new THREE.CylinderGeometry(0.28, 0.38, 0.8, 12), bodyMat));
    torso.position.y = 0.5;
    courier.add(torso);

    const head = shade(new THREE.Mesh(new THREE.SphereGeometry(0.26, 16, 12), mat(C.skin)));
    head.position.y = 1.08;
    courier.add(head);

    // Kaki kurir (melangkah saat mengantar)
    const legGeo = new THREE.CylinderGeometry(0.09, 0.09, 0.45, 8);
    const legMat = mat(0x2E3A40);
    const leftLeg = shade(new THREE.Mesh(legGeo, legMat));
    leftLeg.position.set(-0.14, 0.22, 0);
    courier.add(leftLeg);

    const rightLeg = shade(new THREE.Mesh(legGeo, legMat));
    rightLeg.position.set(0.14, 0.22, 0);
    courier.add(rightLeg);

    // Berkas / Paket Data yang dipegang di tangan kurir
    const packetGeo = step.type === 'response' ? new THREE.OctahedronGeometry(0.35) : new THREE.BoxGeometry(0.55, 0.2, 0.4);
    const pm = new THREE.MeshStandardMaterial({ color, emissive: color, emissiveIntensity: 0.4, roughness: 0.4 });
    pm.userData.unique = true;
    const packetMesh = shade(new THREE.Mesh(packetGeo, pm));
    packetMesh.position.set(0, 0.65, 0.42); // dipegang di depan dada
    courier.add(packetMesh);

    // Jalur kurva lintasan antar ruangan
    const curve = curveFor(step.from, step.to);
    const tubular = 80;
    const trailGeo = new THREE.TubeGeometry(curve, tubular, 0.04, 6, false);
    trailGeo.setDrawRange(0, 0);
    const tm = new THREE.MeshBasicMaterial({ color, transparent: true, opacity: 0.65 });
    tm.userData.unique = true;
    const trail = new THREE.Mesh(trailGeo, tm);

    // Label tag melayang di atas kurir
    const el = document.createElement('div');
    el.className = 'packet-tag packet-tag--' + step.type;
    el.textContent = step.label || TYPE_META[step.type].label;
    const tag = new CSS2DObject(el);
    tag.position.set(0, 1.6, 0);
    courier.add(tag);

    courier.position.copy(curve.getPoint(0));
    packetGroup.add(courier, trail);

    return {
        mesh: courier,
        leftLeg,
        rightLeg,
        packetMesh,
        curve,
        trail,
        trailGeo,
        tubular,
        walkCycle: 0
    };
}

/* ------------------------------------------------------------------ */
/* Player                                                              */
/* ------------------------------------------------------------------ */
const player = { playing: false, single: false, index: -1, t: 0, dur: 1, active: null, pendingNext: false, gap: 0, finished: false };
let userDragging = false;

function stepDuration(step) {
    return (isProcess(step) ? 2.0 : 2.6) / state.settings.speed;
}

function setRoomMode(id, mode, color) {
    const r = rooms.get(id);
    if (!r) return;
    r.mode = mode;
    if (color !== undefined) r.modeColor = color;
}

function flashRoom(id, color) {
    const r = rooms.get(id);
    if (!r) return;
    r.flash = 1;
    r.modeColor = color;
}

function resetRooms() {
    rooms.forEach((r) => { r.mode = 'idle'; r.flash = 0; });
}

function clearActive() {
    if (player.active) {
        const a = player.active;
        if (a.mesh) {
            a.mesh.children.slice().forEach((c) => { if (c.isCSS2DObject) { c.element.remove(); a.mesh.remove(c); } });
        }
        disposeGroup(packetGroup);
        player.active = null;
    }
    resetRooms();
}

function nextValid(from, dir) {
    const steps = scenario().steps;
    for (let i = from + dir; i >= 0 && i < steps.length; i += dir) {
        if (!stepIssue(steps[i])) return i;
    }
    return -1;
}

function startStep(i) {
    clearActive();
    const step = scenario().steps[i];
    if (!step || stepIssue(step)) return;
    player.index = i;
    player.t = 0;
    player.finished = false;
    player.dur = stepDuration(step);
    const color = TYPE_META[step.type] ? TYPE_META[step.type].color : C.teal;
    const act = { step, process: isProcess(step) };
    if (act.process) {
        setRoomMode(step.from, 'busy', color);
        focusTarget = rooms.get(step.from).group.position.clone();
    } else {
        Object.assign(act, spawnPacket(step));
        setRoomMode(step.from, 'busy', color);
        focusTarget = rooms.get(step.from).group.position.clone().lerp(rooms.get(step.to).group.position, 0.5);
    }
    player.active = act;
    renderNarration();
}

function finishStep() {
    const a = player.active;
    if (!a) return;
    const color = TYPE_META[a.step.type] ? TYPE_META[a.step.type].color : C.teal;
    setRoomMode(a.step.from, 'idle');
    if (!a.process) setRoomMode(a.step.to, 'idle');
    disposeGroup(packetGroup);
    player.active = null;
    flashRoom(a.process ? a.step.from : a.step.to, color);
}

function advance() {
    let n = nextValid(player.index, 1);
    if (n < 0 && state.settings.loop) n = nextValid(-1, 1);
    if (n >= 0) {
        startStep(n);
    } else {
        player.playing = false;
        player.finished = true;
        syncPlayButton();
        renderNarration();
    }
}

function play() {
    if (!playableCount()) {
        toast('Skenario ini belum punya langkah yang bisa diputar. Tambahkan langkah di panel Atur Alur.', 'warn');
        return;
    }
    player.playing = true;
    player.single = false;
    if (!player.active && !player.pendingNext) {
        if (nextValid(player.index, 1) < 0) player.index = -1;
        advance();
    }
    syncPlayButton();
}

function pause() {
    player.playing = false;
    player.pendingNext = false;
    syncPlayButton();
}

function togglePlay() {
    if (player.playing) pause(); else play();
}

function stepBy(dir) {
    pause();
    const base = player.index < 0 && dir < 0 ? scenario().steps.length : player.index;
    const n = nextValid(base, dir);
    if (n < 0) {
        toast(dir > 0 ? 'Sudah di langkah terakhir.' : 'Sudah di langkah pertama.', 'info');
        return;
    }
    jumpTo(n);
}

function jumpTo(i) {
    pause();
    player.single = true;
    startStep(i);
}

function restart() {
    pause();
    clearActive();
    player.index = -1;
    player.finished = false;
    renderNarration();
    play();
}

function stopAndReset() {
    pause();
    clearActive();
    player.index = -1;
    player.finished = false;
}

function update(dt, time) {
    if (player.active && (player.playing || player.single)) {
        player.t += dt;
        const p = Math.min(player.t / player.dur, 1);
        const a = player.active;
        if (!a.process) {
            const e = ease(p);
            const currentPos = a.curve.getPoint(e);
            const nextPos = a.curve.getPoint(Math.min(1, e + 0.02));
            a.mesh.position.copy(currentPos);

            // Hadap ke arah depan jalur perjalanan kurir
            const dir = new THREE.Vector3().subVectors(nextPos, currentPos);
            dir.y = 0;
            if (dir.lengthSq() > 0.0001) {
                dir.normalize();
                a.mesh.rotation.y = Math.atan2(dir.x, dir.z);
            }

            // Animasi langkah kaki kurir saat mengantar data
            a.walkCycle = (a.walkCycle || 0) + dt * 14 * state.settings.speed;
            const swing = Math.sin(a.walkCycle) * 0.55;
            if (a.leftLeg) a.leftLeg.rotation.x = swing;
            if (a.rightLeg) a.rightLeg.rotation.x = -swing;
            if (a.packetMesh) a.packetMesh.position.y = 0.65 + Math.sin(a.walkCycle * 2) * 0.03;

            const idx = a.trailGeo.index ? a.trailGeo.index.count : 0;
            a.trailGeo.setDrawRange(0, Math.floor((e * idx) / 6) * 6);
            if (p > 0.82) setRoomMode(a.step.to, 'busy');
        }
        if (p >= 1) {
            finishStep();
            if (player.playing) {
                player.pendingNext = true;
                player.gap = 0.4 / state.settings.speed;
            } else {
                player.single = false;
                renderNarration();
            }
        }
    } else if (player.pendingNext && player.playing) {
        player.gap -= dt;
        if (player.gap <= 0) {
            player.pendingNext = false;
            advance();
        }
    }

    rooms.forEach((r) => {
        const target = r.mode === 'idle' ? 0 : 1;
        r.level += (target - r.level) * Math.min(1, dt * 6);
        if (r.flash > 0) r.flash = Math.max(0, r.flash - dt * 1.1);
        const glow = Math.max(r.level, r.flash);
        r.padMat.color.setHex(r.modeColor);
        r.padMat.opacity = glow * 0.85;
        r.screenMat.emissive.setHex(r.modeColor === C.ink ? C.teal : r.modeColor);
        r.screenMat.emissiveIntensity = glow * 0.9;

        // Simulasi Karakter Pegawai Berjalan & Bekerja mandiri dalam ruangan (Game Style)
        if (r.workers && r.workers.length) {
            r.workers.forEach((w) => {
                const u = w.userData;
                if (!u) return;
                u.stateTimer -= dt;

                // Transisi State AI Pegawai
                if (u.stateTimer <= 0) {
                    if (r.mode !== 'idle') {
                        // Jika ruangan sedang sibuk menerima/memproses paket, kembali ke meja kerja dengan sigap
                        u.state = 'busy_work';
                        u.targetPos.copy(u.homePos);
                        u.stateTimer = 2.5;
                    } else if (u.state === 'desk' || u.state === 'busy_work') {
                        // Pilih titik jalan-jalan acak di dalam batas ruangan (sekitar x: -2.2 s/d 2.2, z: -1.8 s/d 2.2)
                        u.state = 'walk';
                        const rx = (Math.random() - 0.5) * 4.4;
                        const rz = (Math.random() - 0.5) * 4.0;
                        u.targetPos.set(rx, u.workerBaseY || 0.2, rz);
                        u.stateTimer = Math.random() * 4 + 3;
                    } else if (u.state === 'walk') {
                        // Sampai di titik tujuan, berhenti sebentar dan inspeksi ruangan / berkas
                        u.state = 'idle';
                        u.stateTimer = Math.random() * 3 + 1.5;
                    } else {
                        // Kembali ke meja kerja
                        u.state = 'walk_back';
                        u.targetPos.copy(u.homePos);
                        u.stateTimer = Math.random() * 4 + 3;
                    }
                }

                // Gerakan perpindahan & animasi langkah
                const isWalking = (u.state === 'walk' || u.state === 'walk_back' || (u.state === 'busy_work' && w.position.distanceTo(u.homePos) > 0.1));
                if (isWalking) {
                    const toTarget = new THREE.Vector3().subVectors(u.targetPos, w.position);
                    toTarget.y = 0;
                    const dist = toTarget.length();

                    if (dist > 0.08) {
                        toTarget.normalize();
                        const moveDist = Math.min(dist, u.walkSpeed * dt);
                        w.position.addScaledVector(toTarget, moveDist);

                        // Hadap ke arah berjalan
                        const targetAngle = Math.atan2(toTarget.x, toTarget.z);
                        let angleDiff = targetAngle - w.rotation.y;
                        while (angleDiff > Math.PI) angleDiff -= Math.PI * 2;
                        while (angleDiff < -Math.PI) angleDiff += Math.PI * 2;
                        w.rotation.y += angleDiff * Math.min(1, dt * 8);

                        // Animasi ayunan kaki melangkah
                        u.walkCycle += dt * 9.5;
                        const swing = Math.sin(u.walkCycle) * 0.45;
                        if (u.leftLeg) u.leftLeg.rotation.x = swing;
                        if (u.rightLeg) u.rightLeg.rotation.x = -swing;
                        // Badan naik-turun sedikit saat melangkah (bobbing walk)
                        w.position.y = (u.workerBaseY || 0.2) + Math.abs(Math.sin(u.walkCycle * 2)) * 0.04;
                    } else {
                        // Tiba di tujuan
                        if (u.state === 'walk_back' || u.state === 'busy_work') {
                            u.state = 'desk';
                            u.stateTimer = Math.random() * 5 + 3;
                            w.rotation.y = u.homeRot;
                        } else {
                            u.state = 'idle';
                            u.stateTimer = Math.random() * 3 + 2;
                        }
                    }
                } else {
                    // Posisi diam atau sedang mengetik / bekerja di meja
                    u.walkCycle = 0;
                    if (u.leftLeg) u.leftLeg.rotation.x *= 0.8;
                    if (u.rightLeg) u.rightLeg.rotation.x *= 0.8;

                    if (u.state === 'desk' || r.mode !== 'idle') {
                        // Sedang bekerja / mengetik di meja
                        const workSpeed = r.mode !== 'idle' ? 14 : 7;
                        w.position.y = (u.workerBaseY || 0.2) + Math.abs(Math.sin(time * workSpeed)) * 0.05;
                    } else {
                        // Sedang berdiri mengamati
                        w.position.y = (u.workerBaseY || 0.2) + Math.sin(time * 2.5) * 0.015;
                    }
                }
            });
        }

        r.leds.forEach((l) => {
            l.mat.emissiveIntensity = r.mode !== 'idle' ? (Math.sin(time * 14 + l.phase) > 0 ? 1.4 : 0.2) : 0.15;
        });
    });

    if (state.settings.follow && focusTarget && player.playing && !userDragging) {
        const delta = focusTarget.clone().sub(controls.target).multiplyScalar(Math.min(1, dt * 1.6));
        controls.target.add(delta);
        camera.position.add(delta);
    }
}

/* ------------------------------------------------------------------ */
/* Picking (klik ruangan)                                              */
/* ------------------------------------------------------------------ */
function bindPicking() {
    const ray = new THREE.Raycaster();
    const ndc = new THREE.Vector2();
    let down = null;
    renderer.domElement.addEventListener('pointerdown', (e) => { down = { x: e.clientX, y: e.clientY }; });
    renderer.domElement.addEventListener('pointerup', (e) => {
        if (!down || Math.hypot(e.clientX - down.x, e.clientY - down.y) > 5) return;
        const rect = renderer.domElement.getBoundingClientRect();
        ndc.set(((e.clientX - rect.left) / rect.width) * 2 - 1, -((e.clientY - rect.top) / rect.height) * 2 + 1);
        ray.setFromCamera(ndc, camera);
        const hit = ray.intersectObjects(pickables, false)[0];
        if (hit) openModuleInfo(hit.object.userData.moduleId);
    });
}

/* ------------------------------------------------------------------ */
/* UI: narasi & daftar langkah                                         */
/* ------------------------------------------------------------------ */
function renderScenarioSelect() {
    const sel = $('#scenarioSelect');
    sel.innerHTML = state.scenarios.map((s) => `<option value="${esc(s.id)}">${esc(s.name)}</option>`).join('');
    sel.value = scenario().id;
    $('#scenarioDesc').textContent = scenario().desc || '';
}

function typeBadge(type) {
    const meta = TYPE_META[type] || TYPE_META.request;
    return `<span class="type-badge type-badge--${esc(type)}">${esc(meta.label)}</span>`;
}

function renderNarration() {
    const sc = scenario();
    const steps = sc.steps;
    const now = $('#nowCard');
    const total = playableCount();
    const pos = player.index >= 0 ? steps.slice(0, player.index + 1).filter((s) => !stepIssue(s)).length : 0;
    $('#stepCounter').textContent = total ? `Langkah ${pos} / ${total}` : 'Belum ada langkah';

    if (!steps.length) {
        now.innerHTML = `<p class="now-empty">Skenario ini belum punya langkah. Buka <strong>Atur Alur</strong> untuk menambahkan langkah pertama.</p>
            <button type="button" class="btn btn-primary btn-sm" data-action="open-editor">Tambah langkah</button>`;
    } else if (player.finished) {
        now.innerHTML = `<p class="now-empty">Alur selesai diputar (${total} langkah). Putar ulang dari awal atau pilih skenario lain.</p>
            <button type="button" class="btn btn-primary btn-sm" data-action="restart">Putar ulang</button>`;
    } else if (player.index < 0) {
        now.innerHTML = `<p class="now-empty">Tekan <strong>Putar</strong> untuk melihat data bergerak antar ruangan, atau klik salah satu langkah di bawah.</p>`;
    } else {
        const s = steps[player.index];
        const route = isProcess(s) ? esc(modName(s.from)) : `${esc(modName(s.from))} <span class="route-arrow" aria-label="ke">→</span> ${esc(modName(s.to))}`;
        now.innerHTML = `${typeBadge(s.type)}
            <p class="now-route">${route}</p>
            <h2 class="now-title">${esc(s.label || '(tanpa judul)')}</h2>
            ${s.payload ? `<p class="now-payload"><span>Data</span><code>${esc(s.payload)}</code></p>` : ''}`;
    }

    const list = $('#stepList');
    list.innerHTML = steps.map((s, i) => {
        const issue = stepIssue(s);
        const route = isProcess(s) ? modName(s.from) : `${modName(s.from)} → ${modName(s.to)}`;
        return `<li>
            <button type="button" class="step-item step-item--${esc(s.type)}${i === player.index ? ' is-current' : ''}" data-jump="${i}" ${issue ? 'disabled' : ''}>
                <span class="step-no">${i + 1}</span>
                <span class="step-text"><strong>${esc(s.label || '(tanpa judul)')}</strong><small>${esc(route)}${issue ? ' · ' + esc(issue) : ''}</small></span>
            </button>
        </li>`;
    }).join('');
    const cur = list.querySelector('.is-current');
    if (cur) cur.scrollIntoView({ block: 'nearest', behavior: reduceMotion ? 'auto' : 'smooth' });
}

function syncPlayButton() {
    const btn = $('#btnPlay');
    btn.setAttribute('aria-pressed', String(player.playing));
    btn.querySelector('i').className = player.playing ? 'fas fa-pause' : 'fas fa-play';
    btn.querySelector('span').textContent = player.playing ? 'Jeda' : 'Putar';
}

/* ------------------------------------------------------------------ */
/* UI: info modul                                                      */
/* ------------------------------------------------------------------ */
function openModuleInfo(id) {
    const m = moduleById(id);
    if (!m) return;
    const dlg = $('#moduleDialog');
    const involved = scenario().steps.map((s, i) => ({ s, i })).filter(({ s }) => s.from === id || s.to === id);
    $('#moduleDialogTitle').textContent = m.name;
    $('#moduleDialogSub').textContent = m.sub || '';
    $('#moduleDialogBody').innerHTML = involved.length
        ? `<p class="muted">Terlibat di ${involved.length} langkah pada skenario "${esc(scenario().name)}":</p>
           <ul class="mini-steps">${involved.map(({ s, i }) => {
                const dir = isProcess(s) ? 'proses di sini' : s.from === id ? 'kirim ke ' + modName(s.to) : 'terima dari ' + modName(s.from);
                return `<li><button type="button" class="step-item step-item--${esc(s.type)}" data-jump="${i}" data-close-dialog ${stepIssue(s) ? 'disabled' : ''}>
                    <span class="step-no">${i + 1}</span><span class="step-text"><strong>${esc(s.label)}</strong><small>${esc(dir)}</small></span></button></li>`;
            }).join('')}</ul>`
        : `<p class="muted">Modul ini belum dipakai di skenario "${esc(scenario().name)}". Tambahkan langkah agar ruangan ini ikut bekerja.</p>`;
    dlg.dataset.moduleId = id;
    dlg.showModal();
}

/* ------------------------------------------------------------------ */
/* UI: editor                                                          */
/* ------------------------------------------------------------------ */
function moduleOptions(selected) {
    return state.modules.map((m) => `<option value="${esc(m.id)}"${m.id === selected ? ' selected' : ''}>${esc(m.name)}${m.hidden ? ' (disembunyikan)' : ''}</option>`).join('')
        + (selected && !moduleById(selected) ? `<option value="${esc(selected)}" selected>(modul terhapus)</option>` : '');
}
function simpleOptions(list, selected) {
    return list.map((o) => `<option value="${esc(o.value)}"${o.value === selected ? ' selected' : ''}>${esc(o.label)}</option>`).join('');
}

function renderStepsEditor() {
    const sc = scenario();
    $('#scName').value = sc.name;
    $('#scDesc').value = sc.desc || '';
    $('#btnDeleteScenario').disabled = state.scenarios.length <= 1;
    const wrap = $('#stepsEditor');
    if (!sc.steps.length) {
        wrap.innerHTML = '<p class="muted editor-empty">Belum ada langkah. Klik "Tambah langkah" untuk membuat alur pertama.</p>';
        return;
    }
    wrap.innerHTML = sc.steps.map((s, i) => {
        const issue = stepIssue(s);
        return `<li class="step-edit" data-i="${i}">
            <div class="step-edit__head">
                <span class="step-no">${i + 1}</span>
                <select data-f="type" aria-label="Jenis langkah ${i + 1}">${simpleOptions([
                    { value: 'request', label: 'Permintaan data' }, { value: 'response', label: 'Pengembalian data' }, { value: 'process', label: 'Proses internal' }
                ], s.type)}</select>
                <div class="step-edit__tools">
                    <button type="button" class="icon-btn" data-step-act="up" ${i === 0 ? 'disabled' : ''} aria-label="Naikkan langkah ${i + 1}"><i class="fas fa-arrow-up"></i></button>
                    <button type="button" class="icon-btn" data-step-act="down" ${i === sc.steps.length - 1 ? 'disabled' : ''} aria-label="Turunkan langkah ${i + 1}"><i class="fas fa-arrow-down"></i></button>
                    <button type="button" class="icon-btn" data-step-act="dup" aria-label="Duplikat langkah ${i + 1}"><i class="far fa-copy"></i></button>
                    <button type="button" class="icon-btn icon-btn--danger" data-step-act="del" aria-label="Hapus langkah ${i + 1}"><i class="fas fa-trash"></i></button>
                </div>
            </div>
            <div class="step-edit__route">
                <label><span>Dari</span><select data-f="from">${moduleOptions(s.from)}</select></label>
                <label class="${isProcess(s) && s.type === 'process' ? 'is-disabled' : ''}"><span>Ke</span><select data-f="to" ${s.type === 'process' ? 'disabled' : ''}>${moduleOptions(s.to)}</select></label>
            </div>
            <label class="field"><span>Judul langkah</span><input type="text" data-f="label" value="${esc(s.label)}" maxlength="90" placeholder="Contoh: Simpan tanda vital"></label>
            <label class="field"><span>Data yang dikirim (opsional)</span><input type="text" data-f="payload" value="${esc(s.payload || '')}" maxlength="160" placeholder="Nama tabel / isi data"></label>
            ${issue ? `<p class="field-warn"><i class="fas fa-triangle-exclamation"></i> ${esc(issue)}, langkah ini dilewati saat diputar.</p>` : ''}
        </li>`;
    }).join('');
}

function renderModulesEditor() {
    $('#columnsRange').value = state.settings.columns;
    $('#columnsValue').textContent = state.settings.columns;
    $('#modulesEditor').innerHTML = state.modules.map((m, i) => `<li class="mod-edit" data-i="${i}">
        <div class="mod-edit__head">
            <label class="check"><input type="checkbox" data-mf="visible" ${m.hidden ? '' : 'checked'}><span>Tampil</span></label>
            <div class="step-edit__tools">
                <button type="button" class="icon-btn" data-mod-act="up" ${i === 0 ? 'disabled' : ''} aria-label="Geser ${esc(m.name)} ke kiri/atas"><i class="fas fa-arrow-up"></i></button>
                <button type="button" class="icon-btn" data-mod-act="down" ${i === state.modules.length - 1 ? 'disabled' : ''} aria-label="Geser ${esc(m.name)} ke kanan/bawah"><i class="fas fa-arrow-down"></i></button>
                ${m.builtin ? '' : `<button type="button" class="icon-btn icon-btn--danger" data-mod-act="del" aria-label="Hapus modul ${esc(m.name)}"><i class="fas fa-trash"></i></button>`}
            </div>
        </div>
        <label class="field"><span>Nama ruangan</span><input type="text" data-mf="name" value="${esc(m.name)}" maxlength="40"></label>
        <label class="field"><span>Keterangan</span><input type="text" data-mf="sub" value="${esc(m.sub || '')}" maxlength="40"></label>
        <div class="step-edit__route">
            <label><span>Isi ruangan</span><select data-mf="kind">${simpleOptions(KIND_OPTIONS, m.kind)}</select></label>
            <label><span>Petugas</span><select data-mf="staff">${simpleOptions(STAFF_OPTIONS, m.staff)}</select></label>
        </div>
    </li>`).join('');
}

function renderDisplaySettings() {
    ['labels', 'follow', 'routes', 'shadows', 'loop'].forEach((k) => {
        const el = $('#set-' + k);
        if (el) el.checked = !!state.settings[k];
    });
    $('#speedSelect').value = String(state.settings.speed);
}

function renderEditor() {
    renderStepsEditor();
    renderModulesEditor();
    renderDisplaySettings();
}

function afterStructureChange({ rebuildOffice = false } = {}) {
    stopAndReset();
    if (rebuildOffice) buildOffice();
    buildRoutes();
    renderScenarioSelect();
    renderNarration();
    syncPlayButton();
    saveState();
}

function bindEditor() {
    // Tabs
    document.querySelectorAll('[role="tab"]').forEach((tab) => {
        tab.addEventListener('click', () => selectTab(tab));
        tab.addEventListener('keydown', (e) => {
            if (e.key !== 'ArrowRight' && e.key !== 'ArrowLeft') return;
            const tabs = [...document.querySelectorAll('[role="tab"]')];
            const n = tabs[(tabs.indexOf(tab) + (e.key === 'ArrowRight' ? 1 : -1) + tabs.length) % tabs.length];
            selectTab(n);
            n.focus();
        });
    });

    // Skenario
    $('#scName').addEventListener('input', (e) => { scenario().name = e.target.value || 'Tanpa nama'; renderScenarioSelect(); saveState(); });
    $('#scDesc').addEventListener('input', (e) => { scenario().desc = e.target.value; $('#scenarioDesc').textContent = e.target.value; saveState(); });

    $('#btnNewScenario').addEventListener('click', () => {
        const sc = { id: uid('sc'), name: 'Skenario baru', desc: '', steps: [] };
        state.scenarios.push(sc);
        state.active = sc.id;
        afterStructureChange();
        renderEditor();
        $('#scName').select();
        toast('Skenario baru dibuat. Tambahkan langkah pertamanya.', 'ok');
    });
    $('#btnDupScenario').addEventListener('click', () => {
        const sc = clone(scenario());
        sc.id = uid('sc');
        sc.name = sc.name + ' (salinan)';
        state.scenarios.push(sc);
        state.active = sc.id;
        afterStructureChange();
        renderEditor();
        toast('Skenario diduplikat. Silakan ubah salinannya tanpa mengganggu aslinya.', 'ok');
    });
    $('#btnDeleteScenario').addEventListener('click', () => {
        if (state.scenarios.length <= 1) return;
        if (!confirm(`Hapus skenario "${scenario().name}"? Tindakan ini tidak bisa dibatalkan kecuali lewat Reset ke bawaan.`)) return;
        state.scenarios = state.scenarios.filter((s) => s.id !== state.active);
        state.active = state.scenarios[0].id;
        afterStructureChange();
        renderEditor();
        toast('Skenario dihapus.', 'ok');
    });

    // Langkah
    $('#btnAddStep').addEventListener('click', () => addStep());
    const se = $('#stepsEditor');
    se.addEventListener('input', (e) => {
        const li = e.target.closest('.step-edit');
        const f = e.target.dataset.f;
        if (!li || !(f === 'label' || f === 'payload')) return;
        scenario().steps[+li.dataset.i][f] = e.target.value;
        renderNarration();
        saveState();
    });
    se.addEventListener('change', (e) => {
        const li = e.target.closest('.step-edit');
        const f = e.target.dataset.f;
        if (!li || !(f === 'type' || f === 'from' || f === 'to')) return;
        const step = scenario().steps[+li.dataset.i];
        step[f] = e.target.value;
        if (f === 'type' && step.type !== 'process' && step.to === step.from) {
            step.to = step.from === 'database' ? (visibleModules().find((m) => m.id !== 'database') || {}).id || step.to : 'database';
        }
        if (step.type === 'process') step.to = step.from;
        afterStructureChange();
        renderStepsEditor();
        const again = se.querySelector(`.step-edit[data-i="${li.dataset.i}"] [data-f="${f}"]`);
        if (again) again.focus();
    });
    se.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-step-act]');
        if (!btn) return;
        const i = +btn.closest('.step-edit').dataset.i;
        const steps = scenario().steps;
        const act = btn.dataset.stepAct;
        if (act === 'up' && i > 0) [steps[i - 1], steps[i]] = [steps[i], steps[i - 1]];
        if (act === 'down' && i < steps.length - 1) [steps[i + 1], steps[i]] = [steps[i], steps[i + 1]];
        if (act === 'dup') steps.splice(i + 1, 0, clone(steps[i]));
        if (act === 'del') steps.splice(i, 1);
        afterStructureChange();
        renderStepsEditor();
    });

    // Modul
    const me = $('#modulesEditor');
    me.addEventListener('change', (e) => {
        const li = e.target.closest('.mod-edit');
        const f = e.target.dataset.mf;
        if (!li || !f) return;
        const m = state.modules[+li.dataset.i];
        if (f === 'visible') {
            if (!e.target.checked && visibleModules().length <= 1) {
                e.target.checked = true;
                toast('Minimal satu ruangan harus tampil.', 'warn');
                return;
            }
            m.hidden = !e.target.checked;
        } else {
            m[f] = f === 'name' ? (e.target.value.trim() || 'Tanpa nama') : e.target.value;
        }
        afterStructureChange({ rebuildOffice: true });
        renderStepsEditor();
    });
    me.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-mod-act]');
        if (!btn) return;
        const i = +btn.closest('.mod-edit').dataset.i;
        const mods = state.modules;
        const act = btn.dataset.modAct;
        if (act === 'up' && i > 0) [mods[i - 1], mods[i]] = [mods[i], mods[i - 1]];
        if (act === 'down' && i < mods.length - 1) [mods[i + 1], mods[i]] = [mods[i], mods[i + 1]];
        if (act === 'del') {
            const m = mods[i];
            const used = state.scenarios.reduce((n, sc) => n + sc.steps.filter((s) => s.from === m.id || s.to === m.id).length, 0);
            const msg = used
                ? `Modul "${m.name}" dipakai di ${used} langkah. Hapus modul beserta langkah-langkah tersebut?`
                : `Hapus modul "${m.name}"?`;
            if (!confirm(msg)) return;
            state.scenarios.forEach((sc) => { sc.steps = sc.steps.filter((s) => s.from !== m.id && s.to !== m.id); });
            mods.splice(i, 1);
            toast('Modul dihapus.', 'ok');
        }
        afterStructureChange({ rebuildOffice: true });
        renderEditor();
    });

    $('#addModuleForm').addEventListener('submit', (e) => {
        e.preventDefault();
        const f = e.target;
        const name = f.elements.name.value.trim();
        if (!name) {
            f.elements.name.setAttribute('aria-invalid', 'true');
            f.elements.name.focus();
            toast('Nama ruangan wajib diisi.', 'warn');
            return;
        }
        f.elements.name.removeAttribute('aria-invalid');
        state.modules.push({ id: uid('mod'), name, sub: f.elements.sub.value.trim(), kind: f.elements.kind.value, staff: f.elements.staff.value, builtin: false });
        f.reset();
        afterStructureChange({ rebuildOffice: true });
        renderEditor();
        toast(`Ruangan "${name}" ditambahkan di posisi terakhir.`, 'ok');
    });
    $('#addModuleForm [name="kind"]').innerHTML = simpleOptions(KIND_OPTIONS, 'desk');
    $('#addModuleForm [name="staff"]').innerHTML = simpleOptions(STAFF_OPTIONS, 'admin');

    $('#columnsRange').addEventListener('input', (e) => {
        state.settings.columns = +e.target.value;
        $('#columnsValue').textContent = e.target.value;
    });
    $('#columnsRange').addEventListener('change', () => {
        afterStructureChange({ rebuildOffice: true });
        resetCamera();
    });

    // Tampilan
    ['labels', 'follow', 'routes', 'shadows', 'loop'].forEach((k) => {
        const el = $('#set-' + k);
        if (!el) return;
        el.addEventListener('change', () => {
            state.settings[k] = el.checked;
            applySettings();
            saveState();
        });
    });

    // Impor / ekspor
    $('#btnExport').addEventListener('click', exportJson);
    $('#importFile').addEventListener('change', importJson);
    $('#btnReset').addEventListener('click', () => {
        if (!confirm('Kembalikan semua modul dan skenario ke bawaan? Perubahan Anda akan hilang. Ekspor JSON dulu jika ingin menyimpannya.')) return;
        state = defaultState();
        saveState();
        afterStructureChange({ rebuildOffice: true });
        applySettings();
        renderEditor();
        resetCamera();
        toast('Semua pengaturan dikembalikan ke bawaan.', 'ok');
    });
}

function addStep(fromId) {
    const sc = scenario();
    const last = sc.steps[sc.steps.length - 1];
    const from = fromId || (last ? (isProcess(last) ? last.from : last.to) : visibleModules()[0].id);
    const to = from === 'database' ? (visibleModules().find((m) => m.id !== 'database') || { id: from }).id : (moduleById('database') ? 'database' : from);
    sc.steps.push({ from, to, type: 'request', label: 'Langkah baru', payload: '' });
    afterStructureChange();
    renderStepsEditor();
    const lastLi = $('#stepsEditor').lastElementChild;
    if (lastLi) {
        lastLi.scrollIntoView({ block: 'nearest' });
        const inp = lastLi.querySelector('[data-f="label"]');
        if (inp) inp.select();
    }
}

function selectTab(tab) {
    document.querySelectorAll('[role="tab"]').forEach((t) => {
        const on = t === tab;
        t.setAttribute('aria-selected', String(on));
        t.tabIndex = on ? 0 : -1;
        $('#' + t.getAttribute('aria-controls')).hidden = !on;
    });
}

function applySettings() {
    labelRenderer.domElement.style.display = state.settings.labels ? '' : 'none';
    renderer.shadowMap.enabled = state.settings.shadows;
    scene.traverse((o) => { if (o.material) o.material.needsUpdate = true; });
    buildRoutes();
    $('#btnLoop').setAttribute('aria-pressed', String(!!state.settings.loop));
    $('#btnFollow').setAttribute('aria-pressed', String(!!state.settings.follow));
    $('#speedSelect').value = String(state.settings.speed);
}

function exportJson() {
    const data = { app: 'sawamawa-alur3d', version: 1, exportedAt: new Date().toISOString(), modules: state.modules, scenarios: state.scenarios };
    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
    const a = document.createElement('a');
    const d = new Date();
    a.href = URL.createObjectURL(blob);
    a.download = `alur-sistem-3d-${d.getFullYear()}${String(d.getMonth() + 1).padStart(2, '0')}${String(d.getDate()).padStart(2, '0')}.json`;
    document.body.appendChild(a);
    a.click();
    a.remove();
    setTimeout(() => URL.revokeObjectURL(a.href), 1000);
    toast('File JSON diunduh. Bagikan ke tim atau impor di komputer lain.', 'ok');
}

function importJson(e) {
    const file = e.target.files[0];
    e.target.value = '';
    if (!file) return;
    const reader = new FileReader();
    reader.onload = () => {
        try {
            const data = JSON.parse(reader.result);
            if (!validState(data)) throw new Error('struktur');
            if (!confirm(`Impor ${data.modules.length} modul dan ${data.scenarios.length} skenario? Data saat ini akan diganti.`)) return;
            state.modules = data.modules;
            state.scenarios = data.scenarios;
            state.active = data.scenarios[0].id;
            afterStructureChange({ rebuildOffice: true });
            renderEditor();
            resetCamera();
            toast('Impor berhasil.', 'ok');
        } catch (err) {
            toast('File tidak bisa diimpor: bukan JSON hasil ekspor Peta Alur 3D atau isinya rusak.', 'error');
        }
    };
    reader.onerror = () => toast('Gagal membaca file.', 'error');
    reader.readAsText(file);
}

/* ------------------------------------------------------------------ */
/* UI umum                                                             */
/* ------------------------------------------------------------------ */
let toastTimer = null;
function toast(msg, kind = 'info') {
    const el = $('#toast');
    el.textContent = msg;
    el.dataset.kind = kind;
    el.hidden = false;
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => { el.hidden = true; }, 3800);
}

function setDrawer(open) {
    const d = $('#editorDrawer');
    d.hidden = !open;
    $('#btnEditor').setAttribute('aria-expanded', String(open));
    document.body.classList.toggle('drawer-open', open);
    if (open) {
        renderEditor();
        $('#editorDrawer [role="tab"][aria-selected="true"]').focus();
    } else {
        $('#btnEditor').focus();
    }
    requestAnimationFrame(resize);
}

function bindUi() {
    $('#btnPlay').addEventListener('click', togglePlay);
    $('#btnPrev').addEventListener('click', () => stepBy(-1));
    $('#btnNext').addEventListener('click', () => stepBy(1));
    $('#btnRestart').addEventListener('click', restart);
    $('#btnResetCam').addEventListener('click', resetCamera);
    $('#btnLoop').addEventListener('click', () => { state.settings.loop = !state.settings.loop; applySettings(); renderDisplaySettings(); saveState(); });
    $('#btnFollow').addEventListener('click', () => { state.settings.follow = !state.settings.follow; applySettings(); renderDisplaySettings(); saveState(); });
    $('#speedSelect').addEventListener('change', (e) => {
        const old = state.settings.speed;
        state.settings.speed = parseFloat(e.target.value) || 1;
        if (player.active) {
            player.t = player.t * (old / state.settings.speed);
            player.dur = stepDuration(player.active.step);
        }
        saveState();
    });
    $('#scenarioSelect').addEventListener('change', (e) => {
        state.active = e.target.value;
        afterStructureChange();
        if (!$('#editorDrawer').hidden) renderStepsEditor();
    });
    $('#btnEditor').addEventListener('click', () => setDrawer($('#editorDrawer').hidden));
    $('#btnCloseEditor').addEventListener('click', () => setDrawer(false));
    $('#btnHelp').addEventListener('click', () => $('#helpDialog').showModal());

    // Fitur ciutkan / tampilkan panel keterangan alur
    const toggleNarrationCollapse = (collapse) => {
        const p = $('#narration');
        const expandBtn = $('#btnExpandNarration');
        const shouldCollapse = collapse !== undefined ? collapse : !p.classList.contains('is-collapsed');
        p.classList.toggle('is-collapsed', shouldCollapse);
        if (expandBtn) expandBtn.hidden = !shouldCollapse;
        try {
            localStorage.setItem('sawamawa_alur3d_narration_collapsed', shouldCollapse ? '1' : '0');
        } catch(e){}
    };

    if (localStorage.getItem('sawamawa_alur3d_narration_collapsed') === '1') {
        toggleNarrationCollapse(true);
    }

    const btnCol = $('#btnCollapseNarration');
    if (btnCol) btnCol.addEventListener('click', () => toggleNarrationCollapse(true));

    const btnExp = $('#btnExpandNarration');
    if (btnExp) btnExp.addEventListener('click', () => toggleNarrationCollapse(false));

    $('#btnNarration').addEventListener('click', () => {
        const p = $('#narration');
        const open = p.classList.toggle('is-open');
        $('#btnNarration').setAttribute('aria-expanded', String(open));
        if (open) toggleNarrationCollapse(false);
    });

    document.addEventListener('click', (e) => {
        const jump = e.target.closest('[data-jump]');
        if (jump && !jump.disabled) {
            if (jump.hasAttribute('data-close-dialog')) jump.closest('dialog').close();
            jumpTo(+jump.dataset.jump);
            return;
        }
        const act = e.target.closest('[data-action]');
        if (!act) return;
        if (act.dataset.action === 'open-editor') setDrawer(true);
        if (act.dataset.action === 'restart') restart();
        if (act.dataset.action === 'close-dialog') act.closest('dialog').close();
        if (act.dataset.action === 'add-step-from') {
            const id = act.closest('dialog').dataset.moduleId;
            act.closest('dialog').close();
            setDrawer(true);
            selectTab($('#tab-steps'));
            addStep(id);
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.target.closest('input, textarea, select, [contenteditable]') || e.ctrlKey || e.metaKey || e.altKey) return;
        if (document.querySelector('dialog[open]')) return;
        if (e.key === ' ' && !e.target.closest('button')) { e.preventDefault(); togglePlay(); }
        else if (e.key === 'ArrowRight') { e.preventDefault(); stepBy(1); }
        else if (e.key === 'ArrowLeft') { e.preventDefault(); stepBy(-1); }
        else if (e.key === 'r' || e.key === 'R') restart();
        else if (e.key === 'f' || e.key === 'F') $('#btnFollow').click();
        else if (e.key === 'e' || e.key === 'E') setDrawer($('#editorDrawer').hidden);
        else if (e.key === 'k' || e.key === 'K') toggleNarrationCollapse();
        else if (e.key === 'c' || e.key === 'C') resetCamera();
        else if (e.key === '?') $('#helpDialog').showModal();
        else if (e.key === 'Escape' && !$('#editorDrawer').hidden) setDrawer(false);
    });
}

/* ------------------------------------------------------------------ */
/* Start                                                               */
/* ------------------------------------------------------------------ */
function webglAvailable() {
    try {
        const c = document.createElement('canvas');
        return !!(window.WebGL2RenderingContext && c.getContext('webgl2')) || !!c.getContext('webgl');
    } catch (e) {
        return false;
    }
}

function boot() {
    window.__alur3dReady = true;
    if (!webglAvailable()) {
        window.__alur3dFail('Browser atau kartu grafis Anda tidak mendukung WebGL, sehingga tampilan 3D tidak bisa dijalankan. Coba Chrome / Edge versi terbaru dan aktifkan akselerasi hardware.');
        return;
    }
    try {
        initScene();
        buildOffice();
        buildRoutes();
        resetCamera();
        bindUi();
        bindEditor();
        applySettings();
        renderScenarioSelect();
        renderNarration();
        syncPlayButton();
        const clock = new THREE.Clock();
        renderer.setAnimationLoop(() => {
            const dt = Math.min(clock.getDelta(), 0.1);
            update(dt, clock.elapsedTime);
            controls.update();
            renderer.render(scene, camera);
            labelRenderer.render(scene, camera);
        });
        $('#loader').hidden = true;
    } catch (err) {
        console.error(err);
        window.__alur3dFail('Terjadi kesalahan saat menyiapkan tampilan 3D: ' + err.message);
    }
}

boot();

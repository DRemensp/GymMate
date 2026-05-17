<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymMate</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { width: 100%; height: 100%; overflow: hidden; background: #080808; }

        #glow {
            position: fixed; inset: 0; z-index: 0; pointer-events: none;
        }
        #canvas-wrap {
            position: fixed; inset: 0; z-index: 1; pointer-events: none;
            cursor: grab;
        }
        #canvas-wrap.dragging { cursor: grabbing; }
        #screen-overlay {
            position: fixed; z-index: 2; overflow: hidden; pointer-events: all;
            transition: opacity 0.15s ease;
        }
        #screen-overlay iframe {
            width: calc(100% / 0.8);
            height: calc(100% / 0.8);
            transform: scale(0.8);
            transform-origin: top left;
            border: none;
            display: block;
            background: #1c1c1e;
        }
        #brand {
            position: fixed; bottom: 22px; left: 50%; transform: translateX(-50%);
            z-index: 3; pointer-events: none;
            color: rgba(255,255,255,0.2);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            font-size: 10px; letter-spacing: 4px; text-transform: uppercase;
        }
        #reset-btn {
            position: fixed; bottom: 22px; right: 32px; z-index: 5;
            display: flex; align-items: center; gap: 6px;
            padding: 8px 16px; border-radius: 999px; border: none; cursor: pointer;
            background: rgba(255,255,255,0.08); backdrop-filter: blur(8px);
            color: rgba(255,255,255,0.6);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            font-size: 12px; letter-spacing: 0.5px;
            opacity: 0; pointer-events: none;
            transition: opacity 0.3s ease, background 0.2s ease;
        }
        #reset-btn.visible { opacity: 1; pointer-events: all; }
        #reset-btn:hover { background: rgba(255,255,255,0.15); color: #fff; }

        /* ── Info panel ──────────────────────────────────────────── */
        #info-panel {
            position: fixed;
            left: clamp(20px, 4vw, 60px);
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            width: clamp(220px, 18vw, 272px);
            background: rgba(12,12,12,0.80);
            backdrop-filter: blur(24px) saturate(1.3);
            -webkit-backdrop-filter: blur(24px) saturate(1.3);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 20px;
            overflow: hidden;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            pointer-events: none;
            animation: panelIn 0.9s cubic-bezier(0.16,1,0.3,1) 0.5s both;
        }
        #info-panel::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; height: 1px;
            background: linear-gradient(90deg, transparent, var(--accent-hex,#f97316) 50%, transparent);
            opacity: 0.55;
        }
        @keyframes panelIn {
            from { opacity: 0; transform: translateY(-50%) translateX(-22px); }
            to   { opacity: 1; transform: translateY(-50%) translateX(0); }
        }
        .panel-section { padding: 20px; }

        /* ── Drag hint overlay ───────────────────────────────────── */
        #drag-hint {
            position: fixed; inset: 0; z-index: 50;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            background: rgba(0,0,0,0.50);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            pointer-events: all;
            transition: opacity 0.5s ease;
        }
        #drag-hint.hidden { opacity: 0; pointer-events: none; }
        .hint-message {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            font-size: 21px; font-weight: 500;
            color: rgba(255,255,255,0.93); letter-spacing: -0.3px;
            margin-bottom: 44px; text-align: center;
            animation: hintMsgIn 0.7s cubic-bezier(0.16,1,0.3,1) 0.3s both;
        }
        @keyframes hintMsgIn {
            from { opacity: 0; transform: translateY(-10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .hint-arrow {
            position: absolute;
            top: calc(50% + 30px);
            animation: arrowTravel 2s cubic-bezier(0.4,0,0.25,1) 0.8s both;
        }
        @keyframes arrowTravel {
            from  { left: -140px; opacity: 0; }
            8%    { opacity: 1; }
            88%   { opacity: 1; }
            to    { left: calc(100% + 20px); opacity: 0; }
        }
        .panel-badge {
            display: inline-flex; align-items: center; gap: 5px;
            background: rgb(var(--accent-rgb,249 115 22) / 0.12);
            color: var(--accent-hex,#f97316);
            border: 1px solid rgb(var(--accent-rgb,249 115 22) / 0.22);
            border-radius: 999px; font-size: 9px; font-weight: 600;
            letter-spacing: 0.9px; text-transform: uppercase;
            padding: 3px 9px; margin-bottom: 10px;
        }
        .panel-title {
            font-size: 15px; font-weight: 600;
            color: rgba(255,255,255,0.88); margin-bottom: 8px; letter-spacing: -0.2px;
        }
        .panel-desc {
            font-size: 12px; color: rgba(255,255,255,0.40);
            line-height: 1.7; letter-spacing: 0.1px;
        }
        .panel-hl { color: rgba(255,255,255,0.68); font-weight: 500; }
    </style>

    {{-- Instant mobile redirect before any assets load --}}
    <script>
        if (window.innerWidth < 1024 || 'ontouchstart' in window) {
            window.location.replace('{{ auth()->check() ? route("dashboard") : route("login") }}');
        }
    </script>

    {{-- Apply accent glow + CSS vars from localStorage --}}
    <script>
        (function () {
            var map = {
                orange: { rgb: '249 115 22', hex: '#f97316' },
                blue:   { rgb: '59 130 246',  hex: '#3b82f6' },
                violet: { rgb: '139 92 246',  hex: '#8b5cf6' },
                green:  { rgb: '34 197 94',   hex: '#22c55e' },
                red:    { rgb: '239 68 68',   hex: '#ef4444' },
                pink:   { rgb: '236 72 153',  hex: '#ec4899' },
            };
            var n = localStorage.getItem('gymmate-accent') || 'orange';
            var a = map[n] || map.orange;
            document.documentElement.style.setProperty('--accent-rgb', a.rgb);
            document.documentElement.style.setProperty('--accent-hex', a.hex);
            document.addEventListener('DOMContentLoaded', function () {
                document.getElementById('glow').style.background =
                    'radial-gradient(ellipse 55% 75% at 50% 48%, rgb(' + a.rgb + ' / 0.20) 0%, rgb(' + a.rgb + ' / 0.06) 45%, transparent 70%)';
            });
        })();
    </script>

    <script type="importmap">
    {
        "imports": {
            "three": "https://cdn.jsdelivr.net/npm/three@0.160.0/build/three.module.js",
            "three/addons/": "https://cdn.jsdelivr.net/npm/three@0.160.0/examples/jsm/"
        }
    }
    </script>
</head>
<body>
    <div id="glow"></div>
    <div id="canvas-wrap"></div>
    <div id="screen-overlay">
        <iframe id="app-frame" src="{{ $iframeSrc }}" title="GymMate App"></iframe>
    </div>
    <div id="brand">GymMate</div>
    <button id="reset-btn" onclick="resetRotation()">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/>
        </svg>
        Zurücksetzen
    </button>

    <div id="drag-hint">
        <p class="hint-message">Du kannst das Handy drehen</p>
        <svg class="hint-arrow" width="130" height="44" viewBox="0 0 130 44" fill="none" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="arrowGrad" x1="0" y1="0" x2="1" y2="0">
                    <stop offset="0%" stop-color="var(--accent-hex,#f97316)" stop-opacity="0.5"/>
                    <stop offset="100%" stop-color="white" stop-opacity="0.95"/>
                </linearGradient>
            </defs>
            <line x1="4" y1="22" x2="90" y2="22" stroke="url(#arrowGrad)" stroke-width="3" stroke-linecap="round"/>
            <polyline points="74,10 118,22 74,34" stroke="white" stroke-width="3" fill="none" stroke-linecap="round" stroke-linejoin="round" opacity="0.92"/>
        </svg>
    </div>

    <div id="info-panel">
        <div class="panel-section">
            <div class="panel-badge">
                <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="3" width="20" height="14" rx="2"/>
                    <path d="M8 21h8M12 17v4"/>
                </svg>
                Desktop
            </div>
            <h3 class="panel-title">Desktop Umgebung</h3>
            <p class="panel-desc">Du befindest dich in einer <span class="panel-hl">Desktop-Umgebung</span>. GymMate ist für mobile Geräte optimiert und läuft daher auf einem simulierten, detailgetreuen <span class="panel-hl">Samsung Galaxy S25 Ultra</span>.</p>
        </div>
    </div>

    <script type="module">
        import * as THREE from 'three';
        import { RoomEnvironment } from 'three/addons/environments/RoomEnvironment.js';

        /* ── Scene & Camera ─────────────────────────────────────── */
        const container = document.getElementById('canvas-wrap');
        const scene = new THREE.Scene();

        const camera = new THREE.PerspectiveCamera(40, window.innerWidth / window.innerHeight, 0.1, 100);
        camera.position.set(0, 0, 28);

        const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        renderer.setSize(window.innerWidth, window.innerHeight);
        renderer.toneMapping = THREE.ACESFilmicToneMapping;
        renderer.toneMappingExposure = 1.2;
        container.appendChild(renderer.domElement);

        const pmremGenerator = new THREE.PMREMGenerator(renderer);
        scene.environment = pmremGenerator.fromScene(new RoomEnvironment(), 0.04).texture;

        /* ── Lighting ───────────────────────────────────────────── */
        const topLight = new THREE.DirectionalLight(0xffffff, 2.0);
        topLight.position.set(0, 20, 5);
        scene.add(topLight);

        const fillLight = new THREE.DirectionalLight(0xffffff, 0.8);
        fillLight.position.set(0, -20, 5);
        scene.add(fillLight);

        const sideLight = new THREE.PointLight(0xffffff, 1.5);
        sideLight.position.set(10, 0, 10);
        scene.add(sideLight);

        /* ── Materials ──────────────────────────────────────────── */
        const matTitanium = new THREE.MeshPhysicalMaterial({
            color: 0xcbc9c7, metalness: 0.95, roughness: 0.2, clearcoat: 0.1
        });
        const matButton = new THREE.MeshPhysicalMaterial({
            color: 0xaaaaaa, metalness: 1.0, roughness: 0.1, clearcoat: 0.3
        });
        const matBackGlass = new THREE.MeshPhysicalMaterial({
            color: 0xe5e3e1, metalness: 0.1, roughness: 0.6, clearcoat: 0.05,
            polygonOffset: true, polygonOffsetFactor: -1, polygonOffsetUnits: -1
        });
        const matScreen = new THREE.MeshStandardMaterial({
            color: 0x080808, roughness: 0.05, metalness: 0.2,
            polygonOffset: true, polygonOffsetFactor: -1, polygonOffsetUnits: -1
        });
        const matLensGlass = new THREE.MeshPhysicalMaterial({
            color: 0xffffff, metalness: 0.1, roughness: 0.05,
            transmission: 0.95, thickness: 0.1, transparent: true, opacity: 0.6
        });
        const matCameraFrame = new THREE.MeshPhysicalMaterial({
            color: 0x111111, metalness: 0.3, roughness: 0.8
        });
        const matGroove = new THREE.MeshStandardMaterial({
            color: 0x050505, roughness: 1.0,
            polygonOffset: true, polygonOffsetFactor: -0.5, polygonOffsetUnits: -0.5
        });
        const matSlit = new THREE.MeshStandardMaterial({ color: 0xdddddd, roughness: 0.7, metalness: 0.1 });
        const matDark = new THREE.MeshStandardMaterial({ color: 0x111111, roughness: 0.9 });

        /* ── Phone dimensions ───────────────────────────────────── */
        const PW = 7.76, PH = 16.28, PD = 0.82, PCR = 0.35;
        const Z_FRONT = PD / 2, Z_BACK = -PD / 2;
        const SCREEN_INSET = 0.12;
        const SCREEN_W = PW - SCREEN_INSET * 2;   // 7.52
        const SCREEN_H = PH - SCREEN_INSET * 2;   // 16.04
        const SCREEN_CORNER = PCR - SCREEN_INSET;  // 0.23
        const SCREEN_Z = Z_FRONT + 0.02;           // 0.43

        /* ── Shape helpers ──────────────────────────────────────── */
        function createPhoneShape(w, h, r) {
            const shape = new THREE.Shape();
            const sX = -w / 2, sY = -h / 2;
            shape.moveTo(sX + r, sY);
            shape.lineTo(sX + w - r, sY);
            shape.quadraticCurveTo(sX + w, sY, sX + w, sY + r);
            shape.lineTo(sX + w, sY + h - r);
            shape.quadraticCurveTo(sX + w, sY + h, sX + w - r, sY + h);
            shape.lineTo(sX + r, sY + h);
            shape.quadraticCurveTo(sX, sY + h, sX, sY + h - r);
            shape.lineTo(sX, sY + r);
            shape.quadraticCurveTo(sX, sY, sX + r, sY);
            return shape;
        }

        /* ── Build phone group ──────────────────────────────────── */
        const phone = new THREE.Group();

        // Chassis
        const baseShape = createPhoneShape(PW, PH, PCR);
        const chassisGeo = new THREE.ExtrudeGeometry(baseShape, {
            depth: PD, bevelEnabled: true,
            bevelThickness: 0.015, bevelSize: 0.015, bevelSegments: 2, curveSegments: 32
        });
        chassisGeo.translate(0, 0, Z_BACK);
        phone.add(new THREE.Mesh(chassisGeo, matTitanium));

        // Screen (dark, iframe will overlay this)
        const sH = PH - SCREEN_INSET * 2;
        const screenMesh = new THREE.Mesh(
            new THREE.ShapeGeometry(createPhoneShape(SCREEN_W, sH, PCR - SCREEN_INSET)),
            matScreen
        );
        screenMesh.position.z = SCREEN_Z;
        phone.add(screenMesh);

        // Punch-hole front camera
        const punchHole = new THREE.Mesh(new THREE.CircleGeometry(0.20, 32), matLensGlass);
        punchHole.position.set(0, sH / 2 - 0.35, SCREEN_Z + 0.015);
        phone.add(punchHole);

        // Back groove
        const grooveMesh = new THREE.Mesh(
            new THREE.ShapeGeometry(createPhoneShape(PW - 0.04, PH - 0.04, PCR - 0.02)),
            matGroove
        );
        grooveMesh.rotation.y = Math.PI;
        grooveMesh.position.z = Z_BACK - 0.005;
        phone.add(grooveMesh);

        // Back glass
        const backGlassMesh = new THREE.Mesh(
            new THREE.ShapeGeometry(createPhoneShape(PW - 0.12, PH - 0.12, PCR - 0.06)),
            matBackGlass
        );
        backGlassMesh.rotation.y = Math.PI;
        backGlassMesh.position.z = Z_BACK - 0.016;
        phone.add(backGlassMesh);

        // Samsung logo on back
        const logoCanvas = document.createElement('canvas');
        logoCanvas.width = 1024; logoCanvas.height = 512;
        const lCtx = logoCanvas.getContext('2d');
        const logoColor = 'rgba(60,60,60,0.7)';
        lCtx.fillStyle = logoColor;
        lCtx.textAlign = 'center';
        lCtx.font = 'bold 100px "Segoe UI", Roboto, sans-serif';
        lCtx.letterSpacing = '12px';
        lCtx.fillText('SAMSUNG', 512, 180);
        (function drawCE(ctx, x, y, size) {
            ctx.strokeStyle = logoColor; ctx.lineWidth = size * 0.15; ctx.lineCap = 'butt';
            ctx.beginPath(); ctx.arc(x - size * 0.55, y, size * 0.5, 0.2 * Math.PI, 1.8 * Math.PI, true); ctx.stroke();
            ctx.beginPath(); ctx.arc(x + size * 0.55, y, size * 0.5, 0.2 * Math.PI, 1.8 * Math.PI, true); ctx.stroke();
            ctx.beginPath(); ctx.moveTo(x + size * 0.55, y); ctx.lineTo(x + size * 1.0, y); ctx.stroke();
        })(lCtx, 512, 340, 60);
        const logoTex = new THREE.CanvasTexture(logoCanvas);
        logoTex.colorSpace = THREE.SRGBColorSpace;
        const logoMesh = new THREE.Mesh(
            new THREE.PlaneGeometry(4, 2),
            new THREE.MeshBasicMaterial({ map: logoTex, transparent: true, opacity: 0.5,
                polygonOffset: true, polygonOffsetFactor: -2, polygonOffsetUnits: -2 })
        );
        logoMesh.position.set(0, -5.5, Z_BACK - 0.017);
        logoMesh.rotation.y = Math.PI;
        phone.add(logoMesh);

        // Camera lenses
        function createLens(x, y, radius, h, type = 'large') {
            const group = new THREE.Group();
            group.position.set(x, y, Z_BACK - 0.016);

            if (type === 'flash') {
                const foGeo = new THREE.CylinderGeometry(radius, radius, 0.02, 32);
                foGeo.rotateX(Math.PI / 2);
                const flashOuter = new THREE.Mesh(foGeo, matTitanium);
                flashOuter.position.z = -0.01;
                const fGeo = new THREE.CylinderGeometry(radius * 0.7, radius * 0.7, 0.03, 32);
                fGeo.rotateX(Math.PI / 2);
                const flash = new THREE.Mesh(fGeo, new THREE.MeshBasicMaterial({ color: 0xfff4d6 }));
                flash.position.z = -0.015;
                group.add(flashOuter); group.add(flash);
                return group;
            }

            const ringH = h - 0.02;
            const ringGeo = new THREE.CylinderGeometry(radius, radius, ringH, 64);
            ringGeo.rotateX(Math.PI / 2);
            const ring = new THREE.Mesh(ringGeo, matTitanium);
            ring.position.set(0, 0, -ringH / 2);

            const frameThickness = type === 'large' ? 0.26 : 0.14;
            const frameHeight = 0.06;
            const glassRadius = radius - (frameThickness * 0.85);

            const frameShape = new THREE.Shape();
            frameShape.absarc(0, 0, radius, 0, Math.PI * 2, false);
            const frameHole = new THREE.Path();
            frameHole.absarc(0, 0, glassRadius, 0, Math.PI * 2, true);
            frameShape.holes.push(frameHole);

            const frame = new THREE.Mesh(
                new THREE.ExtrudeGeometry(frameShape, { depth: frameHeight, bevelEnabled: false, curveSegments: 32 }),
                matCameraFrame
            );
            frame.position.z = -ringH - frameHeight;

            const glassHeight = 0.02;
            const glassGeo = new THREE.CylinderGeometry(glassRadius - 0.005, glassRadius - 0.005, glassHeight, 64);
            glassGeo.rotateX(Math.PI / 2);
            const glass = new THREE.Mesh(glassGeo, matLensGlass);
            glass.position.z = -ringH - frameHeight + 0.015 + glassHeight / 2;

            group.add(ring); group.add(frame); group.add(glass);
            return group;
        }

        const colL = 2.58, rowT = 6.84, colR = 1.18, rowM = 5.08, rowB = 3.32;
        phone.add(createLens(colL, rowT, 0.80, 0.22, 'large'));
        phone.add(createLens(colL, rowM, 0.80, 0.22, 'large'));
        phone.add(createLens(colL, rowB, 0.80, 0.22, 'large'));
        phone.add(createLens(colR, rowT, 0.45, 0.12, 'small'));
        phone.add(createLens(colR, (rowT + rowM) / 2, 0.22, 0.05, 'flash'));
        phone.add(createLens(colR, rowM, 0.45, 0.12, 'small'));

        // Buttons
        function createButtonGeometry(h, d, extrude, r) {
            const shape = new THREE.Shape();
            shape.moveTo(-d/2+r, -h/2); shape.lineTo(d/2-r, -h/2);
            shape.quadraticCurveTo(d/2, -h/2, d/2, -h/2+r);
            shape.lineTo(d/2, h/2-r); shape.quadraticCurveTo(d/2, h/2, d/2-r, h/2);
            shape.lineTo(-d/2+r, h/2); shape.quadraticCurveTo(-d/2, h/2, -d/2, h/2-r);
            shape.lineTo(-d/2, -h/2+r); shape.quadraticCurveTo(-d/2, -h/2, -d/2+r, -h/2);
            const geo = new THREE.ExtrudeGeometry(shape, { depth: extrude, bevelEnabled: false, curveSegments: 16 });
            geo.rotateY(Math.PI / 2);
            return geo;
        }

        const btnVol = new THREE.Mesh(createButtonGeometry(2.5, 0.30, 0.12, 0.15), matButton);
        btnVol.position.set(PW / 2, 3.8, 0);
        phone.add(btnVol);
        const btnPwr = new THREE.Mesh(createButtonGeometry(1.3, 0.30, 0.12, 0.15), matButton);
        btnPwr.position.set(PW / 2, 1.0, 0);
        phone.add(btnPwr);

        // Antenna slits
        const slitGeo = new THREE.BoxGeometry(0.15, 0.08, PD);
        [[-PW/2+0.05, rowT, 0], [PW/2-0.05, rowT, 0], [-PW/2+0.05, -rowT, 0], [PW/2-0.05, -rowT, 0]]
            .forEach(p => { const s = new THREE.Mesh(slitGeo, matSlit); s.position.set(...p); phone.add(s); });

        const topSlit = new THREE.Mesh(new THREE.BoxGeometry(0.08, 0.15, PD), matSlit);
        topSlit.position.set(-PW / 5, PH / 2 - 0.05, 0);
        phone.add(topSlit);

        const holeGeo = new THREE.CylinderGeometry(0.04, 0.04, 0.1, 16);
        for (let i = 0; i < 2; i++) {
            const hole = new THREE.Mesh(holeGeo, matDark);
            hole.position.set(PW / 6 + 0.25 + i * 0.25, PH / 2 - 0.02, 0);
            phone.add(hole);
        }

        // USB-C port
        const usbGroup = new THREE.Group();
        const uW = 0.6, uHp = 0.26, uR = uHp / 2, uDepth = 0.1;
        usbGroup.add(new THREE.Mesh(new THREE.BoxGeometry(uW, uHp, uDepth), matDark));
        const uSideGeo = new THREE.CylinderGeometry(uR, uR, uDepth, 16);
        uSideGeo.rotateX(Math.PI / 2);
        const uLeft = new THREE.Mesh(uSideGeo, matDark); uLeft.position.x = -uW / 2; usbGroup.add(uLeft);
        const uRight = new THREE.Mesh(uSideGeo, matDark); uRight.position.x = uW / 2; usbGroup.add(uRight);
        usbGroup.rotation.x = Math.PI / 2;
        usbGroup.position.set(0, -PH / 2 + 0.02, 0);
        phone.add(usbGroup);

        scene.add(phone);

        /* ── Projective helpers for exact matrix3d overlay ─────── */
        function adj3(m) {
            return [m[4]*m[8]-m[5]*m[7], m[2]*m[7]-m[1]*m[8], m[1]*m[5]-m[2]*m[4],
                    m[5]*m[6]-m[3]*m[8], m[0]*m[8]-m[2]*m[6], m[2]*m[3]-m[0]*m[5],
                    m[3]*m[7]-m[4]*m[6], m[1]*m[6]-m[0]*m[7], m[0]*m[4]-m[1]*m[3]];
        }
        function mul3(a,b){const c=new Array(9).fill(0);for(let i=0;i<3;i++)for(let j=0;j<3;j++)for(let k=0;k<3;k++)c[3*i+j]+=a[3*i+k]*b[3*k+j];return c;}
        function mulv3(m,v){return[m[0]*v[0]+m[1]*v[1]+m[2]*v[2],m[3]*v[0]+m[4]*v[1]+m[5]*v[2],m[6]*v[0]+m[7]*v[1]+m[8]*v[2]];}
        function basisToPoints(x1,y1,x2,y2,x3,y3,x4,y4){
            const m=[x1,x2,x3,y1,y2,y3,1,1,1],v=mulv3(adj3(m),[x4,y4,1]);
            return mul3(m,[v[0],0,0,0,v[1],0,0,0,v[2]]);
        }
        function computeH(x1s,y1s,x2s,y2s,x3s,y3s,x4s,y4s,x1d,y1d,x2d,y2d,x3d,y3d,x4d,y4d){
            return mul3(basisToPoints(x1d,y1d,x2d,y2d,x3d,y3d,x4d,y4d),
                        adj3(basisToPoints(x1s,y1s,x2s,y2s,x3s,y3s,x4s,y4s)));
        }

        /* ── Overlay & rotation state ───────────────────────────── */
        const overlay    = document.getElementById('screen-overlay');
        const resetBtn   = document.getElementById('reset-btn');
        const canvasWrap = document.getElementById('canvas-wrap');
        const _pv        = new THREE.Vector3();
        const raycaster  = new THREE.Raycaster();
        const _mouse     = new THREE.Vector2();

        let rotY = 0, rotX = 0, targetRotY = 0, targetRotX = 0;
        let isDragging = false, lastMX = 0, lastMY = 0;
        let downX = 0, downY = 0;
        let screenOn = true;

        function projPt(lx, ly) {
            _pv.set(lx, ly, SCREEN_Z);
            phone.localToWorld(_pv);
            _pv.project(camera);
            return [(_pv.x+1)/2*window.innerWidth, (1-_pv.y)/2*window.innerHeight];
        }

        function updateOverlay() {
            // Unrotated pixel size for overlay's natural dimensions
            const fov  = THREE.MathUtils.degToRad(40);
            const dist = camera.position.z - SCREEN_Z;
            const ppu  = window.innerHeight / (2 * Math.tan(fov/2) * dist);
            const W    = SCREEN_W * ppu;
            const H    = SCREEN_H * ppu;
            const cr   = SCREEN_CORNER * ppu;

            // Project all 4 corners of the phone screen (exact Three.js math)
            const tl = projPt(-SCREEN_W/2,  SCREEN_H/2);
            const tr = projPt( SCREEN_W/2,  SCREEN_H/2);
            const br = projPt( SCREEN_W/2, -SCREEN_H/2);
            const bl = projPt(-SCREEN_W/2, -SCREEN_H/2);

            // Homography: overlay rect → projected screen quad
            const h = computeH(0,0, W,0, W,H, 0,H,
                                tl[0],tl[1], tr[0],tr[1], br[0],br[1], bl[0],bl[1]);
            const n = h[8];
            const m = h.map(v => v/n);

            // Embed 3×3 projective into CSS 4×4 column-major matrix3d
            overlay.style.left   = '0';
            overlay.style.top    = '0';
            overlay.style.width  = W + 'px';
            overlay.style.height = H + 'px';
            overlay.style.borderRadius = cr + 'px';
            overlay.style.transformOrigin = '0 0';
            overlay.style.transform =
                `matrix3d(${m[0]},${m[3]},0,${m[6]},${m[1]},${m[4]},0,${m[7]},0,0,1,0,${m[2]},${m[5]},0,1)`;
        }

        /* ── Drag to rotate ─────────────────────────────────────── */
        document.addEventListener('mousedown', e => {
            isDragging = true;
            lastMX = downX = e.clientX;
            lastMY = downY = e.clientY;
            canvasWrap.classList.add('dragging');
        });

        document.addEventListener('mousemove', e => {
            if (isDragging) {
                targetRotY += (e.clientX - lastMX) * 0.012;
                targetRotX  = Math.max(-Math.PI/2, Math.min(Math.PI/2,
                    targetRotX + (e.clientY - lastMY) * 0.006));
                lastMX = e.clientX;
                lastMY = e.clientY;
            } else {
                _mouse.set((e.clientX / window.innerWidth) * 2 - 1,
                           -(e.clientY / window.innerHeight) * 2 + 1);
                raycaster.setFromCamera(_mouse, camera);
                const overBtn = raycaster.intersectObject(btnPwr, false).length > 0;
                canvasWrap.style.cursor = overBtn ? 'pointer' : 'grab';
            }
        });

        // Power button click (short press, not a drag)
        document.addEventListener('mouseup', e => {
            if (Math.hypot(e.clientX - downX, e.clientY - downY) < 6) {
                _mouse.set((e.clientX / window.innerWidth) * 2 - 1,
                           -(e.clientY / window.innerHeight) * 2 + 1);
                raycaster.setFromCamera(_mouse, camera);
                if (raycaster.intersectObject(btnPwr, false).length > 0) {
                    screenOn = !screenOn;
                }
            }
        });

        function stopDrag() {
            if (!isDragging) return;
            isDragging = false;
            canvasWrap.classList.remove('dragging');
            const isRotated = Math.abs(targetRotY) > 0.05 || Math.abs(targetRotX) > 0.05;
            resetBtn.classList.toggle('visible', isRotated);
        }
        document.addEventListener('mouseup',    stopDrag);
        document.addEventListener('mouseleave', stopDrag);

        /* ── Reset button ───────────────────────────────────────── */
        function resetRotation() {
            targetRotY = 0;
            targetRotX = 0;
            resetBtn.classList.remove('visible');
        }
        window.resetRotation = resetRotation;

        updateOverlay();

        /* ── Drag hint dismiss ──────────────────────────────────── */
        const dragHint = document.getElementById('drag-hint');
        function dismissHint() {
            dragHint.classList.add('hidden');
            dragHint.addEventListener('transitionend', () => dragHint.remove(), { once: true });
        }
        document.addEventListener('mousedown', dismissHint, { once: true });
        setTimeout(dismissHint, 4000);

        window.addEventListener('resize', () => {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
            updateOverlay();
        });

        /* ── Animate ────────────────────────────────────────────── */
        let t = 0;
        function animate() {
            requestAnimationFrame(animate);
            t += 0.007;
            if (!isDragging) phone.position.y = Math.sin(t) * 0.28;

            // Smooth rotation lerp
            rotY += (targetRotY - rotY) * 0.1;
            rotX += (targetRotX - rotX) * 0.1;
            phone.rotation.y = rotY;
            phone.rotation.x = rotX;

            // Hide overlay when back is facing the camera or screen is off
            const frontFacing = Math.cos(rotX) * Math.cos(rotY) > 0;
            const showScreen  = frontFacing && screenOn;
            overlay.style.opacity       = showScreen ? '1' : '0';
            overlay.style.pointerEvents = (showScreen && !isDragging) ? 'all' : 'none';

            updateOverlay();
            renderer.render(scene, camera);
        }
        animate();
    </script>
</body>
</html>

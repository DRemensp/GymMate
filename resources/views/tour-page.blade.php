<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymMate Tour</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { width: 100%; height: 100%; overflow: hidden; background: #080808; }

        #glow { position: fixed; inset: 0; z-index: 0; pointer-events: none; }
        #canvas-wrap { position: fixed; inset: 0; z-index: 1; pointer-events: none; }

        #screen-overlay {
            position: fixed; z-index: 2; overflow: hidden; pointer-events: none;
            transition: opacity 0.15s ease;
        }
        #screen-overlay iframe {
            width: calc(100% / 0.8); height: calc(100% / 0.8);
            transform: scale(0.8); transform-origin: top left;
            border: none; display: block; background: #1c1c1e;
        }

        /* ── Click ring ─────────────────────────────────────────── */
        .click-ring {
            position: absolute; border-radius: 50%;
            border: 3px solid rgba(59,130,246,1);
            box-shadow: 0 0 18px 4px rgba(59,130,246,0.75), inset 0 0 10px rgba(59,130,246,0.25);
            transform: translate(-50%,-50%) scale(0);
            animation: ringExpand 1.0s cubic-bezier(0.2,0,0.35,1) forwards;
            pointer-events: none; z-index: 50;
            width: 62px; height: 62px;
        }
        .click-ring-outer {
            width: 92px; height: 92px;
            border-color: rgba(59,130,246,0.55);
            box-shadow: 0 0 28px 8px rgba(59,130,246,0.45);
            animation-delay: 0.1s;
        }
        @keyframes ringExpand {
            0%   { transform: translate(-50%,-50%) scale(0.1); opacity: 1; }
            40%  { opacity: 1; }
            100% { transform: translate(-50%,-50%) scale(1); opacity: 0; }
        }

        /* ── Swipe indicator ────────────────────────────────────── */
        .swipe-dot {
            position: absolute; top: 48%; left: 7%;
            width: 52px; height: 52px; border-radius: 50%;
            background: rgba(59,130,246,0.28); backdrop-filter: blur(4px);
            border: 2px solid rgba(59,130,246,0.8);
            box-shadow: 0 0 20px 6px rgba(59,130,246,0.55), 0 0 0 10px rgba(59,130,246,0.08);
            animation: swipeDot 2.0s cubic-bezier(0.25,0.8,0.35,1) forwards;
            pointer-events: none; z-index: 50;
        }
        .swipe-tail {
            position: absolute; top: calc(48% + 20px); left: 7%;
            height: 3px; width: 0; border-radius: 2px;
            background: linear-gradient(90deg, rgba(59,130,246,0.9), rgba(59,130,246,0.05));
            box-shadow: 0 0 8px rgba(59,130,246,0.6);
            animation: swipeTail 2.0s cubic-bezier(0.25,0.8,0.35,1) forwards;
            pointer-events: none; z-index: 49;
        }
        @keyframes swipeDot {
            0%   { left: 7%;  opacity: 0; transform: scale(0.6); }
            10%  { opacity: 1; transform: scale(1); }
            78%  { opacity: 1; }
            100% { left: 58%; opacity: 0; transform: scale(0.85); }
        }
        @keyframes swipeTail {
            0%   { left: 7%; width: 0;   opacity: 0; }
            10%  { opacity: 1; }
            78%  { opacity: 0.7; }
            100% { left: 7%; width: 51%; opacity: 0; }
        }

        /* ── Tour controls ──────────────────────────────────────── */
        #tour-controls {
            position: fixed; bottom: 32px; right: 32px;
            z-index: 100;
            display: flex; align-items: center; gap: 10px;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        #scene-dots { display: flex; gap: 5px; align-items: center; margin-right: 4px; }
        .scene-dot {
            width: 5px; height: 5px; border-radius: 50%;
            background: rgba(255,255,255,0.18);
            transition: width 0.3s cubic-bezier(0.34,1.56,0.64,1), background 0.3s ease;
        }
        .scene-dot.active { width: 16px; border-radius: 3px; background: var(--accent-hex, #f97316); }

        .ctrl-btn {
            width: 44px; height: 44px; border-radius: 50%;
            background: rgba(18,18,18,0.85);
            backdrop-filter: blur(16px) saturate(1.2);
            -webkit-backdrop-filter: blur(16px) saturate(1.2);
            border: 1px solid rgba(255,255,255,0.10);
            color: rgba(255,255,255,0.65);
            font-size: 17px; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: background 0.18s, color 0.18s, opacity 0.18s, transform 0.15s;
        }
        .ctrl-btn:hover:not(:disabled) {
            background: rgba(50,50,50,0.9);
            color: rgba(255,255,255,0.95);
            transform: translateY(-1px);
        }
        .ctrl-btn:active:not(:disabled) { transform: translateY(0); }
        .ctrl-btn:disabled { opacity: 0.25; cursor: default; pointer-events: none; }

        #btn-next {
            width: 52px; height: 52px; font-size: 20px;
            background: var(--accent-hex, #f97316);
            border-color: transparent; color: #fff;
        }
        #btn-next:hover:not(:disabled) { opacity: 0.85; }
        #btn-next.loading { animation: btnPulse 1s ease-in-out infinite; }
        @keyframes btnPulse { 0%,100%{opacity:1} 50%{opacity:0.45} }

        /* ── Scene text panel ──────────────────────────────── */
        #scene-text {
            position: fixed; top: 50%; transform: translateY(-50%);
            width: 32%; max-width: 420px;
            z-index: 20; pointer-events: none;
            opacity: 0; transition: opacity 0.45s ease;
        }
        #scene-text.visible  { opacity: 1; }
        #scene-text.side-left  { left: 1%; }
        #scene-text.side-right { right: 1%; }

        .st-inner { display: flex; align-items: center; }

        .st-box {
            flex: 1;
            background: rgba(10,10,14,0.82);
            backdrop-filter: blur(28px) saturate(1.5);
            -webkit-backdrop-filter: blur(28px) saturate(1.5);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 18px; padding: 22px 24px;
        }
        .st-label {
            font-size: 12px; letter-spacing: 0.14em; text-transform: uppercase;
            color: var(--accent-hex, #f97316); font-weight: 700; margin-bottom: 10px;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        .st-body {
            font-size: 17px; line-height: 1.65; color: rgba(255,255,255,0.82);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        .st-body em { color: var(--accent-hex, #f97316); font-style: normal; font-weight: 600; }

        /* ── Scene element highlight ────────────────────────── */
        #scene-highlight {
            position: absolute; pointer-events: none; z-index: 15;
            border-radius: 10px;
            border: 2px solid rgba(59,130,246,0.95);
            box-shadow: 0 0 0 4px rgba(59,130,246,0.15), 0 0 24px 8px rgba(59,130,246,0.55);
            opacity: 0; transition: opacity 0.4s ease;
            animation: hlPulse 1.8s ease-in-out infinite;
        }
        #scene-highlight.visible { opacity: 1; }
        @keyframes hlPulse {
            0%,100% { box-shadow: 0 0 0 4px rgba(59,130,246,0.15), 0 0 24px 8px rgba(59,130,246,0.55); }
            50%      { box-shadow: 0 0 0 8px rgba(59,130,246,0.08), 0 0 36px 14px rgba(59,130,246,0.80); }
        }

        /* ── Mobile layout ──────────────────────────────────── */
        @media (max-width: 1023px) {
            #canvas-wrap { display: none; }
            #glow        { display: none; }
            #screen-overlay {
                position: fixed !important; inset: 0 !important;
                width: 100% !important; height: 100% !important;
                border-radius: 0 !important; transform: none !important;
                opacity: 1 !important;
            }
            #screen-overlay iframe {
                width: 100% !important; height: 100% !important;
                transform: none !important;
            }
            #scene-text { width: 90%; max-width: none; left: 5% !important; right: 5% !important; top: auto; bottom: 100px; transform: none; }
            #scene-text.side-left, #scene-text.side-right { left: 5% !important; right: 5% !important; }
            .st-body { font-size: 14px; }
        }
    </style>

    <script>
        (function(){
            var map = {
                orange:{rgb:'249 115 22',hex:'#f97316'}, blue:{rgb:'59 130 246',hex:'#3b82f6'},
                violet:{rgb:'139 92 246',hex:'#8b5cf6'}, green:{rgb:'34 197 94',hex:'#22c55e'},
                red:{rgb:'239 68 68',hex:'#ef4444'},     pink:{rgb:'236 72 153',hex:'#ec4899'},
            };
            var n = localStorage.getItem('gymmate-accent') || 'orange';
            var a = map[n] || map.orange;
            document.documentElement.style.setProperty('--accent-rgb', a.rgb);
            document.documentElement.style.setProperty('--accent-hex', a.hex);
            document.addEventListener('DOMContentLoaded', function(){
                document.getElementById('glow').style.background =
                    'radial-gradient(ellipse 55% 75% at 50% 48%, rgb('+a.rgb+'/0.20) 0%, rgb('+a.rgb+'/0.06) 45%, transparent 70%)';
            });
        })();
    </script>

    <script type="importmap">
    { "imports": {
        "three": "https://cdn.jsdelivr.net/npm/three@0.160.0/build/three.module.js",
        "three/addons/": "https://cdn.jsdelivr.net/npm/three@0.160.0/examples/jsm/"
    }}
    </script>
</head>
<body>
    <div id="glow"></div>
    <div id="canvas-wrap"></div>
    <div id="screen-overlay">
        <iframe id="app-frame" src="{{ $iframeSrc }}" title="GymMate"></iframe>
        <div id="scene-highlight"></div>
    </div>

    <div id="scene-text" class="side-left">
        <div class="st-inner">
            <div class="st-box">
                <div class="st-label" id="st-label"></div>
                <div class="st-body"  id="st-body"></div>
            </div>
        </div>
    </div>

    <div id="tour-controls">
        <div id="scene-dots">
            <div class="scene-dot active" id="dot-0"></div>
            <div class="scene-dot" id="dot-1"></div>
            <div class="scene-dot" id="dot-2"></div>
            <div class="scene-dot" id="dot-3"></div>
            <div class="scene-dot" id="dot-4"></div>
            <div class="scene-dot" id="dot-5"></div>
            <div class="scene-dot" id="dot-6"></div>
            <div class="scene-dot" id="dot-7"></div>
            <div class="scene-dot" id="dot-8"></div>
            <div class="scene-dot" id="dot-9"></div>
            <div class="scene-dot" id="dot-10"></div>
            <div class="scene-dot" id="dot-11"></div>
            <div class="scene-dot" id="dot-12"></div>
            <div class="scene-dot" id="dot-13"></div>
        </div>
        <button class="ctrl-btn" id="btn-back"  onclick="prevScene()"  disabled>←</button>
        <button class="ctrl-btn" id="btn-reset" onclick="resetScene()">↺</button>
        <button class="ctrl-btn" id="btn-next"  onclick="nextScene()">→</button>
    </div>

    <script type="module">
        import * as THREE from 'three';
        import { RoomEnvironment } from 'three/addons/environments/RoomEnvironment.js';

        const isMobile = window.innerWidth < 1024 || 'ontouchstart' in window;
        const overlay  = document.getElementById('screen-overlay');

        // Declared outside so scene functions can set them on both desktop and mobile
        let rotY = 0, rotX = 0, targetRotY = 0, targetRotX = 0;
        let offsetX = 0, targetOffsetX = 0;
        let phoneScale = 1.0, targetPhoneScale = 1.0;

        if (!isMobile) {
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
        topLight.position.set(0, 20, 5); scene.add(topLight);
        const fillLight = new THREE.DirectionalLight(0xffffff, 0.8);
        fillLight.position.set(0, -20, 5); scene.add(fillLight);
        const sideLight = new THREE.PointLight(0xffffff, 1.5);
        sideLight.position.set(10, 0, 10); scene.add(sideLight);

        /* ── Materials ──────────────────────────────────────────── */
        const matTitanium = new THREE.MeshPhysicalMaterial({ color: 0xcbc9c7, metalness: 0.95, roughness: 0.2, clearcoat: 0.1 });
        const matButton = new THREE.MeshPhysicalMaterial({ color: 0xaaaaaa, metalness: 1.0, roughness: 0.1, clearcoat: 0.3 });
        const matBackGlass = new THREE.MeshPhysicalMaterial({ color: 0xe5e3e1, metalness: 0.1, roughness: 0.6, clearcoat: 0.05, polygonOffset: true, polygonOffsetFactor: -1, polygonOffsetUnits: -1 });
        const matScreen = new THREE.MeshStandardMaterial({ color: 0x080808, roughness: 0.05, metalness: 0.2, polygonOffset: true, polygonOffsetFactor: -1, polygonOffsetUnits: -1 });
        const matLensGlass = new THREE.MeshPhysicalMaterial({ color: 0xffffff, metalness: 0.1, roughness: 0.05, transmission: 0.95, thickness: 0.1, transparent: true, opacity: 0.6 });
        const matCameraFrame = new THREE.MeshPhysicalMaterial({ color: 0x111111, metalness: 0.3, roughness: 0.8 });
        const matGroove = new THREE.MeshStandardMaterial({ color: 0x050505, roughness: 1.0, polygonOffset: true, polygonOffsetFactor: -0.5, polygonOffsetUnits: -0.5 });
        const matSlit = new THREE.MeshStandardMaterial({ color: 0xdddddd, roughness: 0.7, metalness: 0.1 });
        const matDark = new THREE.MeshStandardMaterial({ color: 0x111111, roughness: 0.9 });

        /* ── Phone dimensions ───────────────────────────────────── */
        const PW = 7.76, PH = 16.28, PD = 0.82, PCR = 0.35;
        const Z_FRONT = PD / 2, Z_BACK = -PD / 2;
        const SCREEN_INSET = 0.12;
        const SCREEN_W = PW - SCREEN_INSET * 2;
        const SCREEN_H = PH - SCREEN_INSET * 2;
        const SCREEN_CORNER = PCR - SCREEN_INSET;
        const SCREEN_Z = Z_FRONT + 0.02;

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

        const chassisGeo = new THREE.ExtrudeGeometry(createPhoneShape(PW, PH, PCR), {
            depth: PD, bevelEnabled: true,
            bevelThickness: 0.015, bevelSize: 0.015, bevelSegments: 2, curveSegments: 32
        });
        chassisGeo.translate(0, 0, Z_BACK);
        phone.add(new THREE.Mesh(chassisGeo, matTitanium));

        const screenMesh = new THREE.Mesh(
            new THREE.ShapeGeometry(createPhoneShape(SCREEN_W, SCREEN_H, PCR - SCREEN_INSET)), matScreen);
        screenMesh.position.z = SCREEN_Z;
        phone.add(screenMesh);

        const punchHole = new THREE.Mesh(new THREE.CircleGeometry(0.20, 32), matLensGlass);
        punchHole.position.set(0, SCREEN_H / 2 - 0.35, SCREEN_Z + 0.015);
        phone.add(punchHole);

        const grooveMesh = new THREE.Mesh(
            new THREE.ShapeGeometry(createPhoneShape(PW - 0.04, PH - 0.04, PCR - 0.02)), matGroove);
        grooveMesh.rotation.y = Math.PI;
        grooveMesh.position.z = Z_BACK - 0.005;
        phone.add(grooveMesh);

        const backGlassMesh = new THREE.Mesh(
            new THREE.ShapeGeometry(createPhoneShape(PW - 0.12, PH - 0.12, PCR - 0.06)), matBackGlass);
        backGlassMesh.rotation.y = Math.PI;
        backGlassMesh.position.z = Z_BACK - 0.016;
        phone.add(backGlassMesh);

        // Samsung logo
        const logoCanvas = document.createElement('canvas');
        logoCanvas.width = 1024; logoCanvas.height = 512;
        const lCtx = logoCanvas.getContext('2d');
        const logoColor = 'rgba(60,60,60,0.7)';
        lCtx.fillStyle = logoColor; lCtx.textAlign = 'center';
        lCtx.font = 'bold 100px "Segoe UI", Roboto, sans-serif';
        lCtx.letterSpacing = '12px';
        lCtx.fillText('SAMSUNG', 512, 180);
        (function(ctx, x, y, size) {
            ctx.strokeStyle = logoColor; ctx.lineWidth = size * 0.15; ctx.lineCap = 'butt';
            ctx.beginPath(); ctx.arc(x - size * 0.55, y, size * 0.5, 0.2 * Math.PI, 1.8 * Math.PI, true); ctx.stroke();
            ctx.beginPath(); ctx.arc(x + size * 0.55, y, size * 0.5, 0.2 * Math.PI, 1.8 * Math.PI, true); ctx.stroke();
            ctx.beginPath(); ctx.moveTo(x + size * 0.55, y); ctx.lineTo(x + size * 1.0, y); ctx.stroke();
        })(lCtx, 512, 340, 60);
        const logoTex = new THREE.CanvasTexture(logoCanvas);
        logoTex.colorSpace = THREE.SRGBColorSpace;
        const logoMesh = new THREE.Mesh(new THREE.PlaneGeometry(4, 2),
            new THREE.MeshBasicMaterial({ map: logoTex, transparent: true, opacity: 0.5,
                polygonOffset: true, polygonOffsetFactor: -2, polygonOffsetUnits: -2 }));
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
                matCameraFrame);
            frame.position.z = -ringH - frameHeight;
            const glassGeo = new THREE.CylinderGeometry(glassRadius - 0.005, glassRadius - 0.005, 0.02, 64);
            glassGeo.rotateX(Math.PI / 2);
            const glass = new THREE.Mesh(glassGeo, matLensGlass);
            glass.position.z = -ringH - frameHeight + 0.025;
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

        // USB-C
        const usbGroup = new THREE.Group();
        const uW = 0.6, uHp = 0.26, uR = uHp / 2, uDepth = 0.1;
        usbGroup.add(new THREE.Mesh(new THREE.BoxGeometry(uW, uHp, uDepth), matDark));
        const uSideGeo = new THREE.CylinderGeometry(uR, uR, uDepth, 16);
        uSideGeo.rotateX(Math.PI / 2);
        const uLeft = new THREE.Mesh(uSideGeo, matDark); uLeft.position.x = -uW / 2; usbGroup.add(uLeft);
        const uRight = new THREE.Mesh(uSideGeo, matDark); uRight.position.x = uW / 2;  usbGroup.add(uRight);
        usbGroup.rotation.x = Math.PI / 2;
        usbGroup.position.set(0, -PH / 2 + 0.02, 0);
        phone.add(usbGroup);

        scene.add(phone);

        /* ── Projective helpers ─────────────────────────────────── */
        function adj3(m) {
            return [m[4]*m[8]-m[5]*m[7], m[2]*m[7]-m[1]*m[8], m[1]*m[5]-m[2]*m[4],
                    m[5]*m[6]-m[3]*m[8], m[0]*m[8]-m[2]*m[6], m[2]*m[3]-m[0]*m[5],
                    m[3]*m[7]-m[4]*m[6], m[1]*m[6]-m[0]*m[7], m[0]*m[4]-m[1]*m[3]];
        }
        function mul3(a, b) {
            const c = new Array(9).fill(0);
            for (let i = 0; i < 3; i++) for (let j = 0; j < 3; j++) for (let k = 0; k < 3; k++)
                c[3*i+j] += a[3*i+k] * b[3*k+j];
            return c;
        }
        function mulv3(m, v) {
            return [m[0]*v[0]+m[1]*v[1]+m[2]*v[2],
                    m[3]*v[0]+m[4]*v[1]+m[5]*v[2],
                    m[6]*v[0]+m[7]*v[1]+m[8]*v[2]];
        }
        function basisToPoints(x1,y1,x2,y2,x3,y3,x4,y4) {
            const m = [x1,x2,x3, y1,y2,y3, 1,1,1];
            const v = mulv3(adj3(m), [x4,y4,1]);
            return mul3(m, [v[0],0,0, 0,v[1],0, 0,0,v[2]]);
        }
        function computeH(x1s,y1s,x2s,y2s,x3s,y3s,x4s,y4s, x1d,y1d,x2d,y2d,x3d,y3d,x4d,y4d) {
            return mul3(basisToPoints(x1d,y1d,x2d,y2d,x3d,y3d,x4d,y4d),
                        adj3(basisToPoints(x1s,y1s,x2s,y2s,x3s,y3s,x4s,y4s)));
        }

        /* ── Overlay ────────────────────────────────────────────── */
        const _pv = new THREE.Vector3();

        function projPt(lx, ly) {
            _pv.set(lx, ly, SCREEN_Z);
            phone.localToWorld(_pv);
            _pv.project(camera);
            return [(_pv.x+1)/2*window.innerWidth, (1-_pv.y)/2*window.innerHeight];
        }

        function updateOverlay() {
            const fov  = THREE.MathUtils.degToRad(40);
            const dist = camera.position.z - SCREEN_Z;
            const ppu  = window.innerHeight / (2 * Math.tan(fov / 2) * dist);
            const W    = SCREEN_W * ppu;
            const H    = SCREEN_H * ppu;
            const cr   = SCREEN_CORNER * ppu;
            const tl = projPt(-SCREEN_W/2,  SCREEN_H/2);
            const tr = projPt( SCREEN_W/2,  SCREEN_H/2);
            const br = projPt( SCREEN_W/2, -SCREEN_H/2);
            const bl = projPt(-SCREEN_W/2, -SCREEN_H/2);
            const h = computeH(0,0, W,0, W,H, 0,H,
                                tl[0],tl[1], tr[0],tr[1], br[0],br[1], bl[0],bl[1]);
            const n = h[8];
            const m = h.map(v => v / n);
            overlay.style.left   = '0';
            overlay.style.top    = '0';
            overlay.style.width  = W + 'px';
            overlay.style.height = H + 'px';
            overlay.style.borderRadius = cr + 'px';
            overlay.style.transformOrigin = '0 0';
            overlay.style.transform =
                `matrix3d(${m[0]},${m[3]},0,${m[6]},${m[1]},${m[4]},0,${m[7]},0,0,1,0,${m[2]},${m[5]},0,1)`;
        }

        window.addEventListener('resize', () => {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
            updateOverlay();
        });

        /* ── Motion state ───────────────────────────────────────── */
        let t = 0;

        function animate() {
            requestAnimationFrame(animate);
            t += 0.007;
            phone.position.y = Math.sin(t) * 0.28;
            rotY        += (targetRotY        - rotY)        * 0.08;
            rotX        += (targetRotX        - rotX)        * 0.08;
            offsetX     += (targetOffsetX     - offsetX)     * 0.03;
            phoneScale  += (targetPhoneScale  - phoneScale)  * 0.05;
            phone.rotation.y = rotY;
            phone.rotation.x = rotX;
            phone.position.x = offsetX;
            phone.scale.setScalar(phoneScale);
            const frontFacing = Math.cos(rotX) * Math.cos(rotY) > 0;
            overlay.style.opacity = frontFacing ? '1' : '0';
            updateOverlay();
            renderer.render(scene, camera);
        }
        animate();
        } // end !isMobile

        /* ── Scene descriptions ─────────────────────────────── */
        const SCENES = [
            { side:'left',  label:'Dashboard',           target: null,
              text:'Willkommen im Dashboard – auch <em>Standorte</em> genannt. Für jede Stadt, Location oder jedes Gym erstellst du hier eine eigene, vollständig unabhängige Gruppe, die getrennt getrackt und analysiert wird.' },
            { side:'left',  label:'Navigation',          target: null,
              text:'Eine Wischgeste von links öffnet jederzeit die Sidebar – deinen Navigations-Hub. Wechsle blitzschnell zwischen Seiten oder passe in den Einstellungen Farben und Theme ganz nach deinem Geschmack an.' },
            { side:'left',  label:'Standort anlegen',    target: null,
              text:'Als Erstes legen wir testweise ein Gym an. Du kannst es jederzeit über den Bearbeiten-Button umbenennen oder vollständig löschen – kein Aufwand.' },
            { side:'left',  label:'Name eingeben',       target: '[wire\\:click*="open"]',
              text:'Einfach einen Namen tippen – fertig. Keine weiteren Pflichtfelder, kein Overhead. Ein Bild ist komplett optional.' },
            { side:'right', label:'Dein Gym',            target: 'a[href*="trainingskategorie"]',
              text:'Dein Gym erscheint sofort im Dashboard. Du siehst den Namen, wie oft du dort trainiert hast und eine Kalender-Punkteansicht, die deinen Trainingsrhythmus an diesem Standort visualisiert. Lass uns reingehen!' },
            { side:'right', label:'Trainingsgruppen',    target: 'a[href*="uebungen"]',
              text:'In deinem neuen Standort findest du bereits vorgefertigte Gruppen nach Muskelgruppe – Arme, Schulter, Beine, Brust, Rücken und mehr. Oben siehst du deinen Wochenplan – den richten wir ein, sobald alle Übungen stehen. Klicke jetzt auf <em>Arme</em>!' },
            { side:'right', label:'Übung erstellen',     target: '[wire\\:click*="open"]',
              text:'Mit dem <em>+</em> legst du jederzeit neue Übungen an – mit optionaler Beschreibung und der Wahl zwischen ein- und beidhändig. Diese Option ist entscheidend für eine präzise Fortschrittsanalyse.' },
            { side:'right', label:'Deine erste Übung',   target: 'a[href*="/uebungen/"]',
              text:'Deine erste Übung ist bereit – lass uns reingehen und schauen, wie Sätze aufgezeichnet werden und was es dabei Besonderes zu beachten gibt!' },
            { side:'left',  label:'Session starten',     target: 'input[wire\\:model="sets.0.weight"]',
              text:'Hier bist du in deiner Trainings-Session. Als Einstieg tragen wir <em>25 kg</em> und <em>6 Wiederholungen</em> beim Bizeps Curl ein – einfach Gewicht und Reps befüllen.' },
            { side:'left',  label:'Erster Satz & Timer', target: 'button[wire\\:click="addSet"]',
              text:'Dein erster Satz steht! Unten rechts findest du einen optionalen Ruhe-Timer für strukturierte Pausen. Mit dem <em>+</em> darunter fügst du sofort den nächsten Satz hinzu.' },
            { side:'left',  label:'Speicher-Tipp',       target: () => findBtn('Speichern'),
              text:'Kleiner Tipp: Speichere erst, wenn du mit der gesamten Übung fertig bist – so bleibt alles sauber geordnet. Keine Sorge: deine Eingaben werden automatisch zwischengespeichert, solange du auf der Seite bleibst.' },
            { side:'left',  label:'2 Sätze & Speichern', target: () => findBtn('Speichern'),
              text:'Zwei Sätze – das Minimum für optimales Wachstum! Jetzt speichern und direkt ein zweites Training nachtragen. Datum per Kalender oder manuell eingeben – egal wann du es vergessen hast.' },
            { side:'left',  label:'Korrekturen',         target: 'tr[x-on\\:click*="load-session"]',
              text:'Kein Stress bei Tippfehlern! Klicke einfach auf eine aufgezeichnete Session in der History, um Gewicht, Reps oder Datum zu korrigieren – oder den ganzen Eintrag zu löschen.' },
            { side:'left',  label:'Zurück zum Dashboard',target: null,
              text:'Alles erledigt und korrigiert – perfekt! Die Sidebar bringt dich jederzeit zurück ins Dashboard, wo alle deine Standorte auf einen Blick warten.' },
        ];

        /* ── Tour helpers ───────────────────────────────────────── */
        const iframe       = document.getElementById('app-frame');
        const highlightEl  = document.getElementById('scene-highlight');
        const delay    = ms => new Promise(r => setTimeout(r, ms));
        const waitLoad = () => new Promise(r => iframe.addEventListener('load', r, { once: true }));
        const getDoc   = () => iframe.contentDocument;
        const getWin   = () => iframe.contentWindow;

        function getPos(el) {
            const r  = el.getBoundingClientRect();
            const iw = iframe.contentWindow;
            return { x: (r.left + r.width  / 2) / iw.innerWidth,
                     y: (r.top  + r.height / 2) / iw.innerHeight };
        }

        function showRing(fx, fy) {
            ['click-ring', 'click-ring click-ring-outer'].forEach(cls => {
                const d = document.createElement('div');
                d.className = cls;
                d.style.left = (fx * 100) + '%';
                d.style.top  = (fy * 100) + '%';
                overlay.appendChild(d);
                setTimeout(() => d.remove(), 1200);
            });
        }

        function showSwipe() {
            ['swipe-dot', 'swipe-tail'].forEach(cls => {
                const d = document.createElement('div');
                d.className = cls;
                overlay.appendChild(d);
                setTimeout(() => d.remove(), 2100);
            });
        }

        async function typeText(input, text) {
            input.focus();
            for (let i = 0; i < text.length; i++) {
                await delay(110 + Math.random() * 90);
                input.value = text.slice(0, i + 1);
                input.dispatchEvent(new Event('input',  { bubbles: true }));
                input.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }

        async function fillInput(input, value) {
            if (!input) return;
            input.focus();
            input.value = String(value);
            input.dispatchEvent(new Event('input',  { bubbles: true }));
            input.dispatchEvent(new Event('change', { bubbles: true }));
            await delay(350);
        }

        async function tapEl(el) {
            if (!el) return;
            const p = getPos(el);
            showRing(p.x, p.y);
            await delay(550);
            el.click();
        }

        function findBtn(text) {
            return [...getDoc().querySelectorAll('button')].find(b => b.textContent.trim() === text) || null;
        }

        let _hlTimer = null;

        function clearHighlight() {
            clearTimeout(_hlTimer);
            highlightEl.classList.remove('visible');
        }

        function placeHighlight(target, duration = 700) {
            if (!target) { clearHighlight(); return; }
            let el = null;
            try {
                el = typeof target === 'function' ? target() : getDoc().querySelector(target);
            } catch(e) {}
            if (!el) { clearHighlight(); return; }
            const r   = el.getBoundingClientRect();
            const iw  = getWin().innerWidth;
            const ih  = getWin().innerHeight;
            const pad = 6;
            highlightEl.style.left   = ((r.left   - pad) / iw * 100) + '%';
            highlightEl.style.top    = ((r.top    - pad) / ih * 100) + '%';
            highlightEl.style.width  = ((r.width  + pad * 2) / iw * 100) + '%';
            highlightEl.style.height = ((r.height + pad * 2) / ih * 100) + '%';
            highlightEl.classList.add('visible');
            clearTimeout(_hlTimer);
            _hlTimer = setTimeout(clearHighlight, duration);
        }

        /* ── Scene state machine ────────────────────────────────── */
        let currentScene = 0;
        let isAnimating  = false;

        const btnNext  = document.getElementById('btn-next');
        const btnBack  = document.getElementById('btn-back');
        const btnReset = document.getElementById('btn-reset');

        const sceneTextEl = document.getElementById('scene-text');
        const stLabelEl   = document.getElementById('st-label');
        const stBodyEl    = document.getElementById('st-body');

        function setAnimating(val) {
            isAnimating = val;
            btnNext.disabled  = val || currentScene >= 13;
            btnBack.disabled  = val || currentScene <= 0;
            btnReset.disabled = val;
            btnNext.classList.toggle('loading', val);
        }

        function updateDots() {
            for (let i = 0; i <= 13; i++) {
                document.getElementById('dot-' + i).classList.toggle('active', i === currentScene);
            }
        }

        function showText(idx) {
            const sc = SCENES[idx];
            if (!sc) {
                sceneTextEl.classList.remove('visible');
                clearHighlight();
                return;
            }
            sceneTextEl.classList.remove('visible');
            clearHighlight();
            setTimeout(() => {
                stLabelEl.textContent = sc.label;
                stBodyEl.innerHTML    = sc.text;
                sceneTextEl.classList.remove('side-left', 'side-right');
                sceneTextEl.classList.add('side-' + sc.side);
                sceneTextEl.classList.add('visible');
                setTimeout(() => placeHighlight(sc.target), 150);
            }, 300);
        }

        // For scenes < 3: shows SCENES[scene]. For scenes >= 3: shows SCENES[scene+1]
        // so that the "result" text appears after the animation that produced it.
        function showSceneText(scene) {
            showText(scene < 3 ? scene : scene + 1);
        }

        /* ── Forward scenes ─────────────────────────────────── */
        async function scene1_forward() {
            targetPhoneScale = 1.08; // lean in for the swipe
            targetRotY = -0.42; targetRotX = 0.42;
            await delay(300);
            showSwipe();
            await delay(200);
            try { getWin().openSidebar(); } catch(e) {}
            await delay(2800);
        }

        async function scene2_forward() {
            try { getWin().closeSidebar(); } catch(e) {}
            targetRotY = 0; targetRotX = 0;
            targetPhoneScale = 1.0;
            await delay(1800);
        }

        async function scene3_forward() {
            targetPhoneScale = 1.12; // zoom in to watch the typing
            const addBtn = getDoc().querySelector('[wire\\:click*="open"]');
            if (addBtn) { await tapEl(addBtn); }
            await delay(1500);
            const nameInput = getDoc().querySelector('input[wire\\:model="name"]');
            if (nameInput) await typeText(nameInput, 'Mein Gym');
            await delay(600);
            if (findBtn('Erstellen')) { await tapEl(findBtn('Erstellen')); }
            await waitLoad();
            await delay(800);
        }

        async function scene4_forward() {
            targetPhoneScale = 0.86; // pull back as phone tilts
            const locationLink = getDoc().querySelector('a[href*="trainingskategorie"]');
            if (locationLink) {
                const p = getPos(locationLink);
                showRing(p.x, p.y);
                await delay(450);
                locationLink.click();
            }
            targetOffsetX = -5.5; targetRotY = 0.52; targetRotX = -0.26;
            await waitLoad();
            await delay(300);
            targetPhoneScale = 1.10; // zoom in on categories after tilt
            await delay(600);
        }

        async function scene5_forward() {
            const planLink = getDoc().querySelector('a[href*="uebungen"]');
            if (planLink) {
                const p = getPos(planLink);
                showRing(p.x, p.y);
                await delay(450);
                planLink.click();
            }
            await waitLoad();
            await delay(600);
        }

        async function scene6_forward() {
            const addBtn = getDoc().querySelector('[wire\\:click*="open"]');
            if (addBtn) { await tapEl(addBtn); }
            await delay(1500);
            const nameInput = getDoc().querySelector('input[wire\\:model="name"]');
            if (nameInput) await typeText(nameInput, 'Bizeps Curl');
            await delay(600);
            if (findBtn('Erstellen')) { await tapEl(findBtn('Erstellen')); }
            await delay(1200);
        }

        async function scene7_forward() {
            targetPhoneScale = 0.80; // dramatic pull-back during the flip
            const exerciseLink = getDoc().querySelector('a[href*="/uebungen/"]');
            if (exerciseLink) {
                const p = getPos(exerciseLink);
                showRing(p.x, p.y);
                exerciseLink.click();
            }
            targetOffsetX = 5.5; targetRotY = -0.52; targetRotX = -0.26;
            await waitLoad();
            await delay(300);
            targetPhoneScale = 1.18; // snap in tight after flip
            await delay(800);
        }

        async function scene8_forward() {
            targetOffsetX = 0; targetRotY = 0; targetRotX = 0;
            targetPhoneScale = 1.22; // tightest close-up for session logging
            await delay(900);
            const w0 = getDoc().querySelector('input[wire\\:model="sets.0.weight"]');
            const r0 = getDoc().querySelector('input[wire\\:model="sets.0.reps"]');
            if (w0) { const p = getPos(w0); showRing(p.x, p.y); }
            await fillInput(w0, 25);
            if (r0) { const p = getPos(r0); showRing(p.x, p.y); }
            await fillInput(r0, 6);
        }

        async function scene9_forward() {
            await tapEl(getDoc().querySelector('button[wire\\:click="addSet"]'));
            await delay(800);
        }

        async function scene10_forward() {
            const w1 = getDoc().querySelector('input[wire\\:model="sets.1.weight"]');
            const r1 = getDoc().querySelector('input[wire\\:model="sets.1.reps"]');
            if (w1) { const p = getPos(w1); showRing(p.x, p.y); }
            await fillInput(w1, 25);
            if (r1) { const p = getPos(r1); showRing(p.x, p.y); }
            await fillInput(r1, 5);
        }

        async function scene11_forward() {
            await tapEl(findBtn('Speichern'));
            await waitLoad();
            await delay(1000);
            targetPhoneScale = 1.05; // pull back to reveal history table
            // After reload the form already has 2 rows (app mirrors last session count)
            await fillInput(getDoc().querySelector('input[wire\\:model="sets.0.weight"]'), 22.5);
            await fillInput(getDoc().querySelector('input[wire\\:model="sets.0.reps"]'), 7);
            await fillInput(getDoc().querySelector('input[wire\\:model="sets.1.weight"]'), 22.5);
            await fillInput(getDoc().querySelector('input[wire\\:model="sets.1.reps"]'), 6);
            // Set date to 3 days ago so it counts as a separate session
            const d = new Date(); d.setDate(d.getDate() - 3);
            const dateStr = d.toISOString().slice(0, 10);
            await fillInput(getDoc().querySelector('input[wire\\:model="loggedAt"]'), dateStr);
            await delay(400);
            await tapEl(findBtn('Speichern'));
            await waitLoad();
            await delay(800);
        }

        async function scene12_forward() {
            // First session = today (25kg, 2 rows). Second session = 3 days ago (22.5kg).
            // Its first row sits at index 2 in the table.
            const rows = getDoc().querySelectorAll('tr[x-on\\:click*="load-session"]');
            const row  = rows[2] || rows[rows.length - 1];
            if (row) {
                const r  = row.getBoundingClientRect();
                const iw = getWin();
                showRing((r.left + r.width / 2) / iw.innerWidth, (r.top + r.height / 2) / iw.innerHeight);
                await delay(550);
                row.click();
            }
            await delay(1200);
            const modal = getDoc().querySelector('div.fixed.inset-0.z-50');
            if (modal) {
                const w0 = modal.querySelector('input[wire\\:model="sets.0.weight"]');
                const w1 = modal.querySelector('input[wire\\:model="sets.1.weight"]');
                if (w0) { const p = getPos(w0); showRing(p.x, p.y); }
                await fillInput(w0, 21.5);
                if (w1) { const p = getPos(w1); showRing(p.x, p.y); }
                await fillInput(w1, 21.5);
                await delay(400);
                const saveBtn = [...modal.querySelectorAll('button')].find(b => b.textContent.trim() === 'Save');
                if (saveBtn) { await tapEl(saveBtn); }
            }
            await waitLoad();
            await delay(600);
        }

        async function scene13_forward() {
            targetPhoneScale = 0.92; // wide overview for the final dashboard
            try { getWin().openSidebar(); } catch(e) {}
            await delay(1200);
            const dashLink = getDoc().querySelector('a[href*="standorte"]');
            if (dashLink) { await tapEl(dashLink); }
            await waitLoad();
            await delay(600);
        }

        /* ── Backward scenes ────────────────────────────────── */
        async function scene1_backward() {
            try { getWin().closeSidebar(); } catch(e) {}
            targetRotY = 0; targetRotX = 0; targetPhoneScale = 1.0;
            await delay(1200);
        }
        async function scene2_backward() {
            targetRotY = -0.42; targetRotX = 0.42; targetPhoneScale = 1.08;
            await delay(1200);
            try { getWin().openSidebar(); } catch(e) {}
            await delay(500);
        }
        async function scene3_backward() { targetPhoneScale = 1.0; await delay(400); }
        async function scene4_backward() {
            targetPhoneScale = 0.86;
            targetOffsetX = 0; targetRotY = 0; targetRotX = 0;
            await delay(600);
            targetPhoneScale = 1.12;
            await delay(1200);
        }
        async function scene5_backward() {
            try { getWin().history.back(); } catch(e) {}
            await waitLoad(); await delay(500);
        }
        async function scene6_backward()  { await delay(300); }
        async function scene7_backward() {
            targetPhoneScale = 0.80; // mirror the pull-back
            targetOffsetX = 0; targetRotY = 0; targetRotX = 0;
            try { getWin().history.back(); } catch(e) {}
            await waitLoad();
            await delay(300);
            targetPhoneScale = 1.10;
            await delay(500);
        }
        async function scene8_backward() {
            targetOffsetX = 5.5; targetRotY = -0.52; targetRotX = -0.26;
            targetPhoneScale = 1.18;
            await delay(1200);
        }
        async function scene9_backward()  { targetPhoneScale = 1.22; await delay(300); }
        async function scene10_backward() { targetPhoneScale = 1.22; await delay(300); }
        async function scene11_backward() { targetPhoneScale = 1.22; await delay(300); }
        async function scene12_backward() { targetPhoneScale = 1.05; await delay(300); }
        async function scene13_backward() { targetPhoneScale = 1.05; await delay(300); }

        /* ── Controls ───────────────────────────────────────── */
        async function nextScene() {
            if (isAnimating || currentScene >= 13) return;
            setAnimating(true);
            currentScene++;
            updateDots();
            // Scenes 1-3: show text BEFORE animation; 4+: hide during animation
            if (currentScene <= 3) showText(currentScene);
            else { sceneTextEl.classList.remove('visible'); clearHighlight(); }
            if (currentScene ===  1) await scene1_forward();
            if (currentScene ===  2) await scene2_forward();
            if (currentScene ===  3) await scene3_forward();
            if (currentScene ===  4) await scene4_forward();
            if (currentScene ===  5) await scene5_forward();
            if (currentScene ===  6) await scene6_forward();
            if (currentScene ===  7) await scene7_forward();
            if (currentScene ===  8) await scene8_forward();
            if (currentScene ===  9) await scene9_forward();
            if (currentScene === 10) await scene10_forward();
            if (currentScene === 11) await scene11_forward();
            if (currentScene === 12) await scene12_forward();
            if (currentScene === 13) await scene13_forward();
            // Scenes 3+: show result text AFTER animation
            if (currentScene >= 3) showSceneText(currentScene);
            setAnimating(false);
        }

        async function prevScene() {
            if (isAnimating || currentScene <= 0) return;
            setAnimating(true);
            const from = currentScene;
            currentScene--;
            updateDots();
            sceneTextEl.classList.remove('visible');
            clearHighlight();
            if (from ===  1) await scene1_backward();
            if (from ===  2) await scene2_backward();
            if (from ===  3) await scene3_backward();
            if (from ===  4) await scene4_backward();
            if (from ===  5) await scene5_backward();
            if (from ===  6) await scene6_backward();
            if (from ===  7) await scene7_backward();
            if (from ===  8) await scene8_backward();
            if (from ===  9) await scene9_backward();
            if (from === 10) await scene10_backward();
            if (from === 11) await scene11_backward();
            if (from === 12) await scene12_backward();
            if (from === 13) await scene13_backward();
            showSceneText(currentScene);
            setAnimating(false);
        }

        async function resetScene() {
            if (isAnimating) return;
            setAnimating(true);
            currentScene = 0;
            updateDots();
            sceneTextEl.classList.remove('visible');
            clearHighlight();
            try { getWin().closeSidebar(); } catch(e) {}
            targetRotY = 0; targetRotX = 0; targetOffsetX = 0; targetPhoneScale = 1.0;
            iframe.src = '{{ $iframeSrc }}';
            await waitLoad();
            await delay(400);
            showText(0);
            setAnimating(false);
        }

        updateDots();
        showText(0);

        window.nextScene  = nextScene;
        window.prevScene  = prevScene;
        window.resetScene = resetScene;
    </script>
</body>
</html>

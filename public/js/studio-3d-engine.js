/**
 * 🌸 Istanbul Çiçek Evi — 3D Hibrit Çiçek & Vazo Stüdyosu Motoru (Three.js)
 * 
 * Mimari:
 * 1. Katı 3D Seramik Saksı / Lüks Silindir Kutu (Kusursuz stüdyo ışığı ve gölgesi)
 * 2. 3D Kubbe Geometrisi (12 Yuva: Arka, Orta, Ön katmanlar)
 * 3. Çiçek Başları: Yüksek çözünürlüklü gerçek fotoğraf dokulu 3D Hibrit Çiçekler
 * 4. 360° Kesintisiz Döndürme (Paralaks derinliği ve gerçek 3D uzay)
 * 5. Çiçeğe tıklayınca 3D uzayda yaylanan boy ayarı
 */

window.FlowerStudio3D = (function() {
    'use strict';

    let scene, camera, renderer, controls;
    let canvasContainer, canvas;
    let potGroup, domeGroup, slotsMarkerGroup;
    let raycaster, mouse;
    let animationFrameId;

    // Doku Önbelleği (Güller, Papatyalar, Santini, Portakal, Okaliptüs)
    const textureLoader = new THREE.TextureLoader();
    const textures = {};

    // 12 Yuvalı Kusursuz Kubbe Geometrisi (Biedermeier Dome)
    // x, y, z koordinatları saksı ağzından (y=1.65) başlayarak yükselir
    const DOME_SLOTS = [
        // 🌟 ARKA SIRA (3 Adet - Tepe Noktası: 1, 2, 3)
        { id: 1, tier: 'back', x: -0.38, y: 2.22, z: -0.28, scale: 0.85, defaultHeight: 'tall' },
        { id: 2, tier: 'back', x:  0.00, y: 2.32, z: -0.34, scale: 0.90, defaultHeight: 'tall' },
        { id: 3, tier: 'back', x:  0.38, y: 2.22, z: -0.28, scale: 0.85, defaultHeight: 'tall' },

        // 🌟 ORTA SIRA (5 Adet - Kubbe Ana Gövdesi: 4, 5, 6, 7, 8)
        { id: 4, tier: 'mid', x: -0.65, y: 1.95, z: -0.06, scale: 0.82, defaultHeight: 'medium' },
        { id: 5, tier: 'mid', x: -0.32, y: 2.05, z:  0.08, scale: 0.88, defaultHeight: 'medium' },
        { id: 6, tier: 'mid', x:  0.00, y: 2.10, z:  0.15, scale: 0.90, defaultHeight: 'medium' },
        { id: 7, tier: 'mid', x:  0.32, y: 2.05, z:  0.08, scale: 0.88, defaultHeight: 'medium' },
        { id: 8, tier: 'mid', x:  0.65, y: 1.95, z: -0.06, scale: 0.82, defaultHeight: 'medium' },

        // 🌟 ÖN SIRA (4 Adet - Saksı Ağzına Yaslanan, Öne Açılan: 9, 10, 11, 12)
        { id: 9,  tier: 'front', x: -0.48, y: 1.76, z: 0.34, scale: 0.80, defaultHeight: 'short' },
        { id: 10, tier: 'front', x: -0.16, y: 1.78, z: 0.38, scale: 0.84, defaultHeight: 'short' },
        { id: 11, tier: 'front', x:  0.16, y: 1.78, z: 0.38, scale: 0.84, defaultHeight: 'short' },
        { id: 12, tier: 'front', x:  0.48, y: 1.76, z: 0.34, scale: 0.80, defaultHeight: 'short' }
    ];

    const slotObjects = {};   // slotId -> THREE.Group (Çiçek nesnesi)
    const markerObjects = {}; // slotId -> THREE.Group (Boş yuva rozeti)

    let callbacks = {
        onSlotClick: null,
        onFlowerClick: null,
        onSlotHover: null,
        onSlotDrop: null
    };

    let hoveredSlotId = null;
    let currentPotType = 'ceramic'; // 'ceramic', 'box', 'glass'

    /**
     * Dokuları Önceden Yükle
     */
    function preloadTextures() {
        const list = {
            red_rose: '/images/studio/red_rose.png',
            white_rose: '/images/studio/white_rose.png',
            pink_rose: '/images/studio/pink_rose.png',
            yellow_tulip: '/images/studio/yellow_tulip.png',
            daisy: '/images/studio/daisy.png',
            green_santini: '/images/studio/green_santini.png',
            dried_orange: '/images/studio/dried_orange.png',
            eucalyptus: '/images/studio/eucalyptus.png',
            gypsophila: '/images/studio/gypsophila.png',
            leaves_collar: '/images/studio/leaves_collar.png'
        };

        for (const [key, url] of Object.entries(list)) {
            textures[key] = textureLoader.load(url);
        }
    }

    /**
     * 1. 3D Sahneyi Başlat
     */
    function init(containerEl, userCallbacks = {}) {
        canvasContainer = containerEl;
        callbacks = Object.assign(callbacks, userCallbacks);

        preloadTextures();

        const width = canvasContainer.clientWidth || 540;
        const height = canvasContainer.clientHeight || 480;

        // Sahne
        scene = new THREE.Scene();

        // Kamera (Portre Lens Açısı: 34° FOV ile doğal, orantılı ve taşmayan stüdyo görüntüsü)
        camera = new THREE.PerspectiveCamera(34, width / height, 0.1, 100);
        camera.position.set(0, 2.6, 6.2);

        // Renderer
        renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true, powerPreference: 'high-performance' });
        renderer.setSize(width, height);
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        renderer.shadowMap.enabled = true;
        renderer.shadowMap.type = THREE.PCFSoftShadowMap;
        renderer.toneMapping = THREE.ACESFilmicToneMapping;
        renderer.toneMappingExposure = 1.1;

        canvas = renderer.domElement;
        canvas.style.width = '100%';
        canvas.style.height = '100%';
        canvas.style.display = 'block';
        canvasContainer.innerHTML = '';
        canvasContainer.appendChild(canvas);

        // OrbitControls (360° Serbest Döndürme, Ekrandan Taşmayan Mesafe Sınırları)
        controls = new THREE.OrbitControls(camera, canvas);
        controls.enableDamping = true;
        controls.dampingFactor = 0.08;
        controls.target.set(0, 1.35, 0); // Vazo ve buketin görsel ağırlık merkezi
        controls.minDistance = 3.8;
        controls.maxDistance = 8.5;
        controls.maxPolarAngle = Math.PI * 0.48; // Masanın altına girmeyi engelle
        controls.minPolarAngle = 0.08;           // Kuşbakışı bakmaya izin ver

        // Gruplar
        potGroup = new THREE.Group();
        domeGroup = new THREE.Group();
        slotsMarkerGroup = new THREE.Group();
        scene.add(potGroup);
        scene.add(domeGroup);
        scene.add(slotsMarkerGroup);

        // Işıklandırma ve Zemin
        setupStudioLighting();
        setupStudioFloor();

        // Saksıyı İnşa Et
        createVase('ceramic');

        // Boş Yuva Rozetlerini Kur
        setupDomeSlotMarkers();

        // Etkileşim Dinleyicileri
        raycaster = new THREE.Raycaster();
        mouse = new THREE.Vector2();
        setupEventListeners();

        // Render Döngüsü
        animate();

        window.addEventListener('resize', onWindowResize);
    }

    /**
     * Stüdyo Işıkları (Sıcak, Doğal ve Fotoğrafik)
     */
    function setupStudioLighting() {
        const ambient = new THREE.AmbientLight(0xfff7ed, 0.9);
        scene.add(ambient);

        const hemi = new THREE.HemisphereLight(0xffffff, 0xf1f5f9, 0.65);
        hemi.position.set(0, 8, 0);
        scene.add(hemi);

        // Ana Stüdyo Işığı (Sağ Ön Üstten)
        const keyLight = new THREE.DirectionalLight(0xffffff, 1.25);
        keyLight.position.set(3.5, 6, 4.5);
        keyLight.castShadow = true;
        keyLight.shadow.mapSize.width = 1024;
        keyLight.shadow.mapSize.height = 1024;
        keyLight.shadow.camera.near = 0.5;
        keyLight.shadow.camera.far = 15;
        keyLight.shadow.camera.left = -2.5;
        keyLight.shadow.camera.right = 2.5;
        keyLight.shadow.camera.top = 2.5;
        keyLight.shadow.camera.bottom = -2.5;
        keyLight.shadow.bias = -0.0004;
        scene.add(keyLight);

        // Dolgu Işığı (Sol Ön)
        const fillLight = new THREE.DirectionalLight(0xfce7f3, 0.5);
        fillLight.position.set(-4, 4, 3);
        scene.add(fillLight);

        // Arka Taç Işığı (Rim Light)
        const rimLight = new THREE.DirectionalLight(0xffedd5, 0.75);
        rimLight.position.set(0, 5, -4);
        scene.add(rimLight);
    }

    /**
     * Zemin & Masa Yumuşak Temas Gölgesi
     */
    function setupStudioFloor() {
        // Yumuşak radyal gölge dokusu
        const canvasShadow = document.createElement('canvas');
        canvasShadow.width = 256;
        canvasShadow.height = 256;
        const ctx = canvasShadow.getContext('2d');
        const grad = ctx.createRadialGradient(128, 128, 15, 128, 128, 120);
        grad.addColorStop(0, 'rgba(0,0,0,0.38)');
        grad.addColorStop(0.4, 'rgba(0,0,0,0.18)');
        grad.addColorStop(0.8, 'rgba(0,0,0,0.04)');
        grad.addColorStop(1, 'rgba(0,0,0,0)');
        ctx.fillStyle = grad;
        ctx.fillRect(0, 0, 256, 256);

        const shadowTex = new THREE.CanvasTexture(canvasShadow);
        const planeGeo = new THREE.PlaneGeometry(3.6, 3.6);
        const planeMat = new THREE.MeshBasicMaterial({ map: shadowTex, transparent: true, opacity: 0.85, depthWrite: false });
        const shadowMesh = new THREE.Mesh(planeGeo, planeMat);
        shadowMesh.rotation.x = -Math.PI / 2;
        shadowMesh.position.y = 0.01;
        scene.add(shadowMesh);
    }

    /**
     * 2. Katı 3D Saksı / Kutu / Vazo İnşası
     */
    function createVase(type = 'ceramic') {
        currentPotType = type;

        while (potGroup.children.length > 0) {
            const obj = potGroup.children[0];
            potGroup.remove(obj);
            if (obj.geometry) obj.geometry.dispose();
            if (obj.material) obj.material.dispose();
        }

        const potHeight = 1.75;
        const potRadiusTop = 1.15;
        const potRadiusBottom = 0.95;

        if (type === 'box') {
            // Lüks Silindir Siyah Kadife Kutu (Altın Şeritli)
            const boxGeo = new THREE.CylinderGeometry(potRadiusTop, potRadiusTop, potHeight, 48);
            const boxMat = new THREE.MeshStandardMaterial({
                color: 0x18181b,
                roughness: 0.45,
                metalness: 0.15
            });
            const boxMesh = new THREE.Mesh(boxGeo, boxMat);
            boxMesh.position.y = potHeight / 2;
            boxMesh.castShadow = true;
            boxMesh.receiveShadow = true;
            potGroup.add(boxMesh);

            // Altın Yaldızlı Gövde Şeridi
            const goldGeo = new THREE.CylinderGeometry(potRadiusTop * 1.008, potRadiusTop * 1.008, 0.1, 48);
            const goldMat = new THREE.MeshStandardMaterial({ color: 0xd97706, metalness: 0.85, roughness: 0.25 });
            const goldRing = new THREE.Mesh(goldGeo, goldMat);
            goldRing.position.y = potHeight * 0.75;
            potGroup.add(goldRing);

        } else if (type === 'glass') {
            // Buzlu / Dumanlı Lüks Cam Vazo
            const glassGeo = new THREE.CylinderGeometry(potRadiusTop, potRadiusBottom, potHeight, 48);
            const glassMat = new THREE.MeshPhysicalMaterial({
                color: 0xe2e8f0,
                transparent: true,
                opacity: 0.75,
                roughness: 0.15,
                transmission: 0.75,
                ior: 1.45,
                side: THREE.DoubleSide
            });
            const glassMesh = new THREE.Mesh(glassGeo, glassMat);
            glassMesh.position.y = potHeight / 2;
            glassMesh.castShadow = true;
            potGroup.add(glassMesh);

        } else {
            // MAT BEYAZ SERAMİK SAKSI (Referans Fotoğraftaki Gibi)
            const potGeo = new THREE.CylinderGeometry(potRadiusTop, potRadiusBottom, potHeight, 48);
            const potMat = new THREE.MeshStandardMaterial({
                color: 0xffffff,
                roughness: 0.38,
                metalness: 0.05
            });
            const potMesh = new THREE.Mesh(potGeo, potMat);
            potMesh.position.y = potHeight / 2;
            potMesh.castShadow = true;
            potMesh.receiveShadow = true;
            potGroup.add(potMesh);

            // Saksı Ağzı Kalın Seramik Dudak (Rim)
            const lipGeo = new THREE.TorusGeometry(potRadiusTop, 0.055, 16, 48);
            const lipMesh = new THREE.Mesh(lipGeo, potMat);
            lipMesh.rotation.x = Math.PI / 2;
            lipMesh.position.y = potHeight;
            lipMesh.castShadow = true;
            potGroup.add(lipMesh);
        }

        // Saksı Ağzı Koyu Yeşil Oasis Süngeri
        const foamGeo = new THREE.CylinderGeometry(potRadiusTop * 0.95, potRadiusTop * 0.95, 0.15, 32);
        const foamMat = new THREE.MeshStandardMaterial({ color: 0x1b4332, roughness: 0.9 });
        const foamMesh = new THREE.Mesh(foamGeo, foamMat);
        foamMesh.position.y = potHeight - 0.02;
        foamMesh.receiveShadow = true;
        potGroup.add(foamMesh);

        // Vazo Ağzına Oturan Yeşillik Çelengi / Sarkan Yapraklar (Referans Tarzı)
        if (textures.leaves_collar) {
            const collarMat = new THREE.MeshBasicMaterial({
                map: textures.leaves_collar,
                transparent: true,
                opacity: 0.9,
                depthWrite: false,
                side: THREE.DoubleSide
            });
            const collarGeo = new THREE.PlaneGeometry(3.0, 1.5);
            const collarMesh = new THREE.Mesh(collarGeo, collarMat);
            collarMesh.rotation.x = -Math.PI * 0.35;
            collarMesh.position.set(0, potHeight + 0.05, 0.15);
            potGroup.add(collarMesh);
        }
    }

    /**
     * 3. 12 Yuvalı Boş Hedef Rozetleri
     */
    function setupDomeSlotMarkers() {
        DOME_SLOTS.forEach(cfg => {
            const markerGroup = new THREE.Group();
            markerGroup.position.set(cfg.x, cfg.y, cfg.z);

            // Parlayan Küçük 3D Halka
            const ringGeo = new THREE.RingGeometry(0.12, 0.18, 24);
            const ringMat = new THREE.MeshBasicMaterial({
                color: 0xf43f5e,
                side: THREE.DoubleSide,
                transparent: true,
                opacity: 0.75
            });
            const ringMesh = new THREE.Mesh(ringGeo, ringMat);
            markerGroup.add(ringMesh);

            // Numaralı Yuva İpuçları
            const numCanvas = document.createElement('canvas');
            numCanvas.width = 64;
            numCanvas.height = 64;
            const nCtx = numCanvas.getContext('2d');
            nCtx.fillStyle = 'rgba(244, 63, 94, 0.9)';
            nCtx.beginPath();
            nCtx.arc(32, 32, 28, 0, Math.PI * 2);
            nCtx.fill();
            nCtx.fillStyle = '#ffffff';
            nCtx.font = 'bold 30px sans-serif';
            nCtx.textAlign = 'center';
            nCtx.textBaseline = 'middle';
            nCtx.fillText(cfg.id.toString(), 32, 34);

            const numTex = new THREE.CanvasTexture(numCanvas);
            const numMat = new THREE.SpriteMaterial({ map: numTex, depthTest: false });
            const numSprite = new THREE.Sprite(numMat);
            numSprite.scale.set(0.32, 0.32, 1);
            markerGroup.add(numSprite);

            markerGroup.userData = { isSlotMarker: true, slotId: cfg.id };
            slotsMarkerGroup.add(markerGroup);
            markerObjects[cfg.id] = markerGroup;
        });
    }

    /**
     * 4. HİBRİT 3D ÇİÇEK BAŞI OLUŞTURUCU (Photographic Impostor)
     * Gerçek fotoğraf dokusunu 3D uzayda kameraya bakan çift açılı hacimli yüzeye dönüştürür.
     */
    function createFlowerHead(materialData) {
        const group = new THREE.Group();
        const nameLower = (materialData.name || '').toLowerCase();

        let texKey = 'red_rose';
        let bloomW = 0.95;
        let bloomH = 0.95;

        if (nameLower.includes('santini') || nameLower.includes('ponpon') || nameLower.includes('krizantem')) {
            texKey = 'green_santini';
            bloomW = 0.75;
            bloomH = 0.75;
        } else if (nameLower.includes('portakal') || nameLower.includes('tarçın')) {
            texKey = 'dried_orange';
            bloomW = 0.85;
            bloomH = 0.85;
        } else if (nameLower.includes('papatya')) {
            texKey = 'daisy';
            bloomW = 0.90;
            bloomH = 0.90;
        } else if (nameLower.includes('lale')) {
            texKey = 'yellow_tulip';
            bloomW = 0.88;
            bloomH = 0.95;
        } else if (nameLower.includes('beyaz') && nameLower.includes('gül')) {
            texKey = 'white_rose';
        } else if (nameLower.includes('pembe') || nameLower.includes('pudra')) {
            texKey = 'pink_rose';
        }

        const texture = textures[texKey] || textures.red_rose;

        // Çiçek Başı Materyali (Çift taraflı, şeffaf, derinlik yazımlı)
        const flowerMat = new THREE.MeshStandardMaterial({
            map: texture,
            transparent: true,
            alphaTest: 0.08,
            roughness: 0.55,
            metalness: 0.02,
            side: THREE.DoubleSide
        });

        // 🌟 3 Açılı Çapraz Düzlemler (0°, 60°, -60° = 360° Her Açıdan Dolgun Hacim)
        const planeGeo = new THREE.PlaneGeometry(bloomW, bloomH);

        const plane1 = new THREE.Mesh(planeGeo, flowerMat);
        plane1.castShadow = true;
        group.add(plane1);

        const plane2 = new THREE.Mesh(planeGeo, flowerMat);
        plane2.rotation.y = Math.PI / 3;
        plane2.scale.set(0.96, 0.96, 0.96);
        group.add(plane2);

        const plane3 = new THREE.Mesh(planeGeo, flowerMat);
        plane3.rotation.y = -Math.PI / 3;
        plane3.scale.set(0.96, 0.96, 0.96);
        group.add(plane3);

        // 🌟 Üst Taç Yüzeyi (90° Kuşbakışı Görünümde Çiçeğin Yüzünü Gösteren Katman)
        const topGeo = new THREE.PlaneGeometry(bloomW * 0.85, bloomH * 0.85);
        const topPlane = new THREE.Mesh(topGeo, flowerMat);
        topPlane.rotation.x = -Math.PI / 2;
        topPlane.position.y = 0.12;
        group.add(topPlane);

        // Çiçeğin altına saksıya bağlanan küçük botanik çanak/sap boğumu
        const calyxMat = new THREE.MeshStandardMaterial({ color: 0x2d6a4f, roughness: 0.6 });
        const calyxGeo = new THREE.CylinderGeometry(0.04, 0.02, 0.35, 8);
        const calyxMesh = new THREE.Mesh(calyxGeo, calyxMat);
        calyxMesh.position.y = -0.32;
        group.add(calyxMesh);

        return group;
    }

    /**
     * 5. Yuvaya Çiçek Ekleme / Güncelleme
     */
    function updateSlot(slotId, materialData, heightLevel = 'medium') {
        const cfg = DOME_SLOTS.find(c => c.id === parseInt(slotId));
        if (!cfg) return;

        // Eski çiçeği kaldır
        if (slotObjects[slotId]) {
            domeGroup.remove(slotObjects[slotId]);
            delete slotObjects[slotId];
        }

        // Eğer yuva boşaltıldıysa
        if (!materialData) {
            if (markerObjects[slotId]) markerObjects[slotId].visible = true;
            return;
        }

        // Yeni çiçek oluştur
        const flowerObj = createFlowerHead(materialData);

        // Yükseklik Ofseti (Orta: 0, Uzun: +0.22, Kısa: -0.18)
        let hOffset = 0;
        if (heightLevel === 'tall') hOffset = 0.22;
        if (heightLevel === 'short') hOffset = -0.18;

        const posX = cfg.x;
        const posY = cfg.y + hOffset;
        const posZ = cfg.z;

        flowerObj.position.set(posX, posY, posZ);
        flowerObj.scale.set(cfg.scale, cfg.scale, cfg.scale);

        // Çiçek başını kubbe merkezinden dışarıya doğru zarifçe aç
        const angleY = Math.atan2(cfg.x, cfg.z);
        flowerObj.rotation.y = angleY;
        flowerObj.rotation.x = 0.18; // Hafif yukarı/ışığa bakış

        flowerObj.userData = {
            slotId: parseInt(slotId),
            isFlower: true,
            materialData: materialData,
            heightLevel: heightLevel,
            baseY: cfg.y
        };

        domeGroup.add(flowerObj);
        slotObjects[slotId] = flowerObj;

        // Dolu yuva rozetini gizle
        if (markerObjects[slotId]) markerObjects[slotId].visible = false;
    }

    /**
     * 6. Çiçeğe Tıklayınca 3D Boy Animasyonu (Spring Yaylanma)
     */
    function animateSlotHeight(slotId, newHeight) {
        const obj = slotObjects[slotId];
        if (!obj) return;

        let hOffset = 0;
        if (newHeight === 'tall') hOffset = 0.22;
        if (newHeight === 'short') hOffset = -0.18;

        const targetY = (obj.userData.baseY || 2.0) + hOffset;
        const startY = obj.position.y;
        const startTime = performance.now();
        const duration = 300;

        function step(now) {
            const elapsed = now - startTime;
            const progress = Math.min(elapsed / duration, 1);
            // Yaylanma (easeOutBack)
            const c1 = 1.70158;
            const c3 = c1 + 1;
            const ease = 1 + c3 * Math.pow(progress - 1, 3) + c1 * Math.pow(progress - 1, 2);

            obj.position.y = startY + (targetY - startY) * ease;

            if (progress < 1) {
                requestAnimationFrame(step);
            }
        }
        requestAnimationFrame(step);
    }

    /**
     * 7. Kamera Önayarları (45° Stüdyo, 90° Kuşbakışı, Ön Cephe)
     */
    function setCameraPreset(preset) {
        if (!controls || !camera) return;

        let targetPos = new THREE.Vector3(0, 2.6, 6.2);
        let targetLook = new THREE.Vector3(0, 1.35, 0);

        if (preset === 'topdown') {
            // 90° Kuşbakışı Oasis Kubbe Planı (Taşmayan, ferah kadraj)
            targetPos = new THREE.Vector3(0, 6.6, 0.01);
            targetLook = new THREE.Vector3(0, 1.4, 0);
        } else if (preset === 'front') {
            // Ön Göz Hizası
            targetPos = new THREE.Vector3(0, 1.5, 6.0);
            targetLook = new THREE.Vector3(0, 1.35, 0);
        }

        const startPos = camera.position.clone();
        const startLook = controls.target.clone();
        const startTime = performance.now();
        const duration = 450;

        function stepCam(now) {
            const elapsed = now - startTime;
            const p = Math.min(elapsed / duration, 1);
            const ease = p * (2 - p);

            camera.position.lerpVectors(startPos, targetPos, ease);
            controls.target.lerpVectors(startLook, targetLook, ease);
            controls.update();

            if (p < 1) requestAnimationFrame(stepCam);
        }
        requestAnimationFrame(stepCam);
    }

    /**
     * 8. Fare & Dokunmatik Etkileşimler
     */
    function setupEventListeners() {
        canvas.addEventListener('click', onPointerClick);
        canvas.addEventListener('pointermove', onPointerMove);

        canvas.addEventListener('dragover', (e) => {
            e.preventDefault();
            const slot = getIntersectedSlot(e);
            if (slot && callbacks.onSlotHover) {
                callbacks.onSlotHover(slot.id);
            }
        });

        canvas.addEventListener('drop', (e) => {
            e.preventDefault();
            const slot = getIntersectedSlot(e);
            if (slot && callbacks.onSlotDrop) {
                callbacks.onSlotDrop(slot.id, e);
            }
        });
    }

    function updateMousePos(e) {
        const rect = canvas.getBoundingClientRect();
        mouse.x = ((e.clientX - rect.left) / rect.width) * 2 - 1;
        mouse.y = -((e.clientY - rect.top) / rect.height) * 2 + 1;
    }

    function onPointerClick(e) {
        updateMousePos(e);
        raycaster.setFromCamera(mouse, camera);

        // 1. Çiçek Tıklaması
        const flowerHits = raycaster.intersectObjects(domeGroup.children, true);
        if (flowerHits.length > 0) {
            let obj = flowerHits[0].object;
            while (obj.parent && obj.parent !== domeGroup) obj = obj.parent;
            if (obj.userData && obj.userData.slotId) {
                if (callbacks.onFlowerClick) {
                    callbacks.onFlowerClick(obj.userData.slotId);
                }
                return;
            }
        }

        // 2. Boş Yuva Rozeti Tıklaması
        const markerHits = raycaster.intersectObjects(slotsMarkerGroup.children, true);
        if (markerHits.length > 0) {
            let mObj = markerHits[0].object;
            while (mObj.parent && mObj.parent !== slotsMarkerGroup) mObj = mObj.parent;
            if (mObj.userData && mObj.userData.slotId) {
                if (callbacks.onSlotClick) {
                    callbacks.onSlotClick(mObj.userData.slotId);
                }
            }
        }
    }

    function onPointerMove(e) {
        updateMousePos(e);
        raycaster.setFromCamera(mouse, camera);

        const slot = getIntersectedSlot(e);
        if (slot) {
            canvas.style.cursor = 'pointer';
            if (hoveredSlotId !== slot.id) {
                hoveredSlotId = slot.id;
                if (callbacks.onSlotHover) callbacks.onSlotHover(slot.id);
            }
        } else {
            canvas.style.cursor = 'grab';
            if (hoveredSlotId !== null) {
                hoveredSlotId = null;
                if (callbacks.onSlotHover) callbacks.onSlotHover(null);
            }
        }
    }

    function getIntersectedSlot(e) {
        updateMousePos(e);
        raycaster.setFromCamera(mouse, camera);

        // Çiçek
        const flowerHits = raycaster.intersectObjects(domeGroup.children, true);
        if (flowerHits.length > 0) {
            let obj = flowerHits[0].object;
            while (obj.parent && obj.parent !== domeGroup) obj = obj.parent;
            if (obj.userData && obj.userData.slotId) return { id: obj.userData.slotId };
        }

        // Yuva rozeti
        const markerHits = raycaster.intersectObjects(slotsMarkerGroup.children, true);
        if (markerHits.length > 0) {
            let mObj = markerHits[0].object;
            while (mObj.parent && mObj.parent !== slotsMarkerGroup) mObj = mObj.parent;
            if (mObj.userData && mObj.userData.slotId) return { id: mObj.userData.slotId };
        }

        return null;
    }

    function onWindowResize() {
        if (!canvasContainer || !renderer || !camera) return;
        const width = canvasContainer.clientWidth;
        const height = canvasContainer.clientHeight;
        camera.aspect = width / height;
        camera.updateProjectionMatrix();
        renderer.setSize(width, height);
    }

    function animate() {
        animationFrameId = requestAnimationFrame(animate);
        if (controls) controls.update();
        if (renderer && scene && camera) renderer.render(scene, camera);
    }

    function destroy() {
        if (animationFrameId) cancelAnimationFrame(animationFrameId);
        window.removeEventListener('resize', onWindowResize);
        if (renderer) renderer.dispose();
    }

    return {
        init,
        createVase,
        updateSlot,
        animateSlotHeight,
        setCameraPreset,
        destroy
    };

})();

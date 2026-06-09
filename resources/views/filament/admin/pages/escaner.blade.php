<x-filament-panels::page class="!p-0 !max-w-full">
    <style>
        @media (max-width: 768px) and (orientation: portrait) {
            .scanner-shell {
                padding: 12px !important;
            }

            .scanner-hero {
                padding: 18px !important;
            }

            .scanner-grid {
                grid-template-columns: 1fr !important;
            }

            .scanner-camera-header {
                align-items: flex-start !important;
            }

            .scanner-camera-actions {
                width: 100%;
            }

            .scanner-camera-actions button {
                flex: 1 1 0;
            }

            .scanner-preview {
                aspect-ratio: 3 / 4 !important;
            }

            .scanner-qr-frame {
                width: 200px !important;
                height: 200px !important;
            }

            .scanner-side-card,
            .scanner-tip {
                padding: 16px !important;
            }

            .scanner-title {
                font-size: 26px !important;
            }

            .scanner-message {
                width: 100%;
            }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
        window.scannerPage = function () {
            return {
                html5QrCode: null,
                scanning: false,
                status: 'Listo',
                message: @js($mensaje),
                lastCode: '',
                manualCode: '',

                init() {
                    if (!window.Html5Qrcode) {
                        this.status = 'html5-qrcode no disponible';
                        this.message = 'No se pudo cargar html5-qrcode';
                        return;
                    }

                    setTimeout(() => {
                        this.start();
                    }, 250);
                },

                async start() {
                    if (this.scanning || !window.Html5Qrcode) {
                        return;
                    }

                    this.status = 'Solicitando cámara...';
                    this.message = 'Solicitando cámara...';

                    if (!this.html5QrCode) {
                        this.html5QrCode = new Html5Qrcode('qr-reader');
                    }

                    try {
                        await this.html5QrCode.start(
                            { facingMode: 'environment' },
                            {
                                fps: 10,
                                qrbox: { width: 250, height: 250 },
                                aspectRatio: 1.3333333,
                                disableFlip: false,
                            },
                            async (decodedText) => {
                                const code = String(decodedText || '').trim();

                                if (!code) {
                                    return;
                                }

                                this.lastCode = code;
                                this.status = 'Código detectado';
                                this.message = 'Redirigiendo...';

                                await this.stop();
                                await this.$wire.resolverCodigo(code);
                            },
                            () => {}
                        );

                        this.scanning = true;
                        this.status = 'Escaneando...';
                        this.message = 'Escaneando...';
                    } catch (error) {
                        console.error(error);
                        this.status = 'No fue posible abrir la cámara';
                        this.message = 'No fue posible abrir la cámara';
                    }
                },

                async stop() {
                    if (!this.html5QrCode) {
                        this.scanning = false;
                        this.status = 'Detenido';
                        return;
                    }

                    try {
                        if (this.scanning) {
                            await this.html5QrCode.stop();
                        }
                    } catch (error) {
                        console.warn(error);
                    }

                    try {
                        await this.html5QrCode.clear();
                    } catch (error) {
                        console.warn(error);
                    }

                    this.scanning = false;
                    this.status = 'Detenido';
                },

                async submitManual() {
                    const code = String(this.manualCode || '').trim();

                    if (!code) {
                        this.status = 'Escribe un código primero';
                        this.message = 'Escribe un código primero';
                        return;
                    }

                    this.lastCode = code;
                    this.status = 'Buscando...';
                    this.message = 'Buscando código...';
                    await this.$wire.resolverCodigo(code);
                },
            };
        };
    </script>

    <div
        x-data="scannerPage()"
        x-init="init()"
        class="scanner-shell"
        style="min-height:80vh; background:#0f172a; color:#fff; padding:24px;"
    >
        <div style="max-width:1200px; margin:0 auto;">
            <div
                class="scanner-hero"
                style="
                    margin-bottom:24px;
                    border:1px solid rgba(255,255,255,.08);
                    border-radius:24px;
                    padding:24px;
                    background:linear-gradient(135deg, #111827, #1e293b);
                    box-shadow:0 20px 50px rgba(0,0,0,.35);
                "
            >
                <div style="display:flex; justify-content:space-between; gap:16px; flex-wrap:wrap; align-items:flex-start;">
                    <div>
                        <div style="font-size:32px; font-weight:800; line-height:1;">Escáner</div>
                        <div style="margin-top:8px; color:#cbd5e1; max-width:760px; line-height:1.5;">
                            Usa la cámara del dispositivo para leer el QR del ticket o de la cuenta y abrirla de inmediato.
                        </div>
                    </div>

                    <div class="scanner-message" style="padding:12px 16px; border-radius:16px; border:1px solid rgba(34,211,238,.25); background:rgba(34,211,238,.10); color:#a5f3fc; font-weight:700;">
                        <span x-text="message"></span>
                    </div>
                </div>
            </div>

            <div class="scanner-grid" style="display:grid; grid-template-columns: 1.4fr .9fr; gap:24px;">
                <div style="border-radius:24px; overflow:hidden; border:1px solid rgba(255,255,255,.08); background:#000; box-shadow:0 20px 50px rgba(0,0,0,.35);">
                    <div class="scanner-camera-header" style="display:flex; justify-content:space-between; align-items:center; gap:16px; padding:18px 20px; border-bottom:1px solid rgba(255,255,255,.08);">
                        <div>
                            <div style="font-size:12px; letter-spacing:.2em; text-transform:uppercase; color:#94a3b8; font-weight:700;">Cámara</div>
                            <div style="font-size:20px; font-weight:800;">Apunta al QR</div>
                        </div>

                        <div class="scanner-camera-actions" style="display:flex; gap:10px; flex-wrap:wrap;">
                            <button type="button" @click="stop()" style="border:none; border-radius:999px; padding:10px 16px; background:#334155; color:#fff; font-weight:800; cursor:pointer;">Detener</button>
                        </div>
                    </div>

                    <div class="scanner-preview" style="position:relative; aspect-ratio:4/3; background:#0f172a;">
                        <div wire:ignore id="qr-reader" style="width:100%; height:100%;"></div>
                        <div style="pointer-events:none; position:absolute; inset:0; display:flex; align-items:center; justify-content:center;">
                            <div class="scanner-qr-frame" style="width:260px; height:260px; border-radius:28px; border:2px solid rgba(34,211,238,.85); box-shadow:0 0 0 999px rgba(15,23,42,.35);"></div>
                        </div>
                        <div style="position:absolute; left:16px; top:16px; padding:8px 12px; border-radius:999px; background:rgba(0,0,0,.6); color:#a5f3fc; font-size:12px; font-weight:800; letter-spacing:.14em; text-transform:uppercase;">
                            <span x-text="status"></span>
                        </div>
                        <div style="position:absolute; left:16px; right:16px; bottom:16px; padding:12px 14px; border-radius:18px; background:rgba(0,0,0,.6); color:#e2e8f0; font-size:14px;">
                            <strong style="color:#fff;">Último código:</strong>
                            <span x-text="lastCode || 'Sin lectura aún'"></span>
                        </div>
                    </div>
                </div>

                <div style="display:grid; gap:20px;">
                    <div class="scanner-side-card" style="border-radius:24px; border:1px solid rgba(255,255,255,.08); background:#111827; padding:20px;">
                        <div style="font-size:12px; letter-spacing:.2em; text-transform:uppercase; color:#94a3b8; font-weight:700;">Ayuda</div>
                        <div style="margin-top:14px; color:#cbd5e1; font-size:14px; line-height:1.8;">
                            <div>1. Activa la cámara y permite el acceso.</div>
                            <div>2. Enmarca el QR dentro del cuadro central.</div>
                            <div>3. Si el QR pertenece a una cuenta, te lleva a editarla.</div>
                            <div>4. Si pertenece a un ticket, intenta llevarte primero a su cuenta.</div>
                        </div>
                    </div>

                    <div class="scanner-side-card" style="border-radius:24px; border:1px solid rgba(255,255,255,.08); background:#111827; padding:20px;">
                        <div style="font-size:12px; letter-spacing:.2em; text-transform:uppercase; color:#94a3b8; font-weight:700;">Manual</div>
                        <div style="margin-top:14px;">
                            <label style="display:block; margin-bottom:8px; font-size:14px; font-weight:700;">Código manual</label>
                            <div style="display:flex; gap:10px;">
                                <input
                                    x-model="manualCode"
                                    type="text"
                                    placeholder="Escribe el código"
                                    style="flex:1; border-radius:16px; border:1px solid rgba(255,255,255,.10); background:#020617; color:#fff; padding:12px 14px; outline:none;"
                                >
                                <button type="button" @click="submitManual()" style="border:none; border-radius:16px; padding:12px 16px; background:#06b6d4; color:#fff; font-weight:800; cursor:pointer;">Ir</button>
                            </div>
                        </div>
                    </div>

                    <div class="scanner-tip" style="border-radius:24px; border:1px solid rgba(251,191,36,.20); background:rgba(251,191,36,.10); padding:20px; color:#fef3c7; font-size:14px; line-height:1.7;">
                        <div style="font-weight:800; margin-bottom:4px;">Tip</div>
                        Funciona mejor en Chrome o Chromium sobre dispositivos con cámara trasera.
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>

<x-filament-widgets::widget>
    <x-filament::section>
        <div style="display:flex; justify-content:space-between; align-items:center; gap:16px; margin-bottom:18px; flex-wrap:wrap;">
            <div>
                <h2 style="font-size:22px; font-weight:800; margin:0;">
                    Accesos rápidos
                </h2>

                <p style="margin:6px 0 0 0; color:#9ca3af; font-size:14px;">
                    Inicia una venta rápidamente según el tipo de servicio.
                </p>
            </div>
        </div>

        <div
            style="
                display:grid;
                grid-template-columns:repeat(3, minmax(0, 1fr));
                gap:16px;
            "
            class="ventas-rapidas-grid"
        >
            @foreach ($this->getAccesos() as $acceso)
                <a href="{{ $acceso['url'] }}"
                    style="
                        display:block;
                        text-decoration:none;
                        background:linear-gradient(135deg, {{ $acceso['color'] }}, #111827);
                        border-radius:20px;
                        padding:22px;
                        color:white;
                        min-height:150px;
                        box-shadow:0 14px 30px rgba(0,0,0,.25);
                        border:1px solid rgba(255,255,255,.12);
                    ">
                    <div style="font-size:34px; margin-bottom:14px;">
                        {{ $acceso['icono'] }}
                    </div>

                    <div style="font-size:20px; font-weight:800; margin-bottom:8px;">
                        {{ $acceso['titulo'] }}
                    </div>

                    <div style="font-size:14px; color:rgba(255,255,255,.82); line-height:1.4;">
                        {{ $acceso['descripcion'] }}
                    </div>

                    <div style="margin-top:18px; font-size:13px; font-weight:700;">
                        Entrar →
                    </div>
                </a>
            @endforeach
        </div>

        <style>
            @media (max-width: 900px) {
                .ventas-rapidas-grid {
                    grid-template-columns: 1fr !important;
                }
            }
        </style>
    </x-filament::section>
</x-filament-widgets::widget>
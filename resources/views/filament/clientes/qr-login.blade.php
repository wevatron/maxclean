<div class="w-full flex flex-col items-center justify-center text-center gap-4">

    {{-- QR --}}
    <div class="bg-white p-4 rounded-xl shadow flex items-center justify-center">
        {!! QrCode::size(220)->generate($url) !!}
    </div>

    {{-- Texto --}}
    <p class="text-sm text-gray-600">
        Escanea este código desde tu celular
    </p>

    {{-- Expiración --}}
    <p class="text-xs text-warning-600 font-semibold">
        ⏳ Expira a las {{ $expiresAt->timezone(config('app.timezone'))->format('H:i') }}
    </p>

    {{-- Link copiable --}}
    <div class="w-full max-w-md">
        <label class="block text-xs text-gray-500 mb-1">
            Enlace directo
        </label>

        <div class="flex items-center gap-2">
            <input
                type="text"
                readonly
                style="width: 500px"
                value="{{ $url }}"
                class="w-full text-xs bg-gray-100 border rounded-md px-2 py-1 select-all"
            />

            <button
                type="button"
                onclick="navigator.clipboard.writeText('{{ $url }}')"
                class="px-2 py-1 text-xs bg-primary-600 text-white rounded-md hover:bg-primary-700"
            >
                Copiar
            </button>
        </div>

        <p class="text-[11px] text-gray-400 mt-1">
            Puedes enviar este enlace por WhatsApp o usarlo para pruebas locales
        </p>
    </div>

    {{-- Nota de seguridad --}}
    <p class="text-[11px] text-gray-400 mt-2">
        Este acceso es temporal y de un solo uso
    </p>
</div>

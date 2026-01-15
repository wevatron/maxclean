<x-filament::card>
    <div class="flex items-center justify-between gap-4">

        <div>
            {{-- TÍTULO --}}
            <h3 class="text-lg font-semibold">
                Mis puntos
            </h3>

            {{-- SUBTÍTULO --}}
            <p class="text-sm text-gray-500">
                Programa de lealtad
            </p>
        </div>

        <x-filament::button
            color="primary"
            icon="heroicon-o-star"
            tag="a"
            href="{{ route('filament.cliente.resources.puntos.index') }}"
        >
            Ver
        </x-filament::button>

    </div>
</x-filament::card>

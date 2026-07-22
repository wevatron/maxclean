<x-filament-panels::page class="!p-0 !max-w-full">
    <div style="min-height:100vh; padding:24px; background:linear-gradient(180deg, #f8fafc 0%, #eef2ff 100%);">
        <div style="max-width:1200px; margin:0 auto;">
            <div style="margin-bottom:20px;">
                <div style="font-size:13px; font-weight:800; letter-spacing:.12em; text-transform:uppercase; color:#2563eb;">
                    Gestión de clientes
                </div>
                <div style="font-size:32px; font-weight:900; color:#0f172a; line-height:1.1; margin-top:6px;">
                    Fusionar clientes duplicados
                </div>
                <div style="font-size:15px; color:#475569; margin-top:8px; max-width:760px;">
                    Busca el cliente principal y el duplicado. Todo el histórico del segundo se moverá al primero: tickets, cuentas, pagos y puntos.
                </div>
            </div>

            <div style="display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:16px; margin-bottom:18px;">
                <div style="padding:18px; border-radius:20px; background:white; border:1px solid #dbeafe; box-shadow:0 12px 30px rgba(15,23,42,.06);">
                    <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:14px;">
                        <div>
                            <div style="font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:.12em; color:#2563eb;">
                                Cliente principal
                            </div>
                            <div style="font-size:18px; font-weight:800; color:#0f172a;">
                                Se quedará con el histórico
                            </div>
                        </div>

                        @if ($clientePrincipalId)
                            <button type="button" wire:click="limpiarPrincipal"
                                style="padding:10px 12px; border:none; border-radius:12px; background:#ef4444; color:white; font-weight:800;">
                                Limpiar
                            </button>
                        @endif
                    </div>

                    <input type="text" wire:model.live.debounce.300ms="buscarPrincipal"
                        placeholder="Buscar por nombre, correo o whatsapp..."
                        style="width:100%; padding:14px; border-radius:14px; border:1px solid #cbd5e1; font-size:15px; color:#111827; background:white;"
                        class="placeholder:text-slate-400">

                    @if (!empty($resultadosPrincipal))
                        <div style="margin-top:10px; border:1px solid #e2e8f0; border-radius:14px; overflow:hidden;">
                            @foreach ($resultadosPrincipal as $cliente)
                                <button type="button" wire:click="seleccionarPrincipal({{ $cliente['id'] }})"
                                    style="width:100%; text-align:left; padding:12px 14px; border:none; background:white; border-bottom:1px solid #f1f5f9; cursor:pointer;">
                                    <div style="display:flex; justify-content:space-between; gap:12px; align-items:flex-start;">
                                        <div>
                                            <div style="font-weight:800; color:#0f172a;">
                                                {{ $cliente['name'] }}
                                            </div>
                                            <div style="font-size:13px; color:#64748b;">
                                                {{ $cliente['email'] ?: 'Sin correo' }} · {{ $cliente['whatsapp'] ?: 'Sin teléfono' }}
                                            </div>
                                        </div>

                                        @if ($cliente['trashed'])
                                            <span style="padding:5px 8px; border-radius:999px; background:#fef3c7; color:#92400e; font-size:11px; font-weight:800;">
                                                Eliminado
                                            </span>
                                        @endif
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    @endif

                    @if ($clientePrincipalId)
                        @php($resumenPrincipal = $this->resumenCliente($clientePrincipalId))
                        <div style="margin-top:14px; padding:14px; border-radius:16px; background:#eff6ff; border:1px solid #bfdbfe;">
                            <div style="font-weight:800; color:#0f172a;">{{ $clientePrincipalNombre }}</div>
                            <div style="font-size:13px; color:#64748b; margin-top:4px;">
                                {{ $this->buscarClientePorId($clientePrincipalId)?->email ?: 'Sin correo' }}
                                ·
                                {{ $this->buscarClientePorId($clientePrincipalId)?->whatsapp ?: 'Sin teléfono' }}
                            </div>
                            <div style="display:grid; grid-template-columns:repeat(4, minmax(0, 1fr)); gap:10px; margin-top:10px;">
                                <div>
                                    <div style="font-size:11px; color:#64748b; font-weight:700;">Tickets</div>
                                    <div style="font-size:18px; font-weight:900; color:#1d4ed8;">{{ $resumenPrincipal['tickets'] }}</div>
                                </div>
                                <div>
                                    <div style="font-size:11px; color:#64748b; font-weight:700;">Cuentas</div>
                                    <div style="font-size:18px; font-weight:900; color:#1d4ed8;">{{ $resumenPrincipal['cuentas'] }}</div>
                                </div>
                                <div>
                                    <div style="font-size:11px; color:#64748b; font-weight:700;">Pagos</div>
                                    <div style="font-size:18px; font-weight:900; color:#1d4ed8;">{{ $resumenPrincipal['pagos'] }}</div>
                                </div>
                                <div>
                                    <div style="font-size:11px; color:#64748b; font-weight:700;">Puntos</div>
                                    <div style="font-size:18px; font-weight:900; color:#1d4ed8;">{{ $resumenPrincipal['puntos'] }}</div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div style="padding:18px; border-radius:20px; background:white; border:1px solid #fee2e2; box-shadow:0 12px 30px rgba(15,23,42,.06);">
                    <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:14px;">
                        <div>
                            <div style="font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:.12em; color:#dc2626;">
                                Cliente duplicado
                            </div>
                            <div style="font-size:18px; font-weight:800; color:#0f172a;">
                                Se fusionará dentro del principal
                            </div>
                        </div>

                        @if ($clienteSecundarioId)
                            <button type="button" wire:click="limpiarSecundario"
                                style="padding:10px 12px; border:none; border-radius:12px; background:#ef4444; color:white; font-weight:800;">
                                Limpiar
                            </button>
                        @endif
                    </div>

                    <input type="text" wire:model.live.debounce.300ms="buscarSecundario"
                        placeholder="Buscar por nombre, correo o whatsapp..."
                        style="width:100%; padding:14px; border-radius:14px; border:1px solid #cbd5e1; font-size:15px; color:#111827; background:white;"
                        class="placeholder:text-slate-400">

                    @if (!empty($resultadosSecundario))
                        <div style="margin-top:10px; border:1px solid #e2e8f0; border-radius:14px; overflow:hidden;">
                            @foreach ($resultadosSecundario as $cliente)
                                <button type="button" wire:click="seleccionarSecundario({{ $cliente['id'] }})"
                                    style="width:100%; text-align:left; padding:12px 14px; border:none; background:white; border-bottom:1px solid #f1f5f9; cursor:pointer;">
                                    <div style="display:flex; justify-content:space-between; gap:12px; align-items:flex-start;">
                                        <div>
                                            <div style="font-weight:800; color:#0f172a;">
                                                {{ $cliente['name'] }}
                                            </div>
                                            <div style="font-size:13px; color:#64748b;">
                                                {{ $cliente['email'] ?: 'Sin correo' }} · {{ $cliente['whatsapp'] ?: 'Sin teléfono' }}
                                            </div>
                                        </div>

                                        @if ($cliente['trashed'])
                                            <span style="padding:5px 8px; border-radius:999px; background:#fef3c7; color:#92400e; font-size:11px; font-weight:800;">
                                                Eliminado
                                            </span>
                                        @endif
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    @endif

                    @if ($clienteSecundarioId)
                        @php($resumenSecundario = $this->resumenCliente($clienteSecundarioId))
                        <div style="margin-top:14px; padding:14px; border-radius:16px; background:#fff7ed; border:1px solid #fed7aa;">
                            <div style="font-weight:800; color:#0f172a;">{{ $clienteSecundarioNombre }}</div>
                            <div style="font-size:13px; color:#64748b; margin-top:4px;">
                                {{ $this->buscarClientePorId($clienteSecundarioId)?->email ?: 'Sin correo' }}
                                ·
                                {{ $this->buscarClientePorId($clienteSecundarioId)?->whatsapp ?: 'Sin teléfono' }}
                            </div>
                            <div style="display:grid; grid-template-columns:repeat(4, minmax(0, 1fr)); gap:10px; margin-top:10px;">
                                <div>
                                    <div style="font-size:11px; color:#64748b; font-weight:700;">Tickets</div>
                                    <div style="font-size:18px; font-weight:900; color:#c2410c;">{{ $resumenSecundario['tickets'] }}</div>
                                </div>
                                <div>
                                    <div style="font-size:11px; color:#64748b; font-weight:700;">Cuentas</div>
                                    <div style="font-size:18px; font-weight:900; color:#c2410c;">{{ $resumenSecundario['cuentas'] }}</div>
                                </div>
                                <div>
                                    <div style="font-size:11px; color:#64748b; font-weight:700;">Pagos</div>
                                    <div style="font-size:18px; font-weight:900; color:#c2410c;">{{ $resumenSecundario['pagos'] }}</div>
                                </div>
                                <div>
                                    <div style="font-size:11px; color:#64748b; font-weight:700;">Puntos</div>
                                    <div style="font-size:18px; font-weight:900; color:#c2410c;">{{ $resumenSecundario['puntos'] }}</div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div style="padding:18px; border-radius:20px; background:white; border:1px solid #cbd5e1; box-shadow:0 12px 30px rgba(15,23,42,.06);">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:16px; flex-wrap:wrap;">
                    <div style="max-width:760px;">
                        <div style="font-size:14px; font-weight:800; text-transform:uppercase; letter-spacing:.12em; color:#475569;">
                            Confirmación
                        </div>
                        <div style="font-size:18px; font-weight:800; color:#0f172a; margin-top:6px;">
                            Antes de fusionar, revisa que el cliente principal sea el correcto.
                        </div>
                        <div style="font-size:14px; color:#475569; margin-top:6px;">
                            El duplicado se moverá al principal y luego se eliminará del listado activo.
                        </div>
                    </div>
                </div>

                <div style="margin-top:16px;">
                    <label
                        style="
                            display:flex;
                            gap:12px;
                            align-items:flex-start;
                            padding:14px 16px;
                            border-radius:16px;
                            border:1px solid #cbd5e1;
                            background:#f8fafc;
                            cursor:pointer;
                        ">
                        <input type="checkbox" wire:model.live="confirmarFusion"
                            style="width:20px; height:20px; margin-top:2px; accent-color:#2563eb;">

                        <div>
                            <div style="font-weight:800; color:#0f172a;">
                                Confirmo que quiero fusionar estos dos clientes
                            </div>
                            <div style="font-size:13px; color:#64748b; margin-top:4px;">
                                Se transferirán tickets, cuentas, pagos y puntos al cliente principal.
                            </div>
                        </div>
                    </label>
                </div>

                <div style="display:flex; justify-content:flex-end; margin-top:16px;">
                    <button type="button" wire:click="fusionarClientes" wire:loading.attr="disabled"
                        style="
                            padding:14px 18px;
                            border:none;
                            border-radius:14px;
                            background:#dc2626;
                            color:white;
                            font-size:16px;
                            font-weight:900;
                            cursor:pointer;
                        ">
                        Fusionar clientes
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>

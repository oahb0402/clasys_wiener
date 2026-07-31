@extends('layouts.app')

@section('title', 'Clasys v3 - Panel de Gestión')

@section('content')

    <div class="min-h-full p-6 max-w-7xl mx-auto space-y-6">

        <div class="max-w-6xl mx-auto space-y-4">
            @include('crm.partials._barra-estado')
            @include('crm.partials._cliente-info')
            @include('crm.partials._tabla-recibos')
            @include('crm.partials._panel-gestion')
        </div>

        @include('crm.partials._modal-historial')
        @include('crm.partials._modal-modificar')

    </div>

    @include('crm.partials._drawer-acciones')
    @include('crm.partials._fab-boton')
    @include('crm.partials._modal-historial-interacciones')

@endsection

@push('scripts')
    @include('crm.partials._scripts')
@endpush

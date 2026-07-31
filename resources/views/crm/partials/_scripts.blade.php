{{-- Puente de datos Blade -> JS. El resto de la lógica vive en resources/js/crm/ --}}
<script>
    window.APP_CLIENTE_ID = {{ $cliente->cod_deu ?? 0 }};
</script>

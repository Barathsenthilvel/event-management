<style>
    @keyframes gnat-btn-spin {
        to { transform: rotate(360deg); }
    }
    .gnat-btn-spinner {
        width: 1.125em;
        height: 1.125em;
        flex-shrink: 0;
        animation: gnat-btn-spin 0.75s linear infinite;
    }
    button[aria-busy="true"],
    input[type="submit"][aria-busy="true"] {
        cursor: wait;
        pointer-events: none;
    }
    .gnat-submit-loading-wrap {
        width: 100%;
    }
</style>
<script src="{{ asset('js/gnat-submit-loading.js') }}" defer></script>

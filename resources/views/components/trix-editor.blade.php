@props(['value' => ''])

<div
    wire:ignore
    {{ $attributes }}
>
    <input id="{{ $attributes->get('id') }}" type="hidden" name="content" value="{{ $value }}">
    <trix-editor input="{{ $attributes->get('id') }}" class="form-control"></trix-editor>
</div>

<script>
    var trixEditor = document.getElementById("{{ $attributes->get('id') }}")
    addEventListener("trix-blur", function(event) {
        @this.set('{{ $attributes->get('wire:model') }}', trixEditor.getAttribute('value'))
    })
</script>
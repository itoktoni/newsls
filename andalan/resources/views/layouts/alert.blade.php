<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
<script>
    var toastr = new Notyf({
        duration: 5000,
        dismissible: true,
        position: {
            x: 'right',
            y: 'top',
        },
    });
</script>
@if ($errors->any())
<script type="text/javascript">
    @foreach($errors->all() as $error)
        @php
            $string = Str::of($error);
            $required = ' wajib diisi';
            if($string->contains($required)){
                $error = formatAttribute($string->before($required)).$required ?? $error;
            }
        @endphp
        toastr.error('{{ $error }}');
    @endforeach
</script>
@endif

@if(session()->has('success') && !request()->ajax())
<script type="text/javascript">
    toastr.success("{{ session()->get('success') }}");
</script>
@php
session()->forget('success');
@endphp
@endif

@if(session()->has('error') && !request()->ajax())
<script type="text/javascript">
    // cuteToast({
    //     type: "error",
    //     message: "{{ session()->get('error') }}",
    //     timer: 5000
    // });
    toastr.error("{{ session()->get('error') }}");
</script>
@php
session()->forget('error');
@endphp
@endif
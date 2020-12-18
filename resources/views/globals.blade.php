<script>
    window.Laravel = <?php echo json_encode([
        'csrfToken' => csrf_token(),
        'baseUrl' => url(''),
        'isGuest' => Auth::guard('users')->guest(),
    ]); ?>;

    @if (!Auth::guard('users')->guest())
        Laravel.user = {!! Auth::guard('users')->user() !!};
    @else
        Laravel.user = {};
    @endif

    Laravel.errors = {};

    Laravel.updateProfile = function (profile) {
        Object.assign(Laravel.user, profile);
    };
</script>

@foreach ($errors->keys() as $key)
    <script>
        Laravel.errors['{{ $key }}'] = '{{ $errors->first($key) }}';
    </script>
@endforeach

<script>
    Laravel.oldInputs = JSON.parse('{!! json_encode(old()) !!}');
</script>

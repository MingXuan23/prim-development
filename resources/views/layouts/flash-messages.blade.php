@if( Session::has("success") )
<div class="alert alert-success alert-block" role="alert">
    <button class="close" data-dismiss="alert"></button>
    {{ Session::get("success") }}
</div>
@endif

@if( Session::has("error") )
<div class="alert alert-danger alert-block" role="alert">
    <button class="close" data-dismiss="alert"></button>
    {{ Session::get("error") }}
</div>
@endif

<script>
    $('.alert').delay(3000).fadeOut();
</script>
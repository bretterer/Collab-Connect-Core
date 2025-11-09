<section class="py-8">
    @if(!empty($data['html']))
        {!! $data['html'] !!}
    @endif

    @if(!empty($data['css']))
        <style>{!! $data['css'] !!}</style>
    @endif

    @if(!empty($data['js']))
        <script>{!! $data['js'] !!}</script>
    @endif
</section>

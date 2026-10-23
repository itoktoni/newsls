<nav class="container-pagination">
    {!! $data->appends(\Request::except('page'))->render() !!}
</nav>
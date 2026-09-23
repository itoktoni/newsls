@yield('css')

<div class="modal-header" id="modal-header">
	@yield('title')
	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		<span aria-hidden="true">&times;</span>
	</button>
</div>

@yield('container')

<div class="modal-footer" id="modal-footer">
	@yield('action')
</div>

<script src="{{ url('assets/js/app.min.js') }}"></script>

@stack('javascript')
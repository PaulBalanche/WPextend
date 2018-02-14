<!-- content-wysiwyg -->
<section class="content-wysiwyg section"
	set-topbar-class="c-text"
	@if ($bkg_url)
		style="background-image:url({{ $bkg_url }})"
	@endif
>
	@if ($bkg_class)
		<div class="bkg bkg--{{ $bkg_class }}"></div>
	@endif
	<div class="container">
		<div class="row">
			<div class="gr-12">
			@if ($title)
				<h1 class="h1 m-b">
					{!! $title !!}
				</h1>
			@endif
			@if ($body)
				<div class="tf vr m-b">
					{!! $body !!}
				</div>
			@endif
		</div>
	</div>
</section><!-- end content-wysiwyg -->

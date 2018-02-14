<!-- logos-grid -->
<section class="logos-grid section"
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
			@if ($title)
				<div class="gr-12 m-b">
					<h1 class="h5 c-grey m-b" anim="letters-in">
						{{ $title }}
					</h1>
				</div>
			@endif
		</div>
		<div class="row row-align-middle" anim="slide-in-up">
		@foreach ($logos as $logo)
			<div class="gr-6 gr-4@tablet gr-1-5@desktop t-center">
				<div class="p p-medium@tablet">
					@if ($logo->link_url)
						<a href="{{ $logo->link_url }}" title="{{ t_esc_attr($logo->title) }}">
					@endif
					@if ($logo->media_url)
						<img lazy-src="{{ $logo->media_url }}" class="logos-grid__img" title="{{ t_esc_attr($logo->title) }}" />
					@endif
					@if ($logo->icon)
						<i class="icon-{{ $logo->icon }} logos-grid__icon"></i>
					@endif
					@if ($logo->link_url)
						</a>
					@endif
				</div>
			</div>
		@endforeach
		</div>
	</div>
</section><!-- end logos-grid -->

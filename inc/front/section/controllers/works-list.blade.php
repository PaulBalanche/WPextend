<!-- works-list -->
<section class="works-list section"
	set-topbar-class="c-text"
>
	@if ($bkg_class)
		<div class="bkg bkg--{{ $bkg_class }}"></div>
	@endif
	<div class="container">
		<div class="row">
			@if ($title)
				<div class="gr-12 m-b-bigger">
					<h1 class="h3 m-b" anim="letters-in">
						{{ $title }}
					</h1>
					@if ($body)
						<div class="vr tf" anim="slide-in-up">
							{!! $body !!}
						</div>
					@endif
				</div>
			@endif
		</div>
		@foreach($works as $work)
			<div class="row m-b-big">
				<div class="gr-12">
					<div class="works-list__work">
						<a href="{{ $work->url }}" title="{{ t_esc_attr($work->title) }}">
							<figure class="works-list__work-media" anim="slide-in-left">
								<img lazy-src="{{ $work->media_url }}" title="{{ t_esc_attr($work->title) }}" />
							</figure>
						</a>
						<div class="works-list__work-metas">
							<a href="{{ $work->url }}" title="{{ t_esc_attr($work->title) }}">
								<h1 class="works-list__work-title title-decoration m-b@tablet m-t no-m-t@tablet" anim="letters-in">
									{!! $work->title !!}
								</h1>
							</a>
							<div anim="slide-in-up">
								<h2 class="h5 t-uppercase m-b-small">
									{{ $work->client->name }}
								</h2>
								<a class="link" href="{{ $work->category->url }}" title="{{ t_esc_attr($work->category->name) }}">
									/ {{ $work->category->name }}
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		@endforeach
	</div>
</section><!-- end works-list -->

<!-- team-list -->
<section class="team-list p-t-big p-b-big bkg-primary"
	set-topbar-class="c-white"
>
	@if ($bkg_class)
		<div class="bkg bkg--{{ $bkg_class }}"></div>
	@endif
	<div class="container">
		<div class="row row-align-middle">
			<div class="gr-12 gr-6@tablet gr-2@desktop gutter">
				<div class="team-list__count" anim="slide-in-right">
					{{ count($employees) }}
				</div>
			</div>
			<div class="gr-12 gr-6@tablet gr-4@desktop gutter">
				<div anim="slide-in-up">
					@if ($title)
						<h1 class="h3">
							{!! $title !!}
						</h1>
					@endif
					@if ($subtitle)
						<h2 class="h4">
							{{ $subtitle }}
						</h2>
					@endif
					@if ($claim)
						<p class="p">
							/ {{ $claim }}
						</p>
					@endif
				</div>
			</div>
			<div class="gr-12 gr-12@tablet gr-6@desktop gutter">
				<div class="team-list__join-us-inner" anim="slide-in-left">
					<h2 class="h3 c-white m-b">
						{{ $join->title }}
					</h2>
					<div class="tf vr">
						{!! $join->body !!}
					</div>
					<a class="btn btn--secondary" href="{{ $join->link_url }}" title="{{ t_esc_attr($join->title) }}">
						{{ $join->link_label }}
					</a>
				</div>
			</div>
		</div>
	</div>
</section><!-- end team-list -->

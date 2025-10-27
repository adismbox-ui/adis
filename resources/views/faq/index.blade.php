@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-3">
            @include('layouts.projets_menu')
        </div>
        <div class="col-md-9">
            <h1>Foire aux Questions (FAQ)</h1>
            <div class="accordion" id="faqAccordion">
                @foreach($faq as $item)
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading{{ $item->id }}">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $item->id }}" aria-expanded="false" aria-controls="collapse{{ $item->id }}">
                                {{ $item->question }}
                            </button>
                        </h2>
                        <div id="collapse{{ $item->id }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $item->id }}" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                {{ $item->reponse }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
@component('mail::message')

# {{ $nomeSerie }} criada

A Série <strong>"{{ $nomeSerie }}"</strong> com <strong>"{{ $qtdTemporadas }}"</strong> Temporadas e <strong>"{{ $episodiosPorTemporada }}"</strong> Episódios <br><center><strong>Foi criada com Sucesso!</strong></center>.

@component('mail::button', ['url' => route('seasons.index', $idSerie)])
    Ver série
@endcomponent

@endcomponent
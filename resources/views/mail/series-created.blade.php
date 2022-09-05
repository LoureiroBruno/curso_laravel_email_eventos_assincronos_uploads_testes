@component('mail::message')

**Adicionado Nova Série**:
# <center>{{ $nome }}</center>

- A Série <strong>{{ $nome }}</strong> possui <strong>{{ $seasonsQty }}</strong> Temporadas e <strong>{{ $episodesPerSeason }}</strong> Episódios

@component('mail::button', ['url' => route('seasons.index', $id)])
    Visualizar
@endcomponent

@endcomponent
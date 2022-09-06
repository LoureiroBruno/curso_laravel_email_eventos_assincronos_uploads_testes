<?php

namespace App\Http\Controllers;

use App\Http\Middleware\Autenticador;
use App\Http\Requests\SeriesFormRequestCreate;
use App\Http\Requests\SeriesFormRequestUpdate;
use App\Models\Series;
use App\Models\User;
use App\Repositories\EloquentSeriesRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SeriesCreated;

class SeriesController extends Controller
{
    /**
     * Undocumented function
     *
     * @param SeriesRepository $repository
     */
    public function __construct(private EloquentSeriesRepository $repository)
    {
        $this->repository = $repository;
        $this->middleware(Autenticador::class)->except('index');
    }

    /**
     * list series function
     *
     * @return string
     */
    public function index()
    {
        /** configurado na model Serie o metodo booted orderBy */
        // $series = Serie::orderBy('nome', 'asc')->get();

        /** recebendo do model Serie ordenado por nome asc */
        $series = Series::all();

        return view('series.index')->with('series', $series);
    }

    /**
     * create series function
     *
     * @return void
     */
    public function create()
    {
        return view('series.create');
    }

    /**
     * store series function
     *
     * @param Request $request
     * @return void
     */
    public function store(SeriesFormRequestCreate $request)
    {
        // dd($request->seasonQty);
        /**injeção de dependência - retornar o objeto SeriesRepository criado */
        $serie = $this->repository->add($request);

       

        $userList = User::all();
        // foreach ($userList as $user) {
        foreach ($userList as $index => $user) {
            /** envio de email */
            $email = new SeriesCreated(
                $serie->nome,
                $serie->id,
                $request->seasonQty,
                $request->episodesPerSeason
            );
            
            /** varios usuarios de forma síncrona*/
            // Mail::to($user)->send($email);
            // sleep(2);

            /** varios usuarios de forma assíncrona*/
            // Mail::to($user)->queue($email);

            /** varios usuarios de forma assíncrona*/
            /** com uso do later para agendar a execute */
            $when = now()->addSeconds($index * 5);
            Mail::to($user)->later($when, $email);
        }
        
        /** unico usuário*/
        // Mail::to($request->user())->send($email);

        return to_route('series.index')->with("success", "Cadastrado a série: '{$serie->nome}' com sucesso!");
    }

    public function destroy(Series $series)
    {
        if ($series == null) {
            return to_route('series.index')->with("danger", "Não foi possível realizar exlusão de cadastro");
        }

        $series->delete();

        return to_route('series.index')->with("success", "Excluído o Registro #{$series->id} | nome: '{$series->nome}' com Sucesso!");
    }


    public function edit(Series $series)
    {
        return view('series.edit')->with(
            [
                'series' => $series
            ]
        );
    }


    public function update(SeriesFormRequestUpdate $request, Series $series)
    {
        if ($request->nome == null) {
            return to_route('series.index')->with("danger", "Não foi possível realizar atualização de cadastro");
        }

        $request['nome'] = ucwords(strtolower($request['nome'])); 
        $series->fill($request->all());
        $series->save();
        return to_route('series.index')->with("success", "Atualizado a série: '{$series->nome}' com sucesso!");
    }
}

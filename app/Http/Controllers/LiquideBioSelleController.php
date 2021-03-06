<?php

namespace App\Http\Controllers;

use App\Consultation;
use App\Dossier;
use App\LiquideBioSelle;
use App\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class LiquideBioSelleController extends Controller
{
    public function index()
    {
        $liquideBioSelle=LiquideBioSelle::all();
        return view('liquideBioSelle.index',['liquideBioSelle'=>$liquideBioSelle]);
    }

    public function liste_liquide($id)
    {
        $donnees =  LiquideBioSelle::where('id_consultation', $id)
            ->paginate(7);
        $consult = Consultation::where('id', $id)
            ->first();
        $lignes = count($donnees);

        if ($lignes) {
            return view('liquideBioSelle.index', compact('donnees', 'consult'));
        } else{
            Session::flash('message', 'Données non existantes pour cette consultation!');
            Session::flash('alert-class', 'alert-danger');

            return back();
        }
    }

    public function create()
    {
        return view('liquideBioSelle.create');
    }

    public function store(Request $request)
    {
        $validation =Validator::make($request->all(), [
            'date' => ['required', 'date'],
            'nature' => ['required'],
            'analyse' => ['required'],
            'antibiogramme' => ['required'],
        ]);
        if ($validation->fails()) {
            return redirect()->Back()->withInput()->withErrors($validation);
        }

        $liquideBioSelle=new LiquideBioSelle();
        $liquideBioSelle->date=$request->date;
        $liquideBioSelle->analyse=$request->analyse;
        $liquideBioSelle->antibiogramme=$request->antibiogramme;
        $liquideBioSelle->nature=$request->nature;

        $consult = Consultation::where('id', Session::get('idconsultation'))->first();
        $liquideBioSelle->id_consultation = $consult;

        if ($liquideBioSelle->save())
        {
            Session::flash('message', 'informations enregistrées.');
            Session::flash('alert-class', 'alert-success');
            return back();
        }
        else{
            Session::flash('message', 'Verifier tous les champs SVP!');
            Session::flash('alert-class', 'alert-danger');

            return back();
        }

    }

    public function show($id)
    {
        $liquide= LiquideBioSelle::where('id', $id)
            ->first();
        $consult = Consultation::where('id', $liquide->id_consultation)
            ->first();
        $doc = Dossier::select('id_patient')
            ->where('numD', $consult->num_dossier)
            ->first();
        $patient = Patient::where('idpatient', $doc->id_patient)
            ->first();

        if ($liquide){
            return view('liquideBioSelle.show', compact('consult', 'liquide', 'patient'));
        }else {
            Session::flash('message', 'données non existantes pour cette consultation!');
            Session::flash('alert-class', 'alert-danger');

            return back();
        }
    }

    public function edit(LiquideBioSelle $liquideBioSelle)
    {
        return view('liquideBioSelle.edit',['liquideBioSelle'=>$liquideBioSelle]);
    }

    public function update(Request $request, LiquideBioSelle $liquideBioSelle)
    {
        $validation =Validator::make($request->all(), [
            'date' => ['required', 'date'],
            'nature' => ['required'],
            'analyse' => ['required'],
            'antibiogramme' => ['required'],
        ]);
        if ($validation->fails()) {
            return redirect()->Back()->withInput()->withErrors($validation);
        }

        /*$liquideBioSelle=new LiquideBioSelle();
        $liquideBioSelle->date=$request->date;
        $liquideBioSelle->analyse=$request->analyse;
        $liquideBioSelle->antibiogramme=$request->antibiogramme;
        $liquideBioSelle->nature=$request->nature;*/

        if ($liquideBioSelle->update())
        {
            Session::flash('message', 'Modifications effectuées.');
            Session::flash('alert-class', 'alert-success');

            return back();
        }
        else{
            Session::flash('message', 'Verifier tous les champs SVP!');
            Session::flash('alert-class', 'alert-danger');

            return back();
        }

    }

    public function destroy(LiquideBioSelle $liquideBioSelle)
    {
        $liquideBioSelle->delete();
        return back();
    }
}

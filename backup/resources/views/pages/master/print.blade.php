@extends('layouts.print')

@section('header')

<x-action_print/>

@endsection

@section('content')
@includeIf(Template::form(SharedData::get('template'),'data'))
@endsection
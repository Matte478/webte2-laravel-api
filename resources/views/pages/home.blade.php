@extends('master')
@section('page-class', 'home')

@section('page')
    <section class="section section--home">
        <div class="container">
            <div class="row">
                <div class="col">
                    {{__('frontend.hp.welcome')}}
                </div>
            </div>
        </div>
    </section>


@endsection
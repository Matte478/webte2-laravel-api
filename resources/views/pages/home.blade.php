@extends('master')
@section('page-class', 'home')

@section('style')
    <style>
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }
        .section--home {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100vw;
            height: 100vh;
        }
        .section--home h1 {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
            font-size: 60px;
        }
    </style>
@endsection

@section('page')
    <section class="section section--home">
        <div class="container">
            <div class="row">
                <div class="col">
                    <h1>WEBTE 2 FINAL API</h1>
                </div>
            </div>
        </div>
    </section>
@endsection
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>API ENDPOINTS</title>

    <style>
        @font-face {
            font-family: 'Montserrat';
            font-style: normal;
            font-weight: 400;
            src: url({{storage_path('fonts\montserrat-v14-latin-regular.ttf')}}); /* IE9 Compat Modes */
            url({{storage_path('fonts\montserrat-v14-latin-regular.eot?#iefix')}}) format('embedded-opentype'), /* IE6-IE8 */
            url({{storage_path('fonts\montserrat-v14-latin-regular.woff2')}}) format('woff2'), /* Super Modern Browsers */
            url({{storage_path('fonts\montserrat-v14-latin-regular.woff')}}) format('woff'), /* Modern Browsers */
            url({{storage_path('fonts\montserrat-v14-latin-regular.ttf')}}) format('truetype'), /* Safari, Android, iOS */
            url({{storage_path('fonts\montserrat-v14-latin-regular.svg#Montserrat')}}) format('svg'); /* Legacy iOS */
        }

        body {
            font-family: "Montserrat", sans-serif;
        }

        h1, h2, h3, h4, h5 {
            margin: 0;
            color: #004e66;
            font-weight: 500;
        }

        h1 {
            font-size: 40px;
            margin-bottom: 20px;
            text-align: center;
        }

        h4 {
            font-size: 20px;
            margin-bottom: 6px;
        }

        h5 {
            font-size: 20px;
            margin-bottom: 6px;
        }

        p {
            margin-top: 0;
            margin-bottom: 16px;
            color: #1e1e1e;
        }

        pre {
            display: inline;
            margin-top: 0;
            margin-bottom: 16px;
            color: #1e1e1e;
        }

        hr {
            border-left: 0;
            border-left: 0;
            border-right: 0;
            border-top: 1px solid rgba(0, 78, 102, 0.2);
            margin-top: 16px;
            margin-bottom: 16px;
        }
    </style>
</head>
<body>
<section class="section">
    <div>
        <h1 class="section__title">API Endpoints</h1>
    </div>
    <div>
        <h4>Base API URL:
            <pre>https://wt45.fei.stuba.sk:4445/final-api/api</pre>
        </h4>
    </div>
    <hr>


    <div>
        <h4>Authentication</h4>
        <p>All requests to Web API require authentication. This is achieved by sending a
            valid API access token in the request Authorization header.</p>
    </div>
    <hr>

    <div>
        <h4>Apirplane</h4>
        <h5>
            <pre>[GET] /airplane</pre>
        </h5>
        <p>
            <pre>?r={val}</pre> - required, pitch angle in degrees
        </p>
        <p>
            <pre>?lr1={val}&amp;lr2={val}&amp;lr3={val}</pre> - optional, initialization vals of the calculation
        </p>
    </div>
    <hr>

    <div>
        <h4>Pendulum</h4>
        <h5>
            <pre>[GET] /pendulum</pre>
        </h5>
        <p>
            <pre>?r={val}</pre> - required, position of pendulum
        </p>
        <p>
            <pre>?startDegree={val}&amp;startPosition={val}</pre> - optional, initialization vals of the calculation
        </p>
    </div>
    <hr>

    <div>
        <h4>Ballbeam</h4>
        <h5>
            <pre>[GET] /ballbeam</pre>
        </h5>
        <p>
            <pre>?r={val}</pre> - required, position of ball
        </p>
        <p>
            <pre>?lr1={val}&amp;lr2={val}&amp;lr3={val}&amp;lr4={val}</pre> - optional, initialization vals of the calculation
        </p>
    </div>
    <hr>

    <div>
        <h4>Suspension</h4>
        <h5>
            <pre>[GET] /suspension</pre>
        </h5>
        <p>
            <pre>?r={val}</pre> - required, height of obstacle
        </p>
        <p>
            <pre>?initX1={val}&amp;initX1d={val}&amp;initX2={val}&amp;initX2d={val}&amp;initX3={val}</pre> - optional, initialization vals of the calculation
        </p>
    </div>
    <hr>

    <div>
        <h4>Calculator</h4>
        <h5>
            <pre>[GET] /calculate</pre>
        </h5>
        <p>
            <pre>?problem={val}</pre> - required, problem which will be calculated in octave
        </p>
    </div>
</section>
</body>
</html>
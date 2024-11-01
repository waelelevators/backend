<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <style>
        body {
            direction: rtl;
            font-family: "changa";
        }

        table {
            border-collapse: collapse;
            width: 100%;
            direction: rtl;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-weight: normal;
        }

        /* .column {
    float: left;
    width: 33.33%;
  } */
        .top-space {
            margin: 10px;
        }

        .offer-box {
            width: 50%;
            padding: 5px;
            margin: 0 10px 10 10px;
            border: 1px solid rgba(0, 0, 0, 0.05);
            background-color: rgba(0, 0, 0, 0.05);
            float: right;
        }

        .offer-box p {
            text-align: center;
        }

        .offer-price li {
            font-size: 17pt;
        }

        .desc-box {
            width: 15%;
            height: 110px;
            float: right;
            border: 0px solid #2196f3;
            border-left-width: 1px;
        }

        .desc-box .desc-header {
            height: 40px;
            background-color: rgba(0, 0, 0, 0.09);
            padding: 5px;
            border-right-width: 1px;
            border-top-width: 1px;
            border-bottom-width: 1px;
        }

        .desc-box .desc-body {
            height: 60px;
            padding: 10px;
        }

        .spit {
            width: 50%;
            text-align: center;
        }

        .left-signature {
            float: left;
            width: 50%;
        }

        .right-signature {
            float: right;
            width: 50%;
        }

        .right {
            float: right;
            width: 33%;
        }

        .left {
            float: left;
            width: 33%;
        }

        .middle {
            /* text-align: center; */
            width: 33%;
        }

        .table-striped>tbody>tr:nth-of-type(odd) {
            background-color: #2c9e99;
            color: #fff;
        }

        .center-title {
            margin: auto;
            width: 60%;
            padding: 1px;
            border: 1px solid #5a0909;
            border-radius: 9px;
            text-align: center;
        }

        .pdf table,
        .pdf th,
        .pdf td {
            border: 1px solid black;

        }

        .pdf td,
        .pdf th {
            padding: 0.75rem;
            vertical-align: top;
        }

        .table-striped>tbody>tr:nth-of-type(even) {
            background-color: #343a40;
            color: #fff;
        }

        .estate-title {
            background: #343a40;
            width: 100%;
            color: white;
        }

        .estate-title .paragraph {
            padding: 5px;
            font-size: 16pt;
        }

        div.polaroid {
            width: 80%;
            background-color: white;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            margin-bottom: 25px;
        }

        li {
            text-align: justify;
        }

        @media all {
            .page-break {
                display: none;
            }
        }

        @media print {
            .page-break {
                display: block;
                page-break-before: always;
            }
        }
    </style>

</head>


<body>

    <div class="container" style="direction: rtl" id="container">
        @yield('content')
    </div>
</body>

</html>

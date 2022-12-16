<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Mail</title>
</head>
<body>
    <p>Tere.</p><br/>
    <p>Olete broneerinud teenuse: {{ $data['location_name'] }}</p><br/>
    <p>Broneerimise aeg: {{ $data['time'] }}</p>
    <p>Valitud teenused: {{ $data['service_name'] }}</p>
    <p>
        E-post: {{ $data['e_post'] }}<br/>
        Telefon: {{ $data['telephone'] }}<br/>
        Märkused: {{ $data['message'] }}<br/><br/>
    </p>
    <p>
        Kui leiate, et Te ei saa broneeritud ajal kohale ilmuda, siis<br/>
        on kõige mugavam broneering tühistada klikkides järgnevat
    </p>
    <p>
        linki:
    </p>
    <a href="{{ env('APP_URL') }}/cancelBooking?id={{ $data['book_id'] }}">{{ env('APP_URL') }}/cancelBooking?id={{ $data['book_id'] }}</a>
    <br/>
    <p>Enne vastuvõtule pöördumist palume tutuvuda <a href="https://seksuaaltervis.ee/seksuaaltervise-kliinik/patsiendi-meelespea">patsiendi meelespeaga</a></p>
    <br/>
    <p>Täname!</p>
    <p>Aadress: Mardi 3, Tallinn, </p>
    <p>Telefon: 6665123</p>
    <p>NB: Alates 09.01.2023 on uus aadress: T1 keskus, Peterburi tee 2, Tallinn</p>

</body>
</html>
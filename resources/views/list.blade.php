<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yuklenen Gorseller</title>
</head>
<body>
    <h1>Yuklenen Gorseller</h1>
    <a href="/upload">Yeni Gorsel Yukle</a>

    @if($images->isEmpty())
        <p>Henuz gorsel yuklenmedi.</p>
    @else
        <table border="1" cellpadding="8">
            <tr>
                <th>ID</th>
                <th>Ad</th>
                <th>Tur</th>
                <th>Klasor</th>
                <th>Tarih</th>
            </tr>
            @foreach($images as $image)
            <tr>
                <td>{{ $image->id }}</td>
                <td>{{ $image->name }}</td>
                <td>{{ $image->mime_type }}</td>
                <td>{{ $image->folder }}</td>
                <td>{{ $image->created_at }}</td>
            </tr>
            @endforeach
        </table>
    @endif
</body>
</html>
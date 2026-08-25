<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gorsel Yukle</title>
</head>
<body>
    <h1>Gorsel Yukle</h1>

    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    @if($errors->any())
        <p style="color: red;">{{ $errors->first() }}</p>
    @endif

    <form action="/upload" method="POST" enctype="multipart/form-data">
        @csrf
        <div>
            <label>Gorsel Adi:</label><br>
            <input type="text" name="name" required>
        </div>
        <br>
        <div>
            <label>Aciklama (opsiyonel):</label><br>
            <textarea name="description"></textarea>
        </div>
        <br>
        <div>
            <label>Dosya Sec:</label><br>
            <input type="file" name="image" accept="image/*" required>
        </div>
        <br>
        <button type="submit">Yukle</button>
    </form>
</body>
</html>
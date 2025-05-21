<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
</head>
<body>
    <h1>Hello World</h1>
    @if(isset($postData))
        @php print_r($postData); @endphp
    @endif
    <form method="POST">
        <input type="text" name="nome" value="{{ $postData['nome']??'' }}" placeholder="Enter your name">
        <input type="number" name="idade" value="{{ $postData['idade']??'' }}" placeholder="Enter your age">
        <button type="submit">Submit</button>
    </form>
</body>
</html>
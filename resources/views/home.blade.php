<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Aamarpay Test</title>
</head>
<body>
    <form action="{{url('/payment')}}" method="POST">
        @csrf
        <label for="">Transaction ID</label>
        <input type="text" name="txn_id" value="{{uniqid()}}">
        <label for="">Amount</label>
        <input type="text" name="amount">
        <button type="submit">Submit</button>
    </form>
</body>
</html>

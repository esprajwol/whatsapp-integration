<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>WhatsApp Chat</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href={{ asset('/whats-app-icon.png') }}>
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center">WhatsApp Chat Fetcher</h1>

    <!-- Form to generate QR -->
    <form action="{{ route('whatsapp.fetch') }}" method="POST" class="mt-4">
        @csrf
{{--        <div class="form-group">--}}
{{--            <label for="friend_number">Friend's WhatsApp Number:</label>--}}
{{--            <input type="text" name="friend_number" id="friend_number" class="form-control" required>--}}
{{--        </div>--}}

        <button type="submit" class="btn btn-primary">Generate QR</button>
    </form>

    <!-- QR Code Display -->
    @if (isset($qrCode))
        <div class="mt-4 text-center">
            <h3>Scan the QR Code</h3>
            <img src="{{ $qrCode }}" alt="WhatsApp QR Code" class="img-fluid" style="max-width: 300px;">
        </div>
    @endif

<!-- Fetch Chat Form -->
    <form action="{{ route('whatsapp.getChat') }}" method="POST" class="mt-4">
        @csrf
        <div class="form-group">
            <label for="friend_number">Fetch Chat for Friend:</label>
            <input type="text" name="friend_number" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Fetch Chat</button>
    </form>

    <!-- Display Chat Messages -->
    @if (isset($messages))
        <h3 class="mt-5">Chat Messages:</h3>
        <ul class="list-group">
            @foreach ($messages as $message)
                <li class="list-group-item">
                    <strong>{{ $message['from'] }}:</strong> {{ $message['body']['text'] }}
                    <small class="text-muted">{{ \Carbon\Carbon::createFromTimestamp($message['datetime'])->toDateTimeString() }}</small>
                    <div>
                        @if(isset($message['body']['attachment']) && $message['body']['attachment']['mimetype'] == "audio/ogg; codecs=opus" )
                            <audio controls>
                                <source src="data:audio/ogg;base64,{{ $message['body']['attachment']['data'] }}">
                                Your browser does not support the audio element.
                            </audio>
                        @endif
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

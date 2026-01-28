<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Field Deployment Hub</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #e5e7eb;
            min-height: 100vh;
            padding: 40px 20px;
        }

        .container {
            max-width: 520px;
            margin: 0 auto;
        }

        .box {
            background: #020617;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            border: 1px solid #1e293b;
        }

        h2 {
            font-size: 24px;
            margin-bottom: 8px;
            color: #f1f5f9;
        }

        .subtitle {
            color: #94a3b8;
            font-size: 14px;
            margin-bottom: 24px;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #cbd5e1;
            margin-top: 16px;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        input {
            width: 100%;
            padding: 12px 14px;
            background: #0f172a;
            border: 1px solid #334155;
            border-radius: 6px;
            color: #e5e7eb;
            font-size: 14px;
            transition: all 0.2s;
        }

        input:focus {
            outline: none;
            border-color: #dc2626;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
        }

        input::placeholder {
            color: #475569;
        }

        button {
            width: 100%;
            padding: 14px;
            margin-top: 24px;
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            border: none;
            border-radius: 6px;
            color: white;
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.2s;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        button:hover {
            background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.4);
        }

        button:active {
            transform: translateY(0);
        }

        .alert {
            margin-top: 20px;
            padding: 14px 16px;
            border-radius: 6px;
            font-size: 14px;
            line-height: 1.5;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .success {
            background: #14532d;
            border: 1px solid #166534;
            color: #86efac;
        }

        .error {
            background: #7f1d1d;
            border: 1px solid #991b1b;
            color: #fca5a5;
        }

        .hint {
            font-size: 12px;
            color: #64748b;
            margin-top: 4px;
        }

        .test-btn {
            background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%);
            margin-top: 12px;
            font-size: 13px;
            padding: 10px;
        }

        .test-btn:hover {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e3a8a 100%);
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="box">
            <h2>🚀 Field Deployment Hub</h2>
            <p class="subtitle">Deploy updates to remote field servers via ngrok</p>

            <form method="POST" action="{{ route('hub.deploy') }}" id="deployForm">
                @csrf

                <label for="field_url">Field Server URL</label>
                <input type="text"
                    id="field_url"
                    name="field_url"
                    placeholder="https://your-tunnel.ngrok-free.dev"
                    value="{{ old('field_url', 'https://terica-nongraceful-karie.ngrok-free.dev') }}"
                    required>
                <div class="hint">Full ngrok URL including https://</div>

                <label for="deploy_token">Deployment Token</label>
                <input type="password"
                    id="deploy_token"
                    name="deploy_token"
                    placeholder="X-DEPLOY-TOKEN value"
                    value="{{ old('deploy_token') }}"
                    required>
                <div class="hint">Secret token configured on field server</div>

                <button type="submit">🚀 Trigger Deployment</button>
                <button type="button" class="test-btn" onclick="testConnection()">
                    🔍 Test Connection
                </button>
            </form>

            @if(session('success'))
            <div class="alert success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
            <div class="alert error">{{ session('error') }}</div>
            @endif

            @if ($errors->any())
            <div class="alert error">
                {{ $errors->first() }}
            </div>
            @endif
        </div>
    </div>

    <script>
        function testConnection() {
            const url = document.getElementById('field_url').value;
            const token = document.getElementById('deploy_token').value;

            if (!url || !token) {
                alert('Please fill in both URL and token');
                return;
            }

            const btn = event.target;
            btn.disabled = true;
            btn.textContent = '⏳ Testing...';

            fetch(url.replace(/\/$/, '') + '/api/deploy/pull', {
                    method: 'POST',
                    headers: {
                        'X-DEPLOY-TOKEN': token,
                        'Accept': 'application/json',
                        'ngrok-skip-browser-warning': 'true'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    btn.disabled = false;
                    btn.textContent = '✅ Connection OK';
                    setTimeout(() => {
                        btn.textContent = '🔍 Test Connection';
                    }, 3000);
                    alert('✅ Connection successful!\n\n' + JSON.stringify(data, null, 2));
                })
                .catch(error => {
                    btn.disabled = false;
                    btn.textContent = '❌ Connection Failed';
                    setTimeout(() => {
                        btn.textContent = '🔍 Test Connection';
                    }, 3000);
                    alert('❌ Connection failed:\n\n' + error.message);
                });
        }
    </script>

</body>

</html>